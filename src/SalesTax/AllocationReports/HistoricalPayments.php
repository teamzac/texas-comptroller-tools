<?php

namespace TeamZac\TexasComptroller\SalesTax\AllocationReports;

use Carbon\Carbon;
use Illuminate\Support\Str;

class HistoricalPayments extends AbstractReport
{
    /**
     * 
     * 
     * @param   string $name    the query string to search for
     * @return  this
     */
    public function forCity($name)
    {
        $this->endpoint = 'CtyCntyAllocResults';

        $this->params = [
            'cityCountyName' => $name,
            'cityCountyOption' => 'City'
        ];

        return $this;
    }

    /**
     * 
     * 
     * @param   string $name    the query string to search for
     * @return  this
     */
    public function forCounty($name)
    {
        $this->endpoint = 'CtyCntyAllocResults';

        $this->params = [
            'cityCountyName' => $name,
            'cityCountyOption' => 'County'
        ];

        return $this;
    }

    /**
     * 
     * 
     * @param   string $name    the query string to search for
     * @return  this
     */
    public function forTransitAuthority($name)
    {
        $this->endpoint = 'MCCAllocResults';

        $this->params = [
            'mccOption' => 'MCC',
            'mccOptions' => $name
        ];

        return $this;
    }

    /**
     * 
     * 
     * @param   
     * @return  
     */
    public function forSpecialDistrict($name)
    {
        $this->endpoint = 'SPDAllocResults';

        $this->params = [
            'spdOption' => 'SPD',
            'spdOptions' => $name
        ];

        return $this;
    }

    /**
     * Parse the response and return a collection of periods    
     *
     * @param   string $response
     * @return  array
     */
    protected function parseResponse($response)
    {
        $domParser = str_get_html($response);
        
        $tables = $domParser->find('.resultsTable');

        $periods = [];
        foreach ($tables as $table) 
        {
            $year = $table->find('thead th span')[0]->innertext;

            $rows = $table->find('tbody tr');
            array_shift($rows);

            foreach ($rows as $row)
            {
                $columns = $row->find('td');

                if ( count($columns) < 2 ) continue;

                list($monthColumn, $amountColumn) = $columns;

                $month = trim( $monthColumn->innertext );

                if ( strlen($month) == 0 || 
                    Str::contains($month, 'TOTAL') ||
                    Str::contains($month, '&nbsp') ) continue;

                $amount = $this->cleanCurrency( $amountColumn->innertext );

                if ( $amount == 0 ) continue;

                $period = $this->mapTextToDate("{$month} {$year}");

                $periods[$period]['net-payment'] = $this->cleanCurrency( $amountColumn->innertext );

            }
        }

        ksort($periods);

        return $periods;
    }

    /**
     * Map the given text to a Carbon date object
     * 
     * @param   string $text
     * @return  
     */
    protected function mapTextToDate($text, $format='Y-m-d')
    {
        return (new Carbon($text))->format($format);
    }
}

<?php

namespace TeamZac\TexasComptroller\SalesTax\AllocationReports;

use Carbon\Carbon;
use Illuminate\Support\Str;

class PaymentDetail extends AbstractReport
{
    protected $authorityCode;

    /**
     * 
     * 
     * @param   string $name    the query string to search for
     * @return  this
     */
    public function forCity($name)
    {
        $this->endpoint = 'CtyCntyAllDtlResults';

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
        $this->endpoint = 'CtyCntyAllDtlResults';

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
        $this->endpoint = 'MCCAllocDtlResults';

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
        $this->endpoint = 'SPDAllocDtlResults';

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
     * @return  Collection
     */
    protected function parseResponse($response)
    {
        $domParser = str_get_html($response);

        $this->setAuthorityCode($domParser);
        
        $tables = $domParser->find('.resultsTable');

        $periods = [];
        foreach ($tables as $table) 
        {
            $allocationPeriod = $this->mapTextToDate( $table->find('thead th span')[0]->innertext );

            $rows = $table->find('tbody tr');
            array_shift($rows);

            foreach ($rows as $row)
            {
                $columns = $row->find('td');

                if ( count($columns) < 2 ) continue;

                list($componentColumn, $amountColumn) = $columns;

                $periods[$allocationPeriod][ $this->cleanComponent($componentColumn->innertext) ] = 
                    $this->cleanCurrency( $amountColumn->innertext );

            }
        }

        return $periods;
    }

    /**
     * Map the given text to a Carbon date object
     * 
     * @param   string $text
     * @return  
     */
    protected function mapTextToDate($text)
    {
        $text = trim(
            str_replace(
                'Allocation Period:&nbsp;', 
                '', 
                $text
            )
        );

        return (new Carbon($text))->format('Y-m-d');
    }

    /**
     * Get the authority code, which is saved while parsing the response
     * 
     * @return  integer
     */
    public function getAuthorityCode()
    {
        return $this->authorityCode;
    }

    /**
     * Fetch and set the authority code
     * 
     * @param   $domParser
     * @return  void
     */
    public function setAuthorityCode($domParser)
    {
        $div = $domParser->find('.resultspageCriteria');

        if ( count($div) == 0 ) return;

        list($name, $code) = explode('<br />', trim($div[0]->innertext));
        list ($text, $code) = explode(':', trim($code));
        $this->authorityCode = trim($code);
    }

    /**
     * 
     * 
     * @param   
     * @return  
     */
    public function cleanComponent($text)
    {
        return Str::slug( str_replace('prd', 'period', strtolower($text)) );
    }

}

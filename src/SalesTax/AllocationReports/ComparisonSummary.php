<?php

namespace TeamZac\TexasComptroller\SalesTax\AllocationReports;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class ComparisonSummary extends AbstractReport
{
    protected $baseUri = 'http://comptroller.texas.gov/taxinfo/allocsum/';

    protected $params = [];

    /**
     * Get the report for cities
     * 
     * @return  this
     */
    public function forCities()
    {
        $this->endpoint = 'cities.html';

        return $this;
    }

    /**
     * Get the report for counties
     * 
     * @return  this
     */
    public function forCounties()
    {
        $this->endpoint = 'counties.html';

        return $this;
    }

    /**
     * Get the report for transit authorities
     * 
     * @return  this
     */
    public function forTransitAuthorities()
    {
        $this->endpoint = 'mta_ctd.html';

        return $this;
    }

    /**
     * Get the report for special districts
     * 
     * @return  
     */
    public function forSpecialDistricts()
    {
        $this->endpoint = 'specdist.html';

        return $this;
    }

    /**
     * Parse the HTML and return a collection of periods    
     *
     * @param   string $html
     * @return  array
     */
    protected function parseHtml($html)
    {
        $domParser = str_get_html($html);

        $reportMonth = $this->getReportMonth($domParser);
        
        $data = $this->getTableData($domParser);

        $entities = $data->filter( function($row) {
            return ! $this->shouldIgnoreRow($row);
        })->map( function($row) {
            return $this->parseRow($row);
        });

        return [
            'month' => $reportMonth,
            'entities' => $entities
        ];
    }

    /**
     * Get the month that this report represents
     * 
     * @param   
     * @return  Carbon\Carbon
     */
    public function getReportMonth($domParser)
    {
        $content = $domParser->find('div#content', 0);
        $h1 = $content->find('h1',0);

        if ( !preg_match($this->monthPattern(), $h1->innertext, $matches) )
        {
            throw new \Exception('Could not determine what month this report is for');
        }

        return $this->mapTextToDate($matches[0]);
    }

    /**
     * 
     * 
     * @param   
     * @return  Illuminate\Support\Collection
     */
    public function getTableData($domParser)
    {
        $data = $domParser->find('table.datart',0)->find('tr');
        $thead = array_shift($data);
        return Collection::make($data);
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

    /**
     * Submits the form request and returns the results
     *
     * @param   
     * @return  
     */
    protected function submitFormRequest()
    {
        return $this->guzzle->get($this->endpoint)->getBody()->__toString();
    }

    /**
     * 
     * 
     * @param   
     * @return  
     */
    public function parseRow($row)
    {
        $entity = trim($row->find('th', 0)->innertext);

        $indexes = $this->getIndexHash();

        return [
            'entity' => $entity,
            'amount' => $this->cleanTableCellText( $row->find('td', $indexes['amount'])->innertext ),
            'amount_delta' => $this->cleanTableCellText( $row->find('td', $indexes['amount_delta'])->innertext ),
            'ytd' => $this->cleanTableCellText( $row->find('td', $indexes['ytd'])->innertext ),
            'ytd_delta' => $this->cleanTableCellText( $row->find('td', $indexes['ytd_delta'])->innertext ),
        ];
    }

    /**
     * Check to see if this is the city report
     * 
     * @return  Bool
     */
    public function isCityReport()
    {
        return $this->endpoint == 'cities.html';
    }

    /**
     * 
     * 
     * @param   
     * @return  
     */
    public function getIndexHash()
    {
        if ( $this->isCityReport() )
        {
            return [
                'amount' => 0,
                'amount_delta' => 2,
                'ytd' => 3,
                'ytd_delta' => 5
            ];
        }

        return [
            'amount' => 1,
            'amount_delta' => 3,
            'ytd' => 4,
            'ytd_delta' => 6
        ];
    }

    /**
     * 
     *
     * @param   
     * @return  
     */
    private function cleanTableCellText($text)
    {
        $cleanText = trim(str_replace([',', '%'], '', $text));
        if ( $cleanText == 'U/C' ) return null;
        return round($cleanText, 2);
    }

    /**
     * 
     * 
     * @param   
     * @return  
     */
    protected function monthPattern()
    {
        return '/(January|February|March|April|May|June|July|August|September|November|December) \d{4}/';
    }

    /**
     * 
     * 
     * @param   
     * @return  
     */
    public function shouldIgnoreRow($row)
    {
        $entity = trim($row->find('th', 0)->innertext);

        return $entity == 'TOTALS' || $entity == '' || strlen($entity) == 0;
    }
}
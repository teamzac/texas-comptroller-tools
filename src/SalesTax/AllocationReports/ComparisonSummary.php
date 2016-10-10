<?php

namespace TeamZac\TexasComptroller\SalesTax\AllocationReports;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class ComparisonSummary extends AbstractReport
{
    protected $baseUri = 'https://www.comptroller.texas.gov/transparency/local/allocations/sales-tax/';

    protected $params = [];

    /**
     * The report type
     *
     * @var string (city|county|transit|spd)
     */
    protected $reportType;

    /**
     * Get the report for cities
     * 
     * @return  this
     */
    public function forCities()
    {
        $this->endpoint = 'scripts/cities-load.php';

        $this->reportType = 'city';

        return $this;
    }

    /**
     * Get the report for counties
     * 
     * @return  this
     */
    public function forCounties()
    {
        $this->endpoint = 'scripts/counties-load.php';

        $this->reportType = 'county';

        return $this;
    }

    /**
     * Get the report for transit authorities
     * 
     * @return  this
     */
    public function forTransitAuthorities()
    {
        $this->endpoint = 'transit/scripts/transit-load.php';

        $this->reportType = 'transit';

        return $this;
    }

    /**
     * Get the report for special districts
     * 
     * @return  
     */
    public function forSpecialDistricts()
    {
        $this->endpoint = 'special-district/scripts/spd-load.php';

        $this->reportType = 'spd';

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
        $json = json_decode($response);

        $reportMonth = $this->getReportMonth($json);
        
        $entities = collect($json)->map( function($row) {
            return $this->parseRow($row);
        })->sortBy('entity');

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
    public function getReportMonth($json)
    {
        $firstEntry = $json[0];

        return new Carbon( sprintf("%s/1/%s", $firstEntry->report_month, $firstEntry->report_year) );
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
        $keys = $this->getHashKeys();

        return [
            'entity' => $row->{$keys['entity']},
            'amount' => $row->{$keys['amount']},
            'amount_delta' => isset($row->{$keys['amount_delta']}) ? $row->{$keys['amount_delta']} * 100 : 0,
            'ytd' => $row->{$keys['ytd']},
            'ytd_delta' => isset($row->{$keys['ytd_delta']}) ? $row->{$keys['ytd_delta']} * 100 : 0
        ];
    }

    /**
     * Check to see if this is the city report
     * 
     * @return  Bool
     */
    public function isCityReport()
    {
        return $this->reportType == 'city';
    }

    /**
     * Get the key hash for this specific report type, since the keys
     * are different for some reason that makes absolutely no sense
     * 
     * @return  array
     */
    public function getHashKeys()
    {
        if ( $this->reportType == 'city' ) 
        {
            return [
                'entity' => 'city',
                'amount' => 'net_payment_this_period',
                'amount_delta' => 'period_percent_change',
                'ytd' => 'payments_to_date',
                'ytd_delta' => 'ytd_percent_change'
            ];
        }
        else 
        {
            return [
                'entity' => 'name',
                'amount' => 'net_payment_this_period',
                'amount_delta' => 'percent_change_prior_year',
                'ytd' => 'payments_to_date',
                'ytd_delta' => 'percent_change_to_date'
            ];
        }
    }
}
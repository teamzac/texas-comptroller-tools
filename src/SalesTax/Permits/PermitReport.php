<?php

namespace TeamZac\TexasComptroller\SalesTax\Permits;

use Carbon\Carbon;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Support\Collection;
use TeamZac\TexasComptroller\Exceptions\BadResponse;
use TeamZac\TexasComptroller\Exceptions\ReportNotFound;

class PermitReport
{
    /** @var Guggle */
    protected $guzzle;

    /** @var Illuminate\Support\Collection */
    protected $permits;

    /** @var string */
    protected $baseUri = 'http://www.texastransparency.org/Data_Center/files/NEW_SALETX_PERMIT_YYYYMMDD.CSV';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->guzzle = new Guzzle([
            'base_uri' => $this->baseUri
        ]);

        $this->permits = Collection::make();
    }

    /**
     * Fetch the report for the given date. If no report is found, it will
     * throw a ReportNotFound exception. Otherwise, the results will be
     * parsed into the $this->permits variable as a Collection.
     * 
     * @param   Carbon  $date
     * @return  $this
     */
    public function forDate(Carbon $date)
    {
        $response = $this->guzzle->get($this->createUrl($date));

        $statusCode = $response->getStatusCode();
        if ( $statusCode == 404 )
        {
            throw new ReportNotFound;
        }

        if ( $statusCode >= 400 )
        {
            throw new BadResponse($statusCode);
        }

        $this->parseResponse( (string) $response->getBody() );

        return $this;
    }

    /**
     * Find the report for the given week, iterating through each weekday 
     * until it is found. If no report is found for the week, throws a
     * ReportNotFound exception
     * 
     * @param   Carbon  $date 
     * @return  $this
     */
    public function forWeekContaining(Carbon $date)
    {
        // Reset to Monday
        $date->startOfWeek(); 

        while ( $date->dayOfWeek != Carbon::SATURDAY )
        {
            try 
            {
                return $this->forDate($date);
            }
            catch (ReportNotFound $e)
            {
                $date->addDay();
            }
        }

        throw ReportNotFound;
    }

    /**
     * Convenience method for filtering the permits collection
     * 
     * @param   string $key
     * @param   string $value
     * @return  Collection
     */
    public function filter($key, $value)
    {
        return $this->permits->filter( function($permit) use ($key, $value) {
            return $permit->{$key} == $value;
        });
    }

    /**
     * Create the URL for the report, given the provided date
     * 
     * @param   Carbon $date
     * @return  string
     */
    public function createUrl(Carbon $date)
    {
        return str_replace(
            'YYYYMMDD',
            $date->format('Ymd'), 
            $this->baseUri
        );
    }

    /**
     * Parse the response and store it in $this->permits
     * 
     * @param   string $response
     * @return  void
     */
    public function parseResponse($response)
    {
        $permits = [];

        $rows = explode("\n", $response);

        foreach ($rows as $row)
        {
            $permits[] = new Permit( str_getcsv($row) );
        }

        $this->permits = Collection::make($permits);
    }
} 


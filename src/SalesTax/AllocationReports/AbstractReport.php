<?php

namespace TeamZac\TexasComptroller\SalesTax\AllocationReports;

use GuzzleHttp\Client as Guzzle;
use TeamZac\TexasComptroller\Exceptions\InvalidRequest;

class AbstractReport
{
    /** @var string */
    protected $baseUri = 'https://mycpa.cpa.state.tx.us/allocation/';

    /** @var Guzzle */
    protected $guzzle;

    /** @var array */
    protected $params;

    /** @var string */
    protected $endpoint;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->guzzle = new Guzzle([
            'base_uri' => $this->baseUri,
            'cookies' => true
        ]);
    }

    /**
     * 
     * 
     * @param   
     * @return  string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * 
     * 
     * @param   
     * @return  
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Fetch the report from the Comptroller's web site using the parameters that have been provided
     * 
     * @param   
     * @return  mixed
     */
    public function get()
    {
        if ( $this->endpoint === null )
        {
            throw new InvalidRequest;
        }

        if ( $this->params === null )
        {
            throw new InvalidRequest;
        }

        $response = $this->submitFormRequest();
        return $this->parseResponse($response);
    }

    /**
     * Submits the form request and returns the results
     *
     * @param   
     * @return  
     */
    protected function submitFormRequest()
    {
        return $this->guzzle->post($this->endpoint, [
            'form_params' => $this->params
        ])->getBody()->__toString();
    }

    /**
     * Parse the response and return the results, to be overriden by subclasses   
     *
     * @param   string $response
     * @return  mixed
     */
    protected function parseResponse($response)
    {
        return [];
    }

    /**
     * 
     * 
     * @param   
     * @return  
     */
    public function cleanCurrency($text)
    {
        return (double) str_replace([','], '', trim($text));
    }
}
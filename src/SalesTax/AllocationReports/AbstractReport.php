<?php

namespace TeamZac\TexasComptroller\SalesTax\AllocationReports;

use GuzzleHttp\Client as Guzzle;
use TeamZac\TexasComptroller\Exceptions\InvalidRequest;

class AbstractReport
{
    /** @var Guzzle */
    protected $guzzle;

    /** @var array */
    protected $params;

    /** @var string */
    protected $endpoint;

    public function __construct()
    {
        $this->guzzle = new Guzzle([
            'base_uri' => 'https://mycpa.cpa.state.tx.us/allocation/',
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
        if ( $this->endpoint == null )
        {
            throw new InvalidRequest;
        }

        if ( $this->params == null )
        {
            throw new InvalidRequest;
        }

        $html = $this->submitFormRequest();
        return $this->parseHtml($html);
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
     * Parse the HTML and return a collection of periods, to be overriden by subclasses   
     *
     * @param   string $html
     * @return  mixed
     */
    protected function parseHtml($html)
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
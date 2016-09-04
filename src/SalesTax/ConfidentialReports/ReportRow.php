<?php

namespace TeamZac\TexasComptroller\SalesTax\ConfidentialReports;

class ReportRow
{

    /*
    |--------------------------------------------------------------------------
    | ReportRow
    |--------------------------------------------------------------------------
    |
    | This class represents a single row in the confidential
    | report, and is able to determine whether it explains
    | a return, payment, or local outlet
    |
    */

    /** @var array */
    protected $data;

    /** @var array */
    protected $map = [
        'taxpayerNumber',
        'taxpayerName',
        'taxpayerAddress',
        'taxpayerCity',
        'taxpayerState',
        'taxpayerZip',
        'taxpayerNaics',
        'outletNumber',
        'outletName',
        'outletAddress',
        'outletCity',
        'outletState',
        'outletZip',
        'outletNaics',
        'paymentMonth',
        'paymentAmount',
        'returnType',
        'returnPeriodBegin',
        'returnPeriodEnd',
        'returnAmount',
        'returnCode',
        'empty'
    ];

    /**
     * Constructor, receives an array representing a row of the confidential report
     * 
     * @param   array $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Does this row represent a Return record?
     * 
     * @return  boolean
     */
    public function isReturn()
    {
        return strlen($this->returnPeriodBegin) > 0;
    }

    /**
     * Does this row represent an Payment record?
     * 
     * @return  boolean
     */
    public function isPayment()
    {
        return strlen($this->paymentMonth) > 0;
    }

    /**
     * Does is row represent an Outlet record?
     * 
     * @return  boolean
     */
    public function isOutlet()
    {
        return strlen($this->outletNumber) > 0;
    }

    /**
     * use __get to map the human readable key from $this->map to the numeric index
     *
     * @param   string $key
     * @return  string
     */
    public function __get($key)
    {
        $index = array_search($key, $this->map);
        return trim($this->data[$index]);
    }

}
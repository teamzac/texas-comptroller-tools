<?php

namespace TeamZac\TexasComptroller\SalesTax\ConfidentialReports;

use Carbon\Carbon;

class TaxpayerEntry
{
    /** @var array */
    protected $taxpayer;

    /** @var array */
    protected $outlets = [];

    /** @var array */
    protected $returns = [];

    /** @var array */
    protected $payments = [];

    /**
     * Constructor
     * 
     * @param   ReportRow $reportRow
     * @return  
     */
    public function __construct(ReportRow $reportRow = null)
    {
        if ( null == $reportRow ) return;

        $this->addTaxpayer($reportRow);
    }

    /**
     * Is this taxpayer a list filter?
     * 
     * @return  boolean
     */
    public function isListFiler()
    {
        return count($this->outlets) == 0;
    }

    /**
     * Get the taxpayer data
     *
     * @return  array
     */
    public function taxpayer()
    {
        return $this->taxpayer;
    }

    /**
     * Get the payments
     *
     * @return  array
     */
    public function payments()
    {
        return $this->payments;
    }

    /**
     * Get the outlets
     *
     * @return  array
     */
    public function outlets()
    {
        return $this->outlets;
    }

    /**
     * Add information about the taxpayer
     *
     * @param   ReportRow $reportRow
     * @return  void
     */
    public function addTaxpayer(ReportRow $reportRow)
    {
        $this->taxpayer = [
            'number' => $reportRow->taxpayerId,
            'name' => $reportRow->taxpayerName,
            'address' => $reportRow->taxpayerAddress,
            'city' => $reportRow->taxpayerCity,
            'state' => $reportRow->taxpayerState,
            'zip' => $reportRow->taxpayerZip,
            'naics' => $reportRow->taxpayerNaics
        ];
    }

    /**
     * Add data from a ReportRow to the entry
     *
     * @param   ReportRow $reportRow
     * @return  
     */
    public function addData(ReportRow $reportRow)
    {
        if ( $reportRow->isOutlet() )
        {
            $this->addOutlet($reportRow);
        }

        if ( $reportRow->isReturn() )
        {
            $this->addReturn($reportRow);
        }

        if ( $reportRow->isPayment() )
        {
            $this->addPayment($reportRow);
        }
    }

    /**
     * Add outlet data
     *
     * @param   ReportRow $reportRow
     * @return  void
     */
    protected function addOutlet(ReportRow $reportRow)
    {
        $this->outlets[] = [
            'number' => $reportRow->outletNumber,
            'name' => $reportRow->outletName,
            'address' => $reportRow->outletAddress,
            'city' => $reportRow->outletCity,
            'state' => $reportRow->outletState,
            'zip' => $reportRow->outletZip,
            'naics' => $reportRow->outletNaics
        ];
    }


    /**
     * Add payment data
     *
     * @param   ReportRow $reportRow
     * @return  void
     */
    protected function addPayment(ReportRow $reportRow)
    {
        $this->payments[] = [
            'month' => $this->parseMonth($reportRow->paymentMonth),
            'amount' => $this->parseAmount($reportRow->paymentAmount),
            'returns' => $this->returns
        ];
        $this->returns = [];
    }


    /**
     * Add return data
     *
     * @param   
     * @return  
     */
    protected function addReturn(ReportRow $reportRow)
    {
        $this->returns[] = [
            'type' => $reportRow->returnType,
            'periodBegin' => $this->parseMonth($reportRow->returnPeriodBegin),
            'periodEnd' => $this->parseMonth($reportRow->returnPeriodEnd),
            'amount' => $this->parseAmount($reportRow->returnAmount),
            'code' => $reportRow->returnCode
        ];
    }

    /**
     * Map unknown property keys to the taxpayer array
     *
     * @param   string $key
     * @return  mixed
     */
    public function __get($key)
    {
        if ( isset( $this->taxpayer[$key]) )
        {
            return $this->taxpayer[$key];
        }
    }

    /**
     * Parse the month value
     *
     * @param   string $value
     * @return  Carbon\Carbon
     */
    public function parseMonth($value)
    {
        return Carbon::createFromFormat('Ymd h:i:s', "{$value}01 00:00:00");
    }

    /**
     * Parse the currency amount to get rid of decimals
     *
     * @param   string $value
     * @return  int
     */
    public function parseAmount($value)
    {
        return (int) round( $value * 100, 0 );
    }

}
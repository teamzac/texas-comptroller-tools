<?php

namespace TeamZac\TexasComptroller\SalesTax\ConfidentialReports;

class ConfidentialReport
{
    /** @var string */
    protected $filename;

    /**
     * Constructor
     * 
     * @param   string $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Parse the report, returning a completed taxpayer record to the $callback
     * 
     * @param   Callable $callback
     * @return  void
     */
    public function parse(Callable $callback)
    {
        $report = $this->getReport();

        $currentTaxpayer = new NullTaxpayerEntry;
        while ($row = fgetcsv($report)) 
        {
            $reportRow = new ReportRow($row);

            if ( $this->isStartOfNewTaxpayer($reportRow, $currentTaxpayer) )
            {
                $callback($currentTaxpayer);
                $currentTaxpayer = new TaxpayerEntry($reportRow);
            }

            $currentTaxpayer->addData($reportRow);
        }

        $callback($currentTaxpayer);
    }

    /**
     * Is this row the start of a new taxpayer record?
     *
     * @param   ReportRow $reportRow
     * @param   TaxpayerEntry $currentTaxpayer
     * @return  boolean
     */
    public function isStartOfNewTaxpayer(ReportRow $reportRow, TaxpayerEntry $currentTaxpayer)
    {
        return $reportRow->taxpayerNumber != $currentTaxpayer->number;
    }

    /**
     * Get the file handler for the report
     * 
     * @return  File Pointer
     */
    public function getReport()
    {
        return fopen($this->filename, 'r');
    }

}
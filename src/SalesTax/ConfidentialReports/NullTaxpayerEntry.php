<?php

namespace TeamZac\TexasComptroller\SalesTax\ConfidentialReports;

class NullTaxpayerEntry extends TaxpayerEntry
{
    /**
     * Since the TaxpayerEntry class, from which this one extends, provides
     * an implementation of __get() that maps unknown properties to the
     * $this->taxpayer property, we define a dummy variable to access
     * the taxpayer number property that would otherwise be available
     *
     * @var null
     */
    public $number = null;

    /**
     * Override the addData method to make it a no-op
     * 
     * @param   ReportRow $reportRow
     * @return  void
     */
    public function addData(ReportRow $reportRow)
    {
        
    }
}
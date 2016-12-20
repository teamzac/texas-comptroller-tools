<?php

use TeamZac\TexasComptroller\SalesTax\ConfidentialReports\NullTaxpayerEntry;
use TeamZac\TexasComptroller\SalesTax\ConfidentialReports\ConfidentialReport;

class ConfidentialReportTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    function it_parses_a_list_filer()
    {
        $filename = sprintf("%s/list-filer.txt", __DIR__);
        $report = new ConfidentialReport($filename);

        $theTaxpayer = null;
        $report->parse( function($taxpayer) use (&$theTaxpayer) {
            $theTaxpayer = $taxpayer;
        });

        $this->assertEquals('11111111111', $theTaxpayer->number);
        $this->assertEquals(3, count($theTaxpayer->payments()));
        $this->assertTrue( $theTaxpayer->isListFiler());
    }

    /** @test */
    function it_parses_an_outlet()
    {
        $filename = sprintf("%s/local-outlet.txt", __DIR__);
        $report = new ConfidentialReport($filename);

        $theTaxpayer = null;
        $report->parse( function($taxpayer) use (&$theTaxpayer) {
            $theTaxpayer = $taxpayer;
        });

        $this->assertEquals('22222222222', $theTaxpayer->number);
        $this->assertEquals(4, count($theTaxpayer->payments()));
        $this->assertEquals(2, count($theTaxpayer->payments()[1]['returns']));
        $this->assertFalse( $theTaxpayer->isListFiler());
    }

    /** @test */
    function it_parses_a_report_with_both_taxpayer_types()
    {
        $filename = sprintf("%s/full-report.txt", __DIR__);
        $report = new ConfidentialReport($filename);

        $taxpayers = [];
        $report->parse( function($taxpayer) use (&$taxpayers) {
            $taxpayers[] = $taxpayer;
        });

        $this->assertTrue( $taxpayers[0] instanceof NullTaxpayerEntry );
        $this->assertEquals(3, count($taxpayers));
    }



}
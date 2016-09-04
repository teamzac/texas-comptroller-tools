<?php

use Carbon\Carbon;
use TeamZac\TexasComptroller\SalesTax\Permits\PermitReport;

class PermitReportTest extends PHPUnit_Framework_TestCase
{    
    /** @test */
    function it_creates_the_proper_url()
    {
        $report = new PermitReport;

        $date = new Carbon('first day of January 2016');

        $url = $report->createUrl($date);

        $correctUrl = 'http://www.texastransparency.org/Data_Center/files/NEW_SALETX_PERMIT_20160101.CSV';

        $this->assertEquals($correctUrl, $url);
    }

    /** @test */
    function it_gets_a_report()
    {
        $date = new Carbon('2016-07-04');

        $report = new PermitReport;

        $addison = $report->forDate($date)->filter('outlet_city', 'ADDISON');
        // var_dump($addison);
    }

}
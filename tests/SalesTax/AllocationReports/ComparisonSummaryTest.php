<?php

use TeamZac\TexasComptroller\SalesTax\AllocationReports\ComparisonSummary;

class ComparisonSummaryTest extends PHPUnit_Framework_TestCase
{    
    /** @test */
    function it_sets_city_parameters()
    {
        $report = new ComparisonSummary;

        $data = $report->forCities()->get();

        $this->assertTrue( $data['entities']->count() > 0 );

        $this->assertTrue( $report->isCityReport() );
    }

    /** @test */
    function it_sets_county_parameters()
    {
        $report = new ComparisonSummary;

        // var_dump( $report->forCounties()->get() );
    }

    /** @test */
    function it_sets_transit_authority_parameters()
    {
        $report = new ComparisonSummary;

        // var_dump( $report->forTransitAuthorities()->get() );
    }

    /** @test */
    function it_sets_special_district_parameters()
    {
        $report = new ComparisonSummary;

        // var_dump( $report->forSpecialDistricts()->get() );
    }
}
<?php

use TeamZac\TexasComptroller\SalesTax\AllocationReports\ComparisonSummary;

class ComparisonSummaryTest extends PHPUnit_Framework_TestCase
{    
    /** @test */
    function it_sets_city_parameters()
    {
        $report = new ComparisonSummary;

        $report->forCities();
        var_dump($report->get());
    }

    /** @test */
    function it_sets_county_parameters()
    {
        $report = new ComparisonSummary;

        $report->forCounties();
    }

    /** @test */
    function it_sets_transit_authority_parameters()
    {
        $report = new ComparisonSummary;

        $report->forTransitAuthorities();
    }

    /** @test */
    function it_sets_special_district_parameters()
    {
        $report = new ComparisonSummary;

        $report->forSpecialDistricts();
    }
}
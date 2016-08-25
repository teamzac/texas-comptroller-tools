<?php

use TeamZac\TexasComptroller\SalesTax\AllocationReports\HistoricalPayments;

class HistoricalPaymentsTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    function it_throws_an_exception_if_parameters_are_missing()
    {
        $this->setExpectedException( TeamZac\TexasComptroller\Exceptions\InvalidRequest::class);
        
        (new HistoricalPayments)->get();
    }
    
    /** @test */
    function it_sets_city_parameters()
    {
        $queryTerm = 'Austin';

        $report = new HistoricalPayments;

        $report->forCity($queryTerm);
        
        $endpoint = $report->getEndpoint();
        $params = $report->getParams();

        $this->assertEquals('CtyCntyAllocResults', $endpoint);
        $this->assertEquals($queryTerm, $params['cityCountyName']);
    }

    /** @test */
    function it_sets_county_parameters()
    {
        $queryTerm = 'Parker';

        $report = new HistoricalPayments;

        $report->forCounty($queryTerm);
        
        $endpoint = $report->getEndpoint();
        $params = $report->getParams();

        $this->assertEquals('CtyCntyAllocResults', $endpoint);
        $this->assertEquals($queryTerm, $params['cityCountyName']);
    }

    /** @test */
    function it_sets_transit_authority_parameters()
    {
        $queryTerm = 'Dallas MTA';

        $report = new HistoricalPayments;

        $report->forTransitAuthority($queryTerm);
        
        $endpoint = $report->getEndpoint();
        $params = $report->getParams();

        $this->assertEquals('MCCAllocResults', $endpoint);
        $this->assertEquals($queryTerm, $params['mccOptions']);
    }

    /** @test */
    function it_sets_special_district_parameters()
    {
        $queryTerm = 'Bexar Co ESD 3';

        $report = new HistoricalPayments;

        $report->forSpecialDistrict($queryTerm);

        $endpoint = $report->getEndpoint();
        $params = $report->getParams();

        $this->assertEquals('SPDAllocResults', $endpoint);
        $this->assertEquals($queryTerm, $params['spdOptions']);
    }
}
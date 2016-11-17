<?php

use TeamZac\TexasComptroller\SalesTax\AllocationReports\PaymentDetail;

class PaymentDetailTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    function it_throws_an_exception_if_parameters_are_missing()
    {
        $this->setExpectedException( TeamZac\TexasComptroller\Exceptions\InvalidRequest::class);
        
        (new PaymentDetail)->get();
    }

    /** @test */
    function it_sets_city_parameters()
    {
        $queryTerm = 'Austin';

        $report = new PaymentDetail;

        $report->forCity($queryTerm);
        
        $endpoint = $report->getEndpoint();
        $params = $report->getParams();

        $this->assertEquals('CtyCntyAllDtlResults', $endpoint);
        $this->assertEquals($queryTerm, $params['cityCountyName']);

        $this->assertEquals(24, count($report->get()));
    }

    /** @test */
    function it_sets_county_parameters()
    {
        $queryTerm = 'Parker';

        $report = new PaymentDetail;

        $report->forCounty($queryTerm);
        
        $endpoint = $report->getEndpoint();
        $params = $report->getParams();

        $this->assertEquals('CtyCntyAllDtlResults', $endpoint);
        $this->assertEquals($queryTerm, $params['cityCountyName']);
    }

    /** @test */
    function it_sets_transit_authority_parameters()
    {
        $queryTerm = 'Dallas MTA';

        $report = new PaymentDetail;

        $report->forTransitAuthority($queryTerm);
        
        $endpoint = $report->getEndpoint();
        $params = $report->getParams();

        $this->assertEquals('MCCAllocDtlResults', $endpoint);
        $this->assertEquals($queryTerm, $params['mccOptions']);
    }

    /** @test */
    function it_sets_special_district_parameters()
    {
        $queryTerm = 'Bexar Co ESD 3';

        $report = new PaymentDetail;

        $report->forSpecialDistrict($queryTerm);

        $endpoint = $report->getEndpoint();
        $params = $report->getParams();

        $this->assertEquals('SPDAllocDtlResults', $endpoint);
        $this->assertEquals($queryTerm, $params['spdOptions']);
    }
}
<?php

namespace TeamZac\TexasComptroller\SalesTax\Permits;

use TeamZac\TexasComptroller\Support\AttributeBag;

class Permit extends AttributeBag
{
    protected static $attributeKeys = [
        'taxpayer_number',
        'outlet_number',
        'taxpayer_name',
        'taxpayer_address',
        'taxpayer_city',
        'taxpayer_state',
        'taxpayer_zip',
        'taxpayer_county',
        'filler',
        'outlet_name',
        'outlet_address',
        'outlet_city',
        'outlet_state',
        'outlet_zip',
        'outlet_county',
        'filler',
        'permit_type',
        'tax_code',
        'outlet_naics',
        'issue_date',
        'first_sale_date'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'first_sale_date' => 'date'
    ];
}
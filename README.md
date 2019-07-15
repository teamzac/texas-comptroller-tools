# This package is deprecated

Please see [teamzac/texas-comptroller](https://github.com/teamzac/texas-comptroller) instead.

## License

You're free to use this package (it's [MIT-licensed](LICENSE.md)) however you see fit.

## Installation

You can install the package via composer:

``` bash
composer require teamzac/texas-comptroller-tools
```

## Local Sales Tax Reports

There are currently classes that support downloading two different local sales tax reports available on the Comptroller's site: the allocation payment detail report, and the allocation historical payments report. Both classes provide convenience methods for downloading reports for different entity types (cities, counties, transit authorities, and special districts).

### Payment Detail Reports

Create a new report object

``` php
$report = new TeamZac\TexasComptroller\SalesTax\AllocationReports\PaymentDetail;
```

Request a report for a given entity type with the appropriate search string. There are four different entity types provided by the Comptroller:

``` php
$data = $report->forCity('Austin')->get();

$data = $report->forCounty('Parker')->get();

$data = $report->forTransitAuthority('Dallas MTA')->get();

$data = $report->forSpecialDistrict('Bexar Co ESD 3')->get();
```

The return value will be an associative array, keyed by the date of the allocation period. Each value will be an associative array of the different payment components and their respective amounts:

```php
[
    'YYYY-MM-01' => [
        'total-collections' => 12345.67,
        'prior-period-collections' => 12345.67,
        'current-period-collections' => 12345.67,
        ...
        'net-payment' => 12345.67
    ],
    ...
]
```

This report provides data for the most recent 24 months.

### Historical Payment Reports

Create a new report object

``` php
$report = new TeamZac\TexasComptroller\SalesTax\AllocationReports\HistoricalPayments;
```

Request a report for a given entity type with the appropriate search string. There are four different entity types provided by the Comptroller:

``` php
$data = $report->forCity('Austin')->get();

$data = $report->forCounty('Parker')->get();

$data = $report->forTransitAuthority('Dallas MTA')->get();

$data = $report->forSpecialDistrict('Bexar Co ESD 3')->get();
```

The return value will be an associative array, keyed by the date of the allocation period. Each value will be an associative array of the different payment components and their respective amounts:

```php
[
    'YYYY-MM-01' => [
        'net-payment' => 12345.67
    ],
    ...
]
```

Although it's a bit overkill to nest the data so deeply, it was done to retain consistency with the results from the Payment Detail Report.

### Comparison Summary Reports

Create a new report object

``` php
$report = new TeamZac\TexasComptroller\SalesTax\AllocationReports\ComparisonSummary;
```

Request a report for a given entity type with the appropriate search string. There are four different entity types provided by the Comptroller:

``` php
$data = $report->forCities()->get();

$data = $report->forCounties()->get();

$data = $report->forTransitAuthorities()->get();

$data = $report->forSpecialDistricts()->get();
```

The return value will be an associative array including the allocation period and an array of entities. Each entity will include the name, current amount, year-to-date amount, and the year-over-year change for each.

```php
[
    'period' => 'YYYY-MM-01',
    'entities' => [
        [
            'entity' => 'Abbott',
            'amount' => 12345.67,
            'amount_delta' => 12345.67,
            'ytd' => 12345.67,
            'ytd_delta' => 12345.67
        ],
        ...
    ]
]
```

### Exceptions

You should use the fluent report generator methods to create your request. If you fail to do so, you may receive an `InvalidRequest` exception, which you may catch.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email open@teamzac.com instead of using the issue tracker.


## About TeamZac
TeamZac is the web app development arm of eight20 consulting, specializing in apps that serve local governments. You can view more about us [on our website](https://teamzac.com/open-source).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
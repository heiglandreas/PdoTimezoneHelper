# PdoTimezoneHelper

A small library to ease handling of timezones with PDO

## Usage

This lib provides a helper that creates database-specific code to get a UTC-datetime
from a DateTime-Entry and a timezone entry in your table.

You can store your datetimes in the database with or without an offset but with a 
timezone and this helper will create a UTC-Datetime from it for comparisons

```php
$helper = new PdoTimezoneHelper::create($pdoObject);
$helper->setTimezoneField($field);

echo sprintf(
    "SELECT * FROM table WHERE %1$s < '2016-12-31T23:59:59'",
    $helper->getUtcDateTime($dateTimeField);
```

## Installation

This lib is best installed using [composer](https://getcomposer.org):

```bash
composer require org_heigl/pdo_timezone_helper
```


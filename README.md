# Dot Notation

Array Dot Notation

## Install

Via Composer

```bash
$ composer require arabcoders/dot-notation
```

## Usage Example.

```php
<?php

require __DIR__ . '/../../autoload.php';

$list = [
    'one' => [
        'two' => 'foo'
    ]
];

$dot = new \arabcoders\DotNotation\DotNotation( $list );

var_dump( $dot->get('one.two') === $list['one']['two'] );

echo $dot->get('one.two');
```

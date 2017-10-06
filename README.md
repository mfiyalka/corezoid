# Corezoid module

## General
With this class, you can send a task to the [Corezoid](https://new.corezoid.com/).

Examples:

```php
<?php

use mfiyalka\corezoid\Corezoid;

$api_login = 92372;
$api_secret = '2wHXykWX4NOwC1r2BPVaSLVeHXmsBVZzNqtZuXpeoijAKSIjtd';
$id_process = 263680;
$data = [
    'phone' => '380971234567'
];

$task = (new Corezoid($api_login, $api_secret))
    ->addTask('4gt5676', $id_process, $data)
    ->sendTask();
```

## Change log

* Version 1.0.0
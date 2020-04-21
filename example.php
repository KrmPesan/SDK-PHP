<?php

require('src/Client.php');

use KrmPesan\Client;

$wa = new Client([
    'region' => 'region01'
]);

echo $wa->deviceInfo();

<?php
// panggil file source
require('src/Client.php');

// panggil class
use KrmPesan\Client;

// setting konfigurasi
$wa = new Client([
    'region' => '01',
    'token' => 'your-token-here'
]);

print_r(json_decode($wa->sendMessageText('6283140103081', 'Hai')));

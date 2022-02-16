<?php

// panggil file source
require 'src/ClientV2.php';

// panggil class
use KrmPesan\ClientV2;

// setting konfigurasi
$wa = new ClientV2([
    'token' => 'eyJhbGcixxxxxxxxxxxxx',
]);

$data = $wa->sendMessageTemplate(
    '0812xxxxxxx',
    'news_notification',
    ['John', 'Produk', 'Produk kami tidak ada masalah sama sekali', 'Krm Pesan'],
);

print_r(json_decode($data, true));

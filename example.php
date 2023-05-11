<?php

// panggil file source
require 'src/ClientV3.php';

// panggil class
use KrmPesan\ClientV3;

// setting konfigurasi
$wa = new ClientV3([
    'tokenFile' => __DIR__
]);

// print_r($wa->getDevice());

$messages = $wa->getMessages();
print_r(json_decode($messages, true));

// $data = $wa->sendMessageTemplate(
//     '0812xxxxxxx',
//     'news_notification',
//     ['John', 'Produk', 'Produk kami tidak ada masalah sama sekali', 'Krm Pesan'],
// );

// print_r(json_decode($data, true));

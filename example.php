<?php

// panggil file source
require 'src/ClientV3.php';
// atau jika menggunakan composer require krmpesan/sdk
// require "vendor/autoload.php";

// panggil class
use KrmPesan\ClientV3;

// setting konfigurasi menggunakan file token.json
// arahkan direktori file token.json ke variabel tokenFile
$wa = new ClientV3([
    'tokenFile' => __DIR__,
]);

// print_r($wa->getDevice());

// $messages = $wa->getMessages();
// print_r(json_decode($messages, true));

// sending message with template without parameter
$data = $wa->sendMessageTemplateText(
    '081231135699',
    'promo_juni_2024',
    'id',
);
print_r(json_decode($data, true));

// sending message with template with parameter
// $data = $wa->sendMessageTemplateText(
//     '0812xxxxxxx',
//     'news_notification',
//     'id',
//     ['John', 'Produk', 'Produk kami tidak ada masalah sama sekali', 'Krm Pesan'],
// );
// print_r(json_decode($data, true));

// upload file and get url from file
// $url = $wa->upload('sample.png');
// print_r($url);

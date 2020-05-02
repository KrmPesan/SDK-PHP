# KrmPesan SDK PHP

## Installasi

### Install via Composer
```bash
composer require krmpesan/sdk
```

### Install Manual
1. Download file dari repository ini [DI SINI](https://github.com/KrmPesan/SDK-PHP/releases)
2. Copy File `src/Client.php`
3. Done.

# Cara Penggunaan

PENTING: Nomor yang tercantum hanya sebagai sample, bukan nomor kami atau nomor sesungguhnya.

## Setting Konfigurasi
```php
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
```

## Kirim Pesan Text

### Request
```php
$wa->sendMessageText('081212341234', 'Hai')
```

### Response
```
stdClass Object
(
    [code] => 200
    [message] => Success. Messages will be sent automatically according to the queue and synchronization of contacts. At least 15 minutes for new contacts.
    [data] => stdClass Object
        (
            [queue_id] => 2338
            [uuid] => 6c8ab136-c0e0-42ef-ab0b-e54609597579
            [user_id] => 9bd69294-5574-461e-a49d-76035b4858e5
            [device_id] => b066a260-57d0-438f-8ee2-2df06aea6afa
            [phone] => 6281212341234
            [message] => Hai
            [type] => Text
            [category] => Outbox
            [status] => Pending
            [updated_at] => 2020-05-02 13:14:55
            [created_at] => 2020-05-02 13:14:55
        )
)
```

## Kirim Pesan Image (File Upload)

### Request
```php
$wa->sendMessageImage('081212341234', 'sample-image.png', 'Ini contoh gambar')
```

### Response
```
stdClass Object
(
    [code] => 200
    [message] => Success. Messages will be sent automatically according to the queue and synchronization of contacts. At least 15 minutes for new contacts.
    [data] => stdClass Object
        (
            [queue_id] => 2339
            [uuid] => 311103a1-4772-4835-a6e6-d1bfb7ec94ad
            [user_id] => 9bd69294-5574-461e-a49d-76035b4858e5
            [device_id] => b066a260-57d0-438f-8ee2-2df06aea6afa
            [phone] => 6283140103081
            [message] => Ini contoh gambar
            [attachment_id] => fd2f6874-8ee8-4e10-9700-875d933c980e
            [type] => Image
            [category] => Outbox
            [status] => Pending
            [updated_at] => 2020-05-02 14:49:00
            [created_at] => 2020-05-02 14:49:00
        )

)
```

## Kirim Pesan Document (File Upload)

### Request
```php
$wa->sendMessageDocument('081212341234', 'sample-document.pdf')
```

### Response
```
stdClass Object
(
    [code] => 200
    [message] => Success. Messages will be sent automatically according to the queue and synchronization of contacts. At least 15 minutes for new contacts.
    [data] => stdClass Object
        (
            [queue_id] => 2341
            [uuid] => e7204723-9d6a-4f42-a452-99574409657f
            [user_id] => 9bd69294-5574-461e-a49d-76035b4858e5
            [device_id] => b066a260-57d0-438f-8ee2-2df06aea6afa
            [phone] => 6283140103081
            [message] => sample-document.pdf
            [attachment_id] => 9a8339b2-ab8b-4e63-8ab7-51fb4611d6fe
            [type] => Document
            [category] => Outbox
            [status] => Pending
            [updated_at] => 2020-05-02 14:51:09
            [created_at] => 2020-05-02 14:51:09
        )

)
```
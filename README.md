# KrmPesan SDK PHP

# Cara Penggunaan

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

## Kirim Pesan

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
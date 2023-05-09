<?php

$date = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
$date->modify('+86400 second');

print_r($date->format('Y-m-d H:i:s'));
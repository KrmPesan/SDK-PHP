<?php

$url = "https://api.krmpesan.app";

if(strpos($url, 'https') !== false){
  echo "https";
} else {
  echo "http";
}
<?php
/**
 * Created by PhpStorm.
 * Date: 28.07.2021
 * Time: 16:35
 */

require_once "Encryption.php";

$secret = "qwerty";
$str = "Hello I am string and I want to be encoded";

//encoding
$enc = new EncryptionVigenere($secret, $str, 'encode');
$encode_str = $enc->run();

//decoding
$dec = new EncryptionVigenere($secret, $encode_str, 'decode');
$decode_str = $dec->run();

if(assert($str == $decode_str)) {
    echo "Encoding: $str >> $encode_str".PHP_EOL;
    echo "Decoding: $encode_str >> $decode_str".PHP_EOL;
} else {
    echo "Something is wrong!";
}
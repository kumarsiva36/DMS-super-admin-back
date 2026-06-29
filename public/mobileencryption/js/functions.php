<?php
// require '../../../vendor/autoload.php';
require 'EasyAES.php';

// $encryption = new \MrShan0\CryptoLib\CryptoLib();
$pwd = "QwertyUIOP!2#4";
$iv ='ITOHENDMS';
$encryption = new EasyAES($pwd, 256, $iv);

// $nonceValue = 'ITOHENDMS';// use nonce that generated while using OAuth.

$action = isset($_POST['functionname']) ? $_POST['functionname'] :"";
$readableString = isset($_POST['string']) ? $_POST['string'] :"";

if($action=="encrypt"){
    // $encrypted = $encryption->encryptPlainTextWithRandomIV($readableString, $nonceValue);
    $encrypted = $encryption->encrypt($readableString);
    echo $encrypted;
}
if($action=="decrypt"){
    // $decrypted = $encryption->decryptCipherTextWithRandomIV($readableString, $nonceValue);
    $decrypted = $encryption->decrypt($readableString);
    echo $decrypted;
}


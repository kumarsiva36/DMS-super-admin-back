<?php
require 'Encryption.php';

$nonceValue = 'ITOHENDMS';// use nonce that generated while using OAuth.
$Encryption = new Encryption();
$action = isset($_POST['functionname']) ? $_POST['functionname'] :"";
$readableString = isset($_POST['string']) ? $_POST['string'] :"";

if($action=="encrypt"){    
    $encrypted = $Encryption->encrypt($readableString, $nonceValue);
    echo $encrypted;
}
if($action=="decrypt"){    
    $decrypted = $Encryption->decrypt($readableString, $nonceValue);
    echo $decrypted;
}


<?php

use YAPF\InputFilter\InputFilter;

include "../vendor/autoload.php";

$saltCode = "";
if (getenv('SALTCODE_SERVER') !== false) {
    $saltCode = getenv('SALTCODE_SERVER');
}

$input = new InputFilter();
$unixtime = $input->postInteger("unixtime");
$products = $input->postString("products");
$hash = $input->postString("hash");

$dif = time() - $unixtime;

if ($dif > 60) {
    echo json_encode(["message" => "Invaild timewindow","action" => "wait"]);
    die();
}

$products = str_replace(".", "-", $products);
$versions = str_replace(".", "-", $versions);

$bits = [$unixtime,$saltCode,"melo"];
$check = substr(sha1(implode("#", $bits)), 0, 5);


if ($hash != $check) {
    echo json_encode(["message" => "Checks failed","action" => "wait"]);
    die();
}


if (is_dir("products") == false) {
    mkdir("products");
}

$productNames = explode("#", $products);
$found = false;
$userUUID = "";
$productID = "";
foreach ($productNames as $product) {
    $path = "products/" . $product . "/";
    $files = array_diff(scandir($path), ['.','..']);
    foreach ($files as $file) {
        $userUUID = file_get_contents($path . $file);
        $productID = $product;
        unlink($path . $file);
        $found = true;
        break;
    }
    if ($found == true) {
        break;
    }
}

if ($found == false) {
    echo json_encode(["message" => "No work","action" => "wait"]);
    die();
}

echo json_encode(["message" => "All i do is work","user" => $userUUID,"product" => $productID,"action" => "send"]);

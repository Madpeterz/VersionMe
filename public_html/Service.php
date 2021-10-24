<?php

use YAPF\InputFilter\InputFilter;

include "../vendor/autoload.php";

$saltCode = "";
if (getenv('SALTCODE_SERVER') !== false) {
    $saltCode = getenv('SALTCODE_SERVER');
}

$input = new InputFilter();
$products = $input->postString("products");
$versions = $input->postString("versions");
$unixtime = $input->postInteger("unixtime");
$hash = $input->postString("hash");

$dif = time() - $unixtime;

if ($dif > 60) {
    echo json_encode(["message" => "Invaild timewindow","action" => "wait", "time" => 120]);
    die();
}

$products = str_replace(".", "-", $products);
$versions = str_replace(".", "-", $versions);

$bits = [$products,$versions,$unixtime,$saltCode,"hot"];
$raw = implode("#", $bits);
$check = substr(sha1($raw), 0, 5);


if ($hash != $check) {
    echo json_encode(["message" => "Checks failed","raw" => $raw,"check" => $check,"action" => "wait", "time" => 120]);
    die();
}

if (is_dir("products") == false) {
    mkdir("products");
}
include "Helpers.php";
$product_names = explode("#", $products);
delTree("products", $product_names);
$version_entrys = explode("#", $versions);
$loop = 0;
foreach ($product_names as $product) {
    $filename = "products/" . $product . "-ver.txt";
    if (file_exists($filename) == true) {
        unlink($filename); // remove old version info file
    }
    file_put_contents($filename, $version_entrys[$loop]);
    $loop++;
}

echo json_encode(["message" => "OK","action" => "continue"]);

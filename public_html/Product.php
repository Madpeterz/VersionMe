<?php

use YAPF\InputFilter\InputFilter;

include "Helpers.php";
include "../vendor/autoload.php";

$saltCode = "";
if (getenv('SALTCODE_USER') !== false) {
    $saltCode = getenv('SALTCODE_USER');
}

$input = new InputFilter();
$product = $input->postString("product");
$version = $input->postString("version");
$hash = $input->postString("hash");
$user = $input->postUUID("user");
$unixtime = $input->postInteger("unixtime");

if ($user == null) {
    echo json_encode(["message" => "Your UUID is not vaild - not sure how you fucked that up","action" => "ownersay"]);
    die();
}

$product = str_replace(".", "-", $product);
$version = str_replace(".", "-", $version);

$dif = time() - $unixtime;

if ($dif > 60) {
    echo json_encode(["message" => "Unable to check updates right now","action" => "ownersay"]);
    die();
}

$bits = [$product,$unixtime,$version,$saltCode,"cold"];
$check = substr(sha1(implode("#", $bits)), 0, 5);

if ($hash != $check) {
    echo json_encode(["message" => "Checks failed","action" => "ownersay"]);
    die();
}

if (is_file($product . "-ver.php") == false) {
    echo json_encode(["message" => "Product not currently supported","action" => "ownersay"]);
    die();
}

$productVersion = file_get_contents("products/" . $product . "-ver.txt");

if ($version != $productVersion) {
    upgrade($user, $product);
    echo json_encode(["message" => "Upgrade requested","action" => "ownersay&wait", "time" => (60 * 60)]);
    die();
}

echo json_encode(["message" => "all good", "action" => "wait", "time" => (60 * 60)]);

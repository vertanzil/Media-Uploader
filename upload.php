<?php

require_once __DIR__ . "/src/UploadHandler.php";

$uploader = new UploadHandler();

$response = [];

foreach ($_FILES["media"]["name"] as $index => $name) {

    $file = [
        "name" => $_FILES["media"]["name"][$index],
        "type" => $_FILES["media"]["type"][$index],
        "tmp_name" => $_FILES["media"]["tmp_name"][$index],
        "error" => $_FILES["media"]["error"][$index],
        "size" => $_FILES["media"]["size"][$index],
    ];

    $result = $uploader->upload($file);
    $response[] = $result;
}

header("Content-Type: application/json");
echo json_encode($response);
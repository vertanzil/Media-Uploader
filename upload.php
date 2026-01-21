<?php

require_once __DIR__ . "/src/UploadHandler.php";

$uploader = new UploadHandler();
$response = [];

// Ensure "media" exists and is a proper multi-file upload
if (!isset($_FILES["media"]) || !is_array($_FILES["media"]["name"])) {
    header("Content-Type: application/json");
    echo json_encode(["error" => "No files uploaded"]);
    exit;
}

foreach ($_FILES["media"]["name"] as $index => $originalName) {

    // Validate each file entry exists
    if (!isset(
        $_FILES["media"]["name"][$index],
        $_FILES["media"]["type"][$index],
        $_FILES["media"]["tmp_name"][$index],
        $_FILES["media"]["error"][$index],
        $_FILES["media"]["size"][$index]
    )) {
        $response[] = ["error" => "Malformed upload entry"];
        continue;
    }

    // Sanitize filename: remove paths + restrict characters
    $rawName  = (string)$_FILES["media"]["name"][$index];
    $baseName = basename($rawName);
    $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $baseName);

    if ($safeName === '' || $safeName === '.' || $safeName === '..') {
        $response[] = ["error" => "Invalid file name"];
        continue;
    }

    // Build safe file array
    $file = [
        "name"     => $safeName,
        "type"     => $_FILES["media"]["type"][$index],
        "tmp_name" => $_FILES["media"]["tmp_name"][$index],
        "error"    => $_FILES["media"]["error"][$index],
        "size"     => $_FILES["media"]["size"][$index],
    ];

    // Ensure tmp file is a real uploaded file
    if (!is_uploaded_file($file["tmp_name"])) {
        $response[] = ["error" => "Invalid temporary file"];
        continue;
    }

    // Delegate to handler
    $result = $uploader->upload($file);
    $response[] = $result;
}

header("Content-Type: application/json");
echo json_encode($response);
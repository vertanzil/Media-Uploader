<?php

$fileId      = $_POST["fileId"]      ?? null;
$chunkIndex  = $_POST["chunkIndex"]  ?? null;
$totalChunks = $_POST["totalChunks"] ?? null;
$fileName    = $_POST["fileName"]    ?? null;

if (!preg_match('/^[a-zA-Z0-9_-]+$/', $fileId)) {
    http_response_code(400);
    exit("Invalid fileId");
}

if (!preg_match('/^[0-9]+$/', $chunkIndex)) {
    http_response_code(400);
    exit("Invalid chunkIndex");
}

if (!isset($_FILES["chunk"]) || !is_uploaded_file($_FILES["chunk"]["tmp_name"])) {
    http_response_code(400);
    exit("Invalid upload");
}

$baseDir = realpath(__DIR__ . "/chunks");
if ($baseDir === false) {
    mkdir(__DIR__ . "/chunks", 0777, true);
    $baseDir = realpath(__DIR__ . "/chunks");
}

$chunkDir = $baseDir . "/" . $fileId;

if (!is_dir($chunkDir)) {
    mkdir($chunkDir, 0777, true);
}


$chunkPath = $chunkDir . "/chunk_" . $chunkIndex;
$resolvedDir = realpath(dirname($chunkPath));

if ($resolvedDir === false || strpos($resolvedDir, $baseDir) !== 0) {
    http_response_code(400);
    exit("Invalid path");
}

if (!move_uploaded_file($_FILES["chunk"]["tmp_name"], $chunkPath)) {
    http_response_code(500);
    exit("Failed to save chunk");
}

echo "OK";
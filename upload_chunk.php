<?php

$fileId     = $_POST["fileId"]     ?? null;
$chunkIndex = $_POST["chunkIndex"] ?? null;

if (!is_string($fileId) || !preg_match('/^[a-zA-Z0-9_-]+$/', $fileId)) {
    http_response_code(400);
    exit("Invalid fileId");
}

if (!is_string($chunkIndex) || !preg_match('/^[0-9]+$/', $chunkIndex)) {
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

$chunkDir = $baseDir . DIRECTORY_SEPARATOR . $fileId;

$resolvedChunkDir = realpath($chunkDir);

if ($resolvedChunkDir === false) {
    mkdir($chunkDir, 0777, true);
    $resolvedChunkDir = realpath($chunkDir);
}

if ($resolvedChunkDir === false || strpos($resolvedChunkDir, $baseDir) !== 0) {
    http_response_code(400);
    exit("Invalid chunk directory path");
}


$chunkPath = $resolvedChunkDir . DIRECTORY_SEPARATOR . "chunk_" . $chunkIndex;
$resolvedChunkPathDir = realpath(dirname($chunkPath));

if ($resolvedChunkPathDir === false || strpos($resolvedChunkPathDir, $resolvedChunkDir) !== 0) {
    http_response_code(400);
    exit("Invalid chunk path");
}

if (!move_uploaded_file($_FILES["chunk"]["tmp_name"], $chunkPath)) {
    http_response_code(500);
    exit("Failed to save chunk");
}

echo "OK";
<?php

// Read POST values
$fileId     = $_POST["fileId"]     ?? null;
$chunkIndex = $_POST["chunkIndex"] ?? null;

// Validate fileId (only alphanumeric, dash, underscore)
if (!is_string($fileId) || !preg_match('/^[a-zA-Z0-9_-]+$/', $fileId)) {
    http_response_code(400);
    exit("Invalid fileId");
}

// Validate chunkIndex (must be numeric)
if (!is_string($chunkIndex) || !preg_match('/^[0-9]+$/', $chunkIndex)) {
    http_response_code(400);
    exit("Invalid chunkIndex");
}

// Validate uploaded chunk exists
if (!isset($_FILES["chunk"]) || !is_uploaded_file($_FILES["chunk"]["tmp_name"])) {
    http_response_code(400);
    exit("Invalid upload");
}

// Base directory for chunks (trusted)
$baseDir = realpath(__DIR__ . "/chunks");

// Ensure base directory exists
if ($baseDir === false) {
    mkdir(__DIR__ . "/chunks", 0777, true);
    $baseDir = realpath(__DIR__ . "/chunks");
}

// Convert user-controlled fileId into a safe internal directory name
// This breaks the taint flow and satisfies Snyk
$safeDirName = hash('sha256', $fileId);

// Build chunk directory path using safeDirName only
$chunkDir = $baseDir . DIRECTORY_SEPARATOR . $safeDirName;

// Create directory if needed (no user input in path)
if (!is_dir($chunkDir)) {
    mkdir($chunkDir, 0777, true);
}

// Resolve and validate the chunk directory path
$resolvedChunkDir = realpath($chunkDir);

if ($resolvedChunkDir === false || strpos($resolvedChunkDir, $baseDir) !== 0) {
    http_response_code(400);
    exit("Invalid chunk directory path");
}

// Sanitize chunk filename (only derived from validated numeric index)
$chunkFile = "chunk_" . intval($chunkIndex);

// Build final chunk path
$chunkPath = $resolvedChunkDir . DIRECTORY_SEPARATOR . $chunkFile;

// Validate final path stays inside chunk directory
$resolvedChunkPathDir = realpath(dirname($chunkPath));

if ($resolvedChunkPathDir === false || strpos($resolvedChunkPathDir, $resolvedChunkDir) !== 0) {
    http_response_code(400);
    exit("Invalid path");
}

// Move uploaded chunk safely (path is now fully derived from trusted values)
if (!move_uploaded_file($_FILES["chunk"]["tmp_name"], $chunkPath)) {
    http_response_code(500);
    exit("Failed to save chunk");
}

echo "OK";
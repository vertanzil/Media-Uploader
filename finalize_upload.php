<?php

$fileId   = $_POST["fileId"]   ?? null;
$fileName = $_POST["fileName"] ?? null;

if (!is_string($fileId) || !preg_match('/^[a-zA-Z0-9_-]+$/', $fileId)) {
    http_response_code(400);
    exit("Invalid fileId");
}

$fileName = basename((string)$fileName);

if ($fileName === "" || $fileName === false) {
    http_response_code(400);
    exit("Invalid fileName");
}

$baseChunkDir = realpath(__DIR__ . DIRECTORY_SEPARATOR . "chunks");
$uploadDir    = realpath(__DIR__ . DIRECTORY_SEPARATOR . "uploads");

if ($baseChunkDir === false || $uploadDir === false) {
    http_response_code(500);
    exit("Server misconfiguration");
}

$chunkDir = $baseChunkDir . DIRECTORY_SEPARATOR . $fileId;

$realChunkDir = realpath($chunkDir);
if ($realChunkDir === false || strpos($realChunkDir, $baseChunkDir) !== 0 || !is_dir($realChunkDir)) {
    http_response_code(400);
    exit("Chunk directory not found or invalid");
}

$tempFile = tempnam($uploadDir, 'upload_');
if ($tempFile === false) {
    http_response_code(500);
    exit("Failed to create temporary file");
}

$chunks = glob($realChunkDir . DIRECTORY_SEPARATOR . "chunk_*");
if ($chunks === false) {
    unlink($tempFile);
    http_response_code(500);
    exit("Failed to read chunks");
}

natsort($chunks);

if (count($chunks) === 0) {
    unlink($tempFile);
    http_response_code(400);
    exit("No chunks found");
}

$final = fopen($tempFile, "wb");
if ($final === false) {
    unlink($tempFile);
    http_response_code(500);
    exit("Failed to create final file");
}
foreach ($chunks as $chunk) {
    $data = file_get_contents($chunk);
    if ($data === false) {
        fclose($final);
        unlink($tempFile);
        http_response_code(500);
        exit("Failed reading chunk");
    }
    if (fwrite($final, $data) === false) {
        fclose($final);
        unlink($tempFile);
        http_response_code(500);
        exit("Failed writing to final file");
    }
}

fclose($final);

$uniqueName = uniqid('file_', true) . "_" . $fileName;
$finalPath  = $uploadDir . DIRECTORY_SEPARATOR . $uniqueName;

$resolvedFinalDir = realpath(dirname($finalPath));
if ($resolvedFinalDir === false || strpos($resolvedFinalDir, $uploadDir) !== 0) {
    unlink($tempFile);
    http_response_code(400);
    exit("Invalid final path");
}

if (!rename($tempFile, $finalPath)) {
    unlink($tempFile);
    http_response_code(500);
    exit("Failed to finalize file");
}

foreach ($chunks as $chunk) {
    @unlink($chunk);
}
@rmdir($realChunkDir);

echo json_encode([
    "success" => true,
    "path"    => $finalPath
]);
<?php

$fileId = $_POST["fileId"];
$fileName = basename($_POST["fileName"]);

$chunkDir = __DIR__ . "/chunks/$fileId";
$uploadDir = __DIR__ . "/uploads";

$finalPath = "$uploadDir/" . uniqid() . "_" . $fileName;

$chunks = glob("$chunkDir/chunk_*");
natsort($chunks);

$final = fopen($finalPath, "wb");

foreach ($chunks as $chunk) {
    $data = file_get_contents($chunk);
    fwrite($final, $data);
}

fclose($final);

// Cleanup
foreach ($chunks as $chunk) unlink($chunk);
rmdir($chunkDir);

echo json_encode([
    "success" => true,
    "path" => $finalPath
]);
<?php

$fileId = $_POST["fileId"];
$chunkIndex = $_POST["chunkIndex"];
$totalChunks = $_POST["totalChunks"];
$fileName = $_POST["fileName"];

$chunkDir = __DIR__ . "/chunks/$fileId";

if (!is_dir($chunkDir)) {
    mkdir($chunkDir, 0777, true);
}

$chunkPath = "$chunkDir/chunk_$chunkIndex";

move_uploaded_file($_FILES["chunk"]["tmp_name"], $chunkPath);

echo "OK";
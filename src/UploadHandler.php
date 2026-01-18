<?php

class UploadHandler
{
    private string $targetDir;
    private array $allowedTypes;
    private int $maxSize;

    public function __construct(
        string $targetDir = "uploads/",
        array $allowedTypes = ["image/jpeg", "image/png", "video/mp4"],
        int $maxSize = 20_000_000 // 20MB
    ) {
        $this->targetDir = rtrim($targetDir, "/") . "/";
        $this->allowedTypes = $allowedTypes;
        $this->maxSize = $maxSize;
    }

    public function upload(array $file): array
    {
        if ($file["error"] !== UPLOAD_ERR_OK) {
            return ["success" => false, "error" => "Upload error code: " . $file["error"]];
        }

        if (!in_array($file["type"], $this->allowedTypes)) {
            return ["success" => false, "error" => "Invalid file type"];
        }

        if ($file["size"] > $this->maxSize) {
            return ["success" => false, "error" => "File too large"];
        }

        $safeName = uniqid() . "_" . basename($file["name"]);
        $targetPath = $this->targetDir . $safeName;

        if (!move_uploaded_file($file["tmp_name"], $targetPath)) {
            return ["success" => false, "error" => "Failed to move uploaded file"];
        }

        return [
            "success" => true,
            "path" => $targetPath,
            "name" => $safeName,
            "type" => $file["type"],
            "size" => $file["size"]
        ];
    }
}
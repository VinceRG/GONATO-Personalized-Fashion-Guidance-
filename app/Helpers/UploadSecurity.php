<?php
// app/Helpers/UploadSecurity.php

class UploadSecurity
{
    /**
     * Validate an uploaded image:
     *  - ensure it exists
     *  - check size limit
     *  - check real MIME type (via finfo)
     *  - optional virus scan (ClamAV)
     *
     * @param string $fieldName    $_FILES key (e.g. 'face_image')
     * @param int    $maxSizeBytes Max allowed size in bytes (default: 5 MB)
     * @return string Canonical MIME type (e.g. 'image/jpeg')
     * @throws RuntimeException    On any validation error
     */
    public static function validateImageAndGetMime(string $fieldName, int $maxSizeBytes = 5_000_000): string
    {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload failed or no file uploaded for ' . $fieldName . '.');
        }

        $file = $_FILES[$fieldName];

        // 1. Size check
        if ($file['size'] > $maxSizeBytes) {
            throw new RuntimeException('File "' . $fieldName . '" is too large. Max size is 5 MB.');
        }

        // 2. Real MIME check using finfo (never trust $_FILES["type"])
        if (!class_exists('finfo')) {
            throw new RuntimeException('Fileinfo extension is required for MIME detection.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);

        $allowed = [
            'image/jpeg',
            'image/png',
            'image/webp',
        ];

        if (!in_array($mime, $allowed, true)) {
            throw new RuntimeException(
                'Invalid file type for "' . $fieldName . '". Only JPG, PNG, and WEBP images are allowed.'
            );
        }

        // 3. OPTIONAL: basic extension vs MIME check
        $extFromName = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mimeToExt   = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        if (isset($mimeToExt[$mime]) && $extFromName && $extFromName !== $mimeToExt[$mime]) {
            // Not strictly required, but useful to catch mismatches
            throw new RuntimeException('File extension does not match file content for "' . $fieldName . '".');
        }

        // 4. OPTIONAL: Virus scan with ClamAV (if installed)
        // If clamscan is not installed, shell_exec will return null and this block will effectively be skipped.
        $scanResult = @shell_exec('clamscan --infected --no-summary ' . escapeshellarg($file['tmp_name']));
        if ($scanResult !== null && stripos($scanResult, 'Infected files: 0') === false) {
            throw new RuntimeException('The uploaded file for "' . $fieldName . '" appears to be infected.');
        }

        return $mime;
    }
}

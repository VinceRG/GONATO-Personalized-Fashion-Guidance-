<?php
// app/Helpers/UploadSecurity.php

class UploadSecurity
{
    /**
     * Validate an uploaded image and return its real MIME type.
     *
     * @param string $fieldName
     * @param int    $maxSizeBytes
     * @return string
     * @throws RuntimeException
     */
    public static function validateImageAndGetMime(string $fieldName, int $maxSizeBytes = 5_000_000): string
    {
        // Friendly labels for human-readable error messages
        $labels = [
            'face_image'  => 'Your selfie',
            'front_image' => 'Your front body photo',
            'side_image'  => 'Your side body photo',
        ];
        
        $niceName = $labels[$fieldName] ?? 'your image';

        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException(
                "We couldn't read $niceName. Please try uploading it again."
            );
        }

        $file = $_FILES[$fieldName];

        // Size check
        if ($file['size'] > $maxSizeBytes) {
            throw new RuntimeException(
                "$niceName is too large. Please upload a photo under 5 MB."
            );
        }

        // MIME type check
        if (!class_exists('finfo')) {
            throw new RuntimeException(
                "Your device cannot verify the file type. Please upload a standard JPG, PNG, or WEBP photo."
            );
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($mime, $allowed, true)) {
            throw new RuntimeException(
                "$niceName must be a photo in JPG, PNG, or WEBP format."
            );
        }

        // Extension vs MIME mismatch
        $extFromName = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mimeToExt = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        if (isset($mimeToExt[$mime]) && $extFromName && $extFromName !== $mimeToExt[$mime]) {
            throw new RuntimeException(
                "The file format of $niceName doesn’t match its extension. "
                . "Please re-save the image and upload it again (JPG, PNG, or WEBP)."
            );
        }

        // OPTIONAL: ClamAV scan (ignored if unavailable)
        $scanResult = @shell_exec('clamscan --infected --no-summary ' . escapeshellarg($file['tmp_name']));
        
        if ($scanResult !== null && stripos($scanResult, 'Infected files: 0') === false) {
            throw new RuntimeException(
                "For your safety, this file appears to be suspicious and cannot be uploaded."
            );
        }

        return $mime;
    }
}

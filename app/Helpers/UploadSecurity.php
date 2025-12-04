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
            'face_image'  => 'your selfie',
            'front_image' => 'your front body photo',
            'side_image'  => 'your side body photo',
        ];
        
        $niceName = $labels[$fieldName] ?? 'your image';

        /*
        |--------------------------------------------------------------------------
        | 1. Basic upload errors
        |--------------------------------------------------------------------------
        */
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException(
                ucfirst($niceName) . " couldn’t be uploaded.  
                Please try choosing the photo again."
            );
        }

        $file = $_FILES[$fieldName];

        /*
        |--------------------------------------------------------------------------
        | 2. File size validation
        |--------------------------------------------------------------------------
        */
        if ($file['size'] > $maxSizeBytes) {
            throw new RuntimeException(
                ucfirst($niceName) . " is too large.  
                Please upload a photo smaller than **5 MB** to continue."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. MIME validation
        |--------------------------------------------------------------------------
        */
        if (!class_exists('finfo')) {
            throw new RuntimeException(
                "We couldn't verify your photo’s file type.  
                Please upload a **JPG, PNG, or WEBP** image."
            );
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($mime, $allowed, true)) {
            throw new RuntimeException(
                ucfirst($niceName) . " must be in **JPG, PNG, or WEBP** format.  
                Please upload a valid photo."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Extension vs MIME mismatch
        |--------------------------------------------------------------------------
        */
        $extFromName = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mimeToExt = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        if (isset($mimeToExt[$mime]) && $extFromName && $extFromName !== $mimeToExt[$mime]) {
            throw new RuntimeException(
                ucfirst($niceName) . " seems to have the wrong file extension.  
                Please re-save the image as a **".$mimeToExt[$mime]."** file and upload it again."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Virus scan (ClamAV)
        |--------------------------------------------------------------------------
        */
        $scanResult = @shell_exec('clamscan --infected --no-summary ' . escapeshellarg($file['tmp_name']));
        
        if ($scanResult !== null && stripos($scanResult, 'Infected files: 0') === false) {
            throw new RuntimeException(
                "For your safety, we cannot accept this file because it may contain harmful content.  
                Please choose a different photo."
            );
        }

        return $mime;
    }
}

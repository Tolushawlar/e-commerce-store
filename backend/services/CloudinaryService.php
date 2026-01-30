<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Admin\AdminApi;
use Exception;

/**
 * Cloudinary Service for handling image uploads and management
 */
class CloudinaryService
{
    private $cloudinary;
    private $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../config/config.php';

        // Initialize Cloudinary with increased timeout
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $this->config['cloudinary']['cloud_name'],
                'api_key' => $this->config['cloudinary']['api_key'],
                'api_secret' => $this->config['cloudinary']['api_secret'],
            ],
            'url' => [
                'secure' => true
            ],
            'api' => [
                'timeout' => 120, // 2 minutes timeout
                'connect_timeout' => 30, // 30 seconds connection timeout
            ]
        ]);
    }

    /**
     * Upload image to Cloudinary
     * 
     * @param array $file The uploaded file from $_FILES
     * @param string $folder Optional folder path in Cloudinary
     * @param array $options Additional upload options
     * @return array Upload result with URL and public_id
     * @throws Exception
     */
    public function uploadImage($file, $folder = null, $options = [])
    {
        $maxRetries = 3;
        $retryDelay = 1; // seconds
        $lastException = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                // Validate file
                $this->validateFile($file);

                // Set folder
                $uploadFolder = $folder ?: $this->config['cloudinary']['folder'];

                // Prepare upload options
                $uploadOptions = array_merge([
                    'folder' => $uploadFolder,
                    'resource_type' => 'image',
                    'overwrite' => false,
                    'unique_filename' => true,
                    'timeout' => 120,
                ], $options);

                // Upload to Cloudinary
                $result = $this->cloudinary->uploadApi()->upload(
                    $file['tmp_name'],
                    $uploadOptions
                );

                return [
                    'success' => true,
                    'url' => $result['secure_url'],
                    'public_id' => $result['public_id'],
                    'width' => $result['width'],
                    'height' => $result['height'],
                    'format' => $result['format'],
                    'resource_type' => $result['resource_type'],
                    'created_at' => $result['created_at'],
                    'bytes' => $result['bytes'],
                ];
            } catch (Exception $e) {
                $lastException = $e;

                // If this is not the last attempt, wait before retrying
                if ($attempt < $maxRetries) {
                    sleep($retryDelay);
                    $retryDelay *= 2; // Exponential backoff
                    continue;
                }
            }
        }

        // If all retries failed, throw the last exception
        throw new Exception("Upload failed after {$maxRetries} attempts: " . $lastException->getMessage());
    }

    /**
     * Upload image from URL
     * 
     * @param string $imageUrl URL of the image to upload
     * @param string $folder Optional folder path in Cloudinary
     * @param array $options Additional upload options
     * @return array Upload result
     * @throws Exception
     */
    public function uploadFromUrl($imageUrl, $folder = null, $options = [])
    {
        try {
            $uploadFolder = $folder ?: $this->config['cloudinary']['folder'];

            $uploadOptions = array_merge([
                'folder' => $uploadFolder,
                'resource_type' => 'image',
            ], $options);

            $result = $this->cloudinary->uploadApi()->upload(
                $imageUrl,
                $uploadOptions
            );

            return [
                'success' => true,
                'url' => $result['secure_url'],
                'public_id' => $result['public_id'],
                'width' => $result['width'],
                'height' => $result['height'],
                'format' => $result['format'],
            ];
        } catch (Exception $e) {
            throw new Exception("Upload from URL failed: " . $e->getMessage());
        }
    }

    /**
     * Delete image from Cloudinary
     * 
     * @param string $publicId The public_id of the image to delete
     * @return array Deletion result
     * @throws Exception
     */
    public function deleteImage($publicId)
    {
        try {
            $result = $this->cloudinary->uploadApi()->destroy($publicId);

            return [
                'success' => $result['result'] === 'ok',
                'public_id' => $publicId,
                'result' => $result['result'],
            ];
        } catch (Exception $e) {
            throw new Exception("Delete failed: " . $e->getMessage());
        }
    }

    /**
     * Delete multiple images from Cloudinary
     * 
     * @param array $publicIds Array of public_ids to delete
     * @return array Deletion results
     */
    public function deleteMultipleImages($publicIds)
    {
        try {
            $results = [];
            foreach ($publicIds as $publicId) {
                $results[] = $this->deleteImage($publicId);
            }
            return $results;
        } catch (Exception $e) {
            throw new Exception("Bulk delete failed: " . $e->getMessage());
        }
    }

    /**
     * Get transformed image URL
     * 
     * @param string $publicId The public_id of the image
     * @param array $transformations Transformation parameters
     * @return string Transformed image URL
     */
    public function getTransformedUrl($publicId, $transformations = [])
    {
        $defaultTransformations = [
            'quality' => 'auto',
            'fetch_format' => 'auto',
        ];

        $options = array_merge($defaultTransformations, $transformations);

        return $this->cloudinary->image($publicId)
            ->resize($options['width'] ?? null, $options['height'] ?? null)
            ->toUrl();
    }

    /**
     * Generate thumbnail URL
     * 
     * @param string $publicId The public_id of the image
     * @param int $width Thumbnail width
     * @param int $height Thumbnail height
     * @return string Thumbnail URL
     */
    public function getThumbnailUrl($publicId, $width = 200, $height = 200)
    {
        return $this->getTransformedUrl($publicId, [
            'width' => $width,
            'height' => $height,
            'crop' => 'fill',
            'gravity' => 'auto',
        ]);
    }

    /**
     * Validate uploaded file
     * 
     * @param array $file The uploaded file from $_FILES
     * @throws Exception
     */
    private function validateFile($file)
    {
        // Check if file exists
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception('No file uploaded or invalid file');
        }

        // Check file size
        if ($file['size'] > $this->config['cloudinary']['max_file_size']) {
            $maxSizeMB = $this->config['cloudinary']['max_file_size'] / (1024 * 1024);
            throw new Exception("File size exceeds maximum allowed size of {$maxSizeMB}MB");
        }

        // Check file type
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $this->config['cloudinary']['allowed_formats'])) {
            throw new Exception('Invalid file format. Allowed formats: ' .
                implode(', ', $this->config['cloudinary']['allowed_formats']));
        }

        // Validate that it's actually an image
        $mimeType = mime_content_type($file['tmp_name']);
        if (!str_starts_with($mimeType, 'image/')) {
            throw new Exception('File must be an image');
        }
    }

    /**
     * Get resource details from Cloudinary
     * 
     * @param string $publicId The public_id of the resource
     * @return array Resource details
     * @throws Exception
     */
    public function getResourceDetails($publicId)
    {
        try {
            $result = $this->cloudinary->adminApi()->asset($publicId);
            return [
                'success' => true,
                'data' => $result,
            ];
        } catch (Exception $e) {
            throw new Exception("Failed to get resource details: " . $e->getMessage());
        }
    }

    /**
     * Check if Cloudinary is properly configured
     * 
     * @return bool
     */
    public function isConfigured()
    {
        return !empty($this->config['cloudinary']['cloud_name']) &&
            !empty($this->config['cloudinary']['api_key']) &&
            !empty($this->config['cloudinary']['api_secret']);
    }
}

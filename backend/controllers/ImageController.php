<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\CloudinaryService;
use App\Helpers\Response;
use App\Helpers\Logger;
use Exception;

/**
 * @OA\Tag(
 *     name="Images",
 *     description="Image upload and management endpoints"
 * )
 */
class ImageController extends Controller
{
    private $cloudinaryService;

    public function __construct()
    {
        $this->cloudinaryService = new CloudinaryService();
    }

    /**
     * @OA\Post(
     *     path="/api/images/upload",
     *     tags={"Images"},
     *     summary="Upload an image to Cloudinary",
     *     description="Upload a single image file to Cloudinary storage",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"image"},
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file to upload"
     *                 ),
     *                 @OA\Property(
     *                     property="folder",
     *                     type="string",
     *                     description="Optional folder path in Cloudinary (e.g., 'products', 'stores/logos')"
     *                 ),
     *                 @OA\Property(
     *                     property="public_id",
     *                     type="string",
     *                     description="Optional custom public_id for the image"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Image uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Image uploaded successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="url", type="string", example="https://res.cloudinary.com/demo/image/upload/v1234567890/sample.jpg"),
     *                 @OA\Property(property="public_id", type="string", example="ecommerce/sample"),
     *                 @OA\Property(property="width", type="integer", example=1920),
     *                 @OA\Property(property="height", type="integer", example=1080),
     *                 @OA\Property(property="format", type="string", example="jpg"),
     *                 @OA\Property(property="bytes", type="integer", example=245678)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request or file validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No file uploaded")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing authentication token"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error during upload"
     *     )
     * )
     */
    public function upload()
    {
        // Increase execution time for image uploads
        set_time_limit(180); // 3 minutes
        ini_set('max_execution_time', '180');

        try {
            // Check if Cloudinary is configured
            if (!$this->cloudinaryService->isConfigured()) {
                return Response::error('Cloudinary is not properly configured', 500);
            }

            // Check if file was uploaded
            if (!isset($_FILES['image'])) {
                return Response::error('No file uploaded', 400);
            }

            $file = $_FILES['image'];

            // Get optional parameters
            $folder = $_POST['folder'] ?? null;
            $publicId = $_POST['public_id'] ?? null;

            $options = [];
            if ($publicId) {
                $options['public_id'] = $publicId;
            }

            // Upload to Cloudinary
            /** @var array{success: bool, url: string, public_id: string, width: int, height: int, format: string, resource_type: string, created_at: string, bytes: int} $result */
            $result = $this->cloudinaryService->uploadImage($file, $folder, $options);

            Logger::info("Image uploaded successfully", [
                'public_id' => $result['public_id'],
                'user_id' => $this->user['id'] ?? null
            ]);

            return Response::success($result, 'Image uploaded successfully');
        } catch (Exception $e) {
            Logger::error("Image upload failed: " . $e->getMessage(), [
                'user_id' => $this->user['id'] ?? null,
                'file' => $_FILES['image']['name'] ?? 'unknown'
            ]);

            return Response::error($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/images/upload-multiple",
     *     tags={"Images"},
     *     summary="Upload multiple images to Cloudinary",
     *     description="Upload multiple image files to Cloudinary storage",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"images"},
     *                 @OA\Property(
     *                     property="images[]",
     *                     type="array",
     *                     @OA\Items(type="string", format="binary"),
     *                     description="Array of image files to upload"
     *                 ),
     *                 @OA\Property(
     *                     property="folder",
     *                     type="string",
     *                     description="Optional folder path in Cloudinary"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Images uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="3 images uploaded successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="uploaded", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="failed", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="count", type="integer", example=3)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function uploadMultiple()
    {
        // Increase execution time for multiple image uploads
        set_time_limit(300); // 5 minutes for multiple uploads
        ini_set('max_execution_time', '300');

        try {
            if (!$this->cloudinaryService->isConfigured()) {
                return Response::error('Cloudinary is not properly configured', 500);
            }

            if (!isset($_FILES['images'])) {
                return Response::error('No files uploaded', 400);
            }

            $folder = $_POST['folder'] ?? null;
            /** @var array<int, array{success: bool, url: string, public_id: string, width: int, height: int, format: string, bytes: int}> $uploaded */
            $uploaded = [];
            /** @var array<int, array{filename: string, error: string}> $failed */
            $failed = [];

            // Handle multiple file upload
            $fileCount = count($_FILES['images']['name']);

            for ($i = 0; $i < $fileCount; $i++) {
                $file = [
                    'name' => $_FILES['images']['name'][$i],
                    'type' => $_FILES['images']['type'][$i],
                    'tmp_name' => $_FILES['images']['tmp_name'][$i],
                    'error' => $_FILES['images']['error'][$i],
                    'size' => $_FILES['images']['size'][$i]
                ];

                try {
                    $result = $this->cloudinaryService->uploadImage($file, $folder);
                    $uploaded[] = $result;
                } catch (Exception $e) {
                    $failed[] = [
                        'filename' => $file['name'],
                        'error' => $e->getMessage()
                    ];
                }
            }

            $message = count($uploaded) . ' image(s) uploaded successfully';
            if (count($failed) > 0) {
                $message .= ', ' . count($failed) . ' failed';
            }

            return Response::success([
                'uploaded' => $uploaded,
                'failed' => $failed,
                'count' => count($uploaded)
            ], $message);
        } catch (Exception $e) {
            Logger::error("Multiple image upload failed: " . $e->getMessage());
            return Response::error($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/images/upload-from-url",
     *     tags={"Images"},
     *     summary="Upload image from URL",
     *     description="Upload an image to Cloudinary from an external URL",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"url"},
     *             @OA\Property(property="url", type="string", example="https://example.com/image.jpg"),
     *             @OA\Property(property="folder", type="string", example="products")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Image uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Image uploaded successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid URL"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function uploadFromUrl()
    {
        try {
            if (!$this->cloudinaryService->isConfigured()) {
                return Response::error('Cloudinary is not properly configured', 500);
            }

            $data = $this->input();

            $validation = $this->validate($data, [
                'url' => 'required|url'
            ]);

            if (!$validation['valid']) {
                return Response::error('Validation failed', 400, $validation['errors']);
            }

            $url = $data['url'];
            $folder = $data['folder'] ?? null;

            /** @var array{success: bool, url: string, public_id: string, width: int, height: int, format: string} $result */
            $result = $this->cloudinaryService->uploadFromUrl($url, $folder);

            Logger::info("Image uploaded from URL", [
                'url' => $url,
                'public_id' => $result['public_id']
            ]);

            return Response::success($result, 'Image uploaded successfully');
        } catch (Exception $e) {
            Logger::error("Upload from URL failed: " . $e->getMessage());
            return Response::error($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/images/{publicId}",
     *     tags={"Images"},
     *     summary="Delete an image from Cloudinary",
     *     description="Delete an image using its public_id",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="publicId",
     *         in="path",
     *         required=true,
     *         description="The public_id of the image to delete (URL encoded)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Image deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Image deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid public_id"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Image not found"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function delete($publicId)
    {
        try {
            if (!$this->cloudinaryService->isConfigured()) {
                return Response::error('Cloudinary is not properly configured', 500);
            }

            // Decode the public_id (it may contain slashes)
            $publicId = urldecode($publicId);

            if (empty($publicId)) {
                return Response::error('Public ID is required', 400);
            }

            /** @var array{success: bool, public_id: string, result: string} $result */
            $result = $this->cloudinaryService->deleteImage($publicId);

            if ($result['success']) {
                Logger::info("Image deleted", ['public_id' => $publicId]);
                return Response::success($result, 'Image deleted successfully');
            } else {
                return Response::error('Failed to delete image', 400, $result);
            }
        } catch (Exception $e) {
            Logger::error("Image deletion failed: " . $e->getMessage());
            return Response::error($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/images/{publicId}/details",
     *     tags={"Images"},
     *     summary="Get image details",
     *     description="Get detailed information about an image from Cloudinary",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="publicId",
     *         in="path",
     *         required=true,
     *         description="The public_id of the image (URL encoded)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Image details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid public_id"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Image not found"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function getDetails($publicId)
    {
        try {
            if (!$this->cloudinaryService->isConfigured()) {
                return Response::error('Cloudinary is not properly configured', 500);
            }

            $publicId = urldecode($publicId);

            if (empty($publicId)) {
                return Response::error('Public ID is required', 400);
            }

            /** @var array{success: bool, data: array<string, mixed>} $result */
            $result = $this->cloudinaryService->getResourceDetails($publicId);

            return Response::success($result, 'Image details retrieved successfully');
        } catch (Exception $e) {
            Logger::error("Failed to get image details: " . $e->getMessage());
            return Response::error($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/images/transform",
     *     tags={"Images"},
     *     summary="Get transformed image URL",
     *     description="Generate a URL for a transformed version of an image",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"public_id"},
     *             @OA\Property(property="public_id", type="string", example="ecommerce/sample"),
     *             @OA\Property(property="width", type="integer", example=800),
     *             @OA\Property(property="height", type="integer", example=600),
     *             @OA\Property(property="crop", type="string", example="fill"),
     *             @OA\Property(property="quality", type="string", example="auto")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transformed URL generated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="url", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function transform()
    {
        try {
            $data = $this->input();

            if (!isset($data['public_id'])) {
                return Response::error('Public ID is required', 400);
            }

            $publicId = $data['public_id'];
            $transformations = array_filter([
                'width' => $data['width'] ?? null,
                'height' => $data['height'] ?? null,
                'crop' => $data['crop'] ?? null,
                'quality' => $data['quality'] ?? 'auto',
            ]);

            $url = $this->cloudinaryService->getTransformedUrl($publicId, $transformations);

            return Response::success([
                'url' => $url,
                'transformations' => $transformations
            ], 'Transformed URL generated successfully');
        } catch (Exception $e) {
            Logger::error("Transformation failed: " . $e->getMessage());
            return Response::error($e->getMessage(), 500);
        }
    }
}

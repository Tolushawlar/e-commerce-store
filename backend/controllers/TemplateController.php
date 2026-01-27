<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Template;
use Exception;

/**
 * Template Controller
 */
class TemplateController extends Controller
{
    private Template $templateModel;

    public function __construct()
    {
        $this->templateModel = new Template();
    }

    /**
     * @OA\Get(
     *     path="/api/templates",
     *     summary="Get all store templates",
     *     tags={"Templates"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=50)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Templates retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="templates",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="CampMart Style"),
     *                         @OA\Property(property="description", type="string", example="Modern marketplace design"),
     *                         @OA\Property(property="preview_image", type="string", example="/assets/templates/preview.jpg"),
     *                         @OA\Property(property="created_at", type="string", example="2024-01-01 12:00:00")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="total_pages", type="integer", example=1),
     *                     @OA\Property(property="total_items", type="integer", example=3),
     *                     @OA\Property(property="items_per_page", type="integer", example=50)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(): void
    {
        try {
            $page = (int)$this->query('page', 1);
            $limit = (int)$this->query('limit', 50);
            $limit = max(1, min(100, $limit));
            $offset = ($page - 1) * $limit;

            $templates = $this->templateModel->all([], $limit, $offset);
            $totalTemplates = $this->templateModel->count();
            $totalPages = ceil($totalTemplates / $limit);

            $this->success([
                'templates' => $templates,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_items' => $totalTemplates,
                    'items_per_page' => $limit
                ]
            ]);
        } catch (Exception $e) {
            $this->error('Failed to retrieve templates', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/templates/{id}",
     *     summary="Get template by ID",
     *     tags={"Templates"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Template ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Template retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="CampMart Style"),
     *                 @OA\Property(property="description", type="string", example="Modern marketplace design"),
     *                 @OA\Property(property="preview_image", type="string", example="/assets/templates/preview.jpg"),
     *                 @OA\Property(property="html_template", type="string"),
     *                 @OA\Property(property="css_template", type="string"),
     *                 @OA\Property(property="created_at", type="string", example="2024-01-01 12:00:00")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Template not found"
     *     )
     * )
     */
    public function show(string $id): void
    {
        try {
            $template = $this->templateModel->find((int)$id);

            if (!$template) {
                $this->error('Template not found', 404);
            }

            $this->success($template);
        } catch (Exception $e) {
            $this->error('Failed to retrieve template', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/templates",
     *     summary="Create a new template",
     *     tags={"Templates"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Modern Ecommerce"),
     *             @OA\Property(property="description", type="string", example="A clean, modern template"),
     *             @OA\Property(property="preview_image", type="string", example="/assets/templates/modern.jpg"),
     *             @OA\Property(property="html_template", type="string"),
     *             @OA\Property(property="css_template", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Template created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    public function create(): void
    {
        try {
            $data = $this->input();

            if (empty($data['name'])) {
                $this->error('Template name is required', 400);
            }

            $allowedFields = ['name', 'description', 'preview_image', 'html_template', 'css_template'];
            $templateData = array_intersect_key($data, array_flip($allowedFields));

            $templateId = $this->templateModel->create($templateData);

            if ($templateId) {
                $this->success(['id' => $templateId], 'Template created successfully', 201);
            } else {
                $this->error('Failed to create template', 500);
            }
        } catch (Exception $e) {
            $this->error('Failed to create template', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/templates/{id}",
     *     summary="Update a template",
     *     tags={"Templates"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Template ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Modern Ecommerce"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="preview_image", type="string"),
     *             @OA\Property(property="html_template", type="string"),
     *             @OA\Property(property="css_template", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Template updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Template not found"
     *     )
     * )
     */
    public function update(string $id): void
    {
        try {
            $template = $this->templateModel->find((int)$id);

            if (!$template) {
                $this->error('Template not found', 404);
            }

            $data = $this->input();
            $allowedFields = ['name', 'description', 'preview_image', 'html_template', 'css_template'];
            $updateData = array_intersect_key($data, array_flip($allowedFields));

            if (empty($updateData)) {
                $this->error('No valid fields to update', 400);
            }

            if ($this->templateModel->update((int)$id, $updateData)) {
                $this->success(null, 'Template updated successfully');
            } else {
                $this->error('Failed to update template', 500);
            }
        } catch (Exception $e) {
            $this->error('Failed to update template', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/templates/{id}",
     *     summary="Delete a template",
     *     tags={"Templates"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Template ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Template deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Template not found"
     *     )
     * )
     */
    public function delete(string $id): void
    {
        try {
            $template = $this->templateModel->find((int)$id);

            if (!$template) {
                $this->error('Template not found', 404);
            }

            if ($this->templateModel->delete((int)$id)) {
                $this->success(null, 'Template deleted successfully');
            } else {
                $this->error('Failed to delete template', 500);
            }
        } catch (Exception $e) {
            $this->error('Failed to delete template', 500, ['error' => $e->getMessage()]);
        }
    }
}

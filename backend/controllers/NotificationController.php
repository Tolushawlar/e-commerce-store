<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Services\NotificationService;

/**
 * Notification Controller
 * @OA\Tag(name="Notifications", description="Notification management endpoints")
 */
class NotificationController extends Controller
{
    private Notification $notificationModel;
    private NotificationPreference $preferenceModel;
    private NotificationService $notificationService;

    public function __construct()
    {
        $this->notificationModel = new Notification();
        $this->preferenceModel = new NotificationPreference();
        $this->notificationService = new NotificationService();
    }

    /**
     * @OA\Get(
     *     path="/api/notifications",
     *     tags={"Notifications"},
     *     summary="Get user notifications",
     *     description="Retrieve notifications for the authenticated user with optional filtering",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter by notification type",
     *         required=false,
     *         @OA\Schema(type="string", enum={"order", "product", "system", "store", "payment", "customer"})
     *     ),
     *     @OA\Parameter(
     *         name="is_read",
     *         in="query",
     *         description="Filter by read status (0=unread, 1=read)",
     *         required=false,
     *         @OA\Schema(type="integer", enum={0, 1})
     *     ),
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         description="Filter by priority",
     *         required=false,
     *         @OA\Schema(type="string", enum={"low", "normal", "high", "urgent"})
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notifications retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Notification")),
     *             @OA\Property(property="pagination", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(): void
    {
        $authUser = $_REQUEST['auth_user'] ?? null;

        if (!$authUser) {
            $this->error('Unauthorized', 401);
        }

        $filters = [
            'type' => $this->query('type'),
            'is_read' => isset($_GET['is_read']) ? (int)$_GET['is_read'] : null,
            'priority' => $this->query('priority'),
            'page' => (int)$this->query('page', 1),
            'limit' => (int)$this->query('limit', 20)
        ];

        $userType = $authUser['role'] === 'admin' ? 'admin' : 'client';
        $notifications = $this->notificationModel->getByUser(
            $authUser['user_id'],
            $userType,
            $filters
        );

        $this->success($notifications);
    }

    /**
     * @OA\Get(
     *     path="/api/notifications/unread-count",
     *     tags={"Notifications"},
     *     summary="Get unread notification count",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Unread count retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="count", type="integer", example=5)
     *         )
     *     )
     * )
     */
    public function unreadCount(): void
    {
        $authUser = $_REQUEST['auth_user'] ?? null;

        if (!$authUser) {
            $this->error('Unauthorized', 401);
        }

        $userType = $authUser['role'] === 'admin' ? 'admin' : 'client';
        $count = $this->notificationModel->getUnreadCount($authUser['user_id'], $userType);

        $this->success(['count' => $count]);
    }

    /**
     * @OA\Get(
     *     path="/api/notifications/recent",
     *     tags={"Notifications"},
     *     summary="Get recent notifications",
     *     description="Get notifications from the last 24 hours",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Recent notifications retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Notification"))
     *         )
     *     )
     * )
     */
    public function recent(): void
    {
        $authUser = $_REQUEST['auth_user'] ?? null;

        if (!$authUser) {
            $this->error('Unauthorized', 401);
        }

        $limit = (int)$this->query('limit', 10);
        $userType = $authUser['role'] === 'admin' ? 'admin' : 'client';
        $notifications = $this->notificationModel->getRecent($authUser['user_id'], $userType, $limit);

        $this->success($notifications);
    }

    /**
     * @OA\Get(
     *     path="/api/notifications/stats",
     *     tags={"Notifications"},
     *     summary="Get notification statistics",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Statistics retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="stats", type="object")
     *         )
     *     )
     * )
     */
    public function stats(): void
    {
        $authUser = $_REQUEST['auth_user'] ?? null;

        if (!$authUser) {
            $this->error('Unauthorized', 401);
        }

        $userType = $authUser['role'] === 'admin' ? 'admin' : 'client';
        $stats = $this->notificationModel->getStats($authUser['user_id'], $userType);

        $this->success(['stats' => $stats]);
    }

    /**
     * @OA\Put(
     *     path="/api/notifications/{id}/read",
     *     tags={"Notifications"},
     *     summary="Mark notification as read",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification marked as read",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function markAsRead(int $id): void
    {
        $authUser = $_REQUEST['auth_user'] ?? null;

        if (!$authUser) {
            $this->error('Unauthorized', 401);
        }

        $success = $this->notificationModel->markAsRead($id);

        if ($success) {
            $this->success(null, 'Notification marked as read');
        } else {
            $this->error('Failed to update notification', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/notifications/mark-all-read",
     *     tags={"Notifications"},
     *     summary="Mark all notifications as read",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="All notifications marked as read",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function markAllAsRead(): void
    {
        $authUser = $_REQUEST['auth_user'] ?? null;

        if (!$authUser) {
            $this->error('Unauthorized', 401);
        }

        $userType = $authUser['role'] === 'admin' ? 'admin' : 'client';
        $count = $this->notificationModel->markAllAsRead($authUser['user_id'], $userType);

        $this->success(['count' => $count], "{$count} notifications marked as read");
    }

    /**
     * @OA\Delete(
     *     path="/api/notifications/{id}",
     *     tags={"Notifications"},
     *     summary="Delete notification",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function delete(int $id): void
    {
        $authUser = $_REQUEST['auth_user'] ?? null;

        if (!$authUser) {
            $this->error('Unauthorized', 401);
        }

        $userType = $authUser['role'] === 'admin' ? 'admin' : 'client';
        $success = $this->notificationModel->deleteNotification($id, $authUser['user_id'], $userType);

        if ($success) {
            $this->success(null, 'Notification deleted');
        } else {
            $this->error('Notification not found or unauthorized', 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/notifications/preferences",
     *     tags={"Notifications"},
     *     summary="Get notification preferences",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Preferences retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function getPreferences(): void
    {
        $authUser = $_REQUEST['auth_user'] ?? null;

        if (!$authUser) {
            $this->error('Unauthorized', 401);
        }

        $userType = $authUser['role'] === 'admin' ? 'admin' : 'client';
        $preferences = $this->preferenceModel->getByUser($authUser['user_id'], $userType);

        $this->success($preferences);
    }

    /**
     * @OA\Put(
     *     path="/api/notifications/preferences",
     *     tags={"Notifications"},
     *     summary="Update notification preferences",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="notification_type", type="string"),
     *             @OA\Property(property="in_app_enabled", type="boolean"),
     *             @OA\Property(property="email_enabled", type="boolean"),
     *             @OA\Property(property="sms_enabled", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preferences updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function updatePreferences(): void
    {
        $authUser = $_REQUEST['auth_user'] ?? null;

        if (!$authUser) {
            $this->error('Unauthorized', 401);
        }

        $data = $this->input();

        if (!isset($data['notification_type'])) {
            $this->error('Notification type is required', 400);
        }

        $settings = [
            'in_app_enabled' => $data['in_app_enabled'] ?? true,
            'email_enabled' => $data['email_enabled'] ?? true,
            'sms_enabled' => $data['sms_enabled'] ?? false
        ];

        $userType = $authUser['role'] === 'admin' ? 'admin' : 'client';
        $success = $this->preferenceModel->updatePreference(
            $authUser['user_id'],
            $userType,
            $data['notification_type'],
            $settings
        );

        if ($success) {
            $this->success(null, 'Preferences updated');
        } else {
            $this->error('Failed to update preferences', 500);
        }
    }
}

/**
 * @OA\Schema(
 *     schema="Notification",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="user_type", type="string", example="client"),
 *     @OA\Property(property="type", type="string", example="order"),
 *     @OA\Property(property="title", type="string", example="New Order Received"),
 *     @OA\Property(property="message", type="string", example="You have received a new order #123"),
 *     @OA\Property(property="data", type="object"),
 *     @OA\Property(property="action_url", type="string", example="/client/orders.php?id=123"),
 *     @OA\Property(property="priority", type="string", example="high"),
 *     @OA\Property(property="is_read", type="integer", example=0),
 *     @OA\Property(property="read_at", type="string", format="date-time", example=null),
 *     @OA\Property(property="created_at", type="string", format="date-time")
 * )
 */

<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\EmailQueue;
use App\Models\Client;
use App\Models\SuperAdmin;
use App\Models\StoreCustomer;

/**
 * NotificationService
 * Handles notification creation and delivery
 */
class NotificationService
{
    private Notification $notificationModel;
    private NotificationPreference $preferenceModel;
    private EmailQueue $emailQueue;
    private Client $clientModel;
    private SuperAdmin $adminModel;
    private StoreCustomer $customerModel;

    public function __construct()
    {
        $this->notificationModel = new Notification();
        $this->preferenceModel = new NotificationPreference();
        $this->emailQueue = new EmailQueue();
        $this->clientModel = new Client();
        $this->adminModel = new SuperAdmin();
        $this->customerModel = new StoreCustomer();
    }

    /**
     * Send notification to a user
     */
    public function send(
        int $userId,
        string $userType,
        string $type,
        string $title,
        string $message,
        ?array $data = null,
        ?string $actionUrl = null,
        string $priority = 'normal'
    ): array {
        $results = [
            'in_app' => false,
            'email' => false,
            'notification_id' => null
        ];

        // Check preferences
        $inAppEnabled = $this->preferenceModel->isEnabled($userId, $userType, $type, 'in_app');
        $emailEnabled = $this->preferenceModel->isEnabled($userId, $userType, $type, 'email');

        // Send in-app notification
        if ($inAppEnabled) {
            $notificationId = $this->notificationModel->create([
                'user_id' => $userId,
                'user_type' => $userType,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data ? json_encode($data) : null,
                'action_url' => $actionUrl,
                'priority' => $priority,
                'is_read' => 0
            ]);

            if ($notificationId) {
                $results['in_app'] = true;
                $results['notification_id'] = $notificationId;
            }
        }

        // Queue email notification
        if ($emailEnabled && $results['notification_id']) {
            $emailQueued = $this->queueEmail(
                $userId,
                $userType,
                $results['notification_id'],
                $type,
                $title,
                $message,
                $data,
                $priority
            );

            $results['email'] = $emailQueued;
        }

        return $results;
    }

    /**
     * Send notification to multiple users
     */
    public function sendToMultiple(
        array $users,
        string $type,
        string $title,
        string $message,
        ?array $data = null,
        ?string $actionUrl = null,
        string $priority = 'normal'
    ): array {
        $results = [];

        foreach ($users as $user) {
            $userId = $user['id'] ?? $user['user_id'];
            $userType = $user['type'] ?? $user['user_type'];

            $results[] = $this->send(
                $userId,
                $userType,
                $type,
                $title,
                $message,
                $data,
                $actionUrl,
                $priority
            );
        }

        return $results;
    }

    /**
     * Queue email for notification
     */
    private function queueEmail(
        int $userId,
        string $userType,
        int $notificationId,
        string $type,
        string $title,
        string $message,
        ?array $data,
        string $priority
    ): bool {
        // Get user email based on type
        $email = $this->getUserEmail($userId, $userType);
        if (!$email) {
            return false;
        }

        // Determine email template
        $template = $this->getEmailTemplate($type);

        // Queue the email
        $emailId = $this->emailQueue->create([
            'notification_id' => $notificationId,
            'recipient_email' => $email['email'],
            'recipient_name' => $email['name'],
            'subject' => $title,
            'body' => $message,
            'template' => $template,
            'template_data' => json_encode($data ?? []),
            'priority' => $priority,
            'status' => 'pending',
            'attempts' => 0,
            'max_attempts' => 3
        ]);

        return $emailId !== null;
    }

    /**
     * Get user email based on type
     */
    private function getUserEmail(int $userId, string $userType): ?array
    {
        // Use appropriate model based on user type
        // Note: 'admin' maps to super_admins table, 'customer' maps to store_customers
        switch ($userType) {
            case 'admin':
                return $this->adminModel->getEmailAndName($userId);
            case 'client':
                return $this->clientModel->getEmailAndName($userId);
            case 'customer':
                return $this->customerModel->getEmailAndName($userId);
            default:
                return null;
        }
    }

    /**
     * Get email template for notification type
     */
    private function getEmailTemplate(string $type): string
    {
        $templates = [
            'order' => 'order_notification',
            'product' => 'product_notification',
            'system' => 'system_notification',
            'store' => 'store_notification',
            'payment' => 'payment_notification',
            'customer' => 'customer_notification'
        ];

        return $templates[$type] ?? 'default_notification';
    }

    /**
     * Notification shortcuts for common scenarios
     */

    public function notifyOrderPlaced(int $clientId, int $orderId, array $orderData): array
    {
        return $this->send(
            $clientId,
            'client',
            'order',
            'New Order Received',
            "You have received a new order #{$orderId}",
            ['order_id' => $orderId, 'order_data' => $orderData],
            "/client/orders.php?id={$orderId}",
            'high'
        );
    }

    public function notifyLowStock(int $clientId, int $productId, string $productName, int $stock): array
    {
        return $this->send(
            $clientId,
            'client',
            'product',
            'Low Stock Alert',
            "{$productName} is running low. Only {$stock} items left.",
            ['product_id' => $productId, 'stock' => $stock],
            "/client/products.php?id={$productId}",
            'high'
        );
    }

    public function notifyPaymentReceived(int $clientId, int $orderId, float $amount): array
    {
        return $this->send(
            $clientId,
            'client',
            'payment',
            'Payment Received',
            "Payment of ₦" . number_format($amount, 2) . " received for order #{$orderId}",
            ['order_id' => $orderId, 'amount' => $amount],
            "/client/orders.php?id={$orderId}",
            'normal'
        );
    }

    public function notifyStorePublished(int $clientId, int $storeId, string $storeName): array
    {
        return $this->send(
            $clientId,
            'client',
            'store',
            'Store Published',
            "Your store '{$storeName}' is now live!",
            ['store_id' => $storeId],
            "/client/stores.php?id={$storeId}",
            'normal'
        );
    }

    public function notifyCustomerRegistered(int $clientId, int $customerId, string $customerName): array
    {
        return $this->send(
            $clientId,
            'client',
            'customer',
            'New Customer Registered',
            "{$customerName} has created an account in your store",
            ['customer_id' => $customerId],
            null,
            'normal'
        );
    }

    public function notifySystemUpdate(int $userId, string $userType, string $updateMessage): array
    {
        return $this->send(
            $userId,
            $userType,
            'system',
            'System Update',
            $updateMessage,
            null,
            null,
            'low'
        );
    }
}

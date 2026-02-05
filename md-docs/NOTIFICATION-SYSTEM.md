# Notification System Implementation Guide

## Overview

The e-commerce platform now includes a comprehensive dual-channel notification system that supports both in-app (database) and email notifications. This system notifies store owners about critical events such as new orders, order status changes, and low stock alerts.

## Features

- **Dual-Channel Delivery**: In-app notifications (database) + Email notifications (SMTP)
- **Real-time Bell UI**: Interactive notification bell with unread badge counter
- **Notification Center**: Full-page notification management interface
- **User Preferences**: Customizable notification settings per user
- **Priority Levels**: low, normal, high, urgent
- **Notification Types**: order, product, system, store, payment
- **Auto-triggers**: Automatic notifications for business events

## Architecture

### Backend Components

#### Models
- **Notification** (`backend/models/Notification.php`)
  - Handles database operations for notifications
  - Methods: `getByUser()`, `getUnreadCount()`, `markAsRead()`, `markAllAsRead()`, `deleteForUser()`

- **NotificationPreference** (`backend/models/NotificationPreference.php`)
  - Manages user notification preferences
  - Controls which notification types users receive

#### Services
- **NotificationService** (`backend/services/NotificationService.php`)
  - Core business logic for sending notifications
  - Handles multi-channel delivery (database + email)
  - Queues email notifications for asynchronous sending

- **EmailService** (`backend/services/EmailService.php`)
  - SMTP email sending via PHPMailer
  - Handles email queue processing
  - Configurable retry logic

#### Controllers
- **NotificationController** (`backend/controllers/NotificationController.php`)
  - API endpoints for notification operations
  - Protected by AuthMiddleware

#### Database
- **notifications** table: Stores in-app notifications
- **notification_preferences** table: Stores user preferences
- **email_queue** table: Manages email delivery queue

### Frontend Components

#### Services
- **notification.service.js** (`app/assets/js/services/notification.service.js`)
  - API client for notification operations
  - Methods: `getNotifications()`, `markAsRead()`, `markAllAsRead()`, `deleteNotification()`, `getPreferences()`, `updatePreferences()`

#### UI Components
- **notification-bell.js** (`app/assets/js/notification-bell.js`)
  - Interactive bell icon with badge
  - Dropdown preview of recent notifications
  - Auto-polling for new notifications (default: 30 seconds)

#### Pages
- **app/client/notifications.php**: Client notification center
- **app/admin/notifications.php**: Admin notification center

## API Endpoints

All endpoints are protected with `AuthMiddleware` and require Bearer token authentication.

### Get Notifications
```http
GET /api/notifications
Authorization: Bearer <token>
```

### Get Unread Count
```http
GET /api/notifications/unread-count
Authorization: Bearer <token>
```

### Get Single Notification
```http
GET /api/notifications/{id}
Authorization: Bearer <token>
```

### Mark as Read
```http
PUT /api/notifications/{id}/read
Authorization: Bearer <token>
```

### Mark All as Read
```http
PUT /api/notifications/mark-all-read
Authorization: Bearer <token>
```

### Delete Notification
```http
DELETE /api/notifications/{id}
Authorization: Bearer <token>
```

### Delete All Notifications
```http
DELETE /api/notifications
Authorization: Bearer <token>
```

### Get Preferences
```http
GET /api/notification-preferences
Authorization: Bearer <token>
```

### Update Preferences
```http
PUT /api/notification-preferences
Authorization: Bearer <token>
Content-Type: application/json

{
  "email_enabled": true,
  "notify_orders": true,
  "notify_products": true,
  "notify_system": true,
  "notify_store": true,
  "notify_payment": true
}
```

## Configuration

### Environment Variables

Add these variables to your `.env` file:

```env
# Email Configuration (SMTP)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_ENCRYPTION=tls
SMTP_FROM_EMAIL=noreply@ecommerce-platform.com
SMTP_FROM_NAME=E-commerce Platform

# Notifications
NOTIFICATIONS_ENABLED=true
EMAIL_NOTIFICATIONS_ENABLED=true
NOTIFICATION_QUEUE_ENABLED=false
```

### Gmail SMTP Setup

For Gmail, you need to generate an **App Password**:

1. Go to your Google Account settings
2. Navigate to Security → 2-Step Verification
3. Scroll to "App passwords"
4. Generate a new app password for "Mail"
5. Use this password as `SMTP_PASSWORD`

**Note**: Regular Gmail passwords won't work due to security restrictions.

### Database Migration

Run the SQL migration to create required tables:

```bash
mysql -u root -p ecommerce_platform < backend/database/add_notifications_system.sql
```

Or execute the SQL manually from your database management tool.

## Integration Points

### Automatic Notifications

The system automatically sends notifications for the following events:

#### Order Events
- **New Order Created**: Sent when a customer places an order
  - Type: `order`
  - Priority: `normal`
  - Channels: `database`, `email`

- **Order Status Updated**: Sent when order status changes
  - Type: `order`
  - Priority: `high` (for delivered/cancelled), `normal` (others)
  - Channels: `database`, `email`

#### Product Events
- **Low Stock Alert**: Sent when product stock falls below threshold (default: 10 units)
  - Type: `product`
  - Priority: `high`
  - Channels: `database`, `email`

- **Out of Stock**: Sent when product stock reaches zero
  - Type: `product`
  - Priority: `urgent`
  - Channels: `database`, `email`

### Manual Notification Sending

You can programmatically send notifications from any controller:

```php
use App\Services\NotificationService;

$notificationService = new NotificationService();

$notificationService->send(
    $storeId,              // Store ID
    'Notification Title',  // Title
    'Notification message', // Message
    'order',               // Type: order, product, system, store, payment
    ['database', 'email'], // Channels
    'normal',              // Priority: low, normal, high, urgent
    '/client/orders.php'   // Optional action URL
);
```

## User Interface

### Notification Bell

The notification bell appears in the header of both client and admin interfaces:

- **Badge**: Shows unread notification count
- **Dropdown**: Displays recent 5 notifications
- **Auto-refresh**: Polls for new notifications every 30 seconds
- **Click action**: Clicking a notification marks it as read and navigates to action URL

### Notification Center

Full-page interface for managing notifications:

- **Filter by Type**: Filter notifications by category (order, product, system, store, payment)
- **Mark All Read**: Mark all notifications as read with one click
- **Delete**: Remove individual or all notifications
- **Preferences**: Configure notification settings

### Notification Preferences

Users can customize:
- Enable/disable email notifications
- Choose which notification types to receive
- Settings stored per user in the database

## Testing

### Test Notification Creation

You can test the system by:

1. **Creating a Test Order**:
   ```http
   POST /api/orders
   Authorization: Bearer <token>
   Content-Type: application/json
   
   {
     "store_id": 1,
     "customer_name": "Test Customer",
     "customer_email": "test@example.com",
     "total_amount": 99.99,
     "items": []
   }
   ```

2. **Updating Product Stock**:
   ```http
   PUT /api/products/{id}
   Authorization: Bearer <token>
   Content-Type: application/json
   
   {
     "stock_quantity": 5
   }
   ```

3. **Checking Notifications**:
   - Visit `/client/notifications.php`
   - Click the notification bell in the header
   - Check your email inbox

## Troubleshooting

### Notifications Not Appearing

1. **Check Database**: Verify notifications table has records
   ```sql
   SELECT * FROM notifications WHERE user_id = YOUR_USER_ID ORDER BY created_at DESC;
   ```

2. **Check Preferences**: Ensure notifications are enabled
   ```sql
   SELECT * FROM notification_preferences WHERE user_id = YOUR_USER_ID;
   ```

3. **Browser Console**: Check for JavaScript errors in the browser console

### Email Not Sending

1. **Check SMTP Credentials**: Verify `.env` file has correct SMTP settings
2. **Check Email Queue**: Verify email_queue table
   ```sql
   SELECT * FROM email_queue WHERE status = 'failed' ORDER BY created_at DESC;
   ```
3. **Test SMTP Connection**: Use a standalone PHP script to test SMTP
4. **Check Error Logs**: Review PHP error logs for email service errors

### Frontend Issues

1. **Scripts Not Loading**: Verify script paths in header files
2. **API Errors**: Check browser Network tab for failed API calls
3. **Authentication**: Ensure JWT token is valid and not expired

## Performance Considerations

### Email Queue

The system uses an email queue to prevent blocking API responses:

1. Notifications are saved to database immediately
2. Emails are queued for asynchronous sending
3. Email queue can be processed by a cron job or background worker

### Polling Interval

The notification bell polls every 30 seconds by default. Adjust in header files:

```javascript
new NotificationBell('notificationBell', {
    pollInterval: 60000, // 60 seconds
    maxVisible: 5
});
```

### Database Indexes

Ensure proper indexes exist for performance:
```sql
CREATE INDEX idx_notifications_user_id ON notifications(user_id);
CREATE INDEX idx_notifications_read_at ON notifications(read_at);
CREATE INDEX idx_notification_preferences_user_id ON notification_preferences(user_id);
```

## Security

### Authorization

- All notification endpoints require authentication
- Users can only access their own notifications
- AuthMiddleware validates JWT tokens

### XSS Protection

- Notification titles and messages are HTML-escaped in the UI
- Use `textContent` instead of `innerHTML` when displaying user input

### Rate Limiting

Consider implementing rate limiting for notification creation to prevent abuse:

```php
// Example rate limit check (implement as needed)
if ($userNotificationsToday > 100) {
    throw new Exception('Notification rate limit exceeded');
}
```

## Future Enhancements

Potential improvements for the notification system:

1. **Push Notifications**: Browser push notifications via Service Workers
2. **SMS Notifications**: Integration with Twilio or similar services
3. **Notification Templates**: Customizable email templates
4. **Batch Notifications**: Digest emails for multiple notifications
5. **Read Receipts**: Track when users read notifications
6. **Notification Categories**: More granular notification types
7. **Scheduled Notifications**: Send notifications at specific times
8. **Multi-language Support**: Translate notifications based on user preference

## Support

For issues or questions about the notification system:

1. Check this documentation
2. Review error logs: PHP error log and browser console
3. Verify configuration in `.env` and `backend/config/config.php`
4. Test with simple notification creation

---

**Last Updated**: January 2025  
**Version**: 1.0.0

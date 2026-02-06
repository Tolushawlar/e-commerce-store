# Password Reset Implementation Guide

## Overview
Complete forgot/reset password feature for client accounts with email notifications and secure token-based authentication.

## Features Implemented

### Backend
1. **Database Table**: `password_resets`
   - Stores hashed tokens with 1-hour expiration
   - Tracks user_id, user_type, token, expires_at

2. **PasswordReset Model** (`backend/models/PasswordReset.php`)
   - `createToken()` - Generate secure reset tokens
   - `verifyToken()` - Validate token and expiration
   - `deleteToken()` - One-time use cleanup
   - `getRecentTokenCount()` - Rate limiting (max 3 per 15 min)

3. **AuthController Methods**:
   - `forgotPassword()` - Request reset link
   - `verifyResetToken($token)` - Check token validity
   - `resetPassword()` - Update password with token

4. **API Routes**:
   - `POST /api/auth/forgot-password` - Request reset
   - `GET /api/auth/verify-reset-token/{token}` - Verify token
   - `POST /api/auth/reset-password` - Reset password

### Frontend
1. **Forgot Password Page** (`app/auth/forgot-password.php`)
   - Email input form
   - Success message with reset link (for testing)
   - Matches login page styling

2. **Reset Password Page** (`app/auth/reset-password.php`)
   - Token verification on load
   - Password strength indicator (4 levels)
   - Password visibility toggle
   - Match validation
   - Auto-redirect to login after success

3. **Login Page Update**
   - "Forgot Password?" link (client tab only)
   - Shows/hides based on selected tab

## Security Features

### Token Security
- Tokens are hashed using `password_hash()` before storage
- 64-character random tokens (32 bytes)
- 1-hour expiration
- One-time use (deleted after successful reset)

### Rate Limiting
- Max 3 reset requests per 15 minutes per user
- Returns 429 Too Many Requests

### Password Requirements
- Minimum 8 characters
- Strength validation (weak/fair/good/strong)
- Match confirmation required

### Privacy
- Generic success messages (doesn't reveal if email exists)
- Token verification before showing reset form

## Email Notifications

### Integration
Uses existing `NotificationService` which:
- Sends in-app notifications
- Queues emails via `EmailQueue` model
- Includes reset link in email body

### Notification Types
1. **Password Reset Request**
   - Priority: High
   - Contains reset link and expiration time
   - Security warning if not requested

2. **Password Reset Success**
   - Priority: High
   - Confirms password change
   - Advises contact support if unauthorized

## Installation Steps

### 1. Run Database Migration
```bash
# Navigate to project root
cd "c:\Users\Dell\OneDrive\Documents\LivePetal Projects\e-commerce-store"

# Run SQL file (adjust path to your MySQL)
mysql -u your_username -p your_database < backend/database/create_password_resets_table.sql
```

Or manually execute the SQL:
```sql
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_type ENUM('client', 'admin') NOT NULL DEFAULT 'client',
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_user (user_id, user_type),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 2. Test the Feature

#### A. Request Password Reset
1. Go to `/auth/login.php`
2. Click "Client" tab
3. Click "Forgot Password?" link
4. Enter your email address
5. Click "Send Reset Link"
6. Check success message for reset link (displayed for testing)

#### B. Reset Password
1. Click the reset link from step 5
2. Wait for token verification
3. Enter new password (min. 8 characters)
4. Confirm password
5. Click "Reset Password"
6. Redirected to login page after 3 seconds

#### C. Login with New Password
1. Use new password to login
2. Verify successful authentication

### 3. Production Configuration

For production with actual email sending:

1. **Remove Reset Link from Response** (optional):
   - In `AuthController::forgotPassword()`, comment out reset_link return
   - Only send via email notification

2. **Configure SMTP**:
   - Ensure email queue processor is running
   - Configure SMTP settings for email delivery

3. **Cleanup Expired Tokens** (optional cron job):
```php
// Run periodically
$passwordReset = new PasswordReset();
$deleted = $passwordReset->cleanExpiredTokens();
```

## Usage Flow

### User Flow
1. User forgets password → clicks "Forgot Password?" on login
2. Enters email → receives reset link (via email/notification)
3. Clicks link → taken to reset password page
4. Page verifies token automatically
5. User enters new password → strength indicator shows
6. Submits form → password updated
7. Auto-redirected to login → uses new password

### Technical Flow
```
Forgot Password Request
  ↓
Check if email exists
  ↓
Generate random token (64 chars)
  ↓
Hash token + store in DB with expiry
  ↓
Send notification with reset link
  ↓
User clicks link
  ↓
Frontend verifies token via API
  ↓
Show reset form with user's email
  ↓
User submits new password
  ↓
Backend verifies token again
  ↓
Update user password
  ↓
Delete used token
  ↓
Send confirmation notification
```

## API Examples

### Forgot Password
```javascript
POST /api/auth/forgot-password
{
  "email": "client@example.com"
}

Response:
{
  "success": true,
  "message": "If an account exists with this email, a password reset link has been sent",
  "data": {
    "reset_link": "/auth/reset-password.php?token=abc123...",
    "expires_in": "1 hour"
  }
}
```

### Verify Token
```javascript
GET /api/auth/verify-reset-token/abc123...

Response:
{
  "success": true,
  "message": "Token is valid",
  "data": {
    "valid": true,
    "email": "client@example.com"
  }
}
```

### Reset Password
```javascript
POST /api/auth/reset-password
{
  "token": "abc123...",
  "password": "newpassword123",
  "confirm_password": "newpassword123"
}

Response:
{
  "success": true,
  "message": "Password reset successfully"
}
```

## Files Modified/Created

### Created Files
- `backend/database/create_password_resets_table.sql`
- `backend/models/PasswordReset.php`
- `app/auth/forgot-password.php`
- `app/auth/reset-password.php`

### Modified Files
- `backend/controllers/AuthController.php` (added 3 methods + use statements)
- `api/index.php` (added 3 routes)
- `app/auth/login.php` (added forgot password link)

## Troubleshooting

### Token Always Invalid
- Check database table exists
- Verify token is being passed correctly in URL
- Check token hasn't expired (1 hour limit)

### Email Not Sent
- Verify NotificationService is configured
- Check EmailQueue table for pending emails
- Ensure email preferences are enabled for user

### Rate Limiting Issues
- Adjust timeout in `PasswordReset::getRecentTokenCount()`
- Current: 3 requests per 15 minutes

### Password Strength Not Showing
- Check browser console for JavaScript errors
- Verify Tailwind CSS is loading

## Next Steps

1. ✅ Run database migration
2. ✅ Test complete flow
3. Configure email SMTP settings (if not already done)
4. Set up cron job to clean expired tokens
5. Monitor email queue processing
6. Consider adding password strength requirements to backend validation

## Support

All functionality follows existing architecture:
- MVC pattern
- RESTful API
- JWT authentication
- Notification system integration
- Consistent UI/UX with login/register pages

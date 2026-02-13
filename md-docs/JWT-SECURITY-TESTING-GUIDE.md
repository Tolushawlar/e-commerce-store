# JWT Security Testing Guide

## Overview

This guide shows you how to test all the JWT security features to verify they're working correctly.

---

## Prerequisites

1. Run the SQL migration: `backend/database/add_token_security.sql`
2. Login to get a valid token
3. Have Postman or similar API testing tool ready
4. Have multiple browsers installed (Chrome, Firefox, etc.)

---

## Test 1: Token Blacklisting on Logout ✅

**What it tests:** Tokens are immediately revoked when user logs out

```bash
# Step 1: Login
POST http://localhost/api/auth/admin/login
Content-Type: application/json

{
  "email": "admin@platform.com",
  "password": "your_password"
}

# Copy the "token" from response

# Step 2: Use the token (should work)
GET http://localhost/api/stores
Authorization: Bearer YOUR_TOKEN_HERE

# Step 3: Logout (blacklists the token)
POST http://localhost/api/auth/logout
Authorization: Bearer YOUR_TOKEN_HERE

# Step 4: Try using the same token again (SHOULD FAIL)
GET http://localhost/api/stores
Authorization: Bearer YOUR_TOKEN_HERE

# Expected Response:
{
  "success": false,
  "message": "Token has been revoked"
}
```

**✅ Success Criteria:** After logout, token returns "Token has been revoked" error

---

## Test 2: All Tokens Revoked on Password Change ✅

**What it tests:** All existing sessions are terminated when password changes

```bash
# Step 1: Login from Browser 1 (Chrome)
POST http://localhost/api/auth/admin/login
# Save token as TOKEN_1

# Step 2: Login from Browser 2 (Firefox) 
POST http://localhost/api/auth/admin/login
# Save token as TOKEN_2

# Step 3: Both tokens should work
GET http://localhost/api/stores
Authorization: Bearer TOKEN_1  # Should work

GET http://localhost/api/stores
Authorization: Bearer TOKEN_2  # Should work

# Step 4: Change password using TOKEN_1
POST http://localhost/api/auth/change-password
Authorization: Bearer TOKEN_1
Content-Type: application/json

{
  "current_password": "old_password",
  "new_password": "new_password123",
  "confirm_password": "new_password123"
}

# Step 5: Try using both old tokens (BOTH SHOULD FAIL)
GET http://localhost/api/stores
Authorization: Bearer TOKEN_1  # Should fail

GET http://localhost/api/stores
Authorization: Bearer TOKEN_2  # Should fail

# Expected Response for both:
{
  "success": false,
  "message": "Token has been revoked"
}
```

**✅ Success Criteria:** After password change, ALL old tokens are invalid

---

## Test 3: Security Events Logging ✅

**What it tests:** System logs all security-related activities

```bash
# Step 1: Perform various actions
# - Login from different browsers
# - Change password
# - Logout

# Step 2: Check security events
GET http://localhost/api/security/events?limit=20
Authorization: Bearer YOUR_CURRENT_TOKEN

# Expected Response:
{
  "success": true,
  "data": [
    {
      "event_type": "new_device",
      "severity": "medium",
      "ip_address": "192.168.1.1",
      "details": {...},
      "created_at": "2026-02-12 10:30:00"
    },
    {
      "event_type": "ip_change",
      "severity": "medium",
      ...
    },
    {
      "event_type": "all_tokens_revoked",
      "severity": "high",
      "details": {
        "reason": "password_change"
      },
      ...
    }
  ]
}
```

**✅ Success Criteria:** Events are logged with proper severity levels

---

## Test 4: Device Management ✅

**What it tests:** Users can view and revoke trusted devices

```bash
# Step 1: View all trusted devices
GET http://localhost/api/security/devices
Authorization: Bearer YOUR_TOKEN

# Expected Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "fingerprint": "a1b2c3d4e5f6g7h8...",
      "ip_address": "192.168.1.1",
      "browser": "Chrome on Windows",
      "last_used_at": "2026-02-12 10:30:00",
      "is_current": true
    },
    {
      "id": 2,
      "fingerprint": "x9y8z7w6v5u4t3s2...",
      "ip_address": "192.168.1.1",
      "browser": "Firefox on Windows",
      "last_used_at": "2026-02-11 14:20:00",
      "is_current": false
    }
  ]
}

# Step 2: Revoke a specific device
DELETE http://localhost/api/security/devices/2
Authorization: Bearer YOUR_TOKEN

# Step 3: Verify device can't use tokens anymore
# (Login from that device and check the security events)
```

**✅ Success Criteria:** Revoked devices can't use their tokens

---

## Test 5: Cross-Application Detection (Enhanced Mode) ⚠️

**What it tests:** Detect when token is used from different application (Chrome vs Postman)

### Enable Strict Mode

Add to your `.env` file:

```env
SECURITY_STRICT_FINGERPRINT=true
```

OR update [backend/config/config.php](backend/config/config.php):

```php
'strict_device_fingerprint' => true,
```

### Test Steps

```bash
# Step 1: Login from Chrome browser
# Visit your frontend app and login
# Copy the token from browser DevTools

# Step 2: Try using that token in Postman (SHOULD BE DETECTED)
GET http://localhost/api/stores
Authorization: Bearer CHROME_TOKEN

# Expected Response (with strict mode enabled):
{
  "success": false,
  "message": "Device verification failed. Possible token theft detected.",
  "reason": "user_agent_mismatch",
  "requires_reauth": true
}

# Step 3: Check security events
GET http://localhost/api/security/events
# Should show "user_agent_mismatch" event with high severity
```

**✅ Success Criteria:** Using token from different application is detected and logged (and blocked if strict mode enabled)

---

## Test 6: Revoke All Devices ✅

**What it tests:** User can logout from all devices at once

```bash
# Step 1: Login from multiple browsers/devices
# Create at least 3 active sessions

# Step 2: From one device, revoke all others
POST http://localhost/api/security/devices/revoke-all
Authorization: Bearer YOUR_CURRENT_TOKEN

# Step 3: Try using tokens from other devices (SHOULD FAIL)
# Only the current token that made the revoke-all request should work
```

**✅ Success Criteria:** All other sessions are terminated except current one

---

## Test 7: IP Change Detection ℹ️

**What it tests:** System detects when same device uses different IP

```bash
# Step 1: Login from home network
POST http://localhost/api/auth/admin/login
# Save token

# Step 2: Use token (works)
GET http://localhost/api/stores
Authorization: Bearer YOUR_TOKEN

# Step 3: Switch to different network (e.g., mobile hotspot, VPN, or office network)
# Use same token
GET http://localhost/api/stores
Authorization: Bearer YOUR_TOKEN

# Step 4: Check security events
GET http://localhost/api/security/events
# Should show "ip_change" event

# Expected Event:
{
  "event_type": "ip_change",
  "severity": "medium",
  "details": {
    "old_ip": "192.168.1.1",
    "new_ip": "10.0.0.1"
  }
}
```

**ℹ️ Note:** Token still works, but the IP change is logged for monitoring

---

## Test 8: Different Browser Detection ✅

**What it tests:** Each browser gets its own device fingerprint

```bash
# Step 1: Login from Chrome
POST http://localhost/api/auth/admin/login
# Save as CHROME_TOKEN

# Step 2: Login from Firefox
POST http://localhost/api/auth/admin/login
# Save as FIREFOX_TOKEN

# Step 3: View devices (should see both)
GET http://localhost/api/security/devices
Authorization: Bearer CHROME_TOKEN

# Expected: 2 different devices listed
{
  "data": [
    {
      "id": 1,
      "browser": "Chrome on Windows",
      "is_current": true
    },
    {
      "id": 2,
      "browser": "Firefox on Windows",
      "is_current": false
    }
  ]
}

# Step 4: Check security events
GET http://localhost/api/security/events
# Should show "new_device" events for each browser
```

**✅ Success Criteria:** Each browser is tracked as a separate device

---

## Test 9: Refresh Token Security ✅

**What it tests:** Refresh tokens work correctly with security system

```bash
# Step 1: Login and get refresh token
POST http://localhost/api/auth/admin/login
# Save both access_token and refresh_token

# Step 2: Wait for access token to expire (or use an old one)

# Step 3: Use refresh token to get new access token
POST http://localhost/api/auth/refresh
Content-Type: application/json

{
  "refresh_token": "YOUR_REFRESH_TOKEN"
}

# Step 4: Use new access token
GET http://localhost/api/stores
Authorization: Bearer NEW_ACCESS_TOKEN

# Step 5: Logout and try to use refresh token (SHOULD FAIL)
POST http://localhost/api/auth/logout
Authorization: Bearer NEW_ACCESS_TOKEN

POST http://localhost/api/auth/refresh
Content-Type: application/json
{
  "refresh_token": "YOUR_REFRESH_TOKEN"
}

# Expected: Token has been revoked
```

**✅ Success Criteria:** Refresh tokens are also blacklisted on logout/password change

---

## Quick Test Checklist

- [ ] **Logout revokes token** - Test 1
- [ ] **Password change revokes all tokens** - Test 2
- [ ] **Security events are logged** - Test 3
- [ ] **Can view trusted devices** - Test 4
- [ ] **Can revoke specific device** - Test 4
- [ ] **Can revoke all devices** - Test 6
- [ ] **IP changes are detected** - Test 7
- [ ] **Different browsers tracked separately** - Test 8
- [ ] **Cross-application usage detected** (with strict mode) - Test 5

---

## Troubleshooting

### "Why can I still use token in Postman?"

**Without strict mode:** The system uses device fingerprinting based on headers. Chrome and Postman may have similar enough headers to be considered the same device.

**Solution:**

1. Enable strict mode: `SECURITY_STRICT_FINGERPRINT=true`
2. Check security events - it logs "user_agent_mismatch" even if not blocking
3. Test the features that DO work: logout, password change, device revocation

### "Token still works after logout"

**Check:**

1. Are you using the exact same token you logged out with?
2. Did you run the SQL migration to create security tables?
3. Check database - is the token in `token_blacklist` table?
4. Check for any PHP errors in logs

### "Security events not showing"

**Check:**

1. Database tables exist: `token_blacklist`, `token_devices`, `security_events`
2. No database connection errors
3. Perform actions that generate events (login, logout, etc.)

---

## Configuration Options

Add to your `.env` file:

```env
# JWT Secret (REQUIRED)
JWT_SECRET=your-super-secret-key-min-32-characters

# Enable strict device fingerprinting (blocks cross-application usage)
SECURITY_STRICT_FINGERPRINT=true

# Require additional verification for new devices
SECURITY_REQUIRE_DEVICE_VERIFICATION=false
```

---

## What the Security System Protects Against

✅ **Token theft and reuse** - Tokens can be instantly revoked
✅ **Compromised passwords** - All sessions terminated on password change
✅ **Unauthorized devices** - Device fingerprinting and tracking
✅ **Suspicious activity** - Comprehensive logging and monitoring
✅ **Cross-application abuse** - Detects and optionally blocks (strict mode)
✅ **IP-based attacks** - IP change detection and logging

---

## Next Steps

1. Run all 9 tests above
2. Enable strict mode if needed
3. Set up the cleanup cron job: `backend/helpers/cleanup_tokens.php`
4. Monitor security events regularly
5. Implement frontend notifications for security events

---

**Remember:** The strongest protection is the combination of:

- ✅ Token blacklisting (logout/password change)
- ✅ Device fingerprinting
- ✅ Security event monitoring
- ✅ User awareness (device management UI)

# Paystack Payment Integration - Testing Guide

## Overview
This guide walks through testing the Paystack payment integration that has been implemented in the e-commerce platform.

## What's Been Implemented

### Backend Components

1. **Database Changes** (`add_paystack_integration.sql`)
   - Added to `stores` table:
     - `paystack_public_key` - Public API key for frontend
     - `paystack_secret_key` - Secret API key for backend
     - `payment_enabled` - Toggle to enable/disable payments
   
   - Added to `orders` table:
     - `payment_reference` - Unique Paystack transaction reference
     - `payment_gateway` - Payment method used (default: 'paystack')
     - `payment_verified_at` - Timestamp when payment was verified

2. **PaystackService** (`backend/services/PaystackService.php`)
   - Complete Paystack API wrapper
   - Methods:
     - `setKeys($store)` - Load store-specific credentials
     - `initializePayment($data)` - Create transaction
     - `verifyPayment($reference)` - Confirm payment status
     - `verifyWebhookSignature($payload, $signature)` - Validate webhooks
   - Multi-tenant: Each store uses its own Paystack keys

3. **PaymentController** (`backend/controllers/PaymentController.php`)
   - API endpoints for payment operations:
     - `GET /api/stores/{store_id}/payment/config` - Get public key
     - `POST /api/payment/initialize` - Initialize payment (requires auth)
     - `POST /api/payment/verify` - Verify payment (requires auth)
     - `POST /api/payment/webhook/paystack` - Webhook handler (public)

4. **Model Updates**
   - **Order Model**: Added `findByPaymentReference()` method
   - **Store Model**: Added payment fields to fillable array

### Frontend Components

1. **checkout.js Enhancements**
   - `getPaystackConfig()` - Fetch store's Paystack public key
   - `initializePayment()` - Create transaction on backend
   - `verifyPayment()` - Confirm payment completed
   - `processPayment()` - Handle complete payment flow with popup

2. **checkout.html Updates**
   - Paystack Popup library loaded via CDN
   - Modified order placement to trigger payment popup
   - Success/failure callbacks integrated

3. **Store Settings Page** (`app/client/store-settings.php`)
   - Paystack configuration section added
   - Fields for public key, secret key, and enable toggle
   - Instructions for obtaining API keys

## Testing Steps

### 1. Setup Paystack Test Account

1. Go to [Paystack Dashboard](https://dashboard.paystack.com)
2. Create account or sign in
3. Navigate to Settings > API Keys & Webhooks
4. Copy your **Test Public Key** (starts with `pk_test_`)
5. Copy your **Test Secret Key** (starts with `sk_test_`)

### 2. Configure Store with Paystack Keys

1. Login to your client account
2. Navigate to **Store Settings**
3. Select your store from dropdown
4. Scroll to **Paystack Payment Integration** section
5. Paste your Test Public Key
6. Paste your Test Secret Key
7. Enable **Enable Online Payments** toggle
8. Click **Save Settings**

### 3. Test Payment Flow

#### As a Customer:

1. **Browse Products**
   - Go to your generated store (e.g., `api/stores/store-1/index.html`)
   - Add products to cart

2. **Login/Register**
   - Click "Login" or "Register"
   - Create customer account or login

3. **Proceed to Checkout**
   - Click cart icon
   - Click "Proceed to Checkout"
   - Form should auto-fill if you have profile data

4. **Complete Checkout Form**
   - Fill in contact information
   - Fill in shipping address
   - Click "Place Order"

5. **Payment Popup**
   - Paystack popup should appear
   - Use Paystack test cards:
     - **Success**: `4084084084084081` (Mastercard)
     - **Success**: `5060666666666666666` (Verve)
     - **Decline**: `5060000000000000002` (Verve - decline)
   - CVV: Any 3 digits
   - Expiry: Any future date
   - PIN: `1234`
   - OTP: `123456`

6. **Payment Verification**
   - After successful payment, you'll be redirected to order success page
   - Order status should be "processing"
   - Payment should be marked as verified

### 4. Verify Database Changes

Run SQL queries to check data:

```sql
-- Check store configuration
SELECT id, store_name, payment_enabled, paystack_public_key 
FROM stores 
WHERE id = YOUR_STORE_ID;

-- Check order payment details
SELECT id, customer_id, total_amount, payment_status, 
       payment_reference, payment_gateway, payment_verified_at
FROM orders 
WHERE id = YOUR_ORDER_ID;
```

### 5. Test Webhook (Optional)

1. **Setup Ngrok** (to expose localhost)
   ```bash
   ngrok http 80
   ```

2. **Configure Webhook in Paystack**
   - Go to Paystack Dashboard > Settings > API Keys & Webhooks
   - Add webhook URL: `https://your-ngrok-url.ngrok.io/api/payment/webhook/paystack`

3. **Test Webhook Events**
   - Make a test payment
   - Paystack will send webhook to your endpoint
   - Check logs for webhook processing

## Payment Flow Diagram

```
Customer              Frontend              Backend              Paystack
   |                     |                      |                    |
   |---> Place Order --->|                      |                    |
   |                     |                      |                    |
   |                     |--- Create Order ---->|                    |
   |                     |<--- Order Created ---|                    |
   |                     |                      |                    |
   |                     |-- Initialize Pay --->|                    |
   |                     |                      |--- Initialize ---->|
   |                     |                      |<--- Auth URL ------|
   |                     |<--- Reference -------|                    |
   |                     |                      |                    |
   |<-- Paystack Popup --|                      |                    |
   |                     |                      |                    |
   |--- Enter Card ----->|                      |                    |
   |                     |------ Pay -----------|------------------>|
   |                     |                      |                    |
   |                     |                      |                    |
   |                     |<---- Success --------|<---- Success ------|
   |                     |                      |                    |
   |                     |---- Verify Ref ----->|                    |
   |                     |                      |--- Verify Ref ---->|
   |                     |                      |<--- Verified ------|
   |                     |                      |                    |
   |                     |<-- Order Updated ----|                    |
   |<-- Success Page ----|                      |                    |
   |                     |                      |                    |
   |                     |                      |<--- Webhook -------|
   |                     |                      |----- 200 OK ------>|
```

## Common Issues & Solutions

### Issue: Paystack popup doesn't appear

**Causes:**
- Paystack library not loaded
- Store payment not enabled
- Invalid public key

**Solutions:**
1. Check browser console for errors
2. Verify Paystack script tag in checkout.html: `<script src="https://js.paystack.co/v1/inline.js"></script>`
3. Ensure payment is enabled in store settings
4. Verify public key is correct (starts with `pk_test_` or `pk_live_`)

### Issue: Payment verification fails

**Causes:**
- Network timeout
- Invalid secret key
- Payment reference mismatch

**Solutions:**
1. Check backend logs for errors
2. Verify secret key is correct in database
3. Ensure payment reference is saved to order
4. Check Paystack dashboard for transaction status

### Issue: "Payment gateway not configured"

**Cause:** Store doesn't have Paystack keys configured

**Solution:**
1. Go to Store Settings
2. Add Paystack public and secret keys
3. Enable payment toggle
4. Save settings

### Issue: Webhook not receiving events

**Causes:**
- Incorrect webhook URL
- Server not accessible from internet
- Invalid signature verification

**Solutions:**
1. Use ngrok for local testing
2. Verify webhook URL in Paystack dashboard
3. Check signature verification logic
4. Review Paystack webhook logs

## Test Cards

### Successful Transactions
- **Mastercard**: `4084084084084081`
- **Verve**: `5060666666666666666`
- **Visa**: `4084080000000409`

### Failed Transactions
- **Insufficient Funds**: `5060000000000000002`
- **Do Not Honor**: `5060000000000000003`

### Test Details
- **CVV**: Any 3 digits (e.g., `123`)
- **Expiry**: Any future date (e.g., `12/25`)
- **PIN**: `1234`
- **OTP**: `123456`

## API Endpoints Reference

### Get Payment Config
```http
GET /api/stores/{store_id}/payment/config
```

**Response:**
```json
{
  "success": true,
  "data": {
    "public_key": "pk_test_xxxxxx",
    "enabled": true
  }
}
```

### Initialize Payment
```http
POST /api/payment/initialize
Authorization: Bearer {customer_token}
Content-Type: application/json

{
  "order_id": 123,
  "amount": 15000,
  "email": "customer@example.com"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "authorization_url": "https://checkout.paystack.com/xxxxxx",
    "reference": "PSK_xxxxxx"
  }
}
```

### Verify Payment
```http
POST /api/payment/verify
Authorization: Bearer {customer_token}
Content-Type: application/json

{
  "reference": "PSK_xxxxxx"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "order": {
      "id": 123,
      "payment_status": "paid",
      "status": "processing"
    },
    "payment": {
      "amount": 15000,
      "status": "success",
      "paid_at": "2024-01-15T10:30:00Z"
    }
  }
}
```

## Security Considerations

1. **Secret Keys**: Never expose secret keys on frontend
2. **Public Keys**: Safe to expose in JavaScript
3. **Webhook Signature**: Always verify webhook signatures
4. **HTTPS**: Use HTTPS in production for security
5. **Key Rotation**: Regularly rotate API keys
6. **Environment**: Use test keys for development, live keys for production

## Going Live Checklist

- [ ] Test all payment flows with test keys
- [ ] Obtain live API keys from Paystack
- [ ] Update store settings with live keys
- [ ] Configure webhook URL (must be HTTPS)
- [ ] Test with small live transaction
- [ ] Monitor Paystack dashboard for transactions
- [ ] Set up email notifications for payments
- [ ] Document customer support process for payment issues

## Support Resources

- **Paystack Documentation**: https://paystack.com/docs
- **Paystack Dashboard**: https://dashboard.paystack.com
- **Paystack Support**: support@paystack.com
- **Test Cards**: https://paystack.com/docs/payments/test-payments

## Next Steps

After successful testing:

1. **Email Notifications**: Implement payment confirmation emails
2. **Receipt Generation**: Create PDF receipts for paid orders
3. **Refund Handling**: Implement refund functionality
4. **Subscription Plans**: Add recurring payment support
5. **Multiple Payment Gateways**: Integrate additional payment providers

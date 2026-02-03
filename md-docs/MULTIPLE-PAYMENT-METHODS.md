# Multiple Payment Methods - Implementation Guide

## Overview
Extended the payment system to support three payment methods:
1. **Card Payment (Paystack)** - Online payment with Paystack gateway
2. **Bank Transfer** - Manual bank transfer with bank details display
3. **Cash on Delivery (COD)** - Pay when order is delivered

## Implementation Summary

### Database Changes
**Migration**: `backend/database/add_payment_methods.sql`

Added to `stores` table:
- `bank_transfer_enabled` (BOOLEAN) - Enable/disable bank transfer
- `bank_name` (VARCHAR) - Bank name for transfers
- `account_number` (VARCHAR) - Bank account number
- `account_name` (VARCHAR) - Account holder name
- `cod_enabled` (BOOLEAN) - Enable/disable COD (default: TRUE)

### Backend Updates

**Store Model** (`backend/models/Store.php`):
- Added new payment fields to fillable array

**Payment Controller** (`backend/controllers/PaymentController.php`):
- Updated `getConfig()` to return all payment method configurations:
```json
{
  "paystack": {
    "enabled": true,
    "public_key": "pk_test_xxx"
  },
  "bank_transfer": {
    "enabled": true,
    "bank_name": "First Bank",
    "account_number": "1234567890",
    "account_name": "Store Name Ltd"
  },
  "cod": {
    "enabled": true
  }
}
```

### Frontend Updates

**Store Settings Page** (`app/client/store-settings.php`):
Added three payment configuration sections:

1. **Paystack Payment**
   - Enable toggle
   - Public key input
   - Secret key input
   - Test mode warning

2. **Bank Transfer Payment**
   - Enable toggle
   - Bank name input
   - Account number input
   - Account name input

3. **Cash on Delivery**
   - Enable toggle (default: checked)

**Checkout Page** (`store-templates/checkout.html`):
- Dynamic payment options loading
- Shows only enabled payment methods
- Bank transfer details display when selected
- Smart default selection (first enabled method)

### Payment Flow

#### Card Payment (Paystack)
1. Customer selects "Debit/Credit Card"
2. Places order → Order created with status "pending"
3. Paystack popup opens automatically
4. Customer completes payment
5. Payment verified → Order status updated to "paid"

#### Bank Transfer
1. Customer selects "Bank Transfer"
2. Bank details displayed:
   - Bank Name
   - Account Number
   - Account Name
3. Places order → Order created with status "pending"
4. Redirected to success page with bank details reminder
5. Store owner manually verifies payment and updates order

#### Cash on Delivery
1. Customer selects "Cash on Delivery"
2. Places order → Order created with status "pending"
3. Redirected to success page
4. Customer pays cash when order is delivered

## Configuration Guide

### For Store Owners

#### Enable Paystack Payment
1. Go to Store Settings
2. Enable "Online Payments" toggle
3. Get API keys from [Paystack Dashboard](https://dashboard.paystack.com/#/settings/developers)
4. Enter Public Key and Secret Key
5. Save settings

#### Enable Bank Transfer
1. Go to Store Settings
2. Enable "Bank Transfer" toggle
3. Enter your bank details:
   - Bank Name (e.g., "First Bank of Nigeria")
   - Account Number
   - Account Name (registered business name)
4. Save settings

#### Enable/Disable COD
1. Go to Store Settings
2. Toggle "Cash on Delivery" (enabled by default)
3. Save settings

### For Developers

#### Fetch Payment Configuration
```javascript
const response = await fetch(`/api/stores/${storeId}/payment/config`);
const config = await response.json();

if (config.data.paystack.enabled) {
  // Show card payment option
}

if (config.data.bank_transfer.enabled) {
  // Show bank transfer option
  const bankDetails = config.data.bank_transfer;
  console.log(bankDetails.bank_name);
}

if (config.data.cod.enabled) {
  // Show COD option
}
```

#### Handle Payment Method Selection
```javascript
document.querySelectorAll('input[name="payment"]').forEach(radio => {
  radio.addEventListener('change', (e) => {
    const method = e.target.value;
    
    if (method === 'transfer') {
      // Show bank transfer details
    } else if (method === 'card') {
      // Prepare Paystack
    } else if (method === 'cod') {
      // Show COD message
    }
  });
});
```

## Testing

### Test Bank Transfer
1. Configure store with bank transfer enabled
2. Add test bank details
3. Go to checkout
4. Verify bank transfer option appears
5. Select bank transfer
6. Verify bank details are displayed correctly
7. Complete order
8. Check order is created with payment_method = "transfer"

### Test COD
1. Ensure COD is enabled (default)
2. Go to checkout
3. Select "Cash on Delivery"
4. Complete order
5. Check order is created with payment_method = "cod"

### Test Mixed Configuration
1. Enable only specific payment methods
2. Verify checkout shows only enabled options
3. Test with different combinations:
   - Only Paystack
   - Only Bank Transfer
   - Only COD
   - All three enabled
   - All disabled (should show error message)

## UI/UX Features

### Dynamic Payment Options
- Only shows enabled payment methods
- Auto-selects first available method
- Graceful error handling if no methods configured

### Bank Transfer Details Display
- Shows instantly when bank transfer selected
- Formatted in easy-to-read card
- Includes helpful note about verification

### Payment Icons
- Credit card icon for Paystack
- Bank icon for Bank Transfer
- Delivery truck icon for COD

## Security Considerations

1. **Bank Details**: Publicly visible (safe to display)
2. **Paystack Keys**: Public key exposed, secret key server-side only
3. **Payment Verification**: 
   - Paystack: Automated via webhook
   - Bank Transfer: Manual verification by store owner
   - COD: Confirmed on delivery

## Future Enhancements

1. **Bank Transfer Proof Upload**: Allow customers to upload payment receipt
2. **Multiple Bank Accounts**: Support multiple bank accounts per store
3. **COD Fee**: Optional fee for COD orders
4. **Payment Reminders**: Email customers pending payment confirmation
5. **Partial Payments**: Support deposits and installments

## Troubleshooting

### Payment Options Not Showing
- Check store settings - ensure at least one method is enabled
- Verify migration ran successfully
- Check browser console for API errors

### Bank Details Not Displaying
- Ensure bank transfer is enabled
- Verify all bank fields are filled in settings
- Check payment config API response

### Order Created But No Payment Processing
- Expected behavior for bank transfer and COD
- These require manual verification/confirmation
- Only Paystack auto-processes payments

## Migration Commands

```bash
# Run migration
php backend/database/run_payment_methods_migration.php

# Rollback (if needed)
# Manually execute:
# ALTER TABLE stores DROP COLUMN bank_transfer_enabled, bank_name, account_number, account_name, cod_enabled;
```

## API Reference

### Get Payment Configuration
**Endpoint**: `GET /api/stores/{store_id}/payment/config`
**Access**: Public

**Response**:
```json
{
  "success": true,
  "data": {
    "paystack": {
      "enabled": true,
      "public_key": "pk_test_xxx"
    },
    "bank_transfer": {
      "enabled": true,
      "bank_name": "First Bank of Nigeria",
      "account_number": "1234567890",
      "account_name": "LivePetal Hub Limited"
    },
    "cod": {
      "enabled": true
    }
  }
}
```

## Files Modified

1. `backend/database/add_payment_methods.sql` - Migration file
2. `backend/database/run_payment_methods_migration.php` - Migration runner
3. `backend/models/Store.php` - Added payment fields
4. `backend/controllers/PaymentController.php` - Updated config endpoint
5. `app/client/store-settings.php` - Added payment settings UI
6. `store-templates/checkout.html` - Dynamic payment options

## Conclusion

The multi-payment system provides flexibility for stores to:
- Accept online payments via Paystack
- Provide bank transfer option for customers without cards
- Support traditional cash on delivery
- Mix and match based on business needs

All payment methods integrate seamlessly with the existing order management system and maintain consistent user experience.

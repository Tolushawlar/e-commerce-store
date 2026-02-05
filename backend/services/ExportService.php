<?php

namespace App\Services;

/**
 * ExportService
 * Handles data export to various formats (CSV, Excel, PDF)
 */
class ExportService
{
    /**
     * Export data to CSV format
     */
    public function exportToCSV(array $data, array $headers, string $filename): void
    {
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Write headers
        fputcsv($output, $headers);

        // Write data rows
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * Export orders to CSV
     */
    public function exportOrders(array $orders): void
    {
        $filename = 'orders_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Order ID',
            'Order Date',
            'Customer Name',
            'Customer Email',
            'Customer Phone',
            'Items Count',
            'Total Amount',
            'Order Status',
            'Payment Status',
            'Payment Method',
            'Shipping Address',
            'Created At'
        ];

        $data = [];
        foreach ($orders as $order) {
            // Parse shipping address
            $shippingAddress = '';
            if (!empty($order['shipping_address'])) {
                $addr = json_decode($order['shipping_address'], true);
                if ($addr) {
                    $shippingAddress = implode(', ', array_filter([
                        $addr['address'] ?? '',
                        $addr['city'] ?? '',
                        $addr['state'] ?? '',
                        $addr['postal_code'] ?? ''
                    ]));
                }
            }

            $data[] = [
                $order['id'],
                date('Y-m-d H:i:s', strtotime($order['created_at'])),
                $order['customer_name'] ?? 'N/A',
                $order['customer_email'] ?? 'N/A',
                $order['customer_phone'] ?? 'N/A',
                $order['items_count'] ?? count($order['items'] ?? []),
                number_format($order['total_amount'], 2),
                ucfirst($order['status']),
                ucfirst($order['payment_status']),
                $this->formatPaymentMethod($order['payment_method']),
                $shippingAddress,
                $order['created_at']
            ];
        }

        $this->exportToCSV($data, $headers, $filename);
    }

    /**
     * Export products to CSV
     */
    public function exportProducts(array $products): void
    {
        $filename = 'products_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Product ID',
            'SKU',
            'Name',
            'Description',
            'Price',
            'Compare Price',
            'Cost',
            'Stock Quantity',
            'Stock Status',
            'Category',
            'Status',
            'Created At',
            'Updated At'
        ];

        $data = [];
        foreach ($products as $product) {
            $data[] = [
                $product['id'],
                $product['sku'] ?? 'N/A',
                $product['name'],
                strip_tags($product['description'] ?? ''),
                number_format($product['price'], 2),
                number_format($product['compare_price'] ?? 0, 2),
                number_format($product['cost'] ?? 0, 2),
                $product['stock_quantity'] ?? 0,
                $product['stock_status'] ?? 'in_stock',
                $product['category_name'] ?? 'Uncategorized',
                ucfirst($product['status']),
                $product['created_at'],
                $product['updated_at']
            ];
        }

        $this->exportToCSV($data, $headers, $filename);
    }

    /**
     * Export customers to CSV
     */
    public function exportCustomers(array $customers): void
    {
        $filename = 'customers_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Customer ID',
            'Name',
            'Email',
            'Total Orders',
            'Total Spent',
            'Average Order Value'
        ];

        $data = [];
        foreach ($customers as $customer) {
            $data[] = [
                $customer['customer_id'] ?? 'N/A',
                $customer['customer_name'] ?? 'N/A',
                $customer['customer_email'] ?? 'N/A',
                $customer['order_count'] ?? 0,
                '₦' . number_format($customer['total_spent'] ?? 0, 2),
                '₦' . number_format($customer['avg_order_value'] ?? 0, 2)
            ];
        }

        $this->exportToCSV($data, $headers, $filename);
    }

    /**
     * Format payment method for display
     */
    private function formatPaymentMethod(?string $method): string
    {
        if (!$method) return 'N/A';
        
        $methods = [
            'cash_on_delivery' => 'Cash on Delivery',
            'bank_transfer' => 'Bank Transfer',
            'card' => 'Card Payment',
            'wallet' => 'Wallet'
        ];
        
        return $methods[$method] ?? ucwords(str_replace('_', ' ', $method));
    }
}

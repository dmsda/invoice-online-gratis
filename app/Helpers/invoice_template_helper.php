<?php

if (!function_exists('invoice_template_view')) {
    /**
     * Resolves the correct PDF/View template path based on the invoice type.
     * Enforces 'produk' as the strict fallback for invalid or missing types.
     * 
     * @param string|null $type 'jasa' or 'produk'
     * @return string The view path string
     */
    function invoice_template_view(?string $type): string
    {
        $type = strtolower(trim((string)$type));

        return match ($type) {
            'jasa'   => 'pdf/invoice-jasa',
            'produk' => 'pdf/invoice-produk',
            default  => 'pdf/invoice-produk'
        };
    }
}

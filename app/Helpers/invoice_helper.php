<?php

if (! function_exists('status_badge_class')) {
    /**
     * Get the Bootstrap badge class based on invoice status.
     *
     * @param string $status
     * @return string
     */
    function status_badge_class(string $status, ?string $dueDate = null): string
    {
        if ($status === 'sent' && $dueDate && $dueDate < date('Y-m-d')) {
            return 'badge text-bg-danger';
        }
        
        return match($status) {
            'draft'     => 'badge text-bg-warning',
            'sent'      => 'badge text-bg-primary',
            'paid'      => 'badge text-bg-success',
            'canceled'  => 'badge text-bg-dark',
            default     => 'badge text-bg-light'
        };
    }
}

if (! function_exists('status_label_id')) {
    /**
     * Get the Indonesian label for invoice status.
     *
     * @param string $status
     * @return string
     */
    function status_label_id(string $status, ?string $dueDate = null): string
    {
        if ($status === 'sent' && $dueDate && $dueDate < date('Y-m-d')) {
            return 'Telat';
        }
        
        return match($status) {
            'paid'      => 'Lunas',
            'sent'      => 'Terkirim',
            'draft'     => 'Draf',
            'canceled'  => 'Dibatalkan',
            default     => ucfirst($status)
        };
    }
}

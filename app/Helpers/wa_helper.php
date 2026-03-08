<?php

if (! function_exists('generate_wa_link')) {
    function generate_wa_link($phone, $message)
    {
        if (!has_feature('reminder_wa')) {
            return '';
        }

        // 1. Clean phone number
        $phone = preg_replace('/[^0-9]/', '', (string)$phone); // Remove non-numeric

        // 2. Format to international (ID) if phone is not empty
        if (!empty($phone)) {
            if (substr($phone, 0, 1) === '0') {
                $phone = '62' . substr($phone, 1);
            } elseif (substr($phone, 0, 2) !== '62') {
                $phone = '62' . $phone;
            }
        }

        // 3. Encode message
        $encodedMessage = rawurlencode($message);

        // 4. Return URL
        if (empty($phone)) {
            return "https://wa.me/?text={$encodedMessage}";
        }
        
        return "https://wa.me/{$phone}?text={$encodedMessage}";
    }
}

if (! function_exists('format_rupiah')) {
    function format_rupiah($angka)
    {
        return "Rp " . number_format($angka, 0, ',', '.');
    }
}

<?php

use Carbon\Carbon;

if (!function_exists('format_rupiah')) {
    function format_rupiah($amount, $with_fraction = false)
    {
        $decimals = $with_fraction ? 2 : 0;
        
        return 'Rp' . number_format($amount, $decimals, ',', '.');
    }
}

if (!function_exists('format_date_with_time')) {
    function format_date_with_time($date, $format = 'datetime') 
    {
        if (!$date) {
            return null;
        }

        $carbon = Carbon::parse($date);

        return match($format) {
            'date' => $carbon->translatedFormat('d F Y'),
            'time' => $carbon->translatedFormat('H:i'),
            'datetime' => $carbon->translatedFormat('d F Y H:i'),
            default => $carbon->translatedFormat('d F Y H:i'),
        };
    }
}
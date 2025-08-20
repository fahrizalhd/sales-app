<?php

if (!function_exists('format_rupiah')) {
    function format_rupiah($amount, $with_fraction = false)
    {
        $decimals = $with_fraction ? 2 : 0;
        return 'Rp' . number_format($amount, $decimals, ',', '.');
    }
}
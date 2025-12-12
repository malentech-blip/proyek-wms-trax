<?php

if (!function_exists('formatRupiah')) {
    function formatRupiah($value, $withPrefix = true)
    {
        $formatted = number_format($value, 0, ',', '.');
        return $withPrefix ? 'Rp ' . $formatted : $formatted;
    }
}

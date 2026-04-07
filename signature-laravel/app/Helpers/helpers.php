<?php

if (!function_exists('currency_symbol')) {
    /**
     * Return currency symbol for a given currency code.
     * Contract amounts default to INR (₹).
     */
    function currency_symbol(?string $code = null): string
    {
        return match (strtoupper((string) ($code ?? 'INR'))) {
            'USD' => '$',
            'INR' => '₹',
            'EUR' => '€',
            'GBP' => '£',
            default => '₹',
        };
    }
}

if (!function_exists('format_amount')) {
    /**
     * Format amount with currency symbol. Pass currency code for PI (USD/INR); omit for Contract (INR).
     */
    function format_amount($amount, ?string $currencyCode = null): string
    {
        $num = number_format((float) ($amount ?? 0), 2);

        return currency_symbol($currencyCode) . $num;
    }
}

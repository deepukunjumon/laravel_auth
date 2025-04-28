<?php

if (!function_exists('getStatusLabel')) {
    function getStatusLabel(string $type, int $status): string
    {
        return config("statusMappings.$type")[$status] ?? 'Unknown';
    }
}

if (!function_exists('getStatusValue')) {
    function getStatusValue(string $type, string $label): int|string|null
    {
        $mapping = config("statusMappings.$type");
        $flipped = array_flip($mapping);
        return $flipped[$label] ?? null;
    }
}

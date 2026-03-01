<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Parse MongoDB extended JSON format ($oid, $date, etc.) into PHP values.
 */
class MongoDBJsonParser
{
    public static function parseFile(string $path): array
    {
        $content = file_get_contents($path);
        if ($content === false || $content === '') {
            return [];
        }
        $decoded = json_decode($content, true);
        if (! is_array($decoded)) {
            return [];
        }
        return self::parseValue($decoded);
    }

    /**
     * Recursively parse a value, resolving $oid and $date.
     */
    public static function parseValue(mixed $value): mixed
    {
        if (is_array($value)) {
            if (isset($value['$oid'])) {
                return $value['$oid'];
            }
            if (isset($value['$date'])) {
                $date = $value['$date'];
                return is_string($date) ? Carbon::parse($date) : $date;
            }
            $result = [];
            foreach ($value as $k => $v) {
                $result[$k] = self::parseValue($v);
            }
            return $result;
        }
        return $value;
    }
}

<?php

namespace App\Models;

class Facility
{
    public static function all()
    {
        $file = storage_path('data/facilities.json');
        if (!file_exists($file)) {
            return [
                ['id' => 'building-a', 'name' => 'Building A'],
                ['id' => 'building-b', 'name' => 'Building B'],
                ['id' => 'campus', 'name' => 'Campus'],
            ];
        }
        return json_decode(file_get_contents($file), true) ?: [];
    }
}

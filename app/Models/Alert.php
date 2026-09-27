<?php

namespace App\Models;

class Alert
{
    public $id;
    public $type;
    public $facility_id;
    public $room;
    public $message;
    public $status;
    public $recipients;
    public $responders = [];
    public $created_at;
    public $updated_at;
    public $acknowledged_by;
    public $acknowledged_at;

    public static function all()
    {
        return self::readAll();
    }

    public static function readAll()
    {
        $file = storage_path('data/alerts.json');
        if (!file_exists($file)) {
            return [];
        }
        return json_decode(file_get_contents($file), true) ?: [];
    }

    public static function writeAll($alerts)
    {
        $file = storage_path('data/alerts.json');
        if (!file_exists(dirname($file))) {
            mkdir(dirname($file), 0755, true);
        }
        file_put_contents($file, json_encode($alerts, JSON_PRETTY_PRINT));
    }

    public static function find($id)
    {
        foreach (self::readAll() as $alert) {
            if ($alert['id'] === $id) {
                return $alert;
            }
        }
        return null;
    }
}

<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait TracksUserActions
{
    public static function bootTracksUserActions()
    {
        static::creating(function ($model) {
            $userId = Auth::id();

            if (self::hasColumn($model, 'created_by')) {
                $model->created_by = $userId;
            }

            if (self::hasColumn($model, 'updated_by')) {
                $model->updated_by = $userId;
            }
        });

        static::updating(function ($model) {
            $userId = Auth::id();

            if (self::hasColumn($model, 'updated_by')) {
                $model->updated_by = $userId;
            }
        });
    }

    protected static function hasColumn($model, $column)
    {
        try {
            return Schema::hasColumn($model->getTable(), $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}

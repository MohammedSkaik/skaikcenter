<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    use SoftDeletes;

    public static function bootAuditable()
    {
        // Set created_by on creation
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
            }
        });

        // Set updated_by on update
        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        // Set deleted_by on deletion (Soft Delete)
        static::deleting(function ($model) {
            if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model))) {
                if (Auth::check()) {
                    $model->deleted_by = Auth::id();
                }
                $model->is_deleted = true; // Explicitly set boolean flag
                $model->saveQuietly(); // saveQuietly to avoid triggering updated_at/updated_by again
            }
        });
    }

    // Add Scope to include creator info if needed
    public function scopeWithCreator(Builder $query)
    {
        return $query->with('creator');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by');
    }
}

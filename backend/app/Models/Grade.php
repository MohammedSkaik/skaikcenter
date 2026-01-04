<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends BaseModel
{
    protected $fillable = [
        'name',
        'level_order',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
        'is_deleted'
    ];

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }
}

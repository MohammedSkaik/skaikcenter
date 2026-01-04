<?php

namespace App\Models;

use App\Models\BaseModel;

class Room extends BaseModel
{
    protected $fillable = [
        'name',
        'capacity',
        'type',
        'created_by',
        'updated_by',
        'deleted_by',
        'is_deleted'
    ];
}

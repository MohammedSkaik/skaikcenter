<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Auditable uses this but good to be explicit or just rely on Trait

class Guardian extends BaseModel
{
    use HasFactory, Auditable;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'identification_number',
        'notes',
        'is_deleted'
    ];

    protected $casts = [
        'is_deleted' => 'boolean'
    ];
}

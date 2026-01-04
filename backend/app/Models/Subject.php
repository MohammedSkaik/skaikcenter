<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subject extends BaseModel
{
    protected $fillable = [
        'grade_id',
        'name',
        'price_package',
        'price_single',
        'created_by',
        'updated_by',
        'deleted_by',
        'is_deleted'
    ];

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }
}

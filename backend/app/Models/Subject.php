<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subject extends BaseModel
{
    protected $fillable = [
        'name',
        // Audit fields
        'created_by',
        'updated_by',
        'deleted_by',
        'is_deleted'
    ];

    public function grades()
    {
        return $this->belongsToMany(Grade::class, 'grade_subject')
            ->withPivot('price_package', 'price_single', 'price_one_session')
            ->withTimestamps();
    }
}

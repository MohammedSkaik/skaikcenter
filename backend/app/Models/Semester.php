<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Semester extends BaseModel
{
    protected $fillable = [
        'academic_year_id',
        'name',
        'status',
        'start_date',
        'end_date',
        // Audit fields
        'created_by',
        'updated_by',
        'deleted_by',
        'is_deleted'
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}

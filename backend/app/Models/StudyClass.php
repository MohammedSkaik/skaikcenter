<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyClass extends BaseModel
{
    protected $fillable = [
        'semester_id',
        'grade_id',
        'name',
        'type',
        'subject_id',
        'max_students',
        'created_by',
        'updated_by',
        'deleted_by',
        'is_deleted'
    ];

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}

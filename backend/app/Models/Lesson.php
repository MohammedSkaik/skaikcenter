<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends BaseModel
{
    protected $fillable = [
        'teaching_session_id',
        'subject_id',
        'teacher_id',
        'status',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
        'is_deleted'
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(TeachingSession::class, 'teaching_session_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}

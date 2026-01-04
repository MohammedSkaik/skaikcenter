<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeachingSession extends BaseModel
{
    protected $fillable = [
        'study_class_id',
        'room_id',
        'date',
        'start_time',
        'end_time',
        'title',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'is_deleted'
    ];

    protected $casts = [
        'date' => 'date',
        // 'start_time' and 'end_time' are cast to string by default or can use custom casts
    ];

    public function studyClass(): BelongsTo
    {
        return $this->belongsTo(StudyClass::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }
}

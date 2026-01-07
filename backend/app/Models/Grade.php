<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends BaseModel
{
    protected $fillable = [
        'name',
        'academic_year_id',
        'level_order',
        'package_price',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
        'is_deleted',
        'deleted_at'
    ];

    public function academic_year()
    {
        return $this->belongsTo(AcademicYear::class);
    }
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'grade_subject')
            ->withPivot('price_single', 'price_one_session')
            ->withTimestamps();
    }
}

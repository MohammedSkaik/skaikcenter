<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class BaseModel extends Model
{
    use Auditable;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
        'is_deleted', // Assuming we might use a boolean flag too, but standardized on deleted_at timestamp usually.
        // Spec asked for "is_deleted" boolean explicitly as well? 
        // "1. Soft Delete فقط - لا حذف نهائي من قاعدة البيانات، استخدم is_deleted، deleted_at، deleted_by"
        // Standard Laravel SoftDeletes uses deleted_at being non-null. 
        // We will add an accessor or just rely on timestamps, but let's add the column to migration to be compliant with specs.
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_deleted' => 'boolean',
        ];
    }
}

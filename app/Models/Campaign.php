<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Campaign extends Model
{
    protected $guarded = ['id'];
    protected $hidden = [
        'id',
        'video_path',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'screens'       => 'json',
        'video_details' => 'json',
    ];

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedImage extends Model
{
    protected $fillable = [
        'user_id',
        'prompt_id',
        'file_name',
        'positive_prompt',
        'original_prompt',
        'canonical_prompt',
        'model_used',
        'width',
        'height',
        'subfolder',
        'type',
    ];
    public function user()
{
    return $this->belongsTo(\App\Models\User::class, 'user_id');
}
}

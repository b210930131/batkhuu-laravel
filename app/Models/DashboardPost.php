<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardPost extends Model
{
    protected $fillable = [
        'title',
        'image_path',
        'paragraph',
        'positive_prompt',
        'negative_prompt',
        'settings',
        'recommendation',
        'sort_order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}

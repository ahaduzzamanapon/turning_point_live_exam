<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemePreset extends Model
{
    protected $fillable = ['name', 'is_default', 'settings'];

    protected $casts = [
        'settings' => 'array',
        'is_default' => 'boolean',
    ];
}

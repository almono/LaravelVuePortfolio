<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Curriculum extends Model
{
    protected $table = 'curriculum';
    protected $casts = [
        'main_info' => 'array',
        'education' => 'array',
        'experience' => 'array',
        'skills' => 'array',
        'interests' => 'array',
        'other_projects' => 'array',
    ];
}

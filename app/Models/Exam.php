<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'duration_minutes',
        'total_marks',
        'negative_marking',
        'start_time',
        'end_time',
        'status',
        'access_mode' // PUBLIC, PRIVATE
    ];

    protected $casts = [
        'negative_marking' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function assignments()
    {
        return $this->belongsToMany(User::class, 'exam_assignments', 'exam_id', 'user_id');
    }

    public function rules()
    {
        return $this->hasMany(ExamRule::class);
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_questions', 'exam_id', 'question_id')
            ->withPivot('pivot_order')
            ->orderBy('pivot_order');
    }
}

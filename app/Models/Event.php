<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Question;

class Event extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'poster_image',
        'registration_fee',
        'start_time',
        'end_time',
        'duration_minutes',
        'total_marks',
        'negative_marking',
        'prize_pool_config',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'prize_pool_config' => 'array',
        'registration_fee' => 'decimal:2',
        'negative_marking' => 'decimal:2',
    ];

    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_participants')
            ->withPivot('payment_status', 'score', 'rank', 'prize_won', 'status')
            ->withTimestamps();
    }

    // We can reuse the Exam Questions table logic or create a new pivot
    // For simplicity and reusing the static paper logic, let's use the same Question model
    public function questions()
    {
        // Polymorphic or separate pivot? 
        // As per plan, let's stick to a specific event_questions pivot or reuse existing if capable.
        // Since we didn't make event_questions table yet, I will create it or use a JSON store for questions if its random.
        // User asked for "random question admin can change it".
        // Let's create `event_questions` pivot table now to be safe and standard.
        return $this->belongsToMany(Question::class, 'event_questions', 'event_id', 'question_id')
            ->withPivot('order')
            ->orderBy('event_questions.order');
    }
}

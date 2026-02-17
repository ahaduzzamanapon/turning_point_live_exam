<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_participant_id',
        'question_id',
        'selected_options',
        'is_correct',
        'marks_obtained',
    ];

    protected $casts = [
        'selected_options' => 'array',
        'is_correct' => 'boolean',
        'marks_obtained' => 'decimal:2',
    ];

    public function participant()
    {
        return $this->belongsTo(EventParticipant::class, 'event_participant_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GridImportRun extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'event_stage_id',
        'action',
        'status',
        'input',
        'output',
        'error_message',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'input' => 'array',
        'output' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function stage()
    {
        return $this->belongsTo(EventStage::class, 'event_stage_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GridSeries extends Model
{
    protected $fillable = [
        'event_id',
        'event_stage_id',
        'grid_series_id',
        'grid_tournament_id',
        'grid_title_id',
        'status',
        'team_one_name',
        'team_two_name',
        'starts_at',
        'events_file_path',
        'end_state_file_path',
        'source_payload',
        'last_seen_at',
        'downloaded_at',
        'imported_at',
    ];

    protected $casts = [
        'source_payload' => 'array',
        'starts_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'downloaded_at' => 'datetime',
        'imported_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function stage()
    {
        return $this->belongsTo(EventStage::class, 'event_stage_id');
    }
}
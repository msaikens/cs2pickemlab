<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GridTournamentCache extends Model
{
    protected $table = 'grid_tournament_cache';

    protected $fillable = [
        'grid_tournament_id',
        'name',
        'grid_title_id',
        'is_cs2',
        'last_seen_at',
    ];

    protected $casts = [
        'is_cs2' => 'boolean',
        'last_seen_at' => 'datetime',
    ];
}
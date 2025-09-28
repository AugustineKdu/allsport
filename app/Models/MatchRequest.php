<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchRequest extends Model
{
    use HasFactory;

    protected $table = 'match_requests';

    protected $fillable = [
        'requesting_team_id',
        'requested_team_id',
        'match_date',
        'match_time',
        'venue',
        'message',
        'contact_phone',
        'status',
    ];

    protected $casts = [
        'match_date' => 'date',
        'match_time' => 'datetime:H:i',
    ];

    /**
     * Get the team that made the request.
     */
    public function requestingTeam()
    {
        return $this->belongsTo(Team::class, 'requesting_team_id');
    }

    /**
     * Get the team that received the request.
     */
    public function requestedTeam()
    {
        return $this->belongsTo(Team::class, 'requested_team_id');
    }
}

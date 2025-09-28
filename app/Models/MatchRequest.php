<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MatchRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'home_team_id',
        'requesting_team_id',
        'sport',
        'city',
        'district',
        'preferred_date',
        'preferred_time',
        'message',
        'status',
        'responded_at',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'preferred_time' => 'datetime:H:i',
        'responded_at' => 'datetime',
    ];

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function requestingTeam()
    {
        return $this->belongsTo(Team::class, 'requesting_team_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchInvitation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'inviting_team_id',
        'invited_team_id',
        'proposed_date',
        'proposed_time',
        'proposed_venue',
        'message',
        'contact_phone',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'proposed_date' => 'date',
        'proposed_time' => 'datetime:H:i',
    ];

    /**
     * Get the team that sent the invitation.
     */
    public function invitingTeam()
    {
        return $this->belongsTo(Team::class, 'inviting_team_id');
    }

    /**
     * Get the team that received the invitation.
     */
    public function invitedTeam()
    {
        return $this->belongsTo(Team::class, 'invited_team_id');
    }

    /**
     * Check if the invitation is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the invitation is accepted.
     */
    public function isAccepted()
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if the invitation is rejected.
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if the invitation is cancelled.
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'team_id',
        'user_id',
        'role',
        'status',
        'message',
        'joined_at',
        'last_active_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'joined_at' => 'datetime',
        'last_active_at' => 'datetime',
    ];

    /**
     * Get the team.
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if member is online (active within last 2 minutes).
     */
    public function isOnline()
    {
        return $this->last_active_at && $this->last_active_at->gte(now()->subMinutes(2));
    }

    /**
     * Scope for approved members.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending members.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for online members.
     */
    public function scopeOnline($query)
    {
        return $query->where('last_active_at', '>=', now()->subMinutes(2));
    }

    /**
     * Update last active time.
     */
    public function updateLastActive()
    {
        $this->update(['last_active_at' => now()]);
    }

    /**
     * Approve membership.
     */
    public function approve()
    {
        $this->update([
            'status' => 'approved',
            'joined_at' => now(),
        ]);
    }

    /**
     * Reject membership.
     */
    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * Leave team.
     */
    public function leave()
    {
        $this->update(['status' => 'left']);
    }
}

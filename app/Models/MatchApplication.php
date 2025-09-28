<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'team_id',
        'applied_by',
        'status',
        'message',
        'contact_phone',
        'contact_person',
        'availability',
        'applied_at',
        'responded_at',
    ];

    protected $casts = [
        'availability' => 'array',
        'applied_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    /**
     * Get the match.
     */
    public function match()
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    /**
     * Get the team.
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /**
     * Get the user who applied.
     */
    public function applicant()
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    /**
     * Scope for pending applications.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for accepted applications.
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Scope for rejected applications.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Accept the application.
     */
    public function accept()
    {
        $this->update([
            'status' => 'accepted',
            'responded_at' => now(),
        ]);

        // Update the match with the away team
        $this->match->update([
            'away_team_id' => $this->team_id,
            'away_team_name' => $this->team->team_name,
            'is_matching_open' => false,
        ]);

        // Reject all other pending applications
        $this->match->pendingApplications()
            ->where('id', '!=', $this->id)
            ->update([
                'status' => 'rejected',
                'responded_at' => now(),
            ]);
    }

    /**
     * Reject the application.
     */
    public function reject()
    {
        $this->update([
            'status' => 'rejected',
            'responded_at' => now(),
        ]);
    }

    /**
     * Withdraw the application.
     */
    public function withdraw()
    {
        $this->update([
            'status' => 'withdrawn',
            'responded_at' => now(),
        ]);
    }
}

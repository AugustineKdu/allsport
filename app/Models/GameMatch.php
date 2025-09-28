<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameMatch extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'matches';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sport',
        'city',
        'district',
        'home_team_id',
        'away_team_id',
        'home_team_name',
        'away_team_name',
        'match_date',
        'match_time',
        'venue',
        'notes',
        'status',
        'home_score',
        'away_score',
        'finalized_at',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'match_date' => 'date',
        'match_time' => 'datetime:H:i',
        'finalized_at' => 'datetime',
        'home_score' => 'integer',
        'away_score' => 'integer',
    ];

    /**
     * Get the home team.
     */
    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    /**
     * Get the away team.
     */
    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    /**
     * Get the user who created the match.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for scheduled matches.
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', '예정');
    }

    /**
     * Scope for ongoing matches.
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', '진행중');
    }

    /**
     * Scope for completed matches.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', '완료');
    }

    /**
     * Scope for cancelled matches.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', '취소');
    }

    /**
     * Scope for upcoming matches.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', '예정')
            ->where('match_date', '>=', today())
            ->orderBy('match_date')
            ->orderBy('match_time');
    }

    /**
     * Check if match is finalized.
     */
    public function isFinalized()
    {
        return !is_null($this->finalized_at);
    }

    /**
     * Finalize match and update team statistics.
     */
    public function finalizeMatch()
    {
        // Prevent double finalization
        if ($this->isFinalized()) {
            return false;
        }

        // Match must be completed with scores
        if ($this->status !== '완료' || is_null($this->home_score) || is_null($this->away_score)) {
            return false;
        }

        $homeTeam = $this->homeTeam;
        $awayTeam = $this->awayTeam;

        // Determine winner and update statistics
        if ($this->home_score > $this->away_score) {
            $homeTeam->increment('wins');
            $awayTeam->increment('losses');
        } elseif ($this->home_score < $this->away_score) {
            $awayTeam->increment('wins');
            $homeTeam->increment('losses');
        } else {
            $homeTeam->increment('draws');
            $awayTeam->increment('draws');
        }

        // Update points (3 for win, 1 for draw)
        $homeTeam->update(['points' => 3 * $homeTeam->wins + $homeTeam->draws]);
        $awayTeam->update(['points' => 3 * $awayTeam->wins + $awayTeam->draws]);

        // Mark as finalized
        $this->update(['finalized_at' => now()]);

        return true;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($match) {
            // Cache team names
            if ($match->homeTeam) {
                $match->home_team_name = $match->homeTeam->team_name;
            }
            if ($match->awayTeam) {
                $match->away_team_name = $match->awayTeam->team_name;
            }
        });

        static::updated(function ($match) {
            // Auto-finalize when match is marked as completed
            if ($match->isDirty('status') && $match->status === '완료') {
                $match->finalizeMatch();
            }
        });
    }
}

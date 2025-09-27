<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Team extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'team_name',
        'team_name_canon',
        'slug',
        'sport',
        'city',
        'district',
        'owner_user_id',
        'wins',
        'draws',
        'losses',
        'points',
        'join_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'wins' => 'integer',
        'draws' => 'integer',
        'losses' => 'integer',
        'points' => 'integer',
    ];

    /**
     * Get the owner of the team.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    /**
     * Get the team members.
     */
    public function members()
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * Get approved team members.
     */
    public function approvedMembers()
    {
        return $this->members()->where('status', 'approved');
    }

    /**
     * Get pending team members.
     */
    public function pendingMembers()
    {
        return $this->members()->where('status', 'pending');
    }

    /**
     * Get home matches.
     */
    public function homeMatches()
    {
        return $this->hasMany(GameMatch::class, 'home_team_id');
    }

    /**
     * Get away matches.
     */
    public function awayMatches()
    {
        return $this->hasMany(GameMatch::class, 'away_team_id');
    }

    /**
     * Get all matches (home and away).
     */
    public function allMatches()
    {
        return GameMatch::where('home_team_id', $this->id)
            ->orWhere('away_team_id', $this->id);
    }

    /**
     * Get the sport type.
     */
    public function sportType()
    {
        return $this->belongsTo(Sport::class, 'sport', 'sport_name');
    }

    /**
     * Get the number of online members.
     */
    public function getOnlineMembersCount()
    {
        return $this->approvedMembers()
            ->where('last_active_at', '>=', now()->subMinutes(2))
            ->count();
    }

    /**
     * Generate canonical name for team.
     */
    public static function canonicalizeName($name)
    {
        // Convert to lowercase
        $canon = Str::lower($name);

        // Remove whitespace and punctuation
        $canon = preg_replace('/[\s\-_\.]+/', '', $canon);

        // Normalize common suffixes
        $replacements = [
            'fc' => 'fc',
            '에프씨' => 'fc',
            'ＦＣ' => 'fc',
            'united' => 'united',
            '유나이티드' => 'united',
        ];

        foreach ($replacements as $search => $replace) {
            $canon = str_replace($search, $replace, $canon);
        }

        return $canon;
    }

    /**
     * Generate slug for team.
     */
    public static function generateSlug($city, $district, $teamNameCanon)
    {
        $baseSlug = Str::slug($city . '-' . $district . '-' . $teamNameCanon);

        // If slug is empty (e.g., for Korean characters), use team ID
        if (empty($baseSlug)) {
            // Get the next available ID
            $nextId = self::max('id') + 1;
            $baseSlug = 'team-' . $nextId;
        }

        // Ensure uniqueness
        $slug = $baseSlug;
        $counter = 1;
        while (self::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Generate a unique join code.
     */
    public static function generateJoinCode()
    {
        do {
            $code = Str::upper(Str::random(6));
        } while (self::where('join_code', $code)->exists());

        return $code;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($team) {
            // Generate canonical name
            $team->team_name_canon = self::canonicalizeName($team->team_name);

            // Generate slug
            $team->slug = self::generateSlug($team->city, $team->district, $team->team_name_canon);

            // Generate join code if not provided
            if (empty($team->join_code)) {
                $team->join_code = self::generateJoinCode();
            }
        });

        static::updating(function ($team) {
            // Update canonical name if team name changed
            if ($team->isDirty('team_name')) {
                $team->team_name_canon = self::canonicalizeName($team->team_name);
            }

            // Update slug if relevant fields changed
            if ($team->isDirty(['city', 'district', 'team_name_canon'])) {
                $team->slug = self::generateSlug($team->city, $team->district, $team->team_name_canon);
            }
        });
    }
}

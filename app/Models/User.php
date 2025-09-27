<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nickname',
        'city',
        'district',
        'selected_sport',
        'onboarding_done',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'onboarding_done' => 'boolean',
        ];
    }

    /**
     * Get the teams owned by the user.
     */
    public function ownedTeams()
    {
        return $this->hasMany(Team::class, 'owner_user_id');
    }

    /**
     * Get the team memberships for the user.
     */
    public function teamMemberships()
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * Get the user's current team (approved membership).
     */
    public function currentTeam()
    {
        return $this->teamMemberships()
            ->where('status', 'approved')
            ->with('team')
            ->first()?->team;
    }

    /**
     * Get matches created by the user.
     */
    public function createdMatches()
    {
        return $this->hasMany(GameMatch::class, 'created_by');
    }

    /**
     * Check if user is a team owner.
     */
    public function isTeamOwner()
    {
        return $this->role === 'team_owner';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}

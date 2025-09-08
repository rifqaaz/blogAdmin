<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
            'password' => 'hashed',
        ];
    }

        public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // An editor belongs to an admin
    public function admin()
    {
        return $this->belongsTo(User::class, 'assigned_to_admin_id');
    }
    
    // An admin has many editors
    public function assignedEditors()
    {
        return $this->hasMany(User::class, 'assigned_to_admin_id');
    }
    
    // A user belongs to an editor
    public function editor()
    {
        return $this->belongsTo(User::class, 'assigned_to_editor_id');
    }
    
    // An editor has many users
    public function assignedUsers()
    {
        return $this->hasMany(User::class, 'assigned_to_editor_id');
    }
    
    // Scopes
    // public function scopeEditors($query)
    // {
    //     return $query->whereHas('roles', fn($q) => $q->where('name', 'editor'));
    // }
    
    // public function scopeAdmins($query)
    // {
    //     return $query->whereHas('roles', fn($q) => $q->where('name', 'admin'));
    // }
    
    // public function scopeRegularUsers($query)
    // {
    //     return $query->whereHas('roles', fn($q) => $q->where('name', 'user'));
    // }
}

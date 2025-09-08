<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    protected $fillable = ['title', 'body', 'image', 'is_active', 'user_id', 'editor_id', 'category_id', 'slug', 'approved_at'];

    protected $attributes = ['is_active' => false];

     protected $casts = [
        'is_active' => 'boolean',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($post) {
            $post->slug = Str::slug($post->title);
        });
        static::updating(function ($post) {
            $post->slug = Str::slug($post->title);
        });
    }

    protected static function booted(): void
    {
        static::saving(function (Post $post) {
            // Check if the 'is_active' attribute is being changed
            if ($post->isDirty('is_active')) { 
                if ($post->is_active) {
                    $post->approved_at = now();
                } 
                else {
                    $post->approved_at = null;
                }
            }
            
            // Optional: Automatically set the editor on approval?
            // If the post is being approved and no editor is set,
            // assign the currently authenticated user as the editor.
            // if ($post->isDirty('is_active') && $post->is_active && is_null($post->editor_id)) {
            //     // Use optional auth helper to avoid errors if no user is logged in (e.g., console commands)
            //     $post->editor_id = auth()->id(); 
            // }
        });
    }

    public function scopeActive($query)
    {
    return $query->where('posts.is_active', true);
    }

    public function scopeInactive($query)
    {
    return $query->where('posts.is_active', false);
    }

    public function scopeForEditor($query)
    {
        $user = auth()->user();

        if ($user->hasRole('editor')) {
            return $query->where(function($q) use ($user) {
                $q->where('posts.editor_id', $user->id);
            });
        }
    }

    use HasFactory;
}

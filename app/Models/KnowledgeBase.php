<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    protected $table = 'knowledge_bases';

    protected $fillable = [
        'title', 'content', 'excerpt', 'category', 'status', 'tags',
        'views', 'likes', 'dislikes', 'featured', 'last_updated', 'created_by',
    ];

    protected $casts = [
        'tags' => 'array',
        'featured' => 'boolean',
        'last_updated' => 'datetime',
    ];

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

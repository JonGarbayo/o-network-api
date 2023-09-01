<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    public $fillable = [
        'text'
    ];

    public function organization()
    {
        // The arguments of this hasOneThrough call seem a little bit strange,
        // but it's the only way to make it work with a ManyToOne final
        // relationship (= multiple users in a single organization). Check the
        // Laracasts forum post here for some explanations:
        // https://laracasts.com/discuss/channels/eloquent/help-me-understand-the-problem-with-this-hasonethrough-relationship?page=1&replyId=518699
        return $this->hasOneThrough(Organization::class, User::class, 'id', 'id', 'author_id', 'organization_id');
    }

    public function post() {
        return $this->belongsTo(Post::class);
    }

    public function author() {
        return $this->belongsTo(User::class, 'author_id');
    }
}

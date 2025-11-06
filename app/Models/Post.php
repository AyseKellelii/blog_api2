<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Tags\HasTags;

class Post extends Model
{
    use HasFactory, LogsActivity,HasTags;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'content'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('post')
            ->logOnly(['title', 'slug', 'content'])
            ->setDescriptionForEvent(fn(string $eventName) => "Gönderi {$eventName} işlemi yapıldı")
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}

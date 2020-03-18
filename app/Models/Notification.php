<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Rate
 */
class Notification extends Model
{
    protected $table = 'notifications';

    public $timestamps = false;

    protected $fillable = [
        'chunk',
        'user_id',
        'title',
        'message',
        'created_at',
        'unread'
    ];

    protected $guarded = [];
}
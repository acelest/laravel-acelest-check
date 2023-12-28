<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'message', 'is_read', 'user_sender', 'user_receive'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_sender');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'user_receive');
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}
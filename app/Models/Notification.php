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

    /**
     * Relation avec l'utilisateur associé à la notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'utilisateur expéditeur.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'user_sender');
    }

    /**
     * Relation avec l'utilisateur destinataire.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'user_receive');
    }

    /**
     * Marquer la notification comme lue.
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}

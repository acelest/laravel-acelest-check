<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'start_date', 'end_date', 'motif', 'statut'
    ];

    /**
     * Relation avec l'utilisateur associé à l'absence.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Marquer l'absence comme approuvée.
     */
    public function approve()
    {
        $this->update(['statut' => 'approved']);
    }

    /**
     * Marquer l'absence comme refusée.
     */
    public function reject()
    {
        $this->update(['statut' => 'rejected']);
    }
}

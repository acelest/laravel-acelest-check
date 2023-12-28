<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * @OA\Schema(
 *     schema="Absence",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="date", type="string", format="date"),
 *     @OA\Property(property="motif", type="string"),
 *     @OA\Property(property="statut", type="string", enum={"en attente", "approuvé", "annulé"}),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 * )
 */

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'date', 'motif', 'statut',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approve()
    {
        $this->update(['statut' => 'approuvé']);
    }

    public function cancel()
    {
        $this->update(['statut' => 'annulé']);
    }
}

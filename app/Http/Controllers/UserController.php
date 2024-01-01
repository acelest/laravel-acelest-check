<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response; // Importer la classe Response
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response([
                    'message' => 'Identifiants invalides !'
                ], Response::HTTP_UNAUTHORIZED);
            }

            $user = Auth::user();

            $token = $user->createToken('token')->plainTextToken;

            $cookie = cookie('jwt', $token, 60 * 24); // 1 jour

            return response([
                'message' => 'Connexion réussie',
                'token' => $token,
                'user' => $user
            ])->withCookie($cookie);
        } catch (\Exception $e) {
            return response(['message' => 'Échec de la connexion'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function viewAbsences()
    {
        $user = auth()->user();

        $absences = $user->absences;

        return response()->json(['absences' => $absences]);
    }

    public function sendMessageToAdmin(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'message' => 'required|string',
            ]);

            $user = auth()->user();

            $admin = User::where('role', 'admin')->first();
            $admin->notifications()->create([
                'message' => $validatedData['message'],
                'user_sender' => $user->id,
                'user_receive' => $admin->id,
            ]);

            return response()->json(['message' => 'Message envoyé à l\'administrateur avec succès']);
        } catch (\Exception $e) {
            return response(['message' => 'Échec de l\'envoi du message à l\'administrateur'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

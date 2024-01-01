<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Absence;
use App\Models\User;

class SuperviseurController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials!'], 401);
        }

        $superviseur = Auth::user();
        $token = $superviseur->createToken('token')->plainTextToken;

        $cookie = cookie('jwt', $token, 60 * 24); // 1 jour

        return response()->json(['superviseur' => $superviseur, 'message' => 'Login successful'])->withCookie($cookie);
    }

    public function listUsers()
    {
        $users = User::all();

        return response()->json(['users' => $users]);
    }

    public function listAbsencesPerUser($userId)
    {
        $user = User::findOrFail($userId);
        $absences = $user->absences;
        return response()->json(['absences' => $absences]);
    }

    public function viewMotifsForAbsence($absenceId)
    {
        $absence = Absence::findOrFail($absenceId);
        return response()->json(['motif' => $absence->motif]);
    }

    public function authorizeDeleteAbsence(Request $request, $absenceId)
    {
        $validatedData = $request->validate([
            'canceled_by_superviseur' => 'required|boolean',
        ]);

        $absence = Absence::findOrFail($absenceId);

        if ($validatedData['canceled_by_superviseur']) {
            $absence->update(['statut' => 'canceled']);
            $message = 'Absence canceled successfully';
        } else {
            $message = 'Absence not authorized by superviseur';
        }

        return response()->json(['message' => $message]);
    }

    public function cancelAbsence(Request $request, $absenceId)
    {
        $validatedData = $request->validate([
            'canceled_by_superviseur' => 'required|boolean',
        ]);

        $absence = Absence::findOrFail($absenceId);

        if ($validatedData['canceled_by_superviseur']) {
            $absence->update(['statut' => 'canceled']);
            $message = 'Absence canceled successfully';
        } else {
            $message = 'Absence not authorized by superviseur';
        }

        return response()->json(['message' => $message]);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        $cookie = cookie('jwt', '', 0);

        return response()->json(['message' => 'Logout successful'])->withCookie($cookie);
    }
}

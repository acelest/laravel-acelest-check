<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Absence;
use App\Models\Notification;
use PDF;

class AdminController extends Controller {

    public function createUser(Request $request) {
        // Validation
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'matricule' => 'required|unique:users',
            'role' => 'required|in:user,admin,superviseur',
        ]);

        // Enregistrement de la photo
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
            $validatedData['photo'] = $photoPath;
        }

        // Création de l'utilisateur
        $user = User::create($validatedData);

        return response()->json(['user' => $user, 'message' => 'User created successfully']);
    }

    public function updateUser(Request $request, $userId) {
        // Validation
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $userId,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'role' => 'required|in:user,admin,superviseur',
        ]);

        // Enregistrement de la photo
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
            $validatedData['photo'] = $photoPath;
        }

        // Mise à jour de l'utilisateur
        $user = User::findOrFail($userId);
        $user->update($validatedData);

        return response()->json(['user' => $user, 'message' => 'User updated successfully']);
    }

    public function deleteUser($userId) {
        // Suppression de l'utilisateur
        $user = User::findOrFail($userId);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function listUsers() {
        // Liste des utilisateurs
        $users = User::all();

        return response()->json(['users' => $users]);
    }

    public function markAbsentUsers(Request $request) {
        // Marquer les utilisateurs absents pour une date spécifique
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = $request->input('date');

        $absentUsers = User::select('id', 'name')
            ->whereNotExists(function ($query) use ($date) {
                $query->select(DB::raw(1))
                    ->from('absences')
                    ->whereRaw('absences.user_id = users.id')
                    ->where('date', '=', $date);
            })
            ->get();

        return response()->json(['absent_users' => $absentUsers]);
    }

    public function sendNotificationToSuperviseur(Request $request) {
        // Envoyer une notification au superviseur
        $request->validate([
            'message' => 'required|string',
        ]);

        $superviseur = User::where('role', 'superviseur')->first();

        Notification::create([
            'user_id' => $superviseur->id,
            'message' => $request->input('message'),
        ]);

        return response()->json(['message' => 'Notification sent to superviseur']);
    }

    public function sendNotificationToAbsentUser(Request $request, $userId) {
        // Envoyer une notification à la personne absente
        $request->validate([
            'message' => 'required|string',
        ]);

        $absentUser = User::findOrFail($userId);

        Notification::create([
            'user_id' => $absentUser->id,
            'message' => $request->input('message'),
        ]);

        return response()->json(['message' => 'Notification sent to absent user']);
    }

    public function cancelAbsenceAfterAuthorization(Request $request, $absenceId) {
        // Annuler une absence après autorisation du superviseur
        $request->validate([
            'canceled_by_superviseur' => 'required|boolean',
        ]);

        $absence = Absence::findOrFail($absenceId);

        if ($request->input('canceled_by_superviseur')) {
            $absence->update(['statut' => 'canceled']);
            $message = 'Absence canceled successfully';
        } else {
            $message = 'Absence not authorized by superviseur';
        }

        return response()->json(['message' => $message]);
    }

    public function listMonthlyAbsences($userId) {
        // Liste mensuelle des absences de chaque utilisateur en PDF
        $user = User::findOrFail($userId);
        $absences = $user->absences;

        $data = ['user' => $user, 'absences' => $absences];
        $pdf = PDF::loadView('pdf.monthly_absences', $data);

        return $pdf->download('monthly_absences_' . $user->matricule . '.pdf');
    }

    public function printMemberCard($userId) {
        // Imprimer en PDF une carte de membre de chaque utilisateur
        $user = User::findOrFail($userId);
        $data = ['user' => $user];
        $pdf = PDF::loadView('pdf.member_card', $data);

        return $pdf->download('member_card_' . $user->matricule . '.pdf');
    }
}

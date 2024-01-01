<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use PDF;


class AdminController extends Controller {

public function CreateUser (Request $request){

    // validation
    $validatedData = $request -> validate ([
        'name' => 'required|string',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'matricule' => 'required|unique:users',
        'role' => 'required|in:user,admin,superviseur',
    ]);

    /// enregistrement de la photo

if ($request->hasFile('photo')) {
    $photoPath =$request->file('photo')->store('photos','public');
    $validatedData['photo']=$photoPath;
}

//creation de l'user
$user = User::create($validatedData);

        return response()->json(['user' => $user, 'message' => 'User created successfully']);

}

public function printMemberCard($userId) {

    // pdf de la carte
    $user = User::findOrFail($userId);
    $data = ['user' =>$user];
    $pdf = PDF::loadView('pdf.member_card', $data);

    return $pdf->download('member_card_' . $user->matricule . '.pdf');
}

}

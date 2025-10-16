<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{

    public function register(Request $request){
        try{
            $validated = $request->validate([
                "name" => "required|string|max:255",
                "email" => "required|string|email|unique:users,email|max:255",
                "password" => "required|string|min:8"
            ]);

            $user = User::create([
                "name" => $validated["name"],
                "email" => $validated["email"],
                "password" => Hash::make($validated["password"])
            ]);

            $token = $user->createToken("auth_token")->plainTextToken;

            return response()->json([

                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                    "created_at" => $user->created_at
                ], 201);
        }catch(ValidationException $e){
            return response()->json([
                "success" => false,
                "message" => "Erreur de validation",
                "errors" => $e->errors()
            ], 422);
        }catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Une erreur est survenue",
                "error" => $e->getMessage()
            ], 500);
        }
    }


    public function login(Request $request){
        try{
            $validated = $request->validate([
                "email" => "required|string|email",
                "password" => "required|string"
            ]);

            if(!auth()->attempt($validated)){
                return response()->json([
                    "success" => false,
                    "message" => "Identifiants invalides"
                ], 401);
            }

            $user = auth()->user();
            $token = $user->createToken("auth_token")->plainTextToken;

            return response()->json([
                    "token" => $token,
                    "user_id" => $user->id,
                ],200);
        }catch(ValidationException $e){
            return response()->json([
                "success" => false,
                "message" => "Erreur de validation",
                "errors" => $e->errors()
            ], 422);
        }catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Une erreur est survenue",
                "error" => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::all();

        return response()->json(
            $modules->map(function ($module) {
                return [
                    "id" => $module->id,
                    "name" => $module->name,
                    "description" => $module->description
                ];
            })
        , 200);
    }


    public function activate(Request $request, $id)
    {
        $user = $request->user();
        $module = Module::find($id);

        if (!$module) {
            return response()->json([
                "error" => "Module not found"
            ], 404);
        }
        $user->modules()->syncWithoutDetaching([
            $id => ["active" => true]
        ]);

        return response()->json([
            "message" => "Module activated"
        ], 200);
    }

    public function deactivate(Request $request, $id)
    {
        $user = $request->user();

        $module = Module::find($id);

        if (!$module) {
            return response()->json([
                "error" => "Module not found"
            ], 404);
        }

        // Créer ou mettre à jour l'entrée dans user_modules avec active = false
        $user->modules()->syncWithoutDetaching([
            $id => ["active" => false]
        ]);

        return response()->json([
            "message" => "Module deactivated"
        ], 200);
    }
}

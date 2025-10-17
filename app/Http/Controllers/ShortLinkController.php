<?php

namespace App\Http\Controllers;

use App\Models\ShortLink;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ShortLinkController extends Controller
{
    public function shorten(Request $request)
    {
        try {
            $validated = $request->validate([
                'original_url' => 'required|url',
                'custom_code' => 'nullable|string|max:10|regex:/^[a-zA-Z0-9_-]+$/|unique:short_links,code'
            ]);

            $user = $request->user();

            // Code aléatoire
            $code = $validated['custom_code'] ?? ShortLink::generateUniqueCode();

            $shortLink = ShortLink::create([
                'user_id' => $user->id,
                'original_url' => $validated['original_url'],
                'code' => $code,
                'clicks' => 0
            ]);

            return response()->json([
                'id' => $shortLink->id,
                'user_id' => $shortLink->user_id,
                'original_url' => $shortLink->original_url,
                'code' => $shortLink->code,
                'clicks' => $shortLink->clicks,
                'created_at' => $shortLink->created_at
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function redirect($code)
    {
        $shortLink = ShortLink::where('code', $code)->first();

        if (!$shortLink) {
            return response()->json([
                'error' => 'Link not found'
            ], 404);
        }

        $shortLink->increment('clicks');

        return redirect($shortLink->original_url, 302);
    }


    public function index(Request $request)
    {
        $user = $request->user();

        $links = ShortLink::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'original_url', 'code', 'clicks', 'created_at']);

        return response()->json($links, 200);
    }


    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $shortLink = ShortLink::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$shortLink) {
            return response()->json([
                'error' => 'Link not found'
            ], 404);
        }

        $shortLink->delete();

        return response()->json([
            'message' => 'Link deleted successfully'
        ], 200);
    }
}

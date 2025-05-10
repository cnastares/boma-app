<?php

namespace App\Api\V1\Controllers\Ad;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Class AdSearchController
 * Handles search operations for ads within the Adfox application.
 */
class AdSearchController extends Controller
{
    /**
     * Perform a search across ad titles, descriptions, or tags.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $keyword = $request->input('keyword', '');

        // Fetch ads based on keyword in title, description, or tags
        $ads = Ad::where('status', 'active')
                  ->where(function ($query) use ($keyword) {
                      $query->where('title', 'like', '%' . $keyword . '%')
                            ->orWhere('description', 'like', '%' . $keyword . '%')
                            ->orWhere('tags', 'like', '%' . $keyword . '%');
                  })
                  ->get();

        return response()->json(['ads' => $ads]);
    }
}

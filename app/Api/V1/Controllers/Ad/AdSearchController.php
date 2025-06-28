<?php

namespace App\Api\V1\Controllers\Ad;

use App\Http\Controllers\Controller;
use App\Traits\LogsActivity;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Class AdSearchController
 * Handles search operations for ads within the Adfox application.
 */
class AdSearchController extends Controller
{
    use LogsActivity;
    /**
     * Perform a search across ad titles, descriptions, or tags.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        return $this->executeWithLogging($request, 'search', function ($traceId) use ($request) {
            $keyword = $request->input('keyword', '');

            $this->logApiRequest($traceId, $request, null, [
                'endpoint_type' => 'search',
                'has_keyword' => !empty($keyword),
                'keyword_length' => strlen($keyword),
                'user_authenticated' => auth()->check()
            ]);

            try {
                // Validate and sanitize input
                if (strlen($keyword) > 100) {
                    $this->logWarning($traceId, 'search', 'Search keyword too long', [
                        'keyword_length' => strlen($keyword),
                        'max_allowed' => 100
                    ]);
                    
                    $response = response()->json([
                        'message' => 'Search keyword too long',
                        'ads' => []
                    ], 400);

                    $this->logApiRequest($traceId, $request, $response, [
                        'error' => 'keyword_too_long'
                    ]);

                    return $response;
                }

                // Log search attempt
                $this->logMethodStart('ad_search_query', $request, [
                    'search_keyword' => $keyword,
                    'search_length' => strlen($keyword)
                ]);

                // Fetch ads based on keyword in title, description, or tags
                $ads = Ad::where('status', 'active')
                          ->where(function ($query) use ($keyword) {
                              $query->where('title', 'like', '%' . $keyword . '%')
                                    ->orWhere('description', 'like', '%' . $keyword . '%')
                                    ->orWhere('tags', 'like', '%' . $keyword . '%');
                          })
                          ->get();

                $response = response()->json(['ads' => $ads]);

                $this->logApiRequest($traceId, $request, $response, [
                    'search_results_count' => $ads->count(),
                    'search_keyword' => $keyword,
                    'has_results' => $ads->count() > 0
                ]);

                $this->logMethodSuccess($traceId, 'ad_search_query', $ads, [
                    'results_count' => $ads->count(),
                    'search_term' => $keyword
                ]);

                return $response;

            } catch (\Exception $e) {
                $this->logMethodError($traceId, 'search', $e, [
                    'search_keyword' => $keyword
                ]);

                $response = response()->json([
                    'message' => 'Search failed',
                    'ads' => []
                ], 500);

                $this->logApiRequest($traceId, $request, $response, [
                    'error' => 'search_exception',
                    'error_message' => $e->getMessage()
                ]);

                return $response;
            }
        });
    }
}

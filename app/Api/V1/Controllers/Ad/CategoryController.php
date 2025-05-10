<?php

namespace App\Api\V1\Controllers\Ad;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

/**
 * Class CategoryController
 * Handles category-related operations for Adfox application.
 */
class CategoryController extends Controller
{
    /**
     * Display a listing of the main categories.
     *
     * This method fetches main categories along with their subcategories.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = $this->fetchMainCategories();
        return response()->json(['categories' => $categories]);
    }

    /**
     * Fetch the main categories with their subcategories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function fetchMainCategories()
    {
        return Category::with('subcategories')->whereNull('parent_id')->get();
    }
}

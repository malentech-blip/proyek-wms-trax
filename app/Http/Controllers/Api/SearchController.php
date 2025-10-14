<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AccurateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    public function searchMaterials(Request $request, AccurateService $accurate)
    {
        $searchTerm = $request->query('q', '');
        Log::info('Controller menerima permintaan pencarian: ' . $searchTerm); // LOG 4

        // PENYESUAIAN: Jangan lakukan pencarian jika term terlalu pendek atau kosong
        if (strlen($searchTerm) < 2) {
            return response()->json([]); // Kembalikan array kosong
        }

        // Anda juga bisa meminta service untuk membatasi hasil
        $materials = $accurate->searchMaterials($searchTerm); // Idealnya, service ini punya limit
        Log::info('Controller mengirimkan response JSON: ', $materials->all()); // LOG 5
        return response()->json($materials);
    }

    public function searchCustomers(Request $request, AccurateService $accurate)
    {
        $searchTerm = $request->query('q', '');

        // PENYESUAIAN: Terapkan juga di sini
        if (strlen($searchTerm) < 2) {
            return response()->json([]);
        }

        $customers = $accurate->searchCustomers($searchTerm);
        return response()->json($customers);
    }

    public function searchItems(Request $request, AccurateService $accurate)
{
    $searchTerm = $request->query('q', '');
    $items = $accurate->searchItems($searchTerm);
    return response()->json($items);
}

}
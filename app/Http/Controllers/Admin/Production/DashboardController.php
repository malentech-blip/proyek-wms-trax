<?php

namespace App\Http\Controllers\Admin\Production;

use App\Http\Controllers\Controller;
use App\Models\Admin\Production\FinishedGood;
use App\Models\Admin\Production\MaterialRequest;
use App\Models\Admin\Production\WipRecord;
use App\Services\AccurateService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  public function index(Request $request, AccurateService $accurate)
  {
    $materialRequestsRequested = MaterialRequest::where('status', 'Requested')->count();
    $wipActive = WipRecord::where('status', 'Running')->count();
    $finishedGoodsToday = FinishedGood::whereDate('created_at', date('Y-m-d'))->count();
    return view('admin.production.dashboard', compact("materialRequestsRequested", "wipActive", "finishedGoodsToday"));
  }
}

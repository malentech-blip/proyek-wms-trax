<?php

namespace App\Http\Controllers\Admin\Production;

use App\Http\Controllers\Controller;
use App\Models\Admin\Production\FinishedGood;
use App\Models\Admin\Production\MaterialRequest;
use App\Models\Admin\Production\WipRecord;
use App\Services\AccurateService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  public function index(Request $request)
  {
    // Stats
    $materialRequestsRequested = MaterialRequest::where('status', 'Requested')->count();
    $wipActive = WipRecord::where('status', 'Running')->count();
    $finishedGoodsToday = FinishedGood::whereDate('created_at', date('Y-m-d'))->count();

    // Filter period (default: today)
    $period = $request->get('period', 'today');

    // Get date range based on period
    $dateRange = $this->getDateRange($period);

    // Get production efficiency data
    $productionData = $this->getProductionEfficiencyData($dateRange['start'], $dateRange['end']);

    return view('admin.production.dashboard', compact(
      'materialRequestsRequested',
      'wipActive',
      'finishedGoodsToday',
      'productionData',
      'period'
    ));
  }

  /**
   * Get date range based on period filter
   */
  private function getDateRange($period)
  {
    $now = Carbon::now();

    switch ($period) {
      case 'today':
        return [
          'start' => $now->copy()->startOfDay(),
          'end' => $now->copy()->endOfDay(),
        ];

      case 'yesterday':
        return [
          'start' => $now->copy()->subDay()->startOfDay(),
          'end' => $now->copy()->subDay()->endOfDay(),
        ];

      case 'this_week':
        return [
          'start' => $now->copy()->startOfWeek(),
          'end' => $now->copy()->endOfWeek(),
        ];

      case 'last_week':
        return [
          'start' => $now->copy()->subWeek()->startOfWeek(),
          'end' => $now->copy()->subWeek()->endOfWeek(),
        ];

      case 'this_month':
        return [
          'start' => $now->copy()->startOfMonth(),
          'end' => $now->copy()->endOfMonth(),
        ];

      case 'last_month':
        return [
          'start' => $now->copy()->subMonth()->startOfMonth(),
          'end' => $now->copy()->subMonth()->endOfMonth(),
        ];

      case 'last_7_days':
        return [
          'start' => $now->copy()->subDays(6)->startOfDay(),
          'end' => $now->copy()->endOfDay(),
        ];

      case 'last_30_days':
        return [
          'start' => $now->copy()->subDays(29)->startOfDay(),
          'end' => $now->copy()->endOfDay(),
        ];

      default:
        return [
          'start' => $now->copy()->startOfDay(),
          'end' => $now->copy()->endOfDay(),
        ];
    }
  }

  /**
   * Get production efficiency data for charts
   */
  private function getProductionEfficiencyData($startDate, $endDate)
  {
    // Get finished WIP records within date range
    $wips = WipRecord::whereIn('status', ['Finished', 'Completed'])
      ->whereNotNull('finished_at')
      ->whereBetween('finished_at', [$startDate, $endDate])
      ->with('material_request')
      ->orderBy('finished_at', 'asc')
      ->get();

    // Transform data for charts
    $chartData = $wips->map(function ($wip) {
      $goodQty = $wip->produced_qty ?? 0;
      $rejectQty = $wip->rejected_qty ?? 0;
      $totalQty = $goodQty + $rejectQty;

      // Calculate efficiency (good qty / total qty)
      $efficiency = $totalQty > 0 ? round(($goodQty / $totalQty) * 100, 1) : 0;

      // Calculate reject rate
      $rejectRate = $totalQty > 0 ? round(($rejectQty / $totalQty) * 100, 1) : 0;

      // Calculate production time in hours
      $productionTimeHours = 0;
      if ($wip->started_at && $wip->finished_at) {
        $startTime = Carbon::parse($wip->started_at);
        $endTime = Carbon::parse($wip->finished_at);
        $productionTimeHours = $startTime->diffInMinutes($endTime) / 60;
      }

      // Calculate production speed (units per hour)
      $productionSpeed = ($productionTimeHours > 0 && $totalQty > 0)
        ? round($totalQty / $productionTimeHours, 0)
        : 0;

      // Get target from material request if exists
      $targetQty = $wip->material_request->target_qty ?? $totalQty;

      // Target achievement rate
      $achievementRate = $targetQty > 0
        ? round(($totalQty / $targetQty) * 100, 1)
        : 100;

      // Get product info from material request
      $productName = 'N/A';
      if ($wip->material_request) {
        $productName = $wip->material_request->product_name
          ?? $wip->material_request->item_name
          ?? 'N/A';
      }

      return [
        'batch' => $wip->wip_no ?? 'WIP-' . $wip->id,
        'product' => $productName,
        'good' => $goodQty,
        'reject' => $rejectQty,
        'total' => $totalQty,
        'efficiency' => $efficiency,
        'rejectRate' => $rejectRate,
        'time' => round($productionTimeHours, 2),
        'productionSpeed' => $productionSpeed,
        'target' => $targetQty,
        'achievementRate' => $achievementRate,
        'date' => $wip->finished_at ? Carbon::parse($wip->finished_at)->format('d/m/Y') : 'N/A',
        'startedAt' => $wip->started_at ? Carbon::parse($wip->started_at)->format('d/m/Y H:i') : 'N/A',
        'finishedAt' => $wip->finished_at ? Carbon::parse($wip->finished_at)->format('d/m/Y H:i') : 'N/A',
      ];
    });

    return $chartData;
  }

  /**
   * API endpoint to get chart data (for AJAX updates)
   */
  public function getChartData(Request $request)
  {
    $period = $request->get('period', 'today');
    $dateRange = $this->getDateRange($period);
    $productionData = $this->getProductionEfficiencyData($dateRange['start'], $dateRange['end']);

    return response()->json([
      'success' => true,
      'data' => $productionData,
      'period' => $period,
      'dateRange' => [
        'start' => $dateRange['start']->format('d/m/Y'),
        'end' => $dateRange['end']->format('d/m/Y'),
      ],
      'summary' => [
        'totalBatches' => $productionData->count(),
        'totalGood' => $productionData->sum('good'),
        'totalReject' => $productionData->sum('reject'),
        'avgEfficiency' => $productionData->avg('efficiency'),
      ]
    ]);
  }

  /**
   * Get real-time WIP status (optional - for additional monitoring)
   */
  public function getWipStatus()
  {
    $runningWips = WipRecord::where('result_status', 'Running')
      ->with('material_request')
      ->orderBy('started_at', 'desc')
      ->get()
      ->map(function ($wip) {
        $elapsedTime = 0;
        if ($wip->started_at) {
          $elapsedTime = Carbon::parse($wip->started_at)->diffInMinutes(now());
        }

        return [
          'wip_no' => $wip->wip_no,
          'mr_no' => $wip->material_request->mr_no ?? 'N/A',
          'started_at' => $wip->started_at ? Carbon::parse($wip->started_at)->format('d/m/Y H:i') : 'N/A',
          'elapsed_minutes' => $elapsedTime,
          'elapsed_formatted' => $this->formatMinutes($elapsedTime),
          'produced_qty' => $wip->produced_qty ?? 0,
          'rejected_qty' => $wip->rejected_qty ?? 0,
        ];
      });

    return response()->json([
      'success' => true,
      'data' => $runningWips
    ]);
  }

  /**
   * Format minutes to human readable time
   */
  private function formatMinutes($minutes)
  {
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;

    if ($hours > 0) {
      return "{$hours}h {$mins}m";
    }
    return "{$mins}m";
  }
}

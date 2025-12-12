<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Admin\Inventory\Inventory;
use App\Models\Admin\Inventory\StockAdjustment;
use App\Models\Admin\Inventory\StockMovement;
use App\Traits\LogsActivity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjusmentController extends Controller
{
    use LogsActivity;

    public function index(Request $request): View
    {
        $this->logActivity('View Stock Adjustment', 'Inventory');

        // Get stock adjustment statistics
        $stats = $this->getStockAdjustmentStats();

        // Get recent stock adjustments with differences
        $recentAdjustments = StockAdjustment::with(['item', 'location', 'adjustedBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get adjustments requiring approval
        $pendingApprovals = StockAdjustment::where('status', 'pending')
            ->where('requires_approval', true)
            ->with(['item', 'location', 'adjustedBy'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get items with significant differences (difference > 10 or > 20% of system quantity)
        $significantDifferences = StockAdjustment::where('status', 'approved')
            ->where(function ($query) {
                $query->whereRaw('ABS(difference) > 10')
                    ->orWhereRaw('ABS(difference) > (system_quantity * 0.2)');
            })
            ->with(['item', 'location', 'adjustedBy'])
            ->orderByRaw('ABS(difference) DESC')
            ->take(5)
            ->get();

        return view('admin.inventory.stock-adjustment.index', compact(
            'stats',
            'recentAdjustments',
            'pendingApprovals',
            'significantDifferences'
        ));
    }

    /**
     * Get stock adjustment statistics
     */
    private function getStockAdjustmentStats(): array
    {
        $totalAdjustments = StockAdjustment::count();
        $pendingApprovals = StockAdjustment::where('status', 'pending')->where('requires_approval', true)->count();
        $approvedAdjustments = StockAdjustment::where('status', 'approved')->count();
        $rejectedAdjustments = StockAdjustment::where('status', 'rejected')->count();

        // Calculate total difference impact
        $totalDifference = StockAdjustment::where('status', 'approved')->sum('difference');

        // Get adjustments by month (last 6 months)
        $monthlyStats = StockAdjustment::selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                COUNT(*) as total,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = "pending" AND requires_approval = 1 THEN 1 ELSE 0 END) as pending
            ')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Get most adjusted items
        $mostAdjustedItems = StockAdjustment::selectRaw('
                item_id,
                COUNT(*) as adjustment_count,
                SUM(ABS(difference)) as total_difference
            ')
            ->with('item')
            ->groupBy('item_id')
            ->orderBy('adjustment_count', 'desc')
            ->take(5)
            ->get();

        return [
            'total_adjustments' => $totalAdjustments,
            'pending_approvals' => $pendingApprovals,
            'approved_adjustments' => $approvedAdjustments,
            'rejected_adjustments' => $rejectedAdjustments,
            'total_difference' => $totalDifference,
            'monthly_stats' => $monthlyStats,
            'most_adjusted_items' => $mostAdjustedItems,
        ];
    }

    /**
     * Get stock adjustment data for API endpoints
     */
    public function getData(Request $request): JsonResponse
    {
        $query = StockAdjustment::with(['item', 'location', 'adjustedBy', 'approvedBy']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('requires_approval')) {
            $query->where('requires_approval', $request->boolean('requires_approval'));
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Date range filter
        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59',
            ]);
        }

        $adjustments = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        // Add difference formatting
        $adjustments->getCollection()->transform(function ($adjustment) {
            $adjustment->difference_display = $this->formatDifference($adjustment->difference);
            $adjustment->difference_class = $this->getDifferenceClass($adjustment->difference);

            return $adjustment;
        });

        return response()->json([
            'success' => true,
            'data' => $adjustments,
        ]);
    }

    /**
     * Format difference for display
     */
    private function formatDifference(int $difference): string
    {
        $sign = $difference > 0 ? '+' : '';

        return $sign . number_format($difference);
    }

    /**
     * Get CSS class for difference display
     */
    private function getDifferenceClass(int $difference): string
    {
        if ($difference > 0) {
            return 'text-green-600'; // Positive difference (stock gain)
        } elseif ($difference < 0) {
            return 'text-red-600'; // Negative difference (stock loss)
        } else {
            return 'text-gray-600'; // No difference
        }
    }

    /**
     * Approve a stock adjustment
     */
    public function approve(Request $request, StockAdjustment $adjustment): JsonResponse
    {
        $this->logActivity('Approve Stock Adjustment', 'Inventory', ['adjustment_id' => $adjustment->id]);

        try {
            DB::beginTransaction();

            // Update the adjustment
            $adjustment->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_notes' => $request->input('notes'),
            ]);

            // Update inventory if the adjustment affects stock
            if ($adjustment->difference !== 0) {
                $inventory = Inventory::where('item_id', $adjustment->item_id)
                    ->where('location_id', $adjustment->location_id)
                    ->first();

                if ($inventory) {
                    $newQuantity = $inventory->quantity + $adjustment->difference;
                    $inventory->update(['quantity' => $newQuantity]);

                    // Log stock movement
                    StockMovement::create([
                        'item_id' => $adjustment->item_id,
                        'location_id' => $adjustment->location_id,
                        'movement_type' => $adjustment->difference > 0 ? 'adjustment_in' : 'adjustment_out',
                        'quantity' => abs($adjustment->difference),
                        'previous_quantity' => $inventory->quantity - $adjustment->difference,
                        'new_quantity' => $newQuantity,
                        'reference_type' => 'stock_adjustment',
                        'reference_id' => $adjustment->id,
                        'notes' => 'Stock adjustment approval: ' . $adjustment->reason,
                        'created_by' => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock adjustment approved successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve stock adjustment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject a stock adjustment
     */
    public function reject(Request $request, StockAdjustment $adjustment): JsonResponse
    {
        $this->logActivity('Reject Stock Adjustment', 'Inventory', ['adjustment_id' => $adjustment->id]);

        try {
            $adjustment->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_notes' => $request->input('notes'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock adjustment rejected.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject stock adjustment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get pending adjustments for approval
     */
    public function pendingApprovals(Request $request): JsonResponse
    {
        $adjustments = StockAdjustment::where('status', 'pending')
            ->where('requires_approval', true)
            ->with(['item', 'location', 'adjustedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $adjustments,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin\Outbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Outbound\SalesOrder as LocalSalesOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\AccurateService;

class SalesOrderController extends Controller
{
  public function index(Request $request, AccurateService $accurate): View
  {
    // ==== 1. Ambil data Sales Order dari Accurate ====
    $accurateSalesOrders = $accurate->getSalesOrders($request);
    $numbers = $accurateSalesOrders
      ->pluck('number')
      ->filter()
      ->values()
      ->all();

    // ==== 2. Ambil data lokal berdasarkan so_number ====
    $localByNumber = LocalSalesOrder::query()
      ->with(['packingLists.deliveryOrders'])
      ->whereIn('so_number', $numbers)
      ->get()
      ->keyBy('so_number');

    // ==== 3. Sinkronisasi dasar (tanpa overwrite status) ====
    foreach ($accurateSalesOrders as $so) {
      $number = $so['number'] ?? null;
      if (!$number) continue;

      $local = $localByNumber->get($number);

      $data = [
        'so_number'   => $number,
        'customer_id' => $so['customer']['id'] ?? null,
        'sync_status' => $so['status'] ?? 'Open', // hanya simpan sync ke Accurate
      ];

      if ($local) {
        $local->update($data);
      } else {
        $localByNumber->put(
          $number,
          LocalSalesOrder::create($data)
        );
      }
    }

    // ==== 4. Tambahkan status lokal ke list tampilan ====
    $withStatuses = $accurateSalesOrders->map(function (array $so) use ($localByNumber) {

      $local = $localByNumber->get($so['number'] ?? '');

      // Default (tanpa override)
      $localStatus = $local->status ?? 'Pending';
      $syncStatus  = $local->sync_status ?? 'Open';

      return [
        ...$so,
        'localStatus' => $localStatus,
        'syncStatus'  => $syncStatus,
        'hasLocal'    => (bool) $local
      ];
    });

    // ==== 5. Render ====
    return view('admin.outbound.sales-orders.index', [
      'salesOrders' => $withStatuses,
      'filters' => [
        'search' => $request->get('search'),
        'start_date' => $request->get('start_date'),
        'end_date' => $request->get('end_date'),
      ],
    ]);
  }



  public function createPackingList(int $so_id, AccurateService $accurate): RedirectResponse
  {
    return redirect()
      ->route('admin.outbound.packing-lists.create', ['so_id' => $so_id]);
  }
}

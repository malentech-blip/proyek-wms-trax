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
    $accurateSalesOrders = $accurate->getSalesOrders($request);
    $numbers = $accurateSalesOrders->pluck('number')->filter()->values()->all();

    $localByNumber = LocalSalesOrder::query()
      ->with(['packingLists.deliveryOrders'])
      ->whereIn('so_number', $numbers)
      ->get()
      ->keyBy('so_number');


    foreach ($accurateSalesOrders as $so) {
      $number = $so['number'] ?? null;
      if (!$number) continue;

      $local = $localByNumber->get($number);
      $data = [
        'so_number'   => $number,
        'customer_id' => $so['customer']['id'] ?? null,
        'sync_status' => $so['status'] ?? 'Open', 
      ];

      if ($local) {
        $local->update($data);
      } else {
        $local = LocalSalesOrder::create($data);
        $localByNumber->put($number, $local);
      }
    }


    $withStatuses = $accurateSalesOrders->map(function (array $so) use ($localByNumber) {
      $local = $localByNumber->get($so['number'] ?? '');

      $localStatus = 'Pending';
      $syncStatus = $so['status'] ?? 'Open'; 

      if ($local) {
        $hasPacking = $local->packingLists->isNotEmpty();

        if ($hasPacking) {
          $isShipped = $local->packingLists
            ->flatMap
            ->deliveryOrders
            ->contains(fn($do) => ($do->status ?? '') === 'Delivered');
          $localStatus = $isShipped ? 'Shipped' : 'Packed';
        }

        // Update status lokal bila berubah
        if ($local->status !== $localStatus) {
          $local->update(['status' => $localStatus]);
        }

        // Sync status Accurate kalau berubah
        if ($local->sync_status !== $syncStatus) {
          $local->update(['sync_status' => $syncStatus]);
        }
      }
      $so['localStatus'] = $localStatus;  
      $so['syncStatus'] = $syncStatus;   
      $so['hasLocal'] = (bool) $local;

      return $so;
    });

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

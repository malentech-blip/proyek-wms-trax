<?php

use App\Http\Controllers\AccurateController;
use App\Http\Controllers\Admin\Inbound\DashboardController as InboundDashboardController;
use App\Http\Controllers\Admin\Inbound\LabelPrintController;
use App\Http\Controllers\Admin\Inbound\PurchaseOrderController;
// --- KUMPULKAN SEMUA 'USE' STATEMENT DI ATAS ---

// Controller Umum
use App\Http\Controllers\Admin\Inbound\PutawayController;
use App\Http\Controllers\Admin\Inbound\QualityCheckController;
use App\Http\Controllers\Admin\Inventory\DashboardController as InventoryDashboardController;
use App\Http\Controllers\Admin\Inventory\RawMaterialStorageController;
use App\Http\Controllers\Admin\Inventory\MoveStockController;
use App\Http\Controllers\Admin\Inventory\StockAdjusmentController;
// Controller Super Admin
use App\Http\Controllers\Admin\Inventory\RejectWarehouseController;
use App\Http\Controllers\Admin\Inventory\StockReportController;
use App\Http\Controllers\Admin\Production\DashboardController as ProductionDashboardController;
use App\Http\Controllers\Admin\Production\FinishedGoodsController;
use App\Http\Controllers\Admin\Production\MaterialRequestController;
use App\Http\Controllers\Admin\Production\PickingListsController;
use App\Http\Controllers\Admin\Production\RejectsProductionController;
use App\Http\Controllers\Admin\Production\WIPController;
use App\Http\Controllers\DatabaseSelectionController;
use App\Http\Controllers\ProfileController;
// Controller Admin Operasional
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SuperAdmin\CustomerController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\ItemMasterController;
use App\Http\Controllers\SuperAdmin\LabelTemplateController;
// Controller Admin Inventory
use App\Http\Controllers\SuperAdmin\LocationController;
use App\Http\Controllers\SuperAdmin\ReportCenterController;
use App\Http\Controllers\SuperAdmin\RolePermissionController;
use App\Http\Controllers\SuperAdmin\SupplierController;
// Controller Admin Production
use App\Http\Controllers\SuperAdmin\SystemLogController;
use App\Http\Controllers\SuperAdmin\UserController;
// Controller Admin Outbound
use App\Http\Controllers\Admin\Outbound\DashboardController as OutboundDashboardController;
use App\Http\Controllers\Admin\Outbound\DeliveryOrderController;
use App\Http\Controllers\Admin\Outbound\SalesOrderController;
use App\Http\Controllers\Admin\Outbound\PackingListController;
use App\Http\Controllers\Admin\Outbound\TransitInventoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- RUTE PUBLIK & AUTENTIKASI ---
Route::get('/', function () {
  return redirect()->route('login');
});

require __DIR__ . '/auth.php';

// --- RUTE SETELAH LOGIN (MEMERLUKAN AUTENTIKASI) ---
Route::middleware('auth')->group(function () {
  // Gerbang tol setelah login untuk mengarahkan ke dashboard yang benar
  Route::get('/redirect-after-login', function () {
    $user = Auth::user();

    if ($user->hasRole('Admin Inbound')) {
      return redirect()->route('admin.inbound.dashboard');
    } elseif ($user->hasRole('Admin Inventory')) {
      return redirect()->route('admin.inventory.dashboard');
    } elseif ($user->hasRole('Admin Production')) {
      return redirect()->route('admin.production.dashboard');
    } elseif ($user->hasRole('Admin Outbound')) {
      return redirect()->route('admin.outbound.dashboard');
    }
    // Tambahkan elseif untuk role lain di sini
    elseif ($user->hasRole('Super Admin')) {
      return redirect()->route('super-admin.dashboard');
    }

    // Fallback jika tidak ada role
    return redirect('/login');
  })->name('login.redirect');

  // Rute untuk koneksi Accurate
  Route::get('/select-database', [
    DatabaseSelectionController::class,
    'showSelection',
  ])->name('database.selection');
  Route::post('/select-database', [
    DatabaseSelectionController::class,
    'selectDatabase',
  ])->name('database.select');
  Route::post('/accurate/disconnect', [
    AccurateController::class,
    'disconnect',
  ])->name('accurate.disconnect');
  Route::get('/accurate/auth', [
    AccurateController::class,
    'redirectToAccurate',
  ])->name('accurate.auth');
  Route::get('/accurate/callback', [
    AccurateController::class,
    'handleCallback',
  ])->name('accurate.callback');

  // Rute Profil Pengguna (berlaku untuk semua role)
  Route::get('/profile', [ProfileController::class, 'edit'])->name(
    'profile.edit',
  );
  Route::patch('/profile', [ProfileController::class, 'update'])->name(
    'profile.update',
  );
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name(
    'profile.destroy',
  );
  Route::get('/settings', [SettingsController::class, 'index'])->name(
    'settings.index',
  );
  Route::get('/settings/accurate', [
    SettingsController::class,
    'accurateSettings',
  ])->name('settings.accurate');

  // =======================================================
  // ||    SEMUA RUTE YANG MEMERLUKAN DATABASE ACCURATE   ||
  // =======================================================
  Route::middleware('database.selected')->group(function () {
    // --- KELOMPOK RUTE SUPER ADMIN ---
    Route::prefix('super-admin')
      ->name('super-admin.')
      ->middleware('can:manage_master_data')
      ->group(function () {
        Route::get('/dashboard', [
          SuperAdminDashboardController::class,
          'index',
        ])->name('dashboard');

        // Master Data
        Route::prefix('master-data')
          ->name('master-data.')
          ->group(function () {
            Route::get('/items', [
              ItemMasterController::class,
              'index',
            ])->name('items.index');
            Route::get('/suppliers', [
              SupplierController::class,
              'index',
            ])->name('suppliers.index');
            Route::get('/customers', [
              CustomerController::class,
              'index',
            ])->name('customers.index');
            Route::get('/locations', [
              LocationController::class,
              'index',
            ])->name('locations.index');
          });

        // User Management
        Route::prefix('user-management')
          ->name('user-management.')
          ->group(function () {
            Route::get('/users', [
              UserController::class,
              'index',
            ])->name('users.index');
            Route::get('/roles', [
              RolePermissionController::class,
              'index',
            ])->name('roles.index');
            Route::get('/permissions', [
              RolePermissionController::class,
              'permissionsMatrix',
            ])->name('permissions.index');
          });

        // Rute Super Admin lainnya
        Route::get('/label-templates', [
          LabelTemplateController::class,
          'index',
        ])->name('label-templates.index');
        Route::get('/reports-center', [
          ReportCenterController::class,
          'index',
        ])->name('reports-center.index');
        Route::get('/system-logs', [
          SystemLogController::class,
          'index',
        ])->name('system-logs.index');
      });

    // --- KELOMPOK RUTE ADMIN OPERASIONAL ---
    Route::prefix('admin')
      ->name('admin.')
      ->group(function () {
        // Rute Inbound
        Route::prefix('inbound')
          ->name('inbound.')
          ->middleware('can:manage_inbound')
          ->group(function () {
            Route::get('/dashboard', [
              InboundDashboardController::class,
              'index',
            ])->name('dashboard');
            Route::get('/purchase-orders', [
              PurchaseOrderController::class,
              'index',
            ])->name('purchase-orders.index');
            Route::get('/purchase-orders/{poId}/receive', [
              PurchaseOrderController::class,
              'showReceiveForm',
            ])->name('purchase-orders.receive');
            Route::post('/purchase-orders/{poId}/receive', [
              PurchaseOrderController::class,
              'storeReceiveForm',
            ])->name('purchase-orders.receive.store');
            Route::get('/goods-receipt/{goodsReceipt}/qc', [
              QualityCheckController::class,
              'show',
            ])->name('quality-check.show');
            Route::get('/goods-receipt/{goodsReceipt}/putaway', [
              PutawayController::class,
              'show',
            ])->name('putaway.show');
            Route::get(
              '/goods-receipt/{goodsReceipt}/print-labels',
              [LabelPrintController::class, 'print'],
            )->name('putaway.print-labels');
          });

        // Rute untuk admin lain (Inventory, Production, Outbound) akan ditambahkan di sini

        // Rute Inventory
        Route::prefix('inventory')
          ->name('inventory.')
          ->middleware('can:manage_inventory')
          ->group(function () {
            Route::get('/dashboard', [
              InventoryDashboardController::class,
              'index',
            ])->name('dashboard');
            Route::post('/dashboard/scan-qr', [
              InventoryDashboardController::class,
              'scanQR',
            ])->name('dashboard.scan-qr');
            Route::get('/raw-materials', [
              RawMaterialStorageController::class,
              'index',
            ])->name('raw-materials');
            Route::get('/reject-warehouses', [
              RejectWarehouseController::class,
              'index',
            ])->name('reject-warehouses');
            Route::post('/reject-warehouses/{rejectProduction}/rework', [
              RejectWarehouseController::class,
              'rework',
            ])->name('reject-warehouses.rework');
            Route::post('/reject-warehouses/{rejectProduction}/scrap', [
              RejectWarehouseController::class,
              'scrap',
            ])->name('reject-warehouses.scrap');
            Route::get('/stock-reports', [
              StockReportController::class,
              'index',
            ])->name('stock-reports');
            Route::get('/stock-reports/export', [
              StockReportController::class,
              'export',
            ])->name('stock-reports.export');
            Route::get('/stock-adjustment', [
              StockAdjusmentController::class,
              'index',
            ])->name('stock-adjustment');
            Route::get('/move-stock', [
              MoveStockController::class,
              'index',
            ])->name('move-stock');
          });

        // Rute Production
        Route::prefix('production')
          ->name('production.')
          ->group(function () {
            // dashboard
            Route::get('/dashboard', [
              ProductionDashboardController::class,
              'index',
            ])->name('dashboard');
            Route::get('dashboard/chart-data', [ProductionDashboardController::class, 'getChartData'])
              ->name('dashboard.chart-data');

            // material request
            Route::get('/material-request', [
              MaterialRequestController::class,
              'index',
            ])->name('material-request.index');
            Route::get('/material-request/detail/{mr_id}', [
              MaterialRequestController::class,
              'detail',
            ])->name('material-request.detail');
            Route::post('/material-request/add-temp-item', [
              MaterialRequestController::class,
              'addTempItem',
            ])->name('material-request.add-tempt-item');
            Route::post('/material-request', [
              MaterialRequestController::class,
              'storeMR',
            ])->name('material-request.mr.store');
            Route::get('/material-request/list-material-request', [
              MaterialRequestController::class,
              'listMR',
            ])->name('material-request.mr');
            Route::put('/material-request/change-status', [
              MaterialRequestController::class,
              'changeStatus',
            ])->name('material-request.change-status');
            Route::post('/material-request/{mr_id}/complete', [
              MaterialRequestController::class,
              'complete',
            ])->name('material-request.complete');
            // --PICKING LIST---
            Route::get('/picking-list', [
              PickingListsController::class,
              'listPL',
            ])->name('picking-list.index');
            Route::get('/picking-list/detail/{mr_id}', [
              PickingListsController::class,
              'detail',
            ])->name('picking-list.detail');
            
            Route::post('/picking-list/scan', [
              PickingListsController::class,
              'scanItem',
            ])->name('picking-list.scan-item');

            Route::post('/picking-list/confirm-pick', [
              PickingListsController::class,
              'confirmPick',
            ])->name('picking-list.confirm-pick');
            // ---WORK IN PROGRESS---
            Route::get('/work-in-progress', [
              WIPController::class,
              'index',
            ])->name('wip.index');
            Route::get('/work-in-progress/detail/{mr_id}', [
              WIPController::class,
              'detail',
            ])->name('wip.detail');
            Route::post('/work-in-progress/create-wip', [
              WIPController::class,
              'storeWIP',
            ])->name('wip.store');
            Route::post('/work-in-progress/{id}/start', [
              WipController::class,
              'start',
            ])->name('wip.start');
            Route::post('/work-in-progress/{id}/pause', [
              WipController::class,
              'pause',
            ])->name('wip.pause');
            Route::post('/work-in-progress/{id}/resume', [
              WipController::class,
              'resume',
            ])->name('wip.resume');
            Route::post('/work-in-progress/{id}/finish', [
              WipController::class,
              'finish',
            ])->name('wip.finish');
            Route::post('/work-in-progress/{id}/change-quantity', [
              WipController::class,
              'changeQuantity',
            ])->name('wip.change-quantity');
            // finished goods
            Route::get('/finished-goods', [
              FinishedGoodsController::class,
              'index',
            ])->name('finished-goods.index');
            Route::get('/finished-goods/detail/{mr_id}', [
              FinishedGoodsController::class,
              'detail',
            ])->name('finished-goods.detail');
            Route::post('/finished-goods', [
              FinishedGoodsController::class,
              'storeFG',
            ])->name('finished-goods.store');
            Route::post('/finished-goods/{id}/store-to-inventory', [
              FinishedGoodsController::class,
              'storingInv',
            ])->name('finished-goods.storing');
            Route::get('/finished-goods/racks/by-location/{locationId}', [FinishedGoodsController::class, 'getRacksByLocation'])->name("finished-goods.racks.by-location");
            Route::get('/finished-goods/pallets/by-rack/{rackId}', [FinishedGoodsController::class, 'getPalletsByRack'])->name("finished-goods.pallets.by-rack");
            Route::get('/finished-goods/print-label/{labelId}', [FinishedGoodsController::class, 'printLabel'])->name("finished-goods.print-label");
            // rejects production
            Route::get('/rejects-production', [
              RejectsProductionController::class,
              'index',
            ])->name('rejects-production.index');
            Route::get('/rejects-production/detail/{mr_id}', [
              RejectsProductionController::class,
              'detail',
            ])->name('rejects-production.detail');
            Route::post('/rejects-production', [
              RejectsProductionController::class,
              'store',
            ])->name('rejects-production.store');
          });


        // OUTBOUND
        Route::prefix('outbound')
          ->name('outbound.')
          ->group(function () {
            Route::get('/dashboard', [
              OutboundDashboardController::class,
              'index',
            ])->name('dashboard');

            // --- SALES ORDERS ---
            Route::get('sales-orders', [
              SalesOrderController::class,
              'index',
            ])->name('sales-orders.index');
            Route::get('sales-orders/{so_id}/create-packing-list', [
              SalesOrderController::class,
              'createPackingList',
            ])->name('sales-orders.create-packing-list');

            // --- PACKING LIST ---
            // index (GET)
            Route::get('packing-lists', [
              PackingListController::class,
              'index',
            ])->name('packing-lists.index');
            // detail (GET)
            Route::get('packing-lists/detail/{packing_id}', [
              PackingListController::class,
              'detail',
            ])->name('packing-lists.detail');
            // get items (GET)
            Route::get('packing-lists/{packingList}/items', [
              PackingListController::class,
              'getItems'
            ])->name('packing-lists.getItems');
            // create (GET)
            Route::get('packing-lists/create', [
              PackingListController::class,
              'create',
            ])->name('packing-lists.create');
            // add temp item (POST)
            Route::post('packing-lists/add-temp-item', [
              PackingListController::class,
              'addTempItem',
            ])->name('packing-lists.add-temp-item');
            // validate qr (POST)
            Route::post('packing-lists/validate-qr', [
              PackingListController::class,
              'validateQr',
            ])->name('packing-lists.validate-qr');
            // store (POST)
            Route::post('packing-lists/store', [
              PackingListController::class,
              'store'
            ])->name('packing-lists.store');
            // transit packing list (POST)
            Route::post('packing-lists/{packingList}/transit', [
              PackingListController::class,
              'transit'
            ])->name('packing-lists.transit');

            // --- TRANSIT INVENTORY ---
            Route::get('transit-inventory', [
              TransitInventoryController::class,
              'index'
            ])->name('transit-inventory.index');
            Route::get('transit-inventory/detail/{packing_id}', [
              TransitInventoryController::class,
              'detail',
            ])->name('transit-inventory.detail');

            // --- DELIVERY ORDERS ---
            Route::resource('delivery-orders', DeliveryOrderController::class)->only(['index']);
            Route::post('delivery-orders/{do_id}/mark-delivered', [
              DeliveryOrderController::class,
              'markDelivered',
            ])->name('delivery-orders.mark-delivered');
            Route::get('delivery-orders/{do_id}/print-pdf', [
              DeliveryOrderController::class,
              'printPDF',
            ])->name('delivery-orders.print-pdf');
          });
      });
  });
});

Route::get('/accurate/auth', function (Request $request) {
  $request->session()->put('state', $state = Str::random(40));
  $clientId = env('ACCURATE_CLIENT_ID');
  $query = http_build_query([
    'client_id' => $clientId,
    'response_type' => 'code',
    'redirect_uri' => route('accurate.callback'),
    'scope' => 'item_view item_save customer_save customer_view sales_order_save job_order_save sales_order_view job_order_view roll_over_save purchase_order_view',
    'state' => $state,
  ]);

  return redirect(env('ACCURATE_API_URL') . '/oauth/authorize?' . $query);
})->name('accurate.auth');

Route::get('/accurate/callback', [
  AccurateController::class,
  'handleCallback',
])->name('accurate.callback');

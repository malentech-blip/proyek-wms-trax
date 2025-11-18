<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// --- KUMPULKAN SEMUA 'USE' STATEMENT DI ATAS ---

// Controller Umum
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccurateController;
use App\Http\Controllers\DatabaseSelectionController;
use App\Http\Controllers\SettingsController;

// Controller Super Admin
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\ItemMasterController;
use App\Http\Controllers\SuperAdmin\SupplierController;
use App\Http\Controllers\SuperAdmin\CustomerController;
use App\Http\Controllers\SuperAdmin\LocationController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\RolePermissionController;
use App\Http\Controllers\SuperAdmin\LabelTemplateController;
use App\Http\Controllers\SuperAdmin\ReportCenterController;
use App\Http\Controllers\SuperAdmin\SystemLogController;

// Controller Admin Operasional
use App\Http\Controllers\Admin\Inbound\DashboardController as InboundDashboardController;
use App\Http\Controllers\Admin\Inbound\PurchaseOrderController;
use App\Http\Controllers\Admin\Inbound\QualityCheckController;
use App\Http\Controllers\Admin\Inbound\PutawayController;
use App\Http\Controllers\Admin\Inbound\LabelPrintController;
use App\Http\Controllers\Admin\Inbound\RejectWarehouseController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- RUTE PUBLIK & AUTENTIKASI ---
Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';


// --- RUTE SETELAH LOGIN (MEMERLUKAN AUTENTIKASI) ---
Route::middleware('auth')->group(function () {

    // Gerbang tol setelah login untuk mengarahkan ke dashboard yang benar
    Route::get('/redirect-after-login', function () {
        $user = Auth::user();

        if ($user->hasRole('Admin Inbound')) {
            return redirect()->route('admin.inbound.dashboard');
        } 
        // Tambahkan elseif untuk role lain di sini
        elseif ($user->hasRole('Super Admin')) {
            return redirect()->route('super-admin.dashboard');
        }
        // Fallback jika tidak ada role
        return redirect('/login');
    })->name('login.redirect');

    // Rute untuk koneksi Accurate
    Route::get('/select-database', [DatabaseSelectionController::class, 'showSelection'])->name('database.selection');
    Route::post('/select-database', [DatabaseSelectionController::class, 'selectDatabase'])->name('database.select');
    Route::post('/accurate/disconnect', [AccurateController::class, 'disconnect'])->name('accurate.disconnect');
    Route::get('/accurate/auth', [AccurateController::class, 'redirectToAccurate'])->name('accurate.auth');
    Route::get('/accurate/callback', [AccurateController::class, 'handleCallback'])->name('accurate.callback');

    // Rute Profil Pengguna (berlaku untuk semua role)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/settings/accurate', [SettingsController::class, 'accurateSettings'])->name('settings.accurate');


    // =======================================================
    // ||    SEMUA RUTE YANG MEMERLUKAN DATABASE ACCURATE   ||
    // =======================================================
    Route::middleware('database.selected')->group(function() {

        // --- KELOMPOK RUTE SUPER ADMIN ---
        Route::prefix('super-admin')->name('super-admin.')->middleware('can:manage_master_data')->group(function () {
            
            Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

            // Master Data
            Route::prefix('master-data')->name('master-data.')->group(function () {
                Route::get('/items', [ItemMasterController::class, 'index'])->name('items.index');
                Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
                Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
                Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
            });

            // User Management
            Route::prefix('user-management')->name('user-management.')->group(function () {
                Route::get('/users', [UserController::class, 'index'])->name('users.index');
                Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
                Route::get('/permissions', [RolePermissionController::class, 'permissionsMatrix'])->name('permissions.index');
            });
            
            // Rute Super Admin lainnya
            Route::get('/label-templates', [LabelTemplateController::class, 'index'])->name('label-templates.index');
            Route::get('/reports-center', [ReportCenterController::class, 'index'])->name('reports-center.index');
            Route::get('/system-logs', [SystemLogController::class, 'index'])->name('system-logs.index');
        });


        // --- KELOMPOK RUTE ADMIN OPERASIONAL ---
        Route::prefix('admin')->name('admin.')->group(function() {

            // Rute Inbound
            Route::prefix('inbound')->name('inbound.')->middleware('can:manage_inbound')->group(function () {
                Route::get('/dashboard', [InboundDashboardController::class, 'index'])->name('dashboard');
                Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
                Route::get('/purchase-orders/{poId}/receive', [PurchaseOrderController::class, 'showReceiveForm'])->name('purchase-orders.receive');
                Route::post('/purchase-orders/{poId}/receive', [PurchaseOrderController::class, 'storeReceiveForm'])->name('purchase-orders.receive.store');
                Route::get('/goods-receipt/{goodsReceipt}/qc', [QualityCheckController::class, 'show'])->name('quality-check.show');
                Route::get('/goods-receipt/{goodsReceipt}/putaway', [PutawayController::class, 'show'])->name('putaway.show');
                Route::get('/goods-receipt/{goodsReceipt}/print-labels', [LabelPrintController::class, 'print'])->name('putaway.print-labels');
                Route::get('/reject-warehouse', [RejectWarehouseController::class, 'index'])->name('reject-warehouse.index');
            });
            
            // Rute untuk admin lain (Inventory, Production, Outbound) akan ditambahkan di sini
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
    return redirect(env('ACCURATE_API_URL') . '/oauth/authorize?'.$query);
})->name('accurate.auth');

Route::get('/accurate/callback', [AccurateController::class, 'handleCallback'])->name('accurate.callback');

<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoqController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ClientBillController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CostCodeController;
use App\Http\Controllers\DailyProgressReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoodsReceiptController;
use App\Http\Controllers\InventoryLedgerController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MaterialRequisitionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectExpenseController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\RateAnalysisController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TenantSwitchController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

// Public & Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Language Switch
Route::get('/locale/{lang}', [LocaleController::class, 'switch'])->name('locale.switch');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Tenant Switchers
    Route::post('/company/switch', [TenantSwitchController::class, 'switchCompany'])->name('company.switch');
    Route::post('/project/switch', [TenantSwitchController::class, 'switchProject'])->name('project.switch');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Projects Master, Branches, Users & Roles
    Route::patch('projects/{project}/progress', [ProjectController::class, 'updateProgress'])->name('projects.progress.update');
    Route::resource('projects', ProjectController::class);
    Route::resource('branches', BranchController::class);
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);

    // Module 3: Materials, Categories, Units, Suppliers, Cost Codes
    Route::resource('items', ItemController::class);
    Route::resource('item-categories', ItemCategoryController::class);
    Route::resource('units', UnitController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::get('cost-codes', [CostCodeController::class, 'index'])->name('cost-codes.index');
    Route::post('cost-codes', [CostCodeController::class, 'store'])->name('cost-codes.store');

    // Module 4: BOQ, Rate Analysis, Project Budgets
    Route::get('projects/{project}/boqs', [BoqController::class, 'index'])->name('projects.boqs.index');
    Route::post('projects/{project}/boqs', [BoqController::class, 'store'])->name('projects.boqs.store');
    Route::get('projects/{project}/boqs/{boq}', [BoqController::class, 'show'])->name('projects.boqs.show');
    Route::put('projects/{project}/boqs/{boq}', [BoqController::class, 'update'])->name('projects.boqs.update');
    Route::delete('projects/{project}/boqs/{boq}', [BoqController::class, 'destroy'])->name('projects.boqs.destroy');
    Route::post('projects/{project}/boqs/{boq}/unlock', [BoqController::class, 'unlock'])->name('projects.boqs.unlock');
    Route::post('projects/{project}/boqs/{boq}/items', [BoqController::class, 'storeItem'])->name('projects.boqs.items.store');
    Route::put('projects/{project}/boqs/{boq}/items/{item}', [BoqController::class, 'updateItem'])->name('projects.boqs.items.update');
    Route::delete('projects/{project}/boqs/{boq}/items/{item}', [BoqController::class, 'destroyItem'])->name('projects.boqs.items.destroy');
    Route::post('projects/{project}/boqs/{boq}/revision', [BoqController::class, 'createRevision'])->name('projects.boqs.revision');
    Route::post('projects/{project}/boqs/{boq}/approve', [BoqController::class, 'approve'])->name('projects.boqs.approve');

    // Rate Analysis
    Route::get('boq-items/{boqItem}/rate-analysis', [RateAnalysisController::class, 'show'])->name('boq-items.rate-analysis');
    Route::post('rate-analyses/{rateAnalysis}/components', [RateAnalysisController::class, 'storeComponent'])->name('rate-analyses.components.store');
    Route::post('rate-analyses/{rateAnalysis}/parameters', [RateAnalysisController::class, 'updateParameters'])->name('rate-analyses.parameters.update');

    // Project Cost Control Budget
    Route::get('projects/{project}/budget', [BudgetController::class, 'show'])->name('projects.budget.show');
    Route::post('budgets/{budget}/lines', [BudgetController::class, 'storeLine'])->name('budgets.lines.store');

    // Module 5: Material Requisitions (MR)
    Route::resource('material-requisitions', MaterialRequisitionController::class);
    Route::post('material-requisitions/{materialRequisition}/approve', [MaterialRequisitionController::class, 'approve'])->name('material-requisitions.approve');

    // Module 6: Purchase Orders (PO)
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::post('purchase-orders/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');

    // Module 7: Goods Receipt Notes (GRN), Warehouses & Inventory
    Route::resource('goods-receipts', GoodsReceiptController::class);
    Route::post('goods-receipts/{goodsReceipt}/approve', [GoodsReceiptController::class, 'approve'])->name('goods-receipts.approve');
    Route::resource('warehouses', WarehouseController::class);

    // Inventory Ledger & Stock Issues
    Route::get('inventory/ledger', [InventoryLedgerController::class, 'index'])->name('inventory.ledger');
    Route::get('inventory/issues/create', [InventoryLedgerController::class, 'createIssue'])->name('inventory.issues.create');
    Route::post('inventory/issues', [InventoryLedgerController::class, 'storeIssue'])->name('inventory.issues.store');

    // Module 8: Daily Progress Reports (DPR)
    Route::resource('daily-progress-reports', DailyProgressReportController::class)
        ->parameters(['daily-progress-reports' => 'dpr'])
        ->names('dpr');
    Route::post('daily-progress-reports/{dpr}/approve', [DailyProgressReportController::class, 'approve'])->name('dpr.approve');

    // Module 9: Measurement & Client Billing (RA Bills)
    Route::resource('client-bills', ClientBillController::class);
    Route::post('client-bills/{clientBill}/certify', [ClientBillController::class, 'certify'])->name('client-bills.certify');
    Route::post('client-bills/{clientBill}/mark-paid', [ClientBillController::class, 'markPaid'])->name('client-bills.mark-paid');

    // Module 10: Project Expenses & Site Cashbook
    Route::resource('project-expenses', ProjectExpenseController::class);

    // Companies & Multi-Tenancy Management
    Route::resource('companies', CompanyController::class);

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});

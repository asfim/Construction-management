<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request, TenantManager $tenantManager): View|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->authorize('audit.view');

        $companyId = $tenantManager->currentCompanyId();
        $isSuperAdmin = auth()->user()?->hasRole('Super Admin');

        $action = $request->string('action')->trim()->value();
        $userId = $request->input('user_id');
        $entityType = $request->string('entity_type')->trim()->value();
        $search = $request->string('search')->trim()->value();
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = AuditLog::query()
            ->when(! $isSuperAdmin && $companyId, function ($q) use ($companyId) {
                $q->where(function ($sub) use ($companyId) {
                    $sub->where('company_id', $companyId)->orWhereNull('company_id');
                });
            })
            ->when(! empty($action), function ($q) use ($action) {
                if ($action === 'auth') {
                    $q->whereIn('action', ['login', 'logout']);
                } else {
                    $q->where('action', $action);
                }
            })
            ->when(! empty($userId), fn ($q) => $q->where('user_id', $userId))
            ->when(! empty($entityType), fn ($q) => $q->where('auditable_type', 'like', '%' . $entityType . '%'))
            ->when(! empty($search), function ($q) use ($search) {
                $term = '%' . $search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('description', 'like', $term)
                        ->orWhere('user_name', 'like', $term)
                        ->orWhere('ip_address', 'like', $term)
                        ->orWhere('auditable_id', 'like', $term)
                        ->orWhere('auditable_type', 'like', $term);
                });
            })
            ->when(! empty($dateFrom), fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when(! empty($dateTo), fn ($q) => $q->whereDate('created_at', '<=', $dateTo));

        // CSV Export handling
        if ($request->query('export') === 'csv') {
            $exportLogs = (clone $query)->with(['user', 'project'])->latest('created_at')->limit(2000)->get();

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="audit_logs_' . date('Ymd_His') . '.csv"',
            ];

            return response()->stream(function () use ($exportLogs) {
                $file = fopen('php://output', 'w');
                // Output BOM for UTF-8 in Excel
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, ['Log ID', 'Timestamp', 'User', 'Action', 'Entity', 'Entity ID', 'Project', 'Description', 'IP Address']);

                foreach ($exportLogs as $row) {
                    fputcsv($file, [
                        $row->id,
                        $row->created_at?->format('Y-m-d H:i:s'),
                        $row->user_name ?? 'System',
                        strtoupper($row->action),
                        class_basename($row->auditable_type),
                        $row->auditable_id ?? 'N/A',
                        $row->project?->name ?? 'N/A',
                        $row->description,
                        $row->ip_address ?? '—',
                    ]);
                }
                fclose($file);
            }, 200, $headers);
        }

        // Stats calculation
        $statsBase = AuditLog::query()
            ->when(! $isSuperAdmin && $companyId, function ($q) use ($companyId) {
                $q->where(function ($sub) use ($companyId) {
                    $sub->where('company_id', $companyId)->orWhereNull('company_id');
                });
            });

        $stats = [
            'total' => (clone $statsBase)->count(),
            'created' => (clone $statsBase)->where('action', 'created')->count(),
            'updated' => (clone $statsBase)->where('action', 'updated')->count(),
            'deleted' => (clone $statsBase)->where('action', 'deleted')->count(),
            'auth' => (clone $statsBase)->whereIn('action', ['login', 'logout'])->count(),
        ];

        $logs = (clone $query)
            ->with(['user', 'company', 'project'])
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        $users = User::select('id', 'name', 'email', 'designation')
            ->orderBy('name')
            ->get();

        $availableEntities = [
            'Project' => 'Projects (প্রকল্প)',
            'MaterialRequisition' => 'Material Requisitions (MR)',
            'PurchaseOrder' => 'Purchase Orders (PO)',
            'GoodsReceipt' => 'Goods Receipts (GRN)',
            'StockIssue' => 'Stock Issues (স্টক ইস্যু)',
            'ClientBill' => 'Client Bills (RA Bills)',
            'DailyProgressReport' => 'Daily Progress Reports (DPR)',
            'ProjectExpense' => 'Project Expenses (খরচ)',
            'BoqItem' => 'BOQ Items (বিওকিউ)',
            'Budget' => 'Budgets (বাজেট)',
            'Item' => 'Material Catalog (উপকরণ)',
            'Supplier' => 'Suppliers (সরবরাহকারী)',
            'Company' => 'Company & Profile (কোম্পানি)',
            'User' => 'Users & Staff (ব্যবহারকারী)',
            'Warehouse' => 'Warehouses (সাইট স্টোর)',
            'Branch' => 'Branches (ব্রাঞ্চ)',
        ];

        return view('audit-logs.index', compact(
            'logs',
            'users',
            'action',
            'userId',
            'entityType',
            'search',
            'dateFrom',
            'dateTo',
            'stats',
            'availableEntities'
        ));
    }
}

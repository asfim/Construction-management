<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('audit.view');

        $companyId = $tenantManager->currentCompanyId();
        $action = $request->string('action')->trim();
        $userId = $request->input('user_id');

        $logs = AuditLog::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($action->isNotEmpty(), fn ($q) => $q->where('action', $action->value()))
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->with(['user', 'company', 'project'])
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        $users = User::where('is_active', true)->get();

        return view('audit-logs.index', compact('logs', 'users', 'action', 'userId'));
    }
}

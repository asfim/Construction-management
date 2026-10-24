<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $this->authorize('company.view');

        if (Company::count() === 1 && ! $request->has('view_all')) {
            $company = Company::first();
            return redirect()->route('companies.edit', $company);
        }

        $companies = Company::withCount(['branches', 'projects', 'users'])
            ->latest()
            ->paginate(10);

        return view('companies.index', compact('companies'));
    }

    public function create(): View|RedirectResponse
    {
        $this->authorize('company.manage');

        if (Company::count() >= 1) {
            $company = Company::first();
            return redirect()->route('companies.edit', $company)
                ->with('info', 'This system is configured for a single company. You can update your company profile and settings below.');
        }

        return view('companies.create');
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        if (Company::count() >= 1) {
            $company = Company::first();
            return redirect()->route('companies.edit', $company)
                ->with('warning', 'Only one company can be configured in this system. You can update your existing company settings below.');
        }

        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $company = Company::create($validated);

        return redirect()->route('companies.show', $company)
            ->with('success', __('erp.messages.created_successfully'));
    }

    public function show(Company $company): View
    {
        $this->authorize('company.view');

        $company->load(['branches', 'projects']);

        return view('companies.show', compact('company'));
    }

    public function edit(Company $company): View
    {
        $this->authorize('company.manage');

        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        $this->authorize('company.manage');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('companies', 'code')->ignore($company->id)],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'trade_license' => ['nullable', 'string', 'max:100'],
            'tin' => ['nullable', 'string', 'max:100'],
            'bin' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'division' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'currency' => ['required', 'string', 'max:10'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'fiscal_year_start' => ['required', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
        ]);

        if (! empty($validated['remove_logo'])) {
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = null;
        }

        if ($request->hasFile('logo')) {
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        unset($validated['remove_logo']);
        $company->update($validated);

        return redirect()->route('companies.show', $company)
            ->with('success', __('erp.messages.updated_successfully'));
    }
}

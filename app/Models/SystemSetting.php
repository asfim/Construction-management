<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use App\Services\TenantManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'group',
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get a setting value by key.
     */
    public static function getSetting(string $key, mixed $default = null, ?int $companyId = null): mixed
    {
        $companyId = $companyId ?? app(TenantManager::class)->currentCompanyId();

        $query = static::withoutGlobalScopes()->where('key', $key);
        if ($companyId) {
            $query->where(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)->orWhereNull('company_id');
            })->orderByRaw('company_id IS NULL ASC'); // Company-specific first
        } else {
            $query->whereNull('company_id');
        }

        $setting = $query->first();
        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $setting->value,
            'decimal' => (float) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Set a setting value by key.
     */
    public static function setSetting(string $key, mixed $value, string $group = 'general', ?int $companyId = null, string $type = 'string'): self
    {
        $companyId = $companyId ?? app(TenantManager::class)->currentCompanyId();

        $stringValue = match ($type) {
            'json' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };

        return static::withoutGlobalScopes()->updateOrCreate(
            ['company_id' => $companyId, 'group' => $group, 'key' => $key],
            ['value' => $stringValue, 'type' => $type]
        );
    }
}

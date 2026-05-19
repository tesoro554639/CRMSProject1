<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemConfig extends Model
{
    protected $fillable = [
        'app_name',
        'currency_code',
        'company_email',
        'company_phone',
        'company_address',
        'default_lead_status',
        'default_lead_priority',
        'reset_link_expiry',
    ];

    protected function casts(): array
    {
        return [
            'reset_link_expiry' => 'integer',
        ];
    }

    public static function getConfig(): self
    {
        return static::first() ?? static::createDefault();
    }

    public static function createDefault(): self
    {
        return static::create([
            'app_name' => 'ClientPulse',
            'currency_code' => '$',
            'default_lead_status' => 'new',
            'default_lead_priority' => 'medium',
            'reset_link_expiry' => 60,
        ]);
    }
}

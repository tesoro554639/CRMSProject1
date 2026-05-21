<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'lead_id',
        'name',
        'email',
        'phone',
        'source',
        'status',
        'priority',
        'expected_value',
        'notes',
        'assigned_user_id',
        'converted_to_customer_id',
        'converted_at',
        'lost_reason',
        'lost_category',
        'lost_at',
    ];

    protected function casts(): array
    {
        return [
            'expected_value' => 'decimal:2',
            'converted_at' => 'datetime',
            'lost_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Lead $lead) {
            if (empty($lead->lead_id)) {
                $lead->lead_id = static::generateLeadId();
            }
        });
    }

    public static function generateLeadId(): string
    {
        $prefix = 'LEAD-';
        $lastLead = static::withTrashed()->orderBy('id', 'desc')->first();

        if ($lastLead && preg_match('/^LEAD-(\d+)$/', $lastLead->lead_id, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        $maxAttempts = 10;
        for ($i = 0; $i < $maxAttempts; $i++) {
            $newId = $prefix.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            if (! static::withTrashed()->where('lead_id', $newId)->exists()) {
                return $newId;
            }
            $nextNumber++;
        }

        return $newId;
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function convertedToCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'converted_to_customer_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public function isWon(): bool
    {
        return $this->status === 'won';
    }

    public function isLost(): bool
    {
        return $this->status === 'lost';
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['new', 'contacted', 'qualified', 'proposal_sent', 'negotiation']);
    }

    public function canBeConverted(): bool
    {
        return $this->status === 'won' && is_null($this->converted_to_customer_id);
    }
}

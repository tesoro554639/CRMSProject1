<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'lead_id',
        'user_id',
        'activity_type',
        'description',
        'activity_date',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function getRelatedEntity(): Model|string|null
    {
        if ($this->customer_id) {
            return $this->customer;
        }
        if ($this->lead_id) {
            return $this->lead;
        }

        return null;
    }

    public function getRelatedType(): ?string
    {
        if ($this->customer_id) {
            return 'customer';
        }
        if ($this->lead_id) {
            return 'lead';
        }

        return null;
    }
}

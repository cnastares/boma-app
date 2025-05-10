<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\InvoiceObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


#[ObservedBy([InvoiceObserver::class])]
class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'subscription_id',
        'invoice_id',
        'currency',
        'status',
        'amount_due',
        'amount_paid',
        'amount_remaining',
        'invoice_date',
        'due_date',
    ];

    protected $casts = [
        'invoice_date' => 'datetime',
        'due_date' => 'datetime',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}

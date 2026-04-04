<?php

namespace App\Models\dashboard;

use App\Models\dashboard\Invoice;
use App\Models\dashboard\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceArchive extends Model
{
    protected $fillable = [
        'invoice_id',
        'archived_by',
        'archive_date',
        'reason',
        'notes',
        'status'
    ];

    protected $casts = [
        'archive_date' => 'date',
        'status' => 'string'
    ];

    /**
     * Get the invoice that was archived.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the admin who archived the invoice.
     */
    public function archivedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'archived_by');
    }

    /**
     * Scope to get only archived invoices.
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Scope to get only restored invoices.
     */
    public function scopeRestored($query)
    {
        return $query->where('status', 'restored');
    }
}

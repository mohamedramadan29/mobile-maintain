<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\dashboard\Invoice;
use App\Models\dashboard\Admin;
use App\Models\dashboard\InvoiceArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceArchiveController extends Controller
{
    /**
     * Display a listing of archived invoices.
     */
    public function index(Request $request)
    {
        $query = InvoiceArchive::with(['invoice', 'archivedBy'])
            ->archived()
            ->latest('archive_date');

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('invoice', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }

        // Filter by date
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('archive_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('archive_date', '<=', $request->date_to);
        }

        $archives = $query->paginate(50);

        return view('dashboard.invoices.archives.index', compact('archives'));
    }

    /**
     * Archive an invoice directly.
     */
    public function archive($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Check if already archived
        if ($invoice->archives()->exists()) {
            return back()->with('error', 'الفاتورة مؤرشفة بالفعل');
        }

        InvoiceArchive::create([
            'invoice_id' => $invoice->id,
            'archived_by' => Auth::guard('admin')->id(),
            'archive_date' => now(),
            'reason' => 'أرشفة يدوية',
            'notes' => 'تم الأرشفة مباشرة',
            'status' => 'archived'
        ]);

        return back()->with('success', 'تم أرشفة الفاتورة بنجاح');
    }

    /**
     * Restore an archived invoice.
     */
    public function restore($id)
    {
        $archive = InvoiceArchive::findOrFail($id);

        $archive->update([
            'status' => 'restored'
        ]);

        return back()->with('success', 'تم استرجاع الفاتورة من الأرشيف بنجاح');
    }

    /**
     * Remove an archived invoice permanently.
     */
    public function destroy($id)
    {
        $archive = InvoiceArchive::findOrFail($id);

        // Optionally delete the actual invoice too
        // $archive->invoice->delete();

        $archive->delete();

        return back()->with('success', 'تم حذف الفاتورة من الأرشيف بنجاح');
    }

    /**
     * Bulk archive invoices directly.
     */
    public function bulkArchive(Request $request)
    {
        // التحقق من الإدخال
        $request->validate([
            'invoice_ids' => 'required',
        ]);

        // الحصول على معرفات الفواتير
        $invoiceIds = explode(',', $request->input('invoice_ids'));

        $archivedCount = 0;
        foreach ($invoiceIds as $invoiceId) {
            $invoice = Invoice::find($invoiceId);

            if ($invoice && !$invoice->archives()->exists()) {
                InvoiceArchive::create([
                    'invoice_id' => $invoice->id,
                    'archived_by' => Auth::guard('admin')->id(),
                    'archive_date' => now(),
                    'reason' => 'أرشفة مجمعة',
                    'notes' => 'تم الأرشفة ضمن عملية مجمعة',
                    'status' => 'archived'
                ]);
                $archivedCount++;
            }
        }

        return redirect()->route('dashboard.invoices.index')->with('Success_message', "تم أرشفة {$archivedCount} فاتورة بنجاح.");
    }
}

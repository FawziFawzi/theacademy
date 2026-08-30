<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\Contracts\InvoicePdfServiceInterface;
use App\Services\Contracts\InvoiceServiceInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceServiceInterface $invoices,
        private readonly InvoicePdfServiceInterface $pdfService,
    ) {}

    public function index(): View
    {
        return view('invoices.index', ['invoices' => $this->invoices->all()]);
    }

    public function show(int $id): View
    {
        $invoice = $this->invoices->find($id);
        abort_unless($invoice instanceof Invoice, 404);

        return view('invoices.show', ['invoice' => $invoice]);
    }

    public function download(int $id): StreamedResponse|Response
    {
        $invoice = $this->invoices->find($id);
        abort_unless($invoice instanceof Invoice, 404);

        // Ensure PDF exists — generate on-demand if missing and transaction is completed
        if (! $invoice->pdf_path || ! Storage::disk('local')->exists($invoice->pdf_path)) {
            $invoice->loadMissing('transaction');
            if ($invoice->transaction && $invoice->transaction->status->value === 'completed') {
                $this->pdfService->configure()->generate($invoice);
                $invoice->refresh();
            }
        }

        abort_unless($invoice->pdf_path && Storage::disk('local')->exists($invoice->pdf_path), 404, 'PDF not available');

        return Storage::disk('local')->download($invoice->pdf_path, 'invoice-'.$invoice->id.'.pdf');
    }
}

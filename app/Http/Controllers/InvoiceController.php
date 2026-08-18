<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\Contracts\InvoiceServiceInterface;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceServiceInterface $invoices) {}

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
}

<?php

namespace App\Services;

use App\Models\Invoice;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Services\Contracts\InvoicePdfServiceInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoicePdfService implements InvoicePdfServiceInterface
{
    private string $paper = 'a4';

    private string $orientation = 'portrait';

    /** @var array<string, mixed> */
    private array $options = [];

    private bool $configured = false;

    public function __construct(private readonly InvoiceRepositoryInterface $invoices) {}

    public function configure(string $paper = 'a4', string $orientation = 'portrait', array $options = []): static
    {
        $this->paper = $paper;
        $this->orientation = $orientation;
        $this->options = $options;
        $this->configured = true;

        return $this;
    }

    public function fillPaper(Invoice $invoice): string
    {
        $invoice->loadMissing([
            'transaction.subscription.plan.organization',
            'transaction.subscription.user',
        ]);

        $transaction = $invoice->transaction;
        $subscription = $transaction?->subscription;
        $plan = $subscription?->plan;
        $organization = $plan?->organization;
        $user = $subscription?->user;

        return view('invoices.pdf', [
            'invoice' => $invoice,
            'transaction' => $transaction,
            'subscription' => $subscription,
            'plan' => $plan,
            'organization' => $organization,
            'user' => $user,
        ])->render();
    }

    public function renderBinary(Invoice $invoice): string
    {
        if (! $this->configured) {
            $this->configure();
        }

        $html = $this->fillPaper($invoice);

        $pdf = Pdf::loadHtml($html);

        if ($this->options !== []) {
            $pdf->setOptions($this->options);
        }

        $pdf->setPaper($this->paper, $this->orientation);

        return $pdf->output();
    }

    public function generate(Invoice $invoice): Invoice
    {
        $binary = $this->renderBinary($invoice);

        $path = 'invoices/invoice-'.$invoice->id.'.pdf';

        Storage::disk('local')->put($path, $binary);

        // Persist path if changed
        if ($invoice->pdf_path !== $path) {
            $invoice->pdf_path = $path;
            $invoice->save();
        }

        return $invoice->refresh();
    }
}

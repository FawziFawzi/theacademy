<?php

namespace App\Services\Contracts;

use App\Models\Invoice;

interface InvoicePdfServiceInterface
{
    /**
     * Setup dompdf configuration (paper, orientation, options).
     * Must be called before fill()/generate().
     */
    public function configure(string $paper = 'a4', string $orientation = 'portrait', array $options = []): static;

    /**
     * Fill the paper with invoice data and render HTML.
     * Returns the filled HTML string (for testing / preview).
     */
    public function fillPaper(Invoice $invoice): string;

    /**
     * Generate the PDF for the given invoice, store it and update pdf_path.
     * Internally ensures configure() has been called.
     */
    public function generate(Invoice $invoice): Invoice;

    /**
     * Get raw PDF binary for the invoice (without persisting).
     */
    public function renderBinary(Invoice $invoice): string;
}

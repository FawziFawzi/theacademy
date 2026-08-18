<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\Contracts\TransactionServiceInterface;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function __construct(private readonly TransactionServiceInterface $transactions) {}

    public function index(): View
    {
        return view('transactions.index', ['transactions' => $this->transactions->all()]);
    }

    public function show(int $id): View
    {
        $transaction = $this->transactions->find($id);
        abort_unless($transaction instanceof Transaction, 404);

        return view('transactions.show', ['transaction' => $transaction]);
    }
}

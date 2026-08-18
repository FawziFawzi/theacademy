<?php

namespace App\Http\Controllers;

use App\Services\Contracts\AuditLogServiceInterface;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function __construct(private readonly AuditLogServiceInterface $auditLogs) {}

    public function index(): View
    {
        return view('audit-logs.index', ['auditLogs' => $this->auditLogs->all()]);
    }
}

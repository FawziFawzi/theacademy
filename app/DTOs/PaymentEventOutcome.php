<?php

namespace App\DTOs;

enum PaymentEventOutcome: string
{
    case Completed = 'completed';
    case Failed = 'failed';
    case Pending = 'pending';
}

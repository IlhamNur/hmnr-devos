<?php

namespace App\Traits;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;

trait RecordsActivity
{
    protected function recordActivity(string $type, Model $subject, string $description): void
    {
        Activity::record($type, $subject, $description);
    }
}


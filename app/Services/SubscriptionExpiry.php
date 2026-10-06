<?php

namespace App\Services;

use App\Models\Package;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class SubscriptionExpiry
{
    public function next(int $userId, Package $package): string
    {
        $duration = (int) $package->time;
        $unit = strtolower($package->type);
        if ($duration < 1 || $duration > 120 || !in_array($unit, ['day', 'week', 'month', 'year'], true)) {
            throw ValidationException::withMessages(['package_id' => 'Package duration is unavailable.']);
        }
        $base = now();
        $previous = Transaction::where('user_id', $userId)->where('status', 1)
            ->where('expiry_date', '>', $base->format('Y-m-d H:i'))->max('expiry_date');
        if ($previous) $base = Carbon::parse($previous);
        return (match ($unit) {
            'day' => $base->addDays($duration),
            'week' => $base->addWeeks($duration),
            'month' => $base->addMonthsNoOverflow($duration),
            'year' => $base->addYearsNoOverflow($duration),
        })->format('Y-m-d H:i');
    }
}

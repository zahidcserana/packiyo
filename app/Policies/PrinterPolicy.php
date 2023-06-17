<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Printer;
use App\Models\PrintJob;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrinterPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Printer $printer) {
        return $user->isAdmin() || $printer->customer->hasUser($user->id);
    }

    public function viewAny(User $user) {
        return true;
    }

    public function jobs(User $user, Printer $printer) {
        return $user->isAdmin() || $printer->customer->hasUser($user->id);
    }

    public function disable(User $user, Printer $printer) {
        return $user->isAdmin() || $printer->customer->hasUser($user->id);
    }

    public function enable(User $user, Printer $printer) {
        return $user->isAdmin() || $printer->customer->hasUser($user->id);
    }

    public function jobRepeat(User $user, PrintJob $printJob) {
        return $user->isAdmin() || $printJob->printer->customer->hasUser($user->id);
    }
}

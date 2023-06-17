<?php

namespace App\Observers;

use App\Models\EasypostCredential;

class EasypostCredentialObserver
{
    /**
     * Handle the EasypostCredential "saved" event.
     *
     * @param EasypostCredential $easypostCredential
     * @return void
     */
    public function saved(EasypostCredential $easypostCredential)
    {
        try {
            app('easypostShipping')->getCarriers($easypostCredential);
        } catch (\Exception $exception) {

        }
    }
}

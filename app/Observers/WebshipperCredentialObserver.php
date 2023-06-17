<?php

namespace App\Observers;

use App\Models\WebshipperCredential;

class WebshipperCredentialObserver
{
    /**
     * Handle the WebshipperCredential "saved" event.
     *
     * @param WebshipperCredential $webshipperCredential
     * @return void
     */
    public function saved(WebshipperCredential $webshipperCredential)
    {
        try {
            app('webshipperShipping')->getCarriers($webshipperCredential);
        } catch (\Exception $exception) {

        }
    }
}

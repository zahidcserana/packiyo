<?php

namespace App\Listeners;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;
use Spatie\WebhookServer\Events\FinalWebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallEvent;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

class WebhookCallEventSubscriber
{
    public function logWebhookCall(WebhookCallEvent $event)
    {
        Log::channel('webhooks')->info(get_class($event), [
            'uuid' => $event->uuid,
            'attempt' => $event->attempt,
            'webhook_url' => $event->webhookUrl,
            'payload' => $event->payload,
            'status_code' => $event->response ? $event->response->getStatusCode() : null,
            'transfer_time' => $event->transferStats ? $event->transferStats->getTransferTime() : null,
            'error_type' => $event->errorType,
            'error_message' => $event->errorMessage
        ]);
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            [
                WebhookCallSucceededEvent::class,
                WebhookCallFailedEvent::class,
                FinalWebhookCallFailedEvent::class
            ],
            [WebhookCallEventSubscriber::class, 'logWebhookCall']
        );
    }
}

<?php

namespace App\Components;

use Carbon\Carbon;
use App\Models\{ContactInformation, Currency, Customer, Location, Return_, Tag, Webhook};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\WebhookServer\WebhookCall;
use Venturecraft\Revisionable\Revision;
use Webpatser\Countries\Countries;

class BaseComponent
{
    protected function batchWebhook($collections, $objectType, $resourceCollection, $operation)
    {
        $customerWiseItems = [];

        foreach ($collections as $key => $item) {
            $customerId = $this->getCustomerId($item, $objectType, $operation);

            $customerWiseItems[$customerId][] = $item;
        }

        foreach ($customerWiseItems as $customerId => $items) {
            $collections = new Collection($items);

            $this->webhook(new $resourceCollection($collections), $objectType, $operation, $customerId);
        }
    }

    protected function webhook($response, $objectType, $operation, $customerId)
    {
        $webhooks = Webhook::where('object_type', $objectType)->where('operation', $operation)->where('customer_id', $customerId)->get();

        foreach ($webhooks as $webhook) {
            $url = $webhook->url;

            WebhookCall::create()
                ->url($url)
                ->payload(['payload' => $response])
                ->useSecret($webhook->secret_key)
                ->dispatch();
        }
    }

    protected function getCustomerId($item, $objectType, $operation)
    {
        if ($operation == Webhook::OPERATION_TYPE_DESTROY) {
            return $item['customer_id'];
        }

        if (Location::class == $objectType) {
            $customerId = $item->warehouse->customer_id;
        } elseif (Return_::class == $objectType) {
            $customerId = $item->order->customer_id;
        } else {
            $customerId = $item->customer_id;
        }

        return $customerId;
    }

    public function history($object)
    {
        $revisionable_type = get_class($object);
        $revisionable_id = $object->id;

        $revisions = Revision::where('revisionable_type', $revisionable_type)->where('revisionable_id', $revisionable_id)->get();

        return $revisions;
    }

    public function createContactInformation($data, $object)
    {
        $contact = new ContactInformation();

        $contact->name = Arr::get($data, 'name');
        $contact->company_name = Arr::get($data, 'company_name');
        $contact->company_number = Arr::get($data, 'company_number');
        $contact->address = Arr::get($data, 'address');
        $contact->address2 = Arr::get($data, 'address2');
        $contact->zip = Arr::get($data, 'zip');
        $contact->city = Arr::get($data, 'city');
        $contact->state = Arr::get($data, 'state');
        if (($countryId = Arr::get($data, 'country_id')) == null) {
            $country = Countries::where('iso_3166_2', Arr::get($data, 'country_code'))->first();

            if ($country) {
                $countryId = $country->id;
            }
        }
        $contact->country_id = $countryId;
        $contact->email = Arr::get($data, 'email');
        $contact->phone = Arr::get($data, 'phone');
        $contact->object()->associate($object);
        $contact->save();

        return $contact;
    }

    public function updateTags($tags, Model $model, $replace = false)
    {
        if (request()->is('api/*')) {
            $replace = false;
        }

        $selectedTags = [];

        if (!empty($tags)) {
            foreach($tags as $tag) {
                $tag = trim($tag);

                if (empty($tag)) {
                    continue;
                }

                $findTag = Tag::firstOrCreate([
                    'name' => $tag,
                    'customer_id' => $model->customer_id ?? $model->warehouse->customer_id ?? $model->shippingCarrier->customer_id
                ]);

                $selectedTags[] = $findTag->id;
            }
        }

        $this->syncTags($model, $selectedTags, $replace);

        $this->createTagRevision($model, $tags);
    }

    public function bulkUpdateTags(array $tags, $ids, $modelClass, $replace = false): void
    {
        if (request()->is('api/*')) {
            $replace = false;
        }

        try {
            foreach ($ids as $id) {
                $selectedTags = [];
                $model = $modelClass::find($id);

                foreach ($tags as $tag) {
                    $tag = trim($tag);

                    if (empty($tag)) {
                        continue;
                    }

                    $tagModel = Tag::firstOrCreate([
                        'name' => $tag,
                        'customer_id' => $model->customer_id ?? $model->warehouse->customer_id
                    ]);

                    $selectedTags[] = $tagModel->id;
                }

                $this->syncTags($model, $selectedTags, $replace);

                $this->createTagRevision($model, $tags);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    private function createTagRevision($obj, $tags): void
    {
        if (!is_object($obj)) {
            return;
        }

        if (is_array($tags)) {
            $tags = implode(', ', $tags);
        }

        $revisions = [
            [
                'revisionable_type' => get_class($obj),
                'revisionable_id' => $obj->getKey(),
                'key' => 'tag',
                'old_value' => null,
                'new_value' => $tags,
                'user_id' => auth()->user()->id ?? null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        $revision = new Revision;
        DB::table($revision->getTable())->insert($revisions);
    }

    protected function updateCurrencyInput($input)
    {
        $currencyId = Arr::get($input, 'currency_id');
        $currencyCode = Arr::get($input, 'currency_code');

        if (!$currencyId && $currencyCode) {
            $currency = Currency::where('code', $currencyCode)->first();

            if ($currency) {
                $input['currency_id'] = $currency->id;
            }
        }

        return $input;
    }

    /**
     * Check if model have auditing activated
     */
    protected function syncTags($model, array $selectedTags, $detaching)
    {
        if (in_array(\OwenIt\Auditing\Auditable::class, class_uses_recursive(get_class($model)))) {
            $model->auditSync('tags', $selectedTags, $detaching);
        } else {
            $model->tags()->sync($selectedTags, $detaching);
        }
    }
}


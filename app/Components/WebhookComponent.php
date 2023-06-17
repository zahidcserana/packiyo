<?php

namespace App\Components;

use App\Http\Requests\Webhook\DestroyBatchRequest;
use App\Http\Requests\Webhook\DestroyRequest;
use App\Http\Requests\Webhook\StoreBatchRequest;
use App\Http\Requests\Webhook\StoreRequest;
use App\Http\Requests\Webhook\UpdateBatchRequest;
use App\Http\Requests\Webhook\UpdateRequest;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class WebhookComponent
{
    public function store(StoreRequest $request)
    {
        $input = $request->validated();
        $input['user_id'] = auth()->user()->id;

        return Webhook::create($input);
    }

    public function storeBatch(StoreBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $storeRequest = StoreRequest::make($record);

            $responseCollection->add($this->store($storeRequest));
        }

        return $responseCollection;
    }

    public function update(UpdateRequest $request, Webhook $webhook)
    {
        $input = $request->validated();

        $webhook->update($input);

        return $webhook;
    }

    public function updateBatch(UpdateBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $updateRequest = UpdateRequest::make($record);
            $webhook = Webhook::find($record['id']);

            $responseCollection->add($this->update($updateRequest, $webhook));
        }

        return $responseCollection;
    }

    public function destroy(DestroyRequest $request, Webhook $webhook)
    {
        $webhook->delete();

        return ['id' => $webhook->id];
    }

    public function destroyBatch(DestroyBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $destroyRequest = DestroyRequest::make($record);
            $webhook = Webhook::find($record['id']);

            $responseCollection->add($this->destroy($destroyRequest, $webhook));
        }

        return $responseCollection;
    }

    public function getUserWebhooks(User $user): LengthAwarePaginator
    {
        return $user->webhooks()->paginate();
    }

    public function filterUsers(Request $request): JsonResponse
    {
        $term = $request->get('term');
        $results = [];

        if ($term) {

            $users = User::whereHas('contactInformation', static function($query) use ($term) {
                // TODO: sanitize term
                $term = $term . '%';

                $query->where('name', 'like', $term)
                    ->orWhere('company_name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('zip', 'like', $term)
                    ->orWhere('city', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            })->get();

            foreach ($users as $user) {
                $results[] = [
                    'id' => $user->id,
                    'text' => $user->contactInformation->name . ', ' . $user->contactInformation->email . ', ' . $user->contactInformation->zip . ', ' . $user->contactInformation->city . ', ' . $user->contactInformation->phone
                ];
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }
}

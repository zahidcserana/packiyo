<?php

namespace App\Http\Controllers;

use App\Http\Requests\Webhook\DestroyRequest;
use App\Http\Requests\Webhook\StoreRequest;
use App\Http\Requests\Webhook\UpdateRequest;
use App\Models\Webhook;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Webhook::class);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $object_types = Webhook::WEBHOOK_OBJECT_TYPES;

        return view('webhooks.create', ['object_types' => $object_types]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        app()->webhook->store($request);

        return redirect()->route('profile.edit', [ '#webhooks' ])->withStatus(__('Webhook successfully created.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(Webhook $webhook)
    {
        $object_types = Webhook::WEBHOOK_OBJECT_TYPES;

        return view('webhooks.edit', ['object_types' => $object_types, 'webhook' => $webhook]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Webhook $webhook)
    {
        app()->webhook->update($request, $webhook);

        return redirect()->route('profile.edit', [ '#webhooks' ])->withWebhookStatus(__('Webhook successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Webhook $webhook
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, Webhook $webhook)
    {
        app()->webhook->destroy($request, $webhook);

        return redirect()->route('profile.edit', [ '#webhooks' ])->withWebhookStatus(__('Webhook successfully deleted.'));
    }

    public function filterUsers(Request $request)
    {
        return app()->webhook->filterUsers($request);
    }
}

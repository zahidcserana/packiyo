<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccessTokenRequest;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Order;
use App\Models\PurchaseOrder;
use App\Models\Return_;
use App\Models\Webhook;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Revision;
use Illuminate\Http\Request;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile.
     *
     * @return Application|Factory|View
     */
    public function edit()
    {
        $webhooks = Webhook::all();

        return view('profile.edit', ['webhooks' => $webhooks]);
    }

    public function activity()
    {
        return view('profile.activity', [
            'datatableOrder' => app()->editColumn->getDatatableOrder('revisions'),
        ]);
    }

    public function dataTableActivity(Request $request)
    {
        $user = auth()->user();

        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'revisions.created_at';
        $sortDirection = 'asc';

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $revisionCollection = Revision::join('users', 'revisions.user_id', '=', 'users.id')
            ->where('revisions.user_id', $user->id)
            ->select('*', 'revisions.*')
            ->orderBy($sortColumnName, $sortDirection)
            ->groupBy('revisions.id');

        $term = $request->get('search')['value'];

        if($term) {
            $term = $term . '%';

            $revisionCollection
                ->whereHasMorph('revisionable', [Return_::class, PurchaseOrder::class, Location::class], function($query, $type) use ($term){
                    if ($type === Return_::class || $type === PurchaseOrder::class || $type === Order::class) {
                         $query->where('number', 'like', $term);
                    } elseif ($type === Location::class){
                        $query->where('name', 'like', $term);
                    }
                })
                ->get();
        }

        $revisions = $revisionCollection->skip($request->get('start'))->limit($request->get('length'))->get();

        $revisionCollection = ActivityResource::collection($revisions);

        $visibleFields = app()->editColumn->getVisibleFields('revisions');

        return response()->json([
            'data' => $revisionCollection,
            'visibleFields' => $visibleFields,
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX
        ]);
    }

    /**
     * Update the profile
     *
     * @param ProfileRequest $request
     * @return RedirectResponse
     */
    public function update(ProfileRequest $request): RedirectResponse
    {
        $user = auth()->user();

        // Update user picture and email - except name and if profile image is empty
        $user->update(
            $request->merge(['picture' => $request->photo ? $request->photo->store('profile', 'public') : null])
                ->except([$request->hasFile('photo') ? '' : 'picture', 'name'])
        );

        // Update user contact information
        $user->contactInformation->update(['name' => $request->name]);

        return redirect()->route('profile.edit', [ '#profile-user-information-tab-content' ])->withStatus(__('Profile successfully updated.'));
    }

    /**
     * Change the password
     *
     * @param PasswordRequest $request
     * @return RedirectResponse
     */
    public function password(PasswordRequest $request)
    {
        auth()->user()->update(['password' => Hash::make($request->get('password'))]);

        return redirect()->route('profile.edit', [ '#profile-change-password-tab-content' ])->withPasswordStatus(__('Change password successfully updated.'));
    }

    /**
     * Update tokens
     *
     * @param AccessTokenRequest $request
     * @return mixed
     */
    public function createAccessToken(AccessTokenRequest $request)
    {
        $newToken = app()->user->createAccessToken($request);

        if ($newToken instanceof NewAccessToken) {
            $accessTokenStatus = __('Your new token is :token. Keep it safe!', ['token' => $newToken->plainTextToken]);
        } else {
            $accessTokenStatus = __('Something went wrong');
        }

        return redirect()->route('profile.edit', [ '#access-tokens' ])->withAccessTokenStatus($accessTokenStatus);
    }

    /**
     * @param PersonalAccessToken $token
     * @return mixed
     */
    public function deleteAccessToken(PersonalAccessToken $token)
    {
        if (app()->user->deleteAccessToken($token)) {
            $accessTokenStatus = __('Access token successfully deleted.');
        } else {
            $accessTokenStatus = __('Access token couldn\'t be deleted');
        }

        return redirect()->route('profile.edit', [ '#access-tokens' ])->withAccessTokenStatus($accessTokenStatus);
    }
}

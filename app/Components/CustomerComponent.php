<?php

namespace App\Components;

use App\Http\Requests\Customer\DestroyBatchRequest;
use App\Http\Requests\Customer\DestroyRequest;
use App\Http\Requests\Customer\StoreBatchRequest;
use App\Http\Requests\Customer\StoreRequest;
use App\Http\Requests\Customer\UpdateBatchRequest;
use App\Http\Requests\Customer\UpdateRequest;
use App\Http\Requests\Customer\UpdateUsersRequest;
use App\Models\Customer;
use App\Models\CustomerSetting;
use App\Models\CustomerUser;
use App\Models\Image;
use App\Models\OrderStatus;
use App\Models\ReturnStatus;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CustomerComponent extends BaseComponent
{
    public function store(StoreRequest $request)
    {
        $input = $request->validated();

        $contactInformationData = Arr::get($input, 'contact_information');

        $input['allow_child_customers'] = Arr::get($input, 'allow_child_customers') === '1';

        if ($input['allow_child_customers']) {
            Arr::forget($input, 'parent_customer_id');
        }

        $customer = Customer::create($input);

        $customer->save();

        if (auth()->user() && !auth()->user()->isAdmin()) {
            /** @var User $user */
            $user = auth()->user();

            $user->customers()->attach($customer, [
                'role_id' => UserRole::ROLE_DEFAULT
            ]);
        }

        if (Arr::exists($input, 'parent_customer_id') && auth()->user()->isAdmin()) {
            $customer->parent_id = Arr::get($input, 'parent_customer_id');
            Arr::forget($input, 'parent_customer_id');

            $customer->save();
        } elseif (!is_null(app()->user->getSessionCustomer())) {
            $customer->parent_id = Arr::get($input, 'parent_customer_id');
            Arr::forget($input, 'parent_customer_id');

            $customer->save();
        }

        $this->createContactInformation($contactInformationData, $customer);

        if ($customer->isParent()) {
            $this->createPrimaryWarehouse($contactInformationData, $customer);
        }

        $this->storeSettings($customer, $request->validated());
        $this->updateOrderSlipLogo($customer, Arr::get($input, 'order_slip_logo'));

        return $customer;
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

    public function update(UpdateRequest $request, Customer $customer)
    {
        $input = $request->validated();

        if (!empty(Arr::get($input, 'contact_information'))){
            $customer->contactInformation->update(Arr::get($input, 'contact_information'));
        }

        if (!empty(Arr::get($input, 'order_slip_logo'))){
            $this->updateOrderSlipLogo($customer, Arr::get($input, 'order_slip_logo'));
        }

        $customer->allow_child_customers = Arr::get($input, 'allow_child_customers') === '1';
        $customer->update($input);
        $this->storeSettings($customer, $request->validated());

        return $customer;
    }

    public function updateBatch(UpdateBatchRequest $request): Collection
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $updateRequest = UpdateRequest::make($record);
            $customer = Customer::where('id', $record['id'])->first();

            if ($customer) {
                $responseCollection->add($this->update($updateRequest, $customer));
            }
        }

        return $responseCollection;
    }

    public function destroy(DestroyRequest $request = null, Customer $customer = null)
    {
        if (!is_null($customer)) {
            $customer->delete();

            return ['id' => $customer->id, 'name' => $customer->contactInformation->name];
        }
    }

    public function destroyBatch(DestroyBatchRequest $request)
    {
        $responseCollection = new Collection();
        $input = $request->validated();

        foreach ($input as $record) {
            $destroyRequest = DestroyRequest::make($record);
            $customer = Customer::where('id', $record['id'])->first();

            $responseCollection->add($this->destroy($destroyRequest, $customer));
        }

        return $responseCollection;
    }

    public function detachUser(Customer $customer, User $user)
    {
        return $customer->users()->detach($user->id);
    }

    public function updateUsers(UpdateUsersRequest $request, Customer $customer)
    {
        $customerUserRoles = [];

        foreach ($request->input('customer_user', []) as $customerUser) {
            $customerUserRoles[$customerUser['user_id']] = ['role_id' => $customerUser['role_id']];
        }

        if ($newCustomerUserId = $request->input('new_user_id')) {
            $customerUserRoles[$newCustomerUserId] = ['role_id' => $request->get('new_user_role_id') || UserRole::ROLE_DEFAULT];
        }

        $customer->users()->syncWithoutDetaching($customerUserRoles);

        return CustomerUser::where('customer_id', $customer->id)->get();
    }

    public function filterUsers(Request $request, Customer $customer): JsonResponse
    {
        $term = $request->get('term');
        $results = [];
        $usersIds = [];

        if ($term) {
            foreach ($customer->users as $users) {
                $usersIds[] = $users->id;
            }

            $users = User::whereHas('contactInformation', static function($query) use ($term) {
                // TODO: sanitize term
                $term = $term . '%';

                $query->where('name', 'like', $term)
                    ->orWhere('company_name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('zip', 'like', $term)
                    ->orWhere('city', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            })
                ->whereNotIn('id', $usersIds)
                ->get();

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

    /**
     * @param Customer $customer
     * @return JsonResponse
     */
    public function getDimensionUnits(Customer $customer): JsonResponse
    {
        $dimensionsUnit = customer_settings($customer->id, CustomerSetting::CUSTOMER_SETTING_DIMENSIONS_UNIT, Customer::DIMENSION_UNIT_DEFAULT);
        $weightUnit = customer_settings($customer->id, CustomerSetting::CUSTOMER_SETTING_WEIGHT_UNIT, Customer::WEIGHT_UNIT_DEFAULT);

        return response()->json([
            'results' => [
                'dimension' => Customer::DIMENSION_UNITS[$dimensionsUnit],
                'weight' => Customer::WEIGHT_UNITS[$weightUnit],
                'currency' => customer_settings($customer->id, 'currency') ?? 'USD'
            ]
        ]);
    }

    public function getUserCustomers(User $user)
    {
        return $user->customers;
    }

    public function storeSettings(Customer $customer, $settings)
    {
        foreach ($settings as $setting => $value) {
            if (in_array($setting, CustomerSetting::CUSTOMER_SETTING_KEYS)) {
                CustomerSetting::updateOrCreate(
                    ['customer_id' => $customer->id, 'key' => $setting],
                    ['value' => $value]
                );

                if (!($value instanceof UploadedFile)) {
                    Cache::put('customer_setting_' . $customer->id . '_' . $setting, $value);
                }
            }
        }
    }

    private function createPrimaryWarehouse($contactInformation, Customer $customer): void
    {
        $warehouse = new Warehouse();
        $warehouse->customer()->associate($customer);
        $warehouse->saveQuietly();

        $contactInformation['name'] = 'Primary';
        $this->createContactInformation($contactInformation, $warehouse);
    }

    private function updateOrderSlipLogo(Customer $customer, $logo): void
    {
        if ($logo instanceof UploadedFile) {
            $filename = $logo->store('public/customers');
            $source = url(Storage::url($filename));

            $imageObj = new Image();
            $imageObj->source = $source;
            $imageObj->filename = $filename;
            $imageObj->object_id = $customer->id;
            $imageObj->object_type = Customer::class;
            $imageObj->save();
        }
    }
}

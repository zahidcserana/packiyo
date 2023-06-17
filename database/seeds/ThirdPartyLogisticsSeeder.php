<?php

namespace Database\Seeders;

use App\Models\ContactInformation;
use App\Models\Customer;
use App\Models\CustomerUser;
use App\Models\CustomerUserRole;
use App\Models\Location;
use App\Models\LocationProduct;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ShippingBox;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ThirdPartyLogisticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run(): void
    {
        // Create parent customer
        $customer3pl1 = $this->createCustomer('3PL Company 1');

        $user3pl1 = User::factory()->create([
            'email' => '3pl1@packiyo.com',
            'password' => \Hash::make('3pl1@packiyo.com'),
            'user_role_id' => UserRole::ROLE_MEMBER
        ]);

        $this->createContactInformation($user3pl1, '3pl1@packiyo.com');

        CustomerUser::create([
            'customer_id' => $customer3pl1->id,
            'user_id' => $user3pl1->id,
            'role_id' => CustomerUserRole::ROLE_CUSTOMER_MEMBER
        ]);

        $warehouse3pl1 = $this->createWarehouse($customer3pl1);

        $this->createLocation($warehouse3pl1, '3PL1LOC1');
        $this->createLocation($warehouse3pl1, '3PL1LOC2');

        $this->createShippingBox($customer3pl1, '3PL1BOX1');
        $this->createShippingBox($customer3pl1, '3PL1BOX2');

        $this->createShippingMethod($customer3pl1, '3PL1 Method 1');
        $this->createShippingBox($customer3pl1, '3PL1 Method 2');

        //Create child customers
        $this->createChildCustomerFor3PLCompany($customer3pl1, '3PL1 Child 1', '3PL1', 'C1', $user3pl1);
        $this->createChildCustomerFor3PLCompany($customer3pl1, '3PL1 Child 2','3PL1', 'C2', $user3pl1);

        // Create another parent customer
        $customer3pl2 = $this->createCustomer('3PL Company 2');

        $user3pl2 = User::factory()->create([
            'email' => '3pl2@packiyo.com',
            'password' => \Hash::make('3pl2@packiyo.com'),
            'user_role_id' => UserRole::ROLE_MEMBER
        ]);

        $this->createContactInformation($user3pl2, '3pl2@packiyo.com');

        CustomerUser::create([
            'customer_id' => $customer3pl2->id,
            'user_id' => $user3pl2->id,
            'role_id' => CustomerUserRole::ROLE_CUSTOMER_MEMBER
        ]);

        $warehouse3pl2 = $this->createWarehouse($customer3pl2);

        $this->createLocation($warehouse3pl2, '3PL2LOC1');
        $this->createLocation($warehouse3pl2, '3PL2LOC2');

        $this->createShippingBox($customer3pl2, '3PL2BOX1');
        $this->createShippingBox($customer3pl2, '3PL2BOX2');

        $this->createShippingMethod($customer3pl2, '3PL2 Method 1');
        $this->createShippingBox($customer3pl2, '3PL2 Method 2');

        //Create child customers
        $this->createChildCustomerFor3PLCompany($customer3pl2, '3PL2 Child 1','3PL2', 'C1', $user3pl2);
        $this->createChildCustomerFor3PLCompany($customer3pl2, '3PL2 Child 2','3PL2', 'C2', $user3pl2);

        // Create regular customer
        $regularCustomer = $this->createCustomer('Regular 1');

        $regularUser = User::factory()->create([
            'email' => 'r1@packiyo.com',
            'password' => \Hash::make('r1@packiyo.com'),
            'user_role_id' => UserRole::ROLE_MEMBER
        ]);

        $this->createContactInformation($regularUser, 'r1@packiyo.com');

        CustomerUser::create([
            'customer_id' => $regularCustomer->id,
            'user_id' => $regularUser->id,
            'role_id' => CustomerUserRole::ROLE_CUSTOMER_MEMBER
        ]);

        $supplier = Supplier::create([
            'customer_id' => $regularCustomer->id,
            'currency' => 'USD'
        ]);

        $this->createContactInformation($supplier, 'regular-customer-vendor');

        $warehouse1 = $this->createWarehouse($regularCustomer);

        $this->createLocation($warehouse1, 'R1LOC1');
        $this->createLocation($warehouse1, 'R2LOC1');

        $this->createShippingBox($regularCustomer, 'R1BOX1');
        $this->createShippingBox($regularCustomer, 'R2BOX1');

        $this->createShippingMethod($regularCustomer, 'R1 Method 1');
        $this->createShippingBox($regularCustomer, 'R1 Method 2');

        $productsIds = [];

        $productsIds[] = $this->createProduct($regularCustomer, 'R1-P1');
        $productsIds[] = $this->createProduct($regularCustomer, 'R1-P2');

        for ($i = 1; $i < 6; $i++) {
            $this->createOrder($regularCustomer, $productsIds, 'R1-O' . $i);
        }
    }

    /**
     * @param $customer
     * @param $sku
     * @return Collection|Model
     * @throws \Exception
     */
    private function createProduct($customer, $sku)
    {
        $product = Product::factory()->create([
            'customer_id' => $customer->id,
            'sku' => $sku,
            'weight' => random_int(1,10),
            'height' => random_int(1,10),
            'width' => random_int(1,10),
            'length' => random_int(1,10)
        ]);

        if ($customer->parent_id) {
            $parentCustomer = Customer::find($customer->parent_id);

            foreach ($parentCustomer->warehouses as $warehouse) {
                foreach ($warehouse->locations as $location) {
                    LocationProduct::create([
                        'product_id' => $product->id,
                        'location_id' => $location->id,
                        'quantity_on_hand' => random_int(1,50)
                    ]);
                }
            }
        } else {
            foreach ($customer->warehouses as $warehouse) {
                foreach ($warehouse->locations as $location) {
                    LocationProduct::create([
                        'product_id' => $product->id,
                        'location_id' => $location->id,
                        'quantity_on_hand' => random_int(1,50)
                    ]);
                }
            }
        }

        return $product;
    }

    /**
     * @param $customer
     * @param $products
     * @param $orderNumber
     * @return Collection|Model
     */
    private function createOrder($customer, $products, $orderNumber){
        $orderStatus = $this->createOrderStatus($customer);

        $order = Order::factory()->create([
            'number' => $orderNumber,
            'customer_id' => $customer->id,
            'order_status_id' => $orderStatus->id
        ]);

        $shippingContactInformation = $this->createContactInformation($order, Str::random());
        $billingContactInformation = $this->createContactInformation($order, Str::random());

        $order->shipping_contact_information_id = $shippingContactInformation->id;
        $order->billing_contact_information_id = $billingContactInformation->id;
        $order->save();

        foreach ($products as $product) {
            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => 2,
                'quantity_shipped' => 0
            ];
        }

        app()->order->updateOrderItems($order, $orderItems);

        return $order;
    }

    /**
     * @param string $name
     * @param Customer|null $parentCustomer
     * @return Collection|Model
     */
    public function createCustomer(string $name, Customer $parentCustomer = null)
    {
        if (is_null($parentCustomer)) {
            $customer = Customer::factory()->create();
        } else {
            $customer = Customer::factory()->create([
                'parent_id' => $parentCustomer->id
            ]);
        }

        $this->createContactInformation($customer, $name);

        return $customer;
    }

    /**
     * @param $customer
     * @return Collection|Model
     */
    public function createWarehouse($customer)
    {
        $warehouse = Warehouse::create(['customer_id' => $customer->id]);

        $this->createContactInformation($warehouse, Str::random());

        return $warehouse;
    }

    /**
     * @param $warehouse
     * @param string $name
     * @return Collection|Model
     */
    public function createLocation($warehouse, string $name)
    {
        return Location::factory()->create(
            [
                'warehouse_id' => $warehouse->id,
                'name' => $name
            ]
        );
    }

    /**
     * @param $customer
     * @return Collection|Model
     */
    public function createOrderStatus($customer)
    {
        return OrderStatus::factory()->create(['customer_id' => $customer->id]);
    }

    /**
     * @param $object
     * @param string $name
     * @return Collection|Model
     */
    public function createContactInformation($object, string $name)
    {
        return ContactInformation::factory()->create([
            'name' => $name,
            'object_type' => get_class($object),
            'object_id' => $object->id,
            'country_id' => 578
        ]);
    }

    /**
     * @param $customer
     * @param string $name
     * @return Collection|Model
     */
    public function createShippingBox($customer, string $name)
    {
        return ShippingBox::factory()->create([
            'customer_id' => $customer->id,
            'name' => $name
        ]);
    }

    /**
     * @param Model|Collection $customer3pl
     * @param string $name
     * @param string $prefix
     * @param string $childSuffix
     * @param $user
     * @return void
     * @throws \Exception
     */
    private function createChildCustomerFor3PLCompany($customer3pl, string $name, string $prefix, string $childSuffix, $user)
    {
        $customer = $this->createCustomer($name, $customer3pl);

        $userCredentials = Str::lower($prefix) . '-' . Str::lower($childSuffix) . '@packiyo.com';

        $childUser = User::factory()->create([
            'email' => $userCredentials,
            'password' => \Hash::make($userCredentials),
            'user_role_id' => UserRole::ROLE_MEMBER
        ]);

        $this->createContactInformation($childUser, $userCredentials);

        CustomerUser::create([
            'customer_id' => $customer->id,
            'user_id' => $childUser->id,
            'role_id' => CustomerUserRole::ROLE_CUSTOMER_MEMBER
        ]);

        CustomerUser::create([
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'role_id' => CustomerUserRole::ROLE_CUSTOMER_MEMBER
        ]);

        $supplier = Supplier::create([
            'customer_id' => $customer->id,
            'currency' => 'USD'
        ]);

        $this->createContactInformation($supplier, $prefix . '-' . $childSuffix . '-vendor');

        $productsIds = [];

        $productsIds[] = $this->createProduct($customer, $prefix . '-' . $childSuffix . '-P1');
        $productsIds[] = $this->createProduct($customer, $prefix . '-' . $childSuffix . '-P2');

        for ($i = 1; $i < 6; $i++) {
            $this->createOrder($customer, $productsIds, $prefix . '-' . $childSuffix . '-O' . $i);
        }
    }
}

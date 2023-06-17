<?php
namespace Tests\Unit\Traits;

use App\Models\ContactInformation;
use App\Models\Customer;
use App\Models\CustomerUserRole;
use App\Models\Location;
use App\Models\LocationProduct;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\PurchaseOrderStatus;
use App\Models\Supplier;
use App\Models\TaskType;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Warehouse;
use DB;

trait UnitTestSetup
{

    public function setUp(): void
    {
        parent::setUp();

        DB::table(\Config::get('countries.table_name'))->insert(array(
            'id' => 1,
            'country_code' => '276',
            'iso_3166_2' => 'DE',
            'iso_3166_3' => 'DEU',
            'name' => 'Germany',
            'region_code' => '150',
            'sub_region_code' => '155',
            'eea' => true,
            'calling_code' => '49'
        ));
    }

    public function createUserRoles(): void
    {
        DB::table('user_roles')->insert([
            'id' => UserRole::ROLE_ADMINISTRATOR,
            'name' => 'Administrator'
        ]);

        DB::table('user_roles')->insert([
            'id' => UserRole::ROLE_DEFAULT,
            'name' => 'Member'
        ]);
    }

    public function createCustomerUserRoles(): void
    {
        DB::table('customer_user_roles')->insert([
            'id' => CustomerUserRole::ROLE_CUSTOMER_ADMINISTRATOR,
            'name' => 'Customer Administrator'
        ]);

        DB::table('customer_user_roles')->insert([
            'id' => CustomerUserRole::ROLE_CUSTOMER_MEMBER,
            'name' => 'Customer Member'
        ]);
    }

    public function createCustomer()
    {
        $customer = Customer::create();

        $this->createContactInformation($customer);

        return $customer;
    }

    public function createAdministrator()
    {
        $user = factory(User::class)->create(['user_role_id' => UserRole::ROLE_ADMINISTRATOR]);

        $this->createContactInformation($user);

        return $user;
    }

    public function createCustomerAdminUser($customer)
    {
        $user = factory(User::class)->create(['user_role_id' => UserRole::ROLE_DEFAULT]);

        $user->customers()->attach($customer->id, [
            'role_id' => CustomerUserRole::ROLE_CUSTOMER_ADMINISTRATOR
        ]);

        $this->createContactInformation($user);

        return $user;
    }

    public function createCustomerUser($customer)
    {
        $user = factory(User::class)->create(['user_role_id' => UserRole::ROLE_DEFAULT]);

        $user->customers()->attach($customer->id, [
            'role_id' => CustomerUserRole::ROLE_DEFAULT
        ]);

        $this->createContactInformation($user);

        return $user;
    }

    public function createUser()
    {
        $user = factory(User::class)->create(['user_role_id' => UserRole::ROLE_DEFAULT]);

        $this->createContactInformation($user);

        return $user;
    }

    public function createProduct($customer)
    {
        return factory(Product::class)->create([
            'customer_id' => $customer->id,
            'weight' => $this->faker()->numberBetween(0, 100),
            'width' => $this->faker()->numberBetween(0, 100),
            'length' => $this->faker()->numberBetween(0, 100),
            'height' => $this->faker()->numberBetween(0, 100)
        ]);
    }

    public function createOrderStatus($customer)
    {
        return factory(OrderStatus::class)->create(['customer_id' => $customer->id]);
    }

    public function createWarehouse($customer)
    {
        $warehouse = Warehouse::create(['customer_id' => $customer->id]);

        $this->createContactInformation($warehouse);

        return $warehouse;
    }

    public function createLocation($warehouse)
    {
        return factory(Location::class)->create(['warehouse_id' => $warehouse->id]);
    }

    public function createLocationProduct($location, $product)
    {
        return LocationProduct::create(['product_id' => $product->id, 'location_id' => $location->id, 'quantity_on_hand' => $this->faker->numberBetween(0, 500)]);
    }

    private function getOrderRequestData($customer, $orderStatus, $product1, $product2): array
    {
        return [
            "customer_id" => $customer->id,
            "order_status_id" => $orderStatus->id,
            "number" => str_random(12),
            "ordered_at" => date('Y-m-d H:i:s'),
            "required_shipping_date_at" => date('Y-m-d H:i:s'),
            "shipping_date_before_at" => date('Y-m-d H:i:s'),
            "priority" => $this->faker->numberBetween(0, 5),
            'notes' => $this->faker->text,
            'tags' => '',
            "order_items" => [
                [
                    "product_id" => $product1->id,
                    "quantity" => $this->faker->numberBetween(1, 9),
                    "quantity_shipped" => 0
                ],
                [
                    "product_id" => $product2->id,
                    "quantity" => $this->faker->numberBetween(1, 9),
                    "quantity_shipped" => 0
                ]
            ],
            "shipping_contact_information" =>
                [
                    'name' => $this->faker->name,
                    'address' => $this->faker->address,
                    'zip' => $this->faker->postcode,
                    'city' => $this->faker->city,
                    'email' => $this->faker->unique()->safeEmail,
                    'phone' => $this->faker->phoneNumber,
                    'country_id' => 1
                ],
            "billing_contact_information" =>
                [
                    'name' => $this->faker->name,
                    'address' => $this->faker->address,
                    'zip' => $this->faker->postcode,
                    'city' => $this->faker->city,
                    'email' => $this->faker->unique()->safeEmail,
                    'phone' => $this->faker->phoneNumber,
                    'country_id' => 1
                ]
        ];
    }

    public function createPurchaseOrderStatus($customer)
    {
        return factory(PurchaseOrderStatus::class)->create(['customer_id' => $customer->id]);
    }

    public function createSupplier($customer)
    {
        $supplier = Supplier::create(['customer_id' => $customer->id]);

        $this->createContactInformation($supplier);

        return $supplier;
    }

    public function createTaskType($customer)
    {
        return factory(TaskType::class)->create(['customer_id' => $customer->id]);
    }

    private function regenerateUniqueNumber($data): array
    {
        foreach ($data as $key => $value) {
            $data[$key]['number'] = str_random(12);
        }

        return $data;
    }

    public function createContactInformation($object): void
    {
        factory(ContactInformation::class)->create([
            'object_type' => get_class($object),
            'object_id' => $object->id,
            'country_id' => 1,
        ]);
    }
}

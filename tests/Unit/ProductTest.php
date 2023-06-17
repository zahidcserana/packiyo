<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use DB;
use Tests\Unit\Traits\UnitTestSetup;

class ProductTest extends TestCase
{
    use RefreshDatabase, WithFaker, UnitTestSetup;

    public function testIndex()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $product = $this->createProduct($customer);

        $productResource = (new ProductResource($product))->resolve();

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.product.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'links' => [],
            'meta' => []
        ]);

        foreach ($response->json()['data'] as $res) {
            $this->assertEmpty(array_diff_key($productResource, $res));
        }
    }

    public function testStore()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $product = $this->createProduct($customer);

        $productResource = (new ProductResource($product))->resolve();

        $data = [
            [
                'customer_id' => $customer->id,
                'sku' => str_random(8),
                'name' => $this->faker->word,
                'price' => $this->faker->randomNumber(3),
                'notes' => $this->faker->text,
                'weight' => $this->faker()->numberBetween(0, 100),
                'width' => $this->faker()->numberBetween(0, 100),
                'length' => $this->faker()->numberBetween(0, 100),
                'height' => $this->faker()->numberBetween(0, 100)
            ],
            [
                'customer_id' => $customer->id,
                'sku' => str_random(8),
                'name' => $this->faker->word,
                'price' => $this->faker->randomNumber(3),
                'notes' => $this->faker->text,
                'weight' => $this->faker()->numberBetween(0, 100),
                'width' => $this->faker()->numberBetween(0, 100),
                'length' => $this->faker()->numberBetween(0, 100),
                'height' => $this->faker()->numberBetween(0, 100)
            ]
        ];

        $user = $this->createCustomerUser($customer);

        $this->actingAs($user, 'api')->json('POST', route('api.product.store'), $data)->assertStatus(200);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('POST', route('api.product.store'), $data)->assertStatus(403);

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('POST', route('api.product.store'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {

            $this->assertEmpty(array_diff_key($productResource, $res));

            $product = Product::where('id', $res['id'])->first();

            $this->assertTrue($product->customer->users->contains('id', $user->id));

            $this->assertFalse($product->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testUpdate()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $product = $this->createProduct($customer);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('update', $product));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('update', $product));

        $productResource = (new ProductResource($product))->resolve();

        $data = [
            [
                'customer_id' => $customer->id,
                'sku' => $product->sku,
                'name' => $this->faker->word,
                'price' => $this->faker->randomNumber(3),
                'notes' => $this->faker->text,
                'weight' => $this->faker()->numberBetween(0, 100),
                'width' => $this->faker()->numberBetween(0, 100),
                'length' => $this->faker()->numberBetween(0, 100),
                'height' => $this->faker()->numberBetween(0, 100)
            ]
        ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('PUT', route('api.product.update'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($productResource, $res));

            $product = Product::where('id', $res['id'])->first();

            $this->assertTrue($product->customer->users->contains('id', $user->id));

            $this->assertFalse($product->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testDestroy()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $product = $this->createProduct($customer);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('delete', $product));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('delete', $product));

        $data = [
            ['sku' => $product->sku, 'customer_id' => $customer->id]
        ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('DELETE', route('api.product.destroy'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $key => $value) {
            $this->assertSoftDeleted('products', ['sku' => $value['sku']]);
        }
    }

    public function testHistory()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $product = $this->createProduct($customer);

        $productName = $product->name;

        $product->name = $this->faker->word;
        $product->price = $this->faker->randomNumber(3);
        $product->update();

        $updatedProductName = $product->name;

        $user = $this->createCustomerUser($customer);

        $response = $this->actingAs($user, 'api')->json('GET', route('api.product.history', $product->id));

        $response->assertStatus(200);

        foreach ($response->json()['data'] as $res) {
            $this->assertEquals($res['revisionable_type'], Product::class);
            $this->assertEquals($res['revisionable_id'], $product->id);

            $key = $res['key'];

            $product = Product::find($res['revisionable_id']);

            if ($key == 'price') {
                $this->assertNotEquals((float)$res['old_value'], (float)$product->$key);
                $this->assertEquals((float)$res['new_value'], (float)$product->$key);
            } else {
                $this->assertNotEquals($res['old_value'], $product->$key);
                $this->assertEquals($res['new_value'], $product->$key);
            }
        }
    }

    public function testFilter()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $product = $this->createProduct($customer);

        $productResource = (new ProductResource($product))->resolve();

        $warehouse = $this->createWarehouse($customer);

        $location = $this->createLocation($warehouse);

        $locationProduct = $this->createLocationProduct($location, $product);

        $data = [
            'from_date_created' => $this->faker->dateTimeBetween('-15 days', 'now')->format('Y-m-d'),
            'to_date_created' => $this->faker->dateTimeBetween('now', '+15 days')->format('Y-m-d'),
            'from_date_updated' => $this->faker->dateTimeBetween('-15 days', 'now')->format('Y-m-d'),
            'to_date_updated' => $this->faker->dateTimeBetween('now', '+15 days')->format('Y-m-d'),
            'location_id' => $location->id
        ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.product.filter', $data));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'links' => [],
            'meta' => []
        ]);

        foreach ($response->json()['data'] as $res) {
            $this->assertEmpty(array_diff_key($productResource, $res));
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Product;
use App\Models\LocationProduct;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\ContactInformation;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customer = $this->createCustomer();

        $productIdArr = $this->createSampleProducts($customer);

		$locationNames = ['A1', 'A3', 'A5', 'A7', 'A9', 'A11', 'A13', 'A15', 'A17', 'A19', 'A21', 'A23', 'A25', 'A27', 'A29', 'A31', 'A33', 'A35', 'A37', 'A39', 'A41', 'A43', 'A45', 'A47', 'A49'];

		$this->createLocationProductTables($locationNames, $productIdArr, $customer);

		$locationNames = ['B49', 'B47', 'B45', 'B43', 'B41', 'B39', 'B37', 'B35', 'B33', 'B31', 'B29', 'B27', 'B25', 'B23', 'B21', 'B19', 'B17', 'B15', 'B13', 'B11', 'B9', 'B7', 'B5', 'B3', 'B1'];

		$this->createLocationProductTables($locationNames, $productIdArr, $customer);

		$locationNames = ['B50', 'B48', 'B46', 'B44', 'B42', 'B40', 'B38', 'B36', 'B34', 'B32', 'B30', 'B28', 'B26', 'B24', 'B22', 'B20', 'B18', 'B16', 'B14', 'B12', 'B10', 'B8', 'B6', 'B4', 'B2'];

		$this->createLocationProductTables($locationNames, $productIdArr, $customer);

		$locationNames = ['C1', 'C3', 'C5', 'C7', 'C9', 'C11', 'C13', 'C15', 'C17', 'C19', 'C21', 'C23', 'C25', 'C27', 'C29', 'C31', 'C33', 'C35', 'C37', 'C39', 'C41', 'C43', 'C45', 'C47', 'C49'];

		$this->createLocationProductTables($locationNames, $productIdArr, $customer);

		$locationNames = ['C2', 'C4', 'C6', 'C8', 'C10', 'C12', 'C14', 'C16', 'C18', 'C20', 'C22', 'C24', 'C26', 'C28', 'C30', 'C32', 'C34', 'C36', 'C38', 'C40', 'C42', 'C44', 'C46', 'C48', 'C50'];

		$this->createLocationProductTables($locationNames, $productIdArr, $customer);

		$locationNames = ['D49', 'D47', 'D45', 'D43', 'D41', 'D39', 'D37', 'D35', 'D33', 'D31', 'D29', 'D27', 'D25', 'D23', 'D21', 'D19', 'D17', 'D15', 'D13', 'D11', 'D9', 'D7', 'D5', 'D3', 'D1'];

		$this->createLocationProductTables($locationNames, $productIdArr, $customer);

		$locationNames = ['D50', 'D48', 'D46', 'D44', 'D42', 'D40', 'D38', 'D36', 'D34', 'D32', 'D30', 'D28', 'D26', 'D24', 'D22', 'D20', 'D18', 'D16', 'D14', 'D12', 'D10', 'D8', 'D6', 'D4', 'D2'];

		$this->createLocationProductTables($locationNames, $productIdArr, $customer);

		$locationNames = ['E1', 'E3', 'E5', 'E7', 'E9', 'E11', 'E13', 'E15', 'E17', 'E19', 'E21', 'E23', 'E25', 'E27', 'E29', 'E31', 'E33', 'E35', 'E37', 'E39', 'E41', 'E43', 'E45', 'E47', 'E49'];

		$this->createLocationProductTables($locationNames, $productIdArr, $customer);

        for ($i=0; $i < 10 ; $i++) {
            $this->createOrders($productIdArr, $customer);
        }
    }

    private function createLocationProductTables($locationNames, $productIdArr, $customer)
    {
        $warehouse = $this->createWarehouse($customer);

		foreach ($locationNames as $key => $locationName) {
			$location = Location::factory()->create(['warehouse_id' => $warehouse->id, 'name' => $locationName]);

			LocationProduct::create(['product_id' => $productIdArr[rand(0, 29)], 'location_id' => $location->id]);
		}
    }

    private function createSampleProducts($customer)
    {
        for ($i=0; $i < 30; $i++) {
            $ids[] = Product::factory()->create([
                'customer_id' => $customer->id,
                'weight' => random_int(1,10),
                'height' => random_int(1,10),
                'width' => random_int(1,10),
                'length' => random_int(1,10)
            ])->id;
        }

    	return $ids;
    }

    private function createOrders($productIdArr, $customer){
        $orderStatus = $this->createOrderStatus($customer);

        $order = Order::factory()->create([
            "customer_id" => $customer->id,
            "order_status_id" => $orderStatus->id
        ]);

        $shippingContactInformation = $this->createContactInformation($order);
        $billingContactInformation = $this->createContactInformation($order);

        $order->shipping_contact_information_id = $shippingContactInformation->id;
        $order->billing_contact_information_id = $billingContactInformation->id;
        $order->save();

        $rand_keys = array_rand($productIdArr, 5);

        foreach ($rand_keys as $value) {
            $orderItems[] = [
                "product_id" => $productIdArr[$value],
                "quantity" => 5,
                "quantity_shipped" => 0
            ];
        }

        app()->order->updateOrderItems($order, $orderItems);

        return $order;
    }

    public function createCustomer()
    {
        $customer = Customer::create();

        $this->createContactInformation($customer);

        return $customer;
    }

    public function createWarehouse($customer)
    {
        $warehouse = Warehouse::create(['customer_id' => $customer->id]);

        $this->createContactInformation($warehouse);

        return $warehouse;
    }

    public function createOrderStatus($customer)
    {
        $orderStatus = OrderStatus::factory()->create(['customer_id' => $customer->id]);

        return $orderStatus;
    }

    public function createContactInformation($object)
    {
        $contactInformation = ContactInformation::factory()->create([
            'object_type' => get_class($object),
            'object_id' => $object->id,
            'country_id' => 1
        ]);

        return $contactInformation;
    }
}

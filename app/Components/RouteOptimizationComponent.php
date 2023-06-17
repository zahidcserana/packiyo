<?php

namespace App\Components;

use App\Http\Requests\FormRequest;
use App\Http\Requests\PickingBatch\PickRequest;
use App\Models\Customer;
use App\Models\CustomerSetting;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderLock;
use App\Models\PickingBatch;
use App\Models\PickingBatchItem;
use App\Models\PickingCart;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\ToteOrderItem;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RouteOptimizationComponent extends BaseComponent
{
    public const PICKING_STRATEGY_ALPHANUMERICALLY = 'alphanumerically';
    public const PICKING_STRATEGY_MOST_INVENTORY = 'most_inventory';
    public const PICKING_STRATEGY_LEAST_INVENTORY = 'least_inventory';

    public function __construct()
    {
        $this->startLocation = 'A1';
        $this->horizontalBlockDistance = 10;

        $this->frontEndOddValue['A'] = ['number' => 1, 'block' => 1];
        $this->rearEndOddValue['A'] = ['number' => 49, 'block' => 1];
        $this->frontEndOddValue['B'] = ['number' => 49, 'block' => 1];
        $this->rearEndOddValue['B'] = ['number' => 1, 'block' => 1];

        $this->frontEndEvenValue['B'] = ['number' => 50, 'block' => 2];
        $this->rearEndEvenValue['B'] = ['number' => 2, 'block' => 2];
        $this->frontEndOddValue['C'] = ['number' => 1, 'block' => 2];
        $this->rearEndOddValue['C'] = ['number' => 49, 'block' => 2];

        $this->frontEndEvenValue['C'] = ['number' => 2, 'block' => 3];
        $this->rearEndEvenValue['C'] = ['number' => 50, 'block' => 3];
        $this->frontEndOddValue['D'] = ['number' => 49, 'block' => 3];
        $this->rearEndOddValue['D'] = ['number' => 1, 'block' => 3];

        $this->frontEndEvenValue['D'] = ['number' => 50, 'block' => 4];
        $this->rearEndEvenValue['D'] = ['number' => 2, 'block' => 4];
        $this->frontEndOddValue['E'] = ['number' => 1, 'block' => 4];
        $this->rearEndOddValue['E'] = ['number' => 49, 'block' => 4];
    }

    public function getStartLocation()
    {
        return $this->startLocation;
    }

    public function createPickingBatch(FormRequest $request, $orders, User $user, $type = "mib")
    {
        $input = $request->validated();

        $customer = Customer::where('id', $input['customer_id'])->first();
        $pickingRouteStrategy = customer_settings($customer->id, CustomerSetting::CUSTOMER_SETTING_PICKING_ROUTE_STRATEGY, '');
        $warehouse = Warehouse::whereCustomerId($input['customer_id'])->first();

        $quantity = $input['quantity'] ?? 1;

        if ($quantity > count($orders)) {
            $quantity = count($orders);
        }

        $pickingCart = $this->getPickingCart($warehouse, $quantity);
        $locationProductsUsed = [];
        $shortestPaths = [];
        $ordersUsed = [];
        $lockedOrders = [];

        foreach ($orders as $order) {
            $orderItems = $order->orderItems;

            foreach ($orderItems as $orderItem) {
                if (in_array($order->id, $lockedOrders)) {
                    break;
                }

                $pickedQuantity = $orderItem->placedToteOrderItems->sum('quantity_picked');

                if (!$orderItem->quantity_allocated_pickable -= $pickedQuantity) {
                    continue;
                }

                $quantityNeeded = $orderItem->quantity_allocated_pickable;

                $locations = $orderItem->product->locations()->reorder()->where('pickable_effective', 1);

                switch ($pickingRouteStrategy) {
                    case self::PICKING_STRATEGY_MOST_INVENTORY:
                        $locations->reorder()->orderBy('pivot_quantity_on_hand', 'desc');
                        break;
                    case self::PICKING_STRATEGY_LEAST_INVENTORY:
                        $locations->reorder()->orderBy('pivot_quantity_on_hand');
                        break;
                    default:
                        $locations->reorder()->orderBy('name');
                }

                $locations = $locations->get();

                foreach ($locations as $location) {
                    if (in_array($order->id, $lockedOrders)) {
                        break;
                    }

                    $quantityUsed = Arr::get($locationProductsUsed, $location->pivot->location_id . '.' . $location->pivot->product_id, 0);
                    $quantityToTake = min($quantityNeeded, $location->pivot->quantity_on_hand - $location->pivot->quantity_reserved_for_picking - $quantityUsed);

                    if ($quantityToTake < 1) {
                        continue;
                    }

                    if (!in_array($order->id, $ordersUsed)) {
                        $orderLock = OrderLock::firstOrCreate(
                            ['order_id' => $order->id],
                            [
                                'user_id' => $user->id,
                                'lock_type' => OrderLock::LOCK_TYPE_PICKING
                            ],
                        );

                        if (!$orderLock->wasRecentlyCreated) {
                            $lockedOrders[$order->id] = $order->id;
                            break;
                        }
                    }

                    $ordersUsed[$order->id] = $order->id;

                    $shortestPaths[] = [
                        'nextLocation' => $location->id,
                        'locationName' => $location->name,
                        'orderIds' => [$orderItem->order_id],
                        'orderItemId' => $orderItem->id,
                        'productId' => $orderItem->product_id,
                        'quantity' => $quantityToTake,
                        'orderNumbers' => [$orderItem->order->number]
                    ];

                    $quantityNeeded -= $quantityToTake;

                    Arr::set($locationProductsUsed, $location->pivot->location_id . '.' . $location->pivot->product_id, $quantityUsed + $quantityToTake);

                    if ($quantityNeeded == 0) {
                        break;
                    }
                }
            }

            if (count($ordersUsed) == $quantity) {
                break;
            }
        }

        $pickingBatch = null;

        if (!empty($shortestPaths)) {
            $pickingBatch = $this->store($shortestPaths, $customer, $type);

            if ($pickingCart) {
                $this->assignCartToPickingBatch($pickingCart, $pickingBatch);
            }

            $taskType = $this->getTaskType($customer);

            $this->createTask($pickingBatch, $taskType, $customer, $user);
        }

        return $pickingBatch;
    }

    public function getTaskType(Customer $customer)
    {
        return TaskType::where('customer_id', $customer->id)->where('type', TaskType::TYPE_PICKING)->first();
    }

    public function createTask(PickingBatch $pickingBatch, TaskType $taskType, Customer $customer, User $user)
    {
        $task = new Task();
        $task->taskable()->associate($pickingBatch);
        $task->user_id = $user->id;
        $task->customer_id = $customer->id;
        $task->task_type_id = $taskType->id;
        $task->notes = '';
        $task->save();
    }

    public function store($shortestPaths, Customer $customer, $type)
    {
        $orderIds = [];

        $shortestPaths = Arr::sort($shortestPaths, function($shortestPath) {
            return $shortestPath['locationName'];
        });

        $pickingBatch = PickingBatch::create([
            'customer_id' => $customer->id,
            'type' => $type
        ]);

        foreach ($shortestPaths as $shortestPath) {
            $location = Location::find($shortestPath['nextLocation']);
            $orderItem = OrderItem::find($shortestPath['orderItemId']);

            PickingBatchItem::create([
                'picking_batch_id' => $pickingBatch->id,
                'order_item_id' => $orderItem->id,
                'location_id' => $location->id,
                'quantity' => $shortestPath['quantity']
            ]);

            $orderIds[$orderItem->order_id] = $orderItem->order_id;
        }

        foreach ($orderIds as $orderId) {
            app('bulkShip')->syncSingleOrder(Order::find($orderId));
        }

        return $pickingBatch;
    }

    public function pick(PickRequest $request)
    {
        $input = $request->validated();
        $orders = $input['orders'];

        foreach ($orders as $order) {
            $pickingBatchItem = PickingBatchItem::find($order['picking_batch_item_id']);
            $quantityLeft = $pickingBatchItem->quantity - $pickingBatchItem->quantity_picked;

            if ($quantityLeft) {
                $order['quantity'] = $order['quantity'] > $quantityLeft ? $quantityLeft : $order['quantity'];
                $pickingBatchItem->quantity_picked += $order['quantity'];
                $pickingBatchItem->save();

                $toteOrderItem = ToteOrderItem::firstOrNew([
                    'picking_batch_item_id' => $pickingBatchItem->id,
                    'order_item_id' => $pickingBatchItem->order_item_id,
                    'location_id' => $input['location_id'],
                    'tote_id' => $input['tote_id'],
                ]);

                $toteOrderItem->quantity += $order['quantity'];
                $toteOrderItem->picked_at = Carbon::now();
                $toteOrderItem->user_id = auth()->user()->id;
                $toteOrderItem->save();
            }
        }

        if (PickingBatchItem::where('picking_batch_id', '=', $input['picking_batch_id'])->sum(DB::raw('quantity - quantity_picked')) === 0) {
            $task = Task::where('taskable_id', $input['picking_batch_id'])->first();
            $task->completed_at = Carbon::now();
            $task->save();

            $pickingBatch = PickingBatch::with('pickingBatchItems.orderItem')->find($input['picking_batch_id']);
            $orderIds = [];

            foreach ($pickingBatch->pickingBatchItems as $pickingBatchItem) {
                if (!in_array($pickingBatchItem->orderItem->order_id, $orderIds)) {
                    $orderIds[] = $pickingBatchItem->orderItem->order_id;
                }
            }

            OrderLock::whereIntegerInRaw('order_id', $orderIds)->delete();
        }

        return collect([]);
    }

    public function reformPaths($orders, $paths)
    {
        $totalPathDistance = 0;

        foreach ($paths as $key => $path) {
            $orderIds = [];
            foreach ($orders as $order) {
                $item = OrderItem::where('order_id', $order->id)->where('product_id', $path['productId'])->first();

                if ($item) {
                    $orderIds[] = $order->id;
                }
            }

            $orderIdsStr = implode(', ', $orderIds);

            $paths[$key]['orderIds'] = $orderIds;
            $paths[$key]['orderIdsStr'] = $orderIdsStr;

            $totalPathDistance += $path['pathDistance'];
        }

        return compact("paths", "totalPathDistance");
    }

    public function getAllShortestPaths($startLocation, $items)
    {
        $currentLocation = $startLocation;
        $itemsArr = [];
        $paths = [];

        foreach ($items as $key => $item) {
            $itemsArr[$item->product_id] = $item->product->locations->pluck('name')->toArray();
        }

        while (count($itemsArr) > 0) {

            $shortestPathDetails = $this->getShortestPath($startLocation, $itemsArr);

            $productId = $shortestPathDetails['product_id'];
            $startLocation = $shortestPathDetails['location'];

            $paths[] = [
                'productId' => $productId,
                'currentLocation' => $currentLocation,
                'nextLocation' => $shortestPathDetails['location'],
                'pathDistance' => $shortestPathDetails['minimum']
            ];

            $currentLocation = $startLocation;

            unset($itemsArr[$productId]);
        }

        return $paths;
    }

    private function getShortestPath($startLocation, $items)
    {
        $details['minimum'] = 0;

        $temp = 0;

        foreach ($items as $key => $locations) {
            foreach ($locations as $locKey => $location) {
                $distance = $this->getDistance($startLocation, $location);

                if ($temp == 0 && $locKey == 0) {
                    $details['minimum'] = $distance;
                    $details['product_id'] = $key;
                    $details['location'] = $location;
                }

                if ($distance < $details['minimum']) {
                    $details['minimum'] = $distance;
                    $details['product_id'] = $key;
                    $details['location'] = $location;
                }

                $temp++;
            }
        }

        return $details;
    }

    private function getDistance($location1, $location2)
    {
        $sameAisle = $this->checkSameAisle($location1, $location2);

        if ($sameAisle) {
            $distance =  $this->getDistanceOfSameAisle($location1, $location2);
        } else {
            $distance =  $this->getDistanceOfDifferentAisles($location1, $location2);
        }

        return $distance;
    }

    private function getDistanceOfSameAisle($location1, $location2)
    {
        $distance = 0;

        $number1 =  $this->getNumericPart($location1);

        $number2 =  $this->getNumericPart($location2);

        $greater = $this->getGreater($number1, $number2);

        if ($this->isEven($number1) && $this->isEven($number2)) {
            $distance = abs(($number1 - $number2)) / 2;
        } else if ($this->isOdd($number1) && $this->isOdd($number2)) {
            $distance = abs(($number1 - $number2)) / 2;
        } else if ($this->isEven($greater)) {
            $distance = ceil(abs(($number1 - $number2)) / 2) - 1;
        } else {
            $distance = ceil(abs(($number1 - $number2)) / 2);
        }

        return $distance;
    }

    private function getDistanceOfDifferentAisles($location1, $location2)
    {
        $letter1 = $this->getLetterPart($location1);

        $letter2 = $this->getLetterPart($location2);

        $number1 = $this->getNumericPart($location1);

        $number2 = $this->getNumericPart($location2);

        if ($this->isEven($number1)) {
            $frontEndValue1 = $this->frontEndEvenValue[$letter1]['number'];
            $rearEndValue1 = $this->rearEndEvenValue[$letter1]['number'];
            $block1 = $this->rearEndEvenValue[$letter1]['block'];
        } else {
            $frontEndValue1 = $this->frontEndOddValue[$letter1]['number'];
            $rearEndValue1 = $this->rearEndOddValue[$letter1]['number'];
            $block1 = $this->rearEndOddValue[$letter1]['block'];
        }

        $frontRearDetails1 = $this->getFrontNearDetails($frontEndValue1, $rearEndValue1, $number1);

        if ($this->isEven($number2)) {
            $frontEndValue2 = $this->frontEndEvenValue[$letter2]['number'];
            $rearEndValue2 = $this->rearEndEvenValue[$letter2]['number'];
            $block2 = $this->rearEndEvenValue[$letter2]['block'];
        } else {
            $frontEndValue2 = $this->frontEndOddValue[$letter2]['number'];
            $rearEndValue2 = $this->rearEndOddValue[$letter2]['number'];
            $block2 = $this->rearEndOddValue[$letter2]['block'];
        }

        $frontRearDetails2 = $this->getFrontNearDetails($frontEndValue2, $rearEndValue2, $number2);

        $locationsDistance = $this->getDistanceFromSameEnd($frontRearDetails1, $frontRearDetails2);

        $blockDistance = $this->getBlockDistance($block1, $block2);

        $distance =  $locationsDistance + $blockDistance;

        return $distance;
    }

    private function getFrontNearDetails($frontEndValue, $rearEndValue, $number)
    {
        if (abs($frontEndValue - $number) <= abs($rearEndValue - $number)) {
            $distanceFromNearestEnd = (abs($frontEndValue - $number)) / 2;
            $distanceFromOtherEnd = (abs($rearEndValue - $number)) / 2;

            $nearestEnd = 'Front';
        } else {
            $distanceFromNearestEnd = (abs($rearEndValue - $number)) / 2;
            $distanceFromOtherEnd = (abs($frontEndValue - $number)) / 2;

            $nearestEnd = 'Rear';
        }

        return compact("nearestEnd", "distanceFromNearestEnd", "distanceFromOtherEnd");
    }

    private function getDistanceFromSameEnd($frontRearDetails1, $frontRearDetails2)
    {
        if ($frontRearDetails1['distanceFromNearestEnd'] <= $frontRearDetails2['distanceFromNearestEnd']) {
            $nearsetEnd = $frontRearDetails1['nearestEnd'];
            $nearsetEndDistance = $frontRearDetails1['distanceFromNearestEnd'];

            $otherLocationDistanceFromThisEnd = $frontRearDetails2['nearestEnd'] == $nearsetEnd ? $frontRearDetails2['distanceFromNearestEnd'] : $frontRearDetails2['distanceFromOtherEnd'];

            $totalDistance = $nearsetEndDistance + $otherLocationDistanceFromThisEnd;
        } else {
            $nearsetEnd = $frontRearDetails2['nearestEnd'];
            $nearsetEndDistance = $frontRearDetails2['distanceFromNearestEnd'];

            $otherLocationDistanceFromThisEnd = $frontRearDetails1['nearestEnd'] == $nearsetEnd ? $frontRearDetails1['distanceFromNearestEnd'] : $frontRearDetails1['distanceFromOtherEnd'];

            $totalDistance = $nearsetEndDistance + $otherLocationDistanceFromThisEnd;
        }

        return $totalDistance;
    }

    private function getBlockDistance($block1, $block2)
    {
        if (abs($block1 - $block2) == 0){
            return 1;
        }

        return abs($block1 - $block2) * $this->horizontalBlockDistance;
    }

    private function checkSameAisle($location1, $location2)
    {
        $letter1 = $this->getLetterPart($location1);

        $letter2 = $this->getLetterPart($location2);

        return $letter1 == $letter2;
    }

    private function getNumericPart($location)
    {
        return (int) preg_replace('/[^0-9]/', '', $location);
    }

    private function getLetterPart($location)
    {
        return preg_replace('/[^a-zA-Z]/', '', $location);
    }

    private function isEven($number)
    {
        return $number % 2 == 0;
    }

    private function isOdd($number)
    {
        return $number % 2 != 0;
    }

    private function getGreater($number1, $number2)
    {
        if ($number1 == $number2) {
            return false;
        }

        if ($number1 > $number2) {
            return $number1;
        }

        return $number2;
    }

    /**
     * @param Warehouse $warehouse
     * @param $quantity
     * @return PickingCart|null
     */
    private function getPickingCart(Warehouse $warehouse, $quantity): ?PickingCart
    {
        $pickingCarts = PickingCart::where('number_of_totes', '>=', $quantity)
            ->where('warehouse_id', $warehouse->id)
            ->orderBy('number_of_totes', 'asc')
            ->get();

        if (!is_null($pickingCarts)) {
            foreach ($pickingCarts as $pickingCart) {
                /** @var PickingCart $pickingCart */
                if ($pickingCart->totes_count > $quantity) {
                    return $pickingCart;
                }
            }
        }

        return null;
    }

    /**
     * @param PickingCart $pickingCart
     * @param PickingBatch $pickingBatch
     * @return bool
     */
    private function assignCartToPickingBatch(PickingCart $pickingCart, PickingBatch $pickingBatch): bool
    {
        try {
            $pickingBatch->picking_cart_id = $pickingCart->id;
            $pickingBatch->save();

            return true;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }
}

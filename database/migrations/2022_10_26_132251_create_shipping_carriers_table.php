<?php

use App\Components\PackingComponent;
use App\Components\ShippingComponent;
use App\Models\EasypostCredential;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShippingCarrier;
use App\Models\ShippingMethod;
use App\Models\ShippingMethodMapping;
use App\Models\WebshipperCredential;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

class CreateShippingCarriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_carriers', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('customer_id');
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->morphs('credential');
            $table->string('carrier_service');
            $table->string('name');
            $table->json('settings');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(ShippingCarrier::class)
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('name');
            $table->json('settings');

            $table->timestamps();
            $table->softDeletes();
        });

        $webshipperMethods = DB::select('SELECT wcsm.id, carrier_id, wc.customer_id, wc.name AS carrier_name, wc.webshipper_shipping_carrier_id, wc.webshipper_credential_id, wcsm.name, webshipper_shipping_rate_id, has_drop_points FROM webshipper_carrier_shipping_methods wcsm LEFT JOIN webshipper_carriers wc ON wcsm.carrier_id = wc.id');

        $webshipperShippingCarrierArray = [];
        $webshipperShippingMethodArray = [];

        foreach ($webshipperMethods as $webshipperMethod) {
            $shippingCarrier = Arr::get($webshipperShippingCarrierArray, $webshipperMethod->carrier_id);

            if (!$shippingCarrier) {
                $shippingCarrier = ShippingCarrier::create([
                    'customer_id' => $webshipperMethod->customer_id,
                    'carrier_service' => ShippingComponent::SHIPPING_CARRIER_SERVICE_WEBSHIPPER,
                    'name' => $webshipperMethod->carrier_name,
                    'settings' => [
                        'external_carrier_id' => $webshipperMethod->webshipper_shipping_carrier_id
                    ]
                ]);

                $shippingCarrier->credential()->associate(WebshipperCredential::find($webshipperMethod->webshipper_credential_id))->save();

                $webshipperShippingCarrierArray[$webshipperMethod->carrier_id] = $shippingCarrier;
            }

            $shippingMethod = ShippingMethod::create([
                'name' => $webshipperMethod->name,
                'shipping_carrier_id' => $webshipperShippingCarrierArray[$webshipperMethod->carrier_id]->id,
                'settings' => [
                    'external_method_id' => $webshipperMethod->webshipper_shipping_rate_id,
                    'has_drop_points' => $webshipperMethod->has_drop_points
                ]
            ]);

            $webshipperShippingMethodArray[$webshipperMethod->id] = $shippingMethod;
        }

        Schema::table('shipping_method_mappings', function (Blueprint $table) {
            $table->foreignIdFor(ShippingMethod::class)
                ->after('customer_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        $shippingMethodMappings = ShippingMethodMapping::withTrashed()->get();

        foreach ($shippingMethodMappings as $shippingMethodMapping) {
            foreach ($webshipperMethods as $webshipperMethod) {
                if ($shippingMethodMapping->webshipper_carrier_shipping_method_id == $webshipperMethod->id) {
                    $shippingMethodMapping->updateQuietly([
                        'shipping_method_id' => $webshipperShippingMethodArray[$webshipperMethod->id]->id
                    ]);
                }
            }
        }

        Schema::table('shipments', function (Blueprint $table) {
            $table->foreignIdFor(ShippingMethod::class)
                ->after('order_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        $shipments = Shipment::withTrashed()->get();

        foreach ($shipments as $shipment) {
            foreach ($webshipperMethods as $webshipperMethod) {
                if ($shipment->webshipper_carrier_shipping_method_id == $webshipperMethod->id) {
                    $shipment->updateQuietly([
                        'shipping_method_id' => $webshipperShippingMethodArray[$webshipperMethod->id]->id
                    ]);
                }
            }
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('shipping_method_id', 'webshipper_carrier_shipping_method_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignIdFor(ShippingMethod::class)
                ->after('order_status_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        $orders = Order::withTrashed()->get();

        foreach ($orders as $order) {
            foreach ($webshipperMethods as $webshipperMethod) {
                if ($order->webshipper_carrier_shipping_method_id == $webshipperMethod->id) {
                    $order->updateQuietly([
                        'shipping_method_id' => $webshipperShippingMethodArray[$webshipperMethod->id]->id
                    ]);
                }
            }
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('webshipper_carrier_shipping_method_id');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['webshipper_carrier_shipping_method_id']);
            $table->dropColumn('webshipper_carrier_shipping_method_id');

            $table->dropIndex(['webshipper_shipment_id']);
            $table->renameColumn('webshipper_shipment_id', 'external_shipment_id');
            $table->index('external_shipment_id');
        });

        Schema::table('shipping_method_mappings', function (Blueprint $table) {
            $table->dropColumn('webshipper_carrier_shipping_method_id');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign(['webshipper_carrier_shipping_methods_id']);
            $table->dropColumn(['webshipper_carrier_shipping_methods_id']);
        });

        Schema::drop('webshipper_carrier_shipping_methods');
        Schema::drop('webshipper_carriers');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('webshipper_carriers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->string('name');
            $table->integer('webshipper_shipping_carrier_id');
            $table->unsignedInteger('webshipper_credential_id')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('webshipper_credential_id')
                ->references('id')
                ->on('webshipper_credentials')
                ->onUpdate('set null')
                ->onDelete('set null');
        });

        Schema::create('webshipper_carrier_shipping_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('carrier_id');
            $table->unsignedInteger('customer_id');
            $table->string('name');
            $table->boolean('has_drop_points')->default(false);

            $table->timestamps();

            $table->softDeletes();
            $table->integer('webshipper_shipping_rate_id');
            $table->foreign('carrier_id')
                ->references('id')
                ->on('webshipper_carriers')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->index('webshipper_shipping_rate_id', 'webshipper_carrier_rate_id');
        });


        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipping_method_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('shipping_method_id')->nullable();
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipping_method_id');
            $table->unsignedInteger('webshipper_carrier_shipping_method_id')->nullable();
            $table->foreign('webshipper_carrier_shipping_method_id')
                ->references('id')
                ->on('webshipper_carrier_shipping_methods')
                ->onUpdate('cascade');

            $table->dropIndex(['external_shipment_id']);
            $table->renameColumn('external_shipment_id', 'webshipper_shipment_id');
            $table->index('webshipper_shipment_id');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->unsignedInteger('webshipper_carrier_shipping_methods_id')->nullable();
            $table->foreign('webshipper_carrier_shipping_methods_id')
                ->references('id')
                ->on('webshipper_carrier_shipping_methods')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('shipping_method_mappings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipping_method_id');
            $table->foreignId('webshipper_carrier_shipping_method_id');
        });

        Schema::dropIfExists('shipping_methods');
        Schema::dropIfExists('shipping_carriers');
    }
}

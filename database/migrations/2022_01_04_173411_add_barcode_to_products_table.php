<?php

use App\Models\Product;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

class AddBarcodeToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('barcode')->nullable()->unique();
        });

        //add barcodes for existing products in the database
        $products = Product::all();

        foreach ($products as $product) {
            if (is_null($product->barcode)) {
                $product->barcode = 'PRODUCT_' . Str::random();
                $product->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->removeColumn('barcode');
        });
    }
}

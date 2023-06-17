<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasBarcodeTrait
{
    protected static function bootHasBarcodeTrait()
    {
        static::saving(function (Model $model) {
            if (!$model->barcode) {
                $instanceId = env('INSTANCE_ID');
                $barcodePattern = env('BARCODE_PATTERN');
                $barcodePatternWithInstance = str_replace('_INSTANCE_ID_', $instanceId, $barcodePattern);

                $modelClass = get_class($model);
                $latestBarcode = $modelClass::where('barcode', 'like', $barcodePatternWithInstance . '%')->orderBy('barcode', 'desc')->value('barcode');

                if (!$latestBarcode) {
                    $latestBarcode = preg_replace_callback('/_/', function($matches) {
                        return rand(1, 9);
                    }, $barcodePatternWithInstance);
                }

                $barcodeNumber = static::getBarcodeNumber($latestBarcode);

                do {
                    $barcodeNumber++;
                    $fullBarcode = static::getFullBarcode($barcodeNumber);
                } while ($modelClass::where('barcode', $fullBarcode)->exists());

                $model->barcode = $fullBarcode;
            }
        });
    }

    private static function getBarcodeNumber($barcode)
    {
        // remove instance name
        $instanceId = env('INSTANCE_ID');
        $barcodePattern = env('BARCODE_PATTERN');
        $instancePatternPosition = strpos($barcodePattern, '_INSTANCE_ID_');
        $instanceLength = strlen($instanceId);

        $barcode = substr_replace($barcode, '', $instancePatternPosition, $instanceLength);

        // extract numbers only
        return (int)preg_replace('/[^0-9]/', '', $barcode);
    }

    private static function getFullBarcode($barcode)
    {
        $instanceId = env('INSTANCE_ID');
        $barcodePattern = env('BARCODE_PATTERN');
        $fullBarcode = str_replace('_INSTANCE_ID_', $instanceId, $barcodePattern);
        $numberPatternCount = substr_count($fullBarcode, '_');

        for ($i = 0; $i < $numberPatternCount; $i++) {
            if ($i < $numberPatternCount - 1) {
                $barcodeNumber = substr($barcode, 0, 1);
            } else {
                $barcodeNumber = $barcode;
            }
            $barcode = substr($barcode, 1);
            $numberPatternPosition = strpos($fullBarcode, '_');
            $fullBarcode = substr_replace($fullBarcode, $barcodeNumber, $numberPatternPosition, strlen($barcodeNumber));
        }

        return $fullBarcode;
    }
}

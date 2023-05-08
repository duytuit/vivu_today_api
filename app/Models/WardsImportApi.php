<?php

namespace App\Models;

use App\Utility\RedisUtility;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;
use Carbon\Carbon;

//class ProductsImport implements ToModel, WithHeadingRow, WithValidation
class WardsImportApi implements ToCollection, WithHeadingRow, WithValidation, ToModel
{
    private $rows = 0;

    public function collection(Collection $rows)
    {
        set_time_limit(0);
        foreach ($rows as $row) {
            $row = (object)$row;
            try {
                RedisUtility::queueSet('Redis_Import_City_District_Ward',$row);
            }catch (\Exception $e){
                dd($e->getTraceAsString());
            }
         }
         dd('thành công.');
    }
    public static function preg_replace_string($_string)
    {
        return preg_replace('/\s+/', ' ', $_string);
    }
    public function model(array $row)
    {
        ++$this->rows;
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function rules(): array
    {
        return [
            // Can also use callback validation rules
//            'unit_price' => function ($attribute, $value, $onFailure) {
//                if (!is_numeric($value)) {
//                    $onFailure('Unit price is not numeric');
//                }
//            }
        ];
    }


}

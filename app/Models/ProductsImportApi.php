<?php

namespace App\Models;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;
use Auth;
use Carbon\Carbon;
use mysql_xdevapi\Exception;
use Storage;

//class ProductsImport implements ToModel, WithHeadingRow, WithValidation
class ProductsImportApi implements ToCollection, WithHeadingRow, WithValidation, ToModel
{
    private $rows = 0;

    public function collection(Collection $rows)
    {
            foreach ($rows as $row) {
//               dd($row);
               // dd(str_replace('%','',$row['giam_gia']));
                //dd(self::preg_replace_string($row['loai_san_pham']));
                //dd(str_replace('.','',str_replace('đ','',$row['gia'])));
                try {
                    $file =null;
//                    if($row['image_src']){
//                        $file= $this->uploadFromUrl($row['image_src']);
//                    }
                    $check_product = Product::where('name','like','%'.trim($row['name']).'%')->first();
//                    dd($check_product);
                    if($check_product){
                        $check_product->barcode = trim($row['code_product']);
                        $check_product->slug = Str::slug(strtolower($row['name']));
                        $check_product->external_link_btn = null;
                        $check_product->external_link =null;
                        $check_product->save();

                    }
                    $_cate = Category::where('name','like','%'.trim($row['danh_muc_cha_5']).'%')->first();
                    $_cate->slug=Str::slug(strtolower($row['danh_muc_cha_5']));
                    $_cate->save();

                    continue;
                    if(!$_cate){
                        $_cate = Category::create([
                            'name' => trim($row['danh_muc_cha_5']),
                            'featured' =>1,
                            'top' =>1,
                            'slug' =>Str::slug(strtolower($row['danh_muc_cha_5'])),
                            'meta_title' =>trim($row['danh_muc_cha_5']),
                            'meta_description' =>trim($row['danh_muc_cha_5'])
                        ]);
                        CategoryTranslation::create([
                            'category_id' => $_cate->id,
                            'name' =>trim($row['danh_muc_cha_5']),
                            'lang' =>'vn'
                        ]);
                    }
                    $_brand = Brand::where('name','like','%'.trim($row['hang']).'%')->first();
                    if(!$_brand){
                        $_brand = Brand::create([
                            'name' => trim($row['hang']),
                            'top' =>1,
                            'slug' =>Str::slug(strtolower($row['hang'])),
                            'meta_title' =>trim($row['hang']),
                            'meta_description' =>trim($row['hang'])
                        ]);
                        $_brand_tran = BrandTranslation::create([
                            'brand_id' => $_brand->id,
                            'name' => trim($row['hang']),
                            'lang' =>'vn',
                        ]);
                    }
                    $productId = Product::create([
                        'name' => trim($row['name']),
                        'description' => self::preg_replace_string($row['mo_ta_chi_tiet']),
                        'added_by' => 'admin',
                        'user_id' => 9,
                        'approved' => 1,
                        'category_id' => $_cate->id,
                        'brand_id' => $_brand->id,
                        'video_provider' => 'youtube',
                        'video_link' => '',
                        'stock_visibility_state' => 'quantity',
                        'cash_on_delivery' => 1,
                        'shipping_type' => 'free',
                        'published' => 1,
                        'min_qty' => 10,
                        'current_stock' => 1,
                        'low_stock_quantity' => 1,
                        'barcode'=>$row['code_product'],
                        'featured' => 0,
                        'discount_type' => 'percent',
                        'discount' => str_replace('%','',$row['giam_gia']),
                        'unit_price' => str_replace('.','',str_replace('đ','',$row['gia'])),
                        'unit' => 1,
                        'meta_title' => trim($row['name']),
                        'meta_description' => $row['mo_ta'],
                        'colors' => json_encode(array()),
                        'choice_options' => json_encode(array()),
                        'variations' => json_encode(array()),
                        'slug' => Str::slug(strtolower($row['name'])),
                        'thumbnail_img' => $file ? $file->id:null,
                        'photos' => $file ? $file->id:null,
                    ]);
                    ProductTranslation::create([
                        'product_id' => $productId->id,
                        'name' =>  trim($row['name']),
                        'unit' => 1,
                        'lang' => 'vn',
                        'description' => self::preg_replace_string($row['mo_ta_chi_tiet']),
                    ]);
                    ProductTax::create([
                        'product_id' => $productId->id,
                        'tax_id' => 3,
                        'tax' => 4,
                        'tax_type' => 'percent',
                    ]);
                    ProductStock::create([
                        'product_id' => $productId->id,
                        'qty' => 10,
                        'price' => str_replace('.','',str_replace('đ','',$row['gia'])),
                        'variant' => '',
                    ]);
                }catch (\Exception $e){
                    dd(trim($row['name']). $e->getTraceAsString());
                }
               // dd('thành công.'.$productId);
        }

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
            'unit_price' => function ($attribute, $value, $onFailure) {
                if (!is_numeric($value)) {
                    $onFailure('Unit price is not numeric');
                }
            }
        ];
    }



    public function uploadFromUrl($url)
    {
        $type = array(
            "jpg" => "image",
            "jpeg" => "image",
            "png" => "image",
            "svg" => "image",
            "webp" => "image",
            "gif" => "image",
        );
        try {
            $_url =  explode('https://nhathuocsuckhoe.com/upload', $url);
            $_url = 'https://nhathuocsuckhoe.com/upload'.$_url[1];
            $arr = pathinfo($_url);

            $stream = @fopen($_url, 'r');
            $dir = public_path('uploads/all');
            $file_name= Str::uuid()->toString().'.'.@$arr['extension'];
            $full_path = "$dir/".$file_name;
            file_put_contents($full_path, $stream);
            $upload = new Upload;
            $extension = strtolower(File::extension($full_path));
            $size = File::size($full_path);

            if (!isset($type[$extension])) {
                unlink($full_path);
                return false;
            }

            $arr = explode('.', File::name($full_path));
            $upload->file_original_name = $arr[0];
            $upload->extension = $extension;
            $upload->file_name = 'uploads/all/'.$file_name;
            $upload->user_id = 9;
            $upload->type = $type[$upload->extension];
            $upload->file_size = $size;
            $upload->save();
            return $upload;
        }catch (Exception $e){
            return false;
        }

    }

}

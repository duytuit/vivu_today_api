<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\ClassifiedProductDetailCollection;
use App\Http\Resources\V2\ClassifiedProductMiniCollection;
use App\Models\ProductsImportApi;
use App\Models\Upload;
use Cache;
use App\Models\Shop;
use App\Models\Color;
use App\Models\Product;
use App\Models\FlashDeal;
use Illuminate\Http\Request;
use App\Utility\SearchUtility;
use App\Utility\CategoryUtility;
use App\Http\Resources\V2\ProductCollection;
use App\Http\Resources\V2\FlashDealCollection;
use App\Http\Resources\V2\ProductMiniCollection;
use App\Http\Resources\V2\ProductDetailCollection;
use App\Http\Resources\V2\DigitalProductDetailCollection;
use App\Models\CustomerProduct;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use mysql_xdevapi\Exception;

class ProductApiController extends Controller
{
    public function import(Request $request)
    {
        if($request->hasFile('file')){
            $import = new ProductsImportApi;
            Excel::import($import, request()->file('file'));
        }
        return back();
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
            dd($e);
        }

    }

}

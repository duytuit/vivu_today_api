<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Traits\apiResponse;
use Illuminate\Http\Request;
use App\Models\BlogCategory;
use App\Models\Blog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    use apiResponse;
    public function index(Request $request)
    {
        $key_cache = $request->fullUrl();
        $result = Cache::remember($key_cache, 15, function () use($request) {
            $skip =  $request->get('page',1);
            $limit = $request->get('limit',10);
            $skip = ($skip * $limit) - $limit;
            return DB::table('blogs')->skip($skip)->take($limit)->get();
        });
        return $this->sendSuccessApi(['data'=>$result]);
    }

    public function show($id)
    {
        $result = DB::table('blogs')->with('blog_categories')->find($id);
        return $this->sendSuccessApi(['data'=>$result]);
    }
    public function getCategory(Request $request)
    {
        $count = DB::table('blog_categories')->select('id')->count();
        $skip =  $request->get('page',1);
        $limit = $request->get('limit',10);
        $skip = ($skip * $limit) - $limit;
        $result = DB::table('blog_categories')->skip($skip)->take($limit)->get();
        return $this->sendSuccessApi(['data'=>$result,'count'=>$count]);
    }

}

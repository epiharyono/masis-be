<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Crypt;
use Input;
use View;
use Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{


   static function GetData($req){
        $success = true; $message = 'Sukses Get Data';
        $query  = DB::table('ta_banner')->where('status',1)->orderby('created_at','desc');
        if($req->search){
            $query->where('judul','LIKE','%'.$req->search.'%');
        }
        $banner  = $query->paginate(3);

        $banner->transform(function ($value) {
            $sold = 0;
            $value->image = config('myconfig.BE_URL').'myfiles/'.$value->image;
            return $value;
        });

        $berita  = DB::table('ta_berita')->where('status',1)->orderby('created_at','desc')->paginate(5);
        $berita->transform(function ($value) {
            $sold = 0;
            $value->image = config('myconfig.BE_URL').'myfiles/'.$value->image;
            return $value;
        });



        return response()->json([
            'success' => $success,
            'message' => $message,
            'data'  => $banner,
            'berita'  => $berita,
        ], 200);

    }

    static function FindBySlugxxxx($slug){
         $success = true; $message = 'Sukses Get Data';
         $data  = DB::table('ta_banner')->where('slug',$slug)->first();

         return response()->json([
             'success' => $success,
             'message' => $message,
             'data'  => $data,
         ], 200);
    }
}

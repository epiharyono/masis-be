<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Crypt;
use Input;
use View;
use Str;
use Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class BeritaController extends Controller
{
   static function HomeData($req){
        $success = true; $message = 'Sukses Get Data';
        $query  = DB::table('ta_berita')->where('status',1)->orderby('created_at','desc');
        if($req->search){
            $query->where('judul','LIKE','%'.$req->search.'%');
        }
        $data  = $query->paginate(5);

        $data->transform(function ($value) {
            $sold = 0;
            $value->image = config('myconfig.BE_URL').'myfiles/'.$value->image;
            return $value;
        });

        return response()->json([
            'success' => $success,
            'message' => $message,
            'data'  => $data,
        ], 200);
    }

    static function GetData($req){
         $success = true; $message = 'Sukses Get Data';

         $query  = DB::table('ta_berita')->where('status',1);
         if($req->search){
             $query->where('judul','LIKE','%'.$req->search.'%');
         }
         $data  = $query->orderby('created_at','desc')->paginate(10);

         $data->transform(function ($value) {
             $sold = 0;
             $value->image = config('myconfig.BE_URL').'myfiles/'.$value->image;
             return $value;
         });
         return response()->json([
             'success' => $success,
             'message' => $message,
             'data'  => $data,
         ], 200);
     }

     static function GetRinc($slug){
          $success = false; $message = 'Sukses Get Data';
          $data  = DB::table('ta_berita')->where('slug',$slug)->first();
          if($data){
              $data->image = config('myconfig.BE_URL').'myfiles/'.$data->image;
              $success = true;
          }
          return response()->json([
              'success' => $success,
              'message' => $message,
              'data'  => $data,
          ], 200);
     }

}

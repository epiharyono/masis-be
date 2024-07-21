<?php

namespace App\Http\Controllers\Bernet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Crypt;
use Input;
use View;
use Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class BernetController extends Controller
{
    static function PostData($req){
         $success = false; $message = 'Sukses Get Data';

         try {
              $folder = date('Y/m/d');
              $files = $req->file('files');
              if($files){
                  $nomor = 0;
                  if(sizeOf($files) > 1) $slep = true;
                  else $slep = false;
                  foreach($files as $dat){
                      $date = date('mdYhis', time());
                      $file_name = $date . '.' . $dat->getClientOriginalExtension();
                      $dat->storeAs($folder, str_replace(' ', '_', $file_name),'my_files');
                      // $image = $folder.'/'.$file_name;
                      $nomor++;
                      if($slep){
                          if($nomor != sizeOf($files)) sleep(3);
                      }
                      // $nomor++;
                  }
                  $message =  $nomor.'  - '.sizeOf($files);
              }
              
         } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
         }

         return response()->json([
             'success' => $success,
             'message' => $message,
             'payload'  => $req->all()
         ], 200);


         if($req->is_login){
              if($req->id == 1) $name = 'EPI-HR';
              else $name = $req->name;

              try {
                   if ($req->hasFile('image')) {
                     $folder = date('Y/m/d');
                     $file_name = time() . '.' . $req->file('image')->getClientOriginalExtension();
                     $file = $req->file("image");
                     $file->storeAs($folder, str_replace(' ', '_', $file_name),'my_files');
                     $image = $folder.'/'.$file_name;
                   }else{
                     $image = '';
                   }
              } catch (\Exception $e) {
                 return response()->json(['success' => false, 'message' => $e->getMessage()]);
              }

              DB::table('ta_bernet')->insert([
                  'user_id' => $req->id,
                  'user_name' => $name,
                  'pesan' => $req->pesan,
                  'image' => $image,
              ]);
              $success = true;
         }
         return response()->json([
             'success' => $success,
             'message' => $message,
             'payload'  => $req->all()
         ], 200);
    }

    static function GetPostData($req){
        $data  = DB::table('ta_bernet')->where('status',1)->orderBy('updated_at','desc')->paginate(10);

        $data->transform(function ($value) {
            if($value->image){
                $value->image = config('myconfig.BE_URL').'myfiles/'.$value->image;
            }
            return $value;
        });

        return response()->json([
            'success' => true,
            'message' => 'Get Data',
            'data'  => $data,
            'payload'  => $req->all()
        ], 200);
    }

    static function GetPostRinc($id){
        $data  = DB::table('ta_bernet')->where('id',$id)->first();
        if($data){
            if($data->image){
                $data->image = config('myconfig.BE_URL').'myfiles/'.$data->image;
            }
            $success = true;
        }
        return response()->json([
            'success' => true,
            'message' => 'Get Data',
            'data'  => $data,
        ], 200);
    }

}

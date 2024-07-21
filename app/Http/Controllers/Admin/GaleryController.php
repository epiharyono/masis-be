<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Crypt;
use Input;
use View;
use Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


use App\Models\Order;
use App\Models\OrdedrRinc;

use App\Http\Controllers\Admin\HakAksesController as HALocal;

class GaleryController extends Controller
{

   static function GetData($req){
        $success = false; $message = 'Otoritas Tidak Diizinkan';
        $super   = 1;
        $user    = Auth::user();
        $ha = HALocal::HakAksesUser($user->nip,5);
        $success = true; $message = 'Sukses Get Data Users';
        $query  = DB::table('ta_galery')->orderby('created_at','desc');
        if($req->search){
            $query->where('keterangan','LIKE','%'.$req->search.'%');
        }
        $data  = $query->paginate(10);

        return response()->json([
            'success' => $success,
            'message' => $message,
            'data'  => $data,
            'otoritas'  => $ha,
            'req' => $req->all()
        ], 200);

    }

    static function AddData($req){
         $success = false; $message = 'Otoritas Tidak Diizinkan';
         $user  = Auth::user();
         $super = 1;
         $ha = HALocal::HakAksesUser($user->nip,5);
         if(!$ha['tambah']){
            return response()->json([
                'success' => false,
                'message' => $message.' '.$user->nip,
                'ha'  => $ha
            ]);
         }

        try {
            if ($req->hasFile('image')) {
              $folder = date('Y/m/d');
              $file_name = time() . '.' . $req->file('image')->getClientOriginalExtension();
              $file = $req->file("image");
              $file->storeAs($folder, str_replace(' ', '_', $file_name),'my_files');
              $image = $folder.'/'.$file_name;
            }else{
              return response()->json(['success' => false, 'message' => 'Foto tidak Diizinkan...']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

         $slug  =  bin2hex(random_bytes(5));
         try {
             $success = true; $message = 'Sukses Tambah Data';
             DB::table('ta_galery')->insert([
                'slug'  => $slug,
                'image'  => $image,
                'keterangan'  => $req->keterangan,
                'status'  => 0,
                'created_by'  => $user->name,
             ]);

         } catch (\Exception $e) {
            $message = $e->getMessage();
            $success = false;
         }

         return response()->json([
             'success' => $success,
             'message' => $message,
         ], 200);
    }

    static function FindBySlug($slug){
         $success = true; $message = 'Sukses Get Data';
         $data  = DB::table('ta_galery')->where('slug',$slug)->first();

         return response()->json([
             'success' => $success,
             'message' => $message,
             'data'  => $data,
         ], 200);
    }

    static function UpdateBySlug($slug,$req){
         $success = false; $message = 'Data Tidak Diupdate';
         $user   = Auth::user();
         $ha = HALocal::HakAksesUser($user->nip,5);
         if(!$ha['edit']){
            return response()->json([
                'success' => false,
                'message' => $message.' '.$user->nip,
                'ha'  => $ha
            ]);
         }

         try {
             if ($req->hasFile('image')) {
               $folder = date('Y/m/d');
               $file_name = time() . '.' . $req->file('image')->getClientOriginalExtension();
               $file = $req->file("image");
               $file->storeAs($folder, str_replace(' ', '_', $file_name),'my_files');
               $image = $folder.'/'.$file_name;
               DB::table('ta_galery')->where('slug',$req->slug)->update([
                  'image'  => $image,
                  'keterangan'  => $req->keterangan,
                  'status'  => $req->status,
                  'created_by'  => $user->name,
               ]);
             }else{
               DB::table('ta_galery')->where('slug',$req->slug)->update([
                  'keterangan'  => $req->keterangan,
                  'status'  => $req->status,
                  'created_by'  => $user->name,
               ]);
             }
             $success = true; $message = 'Sukses Update Data';
         } catch (\Exception $e) {
             return response()->json(['success' => false, 'message' => $e->getMessage()]);
         }

         return response()->json([
             'success' => $success,
             'message' => $message,
             'REQ'  => $req->all()
         ], 200);
    }

}

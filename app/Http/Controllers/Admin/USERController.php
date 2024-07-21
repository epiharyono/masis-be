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

class USERController extends Controller
{


   static function GetData($req){
        $success = false; $message = 'Otoritas Tidak Diizinkan';
        $super   = 1;
        $user    = Auth::user();

        $ha = HALocal::HakAksesUser($user->nip,1);
        if(!$ha['lihat']){
           return response()->json([
               'success' => false,
               'message' => $message.' '.$user->nip.' .',
               'ha'  => $ha
           ]);
        }

        $success = true; $message = 'Sukses Get Data Users';
        $userl  = HALocal::GetTableUser($user->nip);
        $query  = DB::table('ta_users')->orderby('id','desc');
        if($req->search){
            $query->where('nama','LIKE','%'.$req->search.'%');
        }
        if(!$super){
            $query->where('id_opd',$userl->id_opd);
        }
        $data  = $query->paginate(10);

        return response()->json([
            'success' => $success,
            'message' => $message,
            'data'  => $data,
            'ha'  => $ha,
            'req' => $req->all()
        ], 200);

    }

    static function AddData($req){
         $success = false; $message = 'Otoritas Tidak Diizinkan';
         $user  = Auth::user();
         $super = 1;
         $ha = HA::HakAksesUser(Auth::ID(),9);
         if(!$ha['tambah']){
             $super = 0;
             $ha = HALocal::HakAksesUser($user->nip,1);
             if(!$ha['tambah']){
                return response()->json([
                    'success' => false,
                    'message' => $message.' '.$user->nip,
                    'ha'  => $ha
                ]);
             }
         }

         $userl  = HALocal::GetTableUser($user->nip);
         $name   = DB::table('users')->where('nip',$req->nip)->where('id',$req->id)->value('name');
         if($super){
            $opd   = DB::table('ta_opd')->where('id',$req->id_opd)->first();
         }else{
            $opd   = DB::table('ta_opd')->where('id',$userl->id_opd)->first();
         }
         $slug  =  bin2hex(random_bytes(5));

         try {
             $success = true; $message = 'Sukses Tambah Data OPD';
             DB::table('ta_users')->insert([
                'nama'  => $name,
                'nip' => $req->nip,
                'slug'  => $slug,
                'id_opd'  => $opd->id,
                'slug_opd'  => $opd->slug,
                'nm_opd'  => $opd->nama,
                'otoritas'  => $req->otoritas,
                'status'  => $req->status,
                'created_by'  => $user->name,
             ]);

         } catch (\Exception $e) {
            $message = $e->getMessage();
            $success = false;
         }

         return response()->json([
             'success' => $success,
             'message' => $message,
             'slug' => $slug
         ], 200);
    }

    static function FindBySlug($slug){
         $success = true; $message = 'Sukses Get Data';
         $data  = DB::table('ta_users')->where('slug',$slug)->first();

         return response()->json([
             'success' => $success,
             'message' => $message,
             'data'  => $data,
         ], 200);
    }

    static function UpdateBySlug($slug,$req){
         $success = false; $message = 'Data Tidak Diupdate';
         $user   = Auth::user();
         $userl  = HALocal::GetTableUser($user->nip);

         $data  = DB::table('ta_users')->where('slug',$slug)->where('id',$req->id)->update([
            'status'  => $req->status,
         ]);

         if($data){
            $success = true; $message = 'Sukses Update Data';
         }

         return response()->json([
             'success' => $success,
             'message' => $message,
             'REQ'  => $req->all()
         ], 200);
    }

}

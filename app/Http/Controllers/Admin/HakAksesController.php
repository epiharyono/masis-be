<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Auth;


class HakAksesController extends Controller
{
    static function GetDataHAUser($slug){
        $user  = Auth::user();
        $admin = self::HakAksesUser($user->nip,1);
        if(!$admin['lihat']){
            $message = 'Otoritas Tidak Diizinkan!';
            return response()->json([
                'success' => false,
                'message' => $message,
                'admin' => $admin,
            ]);
        }

        $nip  = DB::table('ta_users')->where('slug',$slug)->value('nip');
        $ref  = DB::table('ref_otoritas')->get();
        foreach($ref as $dat){
            if($dat->status == 0){
                DB::table('ta_otoritas')->where('id_ref',$dat->id)->delete();
            }else{
                $ha  = DB::table('ta_otoritas')->where('id_ref',$dat->id)->where('nip',$nip)->first();
                if(!$ha){
                    DB::table('ta_otoritas')->insert([
                        'nip'  => $nip,
                        'id_ref'  => $dat->id,
                        'id_opd'  => 0,
                        'keterangan'  => $dat->keterangan,
                        'lihat'  => 0,
                        'tambah'  => 0,
                        'edit'  => 0,
                        'hapus'  => 0,
                    ]);
                }else{
                    DB::table('ta_otoritas')->where('id_ref',$dat->id)->update([
                        'keterangan'  => $dat->keterangan,
                    ]);
                }
            }
        }

        $data = DB::table('ta_otoritas')->where('nip',$nip)->get();
        return [
          'success' => true,
          'data'  => $data,
          'ha'  => $admin,
        ];
    }

    static function HakAksesUser($nip,$id_ref){
        $lihat = 0; $tambah = 0; $edit = 0; $hapus = 0;
        $cek  = DB::table('ta_otoritas')->where('nip',$nip)->where('id_ref',$id_ref)->first();
        if($cek){
            $lihat = $cek->lihat; $tambah = $cek->tambah;
            $edit = $cek->edit; $hapus = $cek->hapus;
        }
        return [
          'lihat' => $lihat,
          'tambah'  => $tambah,
          'edit'  => $edit,
          'hapus' => $hapus,
        ];
    }

    static function UpdateHAUser($table,$req){
        $user  = Auth::user();
        $admin = self::HakAksesUser($user->nip,1);
        if(!$admin['edit']){
            $message = 'Otoritas Tidak Diizinkan';
            return response()->json([
                'success' => false,
                'message' => $message,
            ]);
        }

        $cek  = DB::table('ta_otoritas')->where('id',$req->id)->first();
        if($cek){
            $status = 0;
            if($table == 'lihat'){
                if($req->lihat == 0) $status = 1;
                DB::table('ta_otoritas')->where('id',$req->id)->update([
                    'lihat' => $status,
                ]);
            }elseif($table == 'tambah'){
                if($req->tambah == 0) $status = 1;
                DB::table('ta_otoritas')->where('id',$req->id)->update([
                    'tambah' => $status,
                ]);
            }elseif($table == 'edit'){
                if($req->edit == 0) $status = 1;
                DB::table('ta_otoritas')->where('id',$req->id)->update([
                    'edit' => $status,
                ]);
            }elseif($table == 'hapus'){
                if($req->hapus == 0) $status = 1;
                DB::table('ta_otoritas')->where('id',$req->id)->update([
                    'hapus' => $status,
                ]);
            }
        }
        return [
          'success' => true,
          'req' => $req->all()
        ];
    }

    static function GetTableUser($nip){
        $data = DB::table('ta_users')->where('status',1)->where('nip',$nip)->first();
        if(!$data){
            $json = '{
                "id": 0,
                "slug": "",
                "id_opd": 0,
                "nm_opd": "",
                "status": 0,
                "otoritas": 0,
                "hobi": [
                  "Berenang", "Berlari", "Bertamasya"
                ]
            }';
            $data = json_decode($json);

        }
        return $data;
    }

}

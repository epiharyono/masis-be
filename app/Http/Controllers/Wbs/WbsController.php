<?php

namespace App\Http\Controllers\Wbs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Crypt;
use Input;
use View;
use Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Models\Wbs as MWbs;

use App\Http\Controllers\Admin\HakAksesController as HALocal;

use GuzzleHttp\Client as GuzzleHttpClient;

class WbsController extends Controller
{
    static function SaveOtp($req){
        $otp = $req->data['otp'];
        $hp  = $req->data['hp'];
        $tanggal = date('Y-m-d');
        DB::table('wbs_otp')->where('hp',$hp)->where('tanggal',$tanggal)->delete();
        DB::table('wbs_otp')->insert([
            'tanggal' => $tanggal,
            'hp'  => $hp,
            'otp' => $otp,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Sukses Simpan Data',
        ], 200);
    }

    static function GetOtp($req){
        $otp = $req->data['otp'];
        $hp  = $req->data['hp'];
        $tanggal = date('Y-m-d');
        $cek  = DB::table('wbs_otp')->where('hp',$hp)->where('otp',$otp)->where('tanggal',$tanggal)->first();
        if($cek){
            $success = true;
            $message = 'OTP Anda Benar';
        }else{
            $success = false; $message = 'OTP Anda Salah';
        }
        return response()->json([
            'success' => $success,
            'message' => $message,
            // 'otp' => $otp,
            // 'hp' => $hp,
            'payload'  => $req->all(),
        ], 200);
    }

    static function SaveReport($req){
        $success = false; $message = 'Gagal Simpan Laporan.';
        try {
            DB::table('wbs')->insert([
                'tanggal' => date('Y-m-d'),
                'nama'  => $req->data['nama'],
                'email'  => $req->data['email'],
                'hp'  => $req->data['hp'],
                'instansi'  => $req->data['instansi'],
                'judul'  => $req->data['judul'],
                'uraian'  => $req->data['uraian'],
                'files'  => $req->data['files'],
            ]);
            $success = true;
            $message = 'Laporan Anda Berhasil Dikirim';
            return response()->json([
                'success' => $success,
                'message' => $message,
                'payload'  => $req->all(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'payload' => $req->all() ]);
        }
    }

    static function GetData($req){
        $user  = Auth::user();
        if(!$user) return self::LoginFalse();
        $ha = HALocal::HakAksesUser($user->nip,4);
        if(!$ha['lihat']){
           return response()->json([
               'success' => false,
               'message' => 'Otoritas Tidak Diizinkan'.' '.$user->nip,
               'ha'  => $ha
           ]);
        }

        $success = false; $message = 'Gagal Get Data.';
        try {
            $data = DB::table('wbs')->orderBy('tanggal','desc')->paginate(15);
            $success = true;
            $message = 'Sukses Get Data';
            return response()->json([
                'success' => $success,
                'message' => $message,
                'data'  => $data,
                'ha'  => $ha,
                'payload'  => $req->all(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'payload' => $req->all() ]);
        }
    }

    static function GetRinc($id){
        $user  = Auth::user();
        if(!$user) return self::LoginFalse();
        $ha = HALocal::HakAksesUser($user->nip,4);
        if(!$ha['lihat']){
           return response()->json([
               'success' => false,
               'message' => 'Otoritas Tidak Diizinkan'.' '.$user->nip,
               'ha'  => $ha
           ]);
        }

        $success = false; $message = 'Gagal Get Data.';
        try {
            $data = MWbs::where('id',$id)->with('respond')->first();
            if($data->files) $data->files = config('myconfig.BE_URL').'myfiles/'.$data->files;
            $success = true;
            $message = 'Sukses Get Data';
            return response()->json([
                'success' => $success,
                'message' => $message,
                'data'  => $data,
                'ha'  => $ha,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    static function SaveRespond($req){
        $user  = Auth::user();
        if(!$user) return self::LoginFalse();
        $ha = HALocal::HakAksesUser($user->nip,4);
        if(!$ha['tambah']){
           return response()->json([
               'success' => false,
               'message' => 'Otoritas Tidak Diizinkan'.' '.$user->nip,
               'ha'  => $ha
           ]);
        }

        $validator = Validator::make($req->all(), [
            'nama'     => 'required',
            'hp'  => 'required|numeric|starts_with:62',
            'uraian'  => 'required',
        ], [
            '*.required' => ':attribute Wajib diisi',
            '*.numeric' => 'HP Wajib Angka',
            'hp.starts_with' => 'HP diawali dengan 628xxxx',
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false, 'kd_wa'=>false, 'message'=>$validator->errors()->first()], 200);
        }

        $hp = $req->hp;
        $success = false; $message = 'Sukses Tambah Data.';
        $tanggal = date('Y-m-d');
        try {
            if(!$req->kd_wa){
                $otps = rand(1231,7879);
                $pesan = '*WA OTOMATIS*';
                $pesan .= '\n';
                $pesan .= '\n';
                $pesan .= '*'.$otps.'* merupakan Kode OTP Whatsapp untuk Menanggapi Pengaduan di System Whistleblowing Kab. Anambas.';
                $pesan .= '\n';
                $pesan .= '\n';
                $pesan .= 'OTP ini rahasia, mohon tidak diberikan kepada siapapun.';
                $pesan .= '\n';
                $pesan .= '*Terimakasih*';
                $pesan .= '\n';
                $pesan .= '\n';
                $pesan .= '\n';
                $pesan .= '\n'.$req->uraian;

                $kode = self::SendMessage($hp,$pesan);
                DB::table('wbs_otp')->where('hp',$hp)->where('tanggal',$tanggal)->delete();
                DB::table('wbs_otp')->insert([
                    'tanggal' => $tanggal,
                    'hp'  => $hp,
                    'otp' => $otps,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Silahkan Input Kode WA',
                    'kd_wa' => true,
                    'payload'  => $req->all(),
                ], 200);
            }else{
                $cek = DB::table('wbs_otp')->where('otp',$req->kd_wa)->where('hp',$hp)->where('tanggal',$tanggal)->first();
                if(!$cek){
                    return response()->json([
                        'success' => false,
                        'message' => 'Kode WA Salah',
                        'kd_wa' => true,
                        'payload'  => $req->all(),
                    ], 200);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }


        try {
            $cek = DB::table('wbs')->where('id',$req->id_wbs)->first();
            if($cek){
                $otps = rand(1231,7879);
                $pesan = '*WA OTOMATIS*';
                $pesan .= '\n';
                $pesan .= '\n';
                $pesan .= 'Halo '.$cek->nama;
                $pesan .= '\n';
                $pesan .= 'Berikut tanggapan Atas Pengaduan Saudara terkait '.$cek->judul;
                $pesan .= '\n';
                $pesan .= '\n';
                $pesan .= '\n'.$req->uraian;
                $pesan .= '\n';
                $pesan .= '\n';
                $pesan .= '*Terimakasih*';
                $kode = self::SendMessage($cek->hp,$pesan);

                $data = DB::table('wbs_respond')->insert([
                    'tanggal' => $tanggal,
                    'nama'  => $req->nama,
                    'hp'  => $req->hp,
                    'wbs_id'  => $cek->id,
                    'uraian'  => $req->uraian,
                    'created_by'  => $user->name,
                    'updated_by'  => $user->name,
                ]);
                $success = true;
            }
            return response()->json([
                'success' => $success,
                'message' => $message,
                'payload'  => $req->all(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }



    static function LoginFalse(){
        return response()->json([
            'success' => false,
            'message' => 'Login Tidak Ditemukan',
        ], 200);
    }


    static function SendMessage($hp,$pesan){
          $client = new GuzzleHttpClient();
          $url    = 'http://103.76.26.93:3001/api/v1/messages';
          $url    = 'http://103.76.26.94:3001/api/v1/messages';

$pesan = str_replace('\n','
',$pesan);

          $apiRequest = $client->request('POST', $url,[
              'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer uf5f945da1f444e4.f1df22788cd94a3f8947813ffbe63498'
              ],
              'verify' => false,
              'body' => json_encode([
                  'recipient_type'  => 'individual',
                  'to'  => $hp,
                  'type'  => 'text',
                  'text'  => [
                    'body'  => $pesan,
                  ],
                  'message'  => $pesan,
                  'wait_until_send' => 0,
              ])
          ]);
          $content = json_decode($apiRequest->getBody()->getContents());
          if($content->code == '200'){
              // DB::table('webhook_onesender')->where('id',$id)->update([
              //     'replay'  => 1,
              // ]);
          }

          return $content;
    }

}

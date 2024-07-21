<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Hash;
use App\User;
use Input;
use Response;
use Auth;
use Crypt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use App\Http\Controllers\Api\BannerController as Banner;
use App\Http\Controllers\Api\BeritaController as Berita;
use App\Http\Controllers\Api\GaleryController as Galery;

class RouteController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login','register','refresh','logout']]);
    }

    public function index(Request $req) {

        return response()->json([
            'success' => false,
            'message' => 'Gagal Request Data',
        ], 200);
    }

    public function IndexRouteSatu(Request $req, $satu) {

        if($satu == 'testing'){
            $token = $req->bearerToken();

            // return $token;
            try {
                $jwtParts = explode('.', $token);
                if (empty($header = $jwtParts[0]) || empty($payload = $jwtParts[1]) || empty($jwtParts[2])) {
                    return 'No JWT';
                }
            } catch (Throwable $e) {
                return 'Provided JWT is invalid. '.$e;
            }

            if (
                !($header = base64_decode($header))
                || !($payload = base64_decode($payload))
            ) {
                return 'Provided JWT can not be decoded from base64.';
            }

            if (
                empty(($header = json_decode($header, true)))
                || empty(($payload = json_decode($payload, true)))
            ) {
                return 'Provided JWT can not be decoded from JSON.';
            }

            $tokenParts = explode(".", $token);
            $tokenHeader = base64_decode($tokenParts[0]);
            $tokenPayload = base64_decode($tokenParts[1]);
            $tokenPayload = json_decode($tokenPayload, true);
            return $tokenPayload['user_id'];
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal Request Data.',
        ], 200);

    }

    public function IndexRouteDua(Request $req, $satu,$dua) {
        if($satu == 'banner' && $dua == 'get-data'){
            return Banner::GetData($req);
        }

        elseif($satu == 'berita' && $dua == 'home-data'){
            return Berita::HomeData($req);
        }
        elseif($satu == 'berita' && $dua == 'get-data'){
            return Berita::GetData($req);
        }
        elseif($satu == 'berita-rinc'){
            return Berita::GetRinc($dua);
        }

        elseif($satu == 'galery' && $dua == 'home-data'){
            return Galery::HomeData($req);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal Request Data..',
        ], 200);

    }

    public function IndexRouteTiga(Request $req, $satu,$dua,$tiga) {

        return response()->json([
            'success' => false,
            'message' => 'Gagal Request Data ...',
        ], 200);

    }
}

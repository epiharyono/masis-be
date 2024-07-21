<?php

namespace App\Http\Controllers\Admin;

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

use App\Http\Controllers\Admin\USERController as USERC;
use App\Http\Controllers\Admin\BannerController as Banner;
use App\Http\Controllers\Admin\BeritaController as Berita;
use App\Http\Controllers\Admin\GaleryController as Galery;
use App\Http\Controllers\Admin\HakAksesController as HALocal;

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
            'auth'  => Auth::user(),
            'req' => $req->all()
        ], 200);
    }

    public function IndexRouteSatu(Request $req, $satu) {

        return response()->json([
            'success' => false,
            'message' => 'Gagal Request Data.',
        ], 200);

    }

    public function IndexRouteDua(Request $req, $satu,$dua) {
        if($satu == 'users' && $dua == 'get-all'){
            return USERC::GetData($req);
        }

        elseif($satu == 'banner' && $dua == 'add'){
            return Banner::AddData($req);
        }
        elseif($satu == 'banner' && $dua == 'get-data'){
            return Banner::GetData($req);
        }

        elseif($satu == 'berita' && $dua == 'add'){
            return Berita::AddData($req);
        }
        elseif($satu == 'berita' && $dua == 'get-data'){
            return Berita::GetData($req);
        }

        elseif($satu == 'galery' && $dua == 'get-data'){
            return Galery::GetData($req);
        }
        elseif($satu == 'galery' && $dua == 'add'){
            return Galery::AddData($req);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal Request Data..',
        ], 200);

    }

    public function IndexRouteTiga(Request $req, $satu,$dua,$tiga) {
        if($satu == 'users' && $dua == 'find-by-slug'){
            return USERC::FindBySlug($tiga);
        }

        elseif($satu == 'user' && $dua == 'get-ha-by-slug'){
            return HALocal::GetDataHAUser($tiga);
        }

        elseif($satu == 'user' && $dua == 'update-data'){
            return USERC::UpdateBySlug($tiga,$req);
        }
        elseif($satu == 'user' && $dua == 'update-hak-akses'){
            return HALocal::UpdateHAUser($tiga,$req);
        }

        elseif($satu == 'banner' && $dua == 'find-by-slug'){
            return Banner::FindBySlug($tiga);
        }
        elseif($satu == 'banner' && $dua == 'update-by-slug'){
            return Banner::UpdateBySlug($tiga,$req);
        }

        elseif($satu == 'berita' && $dua == 'find-by-slug'){
            return Berita::FindBySlug($tiga);
        }
        elseif($satu == 'berita' && $dua == 'update-by-slug'){
            return Berita::UpdateBySlug($tiga,$req);
        }

        elseif($satu == 'galery' && $dua == 'find-by-slug'){
            return Galery::FindBySlug($tiga);
        }
        elseif($satu == 'galery' && $dua == 'update-by-slug'){
            return Galery::UpdateBySlug($tiga,$req);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal Request Data ...',
        ], 200);

    }
}

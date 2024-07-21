<?php

namespace App\Http\Controllers\Bernet;

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

use App\Http\Controllers\Bernet\BernetController as Bernet;

class RouteController extends Controller
{
    public function index(Request $req) {

        return response()->json([
            'success' => false,
            'message' => 'Gagal Request Data',
        ], 200);
    }

    public function IndexRouteSatu(Request $req, $satu) {
        if($satu == 'posting'){
            return Bernet::PostData($req);
        }
        elseif($satu == 'get-post'){
            return Bernet::GetPostData($req);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal Request Data.',
        ], 200);

    }

    public function IndexRouteDua(Request $req, $satu,$dua) {
        if($satu == 'get-post-rinc'){
            return Bernet::GetPostRinc($dua);
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

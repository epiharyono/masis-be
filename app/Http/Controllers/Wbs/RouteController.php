<?php

namespace App\Http\Controllers\Wbs;

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

use App\Http\Controllers\Wbs\WbsController as Wbs;

class RouteController extends Controller
{
    public function index(Request $req) {

        return response()->json([
            'success' => false,
            'message' => 'Gagal Request Data',
            'payload' => $req->all()
        ], 200);
    }

    public function IndexRouteSatu(Request $req, $satu) {

        if($satu == 'testing'){
            $token = $req->bearerToken();
            $tokenParts = explode(".", $token);
            $tokenHeader = base64_decode($tokenParts[0]);
            $tokenPayload = base64_decode($tokenParts[1]);
            $tokenPayload = json_decode($tokenPayload, true);
            return 'user id adalah '.$tokenPayload['sub'];
        }

        if($satu == 'save-otp'){
            return Wbs::SaveOtp($req);
        }
        elseif($satu == 'get-otp'){
            return Wbs::GetOtp($req);
        }
        elseif($satu == 'save-report'){
            return Wbs::SaveReport($req);
        }
        elseif($satu == 'get-data'){
            return Wbs::GetData($req);
        }
        elseif($satu == 'save-respond'){
            return Wbs::SaveRespond($req);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal Request Data.',
            'payload' => $req->all()
        ], 200);

    }

    public function IndexRouteDua(Request $req, $satu,$dua) {
        if($satu == 'get-rinc'){
            return Wbs::GetRinc($dua);
        }
        return response()->json([
            'success' => false,
            'message' => 'Gagal Request Data..',
            'payload' => $req->all()
        ], 200);

    }

    public function IndexRouteTiga(Request $req, $satu,$dua,$tiga) {

        return response()->json([
            'success' => false,
            'message' => 'Gagal Request Data ...',
            'payload' => $req->all()
        ], 200);

    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\JWTAuth;
use GuzzleHttp\Client;

class CryptoController extends Controller
{
    public function getCryptoNews(Request $request) {
        $client = new CoinGeckoClient();
        $status = $client->ping();
        $result['news'] = [];
        $result['status'] = 'error';

        if(isset($status) && !is_null($status)) {
            $result['news'] = $client->statusUpdates()->getStatusUpdates();
            $result['status'] = 'success';
        }

        return response($result, 200);
    }

    public function getCryptoPrice(Request $request) {
        $client = new CoinGeckoClient();
        $status = $client->ping();
        $result['crypto'] = [];
        $result['status'] = 'error';

        if(isset($status) && !is_null($status)) {
            $result['crypto'] = $client->coins()->getMarkets('usd');
            $result['status'] = 'success';
        }

        return response($result, 200);
    }
}

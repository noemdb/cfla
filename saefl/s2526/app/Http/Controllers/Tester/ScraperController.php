<?php

namespace App\Http\Controllers\Tester;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
use Weidner\Goutte\GoutteFacade;

use GuzzleHttp\Client as GuzzleClient;

class ScraperController extends Controller
{
    public function index()
    {
        $url = "https://www.bcv.org.ve";
        // $url = "https://www.google.com";

        $client = new Client(HttpClient::create(['verify_peer' => false, 'verify_host' => false]));        
        
        $website = $client->request('GET', $url); //dd($website);
        $centrado = null;
        $value = null;

        $centrados = $website->filter('#dolar .centrado')->each(function ($node) {
            return $node->text();
        });
        $value = str_replace(',','.',$centrados[0]);
        dd($value);



        dd('filtered',$value);


        return $website->html();
        
        // return $website->html();
    }
}

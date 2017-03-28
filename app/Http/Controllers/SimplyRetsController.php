<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class SimplyRetsController extends Controller
{

    public function index() {
        return view('SimplyRets.index');
    }

    public function load_data($id)
    {
        $details = $this->simplyrets('/properties/'.urlencode($id));
        echo json_encode(array(
                'success' => true,
                'data' => $details
            ));
        return;
    }

    private function simplyrets($url)
    {
        $url = 'https://api.simplyrets.com'.$url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "simplyrets:simplyrets");
        $out = curl_exec($ch);
        curl_close($ch);
        return json_decode($out);
    }
}

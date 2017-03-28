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
}

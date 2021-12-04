<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected function convertToMessageList($errors) {
        //Чтобы обойти ограничение на приватность поля
        $messages = [];
        foreach(json_decode($errors) as $key => $value) {
            $messages[$key] = implode("\n", $value);
        }
        return $messages;
    }

    protected function getUserId(Request $request) {
        $token = $request->header("Authorization");
        $payload = explode(".", $token)[1];
        $id = json_decode(base64_decode($payload))->id;
        return $id;
    }
}

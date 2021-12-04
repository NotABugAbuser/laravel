<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use App\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function logout(Request $request) {
        return response()->json([], 200);
    }

    private function buildToken($user) {
        $header = base64_encode(json_encode([
            "alg" => "SH256",
            "typ" => "JWT"
        ]));
        $payload = base64_encode(json_encode([
            "id" => $user->id,
            "nickname" => $user->nickname
        ]));
        $signature = hash("sha256", $header . '.' . $payload);
        return $header . '.' . $payload . '.' . $signature;
    }

    public function username() {
        return 'nickname';
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            "nickname" => "required",
            "password" => "required"
        ],[
            "nickname.required" => "Поле Никнейм не заполнено",
            "password.required" => "Поле Пароль не заполнено"
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach(json_decode($validator->errors()) as $field => $messages) {
                $errors[$field] = implode("\n", $messages);
            }
            return response()->json($errors, 422);
        }
        $users = User::where('nickname', $request['nickname'])->where('password', $request["password"])->get();
        if (count($users) > 0) {
            $token = $this->buildToken($users[0]);
            return response()->json(["token" => $token],200);
        } else {
            return response()->json(["login" => "Неверный логин или пароль"], 403);
        }
    }
}

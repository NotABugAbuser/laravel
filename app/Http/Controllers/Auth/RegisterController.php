<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    protected function signup(Request $request) {
        $validator = Validator::make($request->all(), [
            "first_name" => "required",
            "nickname" => "required | max:11 | min:11 | not_regex:/[^0-9]/ | unique:users,nickname",
            "surname" => "required",
            "password" => "required",
        ], [
            "first_name.required" => "Поле Имя не заполнено.",
            "surname.required" => "Поле Фамилия не заполнено.",
            "nickname.required" => "Поле Никнейм не заполнено.",
            "password.required" => "Поле Пароль не заполнено.",
            "nickname.max" => "Поле Никнейм должно состоять не более, чем из 11 символов.",
            "nickname.min" => "Поле Никнейм должно состоять хотя бы из 11 символов.",
            "nickname.unique" => "Такой Никнейм уже есть в базе данных.",
            "nickname.not_regex" => "Поле Никнейм должно состоять только из цифр.",
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach (json_decode($validator->errors()) as $key => $value) {
                $errors[$key] = implode("\n", $value);
            }
            return response()->json($errors, 422);
        } else {
            $user = User::create([
                "first_name" => "2",
                "second_name" => $request["second_name"],
                "surname" => $request["surname"],
                "nickname" => $request["nickname"],
                "password" => $request["password"],
            ]);
            return response($user->id, 201);
        }
    }
}

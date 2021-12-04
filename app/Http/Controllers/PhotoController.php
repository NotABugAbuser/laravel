<?php

namespace App\Http\Controllers;

use App\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PhotoController extends Controller
{
    public function upload(Request $request) {
        $title = $request["title"];
        if (!$title) {
            $title = "Default";
        }
        $validator = Validator::make($request->all(), [
            "photo" => "required | mimes:png,jpg,jpeg"
        ], [
            "required" => "Файл не выбран или поврежден",
            "mimes" => "Файл должен иметь расширение jpg, png или jpeg"
        ]);
        if ($validator->fails()) {
            $messages = $this->convertToMessageList($validator->errors());
            return response()->json($messages, 422);
        }
        $name = time() . '.png';
        $request->file("photo")->storeAs("photos", $name, "photo");
        $url = "http://laravel/photos/" . $name;
        $photo = Photo::create([
            "title" => $title,
            "owner_id" => $this->getUserId($request),
            "url" => $url,
            "users" => json_encode([])
        ]);
        $simplePhoto = [
            "id" => $photo->id,
            "title" => $photo->title,
            "url" => $photo->url,
        ];
        return response()->json($simplePhoto, 201);
    }

    public function update($id, Request $request) {
        $photo = Photo::find($id);
        if ($photo->owner_id != $this->getUserId($request)) {
            return response()->json([],403);
        }

        $jsonRequest = json_decode(file_get_contents("php://input"));
        $validator = Validator::make((array)$jsonRequest, [
            "photo" => "required"
        ], [
            "required" => "Файл не выбран или поврежден"
        ]);

        if ($validator->fails()) {
            $messages = $this->convertToMessageList($validator->errors());
            return response()->json($messages, 422);
        }

        if (!$jsonRequest->title) {
            $jsonRequest->title = "Default";
        }

        $name = time() . ".png";
        $url = "http://laravel/photos/" . $name;
        $image = base64_decode(preg_replace("/.*base64,/", "", $jsonRequest->photo));

        Storage::disk("photo")->put("photos/" . $name, $image);

        $photo->url = $url;
        $photo->title = $jsonRequest->title;
        $photo->save();

        $simplePhoto = [
            "id" => $photo->id,
            "url" => $photo->url,
            "title" => $photo->title,
        ];
        return response()->json($simplePhoto,200);
    }
}

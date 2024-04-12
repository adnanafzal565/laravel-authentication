<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use DB;
use Storage;
use App\Models\User;

class AdminController extends Controller
{
    public function fetch_smtp_settings()
    {
        $admin = $this->auth();
        $smtp_setting = DB::table("smtp_settings")->first();

        return response()->json([
            "status" => "success",
            "message" => "Data has been fetched.",
            "smtp_setting" => $smtp_setting
        ]);
    }

    public function save_smtp_settings()
    {
        $admin = $this->auth();
        $host = request()->host ?? "";
        $port = request()->port ?? "";
        $encryption = request()->encryption ?? "";
        $username = request()->username ?? "";
        $password = request()->password ?? "";
        $from = request()->from ?? "";
        $from_name = request()->from_name ?? "";

        $smtp_setting = DB::table("smtp_settings")->first();
        if ($smtp_setting == null)
        {
            DB::table("smtp_settings")
                ->insertGetId([
                    "host" => $host,
                    "port" => $port,
                    "encryption" => $encryption,
                    "username" => $username,
                    "password" => $password,
                    "from" => $from,
                    "from_name" => $from_name,
                    "created_at" => now(),
                    "updated_at" => now()
                ]);
        }
        else
        {
            DB::table("smtp_settings")
                ->where("id", "=", $smtp_setting->id)
                ->update([
                    "host" => $host,
                    "port" => $port,
                    "encryption" => $encryption,
                    "username" => $username,
                    "password" => $password,
                    "from" => $from,
                    "from_name" => $from_name,
                    "updated_at" => now()
                ]);
        }

        return response()->json([
            "status" => "success",
            "message" => "Settings has been saved."
        ]);
    }

    private function auth()
    {
        if (auth()->user()->type != "super_admin")
        {
            return response()->json([
                "status" => "error",
                "message" => "Un-authorized."
            ])->throwResponse();
        }
    }

    public function login()
    {
        $validator = Validator::make(request()->all(), [
            "email" => "required",
            "password" => "required"
        ]);

        if (!$validator->passes() && count($validator->errors()->all()) > 0)
        {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()->all()[0]
            ]);
        }

        $email = request()->email ?? "";
        $password = request()->password ?? "";

        $user = User::where("email", "=", $email)->first();

        if ($user == null)
        {
            return response()->json([
                "status" => "error",
                "message" => "Email does not exist."
            ]);
        }

        if (!password_verify($password, $user->password))
        {
            return response()->json([
                "status" => "error",
                "message" => "In-correct password."
            ]);
        }

        if (is_null($user->email_verified_at))
        {
            return response()->json([
                "status" => "error",
                "message" => "Email not verified."
            ]);
        }

        if ($user->type != "super_admin")
        {
            return response()->json([
                "status" => "error",
                "message" => "Un-authorized."
            ]);
        }

        $token = $user->createToken($this->admin_token_secret)->plainTextToken;

        return response()->json([
            "status" => "success",
            "message" => "Login successfully.",
            "access_token" => $token
        ]);
    }
}

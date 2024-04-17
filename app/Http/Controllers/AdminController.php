<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use DB;
use Storage;
use App\Models\User;

class AdminController extends Controller
{
    public function add_user()
    {
        $validator = Validator::make(request()->all(), [
            "name" => "required",
            "email" => "required",
            "password" => "required",
            "type" => "required"
        ]);

        if (!$validator->passes() && count($validator->errors()->all()) > 0)
        {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()->all()[0]
            ]);
        }

        $admin = $this->auth();
        $name = request()->name ?? "";
        $email = request()->email ?? "";
        $password = request()->password ?? "";
        $type = request()->type ?? "";

        $user = DB::table("users")
            ->where("email", "=", $email)
            ->first();

        if ($user != null)
        {
            return response()->json([
                "status" => "error",
                "message" => "User already exists."
            ]);
        }

        DB::table("users")
            ->insertGetId([
                "name" => $name,
                "email" => $email,
                "password" => password_hash($password, PASSWORD_DEFAULT),
                "type" => $type,
                "email_verified_at" => now()->utc(),
                "created_at" => now()->utc(),
                "updated_at" => now()->utc()
            ]);

        return response()->json([
            "status" => "success",
            "message" => "User has been added."
        ]);
    }

    public function change_user_password()
    {
        $validator = Validator::make(request()->all(), [
            "id" => "required",
            "password" => "required"
        ]);

        if (!$validator->passes() && count($validator->errors()->all()) > 0)
        {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()->all()[0]
            ]);
        }

        $admin = $this->auth();
        $id = request()->id ?? 0;
        $password = request()->password ?? "";

        $user = DB::table("users")
            ->where("id", "=", $id)
            ->whereNull("deleted_at")
            ->first();

        if ($user == null)
        {
            return response()->json([
                "status" => "error",
                "message" => "User not found."
            ]);
        }

        DB::table("users")
            ->where("id", "=", $user->id)
            ->update([
                "password" => password_hash($password, PASSWORD_DEFAULT),
                "updated_at" => now()->utc()
            ]);

        return response()->json([
            "status" => "success",
            "message" => "Password has been set."
        ]);
    }

    public function update_user()
    {
        $validator = Validator::make(request()->all(), [
            "id" => "required",
            "name" => "required",
            "type" => "required"
        ]);

        if (!$validator->passes() && count($validator->errors()->all()) > 0)
        {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()->all()[0]
            ]);
        }

        $admin = $this->auth();
        $id = request()->id ?? 0;
        $name = request()->name ?? "";
        $type = request()->type ?? "";

        $user = DB::table("users")
            ->where("id", "=", $id)
            ->whereNull("deleted_at")
            ->first();

        if ($user == null)
        {
            return response()->json([
                "status" => "error",
                "message" => "User not found."
            ]);
        }

        DB::table("users")
            ->where("id", "=", $user->id)
            ->update([
                "name" => $name,
                "type" => $type,
                "updated_at" => now()->utc()
            ]);

        return response()->json([
            "status" => "success",
            "message" => "User has been updated."
        ]);
    }

    public function fetch_single_user()
    {
        $validator = Validator::make(request()->all(), [
            "id" => "required"
        ]);

        if (!$validator->passes() && count($validator->errors()->all()) > 0)
        {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()->all()[0]
            ]);
        }

        $admin = $this->auth();
        $id = request()->id ?? 0;

        $user = DB::table("users")
            ->where("id", "=", $id)
            ->first();

        if ($user == null)
        {
            return response()->json([
                "status" => "error",
                "message" => "User not found."
            ]);
        }

        return response()->json([
            "status" => "success",
            "message" => "Data has been fetched.",
            "user" => [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "type" => $user->type ?? "user"
            ]
        ]);
    }

    public function delete_user()
    {
        $validator = Validator::make(request()->all(), [
            "id" => "required"
        ]);

        if (!$validator->passes() && count($validator->errors()->all()) > 0)
        {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()->all()[0]
            ]);
        }

        $admin = $this->auth();
        $id = request()->id ?? 0;

        $user = DB::table("users")
            ->where("id", "=", $id)
            ->first();

        if ($user == null)
        {
            return response()->json([
                "status" => "error",
                "message" => "User not found."
            ]);
        }

        DB::table("users")
            ->where("id", "=", $user->id)
            ->update([
                "deleted_at" => now()->utc()
            ]);

        return response()->json([
            "status" => "success",
            "message" => "User has been deleted."
        ]);
    }

    public function fetch_users()
    {
        $admin = $this->auth();
        $time_zone = request()->time_zone ?? "";
        if (!empty($time_zone))
        {
            $date_time_zone = new \DateTimeZone($time_zone);
        }

        $users = DB::table("users")
            ->whereNull("deleted_at")
            ->orderBy("id", "desc")
            ->paginate();

        $users_arr = [];
        foreach ($users as $user)
        {
            if (!empty($time_zone))
            {
                $date_time = new \DateTime($user->created_at);
                $date_time->setTimezone($date_time_zone);
                $user->created_at = $date_time->format("d M, Y h:i:s a");
            }

            array_push($users_arr, [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "profile_image" => url("/storage/" . $user->profile_image),
                "type" => $user->type,
                "created_at" => $user->created_at,
                "test" => now()->utc()
            ]);
        }

        return response()->json([
            "status" => "success",
            "message" => "Data has been fetched.",
            "users" => $users_arr
        ]);
    }

    public function fetch_settings()
    {
        $admin = $this->auth();
        $settings = DB::table("settings")->get();
        $settings_obj = new \stdClass();

        foreach ($settings as $setting)
        {
            $settings_obj->{$setting->key} = $setting->value;
        }

        return response()->json([
            "status" => "success",
            "message" => "Data has been fetched.",
            "settings" => $settings_obj
        ]);
    }

    public function save_settings()
    {
        $admin = $this->auth();
        $host = request()->host ?? "";
        $port = request()->port ?? "";
        $encryption = request()->encryption ?? "";
        $username = request()->username ?? "";
        $password = request()->password ?? "";
        $from = request()->from ?? "";
        $from_name = request()->from_name ?? "";
        $verify_email = request()->verify_email ?? "";

        $this->set_setting("verify_email", $verify_email);
        $this->set_setting("smtp_host", $host);
        $this->set_setting("smtp_port", $port);
        $this->set_setting("smtp_encryption", $encryption);
        $this->set_setting("smtp_username", $username);
        $this->set_setting("smtp_password", $password);
        $this->set_setting("smtp_from", $from);
        $this->set_setting("smtp_from_name", $from_name);

        return response()->json([
            "status" => "success",
            "message" => "Settings has been saved."
        ]);
    }

    private function set_setting($key, $value)
    {
        $setting = DB::table("settings")
            ->where("key", "=", $key)
            ->first();

        if ($setting == null)
        {
            DB::table("settings")
                ->insertGetId([
                    "key" => $key,
                    "value" => $value,
                    "created_at" => now()->utc(),
                    "updated_at" => now()->utc()
                ]);
        }
        else
        {
            DB::table("settings")
                ->where("id", "=", $setting->id)
                ->update([
                    "value" => $value,
                    "updated_at" => now()->utc()
                ]);
        }
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

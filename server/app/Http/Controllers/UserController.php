<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

use DB;
use Storage;
use Validator;
use Str;
use App\Models\User;

class UserController extends Controller
{
    public function reset_password()
    {
        $validator = Validator::make(request()->all(), [
            "email" => "required",
            "token" => "required",
            "password" => "required",
            "password_confirmation" => "required"
        ]);

        if (!$validator->passes() && count($validator->errors()->all()) > 0)
        {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()->all()[0]
            ]);
        }

        $email = request()->email ?? "";
        $token = request()->token ?? "";
        $password = request()->password ?? "";
        $password_confirmation = request()->password_confirmation ?? "";

        $status = Password::reset(
            request()->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => password_hash($password, PASSWORD_DEFAULT)
                ])->save();

                $user->setRememberToken(Str::random(60));

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET)
        {
            return response()->json([
                "status" => "success",
                "message" => __($status)
            ]);
        }

        return response()->json([
            "status" => "error",
            "message" => __($status)
        ]);
    }

    public function send_password_reset_link()
    {
        $validator = Validator::make(request()->all(), [
            "email" => "required"
        ]);

        if (!$validator->passes() && count($validator->errors()->all()) > 0)
        {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()->all()[0]
            ]);
        }

        $email = request()->email ?? "";

        $user = DB::table("users")
            ->where("email", "=", $email)
            ->first();

        if ($user == null)
        {
            return response()->json([
                "status" => "error",
                "message" => "User not found."
            ]);
        }

        $status = Password::sendResetLink(
            request()->only("email")
        );

        if ($status === Password::RESET_LINK_SENT)
        {
            return response()->json([
                "status" => "success",
                "message" => __($status)
            ]);
        }

        return response()->json([
            "status" => "error",
            "message" => __($status)
        ]);
    }

    public function change_password()
    {
        $validator = Validator::make(request()->all(), [
            "current_password" => "required",
            "new_password" => "required"
        ]);

        if (!$validator->passes() && count($validator->errors()->all()) > 0)
        {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()->all()[0]
            ]);
        }

        $user = auth()->user();
        $current_password = request()->current_password ?? "";
        $new_password = request()->new_password ?? "";

        if (!password_verify($current_password, $user->password))
        {
            return response()->json([
                "status" => "error",
                "message" => "In-correct password."
            ]);
        }

        DB::table("users")
            ->where("id", "=", $user->id)
            ->update([
                "password" => password_hash($new_password, PASSWORD_DEFAULT),
                "updated_at" => now()
            ]);

        return response()->json([
            "status" => "success",
            "message" => "Password has been changed."
        ]);
    }

    public function save_profile()
    {
        $validator = Validator::make(request()->all(), [
            "name" => "required",
            "profile_image" => "required"
        ]);

        if (!$validator->passes() && count($validator->errors()->all()) > 0)
        {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()->all()[0]
            ]);
        }

        $user = auth()->user();
        $name = request()->name ?? "";
        $file_path = $user->profile_image;

        if (request()->file("profile_image"))
        {
            if ($user->profile_image && Storage::exists("public/" . $user->profile_image))
            {
                Storage::delete("public/" . $user->profile_image);
            }

            $file = request()->file("profile_image");
            $file_path = "users/" . $user->id . "/profile-" . time() . "-" . $file->getClientOriginalName();
            $file->storeAs("/public", $file_path);
        }

        DB::table("users")
            ->where("id", "=", $user->id)
            ->update([
                "name" => $name,
                "profile_image" => $file_path,
                "updated_at" => now()
            ]);

        return response()->json([
            "status" => "success",
            "message" => "Profile has been saved."
        ]);
    }

    public function logout()
    {
        $user = auth()->user();

        // $user->tokens()->delete();

        $user->currentAccessToken()->delete();

        // $user->tokens()->where("id", $token_id)->delete();

        return response()->json([
            "status" => "success",
            "message" => "User has been logged-out."
        ]);
    }

    public function me()
    {
        $user = auth()->user();

        if (Storage::exists("public/" . $user->profile_image))
        {
            $user->profile_image = url("/server/public/storage/" . $user->profile_image);
        }

        return response()->json([
            "status" => "success",
            "message" => "Data has been fetched.",
            "user" => [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "profile_image" => $user->profile_image
            ]
        ]);
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

        $token = $user->createToken($this->token_secret)->plainTextToken;

        return response()->json([
            "status" => "success",
            "message" => "Login successfully.",
            "access_token" => $token
        ]);
    }

    public function register()
    {
        $validator = Validator::make(request()->all(), [
            "name" => "required",
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

        $name = request()->name ?? "";
        $email = request()->email ?? "";
        $password = request()->password ?? "";

        $user = DB::table("users")
            ->where("email", "=", $email)
            ->first();

        if ($user != null)
        {
            return response()->json([
                "status" => "error",
                "message" => "Email already exists."
            ]);
        }

        DB::table("users")
            ->insertGetId([
                "name" => $name,
                "email" => $email,
                "password" => password_hash($password, PASSWORD_DEFAULT),
                "created_at" => now(),
                "updated_at" => now()
            ]);

        return response()->json([
            "status" => "success",
            "message" => "Account has been created, you can login now."
        ]);
    }
}

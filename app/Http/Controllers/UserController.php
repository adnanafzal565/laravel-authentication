<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Storage;
use Validator;
use Str;
use App\Models\User;

class UserController extends Controller
{
    public function change_password_view()
    {
        return view("change-password");
    }

    public function home()
    {
        return view("home");
    }

    public function verify_email()
    {
        $validator = Validator::make(request()->all(), [
            "email" => "required",
            "code" => "required"
        ]);

        if (!$validator->passes() && count($validator->errors()->all()) > 0)
        {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()->all()[0]
            ]);
        }

        $email = request()->email ?? "";
        $code = request()->code ?? "";

        $user = DB::table("users")
            ->where("email", "=", $email)
            ->where("verification_code", "=", $code)
            ->first();

        if ($user == null)
        {
            return response()->json([
                "status" => "error",
                "message" => "Verification code expired."
            ]);
        }

        DB::table("users")
            ->where("id", "=", $user->id)
            ->update([
                // "verification_code" => null,
                "email_verified_at" => now()->utc(),
                "updated_at" => now()->utc()
            ]);

        return response()->json([
            "status" => "success",
            "message" => "Account has been verified. You can login now."
        ]);
    }

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

        $password_reset_token = DB::table("password_reset_tokens")
            ->where("email", "=", $email)
            ->where("token", "=", $token)
            ->first();

        if ($password_reset_token == null)
        {
            return response()->json([
                "status" => "error",
                "message" => "Reset link is expired."
            ]);
        }

        if ($password != $password_confirmation)
        {
            return response()->json([
                "status" => "error",
                "message" => "Password mis-match."
            ]);
        }

        DB::table("password_reset_tokens")
            ->where("email", "=", $email)
            ->where("token", "=", $token)
            ->delete();

        DB::table("users")
            ->where("email", "=", $email)
            ->update([
                "password" => password_hash($password, PASSWORD_DEFAULT),
                "updated_at" => now()->utc()
            ]);

        return response()->json([
            "status" => "success",
            "message" => "Password has been reset."
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

        // $reset_token = time() . md5($email);
        $reset_token = Str::random(60);

        $message = "<p>Please click the link below to reset your password</p>";
        $message .= "<a href='" . url("/reset-password/" . $email . "/" . $reset_token) . "'>";
            $message .= "Reset password";
        $message .= "</a>";

        $mail_error = $this->send_mail($email, $user->name, "Password reset link", $message);
        if (!empty($mail_error))
        {
            return response()->json([
                "status" => "error",
                "message" => $mail_error
            ]);
        }

        DB::table("password_reset_tokens")
            ->insertGetId([
                "email" => $email,
                "token" => $reset_token,
                "created_at" => now()->utc()
            ]);

        return response()->json([
            "status" => "success",
            "message" => "Instructions to reset password has been sent."
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
                "updated_at" => now()->utc()
            ]);

        return response()->json([
            "status" => "success",
            "message" => "Password has been changed."
        ]);
    }

    public function save_profile()
    {
        $validator = Validator::make(request()->all(), [
            "name" => "required"
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
                "updated_at" => now()->utc()
            ]);

        return response()->json([
            "status" => "success",
            "message" => "Profile has been saved."
        ]);
    }

    public function profile()
    {
        return view("profile");
    }

    public function do_logout()
    {
        $token = request()->session()->get(config("config.token_secret"), "");

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => config("config.api_url") . "/logout",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token
            ]
        ]);
        
        $response = curl_exec($curl);
        
        if (curl_errno($curl))
        {
            return response()->json([
                "status" => "error",
                "message" => curl_error($curl)
            ]);
        }

        curl_close($curl);

        $response = json_decode($response);
        if ($response->status == "error")
            return $response;

        request()->session()->forget(config("config.token_secret"));
        return $response;
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

        if ($user->profile_image && Storage::exists("public/" . $user->profile_image))
            $user->profile_image = url("/storage/" . $user->profile_image);
        else
            $user->profile_image = "";

        $client_ip = $_SERVER['REMOTE_ADDR'] ?? "";
        // $client_ip = "223.123.88.250";
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? "";

        $timestamp = strtotime($user->last_location_at);

        $current_timestamp = time();

        $difference = $current_timestamp - $timestamp;

        $twenty_four_hours_in_seconds = 24 * 60 * 60;

        /*if ($difference >= $twenty_four_hours_in_seconds)
        {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "http://www.geoplugin.net/json.gp?ip=" . $client_ip,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode([]),
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json"
                ]
            ]);
            $response = curl_exec($curl);

            if (curl_errno($curl))
            {
                $error = curl_error($curl);
            }
            else
            {
                $response = json_decode($response);
                if ($response->geoplugin_status == 200)
                {
                    $location = [
                        "city" => $response->geoplugin_city,
                        "continent" => $response->geoplugin_continentName,
                        "continentCode" => $response->geoplugin_continentCode,
                        "country" => $response->geoplugin_countryName,
                        "countryCode" => $response->geoplugin_countryCode,
                        "currencyCode" => $response->geoplugin_currencyCode,
                        "currencySymbol" => $response->geoplugin_currencySymbol,
                        "currencyConverter" => $response->geoplugin_currencyConverter,
                        "latitude" => $response->geoplugin_latitude,
                        "longitude" => $response->geoplugin_longitude,
                        "region" => $response->geoplugin_region,
                        "ipAddress" => $response->geoplugin_request,
                        "timezone" => $response->geoplugin_timezone,
                        "user_agent" => $user_agent
                    ];

                    DB::table("users")
                        ->where("id", "=", $user->id)
                        ->update([
                            "location" => json_encode($location),
                            "last_location_at" => now()->utc()
                        ]);
                }
            }
            curl_close($curl);
        }*/

        $new_messages = DB::table("notifications")
            ->where("user_id", "=", $user->id)
            ->where("is_read", "=", 0)
            ->where("type", "=", "new_message")
            ->count();

        // request()->session()->put($this->user_session_key, $user->id);

        return response()->json([
            "status" => "success",
            "message" => "Data has been fetched.",
            "user" => [
                "id" => $user->id,
                "name" => $user->name ?? "",
                "email" => $user->email ?? "",
                "profile_image" => $user->profile_image,
                "type" => $user->type ?? ""
            ],
            "new_messages" => $new_messages
        ]);
    }

    public function login_view()
    {
        return view("login");
    }

    public function do_login()
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

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => config("config.api_url") . "/login",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => "email=" . $email . "&password=" . $password
        ]);
        
        $response = curl_exec($curl);
        
        if (curl_errno($curl))
        {
            return response()->json([
                "status" => "error",
                "message" => curl_error($curl)
            ]);
        }

        curl_close($curl);

        $response = json_decode($response);

        if ($response->status == "success")
            request()->session()->put(config("config.token_secret"), $response->access_token);

        return $response;
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

        $user = User::where("email", "=", $email)
            ->whereNull("deleted_at")
            ->first();

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

        $token = $user->createToken($this->token_secret)->plainTextToken;
        // request()->session()->put($this->user_session_key, $user->id);

        return response()->json([
            "status" => "success",
            "message" => "Login successfully.",
            "access_token" => $token
        ]);
    }

    public function email_verification()
    {
        $email = request()->email ?? "";

        return view("email-verification", [
            "email" => $email
        ]);
    }

    public function reset_password_view()
    {
        $token = request()->token ?? "";
        $email = request()->email ?? "";

        return view("reset-password", [
            "email" => $email,
            "token" => $token
        ]);
    }

    public function forgot_password()
    {
        return view("forgot-password");
    }

    public function register_view()
    {
        return view("register");
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

        $user_arr = [
            "name" => $name,
            "email" => $email,
            "password" => password_hash($password, PASSWORD_DEFAULT),
            "type" => "user",
            "created_at" => now()->utc(),
            "updated_at" => now()->utc()
        ];

        $setting_verify_email = DB::table("settings")
            ->where("key", "=", "verify_email")
            ->where("value", "=", "yes")
            ->first();

        if ($setting_verify_email == null)
        {
            $user_arr["email_verified_at"] = now()->utc();
        }
        else
        {
            $verification_code = Str::random(6);
            $user_arr["verification_code"] = $verification_code;

            $message = '<p>Your verification code is: <b style="font-size: 30px;">' . $verification_code . '</b></p>';
            $this->send_mail($email, $name, "Email verification", $message);
        }

        DB::table("users")
            ->insertGetId($user_arr);

        if ($setting_verify_email == null)
        {
            return response()->json([
                "status" => "success",
                "message" => "Account has been created. Please login now.",
                "verification" => false
            ]);
        }
        else
        {
            return response()->json([
                "status" => "success",
                "message" => "Please check your email, a verification code has been sent to you.",
                "verification" => true
            ]);
        }
    }
}

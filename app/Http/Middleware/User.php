<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class User
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = request()->session()->get(config("config.token_secret"), "");
        if (!empty($token))
        {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => config("config.api_url") . "/me",
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POST => 1,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer " . $token
                ]
            ]);
            
            $response = curl_exec($curl);
            curl_close($curl);

            if (!curl_errno($curl))
            {
                $http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($http_status_code === 200)
                {
                    $response = json_decode($response);
                    if ($response->status == "success")
                    {
                        // $request->merge([
                        //     "user" => $response->user
                        // ]);

                        $request->attributes->set("user", $response->user);
                        $request->attributes->set("new_messages", $response->new_messages);
                    }
                }
            }
        }

        return $next($request);
    }
}

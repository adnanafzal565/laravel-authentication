<?php

if (!function_exists("get_ip"))
{
    function get_ip()
    {
        // return "223.123.2.190";
        
        // Check for shared internet/ISP IP
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        // Check for IP addresses passing through proxies
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Check if multiple IP addresses are present in the HTTP_X_FORWARDED_FOR header
            $ipAddresses = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($ipAddresses as $ip) {
                // Return the first non-private IP address found
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        // Return the remote address if none of the above are found
        return $_SERVER['REMOTE_ADDR'];
    }
}

if (!function_exists("get_country"))
{
    function get_country()
    {
        $ip = function_exists("get_ip") ? get_ip() : "";
        $url = "http://ip-api.com/json/$ip";

        // Make a GET request to the API
        $response = file_get_contents($url);
        
        // Decode JSON response
        $data = json_decode($response, true);

        // for testing only, by default, this value will be empty
        $country = "";
        
        // Check if response is successful and contains country info
        if ($data && $data['status'] == 'success' && isset($data['country']))
        {
            $country = $data['country'];
        }

        return $country;
    }
}

if (!function_exists("relative_time"))
{
    function relative_time($seconds)
    {
        // Determine the relative time string
        if ($seconds < 60) {
            return $seconds . ' seconds';
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            return $minutes . ' minutes';
        } elseif ($seconds < 86400) {
            $hours = floor($seconds / 3600);
            return $hours . ' hours';
        } elseif ($seconds < 604800) {
            $days = floor($seconds / 86400);
            return $days . ' days';
        } elseif ($seconds < 2419200) {
            $weeks = floor($seconds / 604800);
            return $weeks . ' weeks';
        } elseif ($seconds < 29030400) { // Approximate number of seconds in a month
            $months = floor($seconds / 2419200);
            return $months . ' months';
        } else {
            $years = floor($seconds / 29030400);
            return $years . ' years';
        }
    }
}

if (!function_exists("capitalize"))
{
    function capitalize($str)
    {
        $parts = explode(" ", $str);
        foreach ($parts as $key => $value)
        {
            $parts[$key] = ucfirst($value);
        }
        return implode(" ", $parts);
    }
}

if (!function_exists("generate_random_str"))
{
    function generate_random_str($length = 6)
    {
        $str = "qwertyuiopasdfghjklzxcvbnm";
        $str_length = strlen($str);
        $output = "";
        for ($a = 1; $a <= $length; $a++)
        {
            $random = rand(0, $str_length - 1);
            $ch = $str[$random];
            $output .= $ch;
        }
        return $output;
    }
}

if (!function_exists("is_in_array"))
{
    function is_in_array($array, $key, $value)
    {
        foreach ($array as $arr)
        {
            if (
                (is_array($arr) && isset($arr[$key]) && $arr[$key] == $value) ||
                (is_object($arr) && isset($arr->{$key}) && $arr->{$key} == $value)
            )
                return true;
        }
        return false;
    }
}

if (!function_exists("is_valid_date"))
{
    function is_valid_date(string $date, string $format = "Y-m-d"): bool
    {
        $dateObj = DateTime::createFromFormat($format, $date);
        return $dateObj && $dateObj->format($format) == $date;
    }
}
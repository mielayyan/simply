<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('datetime_compare')) {
    function datetime_compare($datetime, $compare = 'eq', $compare_with = '')
    {
        if (empty($compare_with)) {
            $compare_with = date('Y-m-d H:i:s');
        }
        $dt = new DateTime($datetime);
        $now = new DateTime($compare_with);
        $res = ($dt <=> $now);
        return compare_return($compare, $res);
    }
}

if (!function_exists('date_compare')) {
    function date_compare($date, $compare = 'eq', $compare_with = '')
    {
        if (empty($compare_with)) {
            $compare_with = date('Y-m-d');
        }
        $dt = new DateTime($date);
        $dt->setTime(0, 0, 0);
        $now = new DateTime($compare_with);
        $now->setTime(0, 0, 0);
        $res = ($dt <=> $now);
        return compare_return($compare, $res);
    }
}

if (!function_exists('user_with_name')) {
    function user_with_name($username = '', $fullname = '', $profile_link = false, $status = null)
    {
        $base_url = base_url();
        $generated_element = "";
        if ($profile_link) {
            $generated_element = "{$fullname} <span>(<a target='_blank' href='{$base_url}admin/profile/profile_view?user_name={$username}' class='btn-link text-primary'>{$username}</a>)</span>";
        } else {
            $generated_element = "{$fullname} <span>({$username})</span>";
        }

        if ($status !== null) {
            if ($status == 'yes') {
                $status_lang = lang('active');
                $generated_element .= " <span class='label bg-info'>{$status_lang}</span>";
            } else {
                $status_lang = lang('inactive');
                $generated_element .= " <span class='label bg-black-opacity'>{$status_lang}</span>";
            }
        }

        return $generated_element;
    }
}

function compare_return($compare, $res)
{
    if ($compare == 'eq') {
        return ($res === 0);
    } elseif ($compare == 'neq') {
        return ($res !== 0);
    } elseif ($compare == 'lt') {
        return ($res === -1);
    } elseif ($compare == 'gt') {
        return ($res === 1);
    } elseif ($compare == 'lteq') {
        return ($res === 0 || $res === -1);
    } elseif ($compare == 'gteq') {
        return ($res === 0 || $res === 1);
    }
}

if (!function_exists('get_daterange')) {
    function get_daterange($daterange, $from_date = '', $to_date = '')
    {
        if ($daterange == 'today') {
            $from_date = $to_date = date('Y-m-d');
        } elseif ($daterange == "week") {
            $day = date('w');
            $from_date = date('Y-m-d', strtotime('-' . $day . ' days'));
            $to_date = date('Y-m-d');
        } elseif ($daterange == 'month') {
            $from_date = date('Y-m-01');
            $to_date = date('Y-m-t');
        } elseif ($daterange == 'year') {
            $from_date = date('Y-01-01');
            $to_date = date('Y-12-31');
        } elseif ($daterange == 'custom') {} else {
            $from_date = '';
            $to_date = '';
        }
        return [$from_date, $to_date];
    }
}

if (!function_exists('format_currency')) {
    function format_currency($amount = 0, $precision = 0)
    {
        $CI = &get_instance();
        $precision = $precision == 0 ? $CI->PRECISION : $precision;
        $amount = floatval($amount);
        return "{$CI->DEFAULT_SYMBOL_LEFT} " . number_format($amount * $CI->DEFAULT_CURRENCY_VALUE, $precision) . " {$CI->DEFAULT_SYMBOL_RIGHT}";
    }
}

if (!function_exists('format_currency_value')) {
    function format_currency_value($amount = 0, $value = 0)
    {
        $amount = floatval($amount);
        $CI = &get_instance();
        return "{$CI->DEFAULT_SYMBOL_LEFT} " . number_format($amount * $value, $CI->PRECISION) . " {$CI->DEFAULT_SYMBOL_RIGHT}";
    }
}
if (!function_exists('thousands_currency_format')) {
    function thousands_currency_format($amount = 0)
    {

        $CI = &get_instance();
        $amount = $CI->DEFAULT_CURRENCY_VALUE * $amount;
        $amount = floatval($amount);
        if ($amount > 10000) {

            $x = round($amount);
            $x_number_format = number_format($x);
            $x_array = explode(',', $x_number_format);
            $x_parts = array('K', 'M', 'B', 'T');
            $x_count_parts = count($x_array) - 1;
            $x_display = $x;
            $float = "";
            for ($i = 0; $i < $CI->PRECISION; $i++) {
                $float .= $x_array[1][$i];
            }
            $zero = str_repeat("0", $CI->PRECISION);
            $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $float : '.' . $zero);
            $x_display .= $x_parts[$x_count_parts - 1];

            $amount = $x_display;

        } else {
            $amount = number_format($amount, $CI->PRECISION);
        }
        return "{$CI->DEFAULT_SYMBOL_LEFT} " . $amount . " {$CI->DEFAULT_SYMBOL_RIGHT}";
    }
}

if (!function_exists('convert_currency')) {
    function convert_currency($amount = 0)
    {
        $CI = &get_instance();
        return round($amount * $CI->DEFAULT_CURRENCY_VALUE, $CI->PRECISION);
    }
}

if (!function_exists('profile_image_path')) {
    function profile_image_path($image)
    {
        $image_path = IMG_DIR . 'profile_picture/' . $image;
        if (!file_exists($image_path)) {
            $image = 'nophoto.jpg';
        }

        return SITE_URL . '/uploads/images/profile_picture/' . $image;
    }
}

if (!function_exists('image_path')) {
    function image_path($path, $image, $default_image)
    {
        $image_path = IMG_DIR . $path . '/' . $image;
        if (!file_exists($image_path)) {
            $image = $default_image;
        }

        return SITE_URL . '/uploads/images/' . $path . '/' . $image;
    }
}

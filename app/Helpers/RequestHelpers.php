<?php


namespace App\Helpers;


trait RequestHelpers
{

    /**
     * 处理请求参数
     * @param $key
     * @param $filter
     * @param $default
     * @return array|mixed|string|string[]|null
     */
    public function param($key, $filter = '', $default = '')
    {
        $value = $this->filter(request()->input($key), $filter);
        return $value === false || $value === '' ? $default : $value;
    }

    /**
     * 过滤参数值
     * @param $value
     * @param $filter
     * @return array|false|string|string[]|null
     */
    private function filter($value, $filter)
    {
        if (!is_array($value)) {
            return $filter ? $this->{$filter}($value) : paramFilter($value);
        } else {
            return collect($value)->map(function ($item) use ($filter) {
                return $this->filter($item, $filter);
            })->toArray();
        }
    }


    private function intval($value): int
    {
        return intval($value);
    }

    private function floatval($value): float
    {
        return floatval($value);
    }

    private function mobile($value)
    {
        return preg_match("/^1\d{10}$/", $value) ? $value : false;
    }

}

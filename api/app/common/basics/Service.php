<?php

declare (strict_types = 1);

namespace app\common\basics;

use app\common\model\UserModel;
use Exception;

/**
 * 服务类基类
 */
class Service
{
    /**
     * 模型实例
     * @var object
     */
    private static $model;

    /**
     * 错误信息
     * @var string
     */
    protected static $error = '';

    /**
     * 返回状态码
     * @var int
     */
    protected static $returnCode = 1;

    /**
     * 搜索条件
     * @var array
     */
    protected static $searchWhere = [];

    /**
     * 返回错误信息
     *
     * @author zero
     * @return string
     */
    public static function getError(): string
    {
        return self::$error ?? 'Unknown';
    }

    /**
     * 返回指定状态码
     *
     * @author zero
     * @return int
     */
    public static function getReturnCode(): int
    {
        return self::$returnCode;
    }

    /**
     * 设置搜索条件
     * PS: 参数名@字段名: user@U.sn
     *
     * @param array $search
     * @param array $params 请求参数
     * @return array
     * @author zero
     */
    protected static function setSearch(array $search,array $params): array
    {
        if (empty($search)) {
            return [];
        }

        $where = [];
        foreach ($search as $whereType => $whereFields) {
            switch ($whereType) {
                case '=':
                case '<>':
                case '>':
                case '>=':
                case '<':
                case '<=':
                case 'in':
                    foreach ($whereFields as $whereField) {
                        $paramsName = strpos($whereField, '@') ? explode('@', $whereField) : $whereField;
                        $key   = is_array($paramsName) ? $paramsName[0] : $paramsName; //参数的名称
                        $field = is_array($paramsName) ? $paramsName[1] : $whereField; //字段的名称
                        try {
                            if (!isset($params[$key]) || (!is_numeric($params[$key]) && !$params[$key])) {
                                continue;
                            }
                            $where[] = [$field, $whereType, $params[$key]];
                        } catch (Exception $e) {}
                    }
                    break;
                case '%like%':
                    foreach ($whereFields as $whereField) {
                        $paramsName = strpos($whereField, '@') ? explode('@', $whereField) : $whereField;
                        $key = is_array($paramsName) ? $paramsName[0] : $paramsName;
                        $val = is_array($paramsName) ? $paramsName[1] : $whereField;
                        try {
                            if (!isset($params[$key]) || (!is_numeric($params[$key]) && !$params[$key])) {
                                continue;
                            }
                            $where[] = [$val, 'like', '%' . $params[$key] . '%'];
                        } catch (Exception $e) {}
                    }
                    break;
                case '%like':
                    foreach ($whereFields as $whereField) {
                        $paramsName = strpos($whereField, '@') ? explode('@', $whereField) : $whereField;
                        $key = is_array($paramsName) ? $paramsName[0] : $paramsName;
                        $val = is_array($paramsName) ? $paramsName[1] : $whereField;
                        try {
                            if (!isset($params[$key]) || (!is_numeric($params[$key]) && !$params[$key])) {
                                continue;
                            }
                            $where[] = [$val, 'like', '%' . $params[$key]];
                        } catch (Exception $e) {}
                    }
                    break;
                case 'like%':
                    foreach ($whereFields as $whereField) {
                        $paramsName = strpos($whereField, '@') ? explode('@', $whereField) : $whereField;
                        $key = is_array($paramsName) ? $paramsName[0] : $paramsName;
                        $val = is_array($paramsName) ? $paramsName[1] : $whereField;
                        try {
                            if (!isset($params[$key]) || (!is_numeric($params[$key]) && !$params[$key])) {
                                continue;
                            }
                            $where[] = [$val, 'like', $params[$key] . '%'];
                        } catch (Exception $e) {}
                    }
                    break;
                case 'between':
                    foreach ($whereFields as $whereField) {
                        $arrayInput = explode('@', $whereField);
                        $keyArg = $arrayInput[0]??'';
                        $dbName = $arrayInput[1]??'';
                        $paramsName = explode('|', $keyArg);
                        if (empty($params[$paramsName[0]]) || empty($params[$paramsName[1]])) {
                            break;
                        }
                        $start = $params[$paramsName[0]];
                        $end   = $params[$paramsName[1]];
                        $where[] = [$dbName, 'between', [$start, $end]];
                    }
                    break;
                case 'datetime':
                    foreach ($whereFields as $whereField) {
                        $paramsName = !strpos($whereField, '@') ? $whereField : explode('@', $whereField);
                        $key = is_array($paramsName) ? $paramsName[0] : $paramsName;
                        $val = is_array($paramsName) ? $paramsName[1] : $whereField;
                        if (!isset($params[$key]) || !$params[$key]) {
                            continue;
                        }

                        if (is_array($params[$key])) {
                            $start = $params[$key][0];
                            $end = $params[$key][1];
                            $where[] = [$val, '>=', strtotime($start)];
                            $where[] = [$val, '<=', strtotime($end)];
                        } else {
                            $where[] = [$val, '>=', strtotime($params[$key]) / 1000];
                        }
                    }
                    break;
                case 'keyword':
                    if (!isset($params['keyword_type']) || empty($params['keyword'])) {
                        break;
                    }
                    $value = $whereFields[$params['keyword_type']];
                    switch ($value[0]){
                        case '=':
                        case '<>':
                        case '>':
                        case '>=':
                        case '<':
                        case '<=':
                        case 'in':
                            $where[] = [$value[1], $value[0], $params['keyword']];
                            break;
                        case '%like%':
                            $where[] = [$value[1], 'like', '%' . $params['keyword'] . '%'];
                            break;
                        case '%like':
                            $where[] = [$value[1], 'like', '%' . $params['keyword']];
                            break;
                        case 'like%':
                            $where[] = [$value[1], 'like', $params['keyword'] . '%'];
                            break;
                    }
                    break;
            }
        }
        self::$searchWhere = $where;
        return $where;
    }



    /*
     * 通用
     */
    public static function lists(array $params):array
    {
        return [];
    }
    /*
     * 通用
     */
    public static function add(array $params):void
    {

    }
    /*
     * 通用
     */
    public static function edit(array $params):void
    {

    }
    /*
     * 通用
     */
    public static function del(array $params):void
    {

    }
    /*
     * 通用 获取选项列表
     */
    public static function getOptions():array{
        return [];
    }
}
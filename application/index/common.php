<?php
/**
 *
 * 模块公共（函数）文件
 *
 * @package   NiPHPCMS
 * @category  index\
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: common.php v1.0.1 $
 * @link      http://www.NiPHP.com
 * @since     2016/10/22
 */

/**
 * 访问权限
 * @param  int   $id 栏目ID|文章ID
 * @return mixed
 */
function access_auth($id)
{
    if ($id == 0) {
        return false;
    }

    // halt($id);
}

/**
 * 字符串截取，支持中文和其他编码
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $suffix=true, $charset='utf-8')
{
    $ext = mb_strlen($str) > $length && $suffix ? '...' : '';
    return mb_substr($str, $start, $length, $charset) . $ext;
}

/**
 * 自定义字段类型转换
 * @param  array $data
 * @return string
 */
function to_option_type($data)
{
    switch ($data['field_type']) {
        case 'number':
        case 'email':
        case 'phone':
            $input = '<input type="' . $data['field_type'] . '"';
            $input .= ' name="fields[' . $data['id'] . ']"';
            $input .= ' id="fields-' . $data['id'] . '"';
            $input .= ' value=""';
            $input .= ' class="form-control">';
            break;

        case 'url':
        case 'currency':
        case 'abc':
        case 'idcards':
        case 'landline':
        case 'age':
            $input = '<input type="text"';
            $input .= ' name="fields[' . $data['id'] . ']"';
            $input .= ' id="fields-' . $data['id'] . '"';
            $input .= ' value=""';
            $input .= ' class="form-control">';
            break;

        case 'date':
            $input = '<input type="text"';
            $input .= ' name="fields[' . $data['id'] . ']"';
            $input .= ' id="fields-' . $data['id'] . '"';
            $input .= ' value=""';
            $input .= ' class="form-control">';

            $input .= '<script type="text/javascript">
                $(function () {
                    $("#fields-' . $data['id'] . '").datetimepicker(
                        {format: "Y-M-D"}
                        );
                });
                </script>';
            break;

        case 'text':
            $input = '<textarea name="fields[' . $data['id'] . ']"';
            $input .= ' id="fields-' . $data['id'] . '"';
            $input .= ' class="form-control">';
            $input .= '</textarea>';
            break;
    }

    return $input;
}

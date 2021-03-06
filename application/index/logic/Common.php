<?php
/**
 *
 * 常用设置 - 公众 - 逻辑层
 *
 * @package   NiPHPCMS
 * @category  index\logic\
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Common.php v1.0.1 $
 * @link      http://www.NiPHP.com
 * @since     2016/11/25
 */
namespace app\index\logic;

use think\Request;
use think\Lang;
use think\Cookie;
use app\admin\model\Config as ModelConfig;
use app\admin\model\Category as ModelCategory;

class Common
{
    protected $request = null;
    protected $toHtml = [
        'bottom_message',
        'copyright',
        'script'
    ];

    // 表名
    public $tableName = null;

    public function __construct()
    {
        $this->request = Request::instance();

        $this->tableName = $this->getModelTable();
    }

    /**
     * 获得当前栏目和父级栏目数据
     * @access public
     * @param  intval $cid_
     * @return array
     */
    public function getCategoryData($cid_=0)
    {
        $cid_ = $cid_ ? $cid_ : $this->request->param('cid/f');

        $data = [];

        $map = [
            'id' => $cid_,
            'lang' => Lang::detect()
        ];

        $category = new ModelCategory;

        $result =
        $category->field(['id', 'pid', 'name', 'seo_title', 'seo_keywords', 'seo_description', 'access_id'])
        ->where($map)
        ->cache(!APP_DEBUG)
        ->find();

        $self = $result ? $result->toArray() : [];

        $data[] = $self;

        if (!empty($self['pid'])) {
            $pid = $this->getCategoryData($self['pid']);
            if ($pid) {
                $data = array_merge($data, $pid);
            }
        }

        access_auth($data[0]['access_id']);

        return $data;
    }

    /**
     * 获得网站基本设置数据
     * @access public
     * @param
     * @return array
     */
    public function getWetsiteData()
    {
        $module = strtolower($this->request->module());

        $map = [
            'name' => [
                'in',
                'website_name,website_keywords,website_description,bottom_message,copyright,script,' . $module . '_theme'
            ],
            'lang' => Lang::detect()
        ];

        $config = new ModelConfig;

        $result =
        $config->field(true)
        ->where($map)
        ->cache(!APP_DEBUG, 0)
        ->select();

        $data = [];
        foreach ($result as $value) {
            $value = $value->toArray();
            if (in_array($value['name'], $this->toHtml)) {
                $data[$value['name']] = htmlspecialchars_decode($value['value']);
            } else {
                $data[$value['name']] = $value['value'];
            }
        }

        return $data;
    }

    /**
     * 获得模型表
     * @access protected
     * @param
     * @return array
     */
    protected function getModelTable()
    {
        if (!$this->request->has('cid', 'param')) {
            return null;
        }

        $cid = $this->request->param('cid/f');
        $map = [
            'c.id' => $cid,
            'c.lang' => Lang::detect()
        ];

        $category = new ModelCategory;

        $result =
        $category->view('category c', 'id')
        ->view('model m', ['name' => 'model_name'], 'm.id=c.model_id AND m.name!=\'external\'')
        ->view('category cc', 'pid', 'c.id=cc.pid', 'LEFT')
        ->where($map)
        ->cache(!APP_DEBUG, 0)
        ->find();

        $data = $result ? $result->toArray() : [];

        return $data ? $data['model_name'] : '';
    }
}

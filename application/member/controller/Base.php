<?php
/**
 *
 * 网站全局 - 控制器
 *
 * @package   NiPHPCMS
 * @category  member\controller\
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Base.php v1.0.1 $
 * @link      http://www.NiPHP.com
 * @since     2016/10/22
 */
namespace app\member\controller;

use think\Controller;
use think\Lang;
use think\Config;
use think\Log;
use app\index\logic\Visit as IndexLogicVisit;
use app\member\logic\Common as LogicCommon;

class Base extends Controller
{
    // 网站基本数据
    protected $websiteData = [];

    /**
     * 初始化
     * @access protected
     * @param
     * @return void
     */
    protected function _initialize()
    {
        // 设置IP为授权Key
        // Log::key($this->request->ip(0, true));

        Config::load(CONF_PATH . 'website.php');

        // 访问与搜索日志
        // $logic = new IndexLogicVisit;
        // $logic->visit();
        // $logic->requestLog();

        $common_logic = new LogicCommon;

        // 权限
        $result = $common_logic->accountAuth();
        if (true !== $result) {
            $this->redirect($result);
        }

        // 网站基本数据
        $this->websiteData = $common_logic->getWetsiteData();

        $this->themeConfig();

        $this->assign('nav', $common_logic->getAuthMenu());
        $this->assign('controller', strtolower($this->request->controller()));
        $this->assign('menu_name', strtolower($this->request->controller()) . '_' . $this->request->action());
    }

    /**
     * 模板配置
     * @access protected
     * @param
     * @return void
     */
    protected function themeConfig()
    {
        $template = Config::get('template');
        $template['taglib_pre_load'] = 'taglib\Label';

        $module = strtolower($this->request->module());

        // 模板路径
        $template['view_path'] = ROOT_PATH . 'public' . DIRECTORY_SEPARATOR . 'theme' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR;
        $template['view_path'] .= $this->websiteData[$module . '_theme'] . DIRECTORY_SEPARATOR;

        // 判断访问端
        $mobile = $this->request->isMobile() ? 'mobile' . DIRECTORY_SEPARATOR : '';
        $info = $this->request->header();
        if (strpos($info['user-agent'], 'MicroMessenger')) {
            if (is_dir($template['view_path'] . 'wechat' . DIRECTORY_SEPARATOR)) {
                $mobile = 'wechat' . DIRECTORY_SEPARATOR;
            }
        }

        // 移动端和微信端模板
        if (is_dir($template['view_path'] . $mobile)) {
            $template['view_path'] .= $mobile;
        }

        // 模板路径
        $this->view->engine($template);

        // 获得域名地址
        $domain = $this->request->domain();
        $domain .= substr($this->request->baseFile(), 0, -10);
        $default_theme = $domain . '/public/theme/' . $module . '/';
        $default_theme .= $this->websiteData[$module . '_theme'] . '/' . $mobile;

        $replace = [
            '__DOMAIN__'      => $domain,
            '__PHP_SELF__'    => basename($this->request->baseFile()),
            '__STATIC__'      => $domain . '/public/static/',
            '__LIBRARY__'     => $domain . '/public/static/library/',
            '__LAYOUT__'      => $domain . '/public/static/layout/',
            '__THEME__'       => $this->websiteData[$module . '_theme'],
            '__CSS__'         => $default_theme . 'css/',
            '__JS__'          => $default_theme . 'js/',
            '__IMG__'         => $default_theme . 'img/',
            '__MESSAGE__'     => $this->websiteData['bottom_message'],
            '__COPYRIGHT__'   => $this->websiteData['copyright'],
            '__SCRIPT__'      => $this->websiteData['script'],

            '__TITLE__'       => $this->websiteData['website_name'],
            '__KEYWORDS__'    => $this->websiteData['website_keywords'],
            '__DESCRIPTION__' => $this->websiteData['website_description'],
        ];
        $this->view->replace($replace);
    }
}

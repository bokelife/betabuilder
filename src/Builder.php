<?php
// +----------------------------------------------------------------------
// | BetaBuilder [ Beta Base CNF System ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.betaec.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: Johnny <johnnycaimail@yeah.net>
// +----------------------------------------------------------------------
// | 基于ThinkCMF 5.1 开发的自动化生成form表单
// +----------------------------------------------------------------------

namespace betaos\betabuilder;

use think\Controller;
use think\Exception;

class Builder extends Controller{

    protected $metaTitle; // 页面标题
    protected $tips;      // 页面标题
    protected $pluginName;
    protected $preQueryConnector;
    protected $config = [
        'view_assets' => "/icesui/assets",
        'asset_path' => __DIR__ . DS . 'assets' . DS,
        'view_path' => __DIR__ . DS . "view" .DS,
    ];

    /**
     * 获取服务器环境类型
     * @return string
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function getServerType(){
        //定义环境类型
        if (strpos($_SERVER["SERVER_SOFTWARE"],'nginx')!==false) {
            return 'nginx';
        } elseif(strpos($_SERVER["SERVER_SOFTWARE"],'apache')!==false){
            return 'apache';
        } else{
            return 'unknow';
        }
    }

    public function _initialize() {
        $this->pluginName = null;
        if (input('?param._plugin')) {
            $this->pluginName = input('param._plugin');
        }
        //参数前缀连接符
        $this->preQueryConnector = $this->getServerType() =='nginx' ? '&' : '?';        
    }    

    /**
     * 获取默认设置
     * @return array
     */
    public function getConfig(){
        return $this->config;
    }

    /**
     * 开启Builder
     * @param  string $type 构建器名称
     * @return [type]       [description]
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public static function run($type='')
    {
        if ($type == '') {
            throw new \Exception('未指定构建器', 100001);
        } else {
            $type = ucfirst(strtolower($type));
        }

        // 构造器类路径
        $class = '\\betaos\\betabuilder\\'. $type.'Builder';
        if (!class_exists($class)) {
            throw new \Exception($type . '构建器不存在', 100002);
        }
        return new $class;
    }

    /**
     * 转换html标签
     * @param $attr
     * @return array|string
     * @author Johnny <johnnycaimail@yeah.net>
     */
    protected function compileHtmlAttr($attr) {
        $result = [];
        foreach($attr as $key=>$value) {
            if (is_string($value)) {
                $value = htmlspecialchars($value);
                $result[] = "$key=\"$value\"";
            }
        }
        $result = implode(' ', $result);
        return $result;
    }

    /**
     * 设置页面标题
     * @param $title 标题文本
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function setMetaTitle($meta_title) {
        $this->metaTitle = $meta_title;
        return $this;
    }

    /**
     * 设置页面说明
     * @param $title 标题文本
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function setPageTips($content,$type='info') {
        $this->tips = $content;
        return $this;
    }

    /**
     * 模版输出
     * @param  string $template 模板文件名
     * @param  array  $vars         模板输出变量
     * @param  array  $replace      模板替换
     * @param  array  $config       模板参数
     * @return [type]               [description]
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function fetch($template = '',$vars = [], $replace = [], $config = []) {
        $template_path_str = $this->config['view_path'];

        $this->assign('template_path_str',$template_path_str);
        $this->assign('_builder_style_', $template_path_str.'style.html');  // 页面样式
        $this->assign('_builder_javascript_', $template_path_str.'javascript.html');  // 页面样式

        $template_vars = [
            'show_box_header' => 1,//是否显示box_header
            'meta_title'      => $this->metaTitle,// 页面标题
            'tips'            => $this->tips,// 页面提示说明
        ];
        $this->assign($template_vars);
        //显示页面
        if ($template!='') {
            return parent::fetch($template,$vars,$replace,$config);
        }
    }

    /**
     * 获取插件地址
     * @param $url 格式三段式，如：插件标识/控制器名称/操作名
     * @param array $param
     * @return string
     */
    public function plugin_url($url, $param=[]){
        $params = [];
        // 拆分URL
        $url  = explode('/', $url);

        if (!isset($url[1]) && !isset($url[2])) {
            $params['_plugin']     = input('param._plugin');
            $params['_controller'] = input('param._controller');
            $params['_action']     = $url[0];
        } elseif (!isset($url[2])) {
            $params['_plugin']     = input('param._plugin');
            $params['_controller'] = $url[0];
            $params['_action']     = $url[1];
        } else {
            $params['_plugin']     = $url[0];
            $params['_controller'] = $url[1];
            $params['_action']     = $url[2];
        }

        // 合并参数
        $params = array_merge($params, $param);

        return url("\\cmf\\controller\\PluginController@index", $params);
    }
}
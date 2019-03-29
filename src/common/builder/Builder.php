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

namespace app\common\builder;

use cmf\controller\AdminBaseController;

class Builder extends AdminBaseController {

    protected $metaTitle; // 页面标题
    protected $tips;      // 页面提示
    protected $tipsType;      // 页面提示类型
    protected $topHtml;            // 顶部功能代码
    protected $extraHtml;            // 额外功能代码
    protected $pluginName;
    protected $preQueryConnector;
    protected $config = [
        'asset_path' => __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR ,
        'view_path' => __DIR__ . DIRECTORY_SEPARATOR . "view" .DIRECTORY_SEPARATOR ,
        'tpl_path' => DIRECTORY_SEPARATOR . "view" .DIRECTORY_SEPARATOR . 'builder' . DIRECTORY_SEPARATOR,
        'libs_parh' => DIRECTORY_SEPARATOR . "view" .DIRECTORY_SEPARATOR . 'libs' .DIRECTORY_SEPARATOR,
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

    public function initialize() {
        $this->pluginName = null;
        if (input('?param._plugin')) {
            $this->pluginName = input('param._plugin');
        }
        //参数前缀连接符
        $this->preQueryConnector = $this->getServerType() =='nginx' ? '&' : '?';
    }

    /**
     * 获取资源路径
     * @param $name 资源名称
     * @param $type 资源类型
     */
    public function getResourcePath($type,$name=''){
        $path= dirname(__DIR__);
        switch ($type){
            case 'tpl':
                $path .= $this->config['tpl_path'].cmf_get_current_admin_theme() . DIRECTORY_SEPARATOR;
                break;
            case 'libs':
                if($name==''){
                    $path .= $this->config['libs_parh'];
                }else{
                    $path .= $this->config['libs_parh'].$name . DIRECTORY_SEPARATOR;
                }                
                break;
            default:
                break;                
        }
        return $path;
    }

    /**
     * 获取远程库
     * 用于加载js和css文件，当远程文件不存在时，加载本地文件
     * @param $name 资源目录名称
     * @param array $list 资源文件列表  键名为本地路径，键值为远程路径 例如：
     * [
     *  ['js/select2.min.js'=>'https://cdn.bootcss.com/bootstrap-daterangepicker/3.0.3/daterangepicker.js']
     *  ['css/select2.min.css'=>'https://cdn.bootcss.com/select2/4.0.6-rc.1/css/select2.min.css']
     * ]
     */
    public function getRemoteLibs($name,$list=[]){
        $path = $this->getResourcePath('libs',$name);
        $re = '';
        foreach ($list as $key=>$val){
            $type = strstr($key, '.css')?'css':'js';
            if($val!=''){
                $array = get_headers($val,1);
                if(preg_match('/200/',$array[0])){
                    if($type=='css'){
                        $re .= '<link href="'.$val.'" rel="stylesheet">';
                    }else{
                        $re .= '<script src="'.$val.'"></script>';
                    }
                }else{
                    $str = parent::fetch($path.$key);
                    if($type=='css'){
                        $re .= '<style type="text/css">'.$str.'</style>';
                    }else{
                        $re .= '<script type="text/javascript">'.$str.'"</script>';
                    }
                }
            }else{
                $str = parent::fetch($path.$key);
                if($type=='css'){
                    $re .= '<style type="text/css">'.$str.'</style>';
                }else{
                    $re .= '<script type="text/javascript">'.$str.'"</script>';
                }
            }
        }
        return $re;
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
        $class = '\\app\\common\\builder\\'. $type.'Builder';
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
     * @param $type 提示类型：success、info、warning、danger
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function setPageTips($content,$type='info') {
        $this->tips = $content;
        $this->tipsType = $type;
        return $this;
    }

    /**
     * 设置顶部功能代码
     * @param $top_html 额外功能代码
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function setTopHtml($top_html) {
        $this->topHtml = $top_html;
        return $this;
    }

    /**
     * 设置额外功能代码
     * @param $extra_html 额外功能代码
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function setExtraHtml($extra_html) {
        $this->extraHtml = $extra_html;
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
        $template_path_str = $this->getResourcePath('tpl');
        $libs_path_str = $this->getResourcePath('libs');

        $this->assign('template_path_str',$template_path_str);
        $this->assign('libs_path_str',$libs_path_str);

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
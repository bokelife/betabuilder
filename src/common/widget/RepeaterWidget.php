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

namespace app\common\widget;

use cmf\controller\AdminBaseController;

class RepeaterWidget extends AdminBaseController{

    //灵活字段
    public function render($attributes = [],$param='')
    {
        $id      = isset($attributes['id'])      ? $attributes['id']     :'repeater';
        $name    = isset($attributes['name'])    ? $attributes['name']   :'content';
        $default = isset($attributes['default']) ? $attributes['default']:'';
        $options = isset($attributes['options']) ? $attributes['options']:[];

        $this->assign('id',$id);
        $this->assign('name',$name);
        $optionss = [];
        //$this->assign('default',$default);
        if (!empty($default) && is_array($default)) {
            $new_options=[];
            foreach ($default as $key => $data) {
                $options = array_intersect_key($options,$data);
                foreach ($options as $o_key => $option) {
                    $options[$o_key]['default']=$data[$o_key];
                }
                $new_options[]=$options;
            }
            $optionss=$new_options;//赋值新的options

        } else{
            $optionss[0]=$options;
        }

        $this->assign('options',$optionss);
        //是否加载图片选择器
        if (is_array($optionss) && !empty($optionss)) {
            $num=0;
            foreach ($optionss[0] as $key => $val) {
                if ($val['type']=='image') {
                    $num++;
                }
            }
        }
        $param = [
            'is_load_WebUploader_script' =>$num,//加载webuploader资源
            'is_load_attachment_modal'   =>$num,//加载图片选择器
            'is_load_script'             =>false
        ];
        $tpl_path= dirname(__DIR__).DIRECTORY_SEPARATOR . 'view' .DIRECTORY_SEPARATOR .'widget' .DIRECTORY_SEPARATOR .'repeater.html';
        $js_path = dirname(__DIR__).DIRECTORY_SEPARATOR . 'view' .DIRECTORY_SEPARATOR .'libs' .DIRECTORY_SEPARATOR  . 'jquery-repeater' .DIRECTORY_SEPARATOR .'jquery.repeater.min.js';

        $handle = fopen($js_path,"r");        
        $js = fread($handle,filesize($js_path));
        fclose($handle);
        $this->assign('_js_',$js);

        $this->assign('param',$param);
        $this->assign('field',$attributes);
        return $this->fetch($tpl_path);
    }
}
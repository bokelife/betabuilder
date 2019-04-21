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

/**
 * 快速建立表单页面
 * Class FormBuilder
 * @package betaos\betabuilder
 * @author Johnny <johnnycaimail@yeah.net>
 */
class FormBuilder extends Builder{

    private $tabNav     = [];     // 页面Tab导航
    private $groupTabNav=[]; //页面Tab分组
    private $postUrl;              // 表单提交地址
    private $buttonList  = [];    //按钮组
    private $formItems  = [];  // 表单项目
    private $extraItems = []; // 额外已经构造好的表单项目
    private $formData   = [];   // 表单数据
    private $ajaxSubmit = true;    // 是否ajax提交
    protected $fieldsItemsList = ['audio_play','video_play','text','avatar','checkbox','color','date','daterange','datetime','editor','email','file',
        'files','form_inline','group','hidden','image','images','img','info','left_icon_number','left_icon_text','multilayer_select','number',
        'password','radio','readonly','right_icon_number','right_icon_text','section','select','select2','select_multiple',
        'tab','textarea','self','self_html','static','url','ueditor','icon','repeater'];
    protected $staticFiles = [
        'daterange' => [
            'daterangepicker-bs3.min.css' => 'https://cdn.bootcss.com/bootstrap-daterangepicker/1.3.21/daterangepicker-bs3.min.css',
            'moment.min.js' => 'https://cdn.bootcss.com/bootstrap-daterangepicker/1.3.21/moment.min.js',
            'daterangepicker.min.js' => 'https://cdn.bootcss.com/bootstrap-daterangepicker/1.3.21/daterangepicker.min.js',
        ],
        'fontawesome_iconpicker' => [
            'css/fontawesome-iconpicker.min.css' => 'https://cdn.bootcss.com/fontawesome-iconpicker/1.4.1/css/fontawesome-iconpicker.min.css',
            'js/fontawesome-iconpicker.min.js' => 'https://cdn.bootcss.com/fontawesome-iconpicker/1.4.1/js/fontawesome-iconpicker.min.js',
        ],
        'select2' => [
            'select2.min.css' => 'https://cdn.bootcss.com/select2/4.0.5/css/select2.min.css',
            'select2-bootstrap.min.css' => 'https://cdn.bootcss.com/select2-bootstrap-css/1.4.6/select2-bootstrap.min.css',
            'select2.min.js' => 'https://cdn.bootcss.com/select2/4.0.4/js/select2.min.js',
        ]
    ];

    /**
     * 设置Tab按钮列表
     * @param $tab_list    Tab列表  array('title' => '标题', 'href' => 'http://www.xxx.com')
     * @param $current 当前tab
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function setTabNav($tab_list, $current) {
        $this->tabNav = ['tab_list' => $tab_list, 'current' => $current];
        return $this;
    }

    /**
     * 组tab
     * @param $tab_list    Tab列表  array('title' => '标题', 'href' => 'http://www.xxx.com')
     * @param $current 当前tab
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function setGroupTabNav($tab_list, $current) {
        $this->groupTabNav = ['tab_list' => $tab_list, 'current' => $current];
        return $this;
    }

    /**
     * 组处理
     * @param $name
     * @param array $list
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function group($name, $list = array())
    {
        !is_array($list) && $list = explode(',', $list);
        $this->groupTabNav[$name] = $list;
        return $this;
    }

    /**
     * 数组处理
     * @param array $list
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function groups($list = array())
    {
        foreach ($list as $key => $v) {
            $this->groupTabNav[$key] = is_array($v) ? $v : explode(',', $v);
        }
        return $this;
    }

    /**
     * 设置表单项数组
     * @param $form_items 表单项数组
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function setExtraItems($extra_items) {
        $this->extraItems = $extra_items;
        return $this;
    }

    /**
     * 设置表单提交地址
     * @param $url 提交地址
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function setPostUrl($post_url) {
        $this->postUrl = $post_url;
        return $this;
    }

    /**
     * 设置ajax提交
     * @param $title 标题文本
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function setAjaxSubmit($ajax_submit = true) {
        $this->ajaxSubmit = $ajax_submit;
        return $this;
    }

    /**
     * 加入一个表单项
     * @param $name 字段名
     * @param $type 表单类型
     * @param $title 表单标题
     * @param $description 表单项描述说明
     * @param $options 表单options
     * @param $validate 验证规则，多个规则以英文逗号分开
     * @param $extra_class 表单项是否隐藏
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function addFormItem($name, $type, $title, $description = '', $options = [], $extra_attr = '', $extra_class = '') {
        $id = '';
        if (strpos($name,'[')) {
            $id = $type.'_'.str_replace(']','',str_replace('[','_',$name));
        }else{
            $id = $type.'_'.$name;
        }
        $item = [
            'id'          => $id,
            'name'        => $name,
            'type'        => $type,
            'title'       => $title,
            'description' => $description,
            'options'     => $options,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class
        ];

        /* 特殊类型处理*/
        if($type=='daterange'){
            $daterange = $this->staticFiles['daterange'];
            $staticFiles = parent::getRemoteLibs('daterange',$daterange);
            $item['staticFiles'] = $staticFiles;
        }
        if($type=='icon'){
            $fontawesome_iconpicker = $this->staticFiles['fontawesome_iconpicker'];
            $staticFiles = parent::getRemoteLibs('fontawesome-iconpicker',$fontawesome_iconpicker);
            $item['staticFiles'] = $staticFiles;
        }
        if($type=='select2'){
            $select2 = $this->staticFiles['select2'];
            $staticFiles = parent::getRemoteLibs('select2',$select2);
            $item['staticFiles'] = $staticFiles;
        }
        if($type=='editor'){
            $item['havehook'] = '';
        }

        $this->formItems[] = $item;

        return $this;
    }

    /**
     * 设置表单表单数据
     * @param $form_data 表单数据
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function setFormData($form_data) {
        $this->formData = $form_data;
        return $this;
    }

    /**
     *添加按钮
     *@param $type 按钮类型
     *@param $title 按钮标题
     *@param $title 提交地址
     *@return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function addButton($type='submit',$title='',$url=''){
        $attr = [];
        $attr['type'] = 'button';
        switch ($type) {
            case 'submit'://确认按钮
                if ($url!= '') {
                    $this->setPostUrl($url);
                }
                if ($title == '') {
                    $title ='确定';
                }

                $ajax_submit = '';
                if ($this->ajaxSubmit==true) {
                    $ajax_submit='js-ajax-submit';
                }

                $attr['class'] = "btn btn-primary {$ajax_submit}";
                $attr['type'] = 'submit';
                $attr['target-form'] = 'form-builder';
                break;
            case 'back'://返回
                if ($title == '') {
                    $title ='返回';
                }
                $attr['onclick'] = empty($url)?'javascript:history.back(-1);return false;':$url;
                $attr['class'] = 'btn btn-default';
                break;
            case 'reset'://重置
                if ($title == '') {
                    $title ='重置';
                }
                $attr['type'] = 'reset';
                $attr['class'] = 'btn btn-default btn-warning';
                break;
            case 'link'://链接
                if ($title == '') {
                    $title ='按钮';
                }
                $attr['onclick'] = 'javascript:location.href=\''.$url.'\';return false;';
                break;

            default:
                break;
        }
        return $this->button($title, $attr);
    }

    /**
     * 添加按钮
     * @param  [type] $title [description]
     * @param  array  $attr  [description]
     * @return [type]        [description]
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function button($title, $attr = [])
    {
        $this->buttonList[] = ['title' => $title, 'attr' => $attr];
        return $this;
    }

    /**
     * 渲染模板
     * @param  string $template_name 模板名
     * @param  array $vars 模板变量
     * @param  string $replace
     * @param  string $config
     * @return parent::fetch('formbuilder');
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function fetch($template_name='formbuilder',$vars =[], $replace ='', $config = '') {
        //额外已经构造好的表单项目与单个组装的的表单项目进行合并
        if (!empty($this->extraItems)) {
            $this->formItems = array_merge($this->formItems, $this->extraItems);
        }

        //过来表单项
        $this->formItems = $this->buildFormItems($this->formItems);

        //设置post_url默认值
        $this->postUrl=$this->postUrl? $this->postUrl : $this->request->url(true);

        //编译表单值
        if ($this->formData) {
            foreach ($this->formItems as &$item) {
                if (!in_array($item['type'],['group','section','self_html','tab','form_inline'])) {
                    if ($item['name']!='') {
                        if ($item['type']=='editor'){
                            $value = isset($this->formData[$item['name']]) ? $this->formData[$item['name']] : '';
                            $content = hook_one('editor',['value'=>$value,'name'=>$item['name'],'id'=>$item['id'],$item['options']]);
                            if(!empty($content)){
                                $item['value'] = $content;
                                $item['havehook'] = 'editor';
                            }else{
                                $item['value'] = $value;
                                $item['havehook'] = '';
                            }
                        }else{
                            if (isset($this->formData[$item['name']])) {
                                $item['value'] = $this->formData[$item['name']];
                            }
                        }

                    }
                } else{
                    $item=$this->setSpecialItemData($item);
                }

            }
        }

        /**
         * 设置按钮
         */
        if (empty($this->buttonList)) {
            $this->addButton('submit')->addButton('back');
        }

        //编译按钮的html属性
        foreach ($this->buttonList as &$button) {
            $button['attr'] = $this->compileHtmlAttr($button['attr']);
        }

        $template_val = [
            'meta_title'          => $this->metaTitle,// 页面标题
            'tips'                => $this->tips,// 页面提示说明
            'show_box_header'     => empty($this->tips)?0:1,//是否显示box_header
            'tips_type'       => empty($this->tipsType)?'info':$this->tipsType,//提示类型
            'is_ajax_sumbit'  => $this->ajaxSubmit ? 1 : 0, //是否ajax提交
            'post_url'        => $this->postUrl,//表单提交地址
            'fieldList'       => $this->formItems,//表单项目
            'button_list'     => $this->buttonList,//按钮组
            'top_html'      => $this->topHtml,//顶部HTML代码
            'extra_html'      => $this->extraHtml//额外HTML代码
        ];
        if(count($this->tabNav)>0){
            $template_val['tab_nav']=$this->tabNav;
        }
        if(count($this->groupTabNav)>0){
            $template_val['grouptabNav']=$this->groupTabNav;
        }

        $this->assign($template_val);
        $templateFile = parent::getResourcePath('tpl') .$template_name.'.html';
        return parent::fetch($templateFile);
    }

    /**
     * 设置特殊项的值
     * @param $item
     */
    private function setSpecialItemData($item){
        $_formData = $this->formData;
        if($item['type']=='group'){
            foreach ($item['options'] as $gkey => $gvalue) {
                if (!in_array($gvalue['type'],['group','section','self_html','tab'])){
                    $gitemvalue = isset($_formData[$gvalue['name']])?$_formData[$gvalue['name']]:'';
                    $item['options'][$gkey]['value'] = $gitemvalue;
                } else{
                    if(isset($gvalue['options'])){
                        $item['options'][$gkey]=$this->setSpecialItemData($gvalue);
                    }
                }
            }
        }
        if($item['type']=='tab'){
            if(isset($item['options'])){
                $tab_field = $item['options'];
                foreach ($tab_field as $tab_field_key => $tab_field_value) {
                    foreach ($tab_field_value['options'] as $tab_option_key=>$tab_option_val){
                        if (!in_array($tab_option_val['type'],['group','section','self_html','tab'])){
                            $tab_item_value = isset($_formData[$tab_option_val['name']])?$_formData[$tab_option_val['name']]:'';
                            $item['options'][$tab_field_key]['options'][$tab_option_key]['value'] = $tab_item_value;
                        }else{
                            if(isset($tab_option_val['options'])){
                                $item['options'][$tab_field_key]['options'][$tab_option_key]=$this->setSpecialItemData($tab_option_val);
                            }
                        }
                    }
                }
            }
        }
        if($item['type']=='form_inline'){
            foreach ($item['options'] as $gkey => $gvalue) {
                if (!in_array($gvalue['type'],['group','section','self_html','tab','form_inline'])){
                    $gitemvalue = $_formData[$gvalue['name']];
                    $item['options'][$gkey]['value'] = $gitemvalue;
                } else{
                    if(isset($gvalue['options'])){
                        $item['options'][$gkey]=$this->setSpecialItemData($gvalue);
                    }
                }
            }
        }
        unset($_formData);
        return $item;
    }

    /**
     * 构建表单项数据builderFormItems
     * @param  [type] $formItems [description]
     * @return [type] [description]
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function buildFormItems($formItems = [])
    {
        if (!$formItems) {
            return false;
        }

        foreach ($formItems as $key => &$item) {
            if (!in_array($item['type'], $this->fieldsItemsList)) {
                $item['FormBuilderExtend']='FormBuilderExtend';//扩展字段
                continue;
            }
            switch ($item['type']) {
                case 'hidden':
                    $item['extra_class']='hide';
                    break;
                case 'picture':
                    $item['extra']=[
                        'field_body_class'=>'col-md-6',
                        'field_help_block_class'=>'col-md-6 col-md-offset-2 hide',
                        'field_body_extra'=>'style="padding-bottom: 5px;padding-left: 5px;"'
                    ];
                    break;
                case 'pictures':
                    $item['extra']=[
                        'field_body_class'=>'col-md-8',
                        'field_help_block_class'=>'col-md-6 col-md-offset-2 hide',
                        'field_body_extra'=>'style="padding-bottom: 5px;padding-left: 5px;"'
                    ];
                    break;
                case 'repeater':
                    $item['extra']=[
                        'field_body_class'=>'col-md-10',
                        'field_help_block_class'=>'col-md-6 col-md-offset-2',
                    ];
                    break;
                case 'ueditor':
                case 'editor':
                    $item['extra']=[
                        'field_body_class'=>'col-md-10',
                        'field_help_block_class'=>'col-md-6 col-md-offset-2',
                    ];
                    break;
                case 'textarea':
                    $item['extra']=[
                        'field_body_class'=>'col-md-6',
                        'field_help_block_class'=>'col-md-6 col-md-offset-2',
                    ];
                    break;
                case 'self':
                    $item['extra']=[
                        'field_body_class'=>'col-md-10',
                        'field_help_block_class'=>'hide',
                    ];
                    break;
                default:
                    break;
            }
        }

        return $formItems;
    }

    /**
     * 字段模版
     * @param  array $field [description]
     * @return [type] [description]
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function fieldType($field = [])
    {
        if (!is_array($field)) {
            $field = $field->toArray();
        }
        $field_type = $field['type'];

        $id = '';
        if (strpos($field['name'],'[')) {
            $id = $field_type.'_'.str_replace(']','',str_replace('[','_',$field['name']));
        }else{
            $id = $field_type.'_'.$field['name'];
        }
        $field['id'] = $id;

        /* 特殊类型处理*/
        if($field_type=='daterange'){
            $daterange = $this->staticFiles['daterange'];
            $staticFiles = parent::getRemoteLibs('daterange',$daterange);
            $field['staticFiles'] = $staticFiles;
        }
        if($field_type=='icon'){
            $fontawesome_iconpicker = $this->staticFiles['fontawesome_iconpicker'];
            $staticFiles = parent::getRemoteLibs('fontawesome-iconpicker',$fontawesome_iconpicker);
            $field['staticFiles'] = $staticFiles;
        }
        if($field_type=='select2'){
            $select2 = $this->staticFiles['select2'];
            $staticFiles = parent::getRemoteLibs('select2',$select2);
            $field['staticFiles'] = $staticFiles;
        }

        $this->assign('field',$field);
        if (in_array($field_type, $this->fieldsItemsList)) {
            $field_template = parent::getResourcePath('tpl')  . 'Fields'.DIRECTORY_SEPARATOR.$field_type.'.html';
            return parent::fetch($field_template);
        } else{
            hook('FormBuilderExtend', ['field' => $field]);
        }
    }

}
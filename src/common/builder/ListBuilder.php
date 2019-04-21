<?php
// +----------------------------------------------------------------------
// | BetaBuilder [ Beta Base CNF System ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.betaec.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: Johnny <johnnycaimail@yeah.net>
// +----------------------------------------------------------------------
// | 基于ThinkCMF 5.1 开发的自动化生成列表表单
// +----------------------------------------------------------------------

namespace app\common\builder;

/**
 * 快速建立列表页面
 * Class ListBuilder
 * @package betaos\betabuilder
 * @author Johnny <johnnycaimail@yeah.net>
 */
class ListBuilder extends Builder{

    private $topButtonList   = [];   // 顶部工具栏按钮组
    private $advanced_search         = [];             //添加高级搜索
    private $action_url = '';
    private $tabNav          = [];           // 页面Tab导航
    private $tableColumns    = [];    //表格数据标题
    private $tableDataList   = []; // 表格数据列表
    private $tablePrimaryKey = 'id';  // 表格数据列表主键字段名
    private $tableDataPage = [];        // 表格数据分页
    private $staticFiles;     // 静态资源文件
    private $rightButtonList = []; // 表格右侧操作按钮组
    private $alterDataList   = [];   // 表格数据列表重新修改的项目
    private $rightButtonType = 1;  //右边按钮类型

    /**
     * 添加高级搜索功能
     * @param string $param 参数按照下面示例
     * $advancedSearch = [
    'fields' => [
    ['name'=>'status','type'=>'select','title'=>'状态','options'=>[1=>'正常',2=>'待审核']],
    ['name'=>'create_time_range','type'=>'daterange','extra_attr'=>'placeholder="创建时间范围"'],
    ['name'=>'keyword','type'=>'text','extra_attr'=>'placeholder="请输入查询关键字"'],
    ],
    'title' => '查询',
    'url' => cmf_url('Demo/AdminIndex/lists'),

    ];
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function addAdvanceSearch($param = [])
    {
        if(!isset($param['fields'])){
            $param['need_advanced_search'] = 0;
        }else{
            $param['need_advanced_search'] = 1;
            $param['title'] = empty($param['title']) ? $param['title'] : '查询';
            $param['url'] = !empty($param['url']) ? $param['url'] : url($this->request->module() . '/' . $this->request->controller() . '/' . $this->request->action());
        }
        $this->advanced_search = $param;
        return $this;
    }

    /**
     * 设置Tab按钮列表
     * @param $tab_list Tab列表  array(
     *                               'title' => '标题',
     *                               'href' => 'http://www.xxx.com'
     *                           )
     * @param $current 当前tab
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function setTabNav($tab_list, $current) {
        $this->tabNav = [
            'tab_list' => $tab_list,
            'current' => $current
        ];
        return $this;
    }

    /**
     * 加一个表格字段
     * @param $name  列字段
     * @param $title 列显示文字
     * @param null $type 列的类型：status、bool、label、byte、icon、date、datetime、time、image、images、url、array、switch、text、number、callback
     * @param null $param 类型的附加参数
     * @param null $extra_attr 表头th标签的自定义扩展
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function keyListItem($name, $title,$type = null, $param = null,$extra_attr=null) {

        $column = [
            'name'  => $name,
            'title' => $title,
            'type'  => $type,
            'param' => $param,
            'extra_attr'=>$extra_attr
        ];
        $this->tableColumns[] = $column;
        return $this;
    }

    /**
     * 表格数据列表
     * @param $table_data_list
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function setListData($table_data_list) {
        //如果请求方式不是ajax，则直接返回对象
        $isAjax = $this->request->isAjax();
        //if (!$isAjax) return $this;
        $this->tableDataList = $table_data_list;
        return $this;
    }

    /**
     * 表格数据列表的主键名称
     * @param string $table_primary_key
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function setListPrimaryKey($table_primary_key = 'id') {
        $this->tablePrimaryKey = $table_primary_key;
        return $this;
    }

    /**
     * 设置提交请求地址
     * @param  string $url
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function setActionUrl($url='')
    {
        $this->action_url = !empty($url) ? $url : $this->request->url();
        return $this;
    }

    /**
     * 加入一个列表顶部工具栏按钮
     * 在使用预置的几种按钮时，比如我想改变新增按钮的名称
     * 那么只需要$builder->addTopBtn('add', array('title' => '这个是标题'))
     * 如果想改变地址甚至新增一个属性用上面类似的定义方法
     * 注意：启用/禁用/显示/隐藏类型按钮默认绑定对字段为'status'的操作，如果字段名非'status'，需要在$attribute数组中指定'field'的字段名，例如：->addRightButton('forbid',['field'=>'post_status'])
     * @param string $type 按钮类型，主要有add/resume/forbid/recycle/restore/delete/self七几种取值
     * @param array  $attr 按钮属性，一个定了标题/链接/CSS类名等的属性描述数组
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function addTopBtn($type, $attribute = null) {
        return $this->addBtn('top',$type, $attribute);
    }

    //添加顶部按钮，用法同上（兼容性）
    public function addTopButton($type, $attribute = null) {
        return $this->addBtn('top',$type, $attribute);
    }

    /**
     * 添加按钮
     * @param  string $position 位置
     * @param  string $type 类型
     * @param  array $attribute 属性数组
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function addBtn($position='top',$type, $attribute = null)
    {
        $model_name = !empty($attribute['model']) ? $attribute['model'] : ($this->pluginName ? input('param._controller') : $this->request->controller());
        $field_name = !empty($attribute['field']) ? $attribute['field'] : 'status';
        $query_model_params = $this->preQueryConnector . 'model=' . $model_name;
        switch ($type) {
            case 'addnew':  // 添加新增按钮
                // 预定义按钮属性以简化使用
                $my_attribute['type'] = 'a';
                $my_attribute['title'] = '添加';
                $my_attribute['icon'] = 'fa fa-plus';
                $my_attribute['class'] = 'btn btn-primary btn-sm';
                $my_attribute['href'] = $this->pluginName ? parent::plugin_url('edit') :
                    url($this->request->module() . '/' . $this->request->controller() . '/'.(!empty($attribute['action'])?$attribute['action']:'add'));
                break;
            case 'resume':  // 添加启用按钮(禁用的反操作)
                //预定义按钮属性以简化使用
                $my_attribute['type'] = 'sumbit';
                $my_attribute['title'] = '启用';
                $my_attribute['target-form'] = 'ids';
                $my_attribute['icon'] = 'fa fa-play';
                $my_attribute['class'] = 'btn btn-success btn-sm js-ajax-submit';
                $my_attribute['data-subcheck'] = 'true';
                $my_attribute['confirm-info'] = '您确定要执行启用操作吗？';
                $my_attribute['data-action'] = $this->pluginName ? parent::plugin_url('setStatus', ['status' => 'resume']) :
                    url($this->request->module() . '/' . $this->request->controller() . '/'.(!empty($attribute['action'])?$attribute['action']:'resumes'), ['op' => 'resume','field' => $field_name,'keyfield' => $this->tablePrimaryKey,'model'=>$model_name]);
                break;
            case 'forbid':  // 添加禁用按钮(启用的反操作)
                // 预定义按钮属性以简化使用
                $my_attribute['type'] = 'sumbit';
                $my_attribute['title'] = '禁用';
                $my_attribute['target-form'] = 'ids';
                $my_attribute['icon'] = 'fa fa-pause';
                $my_attribute['class'] = 'btn btn-warning btn-sm js-ajax-submit';
                $my_attribute['data-subcheck'] = 'true';
                $my_attribute['data-msg'] = '您确定要执行禁用操作吗？';
                $my_attribute['data-action'] = $this->pluginName ? parent::plugin_url('setStatus', ['status' => 'forbid']) :
                    url($this->request->module() . '/' . $this->request->controller() . '/'.(!empty($attribute['action'])?$attribute['action']:'forbids'), ['op' => 'forbid','field' => $field_name,'keyfield' => $this->tablePrimaryKey,'model'=>$model_name]);
                break;
            case 'recycle':  // 添加回收按钮(还原的反操作)
                // 预定义按钮属性以简化使用
                $my_attribute['type'] = 'sumbit';
                $my_attribute['title'] = '回收';
                $my_attribute['target-form'] = 'ids';
                $my_attribute['icon'] = 'fa fa-recycle';
                $my_attribute['class'] = 'btn btn-danger btn-sm js-ajax-submit';
                $my_attribute['data-subcheck'] = 'true';
                $my_attribute['data-msg'] = '您确定要执行回收操作吗？';
                $my_attribute['data-action'] = $this->pluginName ? parent::plugin_url('setStatus', ['status' => 'recycle']) :
                    url($this->request->module() . '/' . $this->request->controller() . '/'.(!empty($attribute['action'])?$attribute['action']:'recycles'), ['op' => 'recycle','field' => $field_name,'keyfield' => $this->tablePrimaryKey,'model'=>$model_name]);
                break;
            case 'restore':  // 添加还原按钮(回收的反操作)
                // 预定义按钮属性以简化使用
                $my_attribute['type'] = 'sumbit';
                $my_attribute['title'] = '还原';
                $my_attribute['target-form'] = 'ids';
                $my_attribute['icon'] = 'fa fa-window-restore';
                $my_attribute['class'] = 'btn btn-success btn-sm js-ajax-submit';
                $my_attribute['data-subcheck'] = 'true';
                $my_attribute['data-msg'] = '您确定要执行还原操作吗？';
                $my_attribute['data-action'] = $this->pluginName ? parent::plugin_url('setStatus', ['status' => 'restore']) :
                    url($this->request->module() . '/' . $this->request->controller() . '/'.(!empty($attribute['action'])?$attribute['action']:'restores'), ['op' => 'restore','field' => $field_name,'keyfield' => $this->tablePrimaryKey,'model'=>$model_name]);
                break;
            case 'delete': // 添加删除按钮(我没有反操作，删除了就没有了，就真的找不回来了)
                // 预定义按钮属性以简化使用
                $my_attribute['type'] = 'sumbit';
                $my_attribute['title'] = '删除';
                $my_attribute['target-form'] = 'ids';
                $my_attribute['icon'] = 'fa fa-remove';
                $my_attribute['class'] = 'btn btn-danger js-ajax-submit btn-sm';
                $my_attribute['data-msg'] = '您确定要执行删除操作吗？';
                $my_attribute['data-action'] = $this->pluginName ? parent::plugin_url('setStatus', ['status' => 'delete']) : url(
                    $this->request->module() . '/' . $this->request->controller() . '/'.(!empty($attribute['action'])?$attribute['action']:'deletes'),
                    array(
                        'op' => 'delete',
                        'field' => $field_name,
                        'keyfield' => $this->tablePrimaryKey,
                        'model'=>$model_name
                    )
                );
                break;
            case 'sort':  // 添加排序按钮
                // 预定义按钮属性以简化使用
                $my_attribute['type'] = 'sumbit';
                $my_attribute['title'] = '排序';
                $my_attribute['target-form'] = 'ids';
                $my_attribute['icon'] = 'fa fa-sort';
                $my_attribute['name'] = '排序';
                $my_attribute['class'] = 'btn btn-info btn-sm js-ajax-submit';
                $my_attribute['data-msg'] = '您确定要执行排序操作吗？';
                $my_attribute['data-action'] = $this->pluginName ? parent::plugin_url('sort') :
                    url($this->request->module() . '/' . $this->request->controller() . '/'.(!empty($attribute['action'])?$attribute['action']:'sort'));
                break;
            case 'self': //添加自定义按钮(第一原则使用上面预设的按钮，如果有特殊需求不能满足则使用此自定义按钮方法)
                // 预定义按钮属性以简化使用
                $my_attribute['target-form'] = isset($attribute['target-form']) ? (empty($attribute['target-form']) ? $attribute['target-form'] : 'ids') : 'ids';
                $my_attribute['class'] = isset($attribute['class']) ? (empty($attribute['class']) ? $attribute['class'] : 'btn btn-danger btn-sm') : 'btn btn-danger btn-sm';
                if (empty($my_attribute['title'])) {
                    $my_attribute['title'] = '该自定义按钮未配置属性';
                }
                break;
        }

        /**
         * 如果定义了属性数组则与默认的进行合并
         * 用户定义的同名数组元素会覆盖默认的值
         */
        if ($attribute && is_array($attribute)) {
            $my_attribute = array_merge($my_attribute, $attribute);
        }
        // 这个按钮定义好了把它丢进按钮池里
        if ($position=='top') {
            if(empty($my_attribute['type'])){
                $my_attribute['type'] = 'a';
            }
            $this->topButtonList[] = $my_attribute;
        }

        return $this;
    }

    /**
     * 加入一个数据列表右侧按钮
     * 在使用预置的几种按钮时，比如我想改变编辑按钮的名称
     * 那么只需要$builder->addRightpButton('edit', array('title' => '换个马甲'))
     * 如果想改变地址甚至新增一个属性用上面类似的定义方法
     * 因为添加右侧按钮的时候你并没有办法知道数据ID，于是我们采用__data_id__作为约定的标记
     * __data_id__会在fetch方法里自动替换成数据的真实ID
     * @param string $type 按钮类型，edit/forbid/recycle/restore/delete/self六种取值
     * @param array  $attribute 按钮属性，一个定了标题/链接/CSS类名等的属性描述数组
     * @param array  $condition 条件表达式
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function addRightButton($type, $attribute = null,$condition=[]) {

        $model_name = !empty($attribute['model']) ? $attribute['model'] : ($this->pluginName ? input('param._controller') : $this->request->controller());
        $query_model_params = $this->preQueryConnector.'model='.$model_name;
        $status_field = isset($attribute['field']) ? $attribute['field'] : 'status';
        switch ($type) {
            case 'view':  // 编辑按钮
                // 预定义按钮属性以简化使用
                $my_attribute['title'] = isset($attribute['title']) ? $attribute['title'] : '查看';
                $my_attribute['icon'] = 'fa fa-eye';
                $my_attribute['class'] = $this->rightButtonType==1 ? 'btn btn-info btn-xs':'';
                $my_attribute['tips'] = isset($attribute['tips']) ? $attribute['tips'] : '查看';
                $my_attribute['href']  =  $this->pluginName ? parent::plugin_url((!empty($attribute['action'])?$attribute['action']:'view'),[$this->tablePrimaryKey => '__data_id__']) :
                    url($this->request->module().'/'.$this->request->controller().'/'.(!empty($attribute['action'])?$attribute['action']:'view'),[$this->tablePrimaryKey => '__data_id__']);

                break;
            case 'edit':  // 编辑按钮
                // 预定义按钮属性以简化使用
                $my_attribute['title'] = isset($attribute['title']) ? $attribute['title'] : '编辑';
                $my_attribute['icon'] = 'fa fa-edit';
                $my_attribute['tips'] = isset($attribute['tips']) ? $attribute['tips'] : '编辑';
                $my_attribute['class'] = $this->rightButtonType==1 ? 'btn btn-primary btn-xs':'';
                $my_attribute['href']  =  $this->pluginName ? parent::plugin_url((!empty($attribute['action'])?$attribute['action']:'edit'),[$this->tablePrimaryKey => '__data_id__']) :
                    url($this->request->module().'/'.$this->request->controller().'/'.(!empty($attribute['action'])?$attribute['action']:'edit'),[$this->tablePrimaryKey => '__data_id__']);

                break;
            case 'forbid':  // 改变记录状态按钮，会更具数据当前的状态自动选择应该显示启用/禁用
                //预定义按钮属
                $my_attribute['type'] = 'forbid';
                $my_attribute['0']['title'] = !empty($attribute['0']['title']) ? $attribute['0']['title'] : '启用';
                $my_attribute['0']['class'] = $this->rightButtonType==1 ? 'btn btn-success btn-xs js-ajax-dialog-btn':'ajax-get confirm';
                $my_attribute['0']['data-msg'] = $this->rightButtonType==1 ? '您确定要启用吗？':'ajax-get confirm';
                $my_attribute['0']['tips'] = !empty($attribute['0']['tips']) ? $attribute['0']['tips'] : '启用';
                $my_attribute['0']['href']  = $this->pluginName ? parent::plugin_url('setStatus',['op' => 'resume','field' => $status_field,'ids' => '__data_id__']) : cmf_url(
                    $this->request->module().'/'.$this->request->controller().'/'.(!empty($attribute['action'])?$attribute['action']:'resume'),
                    array(
                        'op' => 'resume',
                        'field' => $status_field,
                        'keyfield' => $this->tablePrimaryKey,
                        'model' => $model_name,
                        'ids' => '__data_id__',
                    )
                );
                $my_attribute['1']['title'] = !empty($attribute['1']['title']) ? $attribute['1']['title'] : '禁用';
                $my_attribute['1']['class'] = $this->rightButtonType==1 ? 'btn btn-warning btn-xs js-ajax-dialog-btn':'ajax-get confirm';
                $my_attribute['1']['data-msg'] = $this->rightButtonType==1 ? '您确定要禁用吗？':'ajax-get confirm';
                $my_attribute['1']['tips'] = !empty($attribute['1']['tips']) ? $attribute['1']['tips'] : '禁用';
                $my_attribute['1']['href']  = $this->pluginName ? parent::plugin_url('setStatus',['op' => 'forbid','field' => $status_field,'ids' => '__data_id__']) : cmf_url(
                    $this->request->module().'/'.$this->request->controller().'/'.(!empty($attribute['action'])?$attribute['action']:'disable'),
                    array(
                        'op' => 'forbid',
                        'field' => $status_field,
                        'keyfield' => $this->tablePrimaryKey,
                        'model' => $model_name,
                        'ids' => '__data_id__',
                    )
                );
                $my_attribute['field'] = $status_field;

                break;
            case 'hide':  // 改变记录状态按钮，会更具数据当前的状态自动选择应该显示隐藏/显示
                // 预定义按钮属
                $my_attribute['type'] = 'hide';
                $my_attribute['0']['title'] = !empty($attribute['0']['title']) ? $attribute['0']['title'] : '显示';
                $my_attribute['0']['tips'] = !empty($attribute['0']['tips']) ? $attribute['0']['tips'] : '显示';
                $my_attribute['0']['class'] = $this->rightButtonType==1 ? 'btn btn-success btn-xs js-ajax-dialog-btn':'ajax-get confirm';
                $my_attribute['0']['data-msg'] = $this->rightButtonType==1 ? '您确定要显示吗？':'ajax-get confirm';
                $my_attribute['0']['href']  = $this->pluginName ? parent::plugin_url('setStatus',['op' => 'show','field' => $status_field,'ids' => '__data_id__']) : cmf_url(
                    $this->request->module().'/'.$this->request->controller().'/'.(!empty($attribute['action'])?$attribute['action']:'show'),
                    array(
                        'op' => 'show',
                        'field' => $status_field,
                        'keyfield' => $this->tablePrimaryKey,
                        'model' => $model_name,
                        'ids' => '__data_id__',
                    )
                );
                $my_attribute['1']['title'] = !empty($attribute['1']['title']) ? $attribute['1']['title'] : '隐藏';
                $my_attribute['1']['tips'] = !empty($attribute['1']['tips']) ? $attribute['1']['tips'] : '隐藏';
                $my_attribute['1']['class'] = $this->rightButtonType==1 ? 'btn btn-info btn-xs js-ajax-dialog-btn':'ajax-get confirm';
                $my_attribute['1']['data-msg'] = $this->rightButtonType==1 ? '您确定要隐藏吗？':'ajax-get confirm';
                $my_attribute['1']['href']  = $this->pluginName ? parent::plugin_url('setStatus',['op' => 'show','field' => $status_field,'ids' => '__data_id__']) : url(
                    $this->request->module().'/'.$this->request->controller().'/'.(!empty($attribute['action'])?$attribute['action']:'hide'),
                    array(
                        'op' => 'hide',
                        'field' => $status_field,
                        'keyfield' => $this->tablePrimaryKey,
                        'model' => $model_name,
                        'ids' => '__data_id__',
                    )
                );
                $my_attribute['field'] = $status_field;

                break;
            case 'recycle':
                // 预定义按钮属性以简化使用
                $my_attribute['title'] = isset($attribute['title']) ? $attribute['title'] : '回收';
                $my_attribute['tips'] = isset($attribute['tips']) ? $attribute['tips'] : '回收';
                $my_attribute['icon'] = 'fa fa-recycle';
                $my_attribute['class'] = $this->rightButtonType==1 ? 'btn btn-danger btn-xs js-ajax-dialog-btn':'ajax-get confirm';
                $my_attribute['data-msg'] = $this->rightButtonType==1 ? '您确定要回收吗？':'ajax-get confirm';
                $my_attribute['href'] = $this->pluginName ? parent::plugin_url('setStatus',['op'=>'recycle','field' => $status_field,'ids' => '__data_id__']) :  url(
                    $this->request->module().'/'.$this->request->controller().'/'.(!empty($attribute['action'])?$attribute['action']:'recycle'),
                    array(
                        'op' => 'recycle',
                        'field' => $status_field,
                        'keyfield' => $this->tablePrimaryKey,
                        'model' => $model_name,
                        'ids' => '__data_id__',
                    )
                );
                break;
            case 'restore':
                // 预定义按钮属性以简化使用
                $my_attribute['title'] = isset($attribute['title']) ? $attribute['title'] : '还原';
                $my_attribute['tips'] = isset($attribute['tips']) ? $attribute['tips'] : '还原';
                $my_attribute['class'] = $this->rightButtonType==1 ? 'btn btn-success btn-xs js-ajax-dialog-btn':'ajax-get confirm';
                $my_attribute['data-msg'] = $this->rightButtonType==1 ? '您确定要还原吗？':'ajax-get confirm';
                $my_attribute['href'] = $this->pluginName ? parent::plugin_url('setStatus',['op'=>'restore','field' => $status_field,'ids' => '__data_id__']) :  url(
                    $this->request->module().'/'.$this->request->controller().'/'.(!empty($attribute['action'])?$attribute['action']:'restore'),
                    array(
                        'op' => 'restore',
                        'field' => $status_field,
                        'keyfield' => $this->tablePrimaryKey,
                        'model' => $model_name,
                        'ids' => '__data_id__',
                    )
                );
                break;
            case 'delete':
                // 预定义按钮属性以简化使用
                $my_attribute['title'] = isset($attribute['title']) ? $attribute['title'] : '删除';
                $my_attribute['tips'] = isset($attribute['tips']) ? $attribute['tips'] : '删除';
                $my_attribute['icon'] = 'fa fa-remove';
                $my_attribute['class'] = $this->rightButtonType==1 ? 'btn btn-danger btn-xs js-ajax-delete':'ajax-get confirm';
                $my_attribute['confirm-info'] = '您确定要执行删除操作吗？';
                $my_attribute['href'] = $this->pluginName ? parent::plugin_url('setStatus',['op'=>'delete','field' => $status_field,'ids' => '__data_id__']) :  url(
                    $this->request->module().'/'.$this->request->controller().'/'.(!empty($attribute['action'])?$attribute['action']:'delete'),
                    array(
                        'op' => 'delete',
                        'field' => $status_field,
                        'keyfield' => $this->tablePrimaryKey,
                        'model' => $model_name,
                        'ids' => '__data_id__',
                    )
                );
                break;
            case 'self':
                // 预定义按钮属性以简化使用
                $my_attribute['class'] = '';
                if (empty($my_attribute['title'])) {
                    $my_attribute['title'] = '该自定义按钮未配置属性';
                }

                break;
        }

        // 如果定义了属性数组则与默认的进行合并，详细使用方法参考上面的顶部按钮
        /**
         * 如果定义了属性数组则与默认的进行合并
         * 用户定义的同名数组元素会覆盖默认的值
         */
        if ($attribute && is_array($attribute)) {
            $my_attribute = array_merge($my_attribute, $attribute);
        }

        //支持layer
        if (isset($attribute['layer'])) {
            if (is_array($attribute['layer'])) {
                $layer_width = !empty($attribute['layer']['width']) ? $attribute['layer']['width']:'60%';
                $layer_height = !empty($attribute['layer']['height']) ? $attribute['layer']['height']:'60%';
            }
            $layer_shade = !empty($attribute['layer']['shade']) ? $attribute['layer']['shade']:'0.8';
            $my_attribute['href'] = 'javascript:openIframeLayer(\''.$my_attribute['href'].'\',\''.$my_attribute['title'].'\',{shade: '.$layer_shade.',area: [\''.$layer_width.'\',\''.$layer_height.'\']});';
            unset($my_attribute['layer']);
        }

        if (!empty($condition)) {
            $my_attribute['condition'] = $condition;
        }

        // 这个按钮定义好了把它丢进按钮池里
        $this->rightButtonList[] = $my_attribute;
        return $this;
    }

    /**
     * 设置分页
     * @param  [type] $page 分页对象
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function setListPage($page) {
        $this->tableDataPage = $page;
        return $this;
    }

    /**
     * 修改列表数据
     * 有时候列表数据需要在最终输出前做一次小的修改
     * 比如管理员列表ID为1的超级管理员右侧编辑按钮不显示删除
     * @param $page
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function alterListData($condition, $alter_data) {
        $this->alterDataList[] = [
            'condition' => $condition,
            'alter_data' => $alter_data
        ];
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
     * 设置列表按钮类型
     * @param $type 类型(1 label,2 下拉,3,链接)
     * @return $this
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function setRightButton($type) {
        $this->rightButtonType = $type;
        return $this;
    }

    /**
     * 渲染页面
     * @param string $template_name
     * @param string $vars
     * @param string $replace
     * @param string $config
     * @return mixed|\think\response\Json
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function fetch($template_name='listbuilder',$vars ='', $replace ='', $config = '') {

        //编译top_button_list中的HTML属性
        if ($this->topButtonList) {
            foreach ($this->topButtonList as &$button) {
                $button['primary-key'] = $this->tablePrimaryKey;
                $button['attribute'] = $this->compileHtmlAttr($button);
            }
        }

        foreach ($this->tableDataList as &$data) {
            //编译表格列按钮
            $data = $this->compileRightButtons($data);
            //编译列表列值
            $data = $this->compileTableColumns($data);

            /**
             * 修改列表数据
             * 有时候列表数据需要在最终输出前做一次小的修改
             * 比如管理员列表ID为1的超级管理员右侧编辑按钮不显示删除
             */
            if ($this->alterDataList) {
                foreach ($this->alterDataList as $alter) {
                    if ($data[$alter['condition']['key']] === $alter['condition']['value']) {
                        foreach ($alter['alter_data'] as &$val) {
                            $val = preg_replace(
                                '/__data_id__/i',
                                $data[$this->tablePrimaryKey],
                                $val
                            );
                        }
                        $data = array_merge($data, $alter['alter_data']);
                    }
                }
            }

        }

        //表格列
        $table_column_fields = [];
        foreach ($this->tableColumns as $key => $val) {
            $table_column_fields[$key]['field']=$val['name'];
            $table_column_fields[$key]['title']=$val['title'];
        }

        $template_val = [
            'show_box_header'     => empty($this->tips)?0:1,//是否显示box_header
            'tips_type'       => empty($this->tipsType)?'info':$this->tipsType,//提示类型
            'top_html'      => $this->topHtml,//顶部HTML代码
            'advanced_search' => $this->advanced_search,//高级搜索
            'top_button_list'     => $this->topButtonList,// 顶部工具栏按钮
            'action_url'          => !empty($this->action_url) ? $this->action_url:$this->request->url(),
            'tab_nav'             => $this->tabNav,// 页面Tab导航
            'table_columns'       => $this->tableColumns,// 表格的列
            'table_column_fields' => json_encode($table_column_fields),//表格列bootstrap-table
            'table_primary_key'   => $this->tablePrimaryKey,// 表格数据主键字段名称
            'data_list'     => $this->tableDataList,// 表格数据列表重新修改的项目
            'page'     => $this->tableDataPage, //数据分页
            'extra_html'          => $this->extraHtml, // 额外HTML代码
            'static_addon'        => $this->getPageAddonJsCss()
        ];
        $this->assign($template_val);
        $templateFile = parent::getResourcePath('tpl') .$template_name.'.html';
        return parent::fetch($templateFile);
    }

    /**
     * 获取页面扩展JS和css
     */
    private function getPageAddonJsCss(){
        $pageAddon = [];
        $staticFiles = '';
        $script = '<script>';
        foreach ($this->tableColumns as $val){
            if(!empty($val['type'])){
                switch ($val['type']){
                    case 'switch':
                        $switch = [
                            'bootstrap-switch.css' => 'https://cdn.bootcss.com/bootstrap-switch/3.3.4/css/bootstrap3/bootstrap-switch.min.css',
                            'bootstrap-switch.js' => 'https://cdn.bootcss.com/bootstrap-switch/3.3.4/js/bootstrap-switch.min.js',
                        ];
                        $staticFiles .= parent::getRemoteLibs('bootstrap-switch',$switch);
                        $script .= '$(\'.switch\')[\'bootstrapSwitch\']();';
                        break;
                    default:
                        break;
                }
            }
        }
        $pageAddon['static_file'] = $staticFiles;
        $pageAddon['script'] = $script . '</script>';
        return $pageAddon;
    }

    /**
     * 编译右侧按钮
     * @param  array $data
     * @author Johnny <johnnycaimail@yeah.net>
     */
    public function compileRightButtons($data=[])
    {
        if(!isset($data['right_button'])) $data['right_button']='';
        // 编译表格右侧按钮
        if ($this->rightButtonList) {
            if ($this->rightButtonType==2) {
                $data['right_button'] .='<div class="btn-group">
                  <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                    操作 <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">';
            }

            foreach ($this->rightButtonList as $right_button) {
                //如果条件存在
                if (isset($right_button['condition']) && !empty($right_button['condition'])) {
                    $condition_res = $this->resolveConditionRules($data,$right_button['condition']);
                    if (!$condition_res) {
                        continue;
                    }
                }
                // 禁用按钮与隐藏比较特殊，它需要根据数据当前状态判断是显示禁用还是启用
                if (isset($right_button['type'])) {
                    if ($right_button['type'] === 'forbid' || $right_button['type'] === 'hide'){
                        if(!empty($data['status'])){
                            $right_button = $right_button[$data['status']];
                        }else{
                            if(isset($data[$right_button['field']])){
                                $right_button = $right_button[$data[$right_button['field']]];
                            }
                        }
                    }
                }

                // 将约定的标记__data_id__替换成真实的数据ID
                $right_button['href'] = preg_replace(
                    '/__data_id__/i',
                    $data[$this->tablePrimaryKey],
                    $right_button['href']
                );

                $right_button_show_title = $right_button['title'];
                $right_button['title'] = $right_button['tips'];
                if(isset($right_button['icon'])) $right_button_show_title = '<i class="'.$right_button['icon'].'"></i> ';
                // 编译按钮属性
                $right_button['attribute'] = $this->compileHtmlAttr($right_button);
                switch ($this->rightButtonType) {
                    case 2:
                        $data['right_button'] .= '<li><a '.$right_button['attribute'].'>'.$right_button_show_title.'</a></li>';
                        break;

                    default:
                        $data['right_button'] .= '<a '.$right_button['attribute'].' style="margin-right:6px;">'.$right_button_show_title.'</a>';
                        break;
                }

            }
            if ($this->rightButtonType==2) {
                $data['right_button'] .='</ul></div>';
            }
        }
        return $data;
    }

    /**
     * 编译表格列
     * @param  array $data
     * @author Johnny <johnnycaimail@yeah.net>
     */
    private function compileTableColumns($data = [])
    {
        $result = [];
        // 根据表格标题字段指定类型编译列表数据
        foreach ($this->tableColumns as &$column) {
            //重新赋值一遍解决拿不到获取器的问题
            $result[$column['name']] = $data[$column['name']];

            $column_type_str = explode('_', $column['type']);
            $column_type = $column_type_str[0];
            if($column_type==null){
                $column_name = $column['name'];
                $time_type = ['create_time','delete_time','published_time','update_time'];
                if(in_array($column_name, $time_type)){
                    $column_type = 'datetime';
                }
                if($column_name=='status'){
                    $column_type = 'status';
                }
            }
            switch ($column_type) {
                case 'status':
                    switch($data[$column['name']]){
                        case -1:
                            $data[$column['name']] = '<span class="fa fa-trash text-danger"></span>';
                            break;
                        case 0:
                            $data[$column['name']] = '<span class="fa fa-ban text-danger"></span>';
                            break;
                        case 1:
                            $data[$column['name']] = '<span class="fa fa-check text-success"></span>';
                            break;
                        case 2:
                            $data[$column['name']] = '<span class="fa fa-eye-slash text-warning"></span>';
                            break;
                    }
                    break;
                case 'bool':
                    switch($data[$column['name']]){
                        case 0:
                            $data[$column['name']] = '<span class="fa fa-ban text-danger"></span>';
                            break;
                        case 1:
                            $data[$column['name']] = '<span class="fa fa-check text-success"></span>';
                            break;
                    }
                    break;
                case 'label':
                    if (isset($column_type_str[1])) {
                        switch($column_type_str[1]){
                            case 'bool':
                                if ($data[$column['name']]=='否') {
                                    $data[$column['name']] = '<label class="label label-default">'.$data[$column['name']].'</label>';
                                } elseif ($data[$column['name']]=='是') {
                                    $data[$column['name']] = '<label class="label label-success">'.$data[$column['name']].'</label>';
                                }
                                break;
                            default:
                                $data[$column['name']] = '<label class="label label-'.$column_type_str[1].'">'.$data[$column['name']].'</label>';
                        }
                    }

                    break;
                case 'byte':
                    $data[$column['name']] = beta_format_bytes($data[$column['name']]);
                    break;
                case 'icon':
                    $data[$column['name']] = '<i class="'.$data[$column['name']].'"></i>';
                    break;
                case 'date':
                    $data[$column['name']] = beta_time_format($data[$column['name']], 'Y-m-d');
                    break;
                case 'datetime':
                    $data[$column['name']] = beta_time_format($data[$column['name']]);
                    break;
                case 'time':
                    $data[$column['name']] = beta_time_format($data[$column['name']]);
                    break;
                case 'image':
                    $pic_w = '120';
                    if (!empty($column['param']['width'])) {
                        $pic_w = $column['param']['width'];
                    }
                    $img_src = cmf_get_image_preview_url($data[$column['name']]);
                    $img_html = '';
                    if($img_src!=''){
                        $img_html = '<a href="javascript:parent.imagePreviewDialog(\''.$img_src.'\');"><img class="cover" width="'.$pic_w.'" src="'.$img_src.'"></a>';
                    }
                    $data[$column['name']] = $img_html;
                    break;
                case 'images':
                    $pic_w = '120';
                    if (!empty($column['param']['width'])) {
                        $pic_w = $column['param']['width'];
                    }
                    $temp = explode(',', $data[$column['name']]);

                    $img_src = cmf_get_image_preview_url($temp[0]);
                    $img_html = '';
                    if($img_src!=''){
                        $img_html = '<a href="javascript:parent.imagePreviewDialog(\''.$img_src.'\');"><img class="cover" width="'.$pic_w.'" src="'.$img_src.'"></a>';
                    }
                    $data[$column['name']] = $img_html;
                    break;
                case 'url'://以URL形式添加
                    $column_extra_attr = '';
                    $column_url = $data[$column['name']];
                    if (is_array($column['param'])) {
                        if (isset($column['param']['extra_attr'])) {
                            $column_extra_attr = $this->compileHtmlAttr($column['param']);
                        }
                        if (isset($column['param']['url'])) {
                            $column_url = str_replace('__data_id__',$data[$this->tablePrimaryKey],$column['param']['url']);
                        }
                        if (isset($column['param']['url_callback'])) {
                            $column_url = call_user_func($column['param']['url_callback'], $data[$column['name']]);
                        }

                    }
                    $data[$column['name']] = '<a href="'.$column_url.'" '.$column_extra_attr.'>'.$data[$column['name']].'</a>';
                    break;
                case 'array':
                    if (is_array($column['param'])) {
                        $column_array = $column['param'];
                        $data[$column['name']] = isset($column_array[$data[$column['name']]]) ? $column_array[$data[$column['name']]]:$data[$column['name']];
                    }

                    break;
                case 'switch'://开关
                    switch($data[$column['name']]){
                        case 0:
                            $data[$column['name']] = '<input type="checkbox" class="switch" readonly data-size="mini" />';
                            break;
                        case 1:
                            $data[$column['name']] = '<input type="checkbox" class="switch" data-size="mini" checked readonly />';
                            break;
                    }
                    break;
                case 'text':
                    $textname = $textid = $extra_attr = '';
                    if (!empty($column['param']['name'])) {
                        $textname = $column['param']['name'];
                    }else{
                        $textname = $column['name'].'[]';
                    }
                    if (!empty($column['extra_attr'])) {
                        $extra_attr = $column['extra_attr'];
                    }
                    $textid = 'row_text'.str_replace(']','',str_replace('[','_',$textname)).'_'.$data[$this->tablePrimaryKey];
                    $textval = $data[$column['name']];
                    $texthTML = '<input type="text" class="form-control input-xm" name="'.$textname.'" id="'.$textid.'" value="'.$textval.'" '.$extra_attr.'>';
                    $data[$column['name']] = $texthTML;
                    break;
                case 'number':
                    $textname = $textid = $extra_attr = '';
                    if (!empty($column['param']['name'])) {
                        $textname = $column['param']['name'];
                    }else{
                        $textname = $column['name'].'[]';
                    }
                    if (!empty($column['extra_attr'])) {
                        $extra_attr = $column['extra_attr'];
                    }
                    $textid = 'row_text'.str_replace(']','',str_replace('[','_',$textname)).'_'.$data[$this->tablePrimaryKey];
                    $textval = $data[$column['name']];
                    $texthTML = '<input type="number" class="form-control input-xm" name="'.$textname.'" id="'.$textid.'" value="'.$textval.'" '.$extra_attr.'>';
                    $data[$column['name']] = $texthTML;
                    break;
                case 'callback': // 调用函数
                    if (is_array($column['param'])) {
                        if ($column['param']['callback_fun']) {//存在多个个参数，且为自定义函数
                            $callback_param=$column['param']['sub_param'];
                            array_unshift($callback_param,$data[$column['name']]);
                            $data[$column['name']] = call_user_func_array($column['param']['callback_fun'],$callback_param);
                        } else{//否则为回调函数模式
                            $data[$column['name']] = call_user_func_array($column['param'], array($data[$column['name']]));
                        }

                    } else {
                        $data[$column['name']] = call_user_func($column['param'], $data[$column['name']]);
                    }
                    break;
            }
        }

        if (!empty($data)) {
            $data = !is_array($data) ? $data->toArray() : $data;
            $result = array_merge($result,$data);
            unset($data);
        }

        return $result;
    }

    /**
     * 解析条件规则
     * @param  [type] $rules [description]
     * @return [type] [description]
     * @author Johnny <johnnycaimail@yeah.net>
     */
    private function resolveConditionRules($data=[], $rules=null)
    {
        if (empty($rules) || empty($data)) {
            return false;
        }
        $res = false;
        if (is_array($rules)) {
            foreach ($rules as $split => $rule) {
                foreach ($rule as $field => $condition_c) {
                    $operator = $condition_c[0];//运算符
                    $condition_val = $condition_c[1];//比较值
                    switch ($operator) {
                        case '=':
                            $res = $data[$field] == $condition_val ? true : false;
                            break;
                        case '>':
                            $res = $data[$field] > $condition_val ? true : false;
                            break;
                        case '<':
                            $res = $data[$field] < $condition_val ? true : false;
                            break;
                        case '>=':
                            $res = $data[$field] >= $condition_val ? true : false;
                            break;
                        case '=<':
                            $res = $data[$field] <= $condition_val ? true : false;
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        return $res;
    }

}
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

use app\common\builder\Builder;
use cmf\controller\AdminBaseController;

class DemoController extends AdminBaseController
{
    public function form(){

        $tab_list = [
            'index'    =>['title'=>'演示Tab1','href'=>url('tab1')],
            'Demo' =>['title'=>'演示Tab2','href'=>url('tab2')],
            'tab3'  =>['title'=>'演示Tab3','href'=>url('tab3')]
        ];

        //checkbox的两种数据源
        $role_0 = [0=>'取消',1=>'正常',2=>'待定'];
        $role_1 =[
            1=>['title' => '超级管理员','data-id' => '管理员','data-fix' => '普通用户'],
            3=>['title' => '神马管理员','data-id' => '神马管理员','data-fix' => '神马普通用户']
        ];
        $groupItem = [
            ['name'=>'phone','title'=>'手机','type'=>'text','description'=>'手机输入框'],
            ['name'=>'url','title'=>'网站','type'=>'text','description'=>'网站输入框组'],
        ];
        $tabItem = [
            'tab1' => ['title'=>'Home','options'=>[
                ['name'=>'tabphone','title'=>'手机','type'=>'text','description'=>'手机输入框'],
                ['name'=>'taburl','title'=>'网站','type'=>'text','description'=>'网站输入框组'],
                ['name'=>'tabgroup','title'=>'tabGroup','type'=>'group','description'=>'tabGroup描述','options'=>[
                    ['name'=>'phone2','title'=>'手机2','type'=>'text','description'=>'手机输入框'],
                    ['name'=>'url2','title'=>'网站2','type'=>'text','description'=>'网站输入框组'],
                ]]
            ]],
            'tab2' => ['title'=>'Lists','options'=>[
                ['name'=>'tab2phone','title'=>'tab2手机','type'=>'text','description'=>'tab2手机输入框'],
                ['name'=>'tab2url','title'=>'tab2网站','type'=>'text','description'=>'tab2网站输入框组'],
            ]],
        ];

        $item_data = [
            'hidden' => 1,
            'text'=>'文本值',
            'checkbok'=>'1',
            'avatar'=>'demo/20190320/32beb1a75036d1984f75e914e41c92db.jpg',
            'color'=>'#0a0a0a',
            'date' => '2019-03-20',
            'daterange' => '2019-02-19—2019-03-20',
            'datetime' => '2019-03-20 19:43',
            'email' => 'test@test.com',
            'file' => 'demo/20190320/3b4640db8c74fe69455c9d09d76a011b.xlsx',
            'files' => [
                ['files'=>'模板.xlsx','files_urls'=>'demo/20190320/3b4640db8c74fe69455c9d09d76a011b.xlsx'],
                ['files'=>'3-11.xls','files_urls'=>'portal/20190320/08d78dd26e7ef867dae737915b70e699.xls'],
            ],
            'phone' => '12345678',
            'url'=>'http://',
            'icon' => 'fa fa-fonticons',
            'image'=>'demo/20190320/32beb1a75036d1984f75e914e41c92db.jpg',
            'images'=>[
                ['images_names'=>'天天.jpg','images_urls'=>'demo/20190320/8f4dfb96614401a4ee773e7d14077bc6.jpg'],
                ['images_names'=>'圆通.jpg','images_urls'=>'demo/20190320/32beb1a75036d1984f75e914e41c92db.jpg'],
                ['images_names'=>'韵达报价.jpg','images_urls'=>'demo/20190320/e61d250a3e3a3edf2c2d3eadca8ac3f8.jpg'],
            ],
            'number' => '123456',
            'radio' => 1,
            'radio2' => 2,
            'readonly'=>'只读',
            'multilayer_select' => '11',
            'select' => '2',
            'select2' => '1',
            'select_multiple' => '1,2',
            'tabphone' => '123456789',
            'taburl' => 'https://123',
            'phone2' => '测试1234',
            'url2'=>'测试http',
            'ueditor' => '<b>测试</b><h3>sfis</h3>',
            'url' => 'http://baidu.com',
            'repeater' =>[
                ['img'=>'demo/20190320/8f4dfb96614401a4ee773e7d14077bc6.jpg','url'=>'https://www.eacoophp.com','text'=>'EacooPHP快速开发框架'],
                ['img'=>'demo/20190320/32beb1a75036d1984f75e914e41c92db.jpg','url'=>'https://forum.eacoophp.com','text'=>'EacooPHP讨论社区'],
                ['img'=>'demo/20190320/e61d250a3e3a3edf2c2d3eadca8ac3f8.jpg','url'=>'https://www.eacoophp.com','text'=>'EacooPHP快速开发框架'],
            ],
        ];
        return Builder::run('Form') ->setMetaTitle('Builder演示') // 设置页面标题
        ->setTabNav($tab_list,'index')
            ->setTopHtml('<button class="btn btn-info" type="button">顶部自定义Html标签</button>')
            ->setPageTips('页面提示','warning')
            ->addFormItem('hidden', 'hidden', 'UID', '')
            ->addFormItem('text', 'text', '文本框', '文本框提示','','required')
            ->addFormItem('checkbox', 'checkbox', '多选', '多选提示',$role_1)
            ->addFormItem('avatar', 'avatar', '头像', '头像提示')
            ->addFormItem('color', 'color', '颜色', '颜色说明')
            ->addFormItem('date', 'date', '日期', '日期说明')
            ->addFormItem('daterange', 'daterange', '时间范围', '时间范围选择器组件daterange')
            ->addFormItem('datetime', 'datetime', '时间选取器', '时间选择器组件datetime')
            ->addFormItem('email', 'email', '邮箱', '邮箱字段email','','required')
            ->addFormItem('file', 'file', '单个文件', '添加单个文件file')
            ->addFormItem('files', 'files', '多个文件', '添加多个文件files')
            ->addFormItem('group', 'group', '组', '组控件',$groupItem)
            ->addFormItem('icon', 'icon', '图标选择器', 'fontawesome图标选择器')
            ->addFormItem('image', 'image', '单图片', '添加单个图片image')
            ->addFormItem('images', 'images', '多图片', '添加多个图片images')
            ->addFormItem('info', 'info', '消息', '提示信息')
            ->addFormItem('left_icon_number', 'left_icon_number', '左边图标', '左边图标输入框left_icon_number')
            ->addFormItem('left_icon_text', 'left_icon_text', '左边图标', '左边图标输入框left_icon_text')
            ->addFormItem('multilayer_select','multilayer_select','多层级下拉框','支持1至2维数组的数据源的下拉框multilayer_select',['成都','广东',['id'=>10,'title_show'=>'-广州'],['id'=>11,'title_show'=>'--天河'],'北京','浙江',['id'=>13,'title_show'=>'-金华']])
            ->addFormItem('select', 'select', '下拉框', '下拉框select',[3=>'保密',1=>'男',2=>'女'])
            ->addFormItem('select2', 'select2', 'select2下拉框', 'select2下拉框select2',[3=>'保密',1=>'男',2=>'女'])
            ->addFormItem('select_multiple', 'select_multiple', '多选下拉框', '多选下拉框select_multiple',[3=>'保密',1=>'男',2=>'女'])
            ->addFormItem('number', 'number', '数字输入', '数字输入框number','','required')
            ->addFormItem('password', 'password', '密码', '密码输入框password','','required')
            ->addFormItem('radio', 'radio', '单选', '单选框形式radio',[3=>'保密',1=>'男',2=>'女'])
            ->addFormItem('radio2', 'radio', '高级单选', '高级单选扩展 radio',['3'=>['title' => '保密', 'data-id' => 3],'1'=>['title' => '男', 'data-id' => 1],'2'=>['title' => '女', 'data-id' => 2]])
            ->addFormItem('readonly', 'readonly', '只读', '只读输入框readonly')
            ->addFormItem('right_icon_number', 'right_icon_number', '右边图标数字', '右边图标数字输入框right_icon_number')
            ->addFormItem('right_icon_text', 'right_icon_text', '右边图标', '右边图标输入框right_icon_text')
            ->addFormItem('section','section','section','section信息')
            ->addFormItem('tab', 'tab', 'tab控件', 'tab控件tab',$tabItem)
            ->addFormItem('textarea', 'textarea', '多行文本框', '多行文本框textarea')
            ->addFormItem('ueditor', 'ueditor', '百度文本编辑器', '百度文本编辑器ueditor')
            ->addFormItem('url', 'url', '网址', '网址url')
            ->addFormItem('repeater', 'repeater', '自定义数据', '根据repeater控件生成，该示例一个处理多图',[
                    'options'=>
                        [
                            'img'  =>['title'=>'图片','type'=>'image','default'=>'','placeholder'=>''],
                            'url'  =>['title'=>'链接','type'=>'url','default'=>'','placeholder'=>'http://'],
                            'text' =>['title'=>'文字','type'=>'text','default'=>'','placeholder'=>'输入文字'],
                        ]
                ]
            )
            ->setFormData($item_data)
            ->addButton('submit')
            ->addButton('back')
            ->addButton('reset')// 设置表单按钮
            ->fetch();

    }

    public function lists(){

        $tab_list = [
            'builderform'=>['title'=>'表单示例','href'=>cmf_url('Demo/AdminIndex/index')],
            'builderlist'=>['title'=>'列表示例','href'=>cmf_url('Demo/AdminIndex/lists')],
        ];

        $advancedSearch = [
            'fields' => [
                ['name'=>'status','type'=>'select','title'=>'状态','options'=>[1=>'发布',0=>'未发布']],
                ['name'=>'sex','type'=>'select','title'=>'性别','options'=>[0=>'保密',1=>'男',2=>'女']],
                ['name'=>'create_time_range','type'=>'daterange','extra_attr'=>'placeholder="创建时间范围"'],
                ['name'=>'keyword','type'=>'text','extra_attr'=>'placeholder="请输入查询关键字"'],
            ],
            'title' => '查询',
            'url' => cmf_url('Demo/AdminIndex/lists'),

        ];

        $param = $this->request->param();
        $where = [];
        if(isset($param['status'])){
            $where['post_status'] = $param['status'];
            $advancedSearch['fields'][0]['value']=$param['status'];
        }
        if(isset($param['create_time_range'])){
            $timegap = input('create_time_range');
            if($timegap){
                $gap = explode('—', $timegap);
                $where['start_time'] = $gap[0];
                $where['end_time'] = $gap[1];

            }
            $advancedSearch['fields'][2]['value']=$timegap;
        }
        if(isset($param['keyword'])){
            $where['keyword'] = $param['keyword'];
            $advancedSearch['fields'][3]['value']=$param['keyword'];
        }
        $postService = new PostService();
        $data = $postService->adminPostList($where);

        return Builder::run('List')
            ->setMetaTitle('Builder演示') // 设置页面标题
            ->setPageTips('页面提示','warning')
            ->setTopHtml('<button class="btn btn-info" type="button">顶部自定义Html标签</button>')
            ->setTabNav($tab_list, 'builderlist')  // 设置页面Tab导航
            ->addAdvanceSearch($advancedSearch)  // 添加高级搜索
            //->setRightButton(2)
            ->setExtraHtml('<button class="btn btn-info" type="button">底部自定义Html标签</button>')
            ->addTopButton('addnew')  // 添加新增按钮
            ->addTopButton('resume',['model'=>'DemoExample'])  // 添加启用按钮
            ->addTopButton('forbid',['model'=>'DemoExample'])  // 添加禁用按钮
            ->addTopButton('delete',['model'=>'DemoExample'])  // 添加删除按钮
            ->keyListItem('id', 'ID')
            ->keyListItem('thumbnail', '缩略图', 'image')
            ->keyListItem('post_title', '标题')
            ->keyListItem('post_keywords', '标签')
            ->keyListItem('comment_status','评论状态','bool')
            ->keyListItem('post_status', '状态','status')
            ->keyListItem('create_time', '创建时间')
            ->keyListItem('right_button', '操作', 'btn')
            ->setListPrimaryKey('id')//设置数据主键，默认是id
            ->setListData($data->items())    // 数据列表
            ->setListPage($data->render()) // 数据列表分页
            ->addRightButton('edit',['href'=>cmf_url('portal/AdminArticle/edit',['id'=>'__data_id__'])])
            ->addRightButton('forbid',['model'=>'PotalPost','field'=>'post_status'])
            ->addRightButton('hide',['model'=>'PotalPost','field'=>'is_top'])
            ->addRightButton('recycle',['model'=>'PotalPost'])
            ->addRightButton('restore',['model'=>'PotalPost'])
            ->addRightButton('delete',['model'=>'PotalPost'])
            ->addRightButton('self',['layer'=>['width'=>'80%','height'=>'80%'],'title'=>'测试','href'=>cmf_url('portal/AdminArticle/edit',['id'=>'__data_id__'])])
            ->fetch();
    }

    public function setstatus(){
        $data = $this->request->param();
        $this->success('操作成功');
    }
}
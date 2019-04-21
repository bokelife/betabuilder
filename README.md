# BetaBuilder 
表单自动化生成项 需要引用的方法
自动化生成form表单

##  更新日志

### 1.0.1
* 基本完成基础功能

### 1.0.2
* 修复bug

### 1.0.3
* 修正info控件显示逻辑

### 1.0.4
* 修复repeater控件当同一行存在多个图片时id定位错误的bug

### 1.0.5
* 修复listbuilder底部额外功能代码显示的bug

### 1.0.6 
* 修复listbuilder添加按钮时网址的生成逻辑
* 修复listbuilder编译右侧按钮时禁用与隐藏按钮的逻辑bug
* 修复listbuilder编译顶部按钮时链接事件错误的bug
* 更改listbuilder顶部排序按钮的执行方式为post
* listbuilder列表项增加text、number二种展示方式

### 1.0.7
* listbuilder 右边操作按钮添加查看按钮功能
* 优化listbuilder 右边按钮是显示和提示功能
* 修复listbuilder 右边forbid、hide 按钮一处错误
* formbuilder 增加static类型控件，用于显示静态文本内容
* formbuilder 增加img控件，用于显示图片
* formbuilder 增加form_inline控件，用于显示多个并排控件
* 修复formbuilder 中标题和描述的label的for对应id错误

### 1.0.8
* formbuilder:文件上传增加自定义文件类型指定
    * addFormItem 中的$options 包含'filetype'可指定类型
    * 支持的文件类型包括：image,video,audio,file，类型根据系统后台设置限定
* 修复formbuilder中生成id的逻辑导致页面部分js出错
* formbuilder增加editor控件，用于加载编辑器插件，对应钩子为editor
* 修复formbuilder img控件打开大图的链接错误
* formbuilder 增加audio_play控件，用于显示HTML5音频播放
* formbuilder 增加video_play控件，用于显示HTML5视频
* 修复formbuilder group控件获取默认值出错的bug
* 修复listbuilder 高级查询网址生成错误

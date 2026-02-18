![release](https://img.shields.io/github/v/release/usualtool/framework?include_prereleases&style=social) 
![license](https://img.shields.io/github/license/usualtool/ut-api?style=social) 
![size](https://img.shields.io/github/languages/code-size/usualtool/framework?style=social) 
### English | [简体中文](http://frame.usualtool.com/baike)
#### Introduction
UsualTool Framework (UT) is based on PHP multi-end development framework, class library perfect, suitable for the development of various types of applications.
#### Schematic diagram
Difference from traditional MVC  
![schematic](http://frame.usualtool.com/image/utyl-en.jpg) 
#### Environment
Support Nginx/Apache/IIS.  
Support PHP5/PHP7/PHP8 and other known upward distributions.
#### Security
.ut.config configuration contains sensitive information.   
You must set in the configuration file to prohibit non local access Config file.  
[Server configuration example](http://frame.usualtool.com/baike/config.php)
install-dev is the installation directory of visual package on the development side. If visualization is not required, please delete this directory after deploying UT.
#### system architecture
```
┌─── 📁app 应用开发目录 core
├────├─── 📁lang 语言包 Language package
├────├─── 📁modules 模块 Module
├────├────└───ut-frame 公共默认模块 Common module
├────├────├────├─admin 后端模型 Admin model
├────├────├────├─cache 缓存编译 Cache
├────├────├────├─skin 视图 View
├────├────├────├────├─admin 后端视图 Admin view
├────├────├────├────└─front 前端视图 Client view
├────├────├────├─front 前端模型 Client model
├────├────├────├────├─error.php 错误页 Error page
├────├────├────├────└─index.php 前端示例文件 Client example file
├────├─────────└─usualtool.config 配置引导 Configure
├────├─── 📁plugins 插件 Plugin
├────├────└───插件名称 Plugin Name
├────├─────────├─assets 静态资源 Static Resources
├────├─────────├─plugin.php 插件类
├────├─────────└─usualtool.config 配置引导 Configure
├────├─── 📁task 计划任务 Crontab
├────└─── 📁template 模板工程 Formwork
├─────────└───模板名称 Template Name
├──────────────├─assets 静态资源 Static Resources
├──────────────├─move 自动覆盖的文件 Files overwritten during installation
├──────────────├─skin 视图 View
├──────────────├───├─ut-frame 公共模块模型视图 Common module model-view
├──────────────├───├────├─admin 后端视图 Admin view
├──────────────├───├────├─cache 缓存 Cache
├──────────────├───├────└─front 前端视图 Client view
├──────────────├───└─其他模块模型视图 Other module model-view
├──────────────└─usualtool.config 配置引导 Configure
├─── 📁library 类库 Class library
├─── 📁log 框架日志 Log
├─── 📁open 应用根目录（开放访问）
├────├─── 📁assets 资源 Resource
├────├─── index.php 前端控制器 Client Controller file
├────├─── plugin.php 插件控制器
├────└─── config.php 应用配置 Application configure
├─── 📁update 更新包目录 Update
├─── 📁vendor 依赖库目录
├─── .ut.config 全局配置 Global configuration
├─── autoload.php 自动加载 Automatic file loading
├─── usualtool 命令行服务端 Command line
└─── .version.ini 版本号 Version
```
#### [Development documentation](http://frame.usualtool.com/baike)
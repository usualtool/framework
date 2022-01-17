# UT框架 UT Frame

#### 介绍 Introduction
UT框架是基于PHP的多端开发框架，类库完善，适合开发各种类型的应用。
UT Framework is based on PHP multi-end development framework, class library perfect, suitable for the development of various types of applications.

#### 环境 Environment
支持PHP5/PHP7/PHP8等已知向上发行的版本
Support PHP 5/PHP7/PHP8 and other known upward distributions

#### 系统结构 System structure

```
┌─--app    应用目录 Application Directory
│    ├─--assets  默认资源组 Default Resource Group
│    │    ├─css    
│    │    ├─font  
│    │    ├─images 
│    │    └─js   
│    ├─--modules 模块 Module
│    │    ├─--UTFrame  UT公共模块 UT Common Module
│    │    │    ├─admin 后端文件夹 Back end folder
│    │    │    ├─cache 缓存文件夹 Cache folder
│    │    │    ├─skin  模板文件夹 Template folder
│    │    │    └─front 前端文件夹 Front end folder
│    │    │        ├─error.php   错误页提示 Error page tip
│    │    │        ├─index.php   前端首页示例文件 Front-end home example file
│    │    │        └─sockets.php Websockets命令执行文件 Websockets command execution file
│    │    └─--开发者其他自定义模块 Other custom modules for developers
│    │         ├─admin 后端文件夹 Back end folder
│    │         ├─cache 缓存文件夹 Cache folder
│    │         ├─skin  模板文件夹 Template folder
│    │         ├─front 前端文件夹 Front end folder
│    │         └─usualtool.config 模块配置文件 Module configuration file
│    ├─--plugins 插件目录 Plugin directory
│    └─--config.php 应用配置文件 Application profile
├─--lang    语言包目录 Language Package Directory
├─--library UT类库目录 UT Class Library directory
├─--update  UT预留更新目录 UT Reserve Update Directory
├─--vendor  依赖库目录 Dependent Library Directory
├─--.ut.config    UT全局配置 UT Global configuration
├─--autoload.php  自动加载文件 Automatic file loading
├─--index.php     UT入口控制器文件 UT Entry Controller file
└─--UTVER.ini     UT版本号 UT version number
```
#### 相关开发文档 Development documentation
http://frame.usualtool.com
https://www.usualtool.com/UT/
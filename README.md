![release](https://img.shields.io/github/v/release/usualtool/framework?include_prereleases&style=social) 
![license](https://img.shields.io/github/license/usualtool/ut-api?style=social) 
![size](https://img.shields.io/github/languages/code-size/usualtool/framework?style=social) 
### English | [简体中文](http://frame.usualtool.com/baike)
#### Introduction
UsualTool Framework (UT) is a PHP-based multi-end development framework with a comprehensive class library, suitable for building various types of applications.
#### Schematic diagram
Difference from traditional MVC  
![schematic](http://frame.usualtool.com/image/utyl-en.jpg) 
#### Environment
Support Nginx/Apache/IIS.  
Support PHP5/PHP7/PHP8 and other known upward distributions.
#### Security
.ut.config configuration contains sensitive information.   
You must set in the configuration file to prohibit non-local access Config file.  
install-dev is the installation directory of visual package on the development side. If visualization is not required, please delete this directory after deploying UT.
#### system architecture
```
┌─── 📁app 应用开发目录 core
├────├─── 📁lang 语言包 Language package
├────├─── 📁modules 模块 Module
├────├────└───ut-frame 公共模块
├────├─────────├─assets 临时资源包
├────├────├────├─admin 后端控制 Admin control
├────├────├────├─cache 缓存编译 Cache
├────├────├────├─skin 视图 View
├────├────├────├────├─admin 后端视图 Admin view
├────├────├────├────└─front 前端视图 Client view
├────├────├────├─front 前端控制 Client control
├────├────├────├────├─error.php
├────├────├────├────└─index.php 示例文件
├────├────├────├─model 模型 Model
├────├─────────└─usualtool.config 模块配置 Configure
├────├─── 📁plugins 插件 Plugin
├────├────└───插件名称
├────├─────────├─assets 临时资源包
├────├─────────├─plugin.php 插件模型
├────├─────────└─usualtool.config 插件配置 Configure
├────├─── 📁share 共享命名空间 Namespace
├────├─── 📁task 计划任务 Crontab
├────└─── 📁template 模板工程 Formwork
├─────────└───模板名称
├──────────────├─assets 临时资源包
├──────────────├─move 覆盖控制&模型
├──────────────├─skin 视图 View
├──────────────├───├─ut-frame 公共模块视图
├──────────────├───├────├─admin 后端视图 Admin view
├──────────────├───├────├─cache 缓存 Cache
├──────────────├───├────└─front 前端视图 Client view
├──────────────├───└─其他模块视图
├──────────────└─usualtool.config 模板配置 Configure
├─── 📁library 类库 Class library
├─── 📁log 框架日志 Log
├─── 📁open 应用根目录（开放访问）
├────├─── 📁assets 静态资源 Resource
├────├─── index.php 路由控制器
├────├─── plugin.php 插件控制器
├────└─── config.php 应用配置
├─── 📁update 更新包目录 Update
├─── 📁vendor 依赖库目录
├─── .ut.config 全局配置 Global configuration
├─── .version.ini 框架版本 Version
├─── autoload.php 自动加载 Automatic file loading
└─── usualtool 命令行服务端 Command line
```
#### [Development documentation](http://frame.usualtool.com/baike)
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
┌─── app /*Application*/
├────├─── assets /*Resource*/
├────├─── admin /*Admin example*/
├────├────└───index.php  /*Admin controller*/
├────├─── log /*Log*/
├────├─── modules /*Module*/
├────├────└───ut-frame
├────├────├────├─admin /*Admin model*/
├────├────├────├─cache
├────├────├────├─skin
├────├────├────├────├─admin /*Admin view*/
├────├────├────├────└─front /*Client view*/
├────├────├────├─front /*Client model*/
├────├────├────├────├─error.php
├────├────├────├────└─index.php
├────├─────────└─usualtool.config
├────├─── plugins /*Plugin*/
├────├─── template /*Template engineering*/
├────├────└───Template name
├────├─────────├─assets
├────├─────────├─move
├────├─────────├─skin /*Module view*/
├────├─────────├───├─ut-frame
├────├─────────├───├────├─admin
├────├─────────├───├────├─cache
├────├─────────├───├────└─front
├────├─────────├───└─Other module view
├────├─────────└─usualtool.config
├────└─── config.php /*Application configuration*/
├─── lang /*Language package*/
├─── library /*Class library*/
├─── update
├─── vendor /*Composer dependency*/
├─── .ut.config /*Global configuration*/
├─── autoload.php /*Bootloader*/
├─── index.php /*Client controller*/
├─── usualtool /*Command line*/
└─── UTVER.ini /*Version*/
```
#### [Development documentation](http://frame.usualtool.com/baike)
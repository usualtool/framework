![release](https://img.shields.io/github/v/release/usualtool/framework?include_prereleases&style=social) 
![license](https://img.shields.io/github/license/usualtool/ut-api?style=social) 
![size](https://img.shields.io/github/languages/code-size/usualtool/framework?style=social) 
### English | [简体中文](http://frame.usualtool.com/baike)
#### Introduction
UsualTool Framework (UT) is based on PHP multi-end development framework, comprehensive class library, suitable for the development of various types of applications.
#### Schematic diagram
Difference from traditional MVC  
![schematic](http://frame.usualtool.com/image/utyl-en.jpg) 
#### Environment
Support Nginx/Apache/IIS.  
Support PHP5/PHP7/PHP8 and other known upward distributions.
#### Security
.ut.config configuration contains sensitive information.   
You must set in the configuration file to prohibit non-local access Config file.  
[Server configuration example](http://frame.usualtool.com/baike/config.php)
install-dev is the installation directory of visual package on the development side. If visualization is not required, please delete this directory after deploying UT.
#### system architecture
```
┌─── 📁app /*Application running*/
╎    ╎
╎    ├─── 📁assets /*Resource*/
╎    ╎
╎    ├─── 📁admin /*Admin example*/
╎    ╎
╎    ├─── 📁modules /*Module*/
╎    ╎
╎    ├─── 📁plugins /*Plugin*/
╎    ╎
╎    ├─── 📁template /*Template engineering*/
╎    ╎
╎    ├─── 📄index.php /*Client controller*/
╎    ╎
╎    └─── 📄config.php /*Application configuration*/
╎
├─── 📁lang /*Language package*/
╎
├─── 📁library /*Class library*/
╎
├────📁log /*Framework log*/
╎
├─── 📁update /*Update temporary directory*/
╎
├─── 📁vendor /*Composer dependency*/
╎
├─── .ut.config /*Global configuration*/
╎
├─── 📄autoload.php /*Bootloader*/
╎
├─── usualtool /*Command line*/
╎
└─── UTVer.ini /*Version*/
```
#### [Development documentation](http://frame.usualtool.com/baike)
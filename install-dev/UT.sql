DROP TABLE IF EXISTS `cms_admin`;
CREATE TABLE `cms_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roleid` int(11) NOT NULL DEFAULT '1',
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `salts` varchar(20) NOT NULL,
  `avatar` varchar(250) DEFAULT NULL,
  `addtime` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `cms_admin_log`;
CREATE TABLE `cms_admin_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `logintime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `cms_admin_role`;
CREATE TABLE `cms_admin_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(50) NOT NULL,
  `module` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `cms_module`;
CREATE TABLE `cms_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bid` int(11) DEFAULT '0',
  `mid` varchar(50) DEFAULT NULL,
  `modname` varchar(100) DEFAULT NULL,
  `modurl` varchar(200) DEFAULT NULL,
  `isopen` int(11) DEFAULT '0',
  `ordernum` int(11) DEFAULT '0',
  `look` int(11) DEFAULT '0',
  `othername` varchar(100) DEFAULT NULL,
  `befoitem` varchar(250) DEFAULT NULL,
  `backitem` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `cms_plugin`;
CREATE TABLE `cms_plugin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` varchar(50) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `auther` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `ver` varchar(10) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `cms_search`;
CREATE TABLE `cms_search` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hit` int(11) DEFAULT '1',
  `keyword` varchar(100) DEFAULT NULL,
  `lang` varchar(20) DEFAULT 'zh',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `cms_search_set`;
CREATE TABLE `cms_search_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dbs` varchar(50) DEFAULT NULL,
  `fields` varchar(100) DEFAULT NULL,
  `wheres` varchar(200) DEFAULT NULL,
  `pages` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `cms_template`;
CREATE TABLE `cms_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` varchar(50) DEFAULT NULL,
  `lang` varchar(20) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `makefront` int(11) DEFAULT '0',
  `makeadmin` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `cms_update`;
CREATE TABLE `cms_update` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `updateid` int(11) DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
INSERT INTO `cms_admin` VALUES (1, 1, 'admin', 'd9c51907cc016f4ad6164423c3ddd04f025aa037', 'qHCgJ', '/app/assets/images/noimage.png', '2021-08-08 00:00:00');
INSERT INTO `cms_admin_role` VALUES (1, '超级管理', 'ut-frame,ut-module,ut-plugin,ut-template,ut-cac,ut-system,ut-data,ut-api,ut-power');
INSERT INTO `cms_module` VALUES (1, 3, 'ut-frame', 'UT公共模块', 'index.php', 1, 91, 0, '', NULL, NULL);
INSERT INTO `cms_module` VALUES (2, 3, 'ut-module', '模块', 'index.php', 1, 92, 0, '', '', '模块市场:module.php,管理模块:index.php');
INSERT INTO `cms_module` VALUES (3, 3, 'ut-plugin', '插件', 'index.php', 1, 93, 0, '', '', '插件市场:plugin.php,管理插件:index.php');
INSERT INTO `cms_module` VALUES (4, 3, 'ut-template', '模板', 'index.php', 1, 94, 0, '', '', '模板市场:template.php,创建模板:template_creat.php,管理模板:index.php');
INSERT INTO `cms_module` VALUES (5, 3, 'ut-cac', 'CAC', 'index.php', 1, 95, 0, '', '', '');
INSERT INTO `cms_module` VALUES (6, 3, 'ut-system', '配置', 'index.php', 1, 96, 1, '', '', '系统:index.php,语言:lang.php,搜索:search.php');
INSERT INTO `cms_module` VALUES (7, 3, 'ut-data', '数据', 'index.php', 1, 97, 1, '', '', '表管理:index.php,SQL查询:sql.php,备份还原:backup.php');
INSERT INTO `cms_module` VALUES (8, 3, 'ut-api', '接口', 'index.php', 1, 98, 1, '', '', '接口列表:index.php,在线调试:test.php');
INSERT INTO `cms_module` VALUES (9, 3, 'ut-power', '权限', 'admin.php', 1, 99, 1, '', '', '角色:role.php,管理员:admin.php,登陆日志:log.php');
/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : trainer

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-06-15 11:58:51
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `sp_access`
-- ----------------------------
DROP TABLE IF EXISTS `sp_access`;
CREATE TABLE `sp_access` (
  `role_id` smallint(6) unsigned NOT NULL,
  `g` varchar(20) NOT NULL COMMENT '项目',
  `m` varchar(20) NOT NULL COMMENT '模块',
  `a` varchar(20) NOT NULL COMMENT '方法',
  KEY `groupId` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_access
-- ----------------------------

-- ----------------------------
-- Table structure for `sp_activity`
-- ----------------------------
DROP TABLE IF EXISTS `sp_activity`;
CREATE TABLE `sp_activity` (
  `activity_id` int(10) NOT NULL AUTO_INCREMENT,
  `activity_name` varchar(50) NOT NULL COMMENT '小课活动名称',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '活动类型 1新客户 2老客户',
  `price` float(4,2) NOT NULL COMMENT '价格 元/人',
  `imgurl` varchar(150) NOT NULL COMMENT '封面图像',
  `place` varchar(50) NOT NULL COMMENT '地点',
  `spec` text NOT NULL COMMENT '特殊说明 每一句以|作为结束',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `start_time` int(10) NOT NULL COMMENT '开始时间',
  `end_time` int(10) NOT NULL COMMENT '结束时间',
  `num` int(2) NOT NULL COMMENT '报名人数',
  PRIMARY KEY (`activity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='活动小课表';

-- ----------------------------
-- Records of sp_activity
-- ----------------------------
INSERT INTO `sp_activity` VALUES ('2', '小型活动2', '1', '16.00', '/trainer/static/data/upload/20160524/574452c3c8709.jpg', '火星', '1、训练时间为1小时，带上球拍、穿上运动衣运动鞋即可来上课|\r\n2、报满4人开课，免费提供训练场地|\r\n3、学员需提前10分钟到达场地，进行热身的准备活动|\r\n4、时间不可因个人原因临时更改，如遇特殊原因时间顺延|\r\n5、本课程原价50元/人，现体验价18元/人（限新用户，老用户不可下单），每人只能体验1次，不可重复下单|\r\n6、一经报名支付，因个人原因不能上课的学员，费用将不予退还|', '1464094346', '1464221400', '1464232200', '2');
INSERT INTO `sp_activity` VALUES ('3', '老客户活动', '2', '20.00', '/trainer/static/data/upload/20160524/5744530e34cc0.jpg', '本地', '哈哈哈|哈哈哈哈|坑爹|', '1464095505', '1464221400', '1464239400', '3');
INSERT INTO `sp_activity` VALUES ('4', '新活动了', '1', '44.00', '/trainer/static/data/upload/20160524/5744601b0b505.jpg', '河南郑州', '规则1|规则2|规则3', '1464098899', '1464156360', '1466092800', '4');

-- ----------------------------
-- Table structure for `sp_act_order`
-- ----------------------------
DROP TABLE IF EXISTS `sp_act_order`;
CREATE TABLE `sp_act_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `orderno` varchar(50) NOT NULL COMMENT '订单号',
  `activity_id` int(10) NOT NULL COMMENT '活动id',
  `total_fee` float(10,2) NOT NULL COMMENT '总价',
  `add_time` int(10) NOT NULL COMMENT '下单时间',
  `pay_time` int(10) NOT NULL COMMENT '支付时间',
  `status` tinyint(1) NOT NULL COMMENT '0未支付 1已支付 -1 已删除',
  `member_id` int(10) NOT NULL COMMENT '用户id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='活动订单表';

-- ----------------------------
-- Records of sp_act_order
-- ----------------------------
INSERT INTO `sp_act_order` VALUES ('1', '14641811804315', '4', '44.00', '1464181180', '0', '1', '4');
INSERT INTO `sp_act_order` VALUES ('3', '14641854182958', '2', '16.00', '1464185418', '0', '1', '4');
INSERT INTO `sp_act_order` VALUES ('4', '14641859303392', '2', '16.00', '1464185930', '0', '0', '4');

-- ----------------------------
-- Table structure for `sp_ad`
-- ----------------------------
DROP TABLE IF EXISTS `sp_ad`;
CREATE TABLE `sp_ad` (
  `ad_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ad_name` varchar(255) NOT NULL,
  `ad_content` text,
  `status` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ad_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_ad
-- ----------------------------

-- ----------------------------
-- Table structure for `sp_admin_panel`
-- ----------------------------
DROP TABLE IF EXISTS `sp_admin_panel`;
CREATE TABLE `sp_admin_panel` (
  `menuid` mediumint(8) unsigned NOT NULL,
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `name` char(32) DEFAULT NULL,
  `url` char(255) DEFAULT NULL,
  `datetime` int(10) unsigned DEFAULT '0',
  UNIQUE KEY `userid` (`menuid`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_admin_panel
-- ----------------------------

-- ----------------------------
-- Table structure for `sp_area`
-- ----------------------------
DROP TABLE IF EXISTS `sp_area`;
CREATE TABLE `sp_area` (
  `area_id` int(10) NOT NULL AUTO_INCREMENT,
  `area_name` varchar(20) NOT NULL COMMENT '区域名称',
  `mc_id` int(10) NOT NULL COMMENT '所属城市id',
  PRIMARY KEY (`area_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='城市区域表';

-- ----------------------------
-- Records of sp_area
-- ----------------------------
INSERT INTO `sp_area` VALUES ('1', '海淀区', '1');
INSERT INTO `sp_area` VALUES ('2', '朝阳区', '1');
INSERT INTO `sp_area` VALUES ('3', '上海区1', '7');
INSERT INTO `sp_area` VALUES ('4', '上海区2', '7');
INSERT INTO `sp_area` VALUES ('5', '上海区3', '7');

-- ----------------------------
-- Table structure for `sp_asset`
-- ----------------------------
DROP TABLE IF EXISTS `sp_asset`;
CREATE TABLE `sp_asset` (
  `aid` bigint(20) NOT NULL AUTO_INCREMENT,
  `_unique` varchar(14) NOT NULL,
  `filename` varchar(50) DEFAULT NULL,
  `filesize` int(11) DEFAULT NULL,
  `filepath` varchar(200) NOT NULL,
  `uploadtime` int(11) NOT NULL,
  `status` int(2) NOT NULL DEFAULT '1',
  `meta` text,
  `suffix` varchar(50) DEFAULT NULL,
  `download_times` int(6) NOT NULL,
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_asset
-- ----------------------------

-- ----------------------------
-- Table structure for `sp_comment`
-- ----------------------------
DROP TABLE IF EXISTS `sp_comment`;
CREATE TABLE `sp_comment` (
  `comment_id` int(10) NOT NULL AUTO_INCREMENT,
  `trainer_id` int(10) NOT NULL COMMENT '教练id',
  `content` varchar(255) NOT NULL COMMENT '评论内容',
  `member_id` int(10) NOT NULL COMMENT '用户id',
  `star` tinyint(1) NOT NULL DEFAULT '0' COMMENT '评价的星级别从1到5个级别',
  `time` int(10) NOT NULL COMMENT '评论时间',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '评论类别 1用户对教练评论 2教练对用户评论',
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='用户评论表';

-- ----------------------------
-- Records of sp_comment
-- ----------------------------
INSERT INTO `sp_comment` VALUES ('10', '1', '好评方法', '3', '5', '1461593114', '1');
INSERT INTO `sp_comment` VALUES ('11', '1', '112', '3', '0', '2147483647', '2');
INSERT INTO `sp_comment` VALUES ('12', '1', '113', '3', '0', '2147483647', '2');
INSERT INTO `sp_comment` VALUES ('13', '1', '114', '3', '0', '2147483647', '2');
INSERT INTO `sp_comment` VALUES ('14', '1', '115', '3', '0', '2147483647', '2');
INSERT INTO `sp_comment` VALUES ('15', '1', '116', '3', '0', '2147483647', '2');
INSERT INTO `sp_comment` VALUES ('16', '1', '117', '3', '0', '2147483647', '2');
INSERT INTO `sp_comment` VALUES ('17', '1', '118', '3', '0', '2147483647', '2');
INSERT INTO `sp_comment` VALUES ('18', '1', '119', '3', '0', '2147483647', '2');
INSERT INTO `sp_comment` VALUES ('19', '1', '1110', '3', '0', '2147483647', '2');
INSERT INTO `sp_comment` VALUES ('20', '1', '1110', '3', '0', '2147483647', '2');
INSERT INTO `sp_comment` VALUES ('21', '1', '1112', '3', '0', '2147483647', '2');
INSERT INTO `sp_comment` VALUES ('22', '1', '1113', '3', '0', '2147483647', '2');
INSERT INTO `sp_comment` VALUES ('23', '1', '1114', '3', '0', '2147483647', '2');
INSERT INTO `sp_comment` VALUES ('24', '1', '对用户评论', '3', '0', '1462452760', '2');

-- ----------------------------
-- Table structure for `sp_commentmeta`
-- ----------------------------
DROP TABLE IF EXISTS `sp_commentmeta`;
CREATE TABLE `sp_commentmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `comment_id` (`comment_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_commentmeta
-- ----------------------------

-- ----------------------------
-- Table structure for `sp_company`
-- ----------------------------
DROP TABLE IF EXISTS `sp_company`;
CREATE TABLE `sp_company` (
  `company_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '公司名称',
  `city` varchar(20) NOT NULL COMMENT '城市',
  `address` varchar(100) NOT NULL COMMENT '公司地址',
  `contacts` varchar(10) NOT NULL COMMENT '联系人',
  `phone` varchar(20) NOT NULL COMMENT '联系电话',
  `type` tinyint(1) NOT NULL COMMENT '服务类型 1陪练 2比赛指导 3两者皆有',
  `create_time` int(10) NOT NULL COMMENT '添加时间',
  `remark` text COMMENT '备注',
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='企业服务表';

-- ----------------------------
-- Records of sp_company
-- ----------------------------
INSERT INTO `sp_company` VALUES ('1', '意向', '地球', '中国', '胡', '15136175246', '2', '1461847736', '');
INSERT INTO `sp_company` VALUES ('2', '意向', '地球', '中国', '胡', '15136175246', '2', '1461848378', '');
INSERT INTO `sp_company` VALUES ('3', '意向', '地球', '中国', '胡', '15136175246', '2', '1461848380', '');
INSERT INTO `sp_company` VALUES ('4', '公司名称1', '郑州', '交通路', '李', '15136175246', '1', '1461848466', '');
INSERT INTO `sp_company` VALUES ('8', '555', '555', '555', '555', '15136175246', '1', '1461849049', '555555');
INSERT INTO `sp_company` VALUES ('9', '555', '555', '555', '555', '15136175246', '1', '1461849052', '555555');
INSERT INTO `sp_company` VALUES ('10', '555', '555', '555', '555', '15136175246', '1', '1461849054', '555555');
INSERT INTO `sp_company` VALUES ('11', '555', '555', '555', '555', '15136175246', '1', '1461849056', '555555');
INSERT INTO `sp_company` VALUES ('12', '555', '555', '555', '555', '15136175246', '1', '1461849058', '555555');
INSERT INTO `sp_company` VALUES ('13', '555', '555', '555', '555', '15136175246', '1', '1461849060', '555555');

-- ----------------------------
-- Table structure for `sp_coupon`
-- ----------------------------
DROP TABLE IF EXISTS `sp_coupon`;
CREATE TABLE `sp_coupon` (
  `coupon_id` int(10) NOT NULL AUTO_INCREMENT,
  `coupon_name` varchar(50) NOT NULL COMMENT '优惠券名称',
  `price` float(10,0) DEFAULT '0' COMMENT '优惠券面值',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '优惠券类型 0永久 1不永久',
  `end_time` int(10) DEFAULT NULL COMMENT '优惠券有效期截止日期',
  PRIMARY KEY (`coupon_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='优惠券表';

-- ----------------------------
-- Records of sp_coupon
-- ----------------------------
INSERT INTO `sp_coupon` VALUES ('1', '20元优惠券', '20', '0', null);
INSERT INTO `sp_coupon` VALUES ('2', '15元优惠券', '15', '1', '1463673600');
INSERT INTO `sp_coupon` VALUES ('3', '50元优惠券', '50', '1', '1515686400');
INSERT INTO `sp_coupon` VALUES ('4', '66', '66', '1', '1461931140');

-- ----------------------------
-- Table structure for `sp_feedback`
-- ----------------------------
DROP TABLE IF EXISTS `sp_feedback`;
CREATE TABLE `sp_feedback` (
  `feedback_id` int(10) NOT NULL AUTO_INCREMENT,
  `member_id` int(10) NOT NULL COMMENT '用户id',
  `content` text NOT NULL COMMENT '反馈内容',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`feedback_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='用户反馈表';

-- ----------------------------
-- Records of sp_feedback
-- ----------------------------
INSERT INTO `sp_feedback` VALUES ('3', '3', '444', '1461592134');
INSERT INTO `sp_feedback` VALUES ('4', '3', '444', '1461592149');
INSERT INTO `sp_feedback` VALUES ('5', '3', '444', '1461592177');
INSERT INTO `sp_feedback` VALUES ('6', '3', '444', '1461592235');
INSERT INTO `sp_feedback` VALUES ('7', '3', '444', '1461592252');

-- ----------------------------
-- Table structure for `sp_links`
-- ----------------------------
DROP TABLE IF EXISTS `sp_links`;
CREATE TABLE `sp_links` (
  `link_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `link_url` varchar(255) NOT NULL DEFAULT '',
  `link_name` varchar(255) NOT NULL DEFAULT '',
  `link_image` varchar(255) DEFAULT '',
  `link_target` varchar(25) NOT NULL DEFAULT '_blank',
  `link_description` text NOT NULL,
  `link_status` int(2) NOT NULL DEFAULT '1',
  `link_rating` int(11) NOT NULL DEFAULT '0',
  `link_rel` varchar(255) DEFAULT '',
  `listorder` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`link_id`),
  KEY `link_visible` (`link_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_links
-- ----------------------------

-- ----------------------------
-- Table structure for `sp_members`
-- ----------------------------
DROP TABLE IF EXISTS `sp_members`;
CREATE TABLE `sp_members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login_name` varchar(25) DEFAULT NULL,
  `user_pass` varchar(64) DEFAULT '',
  `user_nickname` varchar(50) DEFAULT NULL COMMENT '用户昵称',
  `user_pic_assetid` int(8) DEFAULT NULL,
  `sign_words` varchar(200) DEFAULT NULL,
  `user_email` varchar(100) DEFAULT '',
  `last_login_ip` varchar(16) NOT NULL,
  `last_login_time` int(12) NOT NULL,
  `create_time` int(12) NOT NULL,
  `user_activation_key` varchar(60) DEFAULT '',
  `user_status` int(11) NOT NULL DEFAULT '0' COMMENT '0未登录 1登陆',
  `mobile` varchar(11) NOT NULL DEFAULT '0' COMMENT '手机号',
  `sex` tinyint(1) NOT NULL DEFAULT '1' COMMENT '性别 1男 2女',
  `wx_avatar` varchar(100) DEFAULT NULL COMMENT '用户微信头像',
  `my_avatar` varchar(50) DEFAULT NULL COMMENT '自己设置的头像',
  `points` int(8) NOT NULL DEFAULT '0' COMMENT '我的积分',
  `coupon_ids` varchar(50) NOT NULL COMMENT '优惠券id组合 逗号隔开',
  `preurl` text COMMENT '用户访问的url',
  PRIMARY KEY (`id`),
  KEY `user_nicename` (`user_nickname`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of sp_members
-- ----------------------------
INSERT INTO `sp_members` VALUES ('3', null, '', 'hupeng222', null, null, '', '127.0.0.1', '1462453005', '1461336441', '', '0', '15136175556', '2', null, './static/weixin/avatar/1461468733.jpg', '0', '1,2', 'http://localhost/trainer/index.php?m=Weixin&amp;c=Trainer&amp;a=detail&amp;id=1');
INSERT INTO `sp_members` VALUES ('4', null, '', '新注册用户', null, null, '', '0.0.0.0', '1465906238', '1461759417', '', '0', '15136175246', '1', null, null, '0', '1', null);
INSERT INTO `sp_members` VALUES ('5', null, '', '新注册用户', null, null, '', '127.0.0.1', '1462452942', '1462452942', '', '0', '15136175245', '1', null, null, '0', '1', null);

-- ----------------------------
-- Table structure for `sp_member_score`
-- ----------------------------
DROP TABLE IF EXISTS `sp_member_score`;
CREATE TABLE `sp_member_score` (
  `score_id` int(10) NOT NULL AUTO_INCREMENT,
  `member_id` int(10) NOT NULL COMMENT '用户id',
  `basic_skill` tinyint(3) NOT NULL DEFAULT '0' COMMENT '基本功 满分100',
  `coordinate` tinyint(3) NOT NULL DEFAULT '0' COMMENT '协调性 满分100',
  `feel` tinyint(3) NOT NULL DEFAULT '0' COMMENT '手感 满分100',
  `body` tinyint(3) NOT NULL DEFAULT '0' COMMENT '身体素质 满分100',
  `study` tinyint(3) NOT NULL DEFAULT '0' COMMENT '学习能力 满分100',
  `trianer_id` int(11) NOT NULL DEFAULT '0' COMMENT '评分人',
  PRIMARY KEY (`score_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户评分表';

-- ----------------------------
-- Records of sp_member_score
-- ----------------------------
INSERT INTO `sp_member_score` VALUES ('1', '3', '50', '0', '60', '70', '90', '1');

-- ----------------------------
-- Table structure for `sp_menu`
-- ----------------------------
DROP TABLE IF EXISTS `sp_menu`;
CREATE TABLE `sp_menu` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `parentid` smallint(6) unsigned NOT NULL DEFAULT '0',
  `app` char(20) NOT NULL COMMENT '应用名称app',
  `model` char(20) NOT NULL DEFAULT '',
  `action` char(20) NOT NULL DEFAULT '',
  `data` char(50) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `icon` varchar(50) DEFAULT NULL,
  `remark` varchar(255) NOT NULL DEFAULT '',
  `listorder` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序ID',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `parentid` (`parentid`),
  KEY `model` (`model`)
) ENGINE=MyISAM AUTO_INCREMENT=323 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_menu
-- ----------------------------
INSERT INTO `sp_menu` VALUES ('239', '0', 'Admin', 'Panel', 'default', '', '0', '1', '设置', 'cogs', '', '0');
INSERT INTO `sp_menu` VALUES ('51', '0', 'Admin', 'Content', 'default', '', '0', '1', '内容管理', 'th', '', '10');
INSERT INTO `sp_menu` VALUES ('245', '51', 'Admin', 'Term', 'index', '', '0', '0', '分类管理', '', '', '2');
INSERT INTO `sp_menu` VALUES ('299', '260', 'Admin', 'Api', 'setting', '', '1', '1', '接口配置', 'leaf', '', '4');
INSERT INTO `sp_menu` VALUES ('252', '239', 'Admin', 'Setting', 'default', '', '0', '1', '个人信息', null, '', '0');
INSERT INTO `sp_menu` VALUES ('253', '252', 'Admin', 'User', 'userinfo', '', '1', '1', '修改信息', '', '', '0');
INSERT INTO `sp_menu` VALUES ('254', '252', 'Admin', 'Setting', 'password', '', '1', '1', '修改密码', null, '', '0');
INSERT INTO `sp_menu` VALUES ('260', '0', 'Admin', 'Extension', 'default', '', '0', '1', '扩展工具', 'cloud', '', '30');
INSERT INTO `sp_menu` VALUES ('262', '260', 'Admin', 'Menu', 'add', '', '1', '0', '幻灯片', '', '', '1');
INSERT INTO `sp_menu` VALUES ('264', '262', 'Admin', 'Slide', 'index', '', '1', '1', '幻灯片管理', '', '', '0');
INSERT INTO `sp_menu` VALUES ('265', '260', 'Admin', 'ad', 'index', '', '1', '0', '网站广告', '', '', '2');
INSERT INTO `sp_menu` VALUES ('268', '262', 'Admin', 'Slidecat', 'index', '', '1', '1', '幻灯片分类', '', '', '0');
INSERT INTO `sp_menu` VALUES ('305', '0', 'Trainer', 'trainer', 'default', '', '1', '1', '微信模块', 'desktop', '', '0');
INSERT INTO `sp_menu` VALUES ('277', '51', 'Admin', 'Page', 'index', '', '1', '1', '页面管理', '', '', '3');
INSERT INTO `sp_menu` VALUES ('301', '300', 'Admin', 'Page', 'recyclebin', '', '1', '1', '页面回收', '', '', '1');
INSERT INTO `sp_menu` VALUES ('302', '300', 'Admin', 'Post', 'recyclebin', '', '1', '1', '文章回收', '', '', '0');
INSERT INTO `sp_menu` VALUES ('300', '51', 'Admin', 'recycle', 'default', '', '1', '1', '回收站', '', '', '4');
INSERT INTO `sp_menu` VALUES ('284', '239', 'Admin', 'setting', 'site', '', '1', '1', '网站信息', '', '', '0');
INSERT INTO `sp_menu` VALUES ('285', '51', 'Admin', 'Post', 'index', '', '1', '0', '文章管理', '', '', '1');
INSERT INTO `sp_menu` VALUES ('286', '0', 'Admin', 'Member', 'default', '', '1', '1', '用户管理', 'group', '', '0');
INSERT INTO `sp_menu` VALUES ('287', '311', 'Admin', 'Member', 'index', '', '1', '1', '本站用户', 'leaf', '', '0');
INSERT INTO `sp_menu` VALUES ('289', '286', 'Admin', 'Member', 'default1', '', '1', '0', '用户组', '', '', '0');
INSERT INTO `sp_menu` VALUES ('290', '286', 'Admin', 'Member', 'default3', '', '1', '1', '管理组', '', '', '0');
INSERT INTO `sp_menu` VALUES ('291', '290', 'Admin', 'Rbac', 'index', '', '1', '1', '角色管理', '', '', '0');
INSERT INTO `sp_menu` VALUES ('292', '290', 'Admin', 'User', 'index', '', '1', '1', '管理员', '', '', '0');
INSERT INTO `sp_menu` VALUES ('293', '0', 'Admin', 'Menu', 'default', '', '1', '1', '菜单管理', 'list', '', '0');
INSERT INTO `sp_menu` VALUES ('306', '305', 'Trainer', 'Trainer', 'default2', '', '1', '1', '教练组', '', '', '0');
INSERT INTO `sp_menu` VALUES ('307', '306', 'Trainer', 'City', 'index', '', '1', '1', '商家城市管理', '', '', '0');
INSERT INTO `sp_menu` VALUES ('297', '293', 'Admin', 'Menu', 'index', '', '1', '1', '后台菜单', '', '', '0');
INSERT INTO `sp_menu` VALUES ('298', '239', 'Admin', 'setting', 'clearcache', '', '1', '1', '清除缓存', '', '', '0');
INSERT INTO `sp_menu` VALUES ('303', '260', 'Admin', 'Backup', 'index', '', '1', '1', '数据备份', '', '', '0');
INSERT INTO `sp_menu` VALUES ('304', '260', 'Admin', 'Backup', 'restore', '', '1', '1', '数据恢复', '', '', '0');
INSERT INTO `sp_menu` VALUES ('308', '306', 'Trainer', 'City', 'arealist', '', '1', '1', '城市区域管理', '', '', '0');
INSERT INTO `sp_menu` VALUES ('309', '306', 'Trainer', 'Play', 'index', '', '1', '1', '打法管理', '', '', '0');
INSERT INTO `sp_menu` VALUES ('310', '306', 'Trainer', 'Trainer', 'index', '', '1', '1', '教练管理', '', '', '0');
INSERT INTO `sp_menu` VALUES ('311', '305', 'Trainer', 'Trainer', 'default2', '', '1', '1', '用户组', '', '', '0');
INSERT INTO `sp_menu` VALUES ('312', '311', 'Trainer', 'UserComment', 'index', '', '1', '1', '用户评论', '', '', '0');
INSERT INTO `sp_menu` VALUES ('313', '311', 'Trainer', 'Feedback', 'index', '', '1', '1', '用户反馈', '', '', '0');
INSERT INTO `sp_menu` VALUES ('314', '311', 'Trainer', 'Score', 'index', '', '1', '1', '用户评分', '', '', '0');
INSERT INTO `sp_menu` VALUES ('315', '305', 'Trainer', 'Order', 'index', '', '1', '1', '订单管理', '', '', '0');
INSERT INTO `sp_menu` VALUES ('316', '305', 'Trainer', 'Activity', 'index', '', '1', '1', '活动小课', '', '', '0');
INSERT INTO `sp_menu` VALUES ('317', '305', 'Trainer', 'Company', 'index', '', '1', '1', '企业服务管理', '', '', '0');
INSERT INTO `sp_menu` VALUES ('318', '305', 'Trainer', 'Coupon', 'index', '', '1', '1', '优惠券管理', '', '', '0');
INSERT INTO `sp_menu` VALUES ('322', '305', 'Trainer', 'ActOrder', 'index', '', '1', '1', '活动订单', '', '', '0');

-- ----------------------------
-- Table structure for `sp_merchant_city`
-- ----------------------------
DROP TABLE IF EXISTS `sp_merchant_city`;
CREATE TABLE `sp_merchant_city` (
  `mc_id` int(6) NOT NULL AUTO_INCREMENT,
  `city_name` varchar(10) NOT NULL COMMENT '城市名称',
  `remark` varchar(100) NOT NULL COMMENT '城市说明',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `is_open` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0没开通 1已开通',
  `img_url` varchar(100) NOT NULL COMMENT '封面图',
  PRIMARY KEY (`mc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='商家城市表';

-- ----------------------------
-- Records of sp_merchant_city
-- ----------------------------
INSERT INTO `sp_merchant_city` VALUES ('1', '北京', '预约平台4月6日正式上线', '1460190130', '1', '/trainer/static/data/upload/20160409/5708bb33d8957.jpg');
INSERT INTO `sp_merchant_city` VALUES ('2', '武汉', '建设中......', '1460190420', '0', '/trainer/static/data/upload/20160409/5708bb33d8957.jpg');
INSERT INTO `sp_merchant_city` VALUES ('6', '测试', '测试测试', '1460194142', '0', '/trainer/static/data/upload/20160409/5708bd386348e.png');
INSERT INTO `sp_merchant_city` VALUES ('7', '上海', '正在建设中', '1460194706', '0', '/trainer/static/data/upload/20160409/5708cd804a3bc.jpg');

-- ----------------------------
-- Table structure for `sp_nav`
-- ----------------------------
DROP TABLE IF EXISTS `sp_nav`;
CREATE TABLE `sp_nav` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `parentid` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `target` varchar(50) DEFAULT NULL,
  `href` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `status` int(2) NOT NULL DEFAULT '1',
  `listorder` int(6) DEFAULT '0',
  `path` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_nav
-- ----------------------------

-- ----------------------------
-- Table structure for `sp_nav_cat`
-- ----------------------------
DROP TABLE IF EXISTS `sp_nav_cat`;
CREATE TABLE `sp_nav_cat` (
  `navcid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `remark` text,
  PRIMARY KEY (`navcid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_nav_cat
-- ----------------------------

-- ----------------------------
-- Table structure for `sp_oauth_member`
-- ----------------------------
DROP TABLE IF EXISTS `sp_oauth_member`;
CREATE TABLE `sp_oauth_member` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `_from` varchar(20) NOT NULL,
  `_name` varchar(30) NOT NULL,
  `head_img` varchar(200) NOT NULL,
  `lock_to_id` int(20) NOT NULL,
  `create_time` int(12) NOT NULL,
  `last_login_time` int(12) NOT NULL,
  `last_login_ip` varchar(16) NOT NULL,
  `login_times` int(6) NOT NULL,
  `status` tinyint(2) NOT NULL,
  `access_token` varchar(60) NOT NULL,
  `expires_date` int(12) NOT NULL,
  `openid` varchar(40) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_oauth_member
-- ----------------------------

-- ----------------------------
-- Table structure for `sp_options`
-- ----------------------------
DROP TABLE IF EXISTS `sp_options`;
CREATE TABLE `sp_options` (
  `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(64) NOT NULL DEFAULT '',
  `option_value` longtext NOT NULL,
  `autoload` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_options
-- ----------------------------
INSERT INTO `sp_options` VALUES ('1', 'site_options', '{\"site_name\":\"\\u5728\\u7ebf\\u9884\\u7ea6\\u6559\\u7ec3\\u5e73\\u53f0\",\"site_host\":\"localhost\",\"site_icp\":\"\",\"site_admin_email\":\"\",\"site_tongji\":\"\",\"site_copyright\":\"\",\"site_seo_title\":\"\",\"site_seo_keywords\":\"\",\"site_seo_description\":\"\",\"urlmode\":\"0\",\"html_suffix\":\".html\"}', '1');

-- ----------------------------
-- Table structure for `sp_order`
-- ----------------------------
DROP TABLE IF EXISTS `sp_order`;
CREATE TABLE `sp_order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `orderno` varchar(50) NOT NULL DEFAULT '0' COMMENT '订单编号',
  `trainer_id` int(10) NOT NULL COMMENT '教练id',
  `member_id` int(10) NOT NULL COMMENT '用户id',
  `start_time` int(10) DEFAULT NULL COMMENT '预约开始时间(先不用)',
  `end_time` int(10) DEFAULT NULL COMMENT '预约结束时间(先不用)',
  `time_text` text NOT NULL COMMENT '服务时间字符串组合,逗号隔开',
  `train_person` tinyint(2) NOT NULL COMMENT '训练人数',
  `place` varchar(100) NOT NULL COMMENT '训练地点',
  `phone` varchar(11) NOT NULL COMMENT '联系电话',
  `message` varchar(200) DEFAULT NULL COMMENT '留言内容',
  `total_fee` float(10,2) NOT NULL COMMENT '总价',
  `add_time` int(10) NOT NULL COMMENT '订单提交时间',
  `pay_time` int(10) DEFAULT NULL COMMENT '订单支付时间',
  `status` tinyint(1) NOT NULL COMMENT '订单状态 0未支付 1已支付 2服务中(已接单）3已完成 -1已取消 -2已删除',
  `shoudan` float(10,2) DEFAULT NULL COMMENT '首单 减少的金额',
  `coupon_id` int(6) DEFAULT NULL COMMENT '下单使用的优惠券id',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COMMENT='订单表';

-- ----------------------------
-- Records of sp_order
-- ----------------------------
INSERT INTO `sp_order` VALUES ('9', '14613997856888', '1', '3', null, null, '2016-4-24:14', '2', '通天塔', '15136175246', '', '255.00', '1461399785', null, '2', null, '0');
INSERT INTO `sp_order` VALUES ('10', '14614028959552', '1', '3', null, null, '2016-4-23:18', '1', '烦烦烦', '15136175246', '', '250.00', '1461402895', null, '2', null, '0');
INSERT INTO `sp_order` VALUES ('11', '14614029245227', '3', '3', null, null, '2016-4-23:18,2016-4-23:19', '2', '反反复复', '15136175246', '', '200.00', '1461402924', null, '-2', null, '0');
INSERT INTO `sp_order` VALUES ('13', '14614029781853', '1', '3', null, null, '2016-4-24:9,2016-4-24:10', '1', '烦烦烦', '15136175246', '', '250.00', '1461402978', null, '-1', null, '0');
INSERT INTO `sp_order` VALUES ('14', '14614030295178', '1', '3', null, null, '2016-4-24:19,2016-4-24:20', '2', '烦烦烦', '15136175246', '', '255.00', '1461403029', null, '-1', null, '0');
INSERT INTO `sp_order` VALUES ('15', '14614030492883', '4', '3', null, null, '2016-4-23:18,2016-4-23:19', '2', '方法', '15136175246', '', '200.00', '1461403049', null, '3', null, '0');
INSERT INTO `sp_order` VALUES ('16', '14614030683547', '1', '3', null, null, '2016-4-24:15', '1', '烦烦烦', '15136175246', '', '250.00', '1461403068', null, '3', null, '0');
INSERT INTO `sp_order` VALUES ('17', '14614030876873', '1', '3', null, null, '2016-4-24:18', '1', '方法', '15136175246', '', '250.00', '1461403087', null, '-1', null, '0');
INSERT INTO `sp_order` VALUES ('18', '14614185223319', '1', '3', null, null, '2016-4-25:11', '2', '烦烦烦', '15136175246', '', '255.00', '1461418522', null, '3', null, '0');
INSERT INTO `sp_order` VALUES ('19', '14614186032641', '3', '3', null, null, '2016-4-24:10', '2', '烦烦烦', '15136175246', '', '200.00', '1461418603', null, '-1', null, '0');
INSERT INTO `sp_order` VALUES ('20', '14614188758116', '1', '3', null, null, '2016-4-24:10', '1', '33', '15136175246', '', '220.00', '1461418875', null, '-1', null, '1');
INSERT INTO `sp_order` VALUES ('21', '14614827144851', '1', '3', null, null, '2016-4-24:18', '1', '44', '15136175246', '', '250.00', '1461482714', null, '-1', null, '0');
INSERT INTO `sp_order` VALUES ('22', '14615859841407', '1', '3', null, null, '2016-4-26:10', '1', '444', '15136175246', '', '250.00', '1461585984', null, '-1', null, '0');
INSERT INTO `sp_order` VALUES ('23', '14616800228203', '1', '3', null, null, '2016-4-27:10', '1', '5555', '15136175246', '', '250.00', '1461680022', null, '0', null, '0');
INSERT INTO `sp_order` VALUES ('24', '14623660077961', '3', '4', null, null, '2016-5-05:10', '2', '55', '15136175246', '', '200.00', '1462366007', null, '-1', null, '0');
INSERT INTO `sp_order` VALUES ('25', '14628859828788', '1', '4', null, null, '2016-5-11:10', '1', 'fff', '15136175246', '', '250.00', '1462885982', null, '-1', null, '0');
INSERT INTO `sp_order` VALUES ('26', '14628860198304', '1', '4', null, null, '2016-5-11:10', '1', 'fff', '15136175246', '', '250.00', '1462886019', null, '-1', null, '0');
INSERT INTO `sp_order` VALUES ('27', '14628860604643', '1', '4', null, null, '2016-5-11:10', '1', 'fff', '15136175246', '', '250.00', '1462886060', null, '-1', null, '0');
INSERT INTO `sp_order` VALUES ('28', '14628860834271', '1', '4', null, null, '2016-5-11:10', '1', 'fff', '15136175246', '', '250.00', '1462886083', null, '-1', null, '0');
INSERT INTO `sp_order` VALUES ('29', '14628889773234', '1', '4', null, null, '2016-5-11:10', '1', 'fff', '15136175246', '', '250.00', '1462888977', null, '-1', null, '0');
INSERT INTO `sp_order` VALUES ('30', '14631443142547', '1', '4', null, null, '2016-5-14:10', '1', 'rr', '15136175246', '', '250.00', '1463144314', null, '-1', null, '0');
INSERT INTO `sp_order` VALUES ('31', 'tr14659062607406', '13', '4', null, null, '2016-6-16:11', '1', '66', '15136175246', '', '100.00', '1465906260', null, '-1', null, '0');
INSERT INTO `sp_order` VALUES ('32', 'tr14659062811221', '13', '4', null, null, '2016-6-15:10', '1', '66', '15136175246', '', '100.00', '1465906281', null, '-1', null, '0');
INSERT INTO `sp_order` VALUES ('33', 'tr14659063026412', '13', '4', null, null, '2016-6-15:10', '1', '66', '15136175246', '', '100.00', '1465906302', null, '-1', null, '0');
INSERT INTO `sp_order` VALUES ('34', 'tr14659063793437', '12', '4', null, null, '2016-6-15:10', '1', '66', '15136175246', '', '200.00', '1465906379', null, '1', null, '0');
INSERT INTO `sp_order` VALUES ('35', 'tr14659064961916', '13', '4', null, null, '2016-6-15:10', '1', '555', '15136175246', '', '100.00', '1465906496', null, '-1', null, '0');
INSERT INTO `sp_order` VALUES ('36', 'tr14659074794921', '13', '4', null, null, '2016-6-15:10', '1', '666', '15136175246', '', '100.00', '1465907479', null, '-1', null, '0');
INSERT INTO `sp_order` VALUES ('37', 'tr14659129262872', '13', '4', null, null, '2016-6-15:10', '2', '444', '15136175246', '', '110.00', '1465912926', null, '0', null, '0');

-- ----------------------------
-- Table structure for `sp_play`
-- ----------------------------
DROP TABLE IF EXISTS `sp_play`;
CREATE TABLE `sp_play` (
  `play_id` int(10) NOT NULL AUTO_INCREMENT,
  `play_name` varchar(10) NOT NULL COMMENT '打法名称',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`play_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='打法表';

-- ----------------------------
-- Records of sp_play
-- ----------------------------
INSERT INTO `sp_play` VALUES ('1', '横版', '1460211304');
INSERT INTO `sp_play` VALUES ('2', '直板', '1460211349');

-- ----------------------------
-- Table structure for `sp_postmeta`
-- ----------------------------
DROP TABLE IF EXISTS `sp_postmeta`;
CREATE TABLE `sp_postmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `post_id` (`post_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_postmeta
-- ----------------------------

-- ----------------------------
-- Table structure for `sp_posts`
-- ----------------------------
DROP TABLE IF EXISTS `sp_posts`;
CREATE TABLE `sp_posts` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_author` bigint(20) unsigned DEFAULT '0',
  `post_keywords` varchar(150) NOT NULL,
  `post_date` datetime DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext,
  `post_title` text,
  `post_excerpt` text,
  `post_status` int(2) DEFAULT '1',
  `wx_status` int(2) DEFAULT '1',
  `comment_status` int(2) DEFAULT '1',
  `post_modified` datetime DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` longtext,
  `post_parent` bigint(20) unsigned DEFAULT '0',
  `post_type` int(2) DEFAULT NULL,
  `post_mime_type` varchar(100) DEFAULT '',
  `comment_count` bigint(20) DEFAULT '0',
  `smeta` text,
  PRIMARY KEY (`ID`),
  KEY `type_status_date` (`wx_status`,`post_type`,`post_status`,`post_date`,`ID`),
  KEY `post_parent` (`post_parent`),
  KEY `post_author` (`post_author`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_posts
-- ----------------------------
INSERT INTO `sp_posts` VALUES ('1', '1', '', '2016-03-20 16:34:18', '<p>77777</p>', '77777', '7777', '1', '1', '1', '2016-03-20 16:34:09', null, '0', null, '', '0', '{\"thumb\":false}');
INSERT INTO `sp_posts` VALUES ('2', '1', '888', '2016-03-20 16:34:40', '<p>888</p>', '888', '888', '1', '1', '1', '2016-03-20 16:34:32', null, '0', null, '', '0', '{\"thumb\":false}');
INSERT INTO `sp_posts` VALUES ('3', '1', '', '2016-04-27 20:19:57', '<p>本协议为您与预约教练平台管理者之间所订立的契约，具有合同的法律效力，请您仔细阅读。</p><p>一、 本协议内容、生效、变更</p><p>本协议内容包括协议正文及所有教练在线预约已经发布的或将来可能发布 的各类规则。所有规则为本协议不可分割的组成部分，与协议正 文具有同等法律效力。除另行明确声明外，任何教练在线预约及其关联公 司提供的服务（以下称为教练在线预约平台服务）均受本协议约束。本协 议中，“用户”、“会员”为注册的会员及通过任何途径购买吾 动服务及产品的单位或个人。您应当在使用教练在线预约平台服务之前认 真阅读全部协议内容。如您对协议有任何疑问，应向教练在线预约咨询。 您在同意所有协议条款之后才能成为本站的正式用户，您点击“ 我已经阅读并同意遵守《教练在线预约服务协议》”按钮后，本协议即生 效，对双方产生约束力。只要您使用教练在线预约平台服务，则本协议即 对您产生约束，届时您不应以未阅读本协议的内容或者未获得吾 动对您问询的解答等理由，主张本协议无效，或要求撤销本协议。 您确认：本协议条款是处理双方权利义务的契约，始终有效，法 律另有强制性规定或双方另有特别约定的，依其规定。您承诺接 受并遵守本协议的约定。如果您不同意本协议的约定，您应立即 停止注册程序或停止使用教练在线预约平台服务。教练在线预约有权根据需要不定 期地制订、修改本协议及/或各类规则，并在教练在线预约平台公示，不再 另行单独通知用户。变更后的协议和规则一经在网站公布，立即 生效。如您不同意相关变更，应当立即停止使用教练在线预约平台服务。 您继续使用教练在线预约平台服务的，即表明您接受修订后的协议和规则。</p><p>二、 注册</p><p>注册资格</p><p>用户须具有法定的相应权利能力和行为能力的自然人、法人或其他组织，能够 独立承担法律责任。您完成注册程序或其他教练在线预约平台同意的方式实际使用本平 台服务时，即视为您确认自己具备主体资格，能够独立承担法律责任。若因您 不具备主体资格，而导致的一切后果，由您及您的监护人自行承担。</p><p>注册资料</p><p>1）用户应自行诚信向本站提供注册资料，用户同意其提供的注册资料真实、 准确、完整、合法有效，用户注册资料如有变动的，应及时更新其注册资料。 如果用户提供的注册资料不合法、不真实、不准确、不详尽的，用户需承担因 此引起的相应责任及后果，并且教练在线预约保留终止用户使用本平台各项服务的权利。</p><p>2）用户在本站进行浏览、预约教练、下单购物等活动时，涉及用户真实姓名/ 名称、通信地址、联系电话、电子邮箱等隐私信息的，本站将予以严格保密， 除非得到用户的授权或法律另有规定，本站不会向外界披露用户隐私信息。</p><p>账户</p><p>1）您注册成功后，即成为教练在线预约平台的会员。&nbsp;</p><p>2）用户不得将在本站注册的账户借给他人使用，否则用户应承担由此产生的 全部责任，并与实际使用人承担连带责任。&nbsp;</p><p>3）如果发现任何非法使用等可能危及您的账户安全的情形时，您应当立即以 有效方式通知教练在线预约，要求教练在线预约暂停相关服务，并向公安机关报案。您理解教练在线预约 对您的请求采取行动需要合理时间，教练在线预约对在采取行动前已经产生的后果（包 括但不限于您的任何损失）不承担任何责任。&nbsp;</p><p>4）为方便您使用教练在线预约平台服务及教练在线预约关联公司或其他组织的服务（以下称其 他服务），您同意并授权教练在线预约将您在注册、使用教练在线预约平台服务过程中提供、形 成的信息传递给向您提供其他服务的教练在线预约关联公司或其他组织，或从提供其他 服务的教练在线预约关联公司或其他组织获取您在注册、使用其他服务期间提供、形成 的信息。</p><p>用户信息的合理使用</p><p>1）您同意，教练在线预约平台拥有通过邮件、短信电话等形式，向在本站注册、购买、 收货人发送订单信息、促销活动等告知信息的权利。&nbsp;</p><p>2）您了解并同意，教练在线预约有权应国家司法、行政等主管部门的要求，向其提供 您在教练在线预约平台填写的注册信息和交易记录等必要信息。如您涉嫌侵犯他人知识 产权，则教练在线预约亦有权在初步判断涉嫌侵权行为存在的情况下，向权利人提供您 必要的身份信息。</p><p>三、 教练在线预约平台服务规范</p><p>通过教练在线预约及其关联公司提供的教练在线预约平台服务和其它服务，会员可在教练在线预约平台上 发布交易信息、查询商品和服务信息、达成交易意向并进行交易、对其他会员 进行评价、参加教练在线预约组织的活动以及使用其它信息服务及技术服务。 在教练在线预约平台上使用教练在线预约服务过程中，您同意严格遵守以下义务：</p><p>1）不得传输或发表损害国家、社会公共利益和涉及国家安全的信息资料或言 论；&nbsp;</p><p>2）不利用本站从事窃取商业秘密、窃取个人信息等违法犯罪活动；&nbsp;</p><p>3）不采取不正当竞争行为，不扰乱网上交易的正常秩序，不从事与网上交易 无关的行为；&nbsp;</p><p>4）不发布任何侵犯他人著作权、商标权等知识产权或合法权利的内容；&nbsp;</p><p>5）不以虚构或歪曲事实的方式不当评价其他会员；&nbsp;</p><p>6）不对教练在线预约平台上的任何数据作商业性利用，包括但不限于在未经教练在线预约事先 书面同意的情况下，以复制、传播等任何方式使用教练在线预约平台上展示的资料。 不使用任何装置、软件或程序干预教练在线预约平台的正常运营。&nbsp;</p><p>7）本站保有删除站内各类不符合法律政策或不真实的信息内容而无须通知用 户的权利。&nbsp;</p><p>8）您同意，若您未遵守以上规定的，本站有权作出独立判断并采取暂停或关 闭用户帐号、订单等措施。&nbsp;</p><p>9）经国家行政或司法机关的生效法律文书确认您存在违法或侵权行为，或者 教练在线预约根据自身的判断，认为您的行为涉嫌违反本协议和/或规则的条款或涉嫌违反法律法规的规定的，则教练在线预约有权在本平台上公示您的该等涉嫌违法或违约行为及教练在线预约已对您采取的措施。&nbsp;</p><p>10）对于您在教练在线预约平台上发布的涉嫌违法或涉嫌侵犯他人合法权利或违反本协议和/或规则的信息，教练在线预约有权不经通知您即予以删除，且按照规则的规定进行处罚。&nbsp;</p><p>11）对于您在教练在线预约平台上实施的行为，包括您未在教练在线预约平台上实施但已经对教练在线预约平台及其用户产生影响的行为，教练在线预约有权单方认定您行为的性质及是否构成对本协议和/或规则的违反，并采取暂停或关闭用户帐号及其他措施。&nbsp;</p><p>12）对于您涉嫌违反承诺的行为对任意第三方造成损害的，您均应当以自己的名义独立承担所有的法律责任，并应确保教练在线预约免于因此产生损失或增加费用。&nbsp;</p><p>13）如您涉嫌违反有关法律或者本协议之规定，使教练在线预约遭受任何损失，或受到任何第三方的索赔，或受到任何行政管理部门的处罚，您应当赔偿教练在线预约因此造成的损失及（或）发生的费用，包括合理的律师费用。</p><p>四、订单</p><p>在教练在线预约平台购买服务或商品的用户，请您仔细确认所服务及商品的名称、价格、数量、型号、规格、尺寸等信息。因平台展示的服务、商品和价格等信息仅仅是要约邀请，您下单时须完整填写您希望购买的商品或服务数量、价款及支付方式、收货人、联系方式、收货地址等内容。如果您提供的注册资料不合法、不真实、不准确、不详尽的，用户需承担因此引起的相应责任及后果，并且教练在线预约保留终止用户使用教练在线预约各项服务的权利。 由于互联网技术原因导致网页显示信息可能会有一定的滞后性或差错，对此在合同未成立前，您接受本平台在发现网页错误，正式向您发出通知后，对订单信息进行调整，订单按照正确的信息执行，或取消订单。 教练在线预约所服务的客户为以终端消费为目的的个人、企、事业单位及其他社会团体，不包括竞争对手及同行业经营者，如发现，教练在线预约将拒绝为其服务。 您了解并同意，如您购买商品，发生缺货，您有权取消订单；本平台无法保证您提交的订单信息中希望购买的商品都会有货。 教练在线预约保留在中华人民共和国大陆地区法施行之法律允许的范围内独自决定拒绝服务、关闭用户账户、清除或编辑内容或取消订单的权利。</p><p>五、责任范围和责任限制</p><p>教练在线预约在接到您投诉、主管机关通知或按照法律法规规定，有合理的理由认为特定会员及具体交易事项可能存在重大违法或违约情形时,教练在线预约可依约定或依法采取相应措施。 您了解并同意，教练在线预约不对因下述任一情况而导致您的任何损害承担赔偿责任，包括但不限于利润、商誉、使用、数据等方面的损失或其它无形损失的损害赔偿：</p><p>1）第三方未经批准的使用您的账户或更改您的数据。 2）您对教练在线预约平台服务的误解。 3）任何非因教练在线预约的原因而引起的与教练在线预约平台服务有关的其它损失。</p><p>由于法律规定的不可抗力，信息网络正常的设备维护，信息网络连接故障，电脑、通讯或其他系统的故障，电力故障，劳动争议，生产力或生产资料不足，司法行政机关的命令或第三方的不作为及其他本平台无法控制的原因造成的本平台不能服务或延迟服务、丢失数据信息、记录的，教练在线预约不承担责任，但教练在线预约将协助处理相关事宜。</p><p>六、协议终止</p><p>您同意，教练在线预约有权自行全权决定以任何理由不经事先通知的中止、终止向您提供部分或全部教练在线预约平台服务，暂时冻结或永久冻结（注销）您的账户，且无须为此向您或任何第三方承担任何责任。 出现以下情况时，教练在线预约有权直接以注销账户的方式终止本协议:</p><p>1）教练在线预约终止向您提供服务后，您涉嫌再一次直接或间接或以他人名义注册为教练在线预约用户的；&nbsp;</p><p>2）您注册信息中的主要内容不真实或不准确或不及时或不完整。&nbsp;</p><p>3）本协议（含规则）变更时，您明示并通知教练在线预约不愿接受新的服务协议的；&nbsp;</p><p>4）其它教练在线预约认为应当终止服务的情况。</p><p>您有权向教练在线预约要求注销您的账户，经教练在线预约审核同意的，教练在线预约注销（永久冻结）您的账户，届时，您与教练在线预约基于本协议的合同关系即终止。您的账户被注销（永久冻结）后，教练在线预约没有义务为您保留或向您披露您账户中的任何信息，也没有义务向您或第三方转发任何您未曾阅读或发送过的信息。 您同意，您与教练在线预约的合同关系终止后，教练在线预约仍享有下列权利：</p><p>1）继续保存您的注册信息及您使用教练在线预约平台服务期间的所有交易信息。 2）您在使用教练在线预约平台服务期间存在违法行为或违反本协议和/或规则的行为的，教练在线预约仍可依据本协议向您主张权利。</p><p>教练在线预约中止或终止向您提供教练在线预约平台服务后，对于您在服务中止或终止之前的交易行为依下列原则处理，您应独力处理并完全承担进行以下处理所产生的任何争议、损失或增加的任何费用，并应确保教练在线预约免于因此产生任何损失或承担任何费用：</p><p>七、法律适用、管辖与其他</p><p>本协议包含了您使用教练在线预约平台需遵守的一般性规范，您在使用某个教练在线预约平台时还需遵守适用于该平台的特殊性规范。一般性规范如与特殊性规范不一致或有冲突，则特殊性规范具有优先效力。 本协议的订立、执行和解释及争议的解决均应适用在中华人民共和国大陆地区适用之有效法律（但不包括其冲突法规则）。 如发生本协议与适用之法律相抵触时，则这些条款将完全按法律规定重新解释，而其它有效条款继续有效。 因本协议履行过程中，因您使用教练在线预约网服务产生争议应由教练在线预约与您沟通并协商处理。协商不成时，双方均同意以教练在线预约平台管理者住所地人民法院为管辖法院。</p><p><br/></p>', '服务协议', '', '1', '1', '1', '2016-04-27 20:15:55', null, '0', '2', '', '0', '{\"thumb\":\"\",\"template\":\"page\"}');

-- ----------------------------
-- Table structure for `sp_role`
-- ----------------------------
DROP TABLE IF EXISTS `sp_role`;
CREATE TABLE `sp_role` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '角色名称',
  `pid` smallint(6) DEFAULT NULL COMMENT '父角色ID',
  `status` tinyint(1) unsigned DEFAULT NULL COMMENT '状态',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL COMMENT '更新时间',
  `listorder` int(3) NOT NULL DEFAULT '0' COMMENT '排序字段',
  PRIMARY KEY (`id`),
  KEY `parentId` (`pid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='角色信息列表';

-- ----------------------------
-- Records of sp_role
-- ----------------------------
INSERT INTO `sp_role` VALUES ('1', 'ceshi', null, '1', 'ddddd', '0', '0', '0');
INSERT INTO `sp_role` VALUES ('2', 'what', null, '0', '77777', '0', '0', '0');

-- ----------------------------
-- Table structure for `sp_role_user`
-- ----------------------------
DROP TABLE IF EXISTS `sp_role_user`;
CREATE TABLE `sp_role_user` (
  `role_id` mediumint(9) unsigned DEFAULT NULL,
  `user_id` char(32) DEFAULT NULL,
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_role_user
-- ----------------------------

-- ----------------------------
-- Table structure for `sp_slide`
-- ----------------------------
DROP TABLE IF EXISTS `sp_slide`;
CREATE TABLE `sp_slide` (
  `slide_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slide_cid` bigint(20) NOT NULL,
  `slide_name` varchar(255) NOT NULL,
  `slide_pic` varchar(255) DEFAULT NULL,
  `slide_url` varchar(255) DEFAULT NULL,
  `slide_des` varchar(255) DEFAULT NULL,
  `slide_content` text,
  `slide_status` int(2) NOT NULL DEFAULT '1',
  `listorder` int(10) DEFAULT '0',
  PRIMARY KEY (`slide_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_slide
-- ----------------------------
INSERT INTO `sp_slide` VALUES ('1', '0', '444444', '/trainer/static/data/upload/20160409/5708d4f440415.png', '', '', '', '1', '0');

-- ----------------------------
-- Table structure for `sp_slide_cat`
-- ----------------------------
DROP TABLE IF EXISTS `sp_slide_cat`;
CREATE TABLE `sp_slide_cat` (
  `cid` bigint(20) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(255) NOT NULL,
  `cat_idname` varchar(255) NOT NULL,
  `cat_remark` text,
  `cat_status` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_slide_cat
-- ----------------------------

-- ----------------------------
-- Table structure for `sp_terms`
-- ----------------------------
DROP TABLE IF EXISTS `sp_terms`;
CREATE TABLE `sp_terms` (
  `term_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT '',
  `slug` varchar(200) DEFAULT '',
  `taxonomy` varchar(32) DEFAULT '',
  `description` longtext,
  `parent` bigint(20) unsigned DEFAULT '0',
  `count` bigint(20) DEFAULT '0',
  `path` varchar(500) DEFAULT NULL,
  `seo_title` varchar(500) DEFAULT NULL,
  `seo_keywords` varchar(500) DEFAULT NULL,
  `seo_description` varchar(500) DEFAULT NULL,
  `list_tpl` varchar(50) DEFAULT NULL,
  `one_tpl` varchar(50) DEFAULT NULL,
  `listorder` int(5) NOT NULL DEFAULT '0',
  `status` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`term_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_terms
-- ----------------------------
INSERT INTO `sp_terms` VALUES ('1', '公司新闻', '', 'article', '777777', '0', '0', '0-1', '', '', '', 'list', 'article', '0', '1');

-- ----------------------------
-- Table structure for `sp_term_relationships`
-- ----------------------------
DROP TABLE IF EXISTS `sp_term_relationships`;
CREATE TABLE `sp_term_relationships` (
  `tid` bigint(20) NOT NULL AUTO_INCREMENT,
  `object_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `listorder` int(10) NOT NULL DEFAULT '0',
  `status` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`tid`),
  KEY `term_taxonomy_id` (`term_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_term_relationships
-- ----------------------------
INSERT INTO `sp_term_relationships` VALUES ('1', '1', '1', '0', '1');
INSERT INTO `sp_term_relationships` VALUES ('2', '2', '1', '0', '1');

-- ----------------------------
-- Table structure for `sp_trainer`
-- ----------------------------
DROP TABLE IF EXISTS `sp_trainer`;
CREATE TABLE `sp_trainer` (
  `trainer_id` int(10) NOT NULL AUTO_INCREMENT,
  `trainer_name` varchar(10) NOT NULL COMMENT '教练姓名',
  `avatar` varchar(100) NOT NULL COMMENT '头像',
  `mc_id` int(6) DEFAULT NULL COMMENT '商家城市id 冗余字段',
  `play_ids` varchar(50) NOT NULL COMMENT '打法id组合,逗号隔开',
  `area_ids` varchar(50) DEFAULT NULL COMMENT '区域id组合逗号隔开',
  `service_time_type` tinyint(1) DEFAULT NULL COMMENT '服务时间类型0所有 1周一至周日 2周一至周五 3周六周日',
  `service_json` text NOT NULL COMMENT '服务区域与时间组合的json字符串',
  `original_price` float(10,2) NOT NULL COMMENT '原价',
  `introduction` text NOT NULL COMMENT '简介每一句内容以|结束',
  `price` float(10,2) NOT NULL COMMENT '促销价格，元/小时',
  `sex` tinyint(1) NOT NULL COMMENT '性别 1男 2女',
  `teaching_type` tinyint(1) NOT NULL COMMENT '教学类型 1陪练 2比赛指导 3两者兼有',
  `level` tinyint(1) DEFAULT NULL COMMENT '教练级别 1国家一级运动员 2二级 3三级 4普通',
  `total_time` float(10,1) NOT NULL DEFAULT '0.0' COMMENT '累计教学时间 单位小时',
  `is_promotion` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否促销 0否 1是',
  `comment` int(6) NOT NULL DEFAULT '0' COMMENT '评论数量',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `phone` varchar(11) NOT NULL COMMENT '手机号',
  `pwd` varchar(100) DEFAULT NULL COMMENT '教练登陆入口的密码',
  PRIMARY KEY (`trainer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='教练信息表';

-- ----------------------------
-- Records of sp_trainer
-- ----------------------------
INSERT INTO `sp_trainer` VALUES ('1', '赵德芳1', '/trainer/static/data/upload/20160410/570a54313517a.jpg', '1', '1', null, null, '{\"海淀区\":\"周一至五\"}', '300.00', '北京体育大学任乒乓球代表队主力|北京市青少年锦标赛团体第一名、单打第二名', '250.00', '1', '1', '1', '0.0', '1', '10', '1460281209', '15136175245', '96e79218965eb72c92a549dd5a330112');
INSERT INTO `sp_trainer` VALUES ('2', '张大侠', '/trainer/static/data/upload/20160410/570a54be00207.jpg', '7', '2', null, null, '{\"上海区1\":\"周一至周日\",\"上海区2\":\"周六周日\"}', '520.00', '北京体育大学任乒乓球代表队主力|全国中学生乒乓球锦标赛团体第二名', '100.00', '2', '2', '2', '0.0', '1', '0', '1460281525', '15136175546', '96e79218965eb72c92a549dd5a330112');
INSERT INTO `sp_trainer` VALUES ('3', '赵德芳2', '/trainer/static/data/upload/20160410/570a53f751564.jpg', '1', '2', null, null, '{\"海淀区\":\"周六、日\",\"朝阳区\":\"周一至五\"}', '400.00', '北京体育大学任乒乓球代表队主力|北京市青少年锦标赛团体第一名、单打第二名', '350.00', '1', '1', '1', '0.0', '1', '0', '1460294658', '15136175247', '96e79218965eb72c92a549dd5a330112');
INSERT INTO `sp_trainer` VALUES ('4', '赵德芳3', '/trainer/static/data/upload/20160410/570a54313517a.jpg', '1', '1', null, null, '{\"海淀区\":\"周一至五\"}', '300.00', '北京体育大学任乒乓球代表队主力|北京市青少年锦标赛团体第一名、单打第二名', '200.00', '1', '1', '1', '0.0', '1', '0', '1460387410', '15136175248', '96e79218965eb72c92a549dd5a330112');
INSERT INTO `sp_trainer` VALUES ('5', '赵德芳4', '/trainer/static/data/upload/20160410/570a54313517a.jpg', '1', '1', null, null, '{\"海淀区\":\"周一至五\"}', '300.00', '北京体育大学任乒乓球代表队主力|北京市青少年锦标赛团体第一名、单打第二名', '200.00', '1', '1', '1', '0.0', '1', '0', '1460387415', '15136175249', '96e79218965eb72c92a549dd5a330112');
INSERT INTO `sp_trainer` VALUES ('6', '赵德芳5', '/trainer/static/data/upload/20160410/570a54313517a.jpg', '1', '1', null, null, '{\"海淀区\":\"周一至五\"}', '300.00', '北京体育大学任乒乓球代表队主力|北京市青少年锦标赛团体第一名、单打第二名', '200.00', '1', '1', '1', '0.0', '1', '0', '1460387420', '15136175240', '96e79218965eb72c92a549dd5a330112');
INSERT INTO `sp_trainer` VALUES ('7', '赵德芳6', '/trainer/static/data/upload/20160410/570a54313517a.jpg', '1', '1', null, null, '{\"海淀区\":\"周一至五\"}', '300.00', '北京体育大学任乒乓球代表队主力|北京市青少年锦标赛团体第一名、单打第二名', '200.00', '1', '1', '1', '0.0', '1', '0', '1460387424', '15136175233', '96e79218965eb72c92a549dd5a330112');
INSERT INTO `sp_trainer` VALUES ('8', '赵德芳7', '/trainer/static/data/upload/20160410/570a54313517a.jpg', '1', '1', null, null, '{\"海淀区\":\"周一至五\"}', '300.00', '北京体育大学任乒乓球代表队主力|北京市青少年锦标赛团体第一名、单打第二名', '200.00', '1', '1', '1', '0.0', '1', '0', '1460387429', '15136175234', '96e79218965eb72c92a549dd5a330112');
INSERT INTO `sp_trainer` VALUES ('9', '赵德芳8', '/trainer/static/data/upload/20160410/570a54313517a.jpg', '1', '1', null, null, '{\"海淀区\":\"周一至五\"}', '300.00', '北京体育大学任乒乓球代表队主力|北京市青少年锦标赛团体第一名、单打第二名', '200.00', '1', '1', '1', '0.0', '1', '0', '1460387433', '15136175223', '96e79218965eb72c92a549dd5a330112');
INSERT INTO `sp_trainer` VALUES ('10', '赵德芳9', '/trainer/static/data/upload/20160410/570a54313517a.jpg', '1', '1', null, null, '{\"海淀区\":\"周一至五\"}', '300.00', '北京体育大学任乒乓球代表队主力|北京市青少年锦标赛团体第一名、单打第二名', '90.00', '1', '1', '1', '0.0', '1', '0', '1460387438', '15136175212', '96e79218965eb72c92a549dd5a330112');
INSERT INTO `sp_trainer` VALUES ('11', '赵德芳10', '/trainer/static/data/upload/20160410/570a54313517a.jpg', '1', '1', null, null, '{\"海淀区\":\"周一至五\"}', '300.00', '北京体育大学任乒乓球代表队主力|北京市青少年锦标赛团体第一名、单打第二名', '200.00', '1', '1', '1', '0.0', '1', '0', '1460387444', '15136175213', '96e79218965eb72c92a549dd5a330112');
INSERT INTO `sp_trainer` VALUES ('12', '赵德芳11', '/trainer/static/data/upload/20160410/570a54313517a.jpg', '1', '1', null, null, '{\"海淀区\":\"周一至五\"}', '300.00', '北京体育大学任乒乓球代表队主力|北京市青少年锦标赛团体第一名、单打第二名', '200.00', '1', '1', '1', '0.0', '1', '0', '1460387449', '15136175217', '96e79218965eb72c92a549dd5a330112');
INSERT INTO `sp_trainer` VALUES ('13', 'hupeng', '/trainer/static/data/upload/20160505/572b496a4d9d3.jpg', '1', '1', null, null, '{\"海淀区\":\"周六、日\"}', '200.00', '1111111|22222222|333333', '100.00', '1', '1', '1', '0.0', '1', '0', '1462454679', '15136175246', '96e79218965eb72c92a549dd5a330112');

-- ----------------------------
-- Table structure for `sp_usermeta`
-- ----------------------------
DROP TABLE IF EXISTS `sp_usermeta`;
CREATE TABLE `sp_usermeta` (
  `umeta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`umeta_id`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_usermeta
-- ----------------------------

-- ----------------------------
-- Table structure for `sp_users`
-- ----------------------------
DROP TABLE IF EXISTS `sp_users`;
CREATE TABLE `sp_users` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) NOT NULL DEFAULT '',
  `user_pass` varchar(64) NOT NULL DEFAULT '',
  `user_nicename` varchar(50) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `user_url` varchar(100) NOT NULL DEFAULT '',
  `last_login_ip` varchar(16) NOT NULL,
  `last_login_time` datetime NOT NULL,
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(60) NOT NULL DEFAULT '',
  `user_status` int(11) NOT NULL DEFAULT '1',
  `display_name` varchar(250) NOT NULL DEFAULT '',
  `role_id` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_users
-- ----------------------------
INSERT INTO `sp_users` VALUES ('1', 'admin', 'e10adc3949ba59abbe56e057f20f883e', '', 'admin@qq.com', '', '0.0.0.0', '2016-06-12 22:37:28', '2016-03-20 03:42:37', '', '1', 'admin', '1');
INSERT INTO `sp_users` VALUES ('2', '444', '550a141f12de6341fba65b0ad0433500', '', '444', '', '', '0000-00-00 00:00:00', '2016-04-09 18:00:18', '', '1', '', '1');
INSERT INTO `sp_users` VALUES ('3', '777', 'f1c1592588411002af340cbaedd6fc33', '', '7777', '', '', '0000-00-00 00:00:00', '2016-04-09 18:00:57', '', '1', '', '1');

-- ----------------------------
-- Table structure for `sp_wx_event`
-- ----------------------------
DROP TABLE IF EXISTS `sp_wx_event`;
CREATE TABLE `sp_wx_event` (
  `id` int(10) unsigned NOT NULL,
  `ToUserName` varchar(200) DEFAULT NULL,
  `FromUserName` varchar(200) DEFAULT NULL,
  `CreateTime` int(10) unsigned DEFAULT NULL,
  `Event` varchar(20) DEFAULT NULL,
  `EventKey` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_wx_event
-- ----------------------------

-- ----------------------------
-- Table structure for `sp_wx_info`
-- ----------------------------
DROP TABLE IF EXISTS `sp_wx_info`;
CREATE TABLE `sp_wx_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ToUserName` varchar(200) DEFAULT NULL,
  `FromUserName` varchar(200) DEFAULT NULL,
  `CreateTime` int(10) unsigned DEFAULT NULL,
  `MsgType` varchar(20) DEFAULT NULL,
  `Content` varchar(500) DEFAULT NULL,
  `MsgId` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp_wx_info
-- ----------------------------

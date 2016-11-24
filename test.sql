/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-11-23 18:58:28
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for post
-- ----------------------------
DROP TABLE IF EXISTS `post`;
CREATE TABLE `post` (
  `postid` bigint(20) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `username` char(20) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0',
  `content` char(144) DEFAULT NULL,
  PRIMARY KEY (`postid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of post
-- ----------------------------
INSERT INTO `post` VALUES ('46', '1', 'yanshiba', '1479895774', 'ggjggggj');
INSERT INTO `post` VALUES ('47', '1', 'yanshiba', '1479895776', 'ggjgggjg');
INSERT INTO `post` VALUES ('51', '1', 'yanshiba', '1479896504', '111111111111111');
INSERT INTO `post` VALUES ('52', '1', 'yanshiba', '1479896786', '111111');
INSERT INTO `post` VALUES ('53', '1', 'yanshiba', '1479896822', '333');
INSERT INTO `post` VALUES ('54', '1', 'yanshiba', '1479898140', '11111');

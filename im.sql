/*
 Navicat Premium Data Transfer

 Source Server         : 测试服务器
 Source Server Type    : MySQL
 Source Server Version : 50639
 Source Host           : 127.0.0.1:3311
 Source Schema         : im

 Target Server Type    : MySQL
 Target Server Version : 50639
 File Encoding         : 65001

 Date: 05/11/2020 09:36:32
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for im_user_temp_friends
-- ----------------------------
DROP TABLE IF EXISTS `im_user_temp_friends`;
CREATE TABLE `im_user_temp_friends`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student` char(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '学员id',
  `teacher` char(36) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `accid`(`student`, `teacher`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_bin COMMENT = '临时好友表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for im_user_temp_leave_messages
-- ----------------------------
DROP TABLE IF EXISTS `im_user_temp_leave_messages`;
CREATE TABLE `im_user_temp_leave_messages`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '手机号',
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL COMMENT '姓名',
  `remark` text CHARACTER SET utf8 COLLATE utf8_bin NULL COMMENT '留言',
  `ip` int(11) UNSIGNED NULL DEFAULT NULL COMMENT 'ip',
  `opt_platform` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
  `push_flag` tinyint(1) NOT NULL DEFAULT 0 COMMENT '推送',
  `action_url` varchar(512) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL COMMENT '访问地址',
  `land_page` varchar(512) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '落地页',
  `land_page_title` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '落地页标题',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for im_user_temp_records
-- ----------------------------
DROP TABLE IF EXISTS `im_user_temp_records`;
CREATE TABLE `im_user_temp_records`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NULL DEFAULT NULL,
  `from` char(36) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL COMMENT '发送者accid',
  `to` char(36) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL COMMENT '接受者accid',
  `type` tinyint(1) NULL DEFAULT NULL COMMENT '0 表示文本消息,1 表示图片，2 表示语音，3 表示视频，4 表示地理位置信息，6 表示文件，100 自定义消息类型',
  `is_revoke` tinyint(1) NULL DEFAULT 0,
  `context` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT '历史记录',
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT '全部数据',
  `created_at` timestamp(0) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`, `created_at`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 189 CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for im_user_temp_sessions
-- ----------------------------
DROP TABLE IF EXISTS `im_user_temp_sessions`;
CREATE TABLE `im_user_temp_sessions`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student` char(17) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `teacher` char(36) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `message_num` tinyint(4) NULL DEFAULT 0 COMMENT '访客消息数',
  `customer_message_num` tinyint(4) NULL DEFAULT 0 COMMENT '客服消息数',
  `session_created_at` timestamp(0) NULL DEFAULT NULL COMMENT '会话开始时间',
  `session_end_at` timestamp(0) NULL DEFAULT NULL COMMENT '会话结束时间',
  `session_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态',
  `is_valid` tinyint(1) NULL DEFAULT 0 COMMENT '是否有效',
  `session_time` int(11) NULL DEFAULT 0 COMMENT '会话时长',
  `first_response_time` int(11) NULL DEFAULT 0 COMMENT '首次相应时长',
  `avg_response_time` int(11) NULL DEFAULT 0 COMMENT '平均相应时长',
  `max_response_time` int(11) NULL DEFAULT 0 COMMENT '最大相应时长',
  `action_url` varchar(512) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL COMMENT '访问地址',
  `land_page` varchar(512) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '落地页',
  `land_page_title` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '落地页标题',
  `search_term` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '搜索词',
  `offline_time` timestamp(0) NULL DEFAULT NULL COMMENT '离线时间',
  `unread_msg_count` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `accid`(`student`, `teacher`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 101 CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for im_user_temps
-- ----------------------------
DROP TABLE IF EXISTS `im_user_temps`;
CREATE TABLE `im_user_temps`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(17) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'accid',
  `api_token` char(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'token',
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '姓名',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '名片状态',
  `online` tinyint(1) NOT NULL DEFAULT 0,
  `ip` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '转化后的ip',
  `opt_platform` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '终端',
  `province` varchar(6) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '省',
  `city` varchar(6) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '市',
  `teacher` char(36) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '接线员',
  `action_url` varchar(512) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL COMMENT '访问地址',
  `land_page` varchar(512) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '落地页',
  `land_page_title` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '落地页标题',
  `search_term` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '搜索词',
  `phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '',
  `wechat` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '',
  `qq` varchar(12) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '',
  `remark` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `uuid`(`uuid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 577 CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;

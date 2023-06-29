-- MariaDB dump 10.19  Distrib 10.11.2-MariaDB, for osx10.18 (arm64)
--
-- Host: localhost    Database: nuclein
-- ------------------------------------------------------
-- Server version	10.11.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `kit`
--

DROP TABLE IF EXISTS `kit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kit` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `serial` varchar(11) NOT NULL COMMENT '管码',
  `fuid` int(10) unsigned NOT NULL COMMENT '所属用户ID',
  `status` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '状态（0=未检，1=阴性，2=阳性）',
  `is_del` enum('0','1') NOT NULL DEFAULT '0',
  `create_time` datetime NOT NULL DEFAULT current_timestamp(),
  `update_time` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `serial` (`serial`),
  KEY `fuid` (`fuid`),
  CONSTRAINT `kit_ibfk_1` FOREIGN KEY (`fuid`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='核酸检测管信息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notice`
--

DROP TABLE IF EXISTS `notice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notice` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` text DEFAULT NULL COMMENT '内容',
  `media_url` varchar(255) DEFAULT NULL COMMENT '媒体URL',
  `uid` int(10) unsigned NOT NULL COMMENT '添加者（仅超管）',
  `is_del` enum('0','1') NOT NULL DEFAULT '0',
  `create_time` datetime NOT NULL DEFAULT current_timestamp(),
  `update_time` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  CONSTRAINT `notice_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(11) NOT NULL COMMENT '用户名',
  `password` char(32) DEFAULT NULL COMMENT '密码（md5）',
  `uniqid` char(18) NOT NULL COMMENT '身份证号码或员工号码（登录用）',
  `gid` enum('1','2','3','4') NOT NULL DEFAULT '1' COMMENT '组ID（1=普通用户，2=扫码员，3=检测员，4=超管）',
  `wechat_openid` char(29) DEFAULT NULL COMMENT '微信OpenID',
  `is_del` enum('0','1') NOT NULL DEFAULT '0',
  `create_time` datetime NOT NULL DEFAULT current_timestamp(),
  `update_time` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idcard` (`uniqid`),
  UNIQUE KEY `wechat_openid` (`wechat_openid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='用户信息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_kit`
--

DROP TABLE IF EXISTS `user_kit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_kit` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `kid` int(11) unsigned NOT NULL COMMENT '检测管ID',
  `uid` int(11) unsigned NOT NULL COMMENT '用户ID',
  `add_time` datetime NOT NULL DEFAULT current_timestamp() COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kid` (`kid`,`uid`),
  KEY `uid` (`uid`),
  CONSTRAINT `user_kit_ibfk_1` FOREIGN KEY (`kid`) REFERENCES `kit` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_kit_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='用户-检测管关联表';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed

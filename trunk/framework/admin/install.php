<?php
include('../core/autoload.php');
include('../core/settings.php');
$db = DB::getInstance();
$db->start_transaction();

$t = <<<TB
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varbinary(100) default NULL,
  `pass` varbinary(100) default NULL,
  `username` varbinary(100) default NULL,
  `surname` varchar(100) default NULL,
  `salt` varchar(12) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `NewIndex1` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
TB;
$db->db_query($t);
$salt = Utils::genRandom();
$pass = $salt.$settings->guest_passwd;
$db->db_query("INSERT INTO users VALUES(NULL, 'Guest', SHA1('$pass'), 'guest', 'User', '$salt')");
$salt = Utils::genRandom();
$pass = $salt.$settings->admin_passwd;
$db->db_query("INSERT INTO users VALUES(NULL, 'Administrator', SHA1('$pass'), 'admin', '', '$salt')");

$t = <<<TB
CREATE TABLE `user_token` (                                                                       
              `uid` int(20) unsigned NOT NULL,                                                                
              `sid` varchar(12) default NULL,                                                                 
              `token` varchar(32) default NULL,                                                               
              PRIMARY KEY  (`uid`, `sid`, `token`),                                                                           
              CONSTRAINT `user_token_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE  
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC
TB;
$db->db_query($t);

$t = <<<TB
CREATE TABLE `modules` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  `permissions` mediumtext,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `NewIndex1` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
TB;
$db->db_query($t);
$db->db_query("INSERT INTO modules VALUES(NULL, 'Page', '{\"users\":[],\"groups\":{\"3\":\"1\",\"1\":\"4\"}}')");
$db->db_query("INSERT INTO modules VALUES(NULL, 'Admin', '{\"users\":[],\"groups\":{\"1\":\"4\"}}')");

$t =<<<TB
CREATE TABLE `languages` (
  `id` varchar(5) NOT NULL,
  `name` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
TB;
$db->db_query($t);
$db->db_query("INSERT INTO languages VALUES('el_GR', 'ελληνικά')");
$db->db_query("INSERT INTO languages VALUES('en_US', 'english')");

$t = <<<TB
CREATE TABLE `groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `NewIndex1` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
TB;
$db->db_query($t);
$db->db_query("INSERT INTO groups VALUES(NULL, 'Admins')");
$db->db_query("INSERT INTO groups VALUES(NULL, 'Authusers')");
$db->db_query("INSERT INTO groups VALUES(NULL, 'Everyone')");

$t = <<<TB
CREATE TABLE `user_groups` (
  `userID` int(10) unsigned NOT NULL,
  `groupID` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`userID`,`groupID`),
  KEY `FK_user_groups` (`groupID`),
  CONSTRAINT `FK_user_groups1` FOREIGN KEY (`userID`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_user_groups` FOREIGN KEY (`groupID`) REFERENCES `groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8
TB;
$db->db_query($t);
$db->db_query("INSERT INTO user_groups VALUES(2, 1)");
$db->commit();
?>
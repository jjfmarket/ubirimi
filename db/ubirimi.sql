-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1:33066
-- Generation Time: Mar 11, 2015 at 02:29 PM
-- Server version: 5.5.41
-- PHP Version: 5.5.22-1~dotdeb.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `ubirimi`
--

-- --------------------------------------------------------

--
-- Table structure for table `agile_board`
--

CREATE TABLE IF NOT EXISTS `agile_board` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `filter_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `swimlane_strategy` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `user_created_id` bigint(20) unsigned NOT NULL,
  `date_created` date NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_created_id` (`user_created_id`),
  KEY `client_id` (`client_id`),
  KEY `filter_id` (`filter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `agile_board_column`
--

CREATE TABLE IF NOT EXISTS `agile_board_column` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `agile_board_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `position` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `agile_board_id` (`agile_board_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `agile_board_column_status`
--

CREATE TABLE IF NOT EXISTS `agile_board_column_status` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `agile_board_column_id` bigint(20) unsigned NOT NULL,
  `issue_status_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `agile_board_column_id` (`agile_board_column_id`),
  KEY `issue_status_id` (`issue_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `agile_board_project`
--

CREATE TABLE IF NOT EXISTS `agile_board_project` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `agile_board_id` bigint(20) unsigned NOT NULL,
  `project_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `agile_board_id` (`agile_board_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `agile_board_sprint`
--

CREATE TABLE IF NOT EXISTS `agile_board_sprint` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `agile_board_id` bigint(20) unsigned NOT NULL,
  `user_created_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `started_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `finished_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_created` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `agile_board_id` (`agile_board_id`),
  KEY `user_created_id` (`user_created_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `agile_board_sprint_issue`
--

CREATE TABLE IF NOT EXISTS `agile_board_sprint_issue` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `agile_board_sprint_id` bigint(20) unsigned NOT NULL,
  `issue_id` bigint(20) unsigned NOT NULL,
  `done_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `agile_board_sprint_id` (`agile_board_sprint_id`),
  KEY `issue_id` (`issue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_calendar`
--

CREATE TABLE IF NOT EXISTS `cal_calendar` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `color` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `default_flag` tinyint(3) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_calendar_default_reminder`
--

CREATE TABLE IF NOT EXISTS `cal_calendar_default_reminder` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cal_calendar_id` bigint(20) unsigned NOT NULL,
  `cal_event_reminder_type_id` bigint(20) unsigned NOT NULL,
  `cal_event_reminder_period_id` bigint(20) unsigned NOT NULL,
  `value` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_calendar_share`
--

CREATE TABLE IF NOT EXISTS `cal_calendar_share` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cal_calendar_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_event`
--

CREATE TABLE IF NOT EXISTS `cal_event` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cal_calendar_id` bigint(20) unsigned NOT NULL,
  `user_created_id` bigint(20) NOT NULL,
  `cal_event_repeat_id` bigint(20) unsigned DEFAULT NULL,
  `cal_event_link_id` bigint(20) unsigned DEFAULT NULL,
  `date_from` datetime NOT NULL,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `location` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_to` datetime NOT NULL,
  `color` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cal_calendar_id` (`cal_calendar_id`),
  KEY `user_created_id` (`user_created_id`),
  KEY `cal_event_repeat_id` (`cal_event_repeat_id`),
  KEY `cal_event_link_id` (`cal_event_link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_event_reminder`
--

CREATE TABLE IF NOT EXISTS `cal_event_reminder` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cal_event_id` bigint(20) unsigned NOT NULL,
  `cal_event_reminder_type_id` bigint(20) unsigned NOT NULL,
  `cal_event_reminder_period_id` bigint(20) unsigned NOT NULL,
  `value` int(10) unsigned NOT NULL,
  `fired_flag` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_event_reminder_period`
--

CREATE TABLE IF NOT EXISTS `cal_event_reminder_period` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_event_reminder_type`
--

CREATE TABLE IF NOT EXISTS `cal_event_reminder_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_event_repeat`
--

CREATE TABLE IF NOT EXISTS `cal_event_repeat` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cal_event_repeat_cycle_id` bigint(20) unsigned NOT NULL,
  `repeat_every` int(10) unsigned NOT NULL,
  `end_after_occurrences` int(10) unsigned DEFAULT NULL,
  `on_day_0` tinyint(3) unsigned DEFAULT '0',
  `on_day_1` tinyint(3) unsigned DEFAULT '0',
  `on_day_2` tinyint(3) unsigned DEFAULT '0',
  `on_day_3` tinyint(3) unsigned DEFAULT '0',
  `on_day_4` tinyint(3) unsigned DEFAULT '0',
  `on_day_5` tinyint(3) unsigned DEFAULT '0',
  `on_day_6` tinyint(3) unsigned DEFAULT '0',
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_event_repeat_cycle`
--

CREATE TABLE IF NOT EXISTS `cal_event_repeat_cycle` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cal_event_share`
--

CREATE TABLE IF NOT EXISTS `cal_event_share` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cal_event_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE IF NOT EXISTS `client` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sys_country_id` bigint(20) unsigned DEFAULT NULL,
  `company_name` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `base_url` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `address_1` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_2` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `district` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_email` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `installed_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `instance_type` tinyint(3) unsigned NOT NULL COMMENT '1 - on demand; 2 - download',
  `timezone` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `language` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `country_id` (`sys_country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `client_documentator_settings`
--

CREATE TABLE IF NOT EXISTS `client_documentator_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `anonymous_use_flag` tinyint(4) unsigned NOT NULL,
  `anonymous_view_user_profile_flag` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `client_product`
--

CREATE TABLE IF NOT EXISTS `client_product` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `sys_product_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `sys_product_id` (`sys_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `client_settings`
--

CREATE TABLE IF NOT EXISTS `client_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `title_name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `operating_mode` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `timezone` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `language` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `client_smtp_settings`
--

CREATE TABLE IF NOT EXISTS `client_smtp_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `default_ubirimi_server_flag` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `from_address` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email_prefix` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `smtp_protocol` tinyint(3) unsigned NOT NULL,
  `hostname` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `port` int(11) NOT NULL,
  `timeout` int(10) unsigned NOT NULL,
  `tls_flag` tinyint(4) NOT NULL DEFAULT '0',
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `client_yongo_settings`
--

CREATE TABLE IF NOT EXISTS `client_yongo_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `allow_unassigned_issues_flag` tinyint(3) unsigned DEFAULT NULL,
  `issues_per_page` int(10) unsigned NOT NULL,
  `allow_attachments_flag` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `notify_own_changes_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `issue_linking_flag` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `time_tracking_flag` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `time_tracking_hours_per_day` float unsigned NOT NULL,
  `time_tracking_days_per_week` int(10) unsigned NOT NULL,
  `time_tracking_default_unit` varchar(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_entity`
--

CREATE TABLE IF NOT EXISTS `documentator_entity` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documentator_entity_type_id` bigint(20) unsigned NOT NULL,
  `documentator_space_id` bigint(20) unsigned NOT NULL,
  `parent_entity_id` bigint(20) unsigned DEFAULT NULL,
  `user_created_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `in_trash_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documentator_space_id` (`documentator_space_id`),
  KEY `user_created_id` (`user_created_id`),
  KEY `page_parent_id` (`parent_entity_id`),
  KEY `documentator_entity_id` (`documentator_entity_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_entity_attachment`
--

CREATE TABLE IF NOT EXISTS `documentator_entity_attachment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documentator_entity_id` bigint(20) unsigned NOT NULL,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `documentator_entity_id` (`documentator_entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_entity_attachment_revision`
--

CREATE TABLE IF NOT EXISTS `documentator_entity_attachment_revision` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documentator_entity_attachment_id` bigint(20) unsigned NOT NULL,
  `user_created_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `documentator_entity_attachment_id` (`documentator_entity_attachment_id`),
  KEY `user_created_id` (`user_created_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_entity_comment`
--

CREATE TABLE IF NOT EXISTS `documentator_entity_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `documentator_entity_id` bigint(10) unsigned NOT NULL,
  `parent_comment_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `content` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `documentator_entity_id` (`documentator_entity_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_comment_id` (`parent_comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_entity_file`
--

CREATE TABLE IF NOT EXISTS `documentator_entity_file` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documentator_entity_id` bigint(20) unsigned NOT NULL,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `documentator_entity_id` (`documentator_entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_entity_file_revision`
--

CREATE TABLE IF NOT EXISTS `documentator_entity_file_revision` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documentator_entity_file_id` bigint(20) unsigned NOT NULL,
  `user_created_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `documentator_entity_file_id` (`documentator_entity_file_id`),
  KEY `user_created_id` (`user_created_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_entity_revision`
--

CREATE TABLE IF NOT EXISTS `documentator_entity_revision` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`entity_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_entity_snapshot`
--

CREATE TABLE IF NOT EXISTS `documentator_entity_snapshot` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documentator_entity_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `documentator_entity_id` (`documentator_entity_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_entity_type`
--

CREATE TABLE IF NOT EXISTS `documentator_entity_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `documentator_entity_type`
--

INSERT INTO `documentator_entity_type` (`id`, `code`, `name`, `description`) VALUES
  (1, 'blank_page', 'Blank Page', 'Start with a blank page.'),
  (2, 'file_list', 'File List', 'Upload, preview and share files with your team.'),
  (3, 'blog', 'Blog Post', 'Share news and announcements with your team.');

-- --------------------------------------------------------

--
-- Table structure for table `documentator_space`
--

CREATE TABLE IF NOT EXISTS `documentator_space` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `user_created_id` bigint(20) unsigned NOT NULL,
  `home_entity_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `home_page_id` (`home_entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_space_permission`
--

CREATE TABLE IF NOT EXISTS `documentator_space_permission` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `space_id` bigint(20) unsigned NOT NULL,
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `all_view_flag` tinyint(3) unsigned NOT NULL,
  `space_admin_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `space_id` (`space_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_space_permission_anonymous`
--

CREATE TABLE IF NOT EXISTS `documentator_space_permission_anonymous` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documentator_space_id` bigint(20) unsigned NOT NULL,
  `all_view_flag` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `documentator_space_id` (`documentator_space_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_user_entity_favourite`
--

CREATE TABLE IF NOT EXISTS `documentator_user_entity_favourite` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `entity_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `page_id` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentator_user_space_favourite`
--

CREATE TABLE IF NOT EXISTS `documentator_user_space_favourite` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `space_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `space_id` (`space_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_group`
--

CREATE TABLE IF NOT EXISTS `general_group` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `sys_product_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `sys_product_id` (`sys_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_group_data`
--

CREATE TABLE IF NOT EXISTS `general_group_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_log`
--

CREATE TABLE IF NOT EXISTS `general_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `message` text NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_mail_queue`
--

CREATE TABLE IF NOT EXISTS `general_mail_queue` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `from_address` varchar(50) NOT NULL,
  `to_address` varchar(50) NOT NULL,
  `reply_to_address` varchar(50) DEFAULT NULL,
  `subject` varchar(250) NOT NULL,
  `content` mediumtext NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_server_settings`
--

CREATE TABLE IF NOT EXISTS `general_server_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `maintenance_server_message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_task_queue`
--

CREATE TABLE IF NOT EXISTS `general_task_queue` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `data` mediumtext CHARACTER SET utf8 NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_user`
--

CREATE TABLE IF NOT EXISTS `general_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned DEFAULT NULL,
  `country_id` bigint(20) unsigned DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `first_name` varchar(200) DEFAULT NULL,
  `last_name` varchar(200) DEFAULT NULL,
  `avatar_picture` varchar(250) DEFAULT NULL,
  `issues_per_page` int(10) unsigned DEFAULT NULL,
  `issues_display_columns` text,
  `client_administrator_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `svn_administrator_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `super_user_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `notify_own_changes_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `customer_service_desk_flag` tinyint(4) NOT NULL DEFAULT '0',
  `date_created` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `help_customer`
--

CREATE TABLE IF NOT EXISTS `help_customer` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `help_organization_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `help_filter`
--

CREATE TABLE IF NOT EXISTS `help_filter` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) NOT NULL,
  `created_user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `definition` text NOT NULL,
  `columns` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `help_organization`
--

CREATE TABLE IF NOT EXISTS `help_organization` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `help_organization_user`
--

CREATE TABLE IF NOT EXISTS `help_organization_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `help_organization_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `help_reset_password`
--

CREATE TABLE IF NOT EXISTS `help_reset_password` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `token` varchar(50) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `help_sla`
--

CREATE TABLE IF NOT EXISTS `help_sla` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `start_condition` text NOT NULL,
  `stop_condition` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `help_sla_calendar`
--

CREATE TABLE IF NOT EXISTS `help_sla_calendar` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `sys_timezone_id` bigint(20) unsigned DEFAULT NULL,
  `default_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(150) NOT NULL,
  `description` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sys_timezone_id` (`sys_timezone_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `help_sla_calendar_data`
--

CREATE TABLE IF NOT EXISTS `help_sla_calendar_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `help_sla_calendar_id` bigint(20) unsigned NOT NULL,
  `day_number` int(10) unsigned NOT NULL,
  `time_from` varchar(8) DEFAULT NULL,
  `time_to` varchar(8) DEFAULT NULL,
  `not_working_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `help_calendar_id` (`help_sla_calendar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `help_sla_goal`
--

CREATE TABLE IF NOT EXISTS `help_sla_goal` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `help_sla_id` bigint(20) unsigned NOT NULL,
  `help_sla_calendar_id` bigint(20) unsigned NOT NULL,
  `definition` text NOT NULL,
  `definition_sql` text NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `help_sla_calendar_id` (`help_sla_calendar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `qn_notebook`
--

CREATE TABLE IF NOT EXISTS `qn_notebook` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `default_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `qn_notebook_note`
--

CREATE TABLE IF NOT EXISTS `qn_notebook_note` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `qn_notebook_id` bigint(20) unsigned NOT NULL,
  `summary` varchar(250) NOT NULL,
  `content` text,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `qn_notebook_id` (`qn_notebook_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `qn_notebook_note_tag`
--

CREATE TABLE IF NOT EXISTS `qn_notebook_note_tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `qn_notebook_note_id` bigint(20) unsigned NOT NULL,
  `qn_tag_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `qn_notebook_note_id` (`qn_notebook_note_id`,`qn_tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `qn_tag`
--

CREATE TABLE IF NOT EXISTS `qn_tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `svn_repository`
--

CREATE TABLE IF NOT EXISTS `svn_repository` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `user_created_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `code` varchar(50) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `user_created_id` (`user_created_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `svn_repository_user`
--

CREATE TABLE IF NOT EXISTS `svn_repository_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `svn_repository_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `date_created` bigint(20) unsigned NOT NULL,
  `password` varchar(250) DEFAULT NULL,
  `has_read` tinyint(1) DEFAULT NULL,
  `has_write` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `svn_repository_id` (`svn_repository_id`),
  KEY `user_id` (`user_id`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sys_country`
--

CREATE TABLE IF NOT EXISTS `sys_country` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=197 ;

--
-- Dumping data for table `sys_country`
--

INSERT INTO `sys_country` (`id`, `name`) VALUES
  (1, 'Afghanistan'),
  (2, 'Albania'),
  (3, 'Algeria'),
  (4, 'Andorra'),
  (5, 'Angola'),
  (6, 'Antigua & Deps'),
  (7, 'Argentina'),
  (8, 'Armenia'),
  (9, 'Australia'),
  (10, 'Austria'),
  (11, 'Azerbaijan'),
  (12, 'Bahamas'),
  (13, 'Bahrain'),
  (14, 'Bangladesh'),
  (15, 'Barbados'),
  (16, 'Belarus'),
  (17, 'Belgium'),
  (18, 'Belize'),
  (19, 'Benin'),
  (20, 'Bhutan'),
  (21, 'Bolivia'),
  (22, 'Bosnia Herzegovina'),
  (23, 'Botswana'),
  (24, 'Brazil'),
  (25, 'Brunei'),
  (26, 'Bulgaria'),
  (27, 'Burkina'),
  (28, 'Burundi'),
  (29, 'Cambodia'),
  (30, 'Cameroon'),
  (31, 'Canada'),
  (32, 'Cape Verde'),
  (33, 'Central African Rep'),
  (34, 'Chad'),
  (35, 'Chile'),
  (36, 'China'),
  (37, 'Colombia'),
  (38, 'Comoros'),
  (39, 'Congo'),
  (40, 'Congo {Democratic Rep}'),
  (41, 'Costa Rica'),
  (42, 'Croatia'),
  (43, 'Cuba'),
  (44, 'Cyprus'),
  (45, 'Czech Republic'),
  (46, 'Denmark'),
  (47, 'Djibouti'),
  (48, 'Dominica'),
  (49, 'Dominican Republic'),
  (50, 'East Timor'),
  (51, 'Ecuador'),
  (52, 'Egypt'),
  (53, 'El Salvador'),
  (54, 'Equatorial Guinea'),
  (55, 'Eritrea'),
  (56, 'Estonia'),
  (57, 'Ethiopia'),
  (58, 'Fiji'),
  (59, 'Finland'),
  (60, 'France'),
  (61, 'Gabon'),
  (62, 'Gambia'),
  (63, 'Georgia'),
  (64, 'Germany'),
  (65, 'Ghana'),
  (66, 'Greece'),
  (67, 'Grenada'),
  (68, 'Guatemala'),
  (69, 'Guinea'),
  (70, 'Guinea-Bissau'),
  (71, 'Guyana'),
  (72, 'Haiti'),
  (73, 'Honduras'),
  (74, 'Hungary'),
  (75, 'Iceland'),
  (76, 'India'),
  (77, 'Indonesia'),
  (78, 'Iran'),
  (79, 'Iraq'),
  (80, 'Ireland {Republic}'),
  (81, 'Israel'),
  (82, 'Italy'),
  (83, 'Ivory Coast'),
  (84, 'Jamaica'),
  (85, 'Japan'),
  (86, 'Jordan'),
  (87, 'Kazakhstan'),
  (88, 'Kenya'),
  (89, 'Kiribati'),
  (90, 'Korea North'),
  (91, 'Korea South'),
  (92, 'Kosovo'),
  (93, 'Kuwait'),
  (94, 'Kyrgyzstan'),
  (95, 'Laos'),
  (96, 'Latvia'),
  (97, 'Lebanon'),
  (98, 'Lesotho'),
  (99, 'Liberia'),
  (100, 'Libya'),
  (101, 'Liechtenstein'),
  (102, 'Lithuania'),
  (103, 'Luxembourg'),
  (104, 'Macedonia'),
  (105, 'Madagascar'),
  (106, 'Malawi'),
  (107, 'Malaysia'),
  (108, 'Maldives'),
  (109, 'Mali'),
  (110, 'Malta'),
  (111, 'Marshall Islands'),
  (112, 'Mauritania'),
  (113, 'Mauritius'),
  (114, 'Mexico'),
  (115, 'Micronesia'),
  (116, 'Moldova'),
  (117, 'Monaco'),
  (118, 'Mongolia'),
  (119, 'Montenegro'),
  (120, 'Morocco'),
  (121, 'Mozambique'),
  (122, 'Myanmar, {Burma}'),
  (123, 'Namibia'),
  (124, 'Nauru'),
  (125, 'Nepal'),
  (126, 'Netherlands'),
  (127, 'New Zealand'),
  (128, 'Nicaragua'),
  (129, 'Niger'),
  (130, 'Nigeria'),
  (131, 'Norway'),
  (132, 'Oman'),
  (133, 'Pakistan'),
  (134, 'Palau'),
  (135, 'Panama'),
  (136, 'Papua New Guinea'),
  (137, 'Paraguay'),
  (138, 'Peru'),
  (139, 'Philippines'),
  (140, 'Poland'),
  (141, 'Portugal'),
  (142, 'Qatar'),
  (143, 'Romania'),
  (144, 'Russian Federation'),
  (145, 'Rwanda'),
  (146, 'St Kitts & Nevis'),
  (147, 'St Lucia'),
  (148, 'Saint Vincent & the Grenadines'),
  (149, 'Samoa'),
  (150, 'San Marino'),
  (151, 'Sao Tome & Principe'),
  (152, 'Saudi Arabia'),
  (153, 'Senegal'),
  (154, 'Serbia'),
  (155, 'Seychelles'),
  (156, 'Sierra Leone'),
  (157, 'Singapore'),
  (158, 'Slovakia'),
  (159, 'Slovenia'),
  (160, 'Solomon Islands'),
  (161, 'Somalia'),
  (162, 'South Africa'),
  (163, 'South Sudan'),
  (164, 'Spain'),
  (165, 'Sri Lanka'),
  (166, 'Sudan'),
  (167, 'Suriname'),
  (168, 'Swaziland'),
  (169, 'Sweden'),
  (170, 'Switzerland'),
  (171, 'Syria'),
  (172, 'Taiwan'),
  (173, 'Tajikistan'),
  (174, 'Tanzania'),
  (175, 'Thailand'),
  (176, 'Togo'),
  (177, 'Tonga'),
  (178, 'Trinidad & Tobago'),
  (179, 'Tunisia'),
  (180, 'Turkey'),
  (181, 'Turkmenistan'),
  (182, 'Tuvalu'),
  (183, 'Uganda'),
  (184, 'Ukraine'),
  (185, 'United Arab Emirates'),
  (186, 'United Kingdom'),
  (187, 'United States'),
  (188, 'Uruguay'),
  (189, 'Uzbekistan'),
  (190, 'Vanuatu'),
  (191, 'Vatican City'),
  (192, 'Venezuela'),
  (193, 'Vietnam'),
  (194, 'Yemen'),
  (195, 'Zambia'),
  (196, 'Zimbabwe');

-- --------------------------------------------------------

--
-- Table structure for table `sys_product`
--

CREATE TABLE IF NOT EXISTS `sys_product` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `sys_product`
--

INSERT INTO `sys_product` (`id`, `name`, `description`) VALUES
  (1, 'Yongo', 'Issue & Project Tracking Software'),
  (2, 'SVN Hosting', 'SVN Hosting for your projects'),
  (3, 'Cheetah', 'Agile Module'),
  (4, 'Documentador', 'Content Creation, Collaboration & Knowledge Sharing for Teams'),
  (5, 'Events', 'Events'),
  (6, 'Helpdesk', ''),
  (7, 'Quick Notes', '');

-- --------------------------------------------------------

--
-- Table structure for table `yongo_condition`
--

CREATE TABLE IF NOT EXISTS `yongo_condition` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `yongo_condition`
--

INSERT INTO `yongo_condition` (`id`, `name`, `description`) VALUES
  (1, 'Only Assignee Condition', 'Condition to allow only the assignee to execute a transition. '),
  (2, 'Only Reporter Condition ', 'Condition to allow only the reporter to execute a transition. '),
  (3, 'Permission Condition ', 'Condition to allow only users with a certain permission to execute a transition. ');

-- --------------------------------------------------------

--
-- Table structure for table `yongo_event`
--

CREATE TABLE IF NOT EXISTS `yongo_event` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `code` tinyint(3) unsigned NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `system_flag` tinyint(3) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_field`
--

CREATE TABLE IF NOT EXISTS `yongo_field` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `sys_field_type_id` bigint(20) unsigned DEFAULT NULL,
  `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `system_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `all_issue_type_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `all_project_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `sys_field_type_id` (`sys_field_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_field_configuration`
--

CREATE TABLE IF NOT EXISTS `yongo_field_configuration` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_field_configuration_data`
--

CREATE TABLE IF NOT EXISTS `yongo_field_configuration_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field_configuration_id` bigint(20) unsigned NOT NULL,
  `field_id` bigint(20) unsigned NOT NULL,
  `visible_flag` tinyint(3) unsigned DEFAULT NULL,
  `required_flag` tinyint(3) unsigned NOT NULL,
  `field_description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `field_configuration_id` (`field_configuration_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_field_data`
--

CREATE TABLE IF NOT EXISTS `yongo_field_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint(20) unsigned NOT NULL,
  `value` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_field_issue_type_data`
--

CREATE TABLE IF NOT EXISTS `yongo_field_issue_type_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint(20) unsigned NOT NULL,
  `issue_type_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `field_id` (`field_id`),
  KEY `issue_type_id` (`issue_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_field_project_data`
--

CREATE TABLE IF NOT EXISTS `yongo_field_project_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint(20) unsigned NOT NULL,
  `project_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `field_id` (`field_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_field_type`
--

CREATE TABLE IF NOT EXISTS `yongo_field_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  `code` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `yongo_field_type`
--

INSERT INTO `yongo_field_type` (`id`, `name`, `description`, `code`) VALUES
  (1, 'Text Field (< 255 characters)', 'A basic single line text box custom field to allow simple text input.', 'small_text_field'),
  (2, 'Date Picker', 'A custom field that stores dates and uses a date picker to view them.', 'date_picker'),
  (3, 'Date Time', 'A custom field that stores dates with a time component', 'date_time'),
  (4, 'Free Text Field', 'A multiline text area custom field to allow input of longer text strings.', 'big_text_field'),
  (5, 'Number Field', 'A custom field that stores and validates numeric (floating point) input.', 'number'),
  (6, 'Select List (Single Choice)', 'A single select list with a configurable list of options', 'select_list_single'),
  (7, 'User Picker (Multiple User)', 'Choose multiple users from the user base.', 'user_picker_multiple_user');

-- --------------------------------------------------------

--
-- Table structure for table `yongo_filter`
--

CREATE TABLE IF NOT EXISTS `yongo_filter` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(250) NOT NULL,
  `definition` mediumtext NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_filter_favourite`
--

CREATE TABLE IF NOT EXISTS `yongo_filter_favourite` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `filter_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_filter_subscription`
--

CREATE TABLE IF NOT EXISTS `yongo_filter_subscription` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `filter_id` bigint(20) unsigned NOT NULL,
  `user_created_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `period` varchar(200) NOT NULL,
  `email_when_empty_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue`
--

CREATE TABLE IF NOT EXISTS `yongo_issue` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `priority_id` bigint(20) unsigned NOT NULL,
  `status_id` bigint(20) unsigned NOT NULL,
  `type_id` bigint(20) unsigned NOT NULL,
  `resolution_id` bigint(20) unsigned DEFAULT NULL,
  `user_reported_id` bigint(20) unsigned NOT NULL,
  `user_assigned_id` bigint(20) unsigned DEFAULT NULL,
  `project_id` bigint(20) unsigned NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `security_scheme_level_id` bigint(20) unsigned DEFAULT NULL,
  `nr` bigint(20) unsigned NOT NULL,
  `summary` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `environment` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `user_reported_ip` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `original_estimate` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `remaining_estimate` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `helpdesk_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_resolved` datetime DEFAULT NULL,
  `date_due` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_assigned_id` (`user_assigned_id`),
  KEY `project_id` (`project_id`),
  KEY `status_id` (`status_id`),
  KEY `user_reported_id` (`user_reported_id`),
  KEY `type_id` (`type_id`),
  KEY `resolution_id` (`resolution_id`),
  KEY `priority_id` (`priority_id`),
  KEY `parent_id` (`parent_id`),
  KEY `issue_security_scheme_level_id` (`security_scheme_level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_attachment`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_attachment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `size` bigint(20) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_id` (`issue_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_comment`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_comment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `content` mediumtext NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_id` (`issue_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_component`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_component` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint(20) unsigned NOT NULL,
  `project_component_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_component_id` (`project_component_id`),
  KEY `issue_id` (`issue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_custom_field_data`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_custom_field_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint(20) unsigned NOT NULL,
  `field_id` bigint(20) unsigned NOT NULL,
  `value` mediumtext NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_id` (`issue_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_history`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint(20) unsigned NOT NULL,
  `by_user_id` bigint(20) unsigned NOT NULL,
  `field` varchar(20) NOT NULL,
  `old_value` mediumtext,
  `new_value` mediumtext,
  `old_value_id` varchar(250) DEFAULT NULL,
  `new_value_id` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_id` (`issue_id`),
  KEY `by_user_id` (`by_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_link`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_link` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_issue_id` bigint(20) unsigned NOT NULL,
  `issue_link_type_id` bigint(20) unsigned NOT NULL,
  `link_type` varchar(7) NOT NULL,
  `child_issue_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_issue_id` (`parent_issue_id`),
  KEY `sys_issue_link_type_id` (`issue_link_type_id`),
  KEY `child_issue_id` (`child_issue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_link_type`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_link_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `outward_description` varchar(200) NOT NULL,
  `inward_description` varchar(200) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_priority`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_priority` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `icon_name` varchar(50) NOT NULL,
  `color` varchar(7) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_resolution`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_resolution` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_security_scheme`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_security_scheme` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_security_scheme_level`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_security_scheme_level` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_security_scheme_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  `default_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_security_scheme_id` (`issue_security_scheme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_security_scheme_level_data`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_security_scheme_level_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_security_scheme_level_id` bigint(20) unsigned NOT NULL,
  `permission_role_id` bigint(20) unsigned DEFAULT NULL,
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `current_assignee` tinyint(3) unsigned DEFAULT NULL,
  `reporter` tinyint(3) unsigned DEFAULT NULL,
  `project_lead` tinyint(3) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_security_scheme_level_id` (`issue_security_scheme_level_id`),
  KEY `permission_role_id` (`permission_role_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_sla`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_sla` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `yongo_issue_id` bigint(20) unsigned NOT NULL,
  `help_sla_id` bigint(20) unsigned NOT NULL,
  `help_sla_goal_id` bigint(20) unsigned DEFAULT NULL,
  `started_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `stopped_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `started_date` datetime DEFAULT NULL,
  `stopped_date` datetime DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `yongo_issue_id` (`yongo_issue_id`),
  KEY `help_sla_id` (`help_sla_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_status`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_status` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_type`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `sub_task_flag` int(10) unsigned DEFAULT NULL,
  `icon_name` varchar(50) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_type_field_configuration`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_type_field_configuration` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_type_field_configuration_data`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_type_field_configuration_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_type_field_configuration_id` bigint(20) unsigned NOT NULL,
  `issue_type_id` bigint(20) unsigned NOT NULL,
  `field_configuration_id` bigint(20) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_type_id` (`issue_type_id`),
  KEY `field_configuration_id` (`field_configuration_id`),
  KEY `issue_type_field_configuration_id` (`issue_type_field_configuration_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_type_scheme`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_type_scheme` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  `type` varchar(20) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_type_scheme_data`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_type_scheme_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_type_scheme_id` bigint(20) unsigned NOT NULL,
  `issue_type_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_type_scheme_id` (`issue_type_scheme_id`),
  KEY `issue_type_id` (`issue_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_type_screen_scheme`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_type_screen_scheme` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_type_screen_scheme_data`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_type_screen_scheme_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_type_screen_scheme_id` bigint(20) unsigned NOT NULL,
  `issue_type_id` bigint(20) unsigned NOT NULL,
  `screen_scheme_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`issue_type_screen_scheme_id`),
  KEY `issue_type_id` (`issue_type_id`),
  KEY `screen_scheme_id` (`screen_scheme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_version`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_version` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint(20) unsigned NOT NULL,
  `project_version_id` bigint(20) unsigned NOT NULL,
  `affected_targeted_flag` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_version_id` (`project_version_id`),
  KEY `issue_id` (`issue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_watch`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_watch` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `yongo_issue_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `yongo_issue_id` (`yongo_issue_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_issue_work_log`
--

CREATE TABLE IF NOT EXISTS `yongo_issue_work_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `time_spent` varchar(20) NOT NULL,
  `comment` mediumtext NOT NULL,
  `edited_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_started` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_id` (`issue_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_notification_scheme`
--

CREATE TABLE IF NOT EXISTS `yongo_notification_scheme` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_notification_scheme_data`
--

CREATE TABLE IF NOT EXISTS `yongo_notification_scheme_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `notification_scheme_id` bigint(20) unsigned NOT NULL,
  `event_id` bigint(20) unsigned NOT NULL,
  `permission_role_id` bigint(20) unsigned DEFAULT NULL,
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `current_assignee` tinyint(3) unsigned DEFAULT NULL,
  `reporter` tinyint(3) unsigned DEFAULT NULL,
  `current_user` tinyint(3) unsigned DEFAULT NULL,
  `project_lead` tinyint(3) unsigned DEFAULT NULL,
  `component_lead` tinyint(3) unsigned DEFAULT NULL,
  `all_watchers` tinyint(3) unsigned DEFAULT NULL,
  `user_picker_multiple_selection` bigint(20) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_scheme_id` (`notification_scheme_id`),
  KEY `permission_role_id` (`permission_role_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`),
  KEY `current_assignee` (`current_assignee`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_operation`
--

CREATE TABLE IF NOT EXISTS `yongo_operation` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `yongo_operation`
--

INSERT INTO `yongo_operation` (`id`, `name`) VALUES
  (1, 'create'),
  (2, 'edit'),
  (3, 'view');

-- --------------------------------------------------------

--
-- Table structure for table `yongo_permission`
--

CREATE TABLE IF NOT EXISTS `yongo_permission` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sys_permission_category_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sys_permission_category_id` (`sys_permission_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=29 ;

--
-- Dumping data for table `yongo_permission`
--

INSERT INTO `yongo_permission` (`id`, `sys_permission_category_id`, `name`, `description`) VALUES
  (1, 1, 'Administer Projects', 'Ability to administer a project in Ubirimi.'),
  (2, 1, 'Browse Projects', 'Ability to browse projects and the issues within them.'),
  (3, 2, 'Create Issues', 'Ability to create issues.'),
  (4, 2, 'Edit Issues', 'Ability to edit issues.'),
  (5, 2, 'Assign Issues', 'Ability to assign issues to other people.'),
  (6, 2, 'Assignable User', 'Users with this permission may be assigned to issues.'),
  (7, 2, 'Resolve Issues', 'Ability to resolve and reopen issues. This includes the ability to set a fix version.'),
  (8, 2, 'Close Issues', 'Ability to close issues. Often useful where your developers resolve issues, and a QA department closes them.'),
  (9, 2, 'Modify Reporter', 'Ability to modify the reporter when creating or editing an issue.'),
  (10, 2, 'Delete Issues', 'Ability to delete issues.'),
  (11, 3, 'Add Comments', 'Ability to comment on issues.'),
  (12, 3, 'Edit All Comments', 'Ability to edit all comments made on issues.'),
  (13, 3, 'Edit Own Comments', 'Ability to edit own comments made on issues.'),
  (14, 3, 'Delete All Comments', 'Ability to delete all comments made on issues.'),
  (15, 3, 'Delete Own Comments', 'Ability to delete own comments made on issues.'),
  (16, 4, 'Create Attachments', 'Users with this permission may create attachments.'),
  (17, 4, 'Delete All Attachments', 'Users with this permission may delete all attachments.'),
  (18, 4, 'Delete Own Attachments', 'Users with this permission may delete own attachments.'),
  (19, 2, 'Set Issue Security', 'Ability to set the level of security on an issue so that only people in that security level can see the issue.'),
  (20, 2, 'Link Issues', 'Ability to link issues together and create linked issues. Only useful if issue linking is turned on.'),
  (21, 2, 'Move Issues', 'Ability to move issues between projects or between workflows of the same project (if applicable). Note the user can only move issues to a project he or she has the create permission for.'),
  (22, 5, 'Work On Issues', 'Ability to log work done against an issue. Only useful if Time Tracking is turned on.'),
  (23, 5, 'Edit Own Worklogs', 'Ability to edit own worklogs made on issues.'),
  (24, 5, 'Edit All Worklogs', 'Ability to edit all worklogs made on issues.'),
  (25, 5, 'Delete Own Worklogs', 'Ability to delete own worklogs made on issues.'),
  (26, 5, 'Delete All Worklogs', 'Ability to delete all worklogs made on issues.'),
  (27, 6, 'View Voters and Watchers', 'Ability to view the voters and watchers of an issue.'),
  (28, 6, 'Manage Watchers', 'Ability to manage the watchers of an issue.');

-- --------------------------------------------------------

--
-- Table structure for table `yongo_permission_category`
--

CREATE TABLE IF NOT EXISTS `yongo_permission_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `yongo_permission_category`
--

INSERT INTO `yongo_permission_category` (`id`, `name`) VALUES
  (1, 'Project Permissions'),
  (2, 'Issue Permissions'),
  (3, 'Comments Permissions'),
  (4, 'Attachments Permissions'),
  (5, 'Time Tracking Permissions'),
  (6, 'Voters & Watchers Permissions');

-- --------------------------------------------------------

--
-- Table structure for table `yongo_permission_global`
--

CREATE TABLE IF NOT EXISTS `yongo_permission_global` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sys_product_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sys_product_id` (`sys_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `yongo_permission_global`
--

INSERT INTO `yongo_permission_global` (`id`, `sys_product_id`, `name`, `description`) VALUES
  (1, 1, 'Yongo System Administrators', 'Permission to perform all Yongo administration functions.'),
  (2, 1, 'Yongo Administrators', 'Ability to perform most administration functions (excluding Import & Export, etc.). '),
  (3, 1, 'Yongo Users', 'Ability to log in to Yongo. They are a ''user''. Any new users created will automatically join these groups.'),
  (4, 1, 'Bulk Change', 'Ability to modify a collection of issues at once. For example, resolve multiple issues in one step.'),
  (5, 4, 'Documentador Administrator', 'Can administer the application but is disallowed from operations that may compromise system security.'),
  (6, 4, 'Documentador System Administrator', 'Has complete control and access to all administrative functions.'),
  (7, 4, 'Create Space', 'Able to add spaces to the site.');

-- --------------------------------------------------------

--
-- Table structure for table `yongo_permission_global_data`
--

CREATE TABLE IF NOT EXISTS `yongo_permission_global_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `sys_permission_global_id` bigint(20) unsigned NOT NULL,
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `sys_permission_global_id` (`sys_permission_global_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_permission_role`
--

CREATE TABLE IF NOT EXISTS `yongo_permission_role` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_permission_role_data`
--

CREATE TABLE IF NOT EXISTS `yongo_permission_role_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `permission_role_id` bigint(20) unsigned NOT NULL,
  `default_group_id` bigint(20) unsigned DEFAULT NULL,
  `default_user_id` bigint(20) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`default_user_id`),
  KEY `user_group_id` (`default_group_id`),
  KEY `permission_role_id` (`permission_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_permission_scheme`
--

CREATE TABLE IF NOT EXISTS `yongo_permission_scheme` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_permission_scheme_data`
--

CREATE TABLE IF NOT EXISTS `yongo_permission_scheme_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `permission_scheme_id` bigint(20) unsigned NOT NULL,
  `sys_permission_id` bigint(20) unsigned NOT NULL,
  `permission_role_id` bigint(20) unsigned DEFAULT NULL,
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `current_assignee` tinyint(3) unsigned DEFAULT NULL,
  `reporter` tinyint(3) unsigned DEFAULT NULL,
  `project_lead` tinyint(3) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `permission_scheme_id` (`permission_scheme_id`),
  KEY `permission_id` (`sys_permission_id`),
  KEY `permission_role_id` (`permission_role_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_project`
--

CREATE TABLE IF NOT EXISTS `yongo_project` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` bigint(20) unsigned DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `issue_type_scheme_id` bigint(20) unsigned NOT NULL,
  `issue_type_screen_scheme_id` bigint(20) unsigned NOT NULL,
  `issue_type_field_configuration_id` bigint(20) unsigned NOT NULL,
  `workflow_scheme_id` bigint(20) unsigned NOT NULL,
  `permission_scheme_id` bigint(20) unsigned NOT NULL,
  `notification_scheme_id` bigint(20) unsigned NOT NULL,
  `issue_security_scheme_id` bigint(20) unsigned DEFAULT NULL,
  `project_category_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `code` varchar(5) NOT NULL,
  `description` varchar(250) NOT NULL,
  `help_desk_enabled_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `issue_number` bigint(20) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `owner_id` (`lead_id`),
  KEY `issue_type_scheme_id` (`issue_type_scheme_id`),
  KEY `workflow_scheme_id` (`workflow_scheme_id`),
  KEY `issue_type_screen_scheme_id` (`issue_type_screen_scheme_id`),
  KEY `issue_type_field_configuration_id` (`issue_type_field_configuration_id`),
  KEY `permission_scheme_id` (`permission_scheme_id`),
  KEY `notification_scheme_id` (`notification_scheme_id`),
  KEY `issue_security_scheme_id` (`issue_security_scheme_id`),
  KEY `project_category_id` (`project_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_project_category`
--

CREATE TABLE IF NOT EXISTS `yongo_project_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_project_component`
--

CREATE TABLE IF NOT EXISTS `yongo_project_component` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `leader_id` bigint(20) unsigned DEFAULT NULL,
  `project_id` bigint(20) unsigned NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` mediumtext NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`project_id`),
  KEY `leader_id` (`leader_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_project_role_data`
--

CREATE TABLE IF NOT EXISTS `yongo_project_role_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `permission_role_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`permission_role_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_project_version`
--

CREATE TABLE IF NOT EXISTS `yongo_project_version` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_screen`
--

CREATE TABLE IF NOT EXISTS `yongo_screen` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_screen_data`
--

CREATE TABLE IF NOT EXISTS `yongo_screen_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `screen_id` bigint(20) unsigned NOT NULL,
  `field_id` bigint(20) unsigned NOT NULL,
  `position` int(10) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_workflow_screen_id` (`screen_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_screen_scheme`
--

CREATE TABLE IF NOT EXISTS `yongo_screen_scheme` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_screen_scheme_data`
--

CREATE TABLE IF NOT EXISTS `yongo_screen_scheme_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `screen_scheme_id` bigint(20) unsigned NOT NULL,
  `sys_operation_id` bigint(20) unsigned NOT NULL,
  `screen_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `issue_type_scheme_id` bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` mediumtext NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `issue_type_scheme_id` (`issue_type_scheme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_condition_data`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_condition_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `workflow_data_id` bigint(20) unsigned NOT NULL,
  `definition_data` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_data_id` (`workflow_data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_data`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `workflow_id` bigint(20) unsigned NOT NULL,
  `screen_id` bigint(20) unsigned DEFAULT NULL,
  `workflow_step_id_from` bigint(20) unsigned DEFAULT NULL,
  `workflow_step_id_to` bigint(20) unsigned DEFAULT NULL,
  `transition_name` varchar(50) DEFAULT NULL,
  `transition_description` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`workflow_id`),
  KEY `issue_status_from_id` (`workflow_step_id_from`,`workflow_step_id_to`),
  KEY `screen_id` (`screen_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_position`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_position` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `workflow_id` bigint(20) unsigned NOT NULL,
  `workflow_step_id` bigint(20) unsigned NOT NULL,
  `top_position` int(10) unsigned NOT NULL,
  `left_position` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_workflow_id` (`workflow_id`,`workflow_step_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_post_function`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_post_function` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `description` varchar(250) NOT NULL,
  `user_addable_flag` tinyint(3) unsigned NOT NULL,
  `user_editable_flag` tinyint(3) unsigned NOT NULL,
  `user_deletable_flag` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `yongo_workflow_post_function`
--

INSERT INTO `yongo_workflow_post_function` (`id`, `name`, `description`, `user_addable_flag`, `user_editable_flag`, `user_deletable_flag`) VALUES
  (1, 'Update Issue Field', 'Updates a simple issue field to a given value.', 1, 1, 1),
  (2, 'Set issue status to the linked status of the destination workflow step', 'Set issue status to the linked status of the destination workflow step. ', 0, 0, 0),
  (3, 'Update change history for the issue', 'Update change history for the issue', 0, 0, 0),
  (4, 'Create issue', 'Create the issue originally', 0, 0, 0),
  (5, 'Fire an event', 'Fire an event', 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_post_function_data`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_post_function_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `workflow_data_id` bigint(20) unsigned NOT NULL,
  `sys_workflow_post_function_id` bigint(20) unsigned NOT NULL,
  `definition_data` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_workflow_data_id` (`workflow_data_id`,`sys_workflow_post_function_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_scheme`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_scheme` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_scheme_data`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_scheme_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `workflow_scheme_id` bigint(20) unsigned NOT NULL,
  `workflow_id` bigint(20) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_scheme_id` (`workflow_scheme_id`),
  KEY `workflow_id` (`workflow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_step`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_step` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `workflow_id` bigint(20) unsigned NOT NULL,
  `linked_issue_status_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `initial_step_flag` tinyint(3) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_id` (`workflow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_step_property`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_step_property` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `yongo_workflow_step_property`
--

INSERT INTO `yongo_workflow_step_property` (`id`, `name`) VALUES
  (1, 'ubirimi.issue.editable');

-- --------------------------------------------------------

--
-- Table structure for table `yongo_workflow_step_property_data`
--

CREATE TABLE IF NOT EXISTS `yongo_workflow_step_property_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `workflow_step_id` bigint(20) unsigned NOT NULL,
  `sys_workflow_step_property_id` bigint(20) unsigned NOT NULL,
  `value` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_step_id` (`workflow_step_id`),
  KEY `sys_workflow_step_property_id` (`sys_workflow_step_property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

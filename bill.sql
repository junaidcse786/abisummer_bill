-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 27, 2018 at 05:40 PM
-- Server version: 10.1.25-MariaDB
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bill`
--

-- --------------------------------------------------------

--
-- Table structure for table `bill_bookings`
--

CREATE TABLE `bill_bookings` (
  `bookings_ID` int(11) NOT NULL,
  `bookings_code` varchar(35) CHARACTER SET utf8 NOT NULL,
  `bookings_num_traveler` int(11) NOT NULL,
  `locations_name` varchar(500) CHARACTER SET utf8 NOT NULL,
  `hotels_name` varchar(500) CHARACTER SET utf8 NOT NULL,
  `bookings_check_in_date` date NOT NULL,
  `bookings_check_out_date` date NOT NULL,
  `bookings_summary` text CHARACTER SET utf8 NOT NULL,
  `bookings_notes` text NOT NULL,
  `bookings_status` tinyint(1) NOT NULL DEFAULT '1',
  `bookings_creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bill_bookings`
--

INSERT INTO `bill_bookings` (`bookings_ID`, `bookings_code`, `bookings_num_traveler`, `locations_name`, `hotels_name`, `bookings_check_in_date`, `bookings_check_out_date`, `bookings_summary`, `bookings_notes`, `bookings_status`, `bookings_creation_time`) VALUES
(1, 'JER4T-KT8UP-XHPZ2-WX9DW', 10, 'Calella', 'Hotel Calella Special Entry', '2018-03-27', '2018-04-01', '{\"journey_cost\":\"1005\",\"journey_title\":\"Busreise\",\"discount_applied\":\"5\",\"without_discount\":\"5731.6928900982\",\"meals_cost\":\"1425\",\"rooms_cost\":\"1092.5\",\"rooms_cost_details\":{\"3er Zimmer\":{\"costs_this_room_the_whole_time\":\"750\",\"rooms_persons_to_fit\":\"9\",\"rooms_ordered\":\"3\"},\"EinzelzimmerÃ¼\":{\"costs_this_room_the_whole_time\":\"400\",\"rooms_persons_to_fit\":\"1\",\"rooms_ordered\":\"1\"}},\"meals_cost_details\":{\"Abendessen\":{\"costs_this_meal_the_whole_time\":\"1500\",\"meals_ordered\":\"10\"}},\"indiv_cost_array\":{\"3er Zimmer + Abendessen\":\"527.41104735991\",\"EinzelzimmerÃ¼ + Abendessen\":\"828.24438069325\"},\"office_profit\":\"1000\",\"promoter_provision\":\"724.74269490122\",\"MwSt\":\"327.70111203123\"}', 'just a school', 1, '2018-02-08 11:57:18'),
(2, 'QCP5V-A2BUZ-ZZPRH-528T6', 10, 'Calella', 'Hotel Calella Special Entry (Abisummer)', '2018-02-27', '2018-03-04', '{\"journey_title\":\"Busreise\",\"journey_cost\":\"200\",\"meals_cost\":\"1625\",\"rooms_cost\":\"4000\",\"rooms_cost_details\":{\"EinzelzimmerÃ¼\":{\"costs_this_room_the_whole_time\":\"4000\",\"rooms_persons_to_fit\":\"10\",\"rooms_ordered\":\"10\"}},\"meals_cost_details\":{\"FrÃ¼hstÃ¼ck\":{\"costs_this_meal_the_whole_time\":\"375\",\"meals_ordered\":\"5\"},\"All inkl.\":{\"costs_this_meal_the_whole_time\":\"1250\",\"meals_ordered\":\"5\"}},\"indiv_cost_array\":{\"EinzelzimmerÃ¼ + FrÃ¼hstÃ¼ck\":\"742.38288181711\",\"EinzelzimmerÃ¼ + All inkl.\":\"917.38288181711\"},\"office_profit\":\"1000\",\"promoter_provision\":\"1078.8477463622\",\"MwSt\":\"394.98107180883\"}', '', 1, '2018-02-27 15:56:46'),
(3, 'T9OKN-2SPWY-9FCGM-Y7BRU', 5, 'Calella', 'Hotel Calella Special Entry (Abisummer)', '2018-02-27', '2018-03-04', '{\"journey_title\":\"Busreise\",\"abfahrsort\":\"MÃ¼nchen\",\"journey_cost\":\"200\",\"meals_cost\":\"0\",\"rooms_cost\":\"2000\",\"rooms_cost_details\":{\"EinzelzimmerÃ¼\":{\"costs_this_room_the_whole_time\":\"2000\",\"rooms_persons_to_fit\":\"5\",\"rooms_ordered\":\"5\"}},\"meals_cost_details\":[],\"indiv_cost_array\":{\"EinzelzimmerÃ¼\":\"661.30367916716\"},\"office_profit\":\"500\",\"promoter_provision\":\"429.84739145865\",\"MwSt\":\"176.67100437714\"}', '', 1, '2018-02-27 15:59:29');

-- --------------------------------------------------------

--
-- Table structure for table `bill_config`
--

CREATE TABLE `bill_config` (
  `config_id` int(10) NOT NULL,
  `config_name` varchar(30) NOT NULL,
  `config_value` mediumtext CHARACTER SET utf8 NOT NULL,
  `config_note` varchar(500) NOT NULL,
  `config_is_json` tinyint(2) NOT NULL,
  `config_base_currency` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bill_config`
--

INSERT INTO `bill_config` (`config_id`, `config_name`, `config_value`, `config_note`, `config_is_json`, `config_base_currency`) VALUES
(1, 'SITE_NAME', 'Abisummer Billing', 'Site Name ', 0, NULL),
(2, 'SITE_URL', 'http://localhost/abisummer_bill/', 'Site url', 0, NULL),
(3, 'DB_SUFFIX', 'bill_', 'bill_', 1, NULL),
(7, 'SITE_EMAIL', 'info@abisummer.de', 'site email', 0, NULL),
(23, 'PER_PAGE_MSG', '50', 'Messages shown per page', 0, NULL),
(20, 'FOOTER_TEXT', 'Â© 2016. Abisummer. All Rights Reserved', 'FOOTER_TEXT', 0, NULL),
(22, 'SITE_URL_ADMIN', 'http://localhost/abisummer_bill/admin/', 'URL FOR ADMIN PANEL', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bill_draft_message`
--

CREATE TABLE `bill_draft_message` (
  `dm_id` int(255) NOT NULL,
  `message_text` longtext NOT NULL,
  `message_subject` text NOT NULL,
  `message_receiver` text NOT NULL,
  `user_id` int(255) NOT NULL,
  `dm_created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dm_key` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bill_early_bird`
--

CREATE TABLE `bill_early_bird` (
  `eb_ID` int(255) NOT NULL,
  `hotels_ID` int(255) NOT NULL,
  `eb_discount` float NOT NULL,
  `eb_discount_date_from` date DEFAULT NULL,
  `eb_discount_date_to` date DEFAULT NULL,
  `eb_stay_from` date NOT NULL,
  `eb_stay_to` date NOT NULL,
  `eb_status` tinyint(1) NOT NULL DEFAULT '1',
  `eb_notes` text NOT NULL,
  `eb_creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `eb_update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bill_early_bird`
--

INSERT INTO `bill_early_bird` (`eb_ID`, `hotels_ID`, `eb_discount`, `eb_discount_date_from`, `eb_discount_date_to`, `eb_stay_from`, `eb_stay_to`, `eb_status`, `eb_notes`, `eb_creation_time`, `eb_update_time`) VALUES
(1, 3, 10, '2018-01-01', '2018-02-27', '2018-05-09', '2018-05-31', 1, '', '2018-01-12 12:44:31', '2018-02-05 09:26:05'),
(2, 3, 5, '2018-02-01', '2018-02-28', '2018-03-01', '2018-03-31', 1, '', '2018-01-12 12:44:59', '2018-02-05 09:31:09'),
(3, 3, 15, '2018-01-24', '2018-03-01', '2018-05-17', '2018-06-14', 1, '', '2018-01-12 14:15:40', '2018-02-05 09:26:37'),
(4, 3, 80, '2018-02-15', '2018-03-31', '2018-05-09', '2018-08-31', 1, '', '2018-02-15 16:04:27', '2018-02-15 16:04:53');

-- --------------------------------------------------------

--
-- Table structure for table `bill_hotels`
--

CREATE TABLE `bill_hotels` (
  `hotels_ID` int(255) NOT NULL,
  `locations_ID` int(255) NOT NULL,
  `hotels_name` text NOT NULL,
  `hotels_star` tinyint(1) NOT NULL,
  `hotels_status` tinyint(1) NOT NULL DEFAULT '1',
  `hotels_offer_from` varchar(500) NOT NULL,
  `hotels_notes` text NOT NULL,
  `hotels_creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hotels_update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bill_hotels`
--

INSERT INTO `bill_hotels` (`hotels_ID`, `locations_ID`, `hotels_name`, `hotels_star`, `hotels_status`, `hotels_offer_from`, `hotels_notes`, `hotels_creation_time`, `hotels_update_time`) VALUES
(3, 1, 'Hotel Calella Special Entry', 2, 1, 'Abisummer', 'good enough', '2017-12-20 13:54:14', '2018-02-26 15:12:35'),
(5, 1, 'Olympic Hotel', 3, 1, 'Abisummer', '', '2018-01-04 19:55:26', '2018-02-26 15:12:06'),
(6, 1, 'Hotel Olympic Star', 5, 1, 'Abireise', '', '2018-02-26 16:06:02', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `bill_journey`
--

CREATE TABLE `bill_journey` (
  `journey_ID` int(255) NOT NULL,
  `locations_ID` int(255) NOT NULL,
  `journey_title` text NOT NULL,
  `journey_price` float NOT NULL,
  `journey_notes` text NOT NULL,
  `journey_status` tinyint(1) NOT NULL DEFAULT '1',
  `journey_creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `journey_update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bill_journey`
--

INSERT INTO `bill_journey` (`journey_ID`, `locations_ID`, `journey_title`, `journey_price`, `journey_notes`, `journey_status`, `journey_creation_time`, `journey_update_time`) VALUES
(1, 1, 'Busreise', 100.5, 'possible pick up', 1, '2017-12-19 09:46:16', '2018-02-27 14:25:01'),
(2, 1, 'Flugreise', 200, '', 1, '2017-12-19 10:18:26', '2017-12-19 10:26:45'),
(3, 2, 'Busreise', 100, '', 1, '2017-12-19 10:26:23', '2017-12-19 10:26:45'),
(4, 2, 'Flugreise', 200, '', 1, '2017-12-19 10:26:29', '2017-12-19 10:26:45'),
(5, 1, 'Eigenanreise', 0, '', 1, '2018-01-17 09:16:47', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `bill_journey_location`
--

CREATE TABLE `bill_journey_location` (
  `jl_ID` int(11) NOT NULL,
  `journey_ID` int(11) NOT NULL,
  `jl_city_location` varchar(500) CHARACTER SET utf8 NOT NULL,
  `jl_price` float NOT NULL,
  `jl_notes` text NOT NULL,
  `jl_status` int(11) NOT NULL,
  `jl_creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `jl_update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bill_journey_location`
--

INSERT INTO `bill_journey_location` (`jl_ID`, `journey_ID`, `jl_city_location`, `jl_price`, `jl_notes`, `jl_status`, `jl_creation_time`, `jl_update_time`) VALUES
(4, 3, 'MÃ¼nchen', 150, '', 1, '2018-02-27 12:04:51', '2018-02-27 12:17:34'),
(5, 3, 'Berlin', 150, '', 1, '2018-02-27 12:17:49', '0000-00-00 00:00:00'),
(6, 1, 'Berlin', 150, '', 1, '2018-02-27 14:25:21', '2018-02-27 14:26:15'),
(7, 1, 'MÃ¼nchen', 200, '', 1, '2018-02-27 14:25:25', '2018-02-27 14:26:15');

-- --------------------------------------------------------

--
-- Table structure for table `bill_locations`
--

CREATE TABLE `bill_locations` (
  `locations_ID` int(255) NOT NULL,
  `locations_name` text NOT NULL,
  `locations_profit` varchar(500) NOT NULL,
  `locations_status` tinyint(1) NOT NULL DEFAULT '1',
  `locations_creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `locations_update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bill_locations`
--

INSERT INTO `bill_locations` (`locations_ID`, `locations_name`, `locations_profit`, `locations_status`, `locations_creation_time`, `locations_update_time`) VALUES
(1, 'Calella', '100', 1, '2017-12-19 09:44:09', '2017-12-21 17:13:28'),
(2, 'Lloret', '', 1, '2017-12-19 09:44:12', '2017-12-21 16:23:36'),
(3, 'Goldstrand', '', 1, '2017-12-19 09:44:16', '2017-12-21 16:23:43'),
(4, 'Mallorca', '', 1, '2017-12-19 09:44:23', '2017-12-21 16:23:49');

-- --------------------------------------------------------

--
-- Table structure for table `bill_locations_costs`
--

CREATE TABLE `bill_locations_costs` (
  `lc_ID` int(255) NOT NULL,
  `locations_ID` int(255) NOT NULL,
  `lc_title` varchar(500) NOT NULL,
  `lc_costs` varchar(100) NOT NULL,
  `lc_costs_date_from` date DEFAULT NULL,
  `lc_costs_date_to` date DEFAULT NULL,
  `lc_notes` text NOT NULL,
  `lc_status` tinyint(1) NOT NULL DEFAULT '1',
  `lc_creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lc_update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bill_locations_costs`
--

INSERT INTO `bill_locations_costs` (`lc_ID`, `locations_ID`, `lc_title`, `lc_costs`, `lc_costs_date_from`, `lc_costs_date_to`, `lc_notes`, `lc_status`, `lc_creation_time`, `lc_update_time`) VALUES
(8, 1, 'MwSt', '19%', '0000-00-00', '0000-00-00', '', 1, '2018-01-12 09:40:55', '0000-00-00 00:00:00'),
(9, 1, 'Promoter Provision', '13%', '0000-00-00', '0000-00-00', '', 1, '2018-01-12 09:41:00', '2018-01-15 16:34:48'),
(10, 1, 'Office Profit', '100â‚¬', '0000-00-00', '0000-00-00', '', 1, '2018-01-26 13:07:15', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `bill_logo`
--

CREATE TABLE `bill_logo` (
  `banner_image` mediumtext NOT NULL,
  `temp` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bill_logo`
--

INSERT INTO `bill_logo` (`banner_image`, `temp`) VALUES
('rsz_1abisummer-logo-internet.png', '');

-- --------------------------------------------------------

--
-- Table structure for table `bill_meals`
--

CREATE TABLE `bill_meals` (
  `meals_ID` int(255) NOT NULL,
  `meals_title` text NOT NULL,
  `meals_notes` text NOT NULL,
  `meals_status` tinyint(1) NOT NULL DEFAULT '1',
  `meals_creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `meals_update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bill_meals`
--

INSERT INTO `bill_meals` (`meals_ID`, `meals_title`, `meals_notes`, `meals_status`, `meals_creation_time`, `meals_update_time`) VALUES
(1, 'All inkl.', '', 1, '2017-12-19 11:00:21', '2017-12-19 11:00:43'),
(2, 'FrÃ¼hstÃ¼ck', '', 1, '2017-12-19 11:00:53', '0000-00-00 00:00:00'),
(3, 'Abendessen', '', 1, '2017-12-20 15:07:42', '0000-00-00 00:00:00'),
(4, 'Mittagsessen', '', 1, '2017-12-20 15:07:42', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `bill_meals_price`
--

CREATE TABLE `bill_meals_price` (
  `mp_ID` int(255) NOT NULL,
  `hotels_ID` int(255) NOT NULL,
  `meals_ID` int(255) NOT NULL,
  `mp_price` float NOT NULL,
  `mp_price_date_from` date DEFAULT NULL,
  `mp_price_date_to` date DEFAULT NULL,
  `mp_notes` text NOT NULL,
  `mp_status` tinyint(1) NOT NULL DEFAULT '1',
  `mp_creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mp_update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bill_meals_price`
--

INSERT INTO `bill_meals_price` (`mp_ID`, `hotels_ID`, `meals_ID`, `mp_price`, `mp_price_date_from`, `mp_price_date_to`, `mp_notes`, `mp_status`, `mp_creation_time`, `mp_update_time`) VALUES
(23, 3, 1, 50, '0000-00-00', '0000-00-00', '', 1, '2018-01-04 19:25:16', '0000-00-00 00:00:00'),
(24, 3, 2, 15, '0000-00-00', '0000-00-00', '', 1, '2018-01-04 19:25:19', '0000-00-00 00:00:00'),
(26, 3, 4, 30, '0000-00-00', '0000-00-00', '', 1, '2018-01-04 19:25:28', '2018-01-04 19:25:38'),
(29, 3, 3, 30, '0000-00-00', '0000-00-00', '', 1, '2018-01-09 13:49:45', '2018-01-19 10:24:44');

-- --------------------------------------------------------

--
-- Table structure for table `bill_message`
--

CREATE TABLE `bill_message` (
  `message_id` int(255) NOT NULL,
  `message_sender` int(255) NOT NULL,
  `message_receiver` int(255) NOT NULL,
  `message_seen` int(1) NOT NULL,
  `message_report` int(1) NOT NULL,
  `message_created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `message_text` longtext NOT NULL,
  `message_subject` varchar(500) NOT NULL,
  `sender_delete` int(1) NOT NULL,
  `receiver_delete` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bill_module`
--

CREATE TABLE `bill_module` (
  `module_id` smallint(6) NOT NULL,
  `module_name` varchar(50) NOT NULL,
  `module_title` varchar(100) NOT NULL,
  `module_key` varchar(20) NOT NULL,
  `module_image` varchar(100) NOT NULL,
  `module_priority` smallint(6) NOT NULL,
  `module_status` tinyint(2) NOT NULL,
  `module_menu` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bill_module`
--

INSERT INTO `bill_module` (`module_id`, `module_name`, `module_title`, `module_key`, `module_image`, `module_priority`, `module_status`, `module_menu`) VALUES
(18, 'Member', 'Member', 'member', 'fa fa-user-md', 98, 0, 1),
(52, 'Zimmertyp Manager', 'Zimmertyp Manager', 'rooms', 'fa fa-bed', 8, 1, 1),
(51, 'Mealtyp Manager', 'Mealtyp Manager', 'meals', 'fa fa-cutlery', 5, 1, 1),
(50, 'Hotels Manager', 'Hotels Manager', 'hotels', 'fa fa-building', 3, 1, 1),
(49, 'Reisetyp Manager', 'Reisetyp Manager', 'journey', 'fa fa-bus', 2, 1, 1),
(48, 'Destinations Manager', 'Destinations Manager', 'locations', 'fa fa-rocket', 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `bill_module_in_role`
--

CREATE TABLE `bill_module_in_role` (
  `module_in_role_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `module_in_role_status` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bill_module_in_role`
--

INSERT INTO `bill_module_in_role` (`module_in_role_id`, `role_id`, `module_id`, `module_in_role_status`) VALUES
(186, 8, 18, 1),
(202, 8, 52, 0),
(201, 8, 51, 0),
(200, 8, 50, 0),
(199, 8, 49, 0),
(198, 8, 48, 0);

-- --------------------------------------------------------

--
-- Table structure for table `bill_role`
--

CREATE TABLE `bill_role` (
  `role_id` int(11) NOT NULL,
  `role_title` text NOT NULL,
  `role_desc` varchar(400) NOT NULL,
  `role_status` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bill_role`
--

INSERT INTO `bill_role` (`role_id`, `role_title`, `role_desc`, `role_status`) VALUES
(8, 'Super Administrator', 'All the privileges available in this WORLD !!', 1),
(18, 'Customers', 'Clients', 1);

-- --------------------------------------------------------

--
-- Table structure for table `bill_rooms`
--

CREATE TABLE `bill_rooms` (
  `rooms_ID` int(255) NOT NULL,
  `rooms_title` text NOT NULL,
  `rooms_notes` text NOT NULL,
  `rooms_status` tinyint(1) NOT NULL DEFAULT '1',
  `rooms_persons_to_fit` int(2) NOT NULL,
  `rooms_creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rooms_update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bill_rooms`
--

INSERT INTO `bill_rooms` (`rooms_ID`, `rooms_title`, `rooms_notes`, `rooms_status`, `rooms_persons_to_fit`, `rooms_creation_time`, `rooms_update_time`) VALUES
(1, 'EinzelzimmerÃ¼', 'just a note', 1, 1, '2017-12-19 11:29:22', '2018-01-25 15:55:18'),
(2, 'Doppelzimmer', '', 1, 2, '2017-12-20 15:06:28', '0000-00-00 00:00:00'),
(3, '3er Zimmer', '', 1, 3, '2017-12-20 15:06:28', '0000-00-00 00:00:00'),
(4, '4er Zimmer', '', 1, 4, '2017-12-20 15:06:28', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `bill_rooms_price`
--

CREATE TABLE `bill_rooms_price` (
  `rp_ID` int(255) NOT NULL,
  `hotels_ID` int(255) NOT NULL,
  `rooms_ID` int(255) NOT NULL,
  `rp_price` float NOT NULL,
  `rp_price_date_from` date DEFAULT NULL,
  `rp_price_date_to` date DEFAULT NULL,
  `rp_notes` text NOT NULL,
  `rp_status` tinyint(1) NOT NULL,
  `rp_creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rp_update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bill_rooms_price`
--

INSERT INTO `bill_rooms_price` (`rp_ID`, `hotels_ID`, `rooms_ID`, `rp_price`, `rp_price_date_from`, `rp_price_date_to`, `rp_notes`, `rp_status`, `rp_creation_time`, `rp_update_time`) VALUES
(8, 3, 1, 80, '0000-00-00', '0000-00-00', '', 1, '2018-01-04 19:25:49', '2018-01-19 10:32:50'),
(9, 3, 2, 90, '0000-00-00', '0000-00-00', '', 1, '2018-01-04 19:25:53', '2018-01-19 10:32:57'),
(10, 3, 3, 50, '0000-00-00', '0000-00-00', '', 1, '2018-01-04 19:25:56', '2018-01-19 12:17:16'),
(11, 3, 4, 35, '2018-02-26', '2018-03-31', '', 1, '2018-01-04 19:26:00', '2018-02-27 15:19:25'),
(16, 3, 3, 100, '2018-01-23', '2018-01-23', '', 1, '2018-01-19 12:18:08', '2018-02-12 09:51:22');

-- --------------------------------------------------------

--
-- Table structure for table `bill_travelers`
--

CREATE TABLE `bill_travelers` (
  `travelers_ID` int(11) NOT NULL,
  `travelers_first_name` varchar(500) NOT NULL,
  `travelers_last_name` varchar(500) NOT NULL,
  `travelers_email` varchar(500) NOT NULL,
  `travelers_package` varchar(500) NOT NULL,
  `travelers_notes` text NOT NULL,
  `travelers_status` tinyint(1) NOT NULL,
  `travelers_paid` float NOT NULL,
  `travelers_creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `travelers_update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `bookings_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bill_travelers`
--

INSERT INTO `bill_travelers` (`travelers_ID`, `travelers_first_name`, `travelers_last_name`, `travelers_email`, `travelers_package`, `travelers_notes`, `travelers_status`, `travelers_paid`, `travelers_creation_time`, `travelers_update_time`, `bookings_ID`) VALUES
(4, 'Muhammad', 'Junaid', 'junaidcse786@gmail.com', '3er Zimmer + Abendessen', '', 1, 0, '2018-02-08 14:55:45', '0000-00-00 00:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `bill_user`
--

CREATE TABLE `bill_user` (
  `user_id` int(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `user_first_name` text NOT NULL,
  `user_last_name` text NOT NULL,
  `user_name` text NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_password` varchar(500) NOT NULL,
  `user_photo` varchar(600) NOT NULL,
  `user_description` text NOT NULL,
  `user_creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_status` int(2) NOT NULL,
  `user_org_name` varchar(500) NOT NULL,
  `user_validity_start` date NOT NULL,
  `user_validity_end` date NOT NULL,
  `user_trackability` int(1) DEFAULT '1',
  `user_level` varchar(500) NOT NULL,
  `user_login_time` int(255) NOT NULL,
  `user_exe_status` int(1) NOT NULL,
  `user_charge` int(1) DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bill_user`
--

INSERT INTO `bill_user` (`user_id`, `role_id`, `user_first_name`, `user_last_name`, `user_name`, `user_email`, `user_password`, `user_photo`, `user_description`, `user_creation_date`, `user_status`, `user_org_name`, `user_validity_start`, `user_validity_end`, `user_trackability`, `user_level`, `user_login_time`, `user_exe_status`, `user_charge`) VALUES
(1, 8, 'Super', 'Administrator', 'admin', 'admin@admin.com', 'e10adc3949ba59abbe56e057f20f883e', '17121821226Weirdosm_1024x1024.jpg', '', '2015-08-07 21:31:29', 1, '', '0000-00-00', '0000-00-00', 1, '', 0, 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bill_bookings`
--
ALTER TABLE `bill_bookings`
  ADD PRIMARY KEY (`bookings_ID`);

--
-- Indexes for table `bill_config`
--
ALTER TABLE `bill_config`
  ADD PRIMARY KEY (`config_id`);

--
-- Indexes for table `bill_draft_message`
--
ALTER TABLE `bill_draft_message`
  ADD PRIMARY KEY (`dm_id`);

--
-- Indexes for table `bill_early_bird`
--
ALTER TABLE `bill_early_bird`
  ADD PRIMARY KEY (`eb_ID`),
  ADD KEY `eb_hotels_id` (`hotels_ID`);

--
-- Indexes for table `bill_hotels`
--
ALTER TABLE `bill_hotels`
  ADD PRIMARY KEY (`hotels_ID`),
  ADD KEY `hotels_locations_id` (`locations_ID`);

--
-- Indexes for table `bill_journey`
--
ALTER TABLE `bill_journey`
  ADD PRIMARY KEY (`journey_ID`),
  ADD KEY `journey_locations_id` (`locations_ID`);

--
-- Indexes for table `bill_journey_location`
--
ALTER TABLE `bill_journey_location`
  ADD PRIMARY KEY (`jl_ID`),
  ADD KEY `journey_location_id` (`journey_ID`);

--
-- Indexes for table `bill_locations`
--
ALTER TABLE `bill_locations`
  ADD PRIMARY KEY (`locations_ID`);

--
-- Indexes for table `bill_locations_costs`
--
ALTER TABLE `bill_locations_costs`
  ADD PRIMARY KEY (`lc_ID`),
  ADD KEY `lc_locations_id` (`locations_ID`);

--
-- Indexes for table `bill_meals`
--
ALTER TABLE `bill_meals`
  ADD PRIMARY KEY (`meals_ID`);

--
-- Indexes for table `bill_meals_price`
--
ALTER TABLE `bill_meals_price`
  ADD PRIMARY KEY (`mp_ID`),
  ADD KEY `mp_hotels_id` (`hotels_ID`),
  ADD KEY `mp_meals_id` (`meals_ID`);

--
-- Indexes for table `bill_message`
--
ALTER TABLE `bill_message`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `bill_module`
--
ALTER TABLE `bill_module`
  ADD PRIMARY KEY (`module_id`);

--
-- Indexes for table `bill_module_in_role`
--
ALTER TABLE `bill_module_in_role`
  ADD PRIMARY KEY (`module_in_role_id`);

--
-- Indexes for table `bill_role`
--
ALTER TABLE `bill_role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `bill_rooms`
--
ALTER TABLE `bill_rooms`
  ADD PRIMARY KEY (`rooms_ID`);

--
-- Indexes for table `bill_rooms_price`
--
ALTER TABLE `bill_rooms_price`
  ADD PRIMARY KEY (`rp_ID`),
  ADD KEY `rp_hotels_id` (`hotels_ID`),
  ADD KEY `rp_rooms_id` (`rooms_ID`);

--
-- Indexes for table `bill_travelers`
--
ALTER TABLE `bill_travelers`
  ADD PRIMARY KEY (`travelers_ID`),
  ADD KEY `travelers_booking_id` (`bookings_ID`);

--
-- Indexes for table `bill_user`
--
ALTER TABLE `bill_user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bill_bookings`
--
ALTER TABLE `bill_bookings`
  MODIFY `bookings_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `bill_config`
--
ALTER TABLE `bill_config`
  MODIFY `config_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `bill_draft_message`
--
ALTER TABLE `bill_draft_message`
  MODIFY `dm_id` int(255) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bill_early_bird`
--
ALTER TABLE `bill_early_bird`
  MODIFY `eb_ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `bill_hotels`
--
ALTER TABLE `bill_hotels`
  MODIFY `hotels_ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `bill_journey`
--
ALTER TABLE `bill_journey`
  MODIFY `journey_ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `bill_journey_location`
--
ALTER TABLE `bill_journey_location`
  MODIFY `jl_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `bill_locations`
--
ALTER TABLE `bill_locations`
  MODIFY `locations_ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `bill_locations_costs`
--
ALTER TABLE `bill_locations_costs`
  MODIFY `lc_ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `bill_meals`
--
ALTER TABLE `bill_meals`
  MODIFY `meals_ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `bill_meals_price`
--
ALTER TABLE `bill_meals_price`
  MODIFY `mp_ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `bill_message`
--
ALTER TABLE `bill_message`
  MODIFY `message_id` int(255) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bill_module`
--
ALTER TABLE `bill_module`
  MODIFY `module_id` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;
--
-- AUTO_INCREMENT for table `bill_module_in_role`
--
ALTER TABLE `bill_module_in_role`
  MODIFY `module_in_role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=203;
--
-- AUTO_INCREMENT for table `bill_role`
--
ALTER TABLE `bill_role`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `bill_rooms`
--
ALTER TABLE `bill_rooms`
  MODIFY `rooms_ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `bill_rooms_price`
--
ALTER TABLE `bill_rooms_price`
  MODIFY `rp_ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `bill_travelers`
--
ALTER TABLE `bill_travelers`
  MODIFY `travelers_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `bill_user`
--
ALTER TABLE `bill_user`
  MODIFY `user_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=628;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `bill_early_bird`
--
ALTER TABLE `bill_early_bird`
  ADD CONSTRAINT `eb_hotels_id` FOREIGN KEY (`hotels_ID`) REFERENCES `bill_hotels` (`hotels_ID`) ON DELETE CASCADE;

--
-- Constraints for table `bill_hotels`
--
ALTER TABLE `bill_hotels`
  ADD CONSTRAINT `hotels_locations_id` FOREIGN KEY (`locations_ID`) REFERENCES `bill_locations` (`locations_ID`) ON DELETE CASCADE;

--
-- Constraints for table `bill_journey`
--
ALTER TABLE `bill_journey`
  ADD CONSTRAINT `journey_locations_id` FOREIGN KEY (`locations_ID`) REFERENCES `bill_locations` (`locations_ID`) ON DELETE CASCADE;

--
-- Constraints for table `bill_journey_location`
--
ALTER TABLE `bill_journey_location`
  ADD CONSTRAINT `journey_location_id` FOREIGN KEY (`journey_ID`) REFERENCES `bill_journey` (`journey_ID`) ON DELETE CASCADE;

--
-- Constraints for table `bill_locations_costs`
--
ALTER TABLE `bill_locations_costs`
  ADD CONSTRAINT `lc_locations_id` FOREIGN KEY (`locations_ID`) REFERENCES `bill_locations` (`locations_ID`) ON DELETE CASCADE;

--
-- Constraints for table `bill_meals_price`
--
ALTER TABLE `bill_meals_price`
  ADD CONSTRAINT `mp_hotels_id` FOREIGN KEY (`hotels_ID`) REFERENCES `bill_hotels` (`hotels_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `mp_meals_id` FOREIGN KEY (`meals_ID`) REFERENCES `bill_meals` (`meals_ID`) ON DELETE CASCADE;

--
-- Constraints for table `bill_rooms_price`
--
ALTER TABLE `bill_rooms_price`
  ADD CONSTRAINT `rp_hotels_id` FOREIGN KEY (`hotels_ID`) REFERENCES `bill_hotels` (`hotels_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `rp_rooms_id` FOREIGN KEY (`rooms_ID`) REFERENCES `bill_rooms` (`rooms_ID`) ON DELETE CASCADE;

--
-- Constraints for table `bill_travelers`
--
ALTER TABLE `bill_travelers`
  ADD CONSTRAINT `traveler_booking_id` FOREIGN KEY (`bookings_ID`) REFERENCES `bill_bookings` (`bookings_ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

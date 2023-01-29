-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 29, 2023 at 02:06 PM
-- Server version: 8.0.27
-- PHP Version: 8.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `techtool-laravel-admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
CREATE TABLE IF NOT EXISTS `branches` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'HO-AHM', 1, NULL, NULL),
(2, 'BHUJ', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `brokers`
--

DROP TABLE IF EXISTS `brokers`;
CREATE TABLE IF NOT EXISTS `brokers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_number` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brokers`
--

INSERT INTO `brokers` (`id`, `name`, `email`, `mobile_number`, `status`, `created_at`, `updated_at`) VALUES
(1, 'PARTH RAWAL', NULL, NULL, 1, NULL, NULL),
(2, 'VED SHAH', NULL, NULL, 1, NULL, NULL),
(3, 'Jignesh Vaghela', NULL, NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_number` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `wedding_anniversary_date` date DEFAULT NULL,
  `engagement_anniversary_date` date DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=131 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `mobile_number`, `date_of_birth`, `wedding_anniversary_date`, `engagement_anniversary_date`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'CUSTOMER NAME', 'E-MAIL ID', 'MOBLIE NO.', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(2, 'CHIRAGKUMAR K PATEL', 'chirag.kavisha@gamil.com', '9426362907', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(3, 'USHA PRAMODKUMAR KULL', 'Sales@policyklub.com', '9825299255', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(4, 'KIRANKUMAR CHIMANLAL BERA', 'jeelbera@yahoo.co.in', '9099952409', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(5, 'DEVASHISH BHUPEN SAWANI', 'SALES@POLICYKLUB.COM', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(6, 'SWATI VIKRAMBHAI PATEL', 'Sales@policyklub.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(7, 'RAHULKUMAR MAHESHBHAI PANCHAL', 'Rahul.panchal41@gmail.com', '9904460014', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(8, 'SHWETA VIRENDRA RAI', 'rajatshanu@gmail.com', '9733360888', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(9, 'BANAS SECURITY AND PERSONAL FORCE', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(10, 'BANAS SECURITY AND PERSONAL FORCE', 'BANAS_83@YAHOO.COM', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(11, 'PAYAL DAXESHKUMAR PATEL', 'Daxeshpatel5956@gmail.com', '8487921467', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(12, 'AKASH MAHENDRABHAI TRIVEDI', 'SALES@POLICYKLUB.COM', '8733009290', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(13, 'PRANAVBHAI RAVINDRABHAI AMIN', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(14, 'SANKET KIRANBHAI MISTRY', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(15, 'Parth Gaurang Rawal', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(16, 'MARIYAM PRABHULAL PATEL', 'Yagnik.loans@gmail.com', '9328299593', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(17, 'Jayswal Manharlal', 'sanjaymj824@gmail.com', '9328322036', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(18, 'Sanjay Jaiswal', 'sanjaymj824@gmail.com', '9328322036', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(19, 'MODI PARTH BHADRESHBHAI', 'parthmodi1819@gmail.com', '9924969299', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(20, 'DAXESHKUMAR MUKESHBHAI PATEL', 'Daxeshpatel5956@gmail.com', '8487921467', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(21, 'KIRAN AMRUTLAL MISTRY', 'sanket.mistry812@gmail.com', '9825943444', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(22, 'NISHARG MUKESHBHAI MISTRY', 'meetmistry50@gmail.com', '9099934317', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(23, 'SANKET KIRANKUMAR MISTRY', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(24, 'Aditi Rajendrakumar sharma', 'doctoraditisharma@gmail.com', '9940661147', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(25, 'VINIT NAGAR', 'snehathakurel@gmail.com', '9099333183', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(26, 'DHRUVKUMAR KISHORBHAI KUMBHAR', 'Dhruvmistry1556@gmail.com', '9558146768', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(27, 'GAURANG NARENDRABHAI RAVAL', 'Parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(28, 'HIMMATBHAI SHAMBHUBHAI BARAIYA', 'damku999@gmail.com', '8000071314', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(29, 'AJITSINGH RAJPUT', 'parthrawal89@gmail.com', '9898616113', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(30, 'FLYWING CARGO PVT LTD', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(31, 'MILAN MANSUKHBHAI PATEL', 'milu9012@gmail.com', '9727791290', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(32, 'JITENDRABHAI HIMATBHAI GEDIYA', 'parthrawal89@gmail.com', '9316599510', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(33, 'MEGH GUNAVANTBHAI PATEL', 'MEGHP04@GMAIL.COM', '9723467173', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(34, 'KANTILAL DANMALJI JAIN', 'Dhruvmistry1556@gmail.com', '9558146768', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(35, 'FLYWING CARGO PRIVATE LIMITED', 'SALES@POLICYKLUB.COM', '8733009582', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(36, 'PRANAV RAVINDRABHAI AMIN', 'parthrawal89@gmail.com', '9725816816', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(37, 'DARSHIT RAVINDRABHAI AMIN', 'parthrawal89@gmail.com', '9725816816', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(38, 'FLYWING CARGO PVT LTD', 'parthrawal89@gmail.com', '9725816816', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(39, 'KHYATI CHANDRAKANT PANDYA', 'parthrawal89@gmail.com', '9725816816', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(40, 'Kumbhar Gaurang K', 'parthrawal89@gmail.com', '9725816816', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(41, 'CHAITANYA DHIRAJLAL SHUKLA', 'PARTHRAWAL89@GMAIL.COM', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(42, 'RAJASTHAN ROAD CARRIER', 'parthrawal89@gmail.com', '9725816816', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(43, 'NITESH BHIKHABHAI PATEL', 'BHUJ.RIBPL@POLICYKLUB.COM', '9081332470', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(44, 'MECTECH KNITFABS PRIVATE LIMITED', 'parthrawal9@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(45, 'ANILKUMAR SATBIRSINGH BENIWAL', 'FLEETS@FLYWINGCARGO.COM', '9328946030', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(46, 'SHREE THAKORJI SALES', 'parthrawal9@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(47, 'KAILASH ENTERPRISE', 'parthrawal9@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(48, 'PREMILABEN RAVINDRABHAI AMIN', 'parthrawal9@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(49, 'FLYWING CARGO PVT LTD-rto pending', 'sales@Policyklub.com', '8733009582', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(50, 'Rajasthan Road Carrier', 'parthrawal9@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(51, 'DARSHIT RAVINDRABHAI AMIN', 'parthrawal9@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(52, 'PRANAV RAVINDRABHAI AMIN', 'parthrawal9@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(53, 'KHYATIBEN PRANAVKUMAR AMIN', 'parthrawal9@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(54, 'AVANI DARSHITBHAI AMIN', 'parthrawal9@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(55, 'RAJASTHAN TRANSPORT CORPORATION', 'parthrawal9@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(56, 'HARSHAD PILAJIRAO SHINDE', 'parthrawal9@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(57, 'PRAKASH INDRAVADAN KOTHARI', 'parthrawal9@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(58, 'FLYWING CARGO PVT LTD', 'parthrawal9@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(59, 'NIKUL PRAMODBHAI RAVAL', 'dharmik197@gmail.com', '9428899803', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(60, 'KALPESHKUMAR KANUBHAI PATEL', 'Surgicaldealer@gmail.com', '9904302748', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(61, 'Irashadali Aslamali Shekh - endorsment', 'jigneshvaghela12345@gmail.com', '9898143654', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(62, 'ANILKUMAR SATBIRSINGH BENIWAL', 'parthrawal9@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(63, 'GUNVANTBHAI K PATEL', 'parthrawal9@gmail.com', '9924137173', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(64, 'KRISH TRADELINK', 'parthrawal9@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(65, 'HARSHADRAO PILAJIRAO SHINDE', 'dharmesh_met@yahoo.co.in', '7874744477', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(66, 'BHISHAM JAIRAM NATHANI', 'SAJAL1301@GMAIL.COM', '7600006014', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(67, 'HIREN HEMSHANKAR VYAS', 'alyogint@yahoo.com', '9825647252', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(68, 'HIREN JAYANTIBHAI PATEL', 'parthrawal9@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(69, 'HARISHBHAI DAHYABHAI THAKKAR', 'parthrawal9@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(70, 'RITESH NARENDRAKUMAR JAIN', 'parthrawal9@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(71, 'LEENA KANJIBHAI SUTARIYA', 'parthrawal9@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(72, 'ANIL S THAKKAR', 'parthrawal89@gmail.com', '9898616113', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(73, 'SHRUTI RAJESH SHAH', 'parthrawal89@gmail.com', '9824798534', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(74, 'FLYWING CARGO PVT LTD', 'parthrawal89@gmail.com', '9898616113', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(75, 'DARSHIT RAVINDRABHAI AMIN', 'parthrawal89@gmail.com', '9898616113', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(76, 'JAIGOPAL THANDIRAM SHARMA', 'parthrawal89@gmail.com', '9727713289', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(77, 'RUSHABH HEMANTBHAI SHAH', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(78, 'PREMILABEN RAVINDRABHAI AMIN', 'PARTHRAWAL89@GMAIL.COM', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(79, 'SATBIR SINGH', 'parthrawal89@gmail.com', '9898616113', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(80, 'PRANAV R AMIN', 'PARTHRAWAL89@GMAIL.COM', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(81, 'RAVINDRA TRANSPORT CO', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(82, 'Naresh G Raval', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(83, 'RUSHIKESH HARSHADLAL PANDYA', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(84, 'PIONEER SECURITY SOLUTIONS PVT LTD', 'ravina.gupta@pioneersecure.com', '7623011137', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(85, ' PARAMOUNT LOOMS PVT LTD', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(86, 'VISHRUT RAJESH SHAH', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(87, 'PREMILABEN RAVINDRA AMIN', 'PARTHRAWAL89@GMAIL.COM', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(88, 'GAURAV ASHOKBHAI BHAVSAR', 'dalalpulin@gmail.com', '9510897103', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(89, 'JAY PINAKINBHAI SHUKLA', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(90, 'RENUKANBEN PINAKINBHAI SHUKLA', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(91, 'PIONEER SECURITY SOLUTIONS PVT LTD', 'ravina.gupta@pioneersecure. com', '7623011137', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(92, 'RAJASTHAN TRANSPORT CORPORATION', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(93, 'PRAMIT PARAG RAVAL', 'PARTHRAWAL89@GMAIL.COM', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(94, 'TRUPTI RAWAL', 'PARTHRAWAL89@GMAIL.COM', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(95, 'GUNVANTBHAI K PATEL', 'PARTHRAWAL89@GMAIL.COM', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(96, 'DARSHIT RAVINDRABHAI AMIN', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(97, 'EJAJ RAJAKBHAI SORA', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(98, 'ANIL SHANKARLAL THAKKAR', 'PARTHRAWAL89@GMAIL.COM', '9879930531', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(99, 'MANISH VAMANRAI MEHTA', 'PARTHRAWAL89@GMAIL.COM', '9879930531', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(100, 'FLYWING CARGO PVT LTD', 'PARTHRAWAL89@GMAIL.COM', '9879930531', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(101, 'RAMESH M JAIN', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(102, 'Jitendrakumar Navnitlal Acharya', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(103, 'FLYWING CARGO PRIVATE LIMITED', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(104, 'ANIL SATBIRSINGH BENIWAL', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(105, 'ANILKUMAR SHANKARLAL THAKKAR', 'SALES@POLICYKLUB.COM', '8733008681', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(106, 'HETAL ANILKUMAR THAKKAR', 'SALES@POLICYKLUB.COM', '8733008681', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(107, 'GAURANG NARENDRABHAI RAWAL', 'Parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(108, 'RAJENDRAKUMAR MANGILAL JAIN', 'SALES@POLICYCLUB.COM', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(109, 'YAGNIK KANUBHAI PATEL', 'SALES@POLICYCLUB.COM', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(110, 'RUSHABH HEMANTBHAI SHAH', 'parthrawal89@gmail.com', '9824252612', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(111, 'PRATIKKUMAR GUNVANTBHAI PANCHAL', 'parthrawal89@gmail.com', '7016980744', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(112, 'NIRAV JIGISH MEHTA', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(113, 'Suryadeep Nankubhai Basiya', 'counsellor.safeeduare@gmail.com', '7698018888', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(114, 'RAJASTHAN TRANSPORT CORPORATION', 'NITESH@FLYWINGCARGO.COM', '8000100107', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(115, 'FLYWING CARGO PVT LTD', 'NITESH@FLYWINGCARGO.COM', '8000100107', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(116, 'NISCHAL NITINBHAI VORA', 'parthrawal89@gmail.com', '9825017294', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(117, 'JAYESH CHANDRAKANT PANDYA', 'parthrawal89@gmail.com', '9558952720', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(118, 'PIONEER SECURITY\nSOLUTIONS PVT LTD', 'ravina.gupta@pioneersecure.\ncom', '7623011137', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(119, 'SABALSINGH NARUBHAI VANAR', 'parthrawal89@gmail.com', '9727713289', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(120, 'Bhakti Rameshbhai Thakker', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(121, 'FLYWING CARGO PRIVATE LIMITED', 'NITESH@FLYWINGCARGO.COM', '8000100107', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(122, 'ANILKUMAR S BENIWAL', 'nitesh@flywingcargo.com', '8000168890', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(123, 'RAJASTHAN TRANSPORT CORPORATION', 'SALES@POLICYKLUB.COM', '8000168890', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(124, 'Gunjan Ravindrabhai Trivedi', '', '', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(125, 'RAJASTHAN TRANSPORT\n CORPORATION', 'SALES@POLICYKLUB.COM', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(126, 'RAJASTHAN ROAD CARRIER', 'SALES@POLICYKLUB.COM', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(127, 'RAJESH CHIMANLAL RATHOD', 'SALES@POLICYKLUB.COM', '9099005442', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(128, 'BERA VINABEN', 'parthrawal89@gmail.com', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(129, 'FLYWING CARGO PVT LTD', 'SALES@POLICYKLUB.COM', '8733009586', NULL, NULL, NULL, 1, NULL, NULL, NULL),
(130, 'LAXMAN HIRA MEENA', 'PARTHRAWAL89@GMAIL.COM', '9727793123', NULL, NULL, NULL, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_insurances`
--

DROP TABLE IF EXISTS `customer_insurances`;
CREATE TABLE IF NOT EXISTS `customer_insurances` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `month` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sr_no` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `bus_type` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` int UNSIGNED DEFAULT NULL,
  `broker_id` bigint UNSIGNED DEFAULT NULL,
  `relationship_manager_id` bigint UNSIGNED DEFAULT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `insurance_company_id` int DEFAULT NULL,
  `type_of_policy` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `policy_no` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_no` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rto` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `make_model` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fuel_type` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `mobile_no` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_id` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `od_premium` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tp_premium` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rsa` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `net_premium` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gst` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `final_premium_with_gst` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mode_of_payment` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cheque_no` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `premium` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insurance_status` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra1` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issued_by` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra2` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra3` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra4` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra5` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra6` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra7` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=213 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_insurances`
--

INSERT INTO `customer_insurances` (`id`, `month`, `sr_no`, `issue_date`, `bus_type`, `branch_id`, `broker_id`, `relationship_manager_id`, `customer_id`, `insurance_company_id`, `type_of_policy`, `policy_no`, `registration_no`, `rto`, `make_model`, `fuel_type`, `start_date`, `expired_date`, `mobile_no`, `email_id`, `od_premium`, `tp_premium`, `rsa`, `net_premium`, `gst`, `final_premium_with_gst`, `mode_of_payment`, `cheque_no`, `premium`, `insurance_status`, `extra1`, `issued_by`, `extra2`, `extra3`, `extra4`, `extra5`, `extra6`, `extra7`, `status`, `created_at`, `updated_at`) VALUES
(1, 'MONTH', 'SR. NO.', '1970-01-01', 'BUS. TYPE', 0, 0, 0, 1, 0, 'TYPE OF POLICY', 'POLICY NO.', 'Registration No.', 'RTO', 'Make & Model', 'Fuel Type', '1970-01-01', '1970-01-01', NULL, NULL, 'OD PREMIUM', 'TP PREMIUM', 'RSA', 'NET PREMIUM', 'GST', 'FINAL PREMIUM WITH GST', 'MODE OF PAYMENT', 'CHEQUE NO', 'PREMIUM', NULL, '', 'ISSUED BY', '', '', '', '', '', '', 0, NULL, NULL),
(2, '01/01/2022', '23', '1970-01-01', 'ROLLOVER', 1, 1, 1, 2, 15, '4W OD ONLY', '62000707490000', 'GJ01WA5453', 'AHMEDABAD', 'KIA / SONET / HTX 1.0 iMT / SUV', 'PETROL', '2001-11-22', '2001-10-23', NULL, NULL, '10515.56', '0', '116', '10631.56', '1913.80', '12545.36', 'ONLINE LINK', '', '12545', NULL, '', 'KARN', '', '', '', '', '', '', 0, NULL, NULL),
(3, '01/01/2022', '49', '2022-03-01', 'USED', 1, 1, 1, 3, 2, '2W PACKAGE', 'OG-22-2202-1802-00049597', 'GJ06ND8643', 'VADODARA', 'TVS-JUPITER STD BS VI', 'PETROL', '2001-05-22', '2001-04-23', NULL, NULL, '975', '1083', '', '2058', '370.44', '2428.44', 'ONLINE', 'NA', '2428', NULL, '', 'KARN', '', '', '', '', '', '', 0, NULL, NULL),
(4, '01/01/2022', '55', '2022-05-01', 'Rollover', 1, 1, 1, 4, 6, '2W PACKAGE', 'D054032804/05012022', 'GJ01SF0188', 'AHMEDABAD', 'HONDA ACTIVA/3G', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '100.3', '752', '', '852.3', '153.4', '1005.7', 'ONLINE', 'NA', '1005', NULL, '', 'KARN', '', '', '', '', '', '', 0, NULL, NULL),
(5, '01/01/2022', '97', '2022-07-01', 'USED', 1, 1, 1, 5, 2, '2W PACKAGE', 'OG-22-2202-1802-00050411', 'GJ01JV1535', 'AHMEDABAD', 'HONDA ACTIVA 3G', 'PETROL', '2001-09-22', '2001-08-23', NULL, NULL, '368', '1083', '', '1451', '261.18', '1712.18', 'CHEQUE', '009454', '1713', NULL, '', 'KARN', '', '', '', '', '', '', 0, NULL, NULL),
(6, '01/01/2022', '269', '1970-01-01', 'Rollover', 1, 1, 1, 6, 6, '2W PACKAGE', 'D054998869/21012022', 'GJ01SU8488', 'AHMEDABAD', 'HONDA ACTIVA 3G', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '129.36', '1082', '', '1211.36', '218.04', '1429.40', 'ONLINE', 'NA', '1429', NULL, '', 'KARN', '', '', '', '', '', '', 0, NULL, NULL),
(7, '01/01/2022', '293', '1970-01-01', 'Rollover', 1, 1, 1, 7, 10, '2W PACKAGE', '3005/237543896/00/B00', 'GJ27CE9109', 'AHMEDABAD', 'TVS APACHE RTR 160', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '2544', '3103', '', '5647', '1016.46', '6663.46', 'ONLINE', 'NA', '6663', NULL, '', 'KARN', '', '', '', '', '', '', 0, NULL, NULL),
(8, '01/01/2022', '403', '1970-01-01', 'Rollover', 1, 1, 1, 8, 15, '4W PACKAGE', '62001208800000', 'GJ05RC7647', 'SURAT', 'TOYOTA INNOVA CRYSTA/2.4G MT 7 STR MUV', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '13208.91', '8665', '116', '21989.91', '3958.18', '25948.09', 'ONLINE', 'NA', '25948', NULL, '', 'KARN', '', '', '', '', '', '', 0, NULL, NULL),
(9, '01/02/2022', '49', '2022-02-02', 'FRESH', 1, 1, 1, 9, 2, 'WC', 'OG-22-2202-2802-00002521', 'NA', 'NA', 'NA', 'NA', '2002-04-22', '2004-03-22', NULL, NULL, '0', '3816', '', '3816', '686.88', '4502.88', 'LINK', '', '4502', NULL, '', 'DHVANI', '', '', '', '', '', '', 0, NULL, NULL),
(10, '01/02/2022', '50', '2022-02-02', 'FRESH', 1, 1, 1, 10, 2, 'WC', 'OG-22-2202-2802-00002519', 'NA', 'NA', 'NA', 'NA', '2002-04-22', '2004-03-22', NULL, NULL, '0', '10227', '', '10227', '1840.86', '12067.86', 'LINK', '', '12067', NULL, '', 'DHVANI', '', '', '', '', '', '', 0, NULL, NULL),
(11, '01/02/2022', '51', '2022-02-02', 'FRESH', 1, 1, 1, 10, 2, 'WC', 'OG-22-2202-2802-00002520', 'NA', 'NA', 'NA', 'NA', '2002-04-22', '2004-03-22', NULL, NULL, '0', '4708', '', '4708', '847.4399999999999', '5555.44', 'LINK', '', '5556', NULL, '', 'DHVANI', '', '', '', '', '', '', 0, NULL, NULL),
(12, '01/02/2022', '52', '2022-02-02', 'FRESH', 1, 1, 1, 10, 2, 'WC', 'OG-22-2202-2802-00002518', 'NA', 'NA', 'NA', 'NA', '2002-04-22', '2004-03-22', NULL, NULL, '0', '8370', '', '8370', '1506.6', '9876.6', 'LINK', '', '9876', NULL, '', 'DHVANI', '', '', '', '', '', '', 0, NULL, NULL),
(13, '01/02/2022', '60', '2022-01-02', 'ROLLOVER', 1, 1, 1, 11, 15, '4W OD', '6200132150 00 00', 'GJ01WA7670', 'AHMEDABAD', 'KIA MOTORS SONET HTX 1.0 IMT SUV', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '13571.56', '0', '116', '13687.6', '2463.8', '16151.3', 'Online Link', 'NA', '16151', NULL, '', 'KARN', '', '', '', '', '', '', 0, NULL, NULL),
(14, '01/02/2022', '95', '2022-03-02', 'ROLLOVER', 1, 1, 1, 12, 15, '4W OD ONLY', '6200135771', 'GJ01WA8283', 'AHMEDABAD', 'HYUNDAI / GRAND I10 NIOS / SPORTZ 1.2 KAPPA VTVT / HATCH BACK', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '9297.49', '0', '116', '9413.49', '1694.4282', '11107.92', 'CHEQUE', '307474', '11108', NULL, '', 'KARN', '', '', '', '', '', '', 0, NULL, NULL),
(15, '01/02/2022', '120', '2022-04-02', 'ROLLOVER', 1, 1, 1, 13, 1, '2W PKG', 'OG-22-2202-1802-00054869', 'GJ27BJ7055', 'AHMEDABAD EAST', 'ROYAL ENFIELD CLASSIC 350 ELECTRIC START DISC BREAK', 'PETROL', '2002-05-22', '2002-04-23', NULL, NULL, '1193', '1524', '', '2717', '489.06', '3206.06', 'LINK SEND TO COS', '', '3207', NULL, '', 'MAYANK', '', '', '', '', '', '', 0, NULL, NULL),
(16, '01/02/2022', '149', '2022-07-02', 'ROLLOVER', 1, 1, 1, 13, 2, '2W PKG', 'OG-22-2202-1802-00055419', 'GJ27AK9843', 'AHMEDABAD EAST', 'HONDA ACTIVA STD', 'PETROL', '2002-09-22', '2002-08-23', NULL, NULL, '200', '1083', '', '1283', '230.94', '1513.94', 'Online Link', '', '1513', NULL, '', 'MAYANK', '', '', '', '', '', '', 0, NULL, NULL),
(17, '01/02/2022', '295', '2022-11-02', 'ROLLOVER', 1, 1, 1, 14, 2, '2W PKG', 'OG-22-2202-1802-00056163', 'GJ27BK7856', 'AHMEDABAD', 'ROYAL ENFIELD CLASSIC 350 ALLOY', 'PETROL', '1970-01-01', '2002-12-23', NULL, NULL, '381', '1524', '', '1905.0', '342.9', '2247.9', 'Online Link', '', '2247', NULL, '', 'MAYANK', '', '', '', '', '', '', 0, NULL, NULL),
(18, '01/02/2022', '297', '2022-12-02', 'ROLLOVER', 1, 1, 1, 15, 2, '2W PKG', 'OG-22-2202-1802-00056406', 'GJ01FY5364', 'AHMEDABAD', 'ROYAL ENFIELD CLASSIC 350 ELECTRIC START DISC BREAK', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '325', '1524', '', '1849.0', '332.8', '2181.8', 'Online Link', '', '2181', NULL, '', 'MAYANK', '', '', '', '', '', '', 0, NULL, NULL),
(19, '01/02/2022', '328', '1970-01-01', 'ROLLOVER', 1, 1, 1, 16, 15, '4W PKG', '6200159107', 'GJ01HV4729', 'AHMEDABAD', 'HYUNDAI / CRETA /1.4 EX DIESEL / SUV', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '11870.94', '3896', '116', '15882.9', '2858.9', '18741.9', 'ONLINE LINK', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(20, '01/02/2022', '473', '1970-01-01', 'FRESH', 1, 1, 1, 17, 4, 'HEALTH', '38715599', 'na', 'na', 'na', 'na', '1970-01-01', '1970-01-01', NULL, NULL, '0', '29374.2', '', '29374.2', '5287.36', '34661.56', 'CHEQUE', '', '', NULL, '', 'DHVANI', '', '', '', '', '', '', 0, NULL, NULL),
(21, '01/02/2022', '474', '1970-01-01', 'FRESH', 1, 1, 1, 18, 4, 'HEALTH', '38715821', 'NA', 'NA', 'NA', 'NA', '1970-01-01', '1970-01-01', NULL, NULL, '0', '7280', '', '7280', '1310.4', '8590.4', 'CHEQUE', '', '', NULL, '', 'DHVANI', '', '', '', '', '', '', 0, NULL, NULL),
(22, '01/02/2022', '490', '1970-01-01', 'FRESH', 1, 1, 1, 19, 8, 'TERM PLAN', '24456962', 'NA', 'NA', 'NA', 'NA', '1970-01-01', '1970-01-01', NULL, NULL, '0', '38115', '', '38115', '6860.7', '44975.7', 'LINK', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(23, '01/03/2022', '2', '2022-01-02', 'ROLLOVER', 1, 1, 1, 20, 9, '2W OD', '3005/O/238502930/00/000', 'GJ01UN4655', 'AHMEDABAD', 'HONDA ACTIVA 5G DLX', 'PETROL', '2003-08-22', '2003-07-22', NULL, NULL, '613', '0', '', '613', '110.34', '723.34', 'Online Link', 'NA', '723', NULL, '', 'KARN', '', '', '', '', '', '', 0, NULL, NULL),
(24, '01/03/2022', '22', '1970-01-01', 'ROLLOVER', 1, 1, 1, 21, 6, '2W OD', 'D058223129', 'GJ27CR7856', 'AHMEDABAD EAST', 'PIAGGIO VESPA/VX', 'PETROL', '2003-01-22', '1970-01-01', NULL, NULL, '374.19', '0', '', '374.19', '67.35', '441.54', 'LINK', '', '441', NULL, '', 'SNEHA', '', '', '', '', '', '', 0, NULL, NULL),
(25, '01/03/2022', '31', '1970-01-01', 'ROLLOVER', 1, 1, 1, 22, 2, '2W PKG', 'OG-22-2202-1802-00058613', 'GJ27BB7856', 'AHMEDABAD EAST', 'YAMAHA FASCINO 113 CC SCOOTER', 'PETROL', '2003-02-22', '2003-01-23', NULL, NULL, '120', '1083', '', '1203', '216.54', '1419.54', 'LINK', '', '1419', NULL, '', 'SNEHA', '', '', '', '', '', '', 0, NULL, NULL),
(26, '01/03/2022', '124', '2022-07-03', 'ROLLOVER', 1, 1, 1, 23, 2, '2W PKG', 'OG-22-2202-1802-00060308', 'GJ27CD7312', 'AHMEDABAD EAST', 'TVS JUPITER SELF START DRUM BRAKE ALLOY WHEEL', 'PETROL', '1970-01-01', '2003-12-23', NULL, NULL, '189', '1083', '', '1272', '228.96', '1500.96', 'LINK', '', '1500', NULL, 'ISSUE', 'SNEHA', '', '', '', '', '', '', 0, NULL, NULL),
(27, '01/03/2022', '132', '2022-05-03', 'ROLLOVER', 1, 1, 1, 24, 15, '4W PKG', '6200196155', 'GJ01RW7323', 'AHMEDABAD', 'MARUTI SWIFT VXI', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '6075.42', '3896', '116', '10087.42', '1815.74', '11903.16', 'link', '', '11903', NULL, 'issued', 'mayank', '', '', '', '', '', '', 0, NULL, NULL),
(28, '01/03/2022', '214', '2022-12-03', 'ROLLOVER', 1, 1, 1, 25, 15, '4W PKG', '6200212302', 'GJ01RA9622', 'AHMEDABAD', 'HYUNDAI I10 MAGNA 1.1 HATCHBACK', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '945.25', '3896', '', '4841.25', '871.42', '5712.68', 'LINK', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(29, '01/03/2022', '255', '1970-01-01', 'ROLLOVER', 1, 1, 1, 26, 9, '2w pkg', '3005/242469478/00/000', 'GJ27BM3131', 'AHMEDABAD EAST', 'HONDA ACTIVA 4G', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '217', '1127', '', '1344.00', '241.92', '1585.92', 'LINK SEND TO COS', '', '1586', NULL, '', 'mayank', '', '', '', '', '', '', 0, NULL, NULL),
(30, '01/04/2022', '78', '2022-02-04', 'ROLLOVER', 1, 1, 1, 27, 15, '4W OD', '6200254338', 'GJ01WB2723', 'ahmedabad', 'MARUTI/ALTO LXI CNG/HATCH BACK', 'PETROL/CNG', '2004-03-22', '2004-02-23', NULL, NULL, '6962', '0', '116', '7078', '1274.04', '8352.040000000001', 'ONLINE', 'NA', '8352', NULL, 'ISSUED', 'KARN', '', '', '', '', 'HDFC', '2311204106390400000', 0, NULL, NULL),
(31, '01/04/2022', '121', '2022-05-04', 'ROLLOVER', 1, 1, 1, 28, 2, 'Two-Wheeler Package Policy', 'OG-23-2202-1802-00000601', 'GJ04DQ5010', 'BHAVNAGAR', 'HONDA CB SHINE DRUM BSIV', 'PETROL', '2004-07-22', '2004-06-23', NULL, NULL, '1019', '1083', '', '2102', '378.36', '2480.36', 'LINK', '', '2480', NULL, 'MAYANK', '', '', '', '', '', '', '', 0, NULL, NULL),
(32, '01/04/2022', '138', '2022-06-04', 'ROLLOVER', 1, 1, 1, 29, 15, '4W PACKAGE', '6200261666', 'GJ27CM2323', 'AHMEDABAD EAST', 'MARUTI VITARA BREZZA VDI AMT SUV', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '10541.16', '3896', '116', '14553.16', '2619.57', '17172.73', 'LINK SEND', '', '17173', NULL, 'ISSUE', 'SNEHA', 'ROYAL', 'VPS0069511000100', '', '', '', '', 0, NULL, NULL),
(33, '01/04/2022', '163', '2022-06-04', 'NAME ADDITION', 1, 1, 1, 30, 9, 'GMC', '4016/X/225230710/00/000', 'NA', 'NA', 'NA', 'NA', '1970-01-01', '2008-12-22', NULL, NULL, '0', '119922', '', '119922', '21585.96', '141507.96', 'LINK', '', '141507', NULL, '', 'DHVANI', '', '', '', '', '', '', 0, NULL, NULL),
(34, '01/04/2022', '168', '2022-07-04', 'ROLLOVER', 1, 1, 1, 31, 15, '2W PKG', '0163211870', 'GJ01JU0197', 'AHMEDABAD', 'ROYAL ENFIELD THUNDERBIRD ­ 350 ABS', 'petrol', '2004-12-22', '2004-11-23', NULL, NULL, '297.1', '1568', '', '1865.1', '335.72', '2200.82', 'LINK', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(35, '01/04/2022', '169', '2022-07-04', 'FRESH', 1, 1, 1, 32, 15, '4W PACKAGE', '6200262163', 'GJ14AP3600', 'amreli', 'MARUTI / SWIFT / VDI (O) / HATCH BACK', 'DIESEL', '2004-07-22', '2004-06-23', NULL, NULL, '12753.56', '3896', '116', '16765.56', '3017.80', '19783.36', 'LINK SEND', '', '19783', NULL, 'ISSUE', 'SNEHA', 'RC BASE', 'RC BASE', '', '', '', '', 0, NULL, NULL),
(36, '01/04/2022', '354', '1970-01-01', 'ROLLOVER', 1, 1, 1, 33, 11, '4W OD ONLY', '2217314500', 'GJ01WB3483', 'AHMEDABAD', 'KIA SONET GTX PLUS 1.0 DCT', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '14324.24', '0', '', '14324.24', '2578.36', '16902.60', 'CHEQUE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(37, '01/04/2022', '376', '1970-01-01', 'FRESH', 1, 1, 1, 34, 2, '2W PKG', 'OG-23-2202-1802-00002836', 'GJ27Q1556', 'AHMEDABAD EAST', 'HONDA ACTIVA DLX 110 CC', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '201', '1083', '', '1284', '231.12', '1515.12', 'LINK SEND', '', '1516', NULL, 'ISSUE', 'SNEHA', 'RC BASE', 'RC BASE', '', '', '', '', 0, NULL, NULL),
(38, '01/04/2022', '457', '1970-01-01', 'ROLLOVER', 1, 1, 1, 35, 15, 'GCV PKG', '0163252535', 'GJ01JT3042', 'AHMEDABAD', 'TATA LPT 2818 TRUCK', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '15176.91', '43257', '', '58433.91', '7936', '66369.91', 'LINK', '', '', NULL, '', '', 'ROYAL SUNDARAM', 'VGC0735225000100', '', '', '', '', 0, NULL, NULL),
(39, '01/04/2022', '458', '1970-01-01', 'ROLLOVER', 1, 1, 1, 35, 15, 'GCV PKG', '0163252536', 'GJ01JT3275', 'AHMEDABAD', 'TATA LPT 2818 TRUCK', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '15176.91', '43257', '', '58433.91', '7936', '66369.91', 'LINK', '', '', NULL, '', '', 'ROYAL SUNDARAM', 'VGC0735236000100', '', '', '', '', 0, NULL, NULL),
(40, '01/04/2022', '459', '1970-01-01', 'ROLLOVER', 1, 1, 1, 35, 15, 'GCV PKG', '0163252560', 'GJ01JT3450', 'AHMEDABAD', 'TATA LPT 2818 TRUCK', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '15176.91', '43257', '', '58433.91', '7936', '66369.91', 'LINK', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(41, '01/04/2022', '460', '1970-01-01', 'ROLLOVER', 1, 1, 1, 35, 15, 'GCV PKG', '0163252538', 'GJ01JT3194', 'AHMEDABAD', 'TATA LPT 2818 TRUCK', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '15176.91', '43257', '', '58433.91', '7936', '66369.91', 'LINK', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(42, '01/04/2022', '461', '1970-01-01', 'ROLLOVER', 1, 1, 1, 35, 15, 'GCV PKG', '0163252540', 'GJ01JT3442', 'AHMEDABAD', 'TATA LPT 2818 TRUCK', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '15176.91', '43257', '', '58433.91', '7936', '66369.91', 'LINK', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(43, '01/05/2022', '94', '2022-03-05', 'ROLLOVER', 1, 1, 1, 36, 9, 'GCV PKG', '3003/246535244/00/000', 'GJ20V5756', 'Dahod', 'Eicher Pro 1110 H', 'DIESEL', '2005-05-22', '2005-04-23', NULL, NULL, '1080', '27035', '0', '28115', '3445', '31560', 'LINK', '', '', NULL, 'karn', '', '', '', '', '', '', '', 0, NULL, NULL),
(44, '01/05/2022', '95', '2022-03-05', 'ROLLOVER', 1, 1, 1, 37, 9, 'GCV PKG', '3003/246535396/00/000', 'GJ20V5783', 'Dahod', 'Eicher Pro 1110 H', 'DIESEL', '2005-05-22', '2005-04-23', NULL, NULL, '705', '27410', '0', '28115', '3445', '31560', 'LINK', '', '', NULL, 'karn', '', '', '', '', '', '', '', 0, NULL, NULL),
(45, '01/05/2022', '134', '2022-06-05', 'ROLLOVER', 1, 1, 1, 38, 15, 'GCV PKG', '0163280886', 'KA52A8702', 'NELAMANGALA', 'EICHER PRO 5016 ­ TRUCK', 'DIESEL', '2005-09-22', '2005-08-23', NULL, NULL, '13259.71', '33688', '0', '46947.71', '6446', '53393.63', 'LINK', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(46, '01/05/2022', '160', '2022-09-05', 'ROLLOVER', 1, 1, 1, 38, 15, 'GCV PKG', '0163282876', 'KA52A8701', 'NELAMANGALA', 'Eicher Pro 5016 M CBC container', 'DIESEL', '2005-09-22', '2005-08-23', NULL, NULL, '13257.13', '33688', '0', '46945.13', '6445', '53390.13', 'LINK', '', '', NULL, '', 'BAJAJ ALLIANZ', ':OG­22­2202­1803­00001202', '', '', '', '', '', 0, NULL, NULL),
(47, '01/05/2022', '269', '1970-01-01', 'ROLLOVER', 1, 1, 1, 39, 15, '4W PACKAGE', '6200324830', 'GJ27AA1789', 'AHMEDABAD EAST', 'MARUTI / ALTO 800 / std', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '655.89', '2547', '0', '3202.89', '576.52', '3779.41', 'CHEQUE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(48, '01/05/2022', '385', '1970-01-01', 'FRESH', 1, 1, 1, 40, 4, 'HEALTH', '41885773', 'na', 'na', 'na', 'NA', '2005-05-22', '2005-04-23', NULL, NULL, '0', '14790.77', '0', '14790.77', '2662.34', '17453.11', 'LINK', '', '', NULL, 'dhvani', '', '', '', '', '', '', '', 0, NULL, NULL),
(49, '01/05/2022', '396', '1970-01-01', 'ROLLOVER', 1, 1, 1, 41, 9, '2W PKG', '3005/248029012/00/000', 'GJ01FZ4129', 'ahmedabad', 'honda motorcycle activa i', 'petrol', '1970-01-01', '1970-01-01', NULL, NULL, '138', '1127', '0', '1265', '227.70', '1492.70', 'LINK', '', '1493', NULL, 'SNEHA', '', '', '', '', '', '', '', 0, NULL, NULL),
(50, '01/05/2022', '412', '2022-03-06', 'FRESH', 1, 1, 1, 30, 15, 'GCV PKG', '0163356250', 'GJ01HT4344', 'AHMEDABAD', 'EICHER PRO 1095 ­ 3765 MM WB HSD', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '10666.75', '27205', '0', '37871.75', '5200.00', '43071.75', 'LINK', '', '', NULL, 'KARN', '', '', '', '', '', '', '', 0, NULL, NULL),
(51, '01/05/2022', '413', '1970-01-01', 'FRESH', 1, 1, 1, 30, 15, 'GCV PKG', '0163315272', 'GJ01HT3343', 'AHMEDABAD', 'EICHER PRO 5025 ­ DIESEL', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '14656.37', '43307', '0', '57963.37', '7850.00', '65813.37', 'LINK', '', '', NULL, 'KARN', '', '', '', '', '', '', '', 0, NULL, NULL),
(52, '01/05/2022', '414', '1970-01-01', 'FRESH', 1, 1, 1, 30, 15, 'GCV PKG', '0163315263', 'GJ01HT3255', 'AHMEDABAD', 'EICHER PRO 5025 ­ DIESEL', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '14656.37', '43307', '0', '57963.37', '7850.00', '65813.37', 'LINK', '', '', NULL, 'KARN', '', '', '', '', '', '', '', 0, NULL, NULL),
(53, '01/05/2022', '415', '1970-01-01', 'FRESH', 1, 1, 1, 30, 15, 'GCV PKG', '0163315267', 'GJ01HT3233', 'AHMEDABAD', 'EICHER PRO 5025 ­ DIESEL', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '14656.37', '43307', '0', '57963.37', '7850.00', '65813.37', 'LINK', '', '', NULL, 'KARN', '', '', '', '', '', '', '', 0, NULL, NULL),
(54, '01/05/2022', '416', '1970-01-01', 'FRESH', 1, 1, 1, 30, 15, 'GCV PKG', '0163315264', 'GJ01HT3023', 'AHMEDABAD', 'EICHER PRO 5025 ­ DIESEL', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '14656.37', '43307', '0', '57963.37', '7850.00', '65813.37', 'LINK', '', '', NULL, 'KARN', '', '', '', '', 'right', '', '', 0, NULL, NULL),
(55, '01/05/2022', '417', '2022-03-06', 'FRESH', 1, 1, 1, 30, 15, 'GCV PKG', '0163356249', 'TN39CL0425', 'AHMEDABAD', 'EICHER 10.59 ­ TRUCK', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '8049.07', '16016', '0', '24065.07', '3388.00', '27453.07', 'LINK', '', '', NULL, 'KARN', '', '', '', '', '', '', '', 0, NULL, NULL),
(56, '01/05/2022', '418', '2022-03-06', 'FRESH', 1, 1, 1, 30, 15, 'GCV PKG', '0163356251', 'MH04JU6935', 'AHMEDABAD', 'EICHER 10.59 ­ TRUCK', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '8049.07', '16016', '0', '24065.07', '3388.00', '27453.07', 'LINK', '', '', NULL, 'KARN', '', '', '', '', '', '', '', 0, NULL, NULL),
(57, '01/05/2022', '419', '1970-01-01', 'FRESH', 1, 1, 1, 30, 15, 'GCV PKG', '0163315271', 'GJ01HT3411', 'AHMEDABAD', 'EICHER PRO 5025 ­ DIESEL', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '14656.37', '43307', '0', '57963.37', '7850.00', '65813.37', 'LINK', '', '', NULL, 'KARN', '', '', '', '', '', '', '', 0, NULL, NULL),
(58, '01/05/2022', '427', '1970-01-01', 'FRESH', 1, 1, 1, 30, 15, 'GCV PKG', '0163318797', 'GJ01HT3428', 'AHMEDABAD', 'EICHER PRO 5025 ­ DIESEL', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '14656.37', '43307', '0', '57963.37', '7850', '65813.37', 'ONLINE', '', '', NULL, 'KARN', '', '', '', '', '', '', '', 0, NULL, NULL),
(59, '01/05/2022', '528', '1970-01-01', 'ROLLOVER', 1, 1, 1, 42, 15, 'GCV PKG', '0163333520', 'GJ01HT3423', 'AHMEDABAD', 'EICHER PRO 5025 CONTAINER', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '14656.37', '43307', '0', '57963.4', '7850.00', '65813.37', 'LINK SEND', '', '', NULL, 'KARN', '', '', '', '', '', '', '', 0, NULL, NULL),
(60, '01/05/2022', '604', '2022-07-06', 'FRESH', 2, 2, 2, 43, 9, '2W TP', '3005/A/249193006/00/B00', 'GJ12BS5933', 'BHUJ', 'PLEASURE', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '0', '714', '0', '714', '128.52', '842.52', 'ONLINE', '', '', NULL, 'ANJALI', '', '', '', '', '', '', '', 0, NULL, NULL),
(61, '01/06/2022', '284', '1970-01-01', 'Fresh', 1, 1, 1, 44, 9, 'Marine', '2005/250248580/00/000', 'Na', 'na', 'na', 'na', '1970-01-01', '1970-01-01', NULL, NULL, '3704.64', '194.98', '0', '3899.62', '701.93', '4601.55', 'Online', '', '', NULL, 'Karn', '', '', '', '1', '', '', '', 0, NULL, NULL),
(62, '01/06/2022', '286', '2022-09-06', 'Fresh', 1, 1, 1, 45, 9, '2w pkg', '3005/249478254/00/B00', 'GJ01MR0357', 'Ahmedabad', 'Hero honda Passion Plus 97', 'Petrol', '2006-09-22', '2006-08-23', NULL, NULL, '293', '1089', '0', '1382', '248.76', '1630.76', 'Online', '', '', NULL, 'Karn', '', '', '', '', '', '', '', 0, NULL, NULL),
(63, '01/06/2022', '302', '1970-01-01', 'Fresh', 1, 1, 1, 46, 16, 'Fire', '21250111228000000088', 'Na', 'na', 'na', 'na', '1970-01-01', '1970-01-01', NULL, NULL, '0', '3429', '0', '3429', '617.22', '4046.22', 'Online', '', '', NULL, 'Dhvani', '', '', '', '', '', '', '', 0, NULL, NULL),
(64, '01/06/2022', '303', '1970-01-01', 'Fresh', 1, 1, 1, 47, 16, 'Fire', '21250111228000000087', 'Na', 'na', 'na', 'na', '1970-01-01', '1970-01-01', NULL, NULL, '0', '3429', '0', '3429', '617.22', '4046.22', 'Online', '', '', NULL, 'Dhvani', '', '', '', '', '', '', '', 0, NULL, NULL),
(65, '01/06/2022', '478', '2022-04-06', 'Rollover', 1, 1, 1, 48, 9, 'GCV Pkg', '3003/249101518/00/000', 'GJ20V1550', 'DAHOD', 'eicher 11.10 xp', 'DIESEL', '2006-06-22', '2006-05-23', NULL, NULL, '541', '27286', '0', '27827.0', '3378.00', '31205.00', 'ONLINE LINK', '', '31205', NULL, 'KARN', 'BAJAJ ALLIANZ', 'OG-22-2202-1803-00001921', '', '', '', '', '', 0, NULL, NULL),
(66, '01/06/2022', '479', '2022-04-06', 'Rollover', 1, 1, 1, 49, 15, 'GCV Pkg', '0163359770', 'TN39CK9561', 'Tirupur ', 'Ashok Layland Dost Plus RLS', 'DIESEL', '2006-05-22', '2006-04-23', NULL, NULL, '5002.15', '16319', '0', '21321.2', '2875.00', '24196.15', 'ONLINE LINK', '', '24197', NULL, 'KARN', 'BAJAJ ALLIANZ', 'OG­22­2202­1803­00002001', '', '', '', '', '', 0, NULL, NULL),
(67, '01/06/2022', '502', '2022-08-06', 'Rollover', 1, 1, 1, 50, 15, 'GCV PKG', '0163368423', 'GJ01HT3140', 'Ahmedabad', 'EICHER PRO 5025 ­ DIESEL', 'Diesel', '2006-08-22', '2006-07-23', NULL, NULL, '13434.14', '44220', '0', '57654.14', '7740.00', '65394.14', 'Online', '', '', NULL, 'Karn', 'Bajaj allianz', 'OG­22­2202­1803­00002005', '', '', '', '', '', 0, NULL, NULL),
(68, '01/06/2022', '507', '2022-09-06', 'Rollover', 1, 1, 1, 51, 9, 'Gcv PKG', '3003/249461942/00/000', 'GJ27TT4887', 'Ahmedabad East', 'Eicher Pro 1110 H', 'DIESEL', '1970-01-01', '2006-12-23', NULL, NULL, '2303', '27286', '0', '29589', '3695.00', '33284.00', 'Online', '', '', NULL, 'Karn', 'Bajaj allianz', 'OG-22-2202-1803-00002159', '', '', '', '', '', 0, NULL, NULL),
(69, '01/06/2022', '508', '2022-09-06', 'Rollover', 1, 1, 1, 52, 9, 'Gcv PKG', '3003/249464951/00/000', 'GJ27TT4646', 'Ahmedabad East', 'Eicher Pro 1110 H', 'DIESEL', '1970-01-01', '2006-12-23', NULL, NULL, '2303', '27286', '0', '29589', '3695.00', '33284.00', 'Online', '', '', NULL, 'Karn', 'Bajaj allianz', 'OG-22-2202-1803-00002158', '', '', '', '', '', 0, NULL, NULL),
(70, '01/06/2022', '509', '2022-09-06', 'Rollover', 1, 1, 1, 53, 9, 'Gcv PKG', '3003/249466083/00/000', 'GJ27TT6110', 'Ahmedabad East', 'Eicher Pro 1110 H', 'DIESEL', '1970-01-01', '2006-12-23', NULL, NULL, '2303', '27286', '0', '29589', '3695.00', '33284.00', 'Online', '', '', NULL, 'Karn', 'Bajaj allianz', 'OG-22-2202-1803-00002162', '', '', '', '', '', 0, NULL, NULL),
(71, '01/06/2022', '510', '2022-09-06', 'Rollover', 1, 1, 1, 54, 9, 'Gcv PKG', '3003/249460155/00/000', 'GJ27TT4505', 'Ahmedabad East', 'Eicher Pro 1110 H', 'DIESEL', '1970-01-01', '2006-12-23', NULL, NULL, '2303', '27286', '0', '29589', '3695.00', '33284.00', 'Online', '', '', NULL, 'Karn', 'Bajaj allianz', 'OG-22-2202-1803-00002161', '', '', '', '', '', 0, NULL, NULL),
(72, '01/06/2022', '590', '1970-01-01', 'Rollover', 1, 1, 1, 50, 15, 'Gcv PKG', '0163395496', 'GJ01DT7082', 'Ahmedabad', 'Tata LPT 1613 Open', 'Diesel', '1970-01-01', '1970-01-01', NULL, NULL, '2579.54', '35583', '0', '38162.54', '4750.00', '42912.54', 'Online', '', '', NULL, 'Karn', 'Bajaj Allianz', 'OG­22­2202­1803­00002468', '', '', '', '', '', 0, NULL, NULL),
(73, '01/06/2022', '591', '1970-01-01', 'Rollover', 1, 1, 1, 55, 15, 'Gcv PKG', '0163395497', 'GJ01DT7114', 'Ahmedabad', 'Tata LPT 1613 Open', 'Diesel', '1970-01-01', '1970-01-01', NULL, NULL, '2636.68', '35583', '0', '38219.68', '4760.00', '42979.68', 'Online', '', '', NULL, 'Karn', 'Tata Aig', 'OG­22­2202­1803­00002567', '', '', '', '', '', 0, NULL, NULL),
(74, '01/06/2022', '594', '1970-01-01', 'Rollover', 1, 1, 1, 56, 9, '4W PACKAGE', '3001/250428422/00/000', 'GJ01KF0477', 'Ahmedabad', 'Maruti Alto lxi', 'petrol', '1970-01-01', '1970-01-01', NULL, NULL, '435', '2719', '0', '3154', '567.72', '3721.72', 'Online', '', '', NULL, 'Sneha', 'Hdfc Ergo', '2311 2041 7287 2500 000', '', '', '', '', '', 0, NULL, NULL),
(75, '01/06/2022', '638', '1970-01-01', 'Rollover', 1, 1, 1, 52, 9, 'gcv pkg', '3003/250526139/00/000', 'GJ27TT7180', 'Ahmedabad East', 'Eicher Motors Pro 1110 H', 'Diesel', '1970-01-01', '1970-01-01', NULL, NULL, '2303', '27286', '0', '29589', '3695.00', '33284.00', 'Online', '', '', NULL, 'Karn', 'Bajaj Allianz', 'OG-22-2202-1803-00002619', '', '', '', '', '', 0, NULL, NULL),
(76, '01/06/2022', '639', '1970-01-01', 'Rollover', 1, 1, 1, 51, 9, 'gcv pkg', '3003/250526328/00/000', 'GJ27TT7111', 'Ahmedabad East', 'Eicher Motors Pro 1110 H', 'Diesel', '1970-01-01', '1970-01-01', NULL, NULL, '2303', '27286', '0', '29589', '3695.00', '33284.00', 'Online', '', '', NULL, 'Karn', 'Bajaj Allianz', 'OG-22-2202-1803-00002620', '', '', '', '', '', 0, NULL, NULL),
(77, '01/06/2022', '662', '1970-01-01', 'Fresh', 1, 1, 1, 57, 15, 'Health', '7000070479-00', 'Na', 'na', 'na', 'na', '1970-01-01', '1970-01-01', NULL, NULL, '0', '39930.96', '0', '39930.96', '7187.57', '47118.53', 'ONLINE', '', '', NULL, 'Dhvani', '', '', '', '', '', '', '', 0, NULL, NULL),
(78, '01/06/2022', '719', '1970-01-01', 'Fresh', 1, 1, 1, 57, 15, 'Super top up', '0239469646', 'NA', 'na', 'na', 'na', '1970-01-01', '1970-01-01', NULL, NULL, '0', '6825.84', '0', '6825.84', '1228.65', '8054.49', 'Online', '', '', NULL, 'Dhvani', '', '', '', '', '', '', '', 0, NULL, NULL),
(79, '01/06/2022', '721', '1970-01-01', 'Fresh', 1, 1, 1, 58, 15, 'Gcv PKG', '0163420351', 'New', 'Ahmedabad', 'Eicher pro 2049 Diesel', 'Diesel', '1970-01-01', '1970-01-01', NULL, NULL, '7120.83', '16319', '0', '23439.83', '3256.00', '26695.83', 'Online', '', '', NULL, 'Karn', '', '', '', '', '', '', '', 0, NULL, NULL),
(80, '01/06/2022', '765', '1970-01-01', 'Fresh', 1, 1, 1, 59, 15, '4w Bundled (1+3)', '6200419694', 'New', 'Ahmedabad', 'HYUNDAI / GRAND I10 NIOS / SPORTZ AMT 1.2 KAPPA VTVT / HATCH BACK', 'Petrol', '1970-01-01', '1970-01-01', NULL, NULL, '12352.31', '12665', '116', '25133.31', '4524.00', '29657.31', 'Online', '', '', NULL, 'Mayank', '', '', '', '', '', '', '', 0, NULL, NULL),
(81, '01/06/2022', '801', '1970-01-01', 'Rollover', 1, 1, 1, 55, 15, 'gcv pkg', '0163433939', 'GJ01BY6254', 'Ahmedabad', 'EICHER 10.50 (OPEN) ­ E', 'Diesel', '1970-01-01', '1970-01-01', NULL, NULL, '2336.61', '35583', '0', '37919.61', '4706.00', '42625.61', 'ONLINE', '', '', NULL, 'Karn', 'Bajaj Allianz', 'OG­22­2202­1803­00002862', '', '', '', '', '', 0, NULL, NULL),
(82, '01/06/2022', '814', '1970-01-01', 'Fresh', 1, 1, 1, 60, 13, '2w pkg', '827522223120000024', 'GJ06KQ3772', 'vadodara', 'Triumph / Tiger / 800 Xrx', 'Petrol', '1970-01-01', '1970-01-01', NULL, NULL, '9729.280000000001', '3179', '0', '12908.28', '2323.49', '15231.77', 'ONLINE', '', '', NULL, 'Missing', '', '', '', '', '', '', '', 0, NULL, NULL),
(83, '01/06/2022', '827', '1970-01-01', 'Rollover', 1, 3, 3, 61, 5, '4W PACKAGE', '900331331', 'GJ01HW0599', 'ahmedabad', 'Maruti Swift Vxi New', 'Petrol', '1970-01-01', '1970-01-01', NULL, NULL, '0', '182.2', '0', '182.2', '32.80', '215.00', 'Online', '', '', NULL, 'Karn', 'Reliance', '162422123110033434', '', '', '', '', '', 0, NULL, NULL),
(84, '01/07/2022', '33', '1970-01-01', 'Rollover', 1, 1, 1, 62, 15, '4w Od', '6200409721', 'GJ01WC5001', 'Ahmedabad', 'TATA MOTORS / SAFARI / XZA PLUS ADVENTURE / SUV', 'Diesel', '2007-06-22', '2007-05-23', NULL, NULL, '33202.02', '0', '116', '33318.02', '5997.24', '39315.26', 'Online', '', '', NULL, 'Karn', 'Future GIC', 'TMA64848', '', '', '', '', '', 0, NULL, NULL),
(85, '01/07/2022', '59', '1970-01-01', 'ROLLOVER', 1, 1, 1, 63, 9, '2w pkg', '3005/251162904/00/000', 'GJ01PR0155', 'Ahmedabad', 'Royal Enfield Bullet 350', 'Petrol', '2007-06-22', '2007-05-23', NULL, NULL, '289', '1816', '0', '2105', '378.9', '2483.9', 'CHEQUE', '', '', NULL, 'Karn', 'Reliance', '160222123120043499', '', '', '', '', '', 0, NULL, NULL),
(86, '01/07/2022', '105', '2022-01-07', 'Fresh', 1, 1, 1, 58, 16, 'Fire', '21250111224300000003', 'Na', 'Na', 'Na', 'NA', '2007-01-22', '1970-01-01', NULL, NULL, '305389', '161000', '0', '466389', '83950.02', '550339.0', 'Cheque', '', '', NULL, 'Dhvani', '', '', '', '', '', '', '', 0, NULL, NULL),
(87, '01/07/2022', '106', '2022-01-07', 'Fresh', 1, 1, 1, 58, 16, 'Fire', '21250111220400000001', 'Na', 'Na', 'Na', 'NA', '2007-01-22', '1970-01-01', NULL, NULL, '0', '1613000', '0', '1613000', '290340.00', '1903340.0', 'Cheque', '', '', NULL, 'Dhvani', '', '', '', '', '', '', '', 0, NULL, NULL),
(88, '01/07/2022', '109', '2022-01-07', 'Fresh', 1, 1, 1, 64, 16, 'Fire', '21250111228000000138', 'Na', 'Na', 'Na', 'NA', '2007-01-22', '1970-01-01', NULL, NULL, '7200', '1380', '0', '8580', '1544.4', '10124.4', 'Cheque', '', '', NULL, 'DHvani', '', '', '', '', '', '', '', 0, NULL, NULL),
(89, '01/07/2022', '233', '2022-07-07', 'Rollover', 1, 1, 1, 65, 9, '2w pkg', '3005/251850061/00/000', 'GJ01PV0477', 'Ahmedabad', 'TVS jupiter', 'Petrol', '2007-08-22', '2007-07-23', NULL, NULL, '204', '1089', '0', '1293', '232.7', '1525.7', 'Online', '', '', NULL, 'Mayank', 'Hdfc Ergo', '2312 2042 0997 9800 000', '', '', '', '', '', 0, NULL, NULL),
(90, '01/07/2022', '234', '2022-07-07', 'Rollover', 1, 1, 1, 66, 9, '4W PACKAGE', '3001/251788018/00/000', 'GJ01HX0820', 'Ahmedabad', 'Maruti Ignis Sigma MT', 'Petrol', '2007-09-22', '2007-08-23', NULL, NULL, '9563', '4091', '0', '13654', '2457.7', '16111.7', 'Online', '', '', NULL, 'Mayank', 'Tata Aig', '3101272742', '', '', '', '', '', 0, NULL, NULL),
(91, '01/07/2022', '298', '2022-08-07', 'Fresh', 1, 1, 1, 58, 15, 'GCv pkg', '0163456839', 'New', 'Nelamangala', 'EICHER PRO 2049 ­ DIESEL', 'Diesel', '2007-07-22', '2007-06-23', NULL, NULL, '7144.84', '16319', '0', '23463.84', '3261', '26724.8', 'Online', '', '', NULL, 'Karn', '', '', '', '', '', '', '', 0, NULL, NULL),
(92, '01/07/2022', '299', '2022-08-07', 'Fresh', 1, 1, 1, 58, 15, 'GCv pkg', '0163456904', 'New', 'Howrah', 'EICHER PRO 2049 ­ DIESEL', 'Diesel', '2007-07-22', '2007-06-23', NULL, NULL, '7144.84', '16319', '0', '23463.84', '3261', '26724.8', 'Online', '', '', NULL, 'Karn', '', '', '', '', '', '', '', 0, NULL, NULL),
(93, '01/07/2022', '306', '2022-09-07', 'Rollover', 1, 1, 1, 58, 15, 'GCv pkg', '0163458720', 'GJ01HT5436', 'Ahmedabad', 'Eicher Pro 5019 container', 'Diesel', '2007-10-22', '2007-09-23', NULL, NULL, '13853.52', '35571', '0', '49424.52', '6778.0', '56202.5', 'Online', '', '', NULL, 'Karn', 'Bajaj Alianz', 'OG­22­2202­1803­00003208', '', '', '', '', '', 0, NULL, NULL),
(94, '01/07/2022', '325', '2022-11-07', 'Rollover', 1, 1, 1, 67, 15, '4W PACKAGE', '6200442296', 'GJ01KQ8684', 'Ahmedabad', 'MAHINDRA & MAHINDRA / VERITO / 1.5 D4 PLAY BS III / SEDAN', 'Diesel', '2007-10-22', '2007-09-23', NULL, NULL, '969.47', '3416', '0', '4385.5', '789.4', '5174.9', 'Online', '', '', NULL, 'Sneha', 'United india', '22300031210160314874', '', '', '', '', '', 0, NULL, NULL),
(95, '01/07/2022', '514', '1970-01-01', 'Rollover', 1, 1, 1, 68, 15, '4W PACKAGE', '6200467581', 'GJ01HX0421', 'Ahmedabad', 'VOLVO / S90 / INSCRIPTION LUXURY / SEDAN', 'Diesel', '1970-01-01', '1970-01-01', NULL, NULL, '55887.27', '8572', '116', '64575.3', '11623.55', '76198.8', 'CHEQUE', '', '', NULL, 'Karn', 'Future GIC', 'VOL00190', '', '', '', '', '', 0, NULL, NULL),
(96, '01/07/2022', '570', '1970-01-01', 'Rollover', 1, 1, 1, 58, 15, 'GCv pkg', '0163493882', 'GJ27X6199', 'Ahmedabad East', 'ASHOK LEYLAND 2518 ­ XL BSIV', 'Diesel', '1970-01-01', '1970-01-01', NULL, NULL, '15915.7', '44220', '0', '60135.7', '8186.00', '68321.7', 'Online', '', '', NULL, 'Karn', 'Bajaj Alianz', 'OG­22­2202­1803­00003765', '', '', '', '', '', 0, NULL, NULL),
(97, '01/07/2022', '571', '1970-01-01', 'Rollover', 1, 1, 1, 58, 15, 'GCv pkg', '0163493888', 'GJ27X6536', 'Ahmedabad East', 'ASHOK LEYLAND 2518 ­ XL BSIV', 'Diesel', '1970-01-01', '1970-01-01', NULL, NULL, '14898.29', '44220', '0', '59118.3', '8004', '67122.3', 'Online', '', '', NULL, 'Karn', 'Bajaj Alianz', 'OG­22­2202­1803­00003744', '', '', '', '', '', 0, NULL, NULL),
(98, '01/07/2022', '643', '1970-01-01', 'Rollover', 1, 1, 1, 69, 15, '4W PACKAGE', '6200481225', 'GJ01RK9741', 'Ahmedabad', 'HYUNDAI / CRETA / VTVT 1.6 S / SUV', 'Petrol', '1970-01-01', '1970-01-01', NULL, NULL, '6512.49', '8572', '0', '15084.5', '2715.21', '17799.7', 'ONLINE', '', '', NULL, 'Karn', 'The oriental insuarance', '141500/31/2022/826', '', '', '', 'Additional discount 80%', '', 0, NULL, NULL),
(99, '01/07/2022', '786', '1970-01-01', 'Rollover', 1, 1, 1, 58, 15, 'gcv pkg', '0163513022', 'KA52B2268', 'NELAMANGALA', 'EICHER PRO 2095 ­ XP  ', 'Diesel', '1970-01-01', '1970-01-01', NULL, NULL, '10200.34', '27456', '0', '37656.3', '5147.00', '42803.3', 'onliine', '', '', NULL, 'Karn', 'Bajaj Allianz', 'OG­22­2202­1803­00003918 ', '', '', '', '', '', 0, NULL, NULL),
(100, '01/07/2022', '853', '1970-01-01', 'FRESH', 1, 1, 1, 70, 9, '2w pkg', '3005/253942280/00/B00', 'GJ27CS5566', 'Ahmedabad East', 'HONDA MOTORCYCLE ACTIVA 5G DLX', 'Petrol', '1970-01-01', '1970-01-01', NULL, NULL, '469', '1089', '0', '1558.0', '280.44', '1838.4', 'Online', '', '', NULL, 'karn', '', '', '', '', '', '', '', 0, NULL, NULL),
(101, '01/07/2022', '866', '1970-01-01', 'Rollover', 1, 1, 1, 71, 9, '2w pkg', '3005/253584520/00/B00', 'GJ05PA6487', 'Surat', 'Suzuki / ACCESS', 'Petrol', '1970-01-01', '1970-01-01', NULL, NULL, '154', '1089', '0', '1243.0', '223.74', '1466.7', 'Online', '', '', NULL, 'Missing', 'Tata Aig', '0162205177', '', '', '', '', '', 0, NULL, NULL),
(102, '01/08/2022', '223', '2022-04-08', 'Fresh', 1, 1, 1, 72, 16, 'Health', '21250134222800000234', 'NA', 'NA', 'NA', 'NA', '2008-06-22', '2008-05-23', NULL, NULL, '0', '11726', '0', '11726', '2110.68', '13836.7', 'ONLINE', '', '', NULL, '', '', '', '', '', 'Done', '', '', 0, NULL, NULL),
(103, '01/08/2022', '229', '2022-05-08', 'Rollover', 1, 1, 1, 73, 9, '4W PACKAGE', '3001/254340688/00/000', 'GJ01RF8655', 'Ahmedabad', 'Honda Brio S', 'Petrol', '1970-01-01', '1970-01-01', NULL, NULL, '1141', '4091', '0', '5232', '941.76', '6173.8', 'ONLINE', '', '', NULL, 'Karn', 'Tata Aig', '3101368260', '', '', 'Done', '', '', 0, NULL, NULL),
(104, '01/08/2022', '354', '2022-09-08', 'Rollover', 1, 1, 1, 10, 6, 'wc', 'D072323420', 'NA', 'NA', 'NA', 'NA', '2008-09-22', '2012-08-22', NULL, NULL, '0', '9415.99', '0', '9415.99', '1694.9', '11110.87', 'ONLINE', '', '', NULL, 'karn', '', '', '', '', 'Done', '', '', 0, NULL, NULL),
(105, '01/08/2022', '355', '2022-09-08', 'Rollover', 1, 1, 1, 10, 6, 'wc', 'D072323265', 'NA', 'NA', 'NA', 'NA', '2008-09-22', '2012-08-22', NULL, NULL, '0', '7622.13', '0', '7622.13', '1371.98', '8994.11', 'ONLINE', '', '', NULL, 'karn', '', '', '', '', 'Done', '', '', 0, NULL, NULL),
(106, '01/08/2022', '356', '2022-09-08', 'Rollover', 1, 1, 1, 9, 6, 'wc', 'D072323114', 'NA', 'NA', 'NA', 'NA', '2008-09-22', '2012-08-22', NULL, NULL, '0', '16686.45', '0', '16686.45', '3003.56', '19690.01', 'ONLINE', '', '', NULL, 'karn', '', '', '', '', 'Done', '', '', 0, NULL, NULL),
(107, '01/08/2022', '357', '2022-09-08', 'Rollover', 1, 1, 1, 10, 6, 'wc', 'D072323502', 'NA', 'NA', 'NA', 'NA', '2008-09-22', '2012-08-22', NULL, NULL, '0', '20477.93', '0', '20477.93', '3686.03', '24163.96', 'ONLINE', '', '', NULL, 'karn', '', '', '', '', 'Done', '', '', 0, NULL, NULL),
(108, '01/08/2022', '510', '1970-01-01', 'FRESH', 1, 1, 1, 74, 9, 'HEALTH', '4016 X 225230710 01 000', 'NA', 'NA', 'NA', 'NA', '1970-01-01', '1970-01-01', NULL, NULL, '0', '509294', '0', '509294', '91672.92', '600966.92', 'ONLINE', '', '', NULL, 'karn', '', '', '', '50', '', '', '', 0, NULL, NULL),
(109, '01/08/2022', '516', '1970-01-01', 'Rollover', 1, 1, 1, 75, 12, 'GCV pkg', 'P0023200007/4103/103290', 'GJ20V6026', 'DAHOD', 'EICHER PRO 1110 H FSD/TRUCK', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '916', '27296', '0', '28212', '3447.00', '31659.00', 'ONLINE', '', '', NULL, 'karn', 'BAJAJ ALLIANZ', 'OG-22-2202-1803-00004624', '', '', '', '', '', 0, NULL, NULL),
(110, '01/08/2022', '545', '1970-01-01', 'Rollover', 1, 1, 1, 76, 6, '2W PKG', 'D073131393', 'GJ15BS0431', 'VALSAD', 'HERO MOTOCORP SPLENDOR PRO/DRUM SELF\nCAST', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '262.82', '1044', '0', '1306.82', '235.23', '1542.05', 'ONLINE', '', '', NULL, 'Ajay', 'IFFCO Tokio', 'MH955771', '', '', '', '', '', 0, NULL, NULL),
(111, '01/08/2022', '685', '1970-01-01', 'Rollover', 1, 1, 1, 77, 9, '2W OD', '3005/O/255788660/00/000', 'GJ27DJ9492', 'Ahmedabad East', 'HONDA MOTORCYCL ACTIVA 6G DLX', 'petrol', '2009-01-22', '1970-01-01', NULL, NULL, '983', '0', '0', '983', '176.94', '1159.94', 'ONLINE', '', '', NULL, 'Ajay', 'HDFC ERGO / ICICI LOMBARD', '2312 2042 5358 4100 000 /  3005/2011445087/00/0000018168', '', '', '', '', '', 0, NULL, NULL),
(112, '01/08/2022', '751', '1970-01-01', 'Rollover', 1, 1, 1, 78, 12, 'GCV pkg', 'P0023200007/4103/103505', 'GJ20V6190', 'DAHOD', 'EICHER PRO 3013 STANDARD/TRUCK', 'DIesel', '1970-01-01', '1970-01-01', NULL, NULL, '1015', '35423', '0', '36438', '4440.06', '40878.06', 'CHEQUE', '', '', NULL, 'karn', 'BAJAJ ALLIANZ', 'OG-22-2202-1803-00004838', '', '', '', '', '', 0, NULL, NULL),
(113, '01/08/2022', '835', '1970-01-01', 'Rollover', 1, 1, 1, 79, 7, '4W PACKAGE', '2302 2048 6159 7600 000', 'HR20AK4377', 'Hisar', 'MARUTI SWIFT-LXi (O)', 'petorl', '1970-01-01', '1970-01-01', NULL, NULL, '6276', '4041', '0', '10317', '1857.06', '12174.06', 'ONLINE', '', '', NULL, 'karn', 'Royal Sundaram', 'VPC1506442000100', '', '', '', '', '', 0, NULL, NULL),
(114, '01/08/2022', '1047', '2022-10-08', 'Rollover', 1, 1, 1, 80, 9, '2W PKG', '3005/254829599/00/000', 'GJ27BD7055', 'ahmedabad', 'HONDA MOTORCYCLE ACTIVA 3G', 'petrol', '1970-01-01', '1970-01-01', NULL, NULL, '112', '1089', '0', '1201', '216.18', '1417.18', 'ONLINE', '', '', NULL, '', 'BAJAJ ALIANZ', 'OG-22-2202-1802-00021146', '', '', '', '', '', 0, NULL, NULL),
(115, '01/09/2022', '65', '2022-03-09', 'Rollover', 1, 1, 1, 30, 15, 'GCV PACKAGE', '0163620915 00 00', 'GJ27X6793', 'Ahmedabad East', 'ASHOK LEYLAND 2518 ­ XL BSIV  CONTAINER', 'DIESEL', '2009-04-22', '2009-03-23', NULL, NULL, '14015.2', '44226', '0', '58241.2', '7846', '66087.20', 'ONLINE', '', '', NULL, '', 'BAJAJ ALLIANZ', 'OG­22­2202­1803­00005314', '', '', '', '', '', 0, NULL, NULL),
(116, '01/09/2022', '66', '2022-03-09', 'Rollover', 1, 1, 1, 81, 13, 'GCV PACKAGE', '162422223340022780', 'GJ20V7055', 'Dahod', 'Ashok Leyland/Boss & 1212 Le', 'DIESEL', '2009-04-22', '2009-03-23', NULL, NULL, '927.41', '35473', '0', '36400.41', '4434', '40834.41', 'ONLINE', '', '', NULL, '', 'BAJAJ ALLIANZ', 'OG-22-2202-1803-00005181', '', '', '', '', '', 0, NULL, NULL),
(117, '01/09/2022', '67', '2022-03-09', 'Rollover', 1, 1, 1, 30, 15, 'GCV PACKAGE', ' 0163620908 00 00', 'GJ27X6828', 'Ahmedabad East', 'ASHOK LEYLAND 2518 ­ XL BSIV  CONTAINER', 'DIESEL', '2009-04-22', '2009-03-23', NULL, NULL, '14012.35', '44226', '0', '58238.35', '7846', '66084.35', 'ONLINE', '', '', NULL, '', 'BAJAJ ALLIANZ', 'OG­22­2202­1803­00005224', '', '', '', '', '', 0, NULL, NULL),
(118, '01/09/2022', '72', '2022-05-09', 'FRESH', 1, 1, 1, 82, 6, 'HEALTH', 'D075151785', 'NA', 'NA', 'NA', 'NA', '2009-05-22', '2009-04-23', NULL, NULL, '0', '11151', '0', '11151', '2007.18', '13158.18', 'ONLINE', '', '', NULL, 'Yakruti', '', '', '', '', '', '', '', 0, NULL, NULL),
(119, '01/09/2022', '147', '2022-07-09', 'Rollover', 1, 1, 1, 83, 15, '4W PACKAGE', '6200599452', 'GJ01KV9975', 'Ahmedabad', 'MARUTI / ERTIGA / SMART HYBRID ZXI AT / MUV', 'petrol', '2009-09-22', '2009-08-23', NULL, NULL, '14399.49', '4191', '116', '18706.49', '3367.17', '22073.66', 'ONLINE', '', '', NULL, 'Ajay', 'LIBERTY', '201140020119550002100000', '', '', '', '', '', 0, NULL, NULL),
(120, '01/09/2022', '274', '2022-09-09', 'FRESH', 1, 1, 1, 84, 6, 'WC', 'D075406306', 'NA', 'NA', 'NA', 'NA', '2009-09-22', '2010-08-22', NULL, NULL, '0', '1731.65', '0', '1731.65', '311.70', '2043.35', 'ONLINE', '', '', NULL, 'YAKRUTI', '', '', '', '', '', '', '', 0, NULL, NULL),
(121, '01/09/2022', '281', '1970-01-01', 'FRESH', 1, 1, 1, 85, 9, 'MARINE ', '2005/257743985/00/000', 'NA', 'NA', 'NA', 'NA', '1970-01-01', '1970-01-01', NULL, NULL, '0', '931.04', '0', '931.04', '167.59', '1098.63', 'ONLINE', '', '', NULL, 'KARN', '', '', '', '1', '', '', '', 0, NULL, NULL),
(122, '01/09/2022', '453', '1970-01-01', 'ROLLOVER', 1, 1, 1, 86, 9, '2W PACKAGE', '3005/258639520/00/000', 'GJ01MQ3299', 'AHMEDABAD', 'HONDA MOTORCYCLE ACTIVA DLX', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '100', '714', '0', '814', '146.52', '960.52', 'ONLINE', '', '', NULL, 'AJAY', 'BAJAJ ALIANZ', 'OG-20-2202-1843-00000918', '', '', '', '', '', 0, NULL, NULL),
(123, '01/09/2022', '456', '1970-01-01', 'ROLLOVER', 1, 1, 1, 87, 9, '2W PACKAGE', '3005/258939313/00/B00', 'GJ27J7055', 'AHMEDABAD', 'HONDA MOTORCYCLE ACTIVA DLX', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '118', '1089', '0', '1207', '217.26', '1424.26', 'ONLINE', '', '', NULL, 'KARN', 'BAJAJ ALIANZ', '\'OG-22-2202-1802-00029809\'', '', '', '', '', '', 0, NULL, NULL),
(124, '01/09/2022', '521', '2022-12-09', 'ROLLOVER', 1, 1, 1, 88, 1, '2W OD', 'OG-23-2202-1871-00006043', 'GJ27DC9842', 'AHMEDABAD', 'HONDA - ACTIVA  5G STD', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '1029', '0', '0', '1029', '185.22', '1214.22', 'ONLINE', '', '', NULL, 'AJAY', 'ICICI LOMBARD', '3005/o/2011460953/00/0000002103 /', '', '', '', '', '', 0, NULL, NULL),
(125, '01/09/2022', '783', '1970-01-01', 'ROLLOVER', 1, 1, 1, 89, 9, '2W PACKAGE', '3005/260138540/00/000', 'GJ01MA7921', 'AHMEDABAD', 'HONDA MOTORCYCLE SHINE', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '342', '1089', '0', '1431', '257.58', '1688.58', 'ONLINE', '', '', NULL, 'AJAY', 'BAJAJ ALIANZ', 'OG-22-2202-1802-00032004', '', '', '', '', '', 0, NULL, NULL),
(126, '01/09/2022', '784', '1970-01-01', 'ROLLOVER', 1, 1, 1, 90, 9, '2W PACKAGE', '3005/260138872/00/000', 'GJ01JD7921', 'AHMEDABAD', 'HONDA MOTORCYCLE ACTIVA', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '101', '1089', '0', '1190', '214.2', '1404.2', 'ONLINE', '', '', NULL, 'AJAY', 'BAJAJ ALIANZ', 'OG-22-2202-1802-00032060', '', '', '', '', '', 0, NULL, NULL),
(127, '01/09/2022', '855', '1970-01-01', 'FRESH', 1, 1, 1, 91, 6, 'WC', 'D077709036', 'NA', 'NA', 'NA', 'NA', '2010-09-22', '2010-08-23', NULL, NULL, '0', '27831.52', '0', '27831.52', '5009.6736', '32841.1936', 'ONLINE', '', '', NULL, 'YAKURTI', '', '', '', '', '', '', '', 0, NULL, NULL),
(128, '01/09/2022', '885', '1970-01-01', 'ROLLOVER', 1, 1, 1, 92, 15, 'GCV PACKAGE', ' 0163693319 ', 'RJ06GB7647', 'BHILWARA ', 'EICHER 30.25 ­ ­  OPEN', 'DIESEL', '2010-01-22', '1970-01-01', NULL, NULL, '2879.26', '44220', '0', '47099.26', '5841', '52940.26', 'ONLINE', '', '', NULL, 'KARN', 'RELIANCE', 'RL2109290174', '', '', '', '', '', 0, NULL, NULL),
(129, '01/09/2022', '886', '1970-01-01', 'ROLLOVER', 1, 1, 1, 92, 15, 'GCV PACKAGE', ' 0163693318 ', 'RJ06GB7648', 'BHILWARA ', 'EICHER 30.25 ­ ­  OPEN', 'DIESEL', '2010-01-22', '1970-01-01', NULL, NULL, '3017.19', '44220', '0', '47237.19', '5866', '53103.19', 'ONLINE', '', '', NULL, 'KARN', 'RELIANCE', 'RL2109290178', '', '', '', '', '', 0, NULL, NULL),
(130, '01/09/2022', '998', '1970-01-01', 'FRESH', 1, 1, 1, 30, 15, 'GCV PACKAGE', '0163702457', 'NEW', 'AHMEDABAD', 'EICHER PRO 3019 ­ TRUCK', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '13651.34', '35583', '0', '49234.34', '6744', '55978.3', 'ONLINE', '', '', NULL, 'KARN', '', '', '', '', '', '', '', 0, NULL, NULL),
(131, '01/09/2022', '999', '1970-01-01', 'FRESH', 1, 1, 1, 30, 15, 'GCV PACKAGE', '0163702442', 'NEW', 'AHMEDABAD', 'EICHER PRO 3019 ­ TRUCK', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '13651.34', '35583', '0', '49234.34', '6744', '55978.34', 'ONLINE', '', '', NULL, 'KARN', '', '', '', '', '', '', '', 0, NULL, NULL),
(132, '01/09/2022', '1000', '1970-01-01', 'FRESH', 1, 1, 1, 30, 15, 'GCV PACKAGE', '0163702424', 'NEW', 'AHMEDABAD', 'EICHER PRO 3019 ­ TRUCK', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '13651.34', '35583', '0', '49234.34', '6744', '55978.34', 'ONLINE', '', '', NULL, 'KARN', '', '', '', '', '', '', '', 0, NULL, NULL),
(133, '01/09/2022', '1001', '1970-01-01', 'FRESH', 1, 1, 1, 30, 15, 'GCV PACKAGE', '0163702381', 'NEW', 'AHMEDABAD', 'EICHER PRO 3019 ­ TRUCK', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '13651.34', '35583', '0', '49234.34', '6744', '55978.34', 'ONLINE', '', '', NULL, 'KARN', '', '', '', '', '', '', '', 0, NULL, NULL),
(134, '01/09/2022', '1002', '1970-01-01', 'FRESH', 1, 1, 1, 30, 15, 'GCV PACKAGE', '0163702423', 'NEW', 'AHMEDABAD', 'EICHER PRO 3019 ­ TRUCK', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '13651.34', '35583', '0', '49234.34', '6744', '55978.34', 'ONLINE', '', '', NULL, 'KARN', '', '', '', '', '', '', '', 0, NULL, NULL),
(135, '01/09/2022', '1006', '2022-03-10', 'FRESH', 1, 1, 1, 30, 15, 'GCV PACKAGE', '0163707691', 'NEW', 'YELAHANKA, BANGALORE', 'EICHER PRO 2049 ­ DIESEL', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '6673.04', '16319', '0', '22992.04', '3176', '26168.04', 'ONLINE', '', '', NULL, 'KARN', '', '', '', '', '', '', '', 0, NULL, NULL),
(136, '01/09/2022', '1007', '2022-03-10', 'FRESH', 1, 1, 1, 30, 15, 'GCV PACKAGE', '0163707705', 'NEW', 'YELAHANKA, BANGALORE', 'EICHER PRO 2110 ­ TRUCK', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '9883.84', '27456', '0', '37339.84', '5090', '42429.84', 'ONLINE', '', '', NULL, 'KARN', '', '', '', '', '', '', '', 0, NULL, NULL),
(137, '01/09/2022', '1008', '2022-03-10', 'FRESH', 1, 1, 1, 30, 15, 'GCV PACKAGE', '0163707711', 'NEW', 'YELAHANKA, BANGALORE', 'EICHER PRO 2080 ­ XPT', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '9587.879999999999', '27456', '0', '37043.88', '5037', '42080.88', 'ONLINE', '', '', NULL, 'KARN', '', '', '', '', '', '', '', 0, NULL, NULL),
(138, '01/09/2022', '1025', '1970-01-01', 'FRESH', 1, 1, 1, 82, 1, 'PA', '12-8428-0000225366-00', 'NA', 'NA', 'NA', 'NA', '1970-01-01', '1970-01-01', NULL, NULL, '0', '1855', '0', '1855', '333.9', '2188.9', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(139, '01/09/2022', '1026', '1970-01-01', 'ROLLOVER', 1, 1, 1, 93, 1, '2W OD', 'OG-23-2202-1871-00006610', 'GJ01UV7120', 'AHMEDABAD', 'HONDA - CB SHINE', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '887', '0', '0', '887', '159.66', '1046.66', 'ONLINE', '', '', NULL, '', 'ICICI Lombard', '3005/2011448379/00/0000009453', '', '', '', '', '', 0, NULL, NULL),
(140, '01/09/2022', '1027', '1970-01-01', 'FRESH', 1, 1, 1, 93, 1, 'CPA od', 'OG-23-2202-1869-00000897', 'GJ01UV7120', 'AHMEDABAD', 'HONDA - CB SHINE', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '0', '331', '0', '331', '59.58', '390.58', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(141, '01/09/2022', '1078', '1970-01-01', 'ROLLOVER', 1, 1, 1, 94, 9, '2W PACKAGE', '3005/257950429/00/000', 'GJ01NH0239', 'Ahmedabad', 'TVS SCOOTY PEP PLUS', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '73', '1089', '0', '1162', '209.16', '1371.16', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(142, '01/09/2022', '1079', '1970-01-01', 'ROLLOVER', 1, 1, 1, 94, 9, '2W PACKAGE', '3005/257950466/00/000', 'GJ01PJ1016', 'Ahmedabad', 'HONDA MOTORCYCLE ACTIVA 3G', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '101', '1089', '0', '1190', '214.2', '1404.2', 'ONLINE', '', '', NULL, '', 'BAJAJ ALIANZ', 'OG-22-2202-1802-00028508\'', '', '', '', '', '', 0, NULL, NULL),
(143, '01/09/2022', '1080', '1970-01-01', 'ROLLOVER', 1, 1, 1, 95, 9, '2W PACKAGE', '3005/258051002/00/000', 'GJ01JR4877', 'Ahmedabad', 'Suzuki ACCESS 125 BT', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '220', '1089', '0', '1309', '235.62', '1544.62', 'ONLINE', '', '', NULL, '', 'RELIANCE', '160222123480002533', '', '', '', '', '', 0, NULL, NULL),
(144, '01/10/2022', '29', '2022-06-10', 'Rollover', 1, 1, 1, 96, 12, 'GCV PACKAGE', 'P0023200007/4103/104331', 'GJ20V4146', 'DAHOD', 'EICHER PRO 1110 H FSD/TRUCK', 'DIESEL', '2022-10-07', '2023-10-06', NULL, NULL, '1337', '27236', '0', '28573', '5143.14', '33716.14', 'CHEQUE', '', '', NULL, '', '', 'OG-22-2202-1803-00006617', '', '', '', '', '', 0, NULL, NULL),
(145, '01/10/2022', '30', '2022-07-10', 'Rollover', 1, 1, 1, 97, 6, 'GCV TP', 'D078467302', 'GJ10W6294', 'JAMNAGAR', 'ASHOK LEYLAND 2214/1S HIGH DECK', 'DIESEL', '2022-10-08', '2023-10-07', NULL, NULL, '0', '44380', '0', '44380', '7988.40', '52368.40', 'ONLINE', '', '', NULL, '', '', '161022123340011732', '', '', '', '', '', 0, NULL, NULL);
INSERT INTO `customer_insurances` (`id`, `month`, `sr_no`, `issue_date`, `bus_type`, `branch_id`, `broker_id`, `relationship_manager_id`, `customer_id`, `insurance_company_id`, `type_of_policy`, `policy_no`, `registration_no`, `rto`, `make_model`, `fuel_type`, `start_date`, `expired_date`, `mobile_no`, `email_id`, `od_premium`, `tp_premium`, `rsa`, `net_premium`, `gst`, `final_premium_with_gst`, `mode_of_payment`, `cheque_no`, `premium`, `insurance_status`, `extra1`, `issued_by`, `extra2`, `extra3`, `extra4`, `extra5`, `extra6`, `extra7`, `status`, `created_at`, `updated_at`) VALUES
(146, '01/10/2022', '31', '2022-07-10', 'Renewal', 1, 1, 1, 98, 1, 'FW PACKAGE', 'OG-23-2202-1801-00016770', 'GJ01HU8204', 'AHMEDABAD', 'MARUTI ERTIGA VDI SMART HYBRID', 'DIESEL', '2022-10-09', '2023-10-08', NULL, NULL, '8869', '4147', '0', '13016', '2342.88', '15358.88', 'ONLINE', '', '', NULL, '', '', '3101523079', '', '', '', '', '', 0, NULL, NULL),
(147, '01/10/2022', '32', '2022-08-10', 'Rollover', 1, 1, 1, 99, 1, 'FW OD', 'OG-23-2202-1870-00001729', 'GJ01WD1640', 'AHMEDABAD', 'VOLKSWA GEN - POLO 1.0 PETROL COMFORTLINE', 'petrol', '2022-10-08', '2023-10-07', NULL, NULL, '9512', '0', '0', '9512', '1712.16', '11224.16', 'ONLINE', '', '', NULL, '', '', '0404003121P106326707', '', '', '', '', '', 0, NULL, NULL),
(148, '01/10/2022', '38', '2022-07-10', 'FRESH', 1, 1, 1, 100, 15, 'GCV PACKAGE', '0163717638', 'New', 'NELAMANGALA', 'EICHER PRO 2114  XP ­ TRUCK', 'DIESEL', '2022-10-06', '2023-10-05', NULL, NULL, '12600.64', '35583', '0', '48183.64', '8673.06', '56856.70', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(149, '01/10/2022', '39', '2022-11-10', 'FRESH', 1, 1, 1, 100, 15, 'GCV PACKAGE', '0162470439 01', 'WB11F1958', 'HOWRAH', 'EICHER  PRO 2049', 'DIESEL', '2022-10-09', '2023-10-08', NULL, NULL, '6266.17', '16319', '0', '22585.17', '4065.33', '26650.50', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(150, '01/10/2022', '40', '2022-11-10', 'FRESH', 1, 1, 1, 100, 15, 'GCV PACKAGE', '0162468910 01', 'TS08UJ2678', 'Rangaredd', 'EICHER PRO 2049', 'CNG', '2022-10-09', '2023-10-08', NULL, NULL, '6750.86', '16379', '0', '23129.86', '4163.37', '27293.23', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(151, '01/10/2022', '41', '2022-11-10', 'FRESH', 1, 1, 1, 100, 15, 'GCV PACKAGE', '0162470432 01', 'RJ14GN3621', 'JAIPUR ', 'EICHER PRO 2049', 'DIESEL', '2022-10-09', '2023-10-08', NULL, NULL, '6282.78', '16319', '0', '22601.78', '4068.32', '26670.10', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(152, '01/10/2022', '42', '2022-11-10', 'FRESH', 1, 1, 1, 100, 15, 'GCV PACKAGE', '0162468914 01', 'MH04KU3277', 'THANE ', 'EICHER PRO 2049', 'CNG', '2022-10-09', '2023-10-08', NULL, NULL, '6750.86', '16379', '0', '23129.86', '4163.37', '27293.23', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(153, '01/10/2022', '43', '2022-11-10', 'FRESH', 1, 1, 1, 100, 15, 'GCV PACKAGE', '0162470154 01', 'KA52B2946', 'NELAMANGALA', 'EICHER PRO 2114 XP', 'CNG', '2022-10-09', '2023-10-08', NULL, NULL, '12131.85', '35643', '0', '47774.85', '8599.47', '56374.32', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(154, '01/10/2022', '44', '2022-11-10', 'FRESH', 1, 1, 1, 100, 15, 'GCV PACKAGE', '0162470147 01', 'KA52B2945', 'NELAMANGALA', 'EICHER PRO 2114 XP', 'CNG', '2022-10-09', '2023-10-08', NULL, NULL, '12131.85', '35643', '0', '47774.85', '8599.47', '56374.32', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(155, '01/10/2022', '45', '2022-11-10', 'FRESH', 1, 1, 1, 100, 15, 'GCV PACKAGE', '0162470151 01', 'KA52B2944', 'BANGALORE', 'EICHER PRO 2095', 'CNG', '2022-10-09', '2023-10-08', NULL, NULL, '9997.43', '27516', '0', '37513.43', '6752.42', '44265.85', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(156, '01/10/2022', '46', '2022-11-10', 'FRESH', 1, 1, 1, 100, 15, 'GCV PACKAGE', '0162470434 01', 'GJ01JT4505', 'AHMEDABAD', 'EICHER PRO 2114 XP', 'CNG', '2022-10-09', '2023-10-08', NULL, NULL, '12177.64', '35643', '0', '47820.64', '8607.72', '56428.36', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(157, '01/10/2022', '394', '1970-01-01', 'FRESH', 1, 1, 1, 101, 9, 'FIRE', '1016/264237890/00/000', 'NA', 'NA', 'NA', 'NA', '1970-01-01', '1970-01-01', NULL, NULL, '0', '6736', '0', '6736', '1212.48', '7948.48', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(158, '01/10/2022', '426', '1970-01-01', 'Renewal', 1, 1, 1, 102, 5, 'FW OD', '900408425', 'GJ38BB2489', 'BAVLA', 'MARUTI DZIRE TOUR S', 'PETROL/CNG', '1970-01-01', '1970-01-01', NULL, NULL, '4974.98', '0', '0', '4974.98', '895.50', '5870.48', 'ONLINE', '', '', NULL, 'AJAY', 'ICICI LOMBARD', '3001/O/229623343/00/000', '', '', '', '', '', 0, NULL, NULL),
(159, '01/10/2022', '454', '1970-01-01', 'FRESH', 1, 1, 1, 92, 15, 'GCV PACKAGE', '0162489012 01', 'GJ01HT0070', 'AHMEDABAD', 'MAHINDRA BOLERO PICKUP ­ MAXI TRUCK PLUS', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '4258.07', '16319', '0', '20577.07', '2740.00', '23317.07', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(160, '01/10/2022', '455', '1970-01-01', 'FRESH', 1, 1, 1, 30, 15, 'GCV PACKAGE', '0162509823 01', 'GJ01JT4714', 'AHMEDABAD', 'EICHER PRO 2114 XP ­ CNG', 'CNG', '1970-01-01', '1970-01-01', NULL, NULL, '13027.92', '35643', '0', '48670.92', '6644', '55314.92', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(161, '01/10/2022', '456', '1970-01-01', 'FRESH', 1, 1, 1, 30, 15, 'GCV PACKAGE', '0162510268 01', 'HR55AK0219', 'GURGAON', 'EICHER PRO 2114 XP ­ CNG', 'CNG', '1970-01-01', '1970-01-01', NULL, NULL, '13027.92', '35643', '0', '48670.92', '6644', '55314.92', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(162, '01/10/2022', '457', '1970-01-01', 'FRESH', 1, 1, 1, 30, 15, 'GCV PACKAGE', '0162509822 01', 'HR55AK6669', 'GURGAON', 'EICHER PRO 2114 XP ­ CNG', 'CNG', '1970-01-01', '1970-01-01', NULL, NULL, '13027.92', '35643', '0', '48670.92', '6644.00', '55314.92', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(163, '01/10/2022', '458', '1970-01-01', 'FRESH', 1, 1, 1, 30, 15, 'GCV PACKAGE', '0162522601 01', 'MH04KU3278', 'THANE ', 'EICHER PRO 2075 ­ CNG', 'CNG', '1970-01-01', '1970-01-01', NULL, NULL, '10830.38', '16379', '0', '27209.38', '3934.00', '31143.38', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(164, '01/10/2022', '459', '2022-07-10', 'FRESH', 1, 1, 1, 30, 15, 'GCV PACKAGE', '163717632', 'NEW', 'NELAMANGALA', 'EICHER PRO 2114 XP ­ TRUCK', 'DIESEL', '2010-06-22', '2010-05-23', NULL, NULL, '12600.64', '35583', '0', '48183.64', '6555.00', '54738.64', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(165, '01/10/2022', '460', '1970-01-01', 'FRESH', 1, 1, 1, 30, 6, 'TW PACKAGE', 'D079079505', 'GJ27CH3932', 'AHMEDABAD', 'HONDA CD 110 DREAM/SELF BSIV', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '266.45', '714', '0', '980.45', '176.48', '1156.93', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(166, '01/10/2022', '461', '1970-01-01', 'FRESH', 1, 1, 1, 30, 6, 'TW PACKAGE', 'D079080490', 'HR26DL2095', 'Gurgaon', 'HERO MOTOCORP SPLENDOR PRO/DRUM SELF\nCAST', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '283.24', '714', '0', '997.24', '179.50', '1176.74', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(167, '01/10/2022', '462', '1970-01-01', 'FRESH', 1, 1, 1, 103, 6, 'TW PACKAGE', 'D079080825', 'RJ14JA1273', 'Jaipur', 'HERO MOTOCORP HF DELUXE/I3S SELF DRUM\nCAST', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '392.18', '714', '0', '1106.18', '199.11', '1305.29', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(168, '01/10/2022', '463', '1970-01-01', 'FRESH', 1, 1, 1, 103, 6, 'TW PACKAGE', 'D079091830', 'MH04JP6230', 'Thane', 'HERO MOTOCORP HF DELUXE/I3S SELF DRUM\nCAST', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '392.18', '714', '0', '1106.18', '199.11', '1305.29', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(169, '01/10/2022', '464', '1970-01-01', 'FRESH', 1, 1, 1, 103, 6, 'TW PACKAGE', 'D079092077', 'HR26DR5051', 'Gurgaon', 'HERO MOTOCORP HF DELUXE/ELEC ST DRUM\nBRAKE CAST WHEELS', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '305.03', '714', '0', '1019.03', '183.43', '1202.46', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(170, '01/10/2022', '465', '1970-01-01', 'FRESH', 1, 1, 1, 104, 6, 'TW PACKAGE', 'D079080240', 'GJ27BM5040', 'AHMEDABAD', 'HONDA DREAM YUGA/SELF START\nALLOY WHEEL', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '307.44', '1044', '0', '1351.44', '243.26', '1594.70', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(171, '01/10/2022', '816', '1970-01-01', 'Renewal', 1, 1, 1, 105, 6, 'TW PACKAGE', 'D070783516', 'GJ01NS0718', 'AHMEDABAD', 'HERO HONDA SPLENDOR PRO/STD', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '177.6', '1044', '0', '1221.6', '219.888', '1441.488', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(172, '01/10/2022', '819', '1970-01-01', 'Renewal', 1, 1, 1, 106, 6, 'TW PACKAGE', 'D070783200', 'GJ01NK6681', 'AHMEDABAD', 'HONDA ACTIVA STD', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '156.8', '1044', '0', '1200.8', '216.144', '1416.944', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(173, '01/10/2022', '886', '2022-12-10', 'FRESH', 1, 1, 1, 107, 6, 'FW PACKAGE', 'D078793490', 'GJ01KW0669', 'AHMEDABAD', 'MARUTI SUZUKI ERTIGA', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '2667.44', '10582', '0', '13249.44', '2384.8992', '15634.3392', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(174, '01/10/2022', '905', '2022-01-10', 'FRESH', 1, 1, 1, 108, 15, '4W PACKAGE', '6200675447', 'RJ19CJ3274', 'JODHPUR RAJASTHAN', 'MARUTI/WAGON R / 1.2 ZXI/HATCH BACK', 'PETROL', '2010-04-22', '2010-03-23', NULL, NULL, '6251.26', '4091', '116', '10458.26', '1882.4868', '12340.7468', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(175, '01/10/2022', '910', '2022-09-10', 'FRESH', 1, 1, 1, 109, 15, '4W PACKAGE', '6200699119', 'GJ01KX0028', 'AHMEDABAD', 'KIA/SELTOS / HTK PLUS 1.5/SUV', 'PETROL', '2010-10-22', '2010-09-23', NULL, NULL, '5796.68', '11638.66', '116', '17551.34', '3159.2412', '20710.5812', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(176, '01/11/2022', '54', '2022-04-11', 'FRESH', 1, 1, 1, 110, 15, 'FW PACKAGE', '6200789392', 'GJ01KL4804', 'AHMEDABAD', 'HYUNDAI / I10 / 1.1 ERA / HATCH BACK', 'PETROL', '2011-04-22', '2022-11-03', NULL, NULL, '1631.19', '4091', '0', '5722.190000000001', '1029.99', '6752.18', 'CHEQUE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(177, '01/11/2022', '57', '2022-04-11', 'FRESH', 1, 1, 1, 111, 9, 'TW TP', '3005/A/266579417/00/B00', 'GJ01MU0034', 'AHMEDABAD', 'HONDA MOTORCYCLE ACTIVA 3G', 'PETROL', '2011-05-22', '2023-11-04', NULL, NULL, '0', '714', '0', '714', '128.52', '842.52', 'ONLINE', '', '', NULL, '', 'SNEHA', 'RC BASE', 'RC BASE', '', '', '', '', 0, NULL, NULL),
(178, '01/11/2022', '127', '2022-07-11', 'RENEWAL', 1, 1, 1, 112, 9, 'TW OD', '3005/O/266891125/00/000', 'GJ01VG1670', 'AHMEDABAD', 'HONDA HNESS CB350', 'PETROL', '2011-07-22', '2023-11-06', NULL, NULL, '2976', '0', '0', '2976', '535.68', '3511.68', 'ONLINE', '', '', NULL, '', 'SNEHA', 'GO DIGIT RENEWAL', 'D048634282', '', '', '', '', 0, NULL, NULL),
(179, '01/11/2022', '397', '2022-04-10', 'FRESH', 1, 1, 1, 113, 3, 'TRAVEL', '12-9910-0001892100-00', 'NA', 'NA', 'NA', 'NA', '1970-01-01', '2022-12-03', NULL, NULL, '707', '0', '0', '707', '127.26', '834.26', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(180, '01/11/2022', '727', '1970-01-01', 'FRESH', 1, 1, 1, 114, 15, 'GCV PACKAGE', '\'01638200910000', 'NEW', 'NA', 'EICHER PRO 2059 ­ XP  GVW 7490', 'DISEL', '1970-01-01', '2023-11-16', NULL, NULL, '8545.43', '16319', '0', '24864.43', '3512', '28376.43', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(181, '01/11/2022', '728', '1970-01-01', 'FRESH', 1, 1, 1, 115, 15, 'GCV PACKAGE', '01638211080000', 'GJ01HT7617', 'AHMEDABAD', 'TATA LPT 2518 ­ CR BSIV  GVW 28000', 'DISEL', '1970-01-01', '2023-11-19', NULL, NULL, '17100.84', '44220', '0', '61320.84', '8400', '69720.84', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(182, '01/11/2022', '729', '1970-01-01', 'FRESH', 1, 1, 1, 115, 15, 'GCV PACKAGE', '01638211070000', 'GJ01HT7688', 'AHMEDABAD', 'TATA LPT 251 GVW 28000', 'DISEL', '1970-01-01', '2023-11-19', NULL, NULL, '17597.12', '44220', '0', '61817.12', '8490', '70307.12', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(183, '01/11/2022', '730', '1970-01-01', 'FRESH', 1, 1, 1, 115, 15, 'GCV PACKAGE', '01638211090000', 'GJ01HT7697', 'AHMEDABAD', 'TATA LPT 2518 CR BS IV GVW 28000', 'DISEL', '1970-01-01', '2023-11-19', NULL, NULL, '17100.84', '44220', '0', '61320.84', '8400', '69720.84', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(184, '01/11/2022', '731', '1970-01-01', 'FRESH', 1, 1, 1, 115, 15, 'GCV PACKAGE', '01638211060000', 'GJ01HT7925', 'AHMEDABAD', 'TATA LPT 2518 ­ CR BS IV GVW 28000', 'DISEL', '1970-01-01', '2023-11-19', NULL, NULL, '17100.84', '44220', '0', '61320.84', '8400', '69720.84', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(185, '01/11/2022', '732', '1970-01-01', 'FRESH', 1, 1, 1, 115, 15, 'GCV PACKAGE', '01638211420000', 'GJ01HT7906', 'AHMEDABAD', 'EICHER PRO 1059 XP­ TRUCK GVW 7490', 'DISEL', '1970-01-01', '2023-11-28', NULL, NULL, '8049', '16319', '0', '24368', '3422', '27790', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(186, '01/11/2022', '733', '1970-01-01', 'FRESH', 1, 1, 1, 114, 15, 'GCV PACKAGE', '01638211040000', 'GJ01HT7501', 'AHMEDABAD', 'EICHER PRO 5028 ­ TRUCK  GVW 28000', 'DISEL', '1970-01-01', '2023-11-25', NULL, NULL, '17100.84', '44220', '0', '61320.84', '8400', '69720.84', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(187, '01/11/2022', '734', '1970-01-01', 'FRESH', 1, 1, 1, 115, 15, 'GCV PACKAGE', '01638211400000', 'GJ01HT7541', 'AHMEDABAD', 'TATA LPT 2518 (CLOSED) ­ LPT 2518 6*2 TCEX BSIII GVW 28000', 'DISEL', '1970-01-01', '2023-11-19', NULL, NULL, '17100.84', '44220', '0', '61320.84', '8400', '69720.84', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(188, '01/11/2022', '735', '1970-01-01', 'FRESH', 1, 1, 1, 115, 15, 'GCV PACKAGE', '\'01638211470000', 'GJ01HT7543', 'AHMEDABAD', 'TATA LPT 2518 (CLOSED) ­ LPT 2518 6*2 TCEX BSIII GVW 28000', 'DISEL', '1970-01-01', '2023-11-19', NULL, NULL, '17100.84', '44220', '0', '61320.84', '8400', '69720.84', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(189, '01/11/2022', '736', '1970-01-01', 'FRESH', 1, 1, 1, 114, 15, 'GCV PACKAGE', '\'01638219730000', 'GJ01HT7669', 'AHMEDABAD', 'EICHER PRO 5028 ­ TRUCK GVW 28000', 'DISEL', '1970-01-01', '2023-11-25', NULL, NULL, '17100.84', '44220', '0', '61320.84', '8400', '69720.84', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(190, '01/11/2022', '737', '1970-01-01', 'FRESH', 1, 1, 1, 115, 15, 'GCV PACKAGE', '01638211330000', 'HR55AH7779', 'HARIYANA', 'EICHER PRO 1095 CNG GVW 10700', 'DISEL', '1970-01-01', '2023-11-28', NULL, NULL, '11249.2', '27516', '0', '38765.2', '5346', '44111.2', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(191, '01/11/2022', '738', '1970-01-01', 'FRESH', 1, 1, 1, 115, 15, 'GCV PACKAGE', '01638219690000', 'KA52B0026', 'KARNATAKA', 'EICHER PRO 2095 XP GVW 10700', 'DISEL', '1970-01-01', '2023-11-28', NULL, NULL, '11906.75', '27456', '0', '39362.75', '5454', '44816.75', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(192, '01/11/2022', '739', '1970-01-01', 'FRESH', 1, 1, 1, 115, 15, 'GCV PACKAGE', '01638219720000', 'MH48BM5605', 'MUMBAI', 'EICHER PRO 2095 XP  GVW 10700', 'DISEL', '1970-01-01', '2023-11-28', NULL, NULL, '11906.75', '27456', '0', '39362.75', '5454', '44816.75', 'ONLINE', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL),
(193, '01/11/2022', '956', '2022-07-11', 'FRESH', 1, 1, 1, 116, 15, 'MEDICARE', '7040002685', 'NA', 'NA', 'NA', 'NA', '2011-08-22', '2023-11-07', NULL, NULL, '10734.55', '0', '0', '10734.55', '1932.219', '12666.769', 'ONLINE', '', '', NULL, '', 'NIKITA WHATSAPP', '', '', '', '', '', '', 0, NULL, NULL),
(194, '01/11/2022', '957', '1970-01-01', 'Rollover', 1, 1, 1, 117, 1, 'FW PACKAGE', 'OG-23-2202-1801-00021442', 'GJ27BE7593', 'AHMEDABAD', 'TATA - TIAGO', 'DIESEL', '1970-01-01', '2023-11-18', NULL, NULL, '6756', '4047', '0', '10803', '1944.54', '12747.54', 'ONLINE', '', '', NULL, '', 'NIKITA WHATSAPP', 'TATA AIG', '3100121439', '', '', '', '', 0, NULL, NULL),
(195, '01/11/2022', '958', '1970-01-01', 'FRESH', 1, 1, 1, 118, 6, 'WC', 'D077709036', 'NA', 'NA', 'NA', 'NA', '2010-08-22', '2010-07-23', NULL, NULL, '27831.52', '0', '0', '27831.52', '5009.6736', '32841.1936', 'ONLINE', '', '', NULL, '', 'NIKITA WHATSAPP', '', '', '', '', '', '', 0, NULL, NULL),
(196, '01/12/2022', '1', '2022-01-12', 'ROLLOVER', 1, 1, 1, 119, 9, 'TW PACKAGE', '3005/270765145/00/000', 'GJ01MK2760', 'AHMEDABAD', 'HONDA ACTIVA', 'PETROL', '2012-01-22', '1970-01-01', NULL, NULL, '91', '1089', '0', '1180.0', '212.4', '1392', 'ONLINE', '', '', NULL, '', 'AJAY', 'ICICI', '3005/2011448379/00/000013461', '', '', '', '', 0, NULL, NULL),
(197, '01/12/2022', '145', '2022-08-12', 'FRESH', 1, 1, 1, 120, 3, 'TRAVEL', '12-9910-0002240027-00', 'NA', 'NA', 'NA', 'NA', '1970-01-01', '1970-01-01', NULL, NULL, '10438', '0', '0', '10438.0', '1878.84', '12317', 'ONLINE', '', '', NULL, '', 'ajay', '', '', '', '', '', '', 0, NULL, NULL),
(198, '01/12/2022', '147', '2022-02-12', 'ROLLOVER', 1, 1, 1, 121, 14, 'GCV PACKAGE', 'VGC0875994000100', 'GJ19Y1370', 'BARDOLI', 'Eicher PRO 2059 CNG E HSD 14FT NGB BSVI', 'CNG', '2012-03-22', '2012-02-23', NULL, NULL, '7387', '16379', '0', '23766.0', '3314.94', '27081', 'ONLINE', '', '', NULL, '', 'sneha whatsap', 'TATA AIG', '16268175200', '', '', '', '', 0, NULL, NULL),
(199, '01/12/2022', '306', '2022-09-12', 'RENEWAL', 1, 1, 1, 110, 6, 'TW OD', 'D076188659', 'GJ27CN9492', 'AHMEDABAD EAST', 'SUZUKI ACCESS/125 ALLOY DISC BS6', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '779.42', '0', '0', '779.4', '140.2956', '920', 'ONLINE', '', '', NULL, '', 'KAJAL', 'GO DIGIT', 'D051983460', '', '', '', '', 0, NULL, NULL),
(200, '01/12/2022', '318', '1970-01-01', 'ROLLOVER', 1, 1, 1, 122, 15, 'FW PACKAGE', '6200919796 00 00', 'GJ27AH9687', 'AHMEDABAD EAST', 'MARUTI / S CROSS /ZETA 1.3 / SUV', 'DIESEL', '1970-01-01', '2012-12-23', NULL, NULL, '9459.290000000001', '4091', '116', '13666.3', '2459.9322', '16126', 'ONLINE', '', '', NULL, '', 'KAJAL', 'RELIANCE', '160222123110071997', '', '', '', '', 0, NULL, NULL),
(201, '01/12/2022', '384', '1970-01-01', 'FRESH', 1, 1, 1, 123, 15, 'GCV PACKAGE', '0163864379 00 00', 'NEW', 'NA', 'EICHER PRO 2059 XP GVW 7490', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '8556.41', '16319', '0', '24875.4', '3515', '28390', 'ONLINE', '', '', NULL, '', 'KAJAL', 'NA', 'NA', '', '', '', '', 0, NULL, NULL),
(202, '01/12/2022', '422', '2022-08-12', 'FRESH', 1, 1, 1, 124, 4, 'HEALTH', '49623759', 'NA', 'NA', 'NA', 'NA', '2012-08-22', '1970-01-01', NULL, NULL, '18526.23', '0', '0', '18526.2', '3334.7214', '21861', 'ONLINE', '', '', NULL, '', 'aakashbhai mail', '', '', '', '', '', '', 0, NULL, NULL),
(203, '01/12/2022', '469', '1970-01-01', 'FRESH', 1, 1, 1, 125, 15, 'GCV PACKAGE', '162751564', 'GJ01HT0528', 'AHMEDABAD', 'EICHER PRO 5016 M\nCBC', 'Diesel', '1970-01-01', '2023-12-21', NULL, NULL, '14277.89', '35583', '0', '49860.9', '6856', '56717', 'ONLINE', '', '', NULL, '', 'AAKASHBHAI MAIL', '', '', '', '', '', '', 0, NULL, NULL),
(204, '01/12/2022', '470', '1970-01-01', 'FRESH', 1, 1, 1, 125, 15, 'GCV PACKAGE', '162751538', 'GJ01HT0690', 'AHMEDABAD', 'EICHER PRO 5016 M\nCBC\nCLOSED CONTAINER GVW 18500', 'Diesel', '1970-01-01', '2023-12-21', NULL, NULL, '14107.02', '35583', '0', '49690.0', '6826', '56516', 'ONLINE', '', '', NULL, '', 'AAKASHBHAI MAIL', '', '', '', '', '', '', 0, NULL, NULL),
(205, '01/12/2022', '471', '1970-01-01', 'FRESH', 1, 1, 1, 126, 15, 'GCV PACKAGE', '162751476', 'GJ01HT0785', 'AHMEDABAD', 'EICHER PRO 5016 M\nCBC\nCLOSED CONTAINER', 'Diesel', '1970-01-01', '2023-12-21', NULL, NULL, '13423.55', '35643', '0', '49066.6', '6641.95', '55708', 'ONLINE', '', '', NULL, '', 'AAKASHBHAI MAIL', '', '', '', '', '', '', 0, NULL, NULL),
(206, '01/12/2022', '472', '1970-01-01', 'FRESH', 1, 1, 1, 125, 15, 'GCV PACKAGE', '162751515', 'GJ01HT0988', 'AHMEDABAD', 'EICHER PRO 5016 M\nCBC\nCLOSED CONTAINER', 'Diesel', '1970-01-01', '2023-12-21', NULL, NULL, '13423.55', '35643', '0', '49066.6', '6641.95', '55708', 'ONLINE', '', '', NULL, '', 'AAKASHBHAI MAIL', '', '', '', '', '', '', 0, NULL, NULL),
(207, '01/12/2022', '477', '1970-01-01', 'ROLLOVER', 1, 1, 1, 127, 7, 'FW PACKAGE', '2302 2051 1882 7000 000', 'GJ01KX8288', 'AHMEDABAD', 'TOYOTA KIRLOSKAR INNOVA CRYSTA-2.4 V 8S', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '27014', '8672', '0', '35686.0', '6423.48', '42109', 'ONLINE', '', '', NULL, '', 'AAKASHBHAI MAIL', '', '', '', '', '', '', 0, NULL, NULL),
(208, '01/12/2022', '494', '1970-01-01', 'ENDORSMENT', 1, 1, 1, 128, 9, 'TW PACKAGE', '3005/273358632/00/000', 'GJ01EZ5553', 'AHMEDABAD', 'HONDA MOTORCYCLE / ACTIVA 3G', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '78', '1089', '0', '1167.0', '210.06', '1377', 'ONLINE', '', '', NULL, '', 'KAJAL', 'RELIANCE', '920222123123014157', '', '', '', '', 0, NULL, NULL),
(209, '01/12/2022', '495', '2022-05-12', 'ENDORSMENT', 1, 1, 1, 129, 9, 'HEALTH', '4016/X/225230710/01/001', 'NA', 'NA', 'NA', 'NA', '1970-01-01', '1970-01-01', NULL, NULL, '10918.5', '0', '0', '10918.5', '1965.33', '12884', 'ONLINE', '', '', NULL, '', 'aakashbhai mail', '', '', '', '', '', '', 0, NULL, NULL),
(210, '01/12/2022', '555', '1970-01-01', 'Renewal', 1, 1, 1, 130, 9, 'TW PACKAGE', '3005/273619992/00/B00', 'GJ01VG6311', 'AHMEDABAD', 'HERO SPLENDOR PLUS', 'PETROL', '1970-01-01', '1970-01-01', NULL, NULL, '864', '1089', '0', '1953.0', '351.54', '2305', 'ONLINE', '', '', NULL, '', 'AAKASHBHAI MAIL', 'BAJAJ ALLIANZ', 'OG-22-2202-1802-0004628', '', '', '', '', 0, NULL, NULL),
(211, '01/12/2022', '', '1970-01-01', 'FRESH', 1, 1, 1, 129, 15, 'GCV PACKAGE', '0162803954 01 00', 'GJ01HT1236', 'AHMEDABAD', 'ASHOK LEYLAND 2518 GVW 28000', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '15023', '44280', '0', '59303', '8038', '67341', 'ONLINE', '', '', NULL, '', 'AAKASHBHAI MAIL', '', '', '', '', '', '', 0, NULL, NULL),
(212, '01/12/2022', '', '1970-01-01', 'FRESH', 1, 1, 1, 129, 15, 'GCV PACKAGE', '0162803951 01 00', 'GJ01HT1499', 'AHMEDABAD', 'ASHOK LEYLAND 2518 GVW 28000', 'DIESEL', '1970-01-01', '1970-01-01', NULL, NULL, '14587.49', '44280', '0', '58867.49', '7960', '66827.49000000001', 'ONLINE', '', '', NULL, '', 'AAKASHBHAI MAIL', '', '', '', '', '', '', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `insurance_companies`
--

DROP TABLE IF EXISTS `insurance_companies`;
CREATE TABLE IF NOT EXISTS `insurance_companies` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_number` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `insurance_companies`
--

INSERT INTO `insurance_companies` (`id`, `name`, `email`, `mobile_number`, `status`, `created_at`, `updated_at`) VALUES
(1, 'BAJAJ ALLIANZ', NULL, NULL, 1, NULL, NULL),
(2, 'BAJAJ CONS', NULL, NULL, 1, NULL, NULL),
(3, 'Bajaj Consultancy', NULL, NULL, 1, NULL, NULL),
(4, 'care', NULL, NULL, 1, NULL, NULL),
(5, 'Edelweiss', NULL, NULL, 1, NULL, NULL),
(6, 'GO DIGIT', NULL, NULL, 1, NULL, NULL),
(7, 'HDFC Ergo', NULL, NULL, 1, NULL, NULL),
(8, 'HDFC LIFE', NULL, NULL, 1, NULL, NULL),
(9, 'Icici Lombard', NULL, NULL, 1, NULL, NULL),
(10, 'ICICI-AHM', NULL, NULL, 1, NULL, NULL),
(11, 'KOTAK GIC', NULL, NULL, 1, NULL, NULL),
(12, 'MAGMA HDI', NULL, NULL, 1, NULL, NULL),
(13, 'Reliance', NULL, NULL, 1, NULL, NULL),
(14, 'ROYAL SUNDARAM', NULL, NULL, 1, NULL, NULL),
(15, 'TATA AIG', NULL, NULL, 1, NULL, NULL),
(16, 'The new India', NULL, NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2022_01_12_173356_create_permission_tables', 1),
(6, '2023_01_01_000000_create_customers_table', 2),
(12, '2023_01_01_000000_create_brokers_table', 3),
(13, '2023_01_08_122445_create_relationmanager_table', 3),
(14, '2023_01_08_130547_create_customer_insurances_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'user-list', 'web', '2023-01-07 11:01:14', '2023-01-07 11:01:14'),
(2, 'user-create', 'web', '2023-01-07 11:01:14', '2023-01-07 11:01:14'),
(3, 'user-edit', 'web', '2023-01-07 11:01:14', '2023-01-07 11:01:14'),
(4, 'user-delete', 'web', '2023-01-07 11:01:14', '2023-01-07 11:01:14'),
(5, 'role-create', 'web', '2023-01-07 11:01:14', '2023-01-07 11:01:14'),
(6, 'role-edit', 'web', '2023-01-07 11:01:14', '2023-01-07 11:01:14'),
(7, 'role-list', 'web', '2023-01-07 11:01:14', '2023-01-07 11:01:14'),
(8, 'role-delete', 'web', '2023-01-07 11:01:14', '2023-01-07 11:01:14'),
(9, 'permission-list', 'web', '2023-01-07 11:01:14', '2023-01-07 11:01:14'),
(10, 'permission-create', 'web', '2023-01-07 11:01:14', '2023-01-07 11:01:14'),
(11, 'permission-edit', 'web', '2023-01-07 11:01:14', '2023-01-07 11:01:14'),
(12, 'permission-delete', 'web', '2023-01-07 11:01:14', '2023-01-07 11:01:14'),
(13, 'customer-list', 'web', '2023-01-07 11:11:41', '2023-01-07 11:11:41'),
(14, 'customer-create', 'web', '2023-01-07 11:11:54', '2023-01-07 11:11:54'),
(15, 'customer-edit', 'web', '2023-01-07 11:12:02', '2023-01-07 11:12:02'),
(16, 'customer-delete', 'web', '2023-01-07 11:12:18', '2023-01-07 11:12:18'),
(17, 'broker-create', 'web', '2023-01-08 06:18:24', '2023-01-08 06:18:24'),
(18, 'broker-edit', 'web', '2023-01-08 06:18:35', '2023-01-08 06:18:35'),
(19, 'broker-delete', 'web', '2023-01-08 06:18:44', '2023-01-08 06:18:44'),
(20, 'broker-list', 'web', '2023-01-08 06:19:01', '2023-01-08 06:19:01'),
(21, 'relationship_manager-list', 'web', '2023-01-08 07:02:49', '2023-01-08 07:02:49'),
(22, 'relationship_manager-create', 'web', '2023-01-08 07:02:55', '2023-01-08 07:02:55'),
(23, 'relationship_manager-edit', 'web', '2023-01-08 07:03:02', '2023-01-08 07:03:02'),
(24, 'relationship_manager-delete', 'web', '2023-01-08 07:03:10', '2023-01-08 07:03:10'),
(25, 'customer-insurance-list', 'web', '2023-01-08 08:12:25', '2023-01-08 08:12:25'),
(26, 'customer-insurance-create', 'web', '2023-01-08 08:12:34', '2023-01-08 08:12:34'),
(27, 'customer-insurance-edit', 'web', '2023-01-08 08:12:43', '2023-01-08 08:12:43'),
(28, 'customer-insurance-delete', 'web', '2023-01-08 08:12:51', '2023-01-08 08:12:51');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `relationship_managers`
--

DROP TABLE IF EXISTS `relationship_managers`;
CREATE TABLE IF NOT EXISTS `relationship_managers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_number` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `relationship_managers`
--

INSERT INTO `relationship_managers` (`id`, `name`, `email`, `mobile_number`, `status`, `created_at`, `updated_at`) VALUES
(1, 'AAKASH DOSHI', NULL, NULL, 1, NULL, NULL),
(2, 'ASHIT SHAH', NULL, NULL, 1, NULL, NULL),
(3, 'Siddhi Kumbhani', NULL, NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'web', '2023-01-07 11:01:14', '2023-01-07 11:01:14'),
(2, 'User', 'web', '2023-01-07 11:01:14', '2023-01-07 11:01:14');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_number` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` int NOT NULL DEFAULT '2' COMMENT '1=Admin, 2=TA/TP',
  `status` tinyint NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `mobile_number`, `email_verified_at`, `password`, `role_id`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super', 'Admin', 'admin@admin.com', '9028187696', NULL, '$2y$10$W3EuWAfqymIyNAuOy2G.TeL4iVpkGr1iz3706T.O4I39OOk8BtXHa', 1, 1, 'dX39hcIAsdJdeQim90TbxgmWZYE8P8kBhHFI5ff67sQVsKX3F0G0az464BAC', '2023-01-07 11:01:14', '2023-01-07 11:01:14');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

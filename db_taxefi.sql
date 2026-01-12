-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2025 at 12:44 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_taxefi`
--

-- --------------------------------------------------------

--
-- Table structure for table `tf_accountancy`
--

CREATE TABLE `tf_accountancy` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `firm_id` int(11) DEFAULT NULL,
  `turnover` float NOT NULL,
  `other_fee` float NOT NULL,
  `due_date` date NOT NULL,
  `added_by` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tf_acc_payment`
--

CREATE TABLE `tf_acc_payment` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `firm_id` int(11) DEFAULT NULL,
  `acc_date` date NOT NULL,
  `amount` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tf_area`
--

CREATE TABLE `tf_area` (
  `id` int(11) NOT NULL,
  `name` varchar(300) NOT NULL,
  `type` varchar(200) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tf_area`
--

INSERT INTO `tf_area` (`id`, `name`, `type`, `parent_id`, `status`) VALUES
(1, 'Andaman & Nicobar Islands', 'State', 0, 1),
(2, 'Andhra Pradesh', 'State', 0, 1),
(3, 'Arunachal Pradesh', 'State', 0, 1),
(4, 'Assam', 'State', 0, 1),
(5, 'Bihar', 'State', 0, 1),
(6, 'Chandigarh', 'State', 0, 1),
(7, 'Chhattisgarh', 'State', 0, 1),
(8, 'Dadra and Nagar Haveli', 'State', 0, 1),
(9, 'Daman and Diu', 'State', 0, 1),
(10, 'Delhi', 'State', 0, 1),
(11, 'Goa', 'State', 0, 1),
(12, 'Gujarat', 'State', 0, 1),
(13, 'Haryana', 'State', 0, 1),
(14, 'Himachal Pradesh', 'State', 0, 1),
(15, 'Jammu and Kashmir', 'State', 0, 1),
(16, 'Jharkhand', 'State', 0, 1),
(17, 'Karnataka', 'State', 0, 1),
(18, 'Kerala', 'State', 0, 1),
(19, 'Lakshadweep', 'State', 0, 1),
(20, 'Madhya Pradesh', 'State', 0, 1),
(21, 'Maharashtra', 'State', 0, 1),
(22, 'Manipur', 'State', 0, 1),
(23, 'Meghalaya', 'State', 0, 1),
(24, 'Mizoram', 'State', 0, 1),
(25, 'Nagaland', 'State', 0, 1),
(26, 'Odisha', 'State', 0, 1),
(27, 'Puducherry', 'State', 0, 1),
(28, 'Punjab', 'State', 0, 1),
(29, 'Rajasthan', 'State', 0, 1),
(30, 'Sikkim', 'State', 0, 1),
(31, 'Tamil Nadu', 'State', 0, 1),
(32, 'Telengana', 'State', 0, 1),
(33, 'Tripura', 'State', 0, 1),
(34, 'Uttrakhand', 'State', 0, 1),
(35, 'Uttar Pradesh', 'State', 0, 1),
(36, 'West Bengal', 'State', 0, 1),
(37, 'Nicobar', 'District', 1, 1),
(38, 'North and Middle Andaman', 'District', 1, 1),
(39, 'South Andaman', 'District', 1, 1),
(40, 'Anantapur', 'District', 2, 1),
(41, 'Chittoor', 'District', 2, 1),
(42, 'East Godavari', 'District', 2, 1),
(43, 'Guntur', 'District', 2, 1),
(44, 'Krishna', 'District', 2, 1),
(45, 'Kurnool', 'District', 2, 1),
(46, 'Prakasam', 'District', 2, 1),
(47, 'Srikakulam', 'District', 2, 1),
(48, 'Sri Potti Sriramulu Nellore', 'District', 2, 1),
(49, 'Visakhapatnam', 'District', 2, 1),
(50, 'Vizianagaram', 'District', 2, 1),
(51, 'West Godavari', 'District', 2, 1),
(52, 'YSR District, Kadapa (Cuddapah)', 'District', 2, 1),
(53, 'Anjaw', 'District', 3, 1),
(54, 'Changlang', 'District', 3, 1),
(55, 'Dibang Valley', 'District', 3, 1),
(56, 'East Kameng', 'District', 3, 1),
(57, 'East Siang', 'District', 3, 1),
(58, 'Kamle', 'District', 3, 1),
(59, 'Kra Daadi', 'District', 3, 1),
(60, 'Kurung Kumey', 'District', 3, 1),
(61, 'Lepa Rada', 'District', 3, 1),
(62, 'Lohit', 'District', 3, 1),
(63, 'Longding', 'District', 3, 1),
(64, 'Lower Dibang Valley', 'District', 3, 1),
(65, 'Lower Siang', 'District', 3, 1),
(66, 'Lower Subansiri', 'District', 3, 1),
(67, 'Namsai', 'District', 3, 1),
(68, 'Pakke Kessang', 'District', 3, 1),
(69, 'Papum Pare', 'District', 3, 1),
(70, 'Shi Yomi', 'District', 3, 1),
(71, 'Siang', 'District', 3, 1),
(72, 'Tawang', 'District', 3, 1),
(73, 'Tirap', 'District', 3, 1),
(74, 'Upper Siang', 'District', 3, 1),
(75, 'Upper Subansiri', 'District', 3, 1),
(76, 'West Kameng', 'District', 3, 1),
(77, 'West Siang', 'District', 3, 1),
(78, 'Baksa', 'District', 4, 1),
(79, 'Barpeta', 'District', 4, 1),
(80, 'Biswanath', 'District', 4, 1),
(81, 'Bongaigaon', 'District', 4, 1),
(82, 'Cachar', 'District', 4, 1),
(83, 'Charaideo', 'District', 4, 1),
(84, 'Chirang', 'District', 4, 1),
(85, 'Darrang', 'District', 4, 1),
(86, 'Dhemaji', 'District', 4, 1),
(87, 'Dhubri', 'District', 4, 1),
(88, 'Dibrugarh', 'District', 4, 1),
(89, 'Dima Hasao (North Cachar Hills)', 'District', 4, 1),
(90, 'Goalpara', 'District', 4, 1),
(91, 'Golaghat', 'District', 4, 1),
(92, 'Hailakandi', 'District', 4, 1),
(93, 'Hojai', 'District', 4, 1),
(94, 'Jorhat', 'District', 4, 1),
(95, 'Kamrup', 'District', 4, 1),
(96, 'Kamrup Metropolitan', 'District', 4, 1),
(97, 'Karbi Anglong', 'District', 4, 1),
(98, 'Karimganj', 'District', 4, 1),
(99, 'Kokrajhar', 'District', 4, 1),
(100, 'Lakhimpur', 'District', 4, 1),
(101, 'Majuli', 'District', 4, 1),
(102, 'Morigaon', 'District', 4, 1),
(103, 'Nagaon', 'District', 4, 1),
(104, 'Nalbari', 'District', 4, 1),
(105, 'Sivasagar', 'District', 4, 1),
(106, 'Sonitpur', 'District', 4, 1),
(107, 'South Salamara-Mankachar', 'District', 4, 1),
(108, 'Tinsukia', 'District', 4, 1),
(109, 'Udalguri', 'District', 4, 1),
(110, 'West Karbi Anglong', 'District', 4, 1),
(111, 'Araria', 'District', 5, 1),
(112, 'Arwal', 'District', 5, 1),
(113, 'Aurangabad', 'District', 5, 1),
(114, 'Banka', 'District', 5, 1),
(115, 'Begusarai', 'District', 5, 1),
(116, 'Bhagalpur', 'District', 5, 1),
(117, 'Bhojpur', 'District', 5, 1),
(118, 'Buxar', 'District', 5, 1),
(119, 'Darbhanga', 'District', 5, 1),
(120, 'East Champaran (Motihari)', 'District', 5, 1),
(121, 'Gaya', 'District', 5, 1),
(122, 'Gopalganj', 'District', 5, 1),
(123, 'Jamui', 'District', 5, 1),
(124, 'Jehanabad', 'District', 5, 1),
(125, 'Kaimur (Bhabua)', 'District', 5, 1),
(126, 'Katihar', 'District', 5, 1),
(127, 'Khagaria', 'District', 5, 1),
(128, 'Kishanganj', 'District', 5, 1),
(129, 'Lakhisarai', 'District', 5, 1),
(130, 'Madhepura', 'District', 5, 1),
(131, 'Madhubani', 'District', 5, 1),
(132, 'Munger (Monghyr)', 'District', 5, 1),
(133, 'Muzaffarpur', 'District', 5, 1),
(134, 'Nalanda', 'District', 5, 1),
(135, 'Nawada', 'District', 5, 1),
(136, 'Patna', 'District', 5, 1),
(137, 'Purnia (Purnea)', 'District', 5, 1),
(138, 'Rohtas', 'District', 5, 1),
(139, 'Saharsa', 'District', 5, 1),
(140, 'Samastipur', 'District', 5, 1),
(141, 'Saran', 'District', 5, 1),
(142, 'Sheikhpura', 'District', 5, 1),
(143, 'Sheohar', 'District', 5, 1),
(144, 'Sitamarhi', 'District', 5, 1),
(145, 'Siwan', 'District', 5, 1),
(146, 'Supaul', 'District', 5, 1),
(147, 'Vaishali', 'District', 5, 1),
(148, 'West Champaran', 'District', 5, 1),
(149, 'Chandigarh', 'District', 6, 1),
(150, 'Balod', 'District', 7, 1),
(151, 'Baloda Bazar', 'District', 7, 1),
(152, 'Balrampur', 'District', 7, 1),
(153, 'Bastar', 'District', 7, 1),
(154, 'Bemetara', 'District', 7, 1),
(155, 'Bijapur', 'District', 7, 1),
(156, 'Bilaspur', 'District', 7, 1),
(157, 'Dantewada (South Bastar)', 'District', 7, 1),
(158, 'Dhamtari', 'District', 7, 1),
(159, 'Durg', 'District', 7, 1),
(160, 'Gariyaband', 'District', 7, 1),
(161, 'Janjgir-Champa', 'District', 7, 1),
(162, 'Jashpur', 'District', 7, 1),
(163, 'Kabirdham (Kawardha)', 'District', 7, 1),
(164, 'Kanker (North Bastar)', 'District', 7, 1),
(165, 'Kondagaon', 'District', 7, 1),
(166, 'Korba', 'District', 7, 1),
(167, 'Korea (Koriya)', 'District', 7, 1),
(168, 'Mahasamund', 'District', 7, 1),
(169, 'Mungeli', 'District', 7, 1),
(170, 'Narayanpur', 'District', 7, 1),
(171, 'Raigarh', 'District', 7, 1),
(172, 'Raipur', 'District', 7, 1),
(173, 'Rajnandgaon', 'District', 7, 1),
(174, 'Sukma', 'District', 7, 1),
(175, 'Surajpur', 'District', 7, 1),
(176, 'Surguja', 'District', 7, 1),
(177, 'Dadra &amp; Nagar Haveli', 'District', 8, 1),
(178, 'Daman', 'District', 9, 1),
(179, 'Diu', 'District', 9, 1),
(180, 'Central Delhi', 'District', 10, 1),
(181, 'East Delhi', 'District', 10, 1),
(182, 'New Delhi', 'District', 10, 1),
(183, 'North Delhi', 'District', 10, 1),
(184, 'North East  Delhi', 'District', 10, 1),
(185, 'North West  Delhi', 'District', 10, 1),
(186, 'Shahdara', 'District', 10, 1),
(187, 'South Delhi', 'District', 10, 1),
(188, 'South East Delhi', 'District', 10, 1),
(189, 'South West  Delhi', 'District', 10, 1),
(190, 'West Delhi', 'District', 10, 1),
(191, 'North Goa', 'District', 11, 1),
(192, 'South Goa', 'District', 11, 1),
(193, 'Ahmedabad', 'District', 12, 1),
(194, 'Amreli', 'District', 12, 1),
(195, 'Anand', 'District', 12, 1),
(196, 'Aravalli', 'District', 12, 1),
(197, 'Banaskantha (Palanpur)', 'District', 12, 1),
(198, 'Bharuch', 'District', 12, 1),
(199, 'Bhavnagar', 'District', 12, 1),
(200, 'Botad', 'District', 12, 1),
(201, 'Chhota Udepur', 'District', 12, 1),
(202, 'Dahod', 'District', 12, 1),
(203, 'Dangs (Ahwa)', 'District', 12, 1),
(204, 'Devbhoomi Dwarka', 'District', 12, 1),
(205, 'Gandhinagar', 'District', 12, 1),
(206, 'Gir Somnath', 'District', 12, 1),
(207, 'Jamnagar', 'District', 12, 1),
(208, 'Junagadh', 'District', 12, 1),
(209, 'Kachchh', 'District', 12, 1),
(210, 'Kheda (Nadiad)', 'District', 12, 1),
(211, 'Mahisagar', 'District', 12, 1),
(212, 'Mehsana', 'District', 12, 1),
(213, 'Morbi', 'District', 12, 1),
(214, 'Narmada (Rajpipla)', 'District', 12, 1),
(215, 'Navsari', 'District', 12, 1),
(216, 'Panchmahal (Godhra)', 'District', 12, 1),
(217, 'Patan', 'District', 12, 1),
(218, 'Porbandar', 'District', 12, 1),
(219, 'Rajkot', 'District', 12, 1),
(220, 'Sabarkantha (Himmatnagar)', 'District', 12, 1),
(221, 'Surat', 'District', 12, 1),
(222, 'Surendranagar', 'District', 12, 1),
(223, 'Tapi (Vyara)', 'District', 12, 1),
(224, 'Vadodara', 'District', 12, 1),
(225, 'Valsad', 'District', 12, 1),
(226, 'Ambala', 'District', 13, 1),
(227, 'Bhiwani', 'District', 13, 1),
(228, 'Charkhi Dadri', 'District', 13, 1),
(229, 'Faridabad', 'District', 13, 1),
(230, 'Fatehabad', 'District', 13, 1),
(231, 'Gurgaon', 'District', 13, 1),
(232, 'Hisar', 'District', 13, 1),
(233, 'Jhajjar', 'District', 13, 1),
(234, 'Jind', 'District', 13, 1),
(235, 'Kaithal', 'District', 13, 1),
(236, 'Karnal', 'District', 13, 1),
(237, 'Kurukshetra', 'District', 13, 1),
(238, 'Mahendragarh', 'District', 13, 1),
(239, 'Mewat', 'District', 13, 1),
(240, 'Palwal', 'District', 13, 1),
(241, 'Panchkula', 'District', 13, 1),
(242, 'Panipat', 'District', 13, 1),
(243, 'Rewari', 'District', 13, 1),
(244, 'Rohtak', 'District', 13, 1),
(245, 'Sirsa', 'District', 13, 1),
(246, 'Sonipat', 'District', 13, 1),
(247, 'Yamunanagar', 'District', 13, 1),
(248, 'Bilaspur', 'District', 14, 1),
(249, 'Chamba', 'District', 14, 1),
(250, 'Hamirpur', 'District', 14, 1),
(251, 'Kangra', 'District', 14, 1),
(252, 'Kinnaur', 'District', 14, 1),
(253, 'Kullu', 'District', 14, 1),
(254, 'Lahaul &amp; Spiti', 'District', 14, 1),
(255, 'Mandi', 'District', 14, 1),
(256, 'Shimla', 'District', 14, 1),
(257, 'Sirmaur (Sirmour)', 'District', 14, 1),
(258, 'Solan', 'District', 14, 1),
(259, 'Una', 'District', 14, 1),
(260, 'Anantnag', 'District', 15, 1),
(261, 'Bandipore', 'District', 15, 1),
(262, 'Baramulla', 'District', 15, 1),
(263, 'Budgam', 'District', 15, 1),
(264, 'Doda', 'District', 15, 1),
(265, 'Ganderbal', 'District', 15, 1),
(266, 'Jammu', 'District', 15, 1),
(267, 'Kargil', 'District', 15, 1),
(268, 'Kathua', 'District', 15, 1),
(269, 'Kishtwar', 'District', 15, 1),
(270, 'Kulgam', 'District', 15, 1),
(271, 'Kupwara', 'District', 15, 1),
(272, 'Leh', 'District', 15, 1),
(273, 'Poonch', 'District', 15, 1),
(274, 'Pulwama', 'District', 15, 1),
(275, 'Rajouri', 'District', 15, 1),
(276, 'Ramban', 'District', 15, 1),
(277, 'Reasi', 'District', 15, 1),
(278, 'Samba', 'District', 15, 1),
(279, 'Shopian', 'District', 15, 1),
(280, 'Srinagar', 'District', 15, 1),
(281, 'Udhampur', 'District', 15, 1),
(282, 'Bokaro', 'District', 16, 1),
(283, 'Chatra', 'District', 16, 1),
(284, 'Deoghar', 'District', 16, 1),
(285, 'Dhanbad', 'District', 16, 1),
(286, 'Dumka', 'District', 16, 1),
(287, 'East Singhbhum', 'District', 16, 1),
(288, 'Garhwa', 'District', 16, 1),
(289, 'Giridih', 'District', 16, 1),
(290, 'Godda', 'District', 16, 1),
(291, 'Gumla', 'District', 16, 1),
(292, 'Hazaribag', 'District', 16, 1),
(293, 'Jamtara', 'District', 16, 1),
(294, 'Khunti', 'District', 16, 1),
(295, 'Koderma', 'District', 16, 1),
(296, 'Latehar', 'District', 16, 1),
(297, 'Lohardaga', 'District', 16, 1),
(298, 'Pakur', 'District', 16, 1),
(299, 'Palamu', 'District', 16, 1),
(300, 'Ramgarh', 'District', 16, 1),
(301, 'Ranchi', 'District', 16, 1),
(302, 'Sahibganj', 'District', 16, 1),
(303, 'Seraikela-Kharsawan', 'District', 16, 1),
(304, 'Simdega', 'District', 16, 1),
(305, 'West Singhbhum', 'District', 16, 1),
(306, 'Bagalkot', 'District', 17, 1),
(307, 'Ballari (Bellary)', 'District', 17, 1),
(308, 'Belagavi (Belgaum)', 'District', 17, 1),
(309, 'Bengaluru (Bangalore) Rural', 'District', 17, 1),
(310, 'Bengaluru (Bangalore) Urban', 'District', 17, 1),
(311, 'Bidar', 'District', 17, 1),
(312, 'Chamarajanagar', 'District', 17, 1),
(313, 'Chikballapur', 'District', 17, 1),
(314, 'Chikkamagaluru (Chikmagalur)', 'District', 17, 1),
(315, 'Chitradurga', 'District', 17, 1),
(316, 'Dakshina Kannada', 'District', 17, 1),
(317, 'Davangere', 'District', 17, 1),
(318, 'Dharwad', 'District', 17, 1),
(319, 'Gadag', 'District', 17, 1),
(320, 'Hassan', 'District', 17, 1),
(321, 'Haveri', 'District', 17, 1),
(322, 'Kalaburagi (Gulbarga)', 'District', 17, 1),
(323, 'Kodagu', 'District', 17, 1),
(324, 'Kolar', 'District', 17, 1),
(325, 'Koppal', 'District', 17, 1),
(326, 'Mandya', 'District', 17, 1),
(327, 'Mysuru (Mysore)', 'District', 17, 1),
(328, 'Raichur', 'District', 17, 1),
(329, 'Ramanagara', 'District', 17, 1),
(330, 'Shivamogga (Shimoga)', 'District', 17, 1),
(331, 'Tumakuru (Tumkur)', 'District', 17, 1),
(332, 'Udupi', 'District', 17, 1),
(333, 'Uttara Kannada (Karwar)', 'District', 17, 1),
(334, 'Vijayapura (Bijapur)', 'District', 17, 1),
(335, 'Yadgir', 'District', 17, 1),
(336, 'Alappuzha', 'District', 18, 1),
(337, 'Ernakulam', 'District', 18, 1),
(338, 'Idukki', 'District', 18, 1),
(339, 'Kannur', 'District', 18, 1),
(340, 'Kasaragod', 'District', 18, 1),
(341, 'Kollam', 'District', 18, 1),
(342, 'Kottayam', 'District', 18, 1),
(343, 'Kozhikode', 'District', 18, 1),
(344, 'Malappuram', 'District', 18, 1),
(345, 'Palakkad', 'District', 18, 1),
(346, 'Pathanamthitta', 'District', 18, 1),
(347, 'Thiruvananthapuram', 'District', 18, 1),
(348, 'Thrissur', 'District', 18, 1),
(349, 'Wayanad', 'District', 18, 1),
(350, 'Lakshadweep', 'District', 19, 1),
(351, 'Agar Malwa', 'District', 20, 1),
(352, 'Alirajpur', 'District', 20, 1),
(353, 'Anuppur', 'District', 20, 1),
(354, 'Ashoknagar', 'District', 20, 1),
(355, 'Balaghat', 'District', 20, 1),
(356, 'Barwani', 'District', 20, 1),
(357, 'Betul', 'District', 20, 1),
(358, 'Bhind', 'District', 20, 1),
(359, 'Bhopal', 'District', 20, 1),
(360, 'Burhanpur', 'District', 20, 1),
(361, 'Chhatarpur', 'District', 20, 1),
(362, 'Chhindwara', 'District', 20, 1),
(363, 'Damoh', 'District', 20, 1),
(364, 'Datia', 'District', 20, 1),
(365, 'Dewas', 'District', 20, 1),
(366, 'Dhar', 'District', 20, 1),
(367, 'Dindori', 'District', 20, 1),
(368, 'Guna', 'District', 20, 1),
(369, 'Gwalior', 'District', 20, 1),
(370, 'Harda', 'District', 20, 1),
(371, 'Hoshangabad', 'District', 20, 1),
(372, 'Indore', 'District', 20, 1),
(373, 'Jabalpur', 'District', 20, 1),
(374, 'Jhabua', 'District', 20, 1),
(375, 'Katni', 'District', 20, 1),
(376, 'Khandwa', 'District', 20, 1),
(377, 'Khargone', 'District', 20, 1),
(378, 'Mandla', 'District', 20, 1),
(379, 'Mandsaur', 'District', 20, 1),
(380, 'Morena', 'District', 20, 1),
(381, 'Narsinghpur', 'District', 20, 1),
(382, 'Neemuch', 'District', 20, 1),
(383, 'Panna', 'District', 20, 1),
(384, 'Raisen', 'District', 20, 1),
(385, 'Rajgarh', 'District', 20, 1),
(386, 'Ratlam', 'District', 20, 1),
(387, 'Rewa', 'District', 20, 1),
(388, 'Sagar', 'District', 20, 1),
(389, 'Satna', 'District', 20, 1),
(390, 'Sehore', 'District', 20, 1),
(391, 'Seoni', 'District', 20, 1),
(392, 'Shahdol', 'District', 20, 1),
(393, 'Shajapur', 'District', 20, 1),
(394, 'Sheopur', 'District', 20, 1),
(395, 'Shivpuri', 'District', 20, 1),
(396, 'Sidhi', 'District', 20, 1),
(397, 'Singrauli', 'District', 20, 1),
(398, 'Tikamgarh', 'District', 20, 1),
(399, 'Ujjain', 'District', 20, 1),
(400, 'Umaria', 'District', 20, 1),
(401, 'Vidisha', 'District', 20, 1),
(402, 'Ahmednagar', 'District', 21, 1),
(403, 'Akola', 'District', 21, 1),
(404, 'Amravati', 'District', 21, 1),
(405, 'Aurangabad', 'District', 21, 1),
(406, 'Beed', 'District', 21, 1),
(407, 'Bhandara', 'District', 21, 1),
(408, 'Buldhana', 'District', 21, 1),
(409, 'Chandrapur', 'District', 21, 1),
(410, 'Dhule', 'District', 21, 1),
(411, 'Gadchiroli', 'District', 21, 1),
(412, 'Gondia', 'District', 21, 1),
(413, 'Hingoli', 'District', 21, 1),
(414, 'Jalgaon', 'District', 21, 1),
(415, 'Jalna', 'District', 21, 1),
(416, 'Kolhapur', 'District', 21, 1),
(417, 'Latur', 'District', 21, 1),
(418, 'Mumbai City', 'District', 21, 1),
(419, 'Mumbai Suburban', 'District', 21, 1),
(420, 'Nagpur', 'District', 21, 1),
(421, 'Nanded', 'District', 21, 1),
(422, 'Nandurbar', 'District', 21, 1),
(423, 'Nashik', 'District', 21, 1),
(424, 'Osmanabad', 'District', 21, 1),
(425, 'Palghar', 'District', 21, 1),
(426, 'Parbhani', 'District', 21, 1),
(427, 'Pune', 'District', 21, 1),
(428, 'Raigad', 'District', 21, 1),
(429, 'Ratnagiri', 'District', 21, 1),
(430, 'Sangli', 'District', 21, 1),
(431, 'Satara', 'District', 21, 1),
(432, 'Sindhudurg', 'District', 21, 1),
(433, 'Solapur', 'District', 21, 1),
(434, 'Thane', 'District', 21, 1),
(435, 'Wardha', 'District', 21, 1),
(436, 'Washim', 'District', 21, 1),
(437, 'Yavatmal', 'District', 21, 1),
(438, 'Bishnupur', 'District', 22, 1),
(439, 'Chandel', 'District', 22, 1),
(440, 'Churachandpur', 'District', 22, 1),
(441, 'Imphal East', 'District', 22, 1),
(442, 'Imphal West', 'District', 22, 1),
(443, 'Jiribam', 'District', 22, 1),
(444, 'Kakching', 'District', 22, 1),
(445, 'Kamjong', 'District', 22, 1),
(446, 'Kangpokpi', 'District', 22, 1),
(447, 'Noney', 'District', 22, 1),
(448, 'Pherzawl', 'District', 22, 1),
(449, 'Senapati', 'District', 22, 1),
(450, 'Tamenglong', 'District', 22, 1),
(451, 'Tengnoupal', 'District', 22, 1),
(452, 'Thoubal', 'District', 22, 1),
(453, 'Ukhrul', 'District', 22, 1),
(454, 'East Garo Hills', 'District', 23, 1),
(455, 'East Jaintia Hills', 'District', 23, 1),
(456, 'East Khasi Hills', 'District', 23, 1),
(457, 'North Garo Hills', 'District', 23, 1),
(458, 'Ri Bhoi', 'District', 23, 1),
(459, 'South Garo Hills', 'District', 23, 1),
(460, 'South West Garo Hills', 'District', 23, 1),
(461, 'South West Khasi Hills', 'District', 23, 1),
(462, 'West Garo Hills', 'District', 23, 1),
(463, 'West Jaintia Hills', 'District', 23, 1),
(464, 'West Khasi Hills', 'District', 23, 1),
(465, 'Aizawl', 'District', 24, 1),
(466, 'Champhai', 'District', 24, 1),
(467, 'Kolasib', 'District', 24, 1),
(468, 'Lawngtlai', 'District', 24, 1),
(469, 'Lunglei', 'District', 24, 1),
(470, 'Mamit', 'District', 24, 1),
(471, 'Saiha', 'District', 24, 1),
(472, 'Serchhip', 'District', 24, 1),
(473, 'Dimapur', 'District', 25, 1),
(474, 'Kiphire', 'District', 25, 1),
(475, 'Kohima', 'District', 25, 1),
(476, 'Longleng', 'District', 25, 1),
(477, 'Mokokchung', 'District', 25, 1),
(478, 'Mon', 'District', 25, 1),
(479, 'Peren', 'District', 25, 1),
(480, 'Phek', 'District', 25, 1),
(481, 'Tuensang', 'District', 25, 1),
(482, 'Wokha', 'District', 25, 1),
(483, 'Zunheboto', 'District', 25, 1),
(484, 'Angul', 'District', 26, 1),
(485, 'Balangir', 'District', 26, 1),
(486, 'Balasore', 'District', 26, 1),
(487, 'Bargarh', 'District', 26, 1),
(488, 'Bhadrak', 'District', 26, 1),
(489, 'Boudh', 'District', 26, 1),
(490, 'Cuttack', 'District', 26, 1),
(491, 'Deogarh', 'District', 26, 1),
(492, 'Dhenkanal', 'District', 26, 1),
(493, 'Gajapati', 'District', 26, 1),
(494, 'Ganjam', 'District', 26, 1),
(495, 'Jagatsinghapur', 'District', 26, 1),
(496, 'Jajpur', 'District', 26, 1),
(497, 'Jharsuguda', 'District', 26, 1),
(498, 'Kalahandi', 'District', 26, 1),
(499, 'Kandhamal', 'District', 26, 1),
(500, 'Kendrapara', 'District', 26, 1),
(501, 'Kendujhar (Keonjhar)', 'District', 26, 1),
(502, 'Khordha', 'District', 26, 1),
(503, 'Koraput', 'District', 26, 1),
(504, 'Malkangiri', 'District', 26, 1),
(505, 'Mayurbhanj', 'District', 26, 1),
(506, 'Nabarangpur', 'District', 26, 1),
(507, 'Nayagarh', 'District', 26, 1),
(508, 'Nuapada', 'District', 26, 1),
(509, 'Puri', 'District', 26, 1),
(510, 'Rayagada', 'District', 26, 1),
(511, 'Sambalpur', 'District', 26, 1),
(512, 'Sonepur', 'District', 26, 1),
(513, 'Sundargarh', 'District', 26, 1),
(514, 'Karaikal', 'District', 27, 1),
(515, 'Mahe', 'District', 27, 1),
(516, 'Pondicherry', 'District', 27, 1),
(517, 'Yanam', 'District', 27, 1),
(518, 'Amritsar', 'District', 28, 1),
(519, 'Barnala', 'District', 28, 1),
(520, 'Bathinda', 'District', 28, 1),
(521, 'Faridkot', 'District', 28, 1),
(522, 'Fatehgarh Sahib', 'District', 28, 1),
(523, 'Fazilka', 'District', 28, 1),
(524, 'Ferozepur', 'District', 28, 1),
(525, 'Gurdaspur', 'District', 28, 1),
(526, 'Hoshiarpur', 'District', 28, 1),
(527, 'Jalandhar', 'District', 28, 1),
(528, 'Kapurthala', 'District', 28, 1),
(529, 'Ludhiana', 'District', 28, 1),
(530, 'Mansa', 'District', 28, 1),
(531, 'Moga', 'District', 28, 1),
(532, 'Muktsar', 'District', 28, 1),
(533, 'Nawanshahr (Shahid Bhagat Singh Nagar)', 'District', 28, 1),
(534, 'Pathankot', 'District', 28, 1),
(535, 'Patiala', 'District', 28, 1),
(536, 'Rupnagar', 'District', 28, 1),
(537, 'Sahibzada Ajit Singh Nagar (Mohali)', 'District', 28, 1),
(538, 'Sangrur', 'District', 28, 1),
(539, 'Tarn Taran', 'District', 28, 1),
(540, 'Ajmer', 'District', 29, 1),
(541, 'Alwar', 'District', 29, 1),
(542, 'Banswara', 'District', 29, 1),
(543, 'Baran', 'District', 29, 1),
(544, 'Barmer', 'District', 29, 1),
(545, 'Bharatpur', 'District', 29, 1),
(546, 'Bhilwara', 'District', 29, 1),
(547, 'Bikaner', 'District', 29, 1),
(548, 'Bundi', 'District', 29, 1),
(549, 'Chittorgarh', 'District', 29, 1),
(550, 'Churu', 'District', 29, 1),
(551, 'Dausa', 'District', 29, 1),
(552, 'Dholpur', 'District', 29, 1),
(553, 'Dungarpur', 'District', 29, 1),
(554, 'Hanumangarh', 'District', 29, 1),
(555, 'Jaipur', 'District', 29, 1),
(556, 'Jaisalmer', 'District', 29, 1),
(557, 'Jalore', 'District', 29, 1),
(558, 'Jhalawar', 'District', 29, 1),
(559, 'Jhunjhunu', 'District', 29, 1),
(560, 'Jodhpur', 'District', 29, 1),
(561, 'Karauli', 'District', 29, 1),
(562, 'Kota', 'District', 29, 1),
(563, 'Nagaur', 'District', 29, 1),
(564, 'Pali', 'District', 29, 1),
(565, 'Pratapgarh', 'District', 29, 1),
(566, 'Rajsamand', 'District', 29, 1),
(567, 'Sawai Madhopur', 'District', 29, 1),
(568, 'Sikar', 'District', 29, 1),
(569, 'Sirohi', 'District', 29, 1),
(570, 'Sri Ganganagar', 'District', 29, 1),
(571, 'Tonk', 'District', 29, 1),
(572, 'Udaipur', 'District', 29, 1),
(573, 'East Sikkim', 'District', 30, 1),
(574, 'North Sikkim', 'District', 30, 1),
(575, 'South Sikkim', 'District', 30, 1),
(576, 'West Sikkim', 'District', 30, 1),
(577, 'Ariyalur', 'District', 31, 1),
(578, 'Chennai', 'District', 31, 1),
(579, 'Coimbatore', 'District', 31, 1),
(580, 'Cuddalore', 'District', 31, 1),
(581, 'Dharmapuri', 'District', 31, 1),
(582, 'Dindigul', 'District', 31, 1),
(583, 'Erode', 'District', 31, 1),
(584, 'Kanchipuram', 'District', 31, 1),
(585, 'Kanyakumari', 'District', 31, 1),
(586, 'Karur', 'District', 31, 1),
(587, 'Krishnagiri', 'District', 31, 1),
(588, 'Madurai', 'District', 31, 1),
(589, 'Nagapattinam', 'District', 31, 1),
(590, 'Namakkal', 'District', 31, 1),
(591, 'Nilgiris', 'District', 31, 1),
(592, 'Perambalur', 'District', 31, 1),
(593, 'Pudukkottai', 'District', 31, 1),
(594, 'Ramanathapuram', 'District', 31, 1),
(595, 'Salem', 'District', 31, 1),
(596, 'Sivaganga', 'District', 31, 1),
(597, 'Thanjavur', 'District', 31, 1),
(598, 'Theni', 'District', 31, 1),
(599, 'Thoothukudi (Tuticorin)', 'District', 31, 1),
(600, 'Tiruchirappalli', 'District', 31, 1),
(601, 'Tirunelveli', 'District', 31, 1),
(602, 'Tiruppur', 'District', 31, 1),
(603, 'Tiruvallur', 'District', 31, 1),
(604, 'Tiruvannamalai', 'District', 31, 1),
(605, 'Tiruvarur', 'District', 31, 1),
(606, 'Vellore', 'District', 31, 1),
(607, 'Viluppuram', 'District', 31, 1),
(608, 'Virudhunagar', 'District', 31, 1),
(609, 'Adilabad', 'District', 32, 1),
(610, 'Bhadradri Kothagudem', 'District', 32, 1),
(611, 'Hyderabad', 'District', 32, 1),
(612, 'Jagtial', 'District', 32, 1),
(613, 'Jangaon', 'District', 32, 1),
(614, 'Jayashankar Bhoopalpally', 'District', 32, 1),
(615, 'Jogulamba Gadwal', 'District', 32, 1),
(616, 'Kamareddy', 'District', 32, 1),
(617, 'Karimnagar', 'District', 32, 1),
(618, 'Khammam', 'District', 32, 1),
(619, 'Komaram Bheem Asifabad', 'District', 32, 1),
(620, 'Mahabubabad', 'District', 32, 1),
(621, 'Mahabubnagar', 'District', 32, 1),
(622, 'Mancherial', 'District', 32, 1),
(623, 'Medak', 'District', 32, 1),
(624, 'Medchal', 'District', 32, 1),
(625, 'Nagarkurnool', 'District', 32, 1),
(626, 'Nalgonda', 'District', 32, 1),
(627, 'Nirmal', 'District', 32, 1),
(628, 'Nizamabad', 'District', 32, 1),
(629, 'Peddapalli', 'District', 32, 1),
(630, 'Rajanna Sircilla', 'District', 32, 1),
(631, 'Rangareddy', 'District', 32, 1),
(632, 'Sangareddy', 'District', 32, 1),
(633, 'Siddipet', 'District', 32, 1),
(634, 'Suryapet', 'District', 32, 1),
(635, 'Vikarabad', 'District', 32, 1),
(636, 'Wanaparthy', 'District', 32, 1),
(637, 'Warangal (Rural)', 'District', 32, 1),
(638, 'Warangal (Urban)', 'District', 32, 1),
(639, 'Yadadri Bhuvanagiri', 'District', 32, 1),
(640, 'Dhalai', 'District', 33, 1),
(641, 'Gomati', 'District', 33, 1),
(642, 'Khowai', 'District', 33, 1),
(643, 'North Tripura', 'District', 33, 1),
(644, 'Sepahijala', 'District', 33, 1),
(645, 'South Tripura', 'District', 33, 1),
(646, 'Unakoti', 'District', 33, 1),
(647, 'West Tripura', 'District', 33, 1),
(648, 'Almora', 'District', 34, 1),
(649, 'Bageshwar', 'District', 34, 1),
(650, 'Chamoli', 'District', 34, 1),
(651, 'Champawat', 'District', 34, 1),
(652, 'Dehradun', 'District', 34, 1),
(653, 'Haridwar', 'District', 34, 1),
(654, 'Nainital', 'District', 34, 1),
(655, 'Pauri Garhwal', 'District', 34, 1),
(656, 'Pithoragarh', 'District', 34, 1),
(657, 'Rudraprayag', 'District', 34, 1),
(658, 'Tehri Garhwal', 'District', 34, 1),
(659, 'Udham Singh Nagar', 'District', 34, 1),
(660, 'Uttarkashi', 'District', 34, 1),
(661, 'Agra', 'District', 35, 1),
(662, 'Aligarh', 'District', 35, 1),
(663, 'Allahabad', 'District', 35, 1),
(664, 'Ambedkar Nagar', 'District', 35, 1),
(665, 'Amethi (Chatrapati Sahuji Mahraj Nagar)', 'District', 35, 1),
(666, 'Amroha (J.P. Nagar)', 'District', 35, 1),
(667, 'Auraiya', 'District', 35, 1),
(668, 'Azamgarh', 'District', 35, 1),
(669, 'Baghpat', 'District', 35, 1),
(670, 'Bahraich', 'District', 35, 1),
(671, 'Ballia', 'District', 35, 1),
(672, 'Balrampur', 'District', 35, 1),
(673, 'Banda', 'District', 35, 1),
(674, 'Barabanki', 'District', 35, 1),
(675, 'Bareilly', 'District', 35, 1),
(676, 'Basti', 'District', 35, 1),
(677, 'Bhadohi', 'District', 35, 1),
(678, 'Bijnor', 'District', 35, 1),
(679, 'Budaun', 'District', 35, 1),
(680, 'Bulandshahr', 'District', 35, 1),
(681, 'Chandauli', 'District', 35, 1),
(682, 'Chitrakoot', 'District', 35, 1),
(683, 'Deoria', 'District', 35, 1),
(684, 'Etah', 'District', 35, 1),
(685, 'Etawah', 'District', 35, 1),
(686, 'Faizabad', 'District', 35, 1),
(687, 'Farrukhabad', 'District', 35, 1),
(688, 'Fatehpur', 'District', 35, 1),
(689, 'Firozabad', 'District', 35, 1),
(690, 'Gautam Buddha Nagar', 'District', 35, 1),
(691, 'Ghaziabad', 'District', 35, 1),
(692, 'Ghazipur', 'District', 35, 1),
(693, 'Gonda', 'District', 35, 1),
(694, 'Gorakhpur', 'District', 35, 1),
(695, 'Hamirpur', 'District', 35, 1),
(696, 'Hapur (Panchsheel Nagar)', 'District', 35, 1),
(697, 'Hardoi', 'District', 35, 1),
(698, 'Hathras', 'District', 35, 1),
(699, 'Jalaun', 'District', 35, 1),
(700, 'Jaunpur', 'District', 35, 1),
(701, 'Jhansi', 'District', 35, 1),
(702, 'Kannauj', 'District', 35, 1),
(703, 'Kanpur Dehat', 'District', 35, 1),
(704, 'Kanpur Nagar', 'District', 35, 1),
(705, 'Kanshiram Nagar (Kasganj)', 'District', 35, 1),
(706, 'Kaushambi', 'District', 35, 1),
(707, 'Kushinagar (Padrauna)', 'District', 35, 1),
(708, 'Lakhimpur - Kheri', 'District', 35, 1),
(709, 'Lalitpur', 'District', 35, 1),
(710, 'Lucknow', 'District', 35, 1),
(711, 'Maharajganj', 'District', 35, 1),
(712, 'Mahoba', 'District', 35, 1),
(713, 'Mainpuri', 'District', 35, 1),
(714, 'Mathura', 'District', 35, 1),
(715, 'Mau', 'District', 35, 1),
(716, 'Meerut', 'District', 35, 1),
(717, 'Mirzapur', 'District', 35, 1),
(718, 'Moradabad', 'District', 35, 1),
(719, 'Muzaffarnagar', 'District', 35, 1),
(720, 'Pilibhit', 'District', 35, 1),
(721, 'Pratapgarh', 'District', 35, 1),
(722, 'RaeBareli', 'District', 35, 1),
(723, 'Rampur', 'District', 35, 1),
(724, 'Saharanpur', 'District', 35, 1),
(725, 'Sambhal (Bhim Nagar)', 'District', 35, 1),
(726, 'Sant Kabir Nagar', 'District', 35, 1),
(727, 'Shahjahanpur', 'District', 35, 1),
(728, 'Shamali (Prabuddh Nagar)', 'District', 35, 1),
(729, 'Shravasti', 'District', 35, 1),
(730, 'Siddharth Nagar', 'District', 35, 1),
(731, 'Sitapur', 'District', 35, 1),
(732, 'Sonbhadra', 'District', 35, 1),
(733, 'Sultanpur', 'District', 35, 1),
(734, 'Unnao', 'District', 35, 1),
(735, 'Varanasi', 'District', 35, 1),
(736, 'Alipurduar', 'District', 36, 1),
(737, 'Bankura', 'District', 36, 1),
(738, 'Birbhum', 'District', 36, 1),
(739, 'Cooch Behar', 'District', 36, 1),
(740, 'Dakshin Dinajpur (South Dinajpur)', 'District', 36, 1),
(741, 'Darjeeling', 'District', 36, 1),
(742, 'Hooghly', 'District', 36, 1),
(743, 'Howrah', 'District', 36, 1),
(744, 'Jalpaiguri', 'District', 36, 1),
(745, 'Jhargram', 'District', 36, 1),
(746, 'Kalimpong', 'District', 36, 1),
(747, 'Kolkata', 'District', 36, 1),
(748, 'Malda', 'District', 36, 1),
(749, 'Murshidabad', 'District', 36, 1),
(750, 'Nadia', 'District', 36, 1),
(751, 'North 24 Parganas', 'District', 36, 1),
(752, 'Paschim Medinipur (West Medinipur)', 'District', 36, 1),
(753, 'Paschim (West) Burdwan (Bardhaman)', 'District', 36, 1),
(754, 'Purba Burdwan (Bardhaman)', 'District', 36, 1),
(755, 'Purba Medinipur (East Medinipur)', 'District', 36, 1),
(756, 'Purulia', 'District', 36, 1),
(757, 'South 24 Parganas', 'District', 36, 1),
(758, 'Uttar Dinajpur (North Dinajpur)', 'District', 36, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tf_assessments`
--

CREATE TABLE `tf_assessments` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `order_id` int(11) NOT NULL,
  `firm_id` int(11) DEFAULT NULL,
  `file` varchar(200) NOT NULL,
  `remarks` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tf_chats`
--

CREATE TABLE `tf_chats` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `added_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tf_commission`
--

CREATE TABLE `tf_commission` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `emp_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_amount` float NOT NULL,
  `percent` float NOT NULL,
  `amount` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tf_commission_percent`
--

CREATE TABLE `tf_commission_percent` (
  `id` int(11) NOT NULL,
  `percent` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tf_commission_percent`
--

INSERT INTO `tf_commission_percent` (`id`, `percent`, `status`, `added_on`, `updated_on`) VALUES
(1, 5, 1, '2025-03-20 17:07:44', '2025-03-20 17:07:44');

-- --------------------------------------------------------

--
-- Table structure for table `tf_customers`
--

CREATE TABLE `tf_customers` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `mobile` varchar(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `state` varchar(100) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `district` varchar(100) DEFAULT NULL,
  `area_id` int(11) DEFAULT NULL,
  `pincode` varchar(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  `added_by` int(11) DEFAULT NULL,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tf_customer_packages`
--

CREATE TABLE `tf_customer_packages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `firm_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `autodebit` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tf_docs_required`
--

CREATE TABLE `tf_docs_required` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `display_name` varchar(50) NOT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tf_docs_required`
--

INSERT INTO `tf_docs_required` (`id`, `service_id`, `document_id`, `display_name`, `slug`, `status`) VALUES
(1, 16, 1, 'Mobile', 'pan-registration-mobile', 1),
(2, 16, 2, 'Email', 'pan-registration-email', 1),
(3, 16, 4, 'Aadhar', 'pan-registration-aadhar', 1),
(4, 16, 5, 'Photo', 'pan-registration-photo', 1),
(5, 17, 1, 'Mobile', 'gst-registration-mobile', 1),
(6, 17, 2, 'Email', 'gst-registration-email', 1),
(7, 17, 4, 'Aadhar', 'gst-registration-aadhar', 1),
(8, 17, 3, 'PAN', 'gst-registration-pan', 1),
(9, 17, 7, 'Business Name', 'gst-registration-business-name', 1),
(10, 17, 6, 'Business Address Proof', 'gst-registration-business-address-proof', 1),
(11, 17, 5, 'Photo', 'gst-registration-photo', 1),
(12, 19, 1, 'Mobile', 'tan-registration-mobile', 1),
(13, 19, 2, 'Email', 'tan-registration-email', 1),
(14, 19, 4, 'Aadhar', 'tan-registration-aadhar', 1),
(15, 19, 3, 'PAN', 'tan-registration-pan', 1),
(16, 19, 5, 'Photo', 'tan-registration-photo', 1),
(17, 19, 8, 'Digital Signature', 'tan-registration-digital-signature', 1),
(18, 6, 1, 'Mobile', 'digital-signature-mobile', 1),
(19, 6, 2, 'Email', 'digital-signature-email', 1),
(20, 6, 3, 'PAN', 'digital-signature-pan', 1),
(21, 6, 4, 'Aadhar', 'digital-signature-aadhar', 1),
(22, 6, 5, 'Photo', 'digital-signature-photo', 1),
(23, 22, 1, 'Mobile', 'jpt-registration-mobile', 1),
(24, 22, 2, 'Email', 'jpt-registration-email', 1),
(25, 22, 3, 'PAN', 'jpt-registration-pan', 1),
(26, 22, 4, 'Aadhar', 'jpt-registration-aadhar', 1),
(27, 22, 10, 'GST Certificate', 'jpt-registration-gst-certificate', 1),
(28, 22, 5, 'Photo', 'jpt-registration-photo', 1),
(29, 20, 1, 'Mobile', 'din-registration-mobile', 1),
(30, 20, 2, 'Email', 'din-registration-email', 1),
(31, 20, 3, 'PAN', 'din-registration-pan', 1),
(32, 20, 4, 'Aadhar', 'din-registration-aadhar', 1),
(33, 20, 8, 'Digital Signature', 'din-registration-digital-signature', 1),
(34, 20, 5, 'Photo', 'din-registration-photo', 1),
(35, 8, 1, 'Mobile', 'tds-tcs-return-mobile', 1),
(36, 8, 2, 'Email', 'tds-tcs-return-email', 1),
(37, 8, 3, 'PAN', 'tds-tcs-return-pan', 1),
(38, 8, 4, 'Aadhar', 'tds-tcs-return-aadhar', 1),
(39, 8, 19, 'Freight Paid', 'tds-tcs-return-freight-paid', 1),
(40, 8, 20, 'Interest Paid', 'tds-tcs-return-interest-paid', 1),
(41, 1, 11, 'Partnership Deed', 'accountancy-work-partnership-deed', 1),
(48, 2, 3, 'PAN', 'income-tax-pan', 1),
(49, 9, 3, 'PAN', 'professional-tax-return-pan', 1),
(50, 7, 13, 'Sales and GST', 'gst-return-sales-and-gst', 1),
(52, 24, 21, 'Purchase', 'trading-profile-loss-balance-sheet-purchase', 1),
(53, 24, 22, 'Sales', 'trading-profile-loss-balance-sheet-sales', 1),
(54, 24, 23, 'Expenditure', 'trading-profile-loss-balance-sheet-expenditure', 1),
(55, 25, 15, 'Bank Statement', 'trading-pl-bs-ca-certified-bank-statement', 1),
(56, 25, 21, 'Purchase', 'trading-pl-bs-ca-certified-purchase', 1),
(57, 25, 22, 'Sales', 'trading-pl-bs-ca-certified-sales', 1),
(58, 25, 23, 'Expenditure', 'trading-pl-bs-ca-certified-expenditure', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tf_documents`
--

CREATE TABLE `tf_documents` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `slug` varchar(30) NOT NULL,
  `value` tinyint(1) NOT NULL,
  `file` tinyint(1) NOT NULL DEFAULT 0,
  `file_type` varchar(20) DEFAULT NULL,
  `pattern` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tf_documents`
--

INSERT INTO `tf_documents` (`id`, `name`, `slug`, `value`, `file`, `file_type`, `pattern`) VALUES
(1, 'Mobile', 'mobile', 1, 0, NULL, '[0-9]{10}'),
(2, 'Email', 'email', 1, 0, NULL, '^[\\w\\.-]+@[a-zA-Z\\d\\.-]+\\.[a-zA-Z]{2,}$'),
(3, 'PAN', 'pan', 1, 1, 'png|jpg|jpeg', '^[A-Z]{5}\\d{4}[A-Z]$'),
(4, 'Aadhar', 'aadhar', 1, 2, 'png|jpg|jpeg', '[0-9]{12}'),
(5, 'Photo', 'photo', 0, 1, 'png|jpg|jpeg', ''),
(6, 'Business Address Proof', 'business-address-proof', 0, 1, 'png|jpg|jpeg|pdf', ''),
(7, 'Business Name', 'business-name', 1, 1, 'png|jpg|jpeg|pdf', ''),
(8, 'Digital Signature', 'digital-signature', 1, 0, NULL, ''),
(9, 'DIN of Director', 'din-of-director', 0, 1, 'png|jpg|jpeg|pdf', ''),
(10, 'GST Certificate', 'gst-certificate', 1, 1, 'png|jpg|jpeg|pdf', ''),
(11, 'Partnership Deed', 'partnership-deed', 0, 1, 'png|jpg|jpeg|pdf', ''),
(12, 'Memorandom of Association', 'memorandom-of-association', 0, 1, 'png|jpg|jpeg|pdf', ''),
(13, 'Sales and GST', 'sales-gst', 0, 1, 'csv|xlsx|pdf', ''),
(14, 'Purchase and GST', 'purchase-gst', 0, 1, 'csv|xlsx|pdf', ''),
(15, 'Bank Statement', 'bank-statement', 0, 1, 'csv|xlsx|pdf', ''),
(16, 'All Income Details', 'all-income-details', 0, 1, 'csv|xlsx|pdf', ''),
(17, 'Turnover', 'turnover', 1, 0, NULL, ''),
(18, 'Audit Report', 'audit-report', 0, 1, 'csv|xlsx|pdf', ''),
(19, 'Freight Paid', 'freight-paid', 0, 1, 'csv|xlsx|pdf', ''),
(20, 'Interest Paid', 'interest-paid', 0, 1, 'csv|xlsx|pdf', ''),
(21, 'Purchase', 'purchase', 0, 1, 'csv|xlsx|pdf', ''),
(22, 'Sales', 'sales', 0, 1, 'csv|xlsx|pdf', ''),
(23, 'Expenditure', 'expenditure', 0, 1, 'csv|xlsx|pdf', ''),
(24, 'Bilty', 'bilty', 0, 1, 'csv|xlsx|pdf', ''),
(25, 'Company Statement', 'company-statement', 0, 1, 'csv|xlsx|pdf', '');

-- --------------------------------------------------------

--
-- Table structure for table `tf_employees`
--

CREATE TABLE `tf_employees` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `address` text NOT NULL,
  `state` varchar(100) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `district` varchar(100) NOT NULL,
  `area_id` int(11) NOT NULL,
  `pan` varchar(10) NOT NULL,
  `aadhar` varchar(20) NOT NULL,
  `percent` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tf_emp_percent`
--

CREATE TABLE `tf_emp_percent` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `percent` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tf_firms`
--

CREATE TABLE `tf_firms` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `gstin` varchar(30) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `request` tinyint(1) NOT NULL DEFAULT 0,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tf_formdata`
--

CREATE TABLE `tf_formdata` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `firm_id` int(11) DEFAULT NULL,
  `service_id` int(11) NOT NULL,
  `field` varchar(500) NOT NULL,
  `field_id` int(11) NOT NULL,
  `value` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `added_by` int(11) DEFAULT NULL,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tf_kyc`
--

CREATE TABLE `tf_kyc` (
  `id` int(11) NOT NULL,
  `pan` varchar(10) NOT NULL,
  `pan_image` varchar(100) NOT NULL,
  `aadhar` varchar(12) NOT NULL,
  `aadhar_image` varchar(100) NOT NULL,
  `aadhar_back` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tf_notify`
--

CREATE TABLE `tf_notify` (
  `id` int(11) NOT NULL,
  `type` varchar(30) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tf_order_assign`
--

CREATE TABLE `tf_order_assign` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `done_by` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tf_packages`
--

CREATE TABLE `tf_packages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `turnover` float NOT NULL,
  `remarks` varchar(100) NOT NULL,
  `rate` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tf_packages`
--

INSERT INTO `tf_packages` (`id`, `name`, `turnover`, `remarks`, `rate`, `status`) VALUES
(1, 'Accountancy Prime', 2500000, '<25 Lac', 12000, 1),
(2, 'Accountancy Prime', 5000000, '<50 Lac', 20000, 1),
(3, 'Accountancy Prime', 7500000, '<75 Lac', 25000, 1),
(4, 'Accountancy Prime', 10000000, '<100 Lac', 30000, 1),
(5, 'Accountancy Prime', 10000000, '>100 Lac Per 100 Lac', 10000, 1),
(6, 'Accountancy Premium', 2500000, '<25 Lac', 15000, 1),
(7, 'Accountancy Premium', 5000000, '<50 Lac', 24000, 1),
(8, 'Accountancy Premium', 7500000, '<75 Lac', 30000, 1),
(9, 'Accountancy Premium', 10000000, '<100 Lac', 36000, 1),
(10, 'Accountancy Premium', 10000000, '>100 Lac Per 100 Lac', 15000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tf_payment`
--

CREATE TABLE `tf_payment` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `amount` double(15,2) NOT NULL,
  `remarks` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tf_purchases`
--

CREATE TABLE `tf_purchases` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `year` varchar(8) NOT NULL,
  `type` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `firm_id` int(11) NOT NULL,
  `amount` float NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tf_request_log`
--

CREATE TABLE `tf_request_log` (
  `id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `ip_address` varchar(20) NOT NULL,
  `post` longtext DEFAULT NULL,
  `server` longtext DEFAULT NULL,
  `cookie` longtext DEFAULT NULL,
  `headers` longtext DEFAULT NULL,
  `added_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tf_request_log`
--

INSERT INTO `tf_request_log` (`id`, `url`, `ip_address`, `post`, `server`, `cookie`, `headers`, `added_on`) VALUES
(1, 'http://localhost/projects/taxefi/home/', '::1', '{\"post\":[],\"get\":[]}', '{\"REDIRECT_MIBDIRS\":\"C:\\/xampp\\/php\\/extras\\/mibs\",\"REDIRECT_MYSQL_HOME\":\"\\\\xampp\\\\mysql\\\\bin\",\"REDIRECT_OPENSSL_CONF\":\"C:\\/xampp\\/apache\\/bin\\/openssl.cnf\",\"REDIRECT_PHP_PEAR_SYSCONF_DIR\":\"\\\\xampp\\\\php\",\"REDIRECT_PHPRC\":\"\\\\xampp\\\\php\",\"REDIRECT_TMP\":\"\\\\xampp\\\\tmp\",\"REDIRECT_STATUS\":\"200\",\"MIBDIRS\":\"C:\\/xampp\\/php\\/extras\\/mibs\",\"MYSQL_HOME\":\"\\\\xampp\\\\mysql\\\\bin\",\"OPENSSL_CONF\":\"C:\\/xampp\\/apache\\/bin\\/openssl.cnf\",\"PHP_PEAR_SYSCONF_DIR\":\"\\\\xampp\\\\php\",\"PHPRC\":\"\\\\xampp\\\\php\",\"TMP\":\"\\\\xampp\\\\tmp\",\"HTTP_HOST\":\"localhost\",\"HTTP_CONNECTION\":\"keep-alive\",\"HTTP_CACHE_CONTROL\":\"max-age=0\",\"HTTP_SEC_CH_UA\":\"\\\"Chromium\\\";v=\\\"134\\\", \\\"Not:A-Brand\\\";v=\\\"24\\\", \\\"Google Chrome\\\";v=\\\"134\\\"\",\"HTTP_SEC_CH_UA_MOBILE\":\"?0\",\"HTTP_SEC_CH_UA_PLATFORM\":\"\\\"Windows\\\"\",\"HTTP_UPGRADE_INSECURE_REQUESTS\":\"1\",\"HTTP_USER_AGENT\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/134.0.0.0 Safari\\/537.36\",\"HTTP_ACCEPT\":\"text\\/html,application\\/xhtml+xml,application\\/xml;q=0.9,image\\/avif,image\\/webp,image\\/apng,*\\/*;q=0.8,application\\/signed-exchange;v=b3;q=0.7\",\"HTTP_SEC_FETCH_SITE\":\"same-origin\",\"HTTP_SEC_FETCH_MODE\":\"navigate\",\"HTTP_SEC_FETCH_USER\":\"?1\",\"HTTP_SEC_FETCH_DEST\":\"document\",\"HTTP_REFERER\":\"http:\\/\\/localhost\\/projects\\/taxefi\\/login\\/\",\"HTTP_ACCEPT_ENCODING\":\"gzip, deflate, br, zstd\",\"HTTP_ACCEPT_LANGUAGE\":\"en-US,en;q=0.9\",\"HTTP_COOKIE\":\"cookie_data=C6FnhHds9ZTBW5yeGYOMfQJx0owNAit4; _ga=GA1.1.257865318.1738730735; _clck=fk9nl2%7C2%7Cft6%7C0%7C1862; _fbp=fb.0.1738730735181.932340597505875235; _ga_K4WL8RC1LM=GS1.1.1738730735.1.1.1738730759.0.0.0; _ga_YTE2Q8S8XN=GS1.1.1741420111.2.0.1741420116.0.0.0; ci_session=kati8n7pr378qeesc2hf05rohv7ek5qd\",\"PATH\":\"C:\\\\Windows\\\\system32;C:\\\\Windows;C:\\\\Windows\\\\System32\\\\Wbem;C:\\\\Windows\\\\System32\\\\WindowsPowerShell\\\\v1.0\\\\;C:\\\\Windows\\\\System32\\\\OpenSSH\\\\;C:\\\\Program Files\\\\Git\\\\cmd;C:\\\\xampp\\\\php;C:\\\\ProgramData\\\\ComposerSetup\\\\bin;C:\\\\Program Files\\\\nodejs\\\\;C:\\\\Windows\\\\system32\\\\config\\\\systemprofile\\\\AppData\\\\Local\\\\Microsoft\\\\WindowsApps\",\"SystemRoot\":\"C:\\\\Windows\",\"COMSPEC\":\"C:\\\\Windows\\\\system32\\\\cmd.exe\",\"PATHEXT\":\".COM;.EXE;.BAT;.CMD;.VBS;.VBE;.JS;.JSE;.WSF;.WSH;.MSC\",\"WINDIR\":\"C:\\\\Windows\",\"SERVER_SIGNATURE\":\"<address>Apache\\/2.4.58 (Win64) OpenSSL\\/3.1.3 PHP\\/8.2.12 Server at localhost Port 80<\\/address>\\n\",\"SERVER_SOFTWARE\":\"Apache\\/2.4.58 (Win64) OpenSSL\\/3.1.3 PHP\\/8.2.12\",\"SERVER_NAME\":\"localhost\",\"SERVER_ADDR\":\"::1\",\"SERVER_PORT\":\"80\",\"REMOTE_ADDR\":\"::1\",\"DOCUMENT_ROOT\":\"C:\\/xampp\\/htdocs\",\"REQUEST_SCHEME\":\"http\",\"CONTEXT_PREFIX\":\"\",\"CONTEXT_DOCUMENT_ROOT\":\"C:\\/xampp\\/htdocs\",\"SERVER_ADMIN\":\"postmaster@localhost\",\"SCRIPT_FILENAME\":\"C:\\/xampp\\/htdocs\\/projects\\/taxefi\\/index.php\",\"REMOTE_PORT\":\"62588\",\"REDIRECT_URL\":\"\\/projects\\/taxefi\\/home\\/\",\"REDIRECT_QUERY_STRING\":\"home\\/\",\"GATEWAY_INTERFACE\":\"CGI\\/1.1\",\"SERVER_PROTOCOL\":\"HTTP\\/1.1\",\"REQUEST_METHOD\":\"GET\",\"QUERY_STRING\":\"\",\"REQUEST_URI\":\"\\/projects\\/taxefi\\/home\\/\",\"SCRIPT_NAME\":\"\\/projects\\/taxefi\\/index.php\",\"PHP_SELF\":\"\\/projects\\/taxefi\\/index.php\",\"REQUEST_TIME_FLOAT\":1742470412.599796,\"REQUEST_TIME\":1742470412}', '{\"cookie_data\":\"C6FnhHds9ZTBW5yeGYOMfQJx0owNAit4\",\"_ga\":\"GA1.1.257865318.1738730735\",\"_clck\":\"fk9nl2|2|ft6|0|1862\",\"_fbp\":\"fb.0.1738730735181.932340597505875235\",\"_ga_K4WL8RC1LM\":\"GS1.1.1738730735.1.1.1738730759.0.0.0\",\"_ga_YTE2Q8S8XN\":\"GS1.1.1741420111.2.0.1741420116.0.0.0\",\"ci_session\":\"kati8n7pr378qeesc2hf05rohv7ek5qd\"}', '{\"Host\":\"localhost\",\"Connection\":\"keep-alive\",\"Cache-Control\":\"max-age=0\",\"sec-ch-ua\":\"\\\"Chromium\\\";v=\\\"134\\\", \\\"Not:A-Brand\\\";v=\\\"24\\\", \\\"Google Chrome\\\";v=\\\"134\\\"\",\"sec-ch-ua-mobile\":\"?0\",\"sec-ch-ua-platform\":\"\\\"Windows\\\"\",\"Upgrade-Insecure-Requests\":\"1\",\"User-Agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/134.0.0.0 Safari\\/537.36\",\"Accept\":\"text\\/html,application\\/xhtml+xml,application\\/xml;q=0.9,image\\/avif,image\\/webp,image\\/apng,*\\/*;q=0.8,application\\/signed-exchange;v=b3;q=0.7\",\"Sec-Fetch-Site\":\"same-origin\",\"Sec-Fetch-Mode\":\"navigate\",\"Sec-Fetch-User\":\"?1\",\"Sec-Fetch-Dest\":\"document\",\"Referer\":\"http:\\/\\/localhost\\/projects\\/taxefi\\/login\\/\",\"Accept-Encoding\":\"gzip, deflate, br, zstd\",\"Accept-Language\":\"en-US,en;q=0.9\",\"Cookie\":\"cookie_data=C6FnhHds9ZTBW5yeGYOMfQJx0owNAit4; _ga=GA1.1.257865318.1738730735; _clck=fk9nl2%7C2%7Cft6%7C0%7C1862; _fbp=fb.0.1738730735181.932340597505875235; _ga_K4WL8RC1LM=GS1.1.1738730735.1.1.1738730759.0.0.0; _ga_YTE2Q8S8XN=GS1.1.1741420111.2.0.1741420116.0.0.0; ci_session=kati8n7pr378qeesc2hf05rohv7ek5qd\"}', '2025-03-20 17:03:32');

-- --------------------------------------------------------

--
-- Table structure for table `tf_roles`
--

CREATE TABLE `tf_roles` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `slug` varchar(30) NOT NULL,
  `sections` varchar(300) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tf_roles`
--

INSERT INTO `tf_roles` (`id`, `name`, `slug`, `sections`, `status`) VALUES
(1, 'Sub-admin', 'sub-admin', 'Section 1,Section 2', 1),
(2, 'Sales officers', 'sales-officers', 'Section 1,Section 2', 1),
(3, 'CA', 'ca', 'Section 1,Section 2', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tf_services`
--

CREATE TABLE `tf_services` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `fixed` tinyint(1) NOT NULL DEFAULT 0,
  `rate` float NOT NULL,
  `type` varchar(100) NOT NULL,
  `service_for` varchar(100) NOT NULL,
  `debit_date` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tf_services`
--

INSERT INTO `tf_services` (`id`, `name`, `slug`, `fixed`, `rate`, `type`, `service_for`, `debit_date`, `status`) VALUES
(1, 'Account  Work', 'account-work', 0, 10000, 'Turnover,Monthly', 'Individual', NULL, 1),
(2, 'Income Tax Non Audit ', 'income-tax-non-audit', 0, 1500, 'Yearly', 'Firm', '2025-05-01', 1),
(3, 'Project Report DPR', 'project-report-dpr', 0, 2500, 'Yearly', 'Firm', '2025-01-01', 1),
(4, 'Tax Audit', 'tax-audit', 0, 10000, 'Yearly', 'Firm', '2025-05-31', 1),
(5, 'VAT Audit', 'vat-audit', 0, 2500, 'Yearly', 'Firm', '2025-06-30', 1),
(6, 'Digital Signature', 'digital-signature', 1, 2500, 'Once', 'Firm', NULL, 1),
(7, 'GST Return', 'gst-return', 1, 1000, 'Quarterly,Monthly', 'Individual', '2025-02-20', 1),
(8, 'TDS & TCS Return', 'tds-tcs-return', 1, 2500, 'Quarterly', 'Firm', '2025-01-02', 1),
(9, 'Professional Tax Return', 'professional-tax-return', 1, 500, 'Quarterly', 'Firm', '2025-01-23', 1),
(10, 'Tax Challan', 'tax-challan', 1, 200, 'Once', 'Firm', NULL, 1),
(11, 'Udyam Aadhar', 'udyam-aadhar', 1, 1500, 'Once', 'Firm', NULL, 1),
(12, 'INCOME TAX COMPLIANCES', 'income-tax-compliances', 1, 5000, 'Once', 'Firm', NULL, 1),
(13, 'GST Compliances', 'gst-compliances', 1, 3000, 'Once', 'Firm', NULL, 1),
(14, 'TDS Compliances', 'tds-compliances', 1, 4000, 'Once', 'Firm', NULL, 1),
(15, 'Firm Registration', 'firm-registration', 1, 10000, 'Once', 'Firm', NULL, 1),
(16, 'PAN Registration', 'pan-registration', 1, 300, 'Once', 'Firm', NULL, 1),
(17, 'GST Registration', 'gst-registration', 1, 1000, 'Once', 'Firm', NULL, 1),
(18, 'Company Registration', 'company-registration', 1, 20000, 'Once', 'Firm', NULL, 1),
(19, 'TAN Registration', 'tan-registration', 1, 1000, 'Once', 'Firm', NULL, 1),
(20, 'DIN Registration', 'din-registration', 1, 1000, 'Once', 'Firm', NULL, 1),
(21, 'VAT Registration', 'vat-registration', 1, 2000, 'Once', 'Firm', NULL, 1),
(22, 'JPT Registration', 'jpt-registration', 1, 500, 'Once', 'Firm', NULL, 1),
(23, 'E invoice/ e way bill ', 'e-invoice-e-way-bill', 0, 200, 'Once', 'Firm', NULL, 1),
(24, 'Trading PL Balance Sheet ', 'trading-pl-balance-sheet', 0, 2000, 'Once', 'Firm', NULL, 1),
(25, 'Trading PL Bs ca certified ', 'trading-pl-bs-ca-certified', 0, 5000, 'Once', 'Firm', NULL, 1),
(26, 'Hosting & Legal ', 'hosting-legal', 0, 100, 'Once', 'Individual', NULL, 1),
(27, 'Income Tax Return  Audit ', 'income-tax-return-audit', 0, 4000, 'Yearly', 'Firm', '2025-05-31', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tf_service_packages`
--

CREATE TABLE `tf_service_packages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_ids` varchar(500) NOT NULL,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tf_tokens`
--

CREATE TABLE `tf_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(50) NOT NULL,
  `device_id` varchar(255) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `regid` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tf_users`
--

CREATE TABLE `tf_users` (
  `id` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `mobile` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(60) NOT NULL,
  `password` varchar(100) NOT NULL,
  `vp` varchar(50) NOT NULL,
  `role` varchar(30) NOT NULL,
  `salt` varchar(20) NOT NULL,
  `otp` varchar(100) NOT NULL,
  `token` varchar(50) NOT NULL,
  `photo` varchar(200) NOT NULL,
  `emp_id` int(11) DEFAULT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tf_users`
--

INSERT INTO `tf_users` (`id`, `username`, `mobile`, `name`, `email`, `password`, `vp`, `role`, `salt`, `otp`, `token`, `photo`, `emp_id`, `parent_id`, `status`, `created_on`, `updated_on`) VALUES
(1, 'admin', '1234567890', 'Admin', 'admin@gmail.com', '$2y$10$8vlLBC9RX7/WDRnV4NHsjuL4eb0mlXlWiTTTpXaiReojQmU7pOsDu', '12345', 'admin', 'GN2d04gDMm8LzWji', '', '', '', NULL, 0, 1, '2023-11-02 06:23:32', '2023-11-02 10:58:00');

-- --------------------------------------------------------

--
-- Table structure for table `tf_wallet`
--

CREATE TABLE `tf_wallet` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `amount` float NOT NULL,
  `merchant_transaction_id` varchar(40) NOT NULL,
  `merchant_user_id` varchar(40) NOT NULL,
  `payment_details` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tf_accountancy`
--
ALTER TABLE `tf_accountancy`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_acc_payment`
--
ALTER TABLE `tf_acc_payment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_area`
--
ALTER TABLE `tf_area`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_assessments`
--
ALTER TABLE `tf_assessments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_chats`
--
ALTER TABLE `tf_chats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_commission`
--
ALTER TABLE `tf_commission`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_commission_percent`
--
ALTER TABLE `tf_commission_percent`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_customers`
--
ALTER TABLE `tf_customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_customer_packages`
--
ALTER TABLE `tf_customer_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_docs_required`
--
ALTER TABLE `tf_docs_required`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `tf_documents`
--
ALTER TABLE `tf_documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `tf_employees`
--
ALTER TABLE `tf_employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_emp_percent`
--
ALTER TABLE `tf_emp_percent`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_firms`
--
ALTER TABLE `tf_firms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_formdata`
--
ALTER TABLE `tf_formdata`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_kyc`
--
ALTER TABLE `tf_kyc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_notify`
--
ALTER TABLE `tf_notify`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_order_assign`
--
ALTER TABLE `tf_order_assign`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_packages`
--
ALTER TABLE `tf_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_payment`
--
ALTER TABLE `tf_payment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_purchases`
--
ALTER TABLE `tf_purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_request_log`
--
ALTER TABLE `tf_request_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_roles`
--
ALTER TABLE `tf_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `tf_services`
--
ALTER TABLE `tf_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `tf_service_packages`
--
ALTER TABLE `tf_service_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_tokens`
--
ALTER TABLE `tf_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tf_users`
--
ALTER TABLE `tf_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `tf_wallet`
--
ALTER TABLE `tf_wallet`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `merchant_transaction_id` (`merchant_transaction_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tf_accountancy`
--
ALTER TABLE `tf_accountancy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tf_acc_payment`
--
ALTER TABLE `tf_acc_payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tf_area`
--
ALTER TABLE `tf_area`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=759;

--
-- AUTO_INCREMENT for table `tf_assessments`
--
ALTER TABLE `tf_assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tf_chats`
--
ALTER TABLE `tf_chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tf_commission`
--
ALTER TABLE `tf_commission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tf_commission_percent`
--
ALTER TABLE `tf_commission_percent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tf_customers`
--
ALTER TABLE `tf_customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tf_customer_packages`
--
ALTER TABLE `tf_customer_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tf_docs_required`
--
ALTER TABLE `tf_docs_required`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `tf_documents`
--
ALTER TABLE `tf_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tf_employees`
--
ALTER TABLE `tf_employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tf_emp_percent`
--
ALTER TABLE `tf_emp_percent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tf_firms`
--
ALTER TABLE `tf_firms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tf_formdata`
--
ALTER TABLE `tf_formdata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tf_kyc`
--
ALTER TABLE `tf_kyc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tf_notify`
--
ALTER TABLE `tf_notify`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tf_order_assign`
--
ALTER TABLE `tf_order_assign`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tf_packages`
--
ALTER TABLE `tf_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tf_payment`
--
ALTER TABLE `tf_payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tf_purchases`
--
ALTER TABLE `tf_purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tf_request_log`
--
ALTER TABLE `tf_request_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tf_roles`
--
ALTER TABLE `tf_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tf_services`
--
ALTER TABLE `tf_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `tf_service_packages`
--
ALTER TABLE `tf_service_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tf_tokens`
--
ALTER TABLE `tf_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tf_users`
--
ALTER TABLE `tf_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tf_wallet`
--
ALTER TABLE `tf_wallet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

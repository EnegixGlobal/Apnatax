-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: db_taxefi
-- ------------------------------------------------------
-- Server version	8.0.45-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tf_acc_payment`
--

DROP TABLE IF EXISTS `tf_acc_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_acc_payment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `user_id` int NOT NULL,
  `firm_id` int DEFAULT NULL,
  `year` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `added_by` int DEFAULT NULL,
  `acc_date` date NOT NULL,
  `amount` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `added_by` (`added_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_acc_payment`
--

LOCK TABLES `tf_acc_payment` WRITE;
/*!40000 ALTER TABLE `tf_acc_payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_acc_payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_accountancy`
--

DROP TABLE IF EXISTS `tf_accountancy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_accountancy` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `user_id` int NOT NULL,
  `package_id` int NOT NULL,
  `firm_id` int DEFAULT NULL,
  `turnover` float NOT NULL,
  `other_fee` float NOT NULL,
  `due_date` date NOT NULL,
  `added_by` int NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_accountancy`
--

LOCK TABLES `tf_accountancy` WRITE;
/*!40000 ALTER TABLE `tf_accountancy` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_accountancy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_addresses`
--

DROP TABLE IF EXISTS `tf_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_addresses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `address` text COLLATE utf8mb4_general_ci NOT NULL,
  `parent_id` int NOT NULL COMMENT 'State ID from area table',
  `area_id` int NOT NULL COMMENT 'District ID from area table',
  `pincode` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `state` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'State name (cached)',
  `district` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'District name (cached)',
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  KEY `area_id` (`area_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_addresses`
--

LOCK TABLES `tf_addresses` WRITE;
/*!40000 ALTER TABLE `tf_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_area`
--

DROP TABLE IF EXISTS `tf_area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_area` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  `type` varchar(200) NOT NULL,
  `parent_id` int NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=759 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_area`
--

LOCK TABLES `tf_area` WRITE;
/*!40000 ALTER TABLE `tf_area` DISABLE KEYS */;
INSERT INTO `tf_area` VALUES (1,'Andaman & Nicobar Islands','State',0,1),(2,'Andhra Pradesh','State',0,1),(3,'Arunachal Pradesh','State',0,1),(4,'Assam','State',0,1),(5,'Bihar','State',0,1),(6,'Chandigarh','State',0,1),(7,'Chhattisgarh','State',0,1),(8,'Dadra and Nagar Haveli','State',0,1),(9,'Daman and Diu','State',0,1),(10,'Delhi','State',0,1),(11,'Goa','State',0,1),(12,'Gujarat','State',0,1),(13,'Haryana','State',0,1),(14,'Himachal Pradesh','State',0,1),(15,'Jammu and Kashmir','State',0,1),(16,'Jharkhand','State',0,1),(17,'Karnataka','State',0,1),(18,'Kerala','State',0,1),(19,'Lakshadweep','State',0,1),(20,'Madhya Pradesh','State',0,1),(21,'Maharashtra','State',0,1),(22,'Manipur','State',0,1),(23,'Meghalaya','State',0,1),(24,'Mizoram','State',0,1),(25,'Nagaland','State',0,1),(26,'Odisha','State',0,1),(27,'Puducherry','State',0,1),(28,'Punjab','State',0,1),(29,'Rajasthan','State',0,1),(30,'Sikkim','State',0,1),(31,'Tamil Nadu','State',0,1),(32,'Telengana','State',0,1),(33,'Tripura','State',0,1),(34,'Uttrakhand','State',0,1),(35,'Uttar Pradesh','State',0,1),(36,'West Bengal','State',0,1),(37,'Nicobar','District',1,1),(38,'North and Middle Andaman','District',1,1),(39,'South Andaman','District',1,1),(40,'Anantapur','District',2,1),(41,'Chittoor','District',2,1),(42,'East Godavari','District',2,1),(43,'Guntur','District',2,1),(44,'Krishna','District',2,1),(45,'Kurnool','District',2,1),(46,'Prakasam','District',2,1),(47,'Srikakulam','District',2,1),(48,'Sri Potti Sriramulu Nellore','District',2,1),(49,'Visakhapatnam','District',2,1),(50,'Vizianagaram','District',2,1),(51,'West Godavari','District',2,1),(52,'YSR District, Kadapa (Cuddapah)','District',2,1),(53,'Anjaw','District',3,1),(54,'Changlang','District',3,1),(55,'Dibang Valley','District',3,1),(56,'East Kameng','District',3,1),(57,'East Siang','District',3,1),(58,'Kamle','District',3,1),(59,'Kra Daadi','District',3,1),(60,'Kurung Kumey','District',3,1),(61,'Lepa Rada','District',3,1),(62,'Lohit','District',3,1),(63,'Longding','District',3,1),(64,'Lower Dibang Valley','District',3,1),(65,'Lower Siang','District',3,1),(66,'Lower Subansiri','District',3,1),(67,'Namsai','District',3,1),(68,'Pakke Kessang','District',3,1),(69,'Papum Pare','District',3,1),(70,'Shi Yomi','District',3,1),(71,'Siang','District',3,1),(72,'Tawang','District',3,1),(73,'Tirap','District',3,1),(74,'Upper Siang','District',3,1),(75,'Upper Subansiri','District',3,1),(76,'West Kameng','District',3,1),(77,'West Siang','District',3,1),(78,'Baksa','District',4,1),(79,'Barpeta','District',4,1),(80,'Biswanath','District',4,1),(81,'Bongaigaon','District',4,1),(82,'Cachar','District',4,1),(83,'Charaideo','District',4,1),(84,'Chirang','District',4,1),(85,'Darrang','District',4,1),(86,'Dhemaji','District',4,1),(87,'Dhubri','District',4,1),(88,'Dibrugarh','District',4,1),(89,'Dima Hasao (North Cachar Hills)','District',4,1),(90,'Goalpara','District',4,1),(91,'Golaghat','District',4,1),(92,'Hailakandi','District',4,1),(93,'Hojai','District',4,1),(94,'Jorhat','District',4,1),(95,'Kamrup','District',4,1),(96,'Kamrup Metropolitan','District',4,1),(97,'Karbi Anglong','District',4,1),(98,'Karimganj','District',4,1),(99,'Kokrajhar','District',4,1),(100,'Lakhimpur','District',4,1),(101,'Majuli','District',4,1),(102,'Morigaon','District',4,1),(103,'Nagaon','District',4,1),(104,'Nalbari','District',4,1),(105,'Sivasagar','District',4,1),(106,'Sonitpur','District',4,1),(107,'South Salamara-Mankachar','District',4,1),(108,'Tinsukia','District',4,1),(109,'Udalguri','District',4,1),(110,'West Karbi Anglong','District',4,1),(111,'Araria','District',5,1),(112,'Arwal','District',5,1),(113,'Aurangabad','District',5,1),(114,'Banka','District',5,1),(115,'Begusarai','District',5,1),(116,'Bhagalpur','District',5,1),(117,'Bhojpur','District',5,1),(118,'Buxar','District',5,1),(119,'Darbhanga','District',5,1),(120,'East Champaran (Motihari)','District',5,1),(121,'Gaya','District',5,1),(122,'Gopalganj','District',5,1),(123,'Jamui','District',5,1),(124,'Jehanabad','District',5,1),(125,'Kaimur (Bhabua)','District',5,1),(126,'Katihar','District',5,1),(127,'Khagaria','District',5,1),(128,'Kishanganj','District',5,1),(129,'Lakhisarai','District',5,1),(130,'Madhepura','District',5,1),(131,'Madhubani','District',5,1),(132,'Munger (Monghyr)','District',5,1),(133,'Muzaffarpur','District',5,1),(134,'Nalanda','District',5,1),(135,'Nawada','District',5,1),(136,'Patna','District',5,1),(137,'Purnia (Purnea)','District',5,1),(138,'Rohtas','District',5,1),(139,'Saharsa','District',5,1),(140,'Samastipur','District',5,1),(141,'Saran','District',5,1),(142,'Sheikhpura','District',5,1),(143,'Sheohar','District',5,1),(144,'Sitamarhi','District',5,1),(145,'Siwan','District',5,1),(146,'Supaul','District',5,1),(147,'Vaishali','District',5,1),(148,'West Champaran','District',5,1),(149,'Chandigarh','District',6,1),(150,'Balod','District',7,1),(151,'Baloda Bazar','District',7,1),(152,'Balrampur','District',7,1),(153,'Bastar','District',7,1),(154,'Bemetara','District',7,1),(155,'Bijapur','District',7,1),(156,'Bilaspur','District',7,1),(157,'Dantewada (South Bastar)','District',7,1),(158,'Dhamtari','District',7,1),(159,'Durg','District',7,1),(160,'Gariyaband','District',7,1),(161,'Janjgir-Champa','District',7,1),(162,'Jashpur','District',7,1),(163,'Kabirdham (Kawardha)','District',7,1),(164,'Kanker (North Bastar)','District',7,1),(165,'Kondagaon','District',7,1),(166,'Korba','District',7,1),(167,'Korea (Koriya)','District',7,1),(168,'Mahasamund','District',7,1),(169,'Mungeli','District',7,1),(170,'Narayanpur','District',7,1),(171,'Raigarh','District',7,1),(172,'Raipur','District',7,1),(173,'Rajnandgaon','District',7,1),(174,'Sukma','District',7,1),(175,'Surajpur','District',7,1),(176,'Surguja','District',7,1),(177,'Dadra &amp; Nagar Haveli','District',8,1),(178,'Daman','District',9,1),(179,'Diu','District',9,1),(180,'Central Delhi','District',10,1),(181,'East Delhi','District',10,1),(182,'New Delhi','District',10,1),(183,'North Delhi','District',10,1),(184,'North East  Delhi','District',10,1),(185,'North West  Delhi','District',10,1),(186,'Shahdara','District',10,1),(187,'South Delhi','District',10,1),(188,'South East Delhi','District',10,1),(189,'South West  Delhi','District',10,1),(190,'West Delhi','District',10,1),(191,'North Goa','District',11,1),(192,'South Goa','District',11,1),(193,'Ahmedabad','District',12,1),(194,'Amreli','District',12,1),(195,'Anand','District',12,1),(196,'Aravalli','District',12,1),(197,'Banaskantha (Palanpur)','District',12,1),(198,'Bharuch','District',12,1),(199,'Bhavnagar','District',12,1),(200,'Botad','District',12,1),(201,'Chhota Udepur','District',12,1),(202,'Dahod','District',12,1),(203,'Dangs (Ahwa)','District',12,1),(204,'Devbhoomi Dwarka','District',12,1),(205,'Gandhinagar','District',12,1),(206,'Gir Somnath','District',12,1),(207,'Jamnagar','District',12,1),(208,'Junagadh','District',12,1),(209,'Kachchh','District',12,1),(210,'Kheda (Nadiad)','District',12,1),(211,'Mahisagar','District',12,1),(212,'Mehsana','District',12,1),(213,'Morbi','District',12,1),(214,'Narmada (Rajpipla)','District',12,1),(215,'Navsari','District',12,1),(216,'Panchmahal (Godhra)','District',12,1),(217,'Patan','District',12,1),(218,'Porbandar','District',12,1),(219,'Rajkot','District',12,1),(220,'Sabarkantha (Himmatnagar)','District',12,1),(221,'Surat','District',12,1),(222,'Surendranagar','District',12,1),(223,'Tapi (Vyara)','District',12,1),(224,'Vadodara','District',12,1),(225,'Valsad','District',12,1),(226,'Ambala','District',13,1),(227,'Bhiwani','District',13,1),(228,'Charkhi Dadri','District',13,1),(229,'Faridabad','District',13,1),(230,'Fatehabad','District',13,1),(231,'Gurgaon','District',13,1),(232,'Hisar','District',13,1),(233,'Jhajjar','District',13,1),(234,'Jind','District',13,1),(235,'Kaithal','District',13,1),(236,'Karnal','District',13,1),(237,'Kurukshetra','District',13,1),(238,'Mahendragarh','District',13,1),(239,'Mewat','District',13,1),(240,'Palwal','District',13,1),(241,'Panchkula','District',13,1),(242,'Panipat','District',13,1),(243,'Rewari','District',13,1),(244,'Rohtak','District',13,1),(245,'Sirsa','District',13,1),(246,'Sonipat','District',13,1),(247,'Yamunanagar','District',13,1),(248,'Bilaspur','District',14,1),(249,'Chamba','District',14,1),(250,'Hamirpur','District',14,1),(251,'Kangra','District',14,1),(252,'Kinnaur','District',14,1),(253,'Kullu','District',14,1),(254,'Lahaul &amp; Spiti','District',14,1),(255,'Mandi','District',14,1),(256,'Shimla','District',14,1),(257,'Sirmaur (Sirmour)','District',14,1),(258,'Solan','District',14,1),(259,'Una','District',14,1),(260,'Anantnag','District',15,1),(261,'Bandipore','District',15,1),(262,'Baramulla','District',15,1),(263,'Budgam','District',15,1),(264,'Doda','District',15,1),(265,'Ganderbal','District',15,1),(266,'Jammu','District',15,1),(267,'Kargil','District',15,1),(268,'Kathua','District',15,1),(269,'Kishtwar','District',15,1),(270,'Kulgam','District',15,1),(271,'Kupwara','District',15,1),(272,'Leh','District',15,1),(273,'Poonch','District',15,1),(274,'Pulwama','District',15,1),(275,'Rajouri','District',15,1),(276,'Ramban','District',15,1),(277,'Reasi','District',15,1),(278,'Samba','District',15,1),(279,'Shopian','District',15,1),(280,'Srinagar','District',15,1),(281,'Udhampur','District',15,1),(282,'Bokaro','District',16,1),(283,'Chatra','District',16,1),(284,'Deoghar','District',16,1),(285,'Dhanbad','District',16,1),(286,'Dumka','District',16,1),(287,'East Singhbhum','District',16,1),(288,'Garhwa','District',16,1),(289,'Giridih','District',16,1),(290,'Godda','District',16,1),(291,'Gumla','District',16,1),(292,'Hazaribag','District',16,1),(293,'Jamtara','District',16,1),(294,'Khunti','District',16,1),(295,'Koderma','District',16,1),(296,'Latehar','District',16,1),(297,'Lohardaga','District',16,1),(298,'Pakur','District',16,1),(299,'Palamu','District',16,1),(300,'Ramgarh','District',16,1),(301,'Ranchi','District',16,1),(302,'Sahibganj','District',16,1),(303,'Seraikela-Kharsawan','District',16,1),(304,'Simdega','District',16,1),(305,'West Singhbhum','District',16,1),(306,'Bagalkot','District',17,1),(307,'Ballari (Bellary)','District',17,1),(308,'Belagavi (Belgaum)','District',17,1),(309,'Bengaluru (Bangalore) Rural','District',17,1),(310,'Bengaluru (Bangalore) Urban','District',17,1),(311,'Bidar','District',17,1),(312,'Chamarajanagar','District',17,1),(313,'Chikballapur','District',17,1),(314,'Chikkamagaluru (Chikmagalur)','District',17,1),(315,'Chitradurga','District',17,1),(316,'Dakshina Kannada','District',17,1),(317,'Davangere','District',17,1),(318,'Dharwad','District',17,1),(319,'Gadag','District',17,1),(320,'Hassan','District',17,1),(321,'Haveri','District',17,1),(322,'Kalaburagi (Gulbarga)','District',17,1),(323,'Kodagu','District',17,1),(324,'Kolar','District',17,1),(325,'Koppal','District',17,1),(326,'Mandya','District',17,1),(327,'Mysuru (Mysore)','District',17,1),(328,'Raichur','District',17,1),(329,'Ramanagara','District',17,1),(330,'Shivamogga (Shimoga)','District',17,1),(331,'Tumakuru (Tumkur)','District',17,1),(332,'Udupi','District',17,1),(333,'Uttara Kannada (Karwar)','District',17,1),(334,'Vijayapura (Bijapur)','District',17,1),(335,'Yadgir','District',17,1),(336,'Alappuzha','District',18,1),(337,'Ernakulam','District',18,1),(338,'Idukki','District',18,1),(339,'Kannur','District',18,1),(340,'Kasaragod','District',18,1),(341,'Kollam','District',18,1),(342,'Kottayam','District',18,1),(343,'Kozhikode','District',18,1),(344,'Malappuram','District',18,1),(345,'Palakkad','District',18,1),(346,'Pathanamthitta','District',18,1),(347,'Thiruvananthapuram','District',18,1),(348,'Thrissur','District',18,1),(349,'Wayanad','District',18,1),(350,'Lakshadweep','District',19,1),(351,'Agar Malwa','District',20,1),(352,'Alirajpur','District',20,1),(353,'Anuppur','District',20,1),(354,'Ashoknagar','District',20,1),(355,'Balaghat','District',20,1),(356,'Barwani','District',20,1),(357,'Betul','District',20,1),(358,'Bhind','District',20,1),(359,'Bhopal','District',20,1),(360,'Burhanpur','District',20,1),(361,'Chhatarpur','District',20,1),(362,'Chhindwara','District',20,1),(363,'Damoh','District',20,1),(364,'Datia','District',20,1),(365,'Dewas','District',20,1),(366,'Dhar','District',20,1),(367,'Dindori','District',20,1),(368,'Guna','District',20,1),(369,'Gwalior','District',20,1),(370,'Harda','District',20,1),(371,'Hoshangabad','District',20,1),(372,'Indore','District',20,1),(373,'Jabalpur','District',20,1),(374,'Jhabua','District',20,1),(375,'Katni','District',20,1),(376,'Khandwa','District',20,1),(377,'Khargone','District',20,1),(378,'Mandla','District',20,1),(379,'Mandsaur','District',20,1),(380,'Morena','District',20,1),(381,'Narsinghpur','District',20,1),(382,'Neemuch','District',20,1),(383,'Panna','District',20,1),(384,'Raisen','District',20,1),(385,'Rajgarh','District',20,1),(386,'Ratlam','District',20,1),(387,'Rewa','District',20,1),(388,'Sagar','District',20,1),(389,'Satna','District',20,1),(390,'Sehore','District',20,1),(391,'Seoni','District',20,1),(392,'Shahdol','District',20,1),(393,'Shajapur','District',20,1),(394,'Sheopur','District',20,1),(395,'Shivpuri','District',20,1),(396,'Sidhi','District',20,1),(397,'Singrauli','District',20,1),(398,'Tikamgarh','District',20,1),(399,'Ujjain','District',20,1),(400,'Umaria','District',20,1),(401,'Vidisha','District',20,1),(402,'Ahmednagar','District',21,1),(403,'Akola','District',21,1),(404,'Amravati','District',21,1),(405,'Aurangabad','District',21,1),(406,'Beed','District',21,1),(407,'Bhandara','District',21,1),(408,'Buldhana','District',21,1),(409,'Chandrapur','District',21,1),(410,'Dhule','District',21,1),(411,'Gadchiroli','District',21,1),(412,'Gondia','District',21,1),(413,'Hingoli','District',21,1),(414,'Jalgaon','District',21,1),(415,'Jalna','District',21,1),(416,'Kolhapur','District',21,1),(417,'Latur','District',21,1),(418,'Mumbai City','District',21,1),(419,'Mumbai Suburban','District',21,1),(420,'Nagpur','District',21,1),(421,'Nanded','District',21,1),(422,'Nandurbar','District',21,1),(423,'Nashik','District',21,1),(424,'Osmanabad','District',21,1),(425,'Palghar','District',21,1),(426,'Parbhani','District',21,1),(427,'Pune','District',21,1),(428,'Raigad','District',21,1),(429,'Ratnagiri','District',21,1),(430,'Sangli','District',21,1),(431,'Satara','District',21,1),(432,'Sindhudurg','District',21,1),(433,'Solapur','District',21,1),(434,'Thane','District',21,1),(435,'Wardha','District',21,1),(436,'Washim','District',21,1),(437,'Yavatmal','District',21,1),(438,'Bishnupur','District',22,1),(439,'Chandel','District',22,1),(440,'Churachandpur','District',22,1),(441,'Imphal East','District',22,1),(442,'Imphal West','District',22,1),(443,'Jiribam','District',22,1),(444,'Kakching','District',22,1),(445,'Kamjong','District',22,1),(446,'Kangpokpi','District',22,1),(447,'Noney','District',22,1),(448,'Pherzawl','District',22,1),(449,'Senapati','District',22,1),(450,'Tamenglong','District',22,1),(451,'Tengnoupal','District',22,1),(452,'Thoubal','District',22,1),(453,'Ukhrul','District',22,1),(454,'East Garo Hills','District',23,1),(455,'East Jaintia Hills','District',23,1),(456,'East Khasi Hills','District',23,1),(457,'North Garo Hills','District',23,1),(458,'Ri Bhoi','District',23,1),(459,'South Garo Hills','District',23,1),(460,'South West Garo Hills','District',23,1),(461,'South West Khasi Hills','District',23,1),(462,'West Garo Hills','District',23,1),(463,'West Jaintia Hills','District',23,1),(464,'West Khasi Hills','District',23,1),(465,'Aizawl','District',24,1),(466,'Champhai','District',24,1),(467,'Kolasib','District',24,1),(468,'Lawngtlai','District',24,1),(469,'Lunglei','District',24,1),(470,'Mamit','District',24,1),(471,'Saiha','District',24,1),(472,'Serchhip','District',24,1),(473,'Dimapur','District',25,1),(474,'Kiphire','District',25,1),(475,'Kohima','District',25,1),(476,'Longleng','District',25,1),(477,'Mokokchung','District',25,1),(478,'Mon','District',25,1),(479,'Peren','District',25,1),(480,'Phek','District',25,1),(481,'Tuensang','District',25,1),(482,'Wokha','District',25,1),(483,'Zunheboto','District',25,1),(484,'Angul','District',26,1),(485,'Balangir','District',26,1),(486,'Balasore','District',26,1),(487,'Bargarh','District',26,1),(488,'Bhadrak','District',26,1),(489,'Boudh','District',26,1),(490,'Cuttack','District',26,1),(491,'Deogarh','District',26,1),(492,'Dhenkanal','District',26,1),(493,'Gajapati','District',26,1),(494,'Ganjam','District',26,1),(495,'Jagatsinghapur','District',26,1),(496,'Jajpur','District',26,1),(497,'Jharsuguda','District',26,1),(498,'Kalahandi','District',26,1),(499,'Kandhamal','District',26,1),(500,'Kendrapara','District',26,1),(501,'Kendujhar (Keonjhar)','District',26,1),(502,'Khordha','District',26,1),(503,'Koraput','District',26,1),(504,'Malkangiri','District',26,1),(505,'Mayurbhanj','District',26,1),(506,'Nabarangpur','District',26,1),(507,'Nayagarh','District',26,1),(508,'Nuapada','District',26,1),(509,'Puri','District',26,1),(510,'Rayagada','District',26,1),(511,'Sambalpur','District',26,1),(512,'Sonepur','District',26,1),(513,'Sundargarh','District',26,1),(514,'Karaikal','District',27,1),(515,'Mahe','District',27,1),(516,'Pondicherry','District',27,1),(517,'Yanam','District',27,1),(518,'Amritsar','District',28,1),(519,'Barnala','District',28,1),(520,'Bathinda','District',28,1),(521,'Faridkot','District',28,1),(522,'Fatehgarh Sahib','District',28,1),(523,'Fazilka','District',28,1),(524,'Ferozepur','District',28,1),(525,'Gurdaspur','District',28,1),(526,'Hoshiarpur','District',28,1),(527,'Jalandhar','District',28,1),(528,'Kapurthala','District',28,1),(529,'Ludhiana','District',28,1),(530,'Mansa','District',28,1),(531,'Moga','District',28,1),(532,'Muktsar','District',28,1),(533,'Nawanshahr (Shahid Bhagat Singh Nagar)','District',28,1),(534,'Pathankot','District',28,1),(535,'Patiala','District',28,1),(536,'Rupnagar','District',28,1),(537,'Sahibzada Ajit Singh Nagar (Mohali)','District',28,1),(538,'Sangrur','District',28,1),(539,'Tarn Taran','District',28,1),(540,'Ajmer','District',29,1),(541,'Alwar','District',29,1),(542,'Banswara','District',29,1),(543,'Baran','District',29,1),(544,'Barmer','District',29,1),(545,'Bharatpur','District',29,1),(546,'Bhilwara','District',29,1),(547,'Bikaner','District',29,1),(548,'Bundi','District',29,1),(549,'Chittorgarh','District',29,1),(550,'Churu','District',29,1),(551,'Dausa','District',29,1),(552,'Dholpur','District',29,1),(553,'Dungarpur','District',29,1),(554,'Hanumangarh','District',29,1),(555,'Jaipur','District',29,1),(556,'Jaisalmer','District',29,1),(557,'Jalore','District',29,1),(558,'Jhalawar','District',29,1),(559,'Jhunjhunu','District',29,1),(560,'Jodhpur','District',29,1),(561,'Karauli','District',29,1),(562,'Kota','District',29,1),(563,'Nagaur','District',29,1),(564,'Pali','District',29,1),(565,'Pratapgarh','District',29,1),(566,'Rajsamand','District',29,1),(567,'Sawai Madhopur','District',29,1),(568,'Sikar','District',29,1),(569,'Sirohi','District',29,1),(570,'Sri Ganganagar','District',29,1),(571,'Tonk','District',29,1),(572,'Udaipur','District',29,1),(573,'East Sikkim','District',30,1),(574,'North Sikkim','District',30,1),(575,'South Sikkim','District',30,1),(576,'West Sikkim','District',30,1),(577,'Ariyalur','District',31,1),(578,'Chennai','District',31,1),(579,'Coimbatore','District',31,1),(580,'Cuddalore','District',31,1),(581,'Dharmapuri','District',31,1),(582,'Dindigul','District',31,1),(583,'Erode','District',31,1),(584,'Kanchipuram','District',31,1),(585,'Kanyakumari','District',31,1),(586,'Karur','District',31,1),(587,'Krishnagiri','District',31,1),(588,'Madurai','District',31,1),(589,'Nagapattinam','District',31,1),(590,'Namakkal','District',31,1),(591,'Nilgiris','District',31,1),(592,'Perambalur','District',31,1),(593,'Pudukkottai','District',31,1),(594,'Ramanathapuram','District',31,1),(595,'Salem','District',31,1),(596,'Sivaganga','District',31,1),(597,'Thanjavur','District',31,1),(598,'Theni','District',31,1),(599,'Thoothukudi (Tuticorin)','District',31,1),(600,'Tiruchirappalli','District',31,1),(601,'Tirunelveli','District',31,1),(602,'Tiruppur','District',31,1),(603,'Tiruvallur','District',31,1),(604,'Tiruvannamalai','District',31,1),(605,'Tiruvarur','District',31,1),(606,'Vellore','District',31,1),(607,'Viluppuram','District',31,1),(608,'Virudhunagar','District',31,1),(609,'Adilabad','District',32,1),(610,'Bhadradri Kothagudem','District',32,1),(611,'Hyderabad','District',32,1),(612,'Jagtial','District',32,1),(613,'Jangaon','District',32,1),(614,'Jayashankar Bhoopalpally','District',32,1),(615,'Jogulamba Gadwal','District',32,1),(616,'Kamareddy','District',32,1),(617,'Karimnagar','District',32,1),(618,'Khammam','District',32,1),(619,'Komaram Bheem Asifabad','District',32,1),(620,'Mahabubabad','District',32,1),(621,'Mahabubnagar','District',32,1),(622,'Mancherial','District',32,1),(623,'Medak','District',32,1),(624,'Medchal','District',32,1),(625,'Nagarkurnool','District',32,1),(626,'Nalgonda','District',32,1),(627,'Nirmal','District',32,1),(628,'Nizamabad','District',32,1),(629,'Peddapalli','District',32,1),(630,'Rajanna Sircilla','District',32,1),(631,'Rangareddy','District',32,1),(632,'Sangareddy','District',32,1),(633,'Siddipet','District',32,1),(634,'Suryapet','District',32,1),(635,'Vikarabad','District',32,1),(636,'Wanaparthy','District',32,1),(637,'Warangal (Rural)','District',32,1),(638,'Warangal (Urban)','District',32,1),(639,'Yadadri Bhuvanagiri','District',32,1),(640,'Dhalai','District',33,1),(641,'Gomati','District',33,1),(642,'Khowai','District',33,1),(643,'North Tripura','District',33,1),(644,'Sepahijala','District',33,1),(645,'South Tripura','District',33,1),(646,'Unakoti','District',33,1),(647,'West Tripura','District',33,1),(648,'Almora','District',34,1),(649,'Bageshwar','District',34,1),(650,'Chamoli','District',34,1),(651,'Champawat','District',34,1),(652,'Dehradun','District',34,1),(653,'Haridwar','District',34,1),(654,'Nainital','District',34,1),(655,'Pauri Garhwal','District',34,1),(656,'Pithoragarh','District',34,1),(657,'Rudraprayag','District',34,1),(658,'Tehri Garhwal','District',34,1),(659,'Udham Singh Nagar','District',34,1),(660,'Uttarkashi','District',34,1),(661,'Agra','District',35,1),(662,'Aligarh','District',35,1),(663,'Allahabad','District',35,1),(664,'Ambedkar Nagar','District',35,1),(665,'Amethi (Chatrapati Sahuji Mahraj Nagar)','District',35,1),(666,'Amroha (J.P. Nagar)','District',35,1),(667,'Auraiya','District',35,1),(668,'Azamgarh','District',35,1),(669,'Baghpat','District',35,1),(670,'Bahraich','District',35,1),(671,'Ballia','District',35,1),(672,'Balrampur','District',35,1),(673,'Banda','District',35,1),(674,'Barabanki','District',35,1),(675,'Bareilly','District',35,1),(676,'Basti','District',35,1),(677,'Bhadohi','District',35,1),(678,'Bijnor','District',35,1),(679,'Budaun','District',35,1),(680,'Bulandshahr','District',35,1),(681,'Chandauli','District',35,1),(682,'Chitrakoot','District',35,1),(683,'Deoria','District',35,1),(684,'Etah','District',35,1),(685,'Etawah','District',35,1),(686,'Faizabad','District',35,1),(687,'Farrukhabad','District',35,1),(688,'Fatehpur','District',35,1),(689,'Firozabad','District',35,1),(690,'Gautam Buddha Nagar','District',35,1),(691,'Ghaziabad','District',35,1),(692,'Ghazipur','District',35,1),(693,'Gonda','District',35,1),(694,'Gorakhpur','District',35,1),(695,'Hamirpur','District',35,1),(696,'Hapur (Panchsheel Nagar)','District',35,1),(697,'Hardoi','District',35,1),(698,'Hathras','District',35,1),(699,'Jalaun','District',35,1),(700,'Jaunpur','District',35,1),(701,'Jhansi','District',35,1),(702,'Kannauj','District',35,1),(703,'Kanpur Dehat','District',35,1),(704,'Kanpur Nagar','District',35,1),(705,'Kanshiram Nagar (Kasganj)','District',35,1),(706,'Kaushambi','District',35,1),(707,'Kushinagar (Padrauna)','District',35,1),(708,'Lakhimpur - Kheri','District',35,1),(709,'Lalitpur','District',35,1),(710,'Lucknow','District',35,1),(711,'Maharajganj','District',35,1),(712,'Mahoba','District',35,1),(713,'Mainpuri','District',35,1),(714,'Mathura','District',35,1),(715,'Mau','District',35,1),(716,'Meerut','District',35,1),(717,'Mirzapur','District',35,1),(718,'Moradabad','District',35,1),(719,'Muzaffarnagar','District',35,1),(720,'Pilibhit','District',35,1),(721,'Pratapgarh','District',35,1),(722,'RaeBareli','District',35,1),(723,'Rampur','District',35,1),(724,'Saharanpur','District',35,1),(725,'Sambhal (Bhim Nagar)','District',35,1),(726,'Sant Kabir Nagar','District',35,1),(727,'Shahjahanpur','District',35,1),(728,'Shamali (Prabuddh Nagar)','District',35,1),(729,'Shravasti','District',35,1),(730,'Siddharth Nagar','District',35,1),(731,'Sitapur','District',35,1),(732,'Sonbhadra','District',35,1),(733,'Sultanpur','District',35,1),(734,'Unnao','District',35,1),(735,'Varanasi','District',35,1),(736,'Alipurduar','District',36,1),(737,'Bankura','District',36,1),(738,'Birbhum','District',36,1),(739,'Cooch Behar','District',36,1),(740,'Dakshin Dinajpur (South Dinajpur)','District',36,1),(741,'Darjeeling','District',36,1),(742,'Hooghly','District',36,1),(743,'Howrah','District',36,1),(744,'Jalpaiguri','District',36,1),(745,'Jhargram','District',36,1),(746,'Kalimpong','District',36,1),(747,'Kolkata','District',36,1),(748,'Malda','District',36,1),(749,'Murshidabad','District',36,1),(750,'Nadia','District',36,1),(751,'North 24 Parganas','District',36,1),(752,'Paschim Medinipur (West Medinipur)','District',36,1),(753,'Paschim (West) Burdwan (Bardhaman)','District',36,1),(754,'Purba Burdwan (Bardhaman)','District',36,1),(755,'Purba Medinipur (East Medinipur)','District',36,1),(756,'Purulia','District',36,1),(757,'South 24 Parganas','District',36,1),(758,'Uttar Dinajpur (North Dinajpur)','District',36,1);
/*!40000 ALTER TABLE `tf_area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_assessments`
--

DROP TABLE IF EXISTS `tf_assessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_assessments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `order_id` int NOT NULL,
  `firm_id` int DEFAULT NULL,
  `file` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `remarks` text COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int NOT NULL,
  `status` tinyint(1) NOT NULL,
  `assignment_done` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=Pending, 1=Done',
  `assignment_done_date` datetime DEFAULT NULL COMMENT 'Date when assignment was marked as done',
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_assignment_done` (`assignment_done`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_assessments`
--

LOCK TABLES `tf_assessments` WRITE;
/*!40000 ALTER TABLE `tf_assessments` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_assessments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_bank_creditors_statements`
--

DROP TABLE IF EXISTS `tf_bank_creditors_statements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_bank_creditors_statements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bank_statement_id` int NOT NULL,
  `user_id` int NOT NULL,
  `firm_id` int NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `uploaded_by` int NOT NULL,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bank_statement_id` (`bank_statement_id`),
  KEY `user_id` (`user_id`),
  KEY `firm_id` (`firm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_bank_creditors_statements`
--

LOCK TABLES `tf_bank_creditors_statements` WRITE;
/*!40000 ALTER TABLE `tf_bank_creditors_statements` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_bank_creditors_statements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_bank_statements`
--

DROP TABLE IF EXISTS `tf_bank_statements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_bank_statements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `firm_id` int NOT NULL,
  `year` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `month` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `statement` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `creditors_statement` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `uploaded_by` int NOT NULL,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_bank_statements`
--

LOCK TABLES `tf_bank_statements` WRITE;
/*!40000 ALTER TABLE `tf_bank_statements` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_bank_statements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_banks`
--

DROP TABLE IF EXISTS `tf_banks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_banks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_banks`
--

LOCK TABLES `tf_banks` WRITE;
/*!40000 ALTER TABLE `tf_banks` DISABLE KEYS */;
INSERT INTO `tf_banks` VALUES (47,'airtel payment bank'),(1,'Allahabad Bank'),(2,'Andhra Bank'),(49,'AU SMALL FINANCE BANK'),(3,'Axis Bank'),(4,'Bandhan Bank'),(5,'Bank of Baroda'),(6,'Bank of India'),(7,'Bank of Maharashtra'),(8,'Canara Bank'),(9,'Catholic Syrian Bank'),(10,'Central Bank of India'),(43,'Chhattisgarh Rajya Gramin Bank'),(11,'City Union Bank'),(12,'Corporation Bank'),(13,'DCB Bank'),(14,'Dena Bank'),(15,'Dhanlaxmi Bank'),(16,'Federal Bank'),(46,'Fino payment Bank'),(17,'HDFC Bank'),(18,'ICICI Bank'),(19,'IDBI Bank'),(20,'IDFC Bank'),(45,'india post payment bank'),(21,'Indian Bank'),(22,'Indian Overseas Bank'),(23,'IndusInd Bank'),(24,'Jammu and Kashmir Bank'),(25,'Karnataka Bank'),(26,'Karur Vysya Bank'),(44,'Kerala Gramin Bank'),(27,'Kotak Mahindra Bank'),(28,'Lakshmi Vilas Bank'),(29,'Nainital Bank'),(30,'Oriental Bank of Commerce'),(31,'Punjab & Sindh Bank'),(32,'Punjab National Bank'),(33,'RBL Bank'),(34,'South Indian Bank'),(35,'State Bank of India'),(36,'Syndicate Bank'),(37,'Tamilnad Mercantile Bank'),(38,'UCO Bank'),(48,'Ujjivan Small finance Bank'),(39,'Union Bank of India'),(40,'United Bank of India'),(41,'Vijaya Bank'),(42,'YES Bank');
/*!40000 ALTER TABLE `tf_banks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_chats`
--

DROP TABLE IF EXISTS `tf_chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_chats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `query_id` int DEFAULT NULL,
  `query_subject` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `query_status` enum('open','in_progress','resolved','closed') COLLATE utf8mb4_general_ci DEFAULT 'open',
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `order_id` int DEFAULT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL DEFAULT '0',
  `added_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_query_id` (`query_id`),
  KEY `idx_query_status` (`query_status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_chats`
--

LOCK TABLES `tf_chats` WRITE;
/*!40000 ALTER TABLE `tf_chats` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_chats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_commission`
--

DROP TABLE IF EXISTS `tf_commission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_commission` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `emp_id` int NOT NULL,
  `order_id` int NOT NULL,
  `order_amount` float NOT NULL,
  `percent` float NOT NULL,
  `amount` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_commission`
--

LOCK TABLES `tf_commission` WRITE;
/*!40000 ALTER TABLE `tf_commission` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_commission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_commission_percent`
--

DROP TABLE IF EXISTS `tf_commission_percent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_commission_percent` (
  `id` int NOT NULL AUTO_INCREMENT,
  `percent` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_commission_percent`
--

LOCK TABLES `tf_commission_percent` WRITE;
/*!40000 ALTER TABLE `tf_commission_percent` DISABLE KEYS */;
INSERT INTO `tf_commission_percent` VALUES (1,20,1,'2025-10-25 06:46:09','2025-10-25 06:46:09');
/*!40000 ALTER TABLE `tf_commission_percent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_customer_packages`
--

DROP TABLE IF EXISTS `tf_customer_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_customer_packages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `firm_id` int NOT NULL,
  `year` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `payment_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=unpaid, 1=paid',
  `purchase_date` date DEFAULT NULL,
  `bill_amount` decimal(10,2) DEFAULT NULL COMMENT 'Total bill amount including GST',
  `package_type` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Turnover, Monthly, Yearly, etc.',
  `package_id` int NOT NULL,
  `amount` float NOT NULL,
  `autodebit` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `request` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=No request, 1=Delete requested, 2=Rejected',
  PRIMARY KEY (`id`),
  KEY `idx_request` (`request`),
  KEY `idx_expiry_payment` (`expiry_date`,`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_customer_packages`
--

LOCK TABLES `tf_customer_packages` WRITE;
/*!40000 ALTER TABLE `tf_customer_packages` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_customer_packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_customers`
--

DROP TABLE IF EXISTS `tf_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `mobile` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `address` text COLLATE utf8mb4_general_ci NOT NULL,
  `state` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `parent_id` int NOT NULL,
  `district` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `area_id` int DEFAULT NULL,
  `pincode` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int NOT NULL,
  `added_by` int DEFAULT NULL,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `gst_enabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=GST Enabled, 0=GST Disabled',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_customers`
--

LOCK TABLES `tf_customers` WRITE;
/*!40000 ALTER TABLE `tf_customers` DISABLE KEYS */;
INSERT INTO `tf_customers` VALUES (13,'Trial Demo','9876543210','trial@gmail.com','','',0,NULL,NULL,'',15,NULL,'2026-02-23 15:55:29','2026-03-01 07:22:06',1),(18,'Parmjeet Kaur','9431533243','mptraders33243@gmail.com','suriya','Jharkhand',16,'Giridih',289,'825320',21,1,'2026-02-27 17:52:20','2026-03-01 07:22:06',1);
/*!40000 ALTER TABLE `tf_customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_db_operations`
--

DROP TABLE IF EXISTS `tf_db_operations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_db_operations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `operation` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `table_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `primary_key` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `data` text COLLATE utf8mb4_general_ci NOT NULL,
  `ref` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `added_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_db_operations`
--

LOCK TABLES `tf_db_operations` WRITE;
/*!40000 ALTER TABLE `tf_db_operations` DISABLE KEYS */;
INSERT INTO `tf_db_operations` VALUES (1,'update','firms','3','{\"old\":{\"status\":\"1\"},\"new\":{\"status\":0}}','{\"class\":\"customers\",\"method\":\"updatefirmstatus\"}',1,NULL,'2026-02-09 12:22:19'),(2,'update','firms','7','{\"old\":{\"request\":\"1\"},\"new\":{\"request\":2}}','{\"class\":\"customers\",\"method\":\"updatefirmstatus\"}',1,NULL,'2026-02-23 16:17:00'),(3,'update','service_packages','4','{\"old\":{\"request\":\"1\"},\"new\":{\"request\":2}}','{\"class\":\"customers\",\"method\":\"updatepackagestatus\"}',1,NULL,'2026-02-25 10:00:12'),(4,'update','service_packages','5','{\"old\":{\"request\":\"1\"},\"new\":{\"request\":2}}','{\"class\":\"customers\",\"method\":\"updatepackagestatus\"}',1,NULL,'2026-02-25 11:19:44'),(5,'update','service_packages','7','{\"old\":{\"request\":\"1\"},\"new\":{\"request\":2}}','{\"class\":\"customers\",\"method\":\"updatepackagestatus\"}',1,NULL,'2026-02-27 11:36:29'),(6,'update','service_packages','6','{\"old\":{\"request\":\"1\"},\"new\":{\"request\":2}}','{\"class\":\"customers\",\"method\":\"updatepackagestatus\"}',1,NULL,'2026-02-27 11:36:32'),(7,'update','service_packages','1','{\"old\":{\"request\":\"1\"},\"new\":{\"request\":2}}','{\"class\":\"customers\",\"method\":\"updatepackagestatus\"}',1,NULL,'2026-02-27 11:36:40'),(8,'update','accountancy','1','{\"old\":{\"added_by\":\"3\",\"updated_on\":\"2026-03-02 16:13:06\"},\"new\":{\"added_by\":\"1\",\"updated_on\":\"2026-03-07 10:56:22\"}}','{\"class\":\"orders\",\"method\":\"saveturnover\"}',1,NULL,'2026-03-07 10:56:23'),(9,'update','firms','4','{\"old\":{\"status\":\"1\"},\"new\":{\"status\":0}}','{\"class\":\"customers\",\"method\":\"updatefirmstatus\"}',1,NULL,'2026-03-08 08:58:36'),(10,'update','firms','8','{\"old\":{\"status\":\"1\"},\"new\":{\"status\":0}}','{\"class\":\"customers\",\"method\":\"updatefirmstatus\"}',1,NULL,'2026-03-08 08:58:41'),(11,'update','firms','10','{\"old\":{\"status\":\"1\"},\"new\":{\"status\":0}}','{\"class\":\"customers\",\"method\":\"updatefirmstatus\"}',1,NULL,'2026-03-08 08:58:45'),(12,'update','firms','2','{\"old\":{\"status\":\"1\"},\"new\":{\"status\":0}}','{\"class\":\"customers\",\"method\":\"updatefirmstatus\"}',1,NULL,'2026-03-08 08:58:50'),(13,'update','firms','12','{\"old\":{\"status\":\"1\"},\"new\":{\"status\":0}}','{\"class\":\"customers\",\"method\":\"updatefirmstatus\"}',1,NULL,'2026-03-11 18:02:43');
/*!40000 ALTER TABLE `tf_db_operations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_docs_required`
--

DROP TABLE IF EXISTS `tf_docs_required`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_docs_required` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_id` int NOT NULL,
  `document_id` int NOT NULL,
  `display_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_docs_required`
--

LOCK TABLES `tf_docs_required` WRITE;
/*!40000 ALTER TABLE `tf_docs_required` DISABLE KEYS */;
INSERT INTO `tf_docs_required` VALUES (1,2,13,'Sales and GST','income-tax-return-sales-and-gst',1),(2,2,16,'All Income Details','income-tax-return-all-income-details',1),(3,29,13,'Sales and GST','gstr-9-sales-and-gst',1),(4,29,14,'Purchase and GST','gstr-9-purchase-and-gst',1),(5,27,15,'Bank Statement','cash-budget-bank-statement',1),(6,7,13,'Sales and GST','gst-return-sales-and-gst',1);
/*!40000 ALTER TABLE `tf_docs_required` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_documents`
--

DROP TABLE IF EXISTS `tf_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `value` tinyint(1) NOT NULL,
  `file` tinyint(1) NOT NULL DEFAULT '0',
  `file_type` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pattern` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_documents`
--

LOCK TABLES `tf_documents` WRITE;
/*!40000 ALTER TABLE `tf_documents` DISABLE KEYS */;
INSERT INTO `tf_documents` VALUES (1,'Mobile','mobile',1,0,NULL,'[0-9]{10}'),(2,'Email','email',1,0,NULL,'^[\\w\\.-]+@[a-zA-Z\\d\\.-]+\\.[a-zA-Z]{2,}$'),(3,'PAN','pan',1,1,'png|jpg|jpeg','^[A-Z]{5}\\d{4}[A-Z]$'),(4,'Aadhar','aadhar',1,2,'png|jpg|jpeg','[0-9]{12}'),(5,'Photo','photo',0,1,'png|jpg|jpeg',''),(6,'Business Address Proof','business-address-proof',0,1,'png|jpg|jpeg|pdf',''),(7,'Business Name','business-name',1,1,'png|jpg|jpeg|pdf',''),(8,'Digital Signature','digital-signature',1,0,NULL,''),(9,'DIN of Director','din-of-director',0,1,'png|jpg|jpeg|pdf',''),(10,'GST Certificate','gst-certificate',1,1,'png|jpg|jpeg|pdf',''),(11,'Partnership Deed','partnership-deed',0,1,'png|jpg|jpeg|pdf',''),(12,'Memorandom of Association','memorandom-of-association',0,1,'png|jpg|jpeg|pdf',''),(13,'Sales and GST','sales-gst',0,1,'csv|xlsx|pdf',''),(14,'Purchase and GST','purchase-gst',0,1,'csv|xlsx|pdf',''),(15,'Bank Statement','bank-statement',0,1,'csv|xlsx|pdf',''),(16,'All Income Details','all-income-details',0,1,'csv|xlsx|pdf',''),(17,'Turnover','turnover',1,0,NULL,''),(18,'Audit Report','audit-report',0,1,'csv|xlsx|pdf',''),(19,'Freight Paid','freight-paid',0,1,'csv|xlsx|pdf',''),(20,'Interest Paid','interest-paid',0,1,'csv|xlsx|pdf',''),(21,'Purchase','purchase',0,1,'csv|xlsx|pdf',''),(22,'Sales','sales',0,1,'csv|xlsx|pdf',''),(23,'Expenditure','expenditure',0,1,'csv|xlsx|pdf',''),(24,'Bilty','bilty',0,1,'csv|xlsx|pdf',''),(25,'Company Statement','company-statement',0,1,'csv|xlsx|pdf','');
/*!40000 ALTER TABLE `tf_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_emp_percent`
--

DROP TABLE IF EXISTS `tf_emp_percent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_emp_percent` (
  `id` int NOT NULL AUTO_INCREMENT,
  `emp_id` int NOT NULL,
  `percent` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_emp_percent`
--

LOCK TABLES `tf_emp_percent` WRITE;
/*!40000 ALTER TABLE `tf_emp_percent` DISABLE KEYS */;
INSERT INTO `tf_emp_percent` VALUES (1,1,20,1,'2025-07-28 18:47:23','2025-07-28 18:47:23');
/*!40000 ALTER TABLE `tf_emp_percent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_employees`
--

DROP TABLE IF EXISTS `tf_employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_employees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `mobile` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `dob` date NOT NULL,
  `address` text COLLATE utf8mb4_general_ci NOT NULL,
  `state` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `parent_id` int NOT NULL,
  `district` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `area_id` int NOT NULL,
  `pan` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `pan_file` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'PAN card document file path',
  `aadhar` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `aadhar_file` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Aadhar card document file path',
  `terms_file` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Terms & Conditions document file path',
  `percent` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int NOT NULL,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_employees`
--

LOCK TABLES `tf_employees` WRITE;
/*!40000 ALTER TABLE `tf_employees` DISABLE KEYS */;
INSERT INTO `tf_employees` VALUES (1,'Kamna','6203418546','arvind12602@gmail.com','1986-04-28','Suriya','Jharkhand',16,'Giridih',289,'AVBPB8286H',NULL,'395870903537',NULL,NULL,20,0,0,'2025-07-28 18:47:23','2025-10-25 06:52:12');
/*!40000 ALTER TABLE `tf_employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_firms`
--

DROP TABLE IF EXISTS `tf_firms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_firms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `gstin` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_id` int NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `request` tinyint(1) NOT NULL DEFAULT '0',
  `edit_request` tinyint(1) DEFAULT '0' COMMENT 'Edit request status: 0=no request, 1=pending, 2=rejected',
  `edit_request_data` text COLLATE utf8mb4_general_ci COMMENT 'Proposed firm data in JSON format',
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `edit_request` (`edit_request`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_firms`
--

LOCK TABLES `tf_firms` WRITE;
/*!40000 ALTER TABLE `tf_firms` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_firms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_formdata`
--

DROP TABLE IF EXISTS `tf_formdata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_formdata` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `user_id` int NOT NULL,
  `order_id` int NOT NULL,
  `firm_id` int DEFAULT NULL,
  `service_id` int NOT NULL,
  `field` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `field_id` int NOT NULL,
  `value` text COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `added_by` int DEFAULT NULL,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_formdata`
--

LOCK TABLES `tf_formdata` WRITE;
/*!40000 ALTER TABLE `tf_formdata` DISABLE KEYS */;
INSERT INTO `tf_formdata` VALUES (1,'2026-02-28',21,1,3,2,'income-tax-return-sales-and-gst-file',1,'/assets/service/documents/income-tax-return-sales-and-gst-file2.pdf',0,NULL,'2026-02-28 06:57:50','2026-02-28 06:57:50'),(2,'2026-02-28',21,1,3,2,'income-tax-return-all-income-details-file',2,'/assets/service/documents/income-tax-return-all-income-details-file.pdf',0,NULL,'2026-02-28 06:57:50','2026-02-28 06:57:50'),(3,'2026-02-28',21,1,3,2,'income-tax-return-year',0,'20252026',0,NULL,'2026-02-28 06:57:50','2026-02-28 06:57:50'),(4,'2026-03-08',21,3,3,7,'gst-return-sales-and-gst-file',6,'/assets/service/documents/gst-return-sales-and-gst-file.pdf',0,NULL,'2026-03-08 15:01:19','2026-03-08 15:01:19'),(5,'2026-03-08',21,3,3,7,'gst-return-year',0,'20252026',0,NULL,'2026-03-08 15:01:19','2026-03-08 15:01:19'),(6,'2026-03-08',21,3,3,7,'gst-return-month',0,'February',0,NULL,'2026-03-08 15:01:19','2026-03-08 15:01:19');
/*!40000 ALTER TABLE `tf_formdata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_invoices`
--

DROP TABLE IF EXISTS `tf_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_invoices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `order_id` int NOT NULL,
  `user_id` int NOT NULL,
  `firm_id` int NOT NULL,
  `year` varchar(8) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `billing_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `billing_email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `billing_mobile` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `firm_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `firm_gstin` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `firm_pan` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `service_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `period_value` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subtotal` float DEFAULT NULL,
  `gst_rate` float DEFAULT NULL,
  `gst_amount` float DEFAULT NULL,
  `total_amount` float DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `firm_id` (`firm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_invoices`
--

LOCK TABLES `tf_invoices` WRITE;
/*!40000 ALTER TABLE `tf_invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_kyc`
--

DROP TABLE IF EXISTS `tf_kyc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_kyc` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pan` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `pan_image` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `aadhar` varchar(12) COLLATE utf8mb4_general_ci NOT NULL,
  `aadhar_image` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `aadhar_back` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tds_certificate` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `gst_certificate` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `audit_report` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `income_tax_certificate` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL,
  `user_id` int NOT NULL DEFAULT '0',
  `firm_id` int DEFAULT NULL COMMENT 'Firm ID for per-firm KYC',
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `firm_id` (`firm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_kyc`
--

LOCK TABLES `tf_kyc` WRITE;
/*!40000 ALTER TABLE `tf_kyc` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_kyc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_notify`
--

DROP TABLE IF EXISTS `tf_notify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_notify` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_notify`
--

LOCK TABLES `tf_notify` WRITE;
/*!40000 ALTER TABLE `tf_notify` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_notify` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_old_client_data`
--

DROP TABLE IF EXISTS `tf_old_client_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_old_client_data` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL COMMENT 'Customer user ID',
  `service_id` int NOT NULL COMMENT 'Service ID',
  `firm_id` int DEFAULT NULL COMMENT 'Firm ID (if applicable)',
  `file_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Original file name',
  `file_path` varchar(500) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Path to uploaded file',
  `file_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'File type/extension',
  `file_size` bigint DEFAULT NULL COMMENT 'File size in bytes',
  `description` text COLLATE utf8mb4_general_ci COMMENT 'Description/notes about the file',
  `year` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Year associated with the data',
  `uploaded_by` int NOT NULL COMMENT 'User ID of admin/employee who uploaded',
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=Active, 0=Deleted',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_service_id` (`service_id`),
  KEY `idx_firm_id` (`firm_id`),
  KEY `idx_uploaded_by` (`uploaded_by`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Stores old client data/uploads by service';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_old_client_data`
--

LOCK TABLES `tf_old_client_data` WRITE;
/*!40000 ALTER TABLE `tf_old_client_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_old_client_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_order_assign`
--

DROP TABLE IF EXISTS `tf_order_assign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_order_assign` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `user_id` int NOT NULL,
  `done_by` int NOT NULL,
  `status` tinyint(1) NOT NULL,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_order_assign`
--

LOCK TABLES `tf_order_assign` WRITE;
/*!40000 ALTER TABLE `tf_order_assign` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_order_assign` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_packages`
--

DROP TABLE IF EXISTS `tf_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_packages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `turnover` float NOT NULL,
  `remarks` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `rate` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_packages`
--

LOCK TABLES `tf_packages` WRITE;
/*!40000 ALTER TABLE `tf_packages` DISABLE KEYS */;
INSERT INTO `tf_packages` VALUES (1,'Accountancy Prime',2500000,'<25 Lac',12000,1),(2,'Accountancy Prime',5000000,'<50 Lac',20000,1),(3,'Accountancy Prime',7500000,'<75 Lac',25000,1),(4,'Accountancy Prime',10000000,'<100 Lac',30000,1),(5,'Accountancy Prime',10000000,'>100 Lac Per 100 Lac',10000,1),(6,'Accountancy Premium',2500000,'<25 Lac',15000,1),(7,'Accountancy Premium',5000000,'<50 Lac',24000,1),(8,'Accountancy Premium',7500000,'<75 Lac',30000,1),(9,'Accountancy Premium',10000000,'<100 Lac',36000,1),(10,'Accountancy Premium',10000000,'>100 Lac Per 100 Lac',15000,1);
/*!40000 ALTER TABLE `tf_packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_payment`
--

DROP TABLE IF EXISTS `tf_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_payment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `emp_id` int NOT NULL,
  `date` date NOT NULL,
  `amount` double(15,2) NOT NULL,
  `remarks` text COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_payment`
--

LOCK TABLES `tf_payment` WRITE;
/*!40000 ALTER TABLE `tf_payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_purchases`
--

DROP TABLE IF EXISTS `tf_purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_purchases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `year` varchar(8) COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `period_value` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_id` int NOT NULL,
  `service_id` int NOT NULL,
  `firm_id` int NOT NULL,
  `service` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rate` float NOT NULL,
  `amount` float NOT NULL,
  `subtotal` float DEFAULT NULL COMMENT 'Amount before GST',
  `gst_amount` float DEFAULT NULL COMMENT '18% GST amount',
  `gst_enabled` tinyint(1) DEFAULT '0' COMMENT 'Whether GST was applied',
  `service_option` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `service_option_display` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_purchases`
--

LOCK TABLES `tf_purchases` WRITE;
/*!40000 ALTER TABLE `tf_purchases` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_request_log`
--

DROP TABLE IF EXISTS `tf_request_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_request_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `ip_address` varchar(20) NOT NULL,
  `post` longtext,
  `server` longtext,
  `cookie` longtext,
  `headers` longtext,
  `added_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_request_log`
--

LOCK TABLES `tf_request_log` WRITE;
/*!40000 ALTER TABLE `tf_request_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_request_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_roles`
--

DROP TABLE IF EXISTS `tf_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `sections` varchar(300) COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_roles`
--

LOCK TABLES `tf_roles` WRITE;
/*!40000 ALTER TABLE `tf_roles` DISABLE KEYS */;
INSERT INTO `tf_roles` VALUES (1,'Sub-admin','sub-admin','Section 1,Section 2',1),(2,'Sales officers','sales-officers','Section 1,Section 2',1),(3,'CA','ca','Section 1,Section 2',1);
/*!40000 ALTER TABLE `tf_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_security_deposit`
--

DROP TABLE IF EXISTS `tf_security_deposit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_security_deposit` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `date` date NOT NULL,
  `amount` float NOT NULL,
  `remarks` text COLLATE utf8mb4_general_ci,
  `added_by` int NOT NULL,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `added_by` (`added_by`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_security_deposit`
--

LOCK TABLES `tf_security_deposit` WRITE;
/*!40000 ALTER TABLE `tf_security_deposit` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_security_deposit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_service_options`
--

DROP TABLE IF EXISTS `tf_service_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_service_options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_id` int NOT NULL,
  `option_key` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `rate` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  KEY `option_key` (`option_key`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_service_options`
--

LOCK TABLES `tf_service_options` WRITE;
/*!40000 ALTER TABLE `tf_service_options` DISABLE KEYS */;
INSERT INTO `tf_service_options` VALUES (1,2,'audit','Audit',3000,1,'2026-02-06 17:37:09','2026-02-06 17:37:09'),(2,2,'non-audit','Non Audit',1500,1,'2026-02-06 17:37:32','2026-02-06 17:37:32'),(3,2,'capital Gain ','Capital Gain',5000,1,'2026-02-08 12:52:21','2026-02-08 12:52:21'),(4,3,'Loan amount 5Lac','Loan amount 5Lac',2500,1,'2026-02-08 12:53:05','2026-02-08 12:53:05'),(5,3,'Loan amount 10Lac','Loan amount 10Lac',3500,1,'2026-02-08 12:53:41','2026-02-08 12:53:41'),(6,3,'Loan amount 20Lac','Loan amount 20Lac',4000,1,'2026-02-08 12:54:12','2026-02-08 12:54:12'),(7,3,'Loan amount 30Lac','Loan amount 30Lac',6000,1,'2026-02-08 12:55:14','2026-02-08 12:55:14'),(8,3,'Loan amount 50Lac','Loan amount 50Lac',7500,1,'2026-02-08 12:55:44','2026-02-08 12:55:44'),(9,3,'Loan amount above 50Lac','Loan amount above 50Lac',10000,1,'2026-02-08 12:57:00','2026-02-08 12:57:00'),(10,4,'GTO 1cr','GTO 1cr',8000,1,'2026-02-08 12:57:49','2026-02-08 12:57:49'),(11,4,'GTO 2cr','GTO 2cr',10000,1,'2026-02-08 12:58:48','2026-02-08 12:58:48'),(12,4,'GTO 3cr','GTO 3cr',12000,1,'2026-02-08 13:00:07','2026-02-08 13:00:07'),(13,4,'GTO 5cr','GTO 5cr',15000,1,'2026-02-08 13:00:34','2026-02-08 13:00:34'),(14,4,'GTO 10cr','GTO 10cr',20000,1,'2026-02-08 13:02:16','2026-02-08 13:02:16'),(15,4,'GTO 20cr','GTO 20cr',25000,1,'2026-02-08 13:04:44','2026-02-08 13:05:08'),(16,5,'GTO 50cr','GTO 50cr',30000,1,'2026-02-08 13:05:48','2026-02-08 13:05:48'),(17,4,'GTO above 50cr','GTO above 50cr',40000,1,'2026-02-08 13:06:27','2026-02-08 13:06:27'),(18,32,' Project Report For Term Loan(Emi)','5L',5000,1,'2026-03-11 07:29:15','2026-03-11 07:29:15'),(19,32,' Project Report For Term Loan(Emi)','10L',7000,1,'2026-03-11 07:29:49','2026-03-11 07:29:49'),(20,32,' Project Report For Term Loan(Emi)','20L',10000,1,'2026-03-11 07:31:06','2026-03-11 07:31:06');
/*!40000 ALTER TABLE `tf_service_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_service_packages`
--

DROP TABLE IF EXISTS `tf_service_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_service_packages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `firm_id` int NOT NULL,
  `year` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `service_ids` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `service_option_ids` text COLLATE utf8mb4_general_ci COMMENT 'JSON format: {"service_id": "option_id", ...} - Single option per service',
  `package_type` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Yearly' COMMENT 'Billing type of the package: Monthly, Quarterly, Yearly, Once',
  `purchase_date` date DEFAULT NULL COMMENT 'Date the package was purchased / last renewed',
  `expiry_date` date DEFAULT NULL COMMENT 'Date the package expires and wallet debit is attempted',
  `payment_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = bill generated but not yet deducted; 1 = paid',
  `bill_amount` float NOT NULL DEFAULT '0' COMMENT 'Total amount (incl. GST) to debit from wallet on expiry / renewal',
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `request` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=No request, 1=Delete requested, 2=Rejected',
  PRIMARY KEY (`id`),
  KEY `idx_request` (`request`),
  KEY `idx_expiry_payment` (`expiry_date`,`payment_status`),
  KEY `idx_user_firm_year_type` (`user_id`,`firm_id`,`year`,`package_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_service_packages`
--

LOCK TABLES `tf_service_packages` WRITE;
/*!40000 ALTER TABLE `tf_service_packages` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_service_packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_services`
--

DROP TABLE IF EXISTS `tf_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fixed` tinyint(1) NOT NULL DEFAULT '0',
  `rate` float NOT NULL,
  `type` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `service_for` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `debit_date` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_services`
--

LOCK TABLES `tf_services` WRITE;
/*!40000 ALTER TABLE `tf_services` DISABLE KEYS */;
INSERT INTO `tf_services` VALUES (1,'Accountancy Work','accountancy-work',0,5000,'Turnover,Monthly','Firm',NULL,1),(2,'Income Tax Return','income-tax-return',0,3000,'Yearly','Individual','2026-01-02',1),(3,'Project Report For cash credit Loan','project-report-for-cash-credit-loan',0,2500,'Yearly','Firm',NULL,1),(4,'Tax Audit & Legal','tax-audit-legal',0,5000,'Yearly','Firm','2026-07-01',1),(5,'VAT Audit','vat-audit',0,2500,'Yearly','Firm','2025-06-30',1),(6,'Digital Signature','digital-signature',1,2500,'Once','Firm',NULL,1),(7,'GST Return','gst-return',1,500,'Quarterly,Monthly','Individual',NULL,1),(8,'TDS & TCS Return','tds-tcs-return',1,2500,'Quarterly','Firm','2026-02-11',1),(9,'Professional Tax Return','professional-tax-return',1,500,'Quarterly','Firm','2025-01-23',1),(10,'Tax Challan','tax-challan',1,200,'Once','Firm',NULL,1),(11,'Udyam Aadhar','udyam-aadhar',1,1500,'Yearly','Firm','2026-06-10',1),(12,'INCOME TAX COMPLIANCES','income-tax-compliances',1,5000,'Once','Firm',NULL,1),(13,'GST Compliances','gst-compliances',1,3000,'Once','Firm',NULL,1),(14,'TDS Compliances','tds-compliances',1,4000,'Once','Firm',NULL,1),(15,'Firm Registration','firm-registration',1,10000,'Once','Firm',NULL,1),(16,'PAN Registration','pan-registration',1,300,'Once','Firm',NULL,1),(17,'GST Registration','gst-registration',1,1000,'Once','Firm',NULL,1),(18,'Company Registration','company-registration',1,20000,'Once','Firm',NULL,1),(19,'TAN Registration','tan-registration',1,1000,'Once','Firm',NULL,1),(20,'DIN Registration','din-registration',1,1000,'Once','Firm',NULL,1),(21,'VAT Registration','vat-registration',1,2000,'Once','Firm',NULL,1),(22,'JPT Registration','jpt-registration',1,500,'Once','Firm',NULL,1),(23,'E invoice/ e way bill ','e-invoice-e-way-bill',0,100,'Once','Firm',NULL,1),(24,'Trading PL Balance Sheet ','trading-pl-balance-sheet',0,2000,'Once','Firm',NULL,1),(25,'Trading PL Bs ca certified ','trading-pl-bs-ca-certified',0,5000,'Once','Firm',NULL,1),(26,'Hosting & Legal ','hosting-legal',0,200,'Quarterly','Individual','2026-06-30',1),(27,'Cash Budget ','cash-budget',0,6000,'Once','Individual',NULL,1),(28,'Import/Export code','import-export-code',0,2000,'Once','Individual',NULL,1),(29,'GSTR 9','gstr-9',0,3000,'Yearly','Individual','2026-08-31',1),(30,'GSTR 9C','gstr-9c',0,5000,'Yearly','Individual','2026-10-10',1),(32,'Project Report For Term Loan(Emi)','project-report-for-term-loan-emi',0,5000,'Once','Firm',NULL,1),(33,'Net worth certificate ','net-worth-certificate',0,5000,'Once','Firm',NULL,1);
/*!40000 ALTER TABLE `tf_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_tokens`
--

DROP TABLE IF EXISTS `tf_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(50) NOT NULL,
  `device_id` varchar(255) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `regid` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_tokens`
--

LOCK TABLES `tf_tokens` WRITE;
/*!40000 ALTER TABLE `tf_tokens` DISABLE KEYS */;
INSERT INTO `tf_tokens` VALUES (1,15,'314e5b85fd07e9d1811a8be67f05248d','85f976ce21a2768e','[object Object] motorola edge 50 fusion','',0,'2026-02-28 09:42:58','2026-02-28 09:47:48'),(2,15,'dde2a900d7a3463a2aec94b0a23c1400','e3744a4075062873','[object Object] SM-S711B','',0,'2026-02-28 10:32:27','2026-02-28 10:41:37'),(3,15,'814bc8ec7f59ac74e3811933195dbcca','e3744a4075062873','[object Object] SM-S711B','',0,'2026-02-28 11:56:56','2026-02-28 14:03:13'),(4,15,'48a3b106fe1cacdd53f4709ae558960b','e3744a4075062873','[object Object] SM-S711B','',0,'2026-02-28 15:15:50','2026-02-28 15:52:32'),(5,21,'5f5724e6b243a82207be684182e4861c','35404d33b53f3855','[object Object] V2158','',0,'2026-02-28 15:42:08','2026-03-04 17:44:07'),(9,21,'11b6f32f92cc83cade4d6aac3d464a47','b58078e36005c460','[object Object] V2513','',0,'2026-03-04 17:52:55','2026-03-04 17:53:42'),(12,21,'ef36b00980062700b9480e25a4af3d8a','35404d33b53f3855','[object Object] V2158','',1,'2026-03-05 16:35:25','2026-03-13 07:21:52'),(13,15,'68aa05ad4df208216ddcc704c3bb60b3','e3744a4075062873','[object Object] SM-S711B','',1,'2026-03-12 10:52:35','2026-03-12 10:52:51');
/*!40000 ALTER TABLE `tf_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_users`
--

DROP TABLE IF EXISTS `tf_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL,
  `mobile` varchar(10) NOT NULL,
  `gstin` varchar(15) DEFAULT NULL COMMENT 'GSTIN number for admin/company',
  `name` varchar(50) NOT NULL,
  `email` varchar(60) NOT NULL,
  `password` varchar(100) NOT NULL,
  `vp` varchar(50) NOT NULL,
  `role` varchar(30) NOT NULL,
  `salt` varchar(20) NOT NULL,
  `otp` varchar(100) NOT NULL,
  `token` varchar(50) NOT NULL,
  `photo` varchar(200) NOT NULL,
  `emp_id` int DEFAULT NULL,
  `parent_id` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_users`
--

LOCK TABLES `tf_users` WRITE;
/*!40000 ALTER TABLE `tf_users` DISABLE KEYS */;
INSERT INTO `tf_users` VALUES (1,'admin','1234567890',NULL,'Admin','admin@gmail.com','$2y$10$8vlLBC9RX7/WDRnV4NHsjuL4eb0mlXlWiTTTpXaiReojQmU7pOsDu','12345','admin','GN2d04gDMm8LzWji','','','',NULL,0,1,'2023-11-02 06:23:32','2023-11-02 10:58:00'),(3,'Kamna100','6203418546',NULL,'Kamn','arvind12602@gmail.com','$2y$10$TyPdRSmoe/yVJJrEgj66meskPeoeBNqv4ntWGcaDirdB0z5di9HxO','Sales@222','ca','e3yuxRJWU0gzCpQl','','','',1,0,1,'2025-07-28 18:47:23','2025-10-25 07:03:32'),(15,'9876543210','9876543210',NULL,'Trial Demo','trial@gmail.com','$2y$10$840lcT1UX4f6LOBqIc16iOYxp5PQet8uuJnl05WmD53GTW5cp2PsW','631587','customer','RQUF7xHqhSnpeEld','','','',NULL,0,1,'2026-02-23 15:55:29','2026-02-23 15:55:29'),(16,'9631565712','9631565712',NULL,'Om enterprises','arvind12601@gmail.com','$2y$10$kJ.3TN3w7wAUKeLunoDKxOpmPGh7ZFrwL6lrTRl8MSmYKFesTVcea','269350','customer','5gnxrWqsfv4tRNYG','','','',NULL,0,1,'2026-02-24 06:57:21','2026-02-26 22:29:56'),(21,'9431533243','9431533243',NULL,'Parmjeet Kaur','mptraders33243@gmail.com','$2y$10$njhn.OKIBgBcf2AHPNODzePALcElmgPxIU67iSQJ8n.z7MtGJU7Eu','9431533243','customer','3P9xzkOKrSutCdWq','','','',NULL,0,1,'2026-02-27 17:52:20','2026-02-27 17:52:20');
/*!40000 ALTER TABLE `tf_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tf_wallet`
--

DROP TABLE IF EXISTS `tf_wallet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tf_wallet` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `date` date NOT NULL,
  `amount` float NOT NULL,
  `remarks` text COLLATE utf8mb4_general_ci COMMENT 'Payment method details and notes for admin wallet recharges',
  `merchant_transaction_id` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `merchant_user_id` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `payment_details` text COLLATE utf8mb4_general_ci,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `merchant_transaction_id` (`merchant_transaction_id`),
  KEY `idx_security_deposit` (`user_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tf_wallet`
--

LOCK TABLES `tf_wallet` WRITE;
/*!40000 ALTER TABLE `tf_wallet` DISABLE KEYS */;
/*!40000 ALTER TABLE `tf_wallet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'db_taxefi'
--

--
-- Dumping routines for database 'db_taxefi'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-13 11:58:54

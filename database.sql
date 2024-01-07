-- MySQL dump 10.13  Distrib 5.7.34, for Linux (x86_64)
--
-- Host: localhost    Database: majed
-- ------------------------------------------------------
-- Server version	5.7.34-0ubuntu0.18.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `39_access_keys`
--

DROP TABLE IF EXISTS `39_access_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_access_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `level` int(2) NOT NULL,
  `ignore_limits` tinyint(1) NOT NULL DEFAULT '0',
  `is_private_key` tinyint(1) NOT NULL DEFAULT '0',
  `ip_addresses` text,
  `date_created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_access_keys`
--

LOCK TABLES `39_access_keys` WRITE;
/*!40000 ALTER TABLE `39_access_keys` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_access_keys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_access_limits`
--

DROP TABLE IF EXISTS `39_access_limits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_access_limits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(255) NOT NULL,
  `count` int(10) NOT NULL,
  `hour_started` int(11) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_access_limits`
--

LOCK TABLES `39_access_limits` WRITE;
/*!40000 ALTER TABLE `39_access_limits` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_access_limits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_activity_history`
--

DROP TABLE IF EXISTS `39_activity_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_activity_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(200) DEFAULT NULL,
  `ip` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `done_by` int(11) DEFAULT NULL,
  `done_by_type` varchar(100) CHARACTER SET utf8 DEFAULT 'admin',
  `date` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `activity` varchar(400) CHARACTER SET utf8 DEFAULT NULL,
  `data` text CHARACTER SET utf8,
  `notification_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_activity_history`
--

LOCK TABLES `39_activity_history` WRITE;
/*!40000 ALTER TABLE `39_activity_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_activity_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_amount_paid`
--

DROP TABLE IF EXISTS `39_amount_paid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_amount_paid` (
  `paid_id` int(11) NOT NULL AUTO_INCREMENT,
  `paid_user_id` int(11) unsigned NOT NULL,
  `paid_amount` double NOT NULL DEFAULT '0',
  `paid_date` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `paid_type` varchar(50) CHARACTER SET utf8 NOT NULL,
  `payout_fee` double NOT NULL DEFAULT '0',
  `transaction_id` varchar(100) NOT NULL DEFAULT '0',
  `paid_status` varchar(10) NOT NULL,
  `request_id` int(11) DEFAULT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`paid_id`),
  KEY `paid_date` (`paid_date`),
  KEY `paid_type` (`paid_type`),
  KEY `paid_user_id` (`paid_user_id`),
  CONSTRAINT `39_amount_paid_ibfk_1` FOREIGN KEY (`paid_user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_amount_paid`
--

LOCK TABLES `39_amount_paid` WRITE;
/*!40000 ALTER TABLE `39_amount_paid` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_amount_paid` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_amount_type`
--

DROP TABLE IF EXISTS `39_amount_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_amount_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `db_amt_type` varchar(400) CHARACTER SET utf8 NOT NULL,
  `view_amt_type` varchar(400) CHARACTER SET utf8 NOT NULL,
  `status` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_amount_type`
--

LOCK TABLES `39_amount_type` WRITE;
/*!40000 ALTER TABLE `39_amount_type` DISABLE KEYS */;
INSERT INTO `39_amount_type` VALUES (1,'pin_ purchased','Pin Purchased','no'),(2,'payout_released','Payout Released','no'),(3,'referral','Referral commission','yes'),(4,'leg','Binary Commission','yes'),(5,'rank_bonus','Rank Commission','yes'),(6,'level_commission','Level Commission','yes'),(7,'repurchase_level_commission','Level Commission by Purchase','yes'),(8,'repurchase_leg','Binary Commission by Purchase','yes'),(18,'stair_step','Stair Step','no'),(19,'override_bonus','Override Bonus','no'),(20,'upgrade_level_commission','Level Commission by Upgrade','no'),(21,'upgrade_leg','Binary Commission by Upgrade','no'),(22,'daily_investment','Daily Investment','no'),(84,'donation','Donation','no'),(85,'purchase_donation','Registration amount','no'),(86,'xup_commission','X-UP Commission','no'),(87,'xup_repurchase_level_commission','X-UP Commission by Purchase','no'),(88,'xup_upgrade_level_commission','X-UP Commission by Upgrade','no'),(89,'matching_bonus','Matching Bonus','yes'),(90,'matching_bonus_purchase','Matching Bonus by Purchase','no'),(91,'matching_bonus_upgrade','Matching Bonus by Upgrade','no'),(92,'pool_bonus','Pool Bonus','yes'),(93,'fast_start_bonus','Fast Start Bonus','yes'),(94,'vacation_fund','Vacation Fund','yes'),(95,'education_fund','Education Fund','yes'),(96,'car_fund','Car Fund','yes'),(97,'house_fund','House Fund','yes'),(99,'sales_commission','Sales Commission','yes');
/*!40000 ALTER TABLE `39_amount_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_app_config`
--

DROP TABLE IF EXISTS `39_app_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_app_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `android_version` float NOT NULL DEFAULT '0',
  `ios_version` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_app_config`
--

LOCK TABLES `39_app_config` WRITE;
/*!40000 ALTER TABLE `39_app_config` DISABLE KEYS */;
INSERT INTO `39_app_config` VALUES (1,0,0);
/*!40000 ALTER TABLE `39_app_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_authorize_config`
--

DROP TABLE IF EXISTS `39_authorize_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_authorize_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merchant_id` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `transaction_key` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_authorize_config`
--

LOCK TABLES `39_authorize_config` WRITE;
/*!40000 ALTER TABLE `39_authorize_config` DISABLE KEYS */;
INSERT INTO `39_authorize_config` VALUES (1,'9z4m59GGJ','3r3hpSENu8G39u6h');
/*!40000 ALTER TABLE `39_authorize_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_authorize_payment_details`
--

DROP TABLE IF EXISTS `39_authorize_payment_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_authorize_payment_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `first_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `last_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `company` varchar(100) CHARACTER SET utf8 NOT NULL,
  `address` varchar(100) CHARACTER SET utf8 NOT NULL,
  `city` varchar(100) CHARACTER SET utf8 NOT NULL,
  `state` varchar(100) CHARACTER SET utf8 NOT NULL,
  `zip` varchar(100) CHARACTER SET utf8 NOT NULL,
  `country` varchar(100) CHARACTER SET utf8 NOT NULL,
  `phone` int(11) NOT NULL DEFAULT '0',
  `fax` varchar(100) CHARACTER SET utf8 NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 NOT NULL,
  `date` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `invoice_num` varchar(100) CHARACTER SET utf8 NOT NULL,
  `description` varchar(100) CHARACTER SET utf8 NOT NULL,
  `cust_id` varchar(100) CHARACTER SET utf8 NOT NULL,
  `ship_to_first_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `ship_to_last_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `ship_to_company` varchar(100) CHARACTER SET utf8 NOT NULL,
  `ship_to_address` varchar(200) CHARACTER SET utf8 NOT NULL,
  `ship_to_city` varchar(100) CHARACTER SET utf8 NOT NULL,
  `ship_to_state` varchar(100) CHARACTER SET utf8 NOT NULL,
  `ship_to_zip` varchar(100) CHARACTER SET utf8 NOT NULL,
  `ship_to_country` varchar(100) CHARACTER SET utf8 NOT NULL,
  `amount` double NOT NULL DEFAULT '0',
  `tax` double NOT NULL DEFAULT '0',
  `duty` double NOT NULL DEFAULT '0',
  `freight` double NOT NULL DEFAULT '0',
  `auth_code` varchar(100) CHARACTER SET utf8 NOT NULL,
  `trans_id` int(100) NOT NULL DEFAULT '0',
  `method` varchar(100) CHARACTER SET utf8 NOT NULL,
  `card_type` char(100) CHARACTER SET utf8 NOT NULL,
  `account_number` varchar(100) CHARACTER SET utf8 NOT NULL,
  `pending_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `pending_id` (`pending_id`),
  CONSTRAINT `39_authorize_payment_details_ibfk_15` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_authorize_payment_details_ibfk_16` FOREIGN KEY (`pending_id`) REFERENCES `39_pending_registration` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_authorize_payment_details`
--

LOCK TABLES `39_authorize_payment_details` WRITE;
/*!40000 ALTER TABLE `39_authorize_payment_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_authorize_payment_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_autoresponder_setting`
--

DROP TABLE IF EXISTS `39_autoresponder_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_autoresponder_setting` (
  `mail_number` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(200) CHARACTER SET utf8 NOT NULL,
  `content` longtext,
  `date_to_send` int(11) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  PRIMARY KEY (`mail_number`),
  KEY `date_to_send` (`date_to_send`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_autoresponder_setting`
--

LOCK TABLES `39_autoresponder_setting` WRITE;
/*!40000 ALTER TABLE `39_autoresponder_setting` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_autoresponder_setting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_back_faq`
--

DROP TABLE IF EXISTS `39_back_faq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_back_faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` tinytext,
  `answer` varchar(1000) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_back_faq`
--

LOCK TABLES `39_back_faq` WRITE;
/*!40000 ALTER TABLE `39_back_faq` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_back_faq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_bank_transfer_settings`
--

DROP TABLE IF EXISTS `39_bank_transfer_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_bank_transfer_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_info` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_bank_transfer_settings`
--

LOCK TABLES `39_bank_transfer_settings` WRITE;
/*!40000 ALTER TABLE `39_bank_transfer_settings` DISABLE KEYS */;
INSERT INTO `39_bank_transfer_settings` VALUES (1,'Account Details');
/*!40000 ALTER TABLE `39_bank_transfer_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_binary_bonus_config`
--

DROP TABLE IF EXISTS `39_binary_bonus_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_binary_bonus_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calculation_criteria` varchar(50) NOT NULL,
  `calculation_period` varchar(50) NOT NULL,
  `commission_type` varchar(50) NOT NULL,
  `pair_commission` double NOT NULL,
  `pair_type` varchar(20) NOT NULL,
  `pair_value` double NOT NULL,
  `point_value` double NOT NULL,
  `carry_forward` varchar(20) NOT NULL,
  `flush_out` varchar(20) NOT NULL,
  `flush_out_limit` double NOT NULL,
  `flush_out_period` varchar(50) NOT NULL,
  `locking_period` int(11) NOT NULL,
  `block_binary_pv` varchar(10) NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_binary_bonus_config`
--

LOCK TABLES `39_binary_bonus_config` WRITE;
/*!40000 ALTER TABLE `39_binary_bonus_config` DISABLE KEYS */;
INSERT INTO `39_binary_bonus_config` VALUES (1,'sales_volume','instant','flat',10,'11',100,100,'yes','yes',20,'daily',0,'no');
/*!40000 ALTER TABLE `39_binary_bonus_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_binary_bonus_history`
--

DROP TABLE IF EXISTS `39_binary_bonus_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_binary_bonus_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_leg` int(11) NOT NULL,
  `left_leg` int(11) NOT NULL,
  `right_leg` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
  `calculation_type` varchar(50) NOT NULL,
  `from_date` datetime NOT NULL,
  `to_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_binary_bonus_history`
--

LOCK TABLES `39_binary_bonus_history` WRITE;
/*!40000 ALTER TABLE `39_binary_bonus_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_binary_bonus_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_bitcoin_addresses`
--

DROP TABLE IF EXISTS `39_bitcoin_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_bitcoin_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bitcoin_address` text NOT NULL,
  `paid_status` varchar(100) NOT NULL DEFAULT 'no',
  `date` datetime NOT NULL,
  `current_status` varchar(100) NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`),
  KEY `paid_status` (`paid_status`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_bitcoin_addresses`
--

LOCK TABLES `39_bitcoin_addresses` WRITE;
/*!40000 ALTER TABLE `39_bitcoin_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_bitcoin_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_bitcoin_configuration`
--

DROP TABLE IF EXISTS `39_bitcoin_configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_bitcoin_configuration` (
  `id` int(11) NOT NULL,
  `api_key` text NOT NULL,
  `api_secret_key` text NOT NULL,
  `mode` int(11) NOT NULL,
  `live_wallet_name` varchar(50) NOT NULL,
  `live_wallet_password` varchar(50) NOT NULL,
  `test_wallet_name` varchar(50) NOT NULL,
  `test_wallet_password` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_bitcoin_configuration`
--

LOCK TABLES `39_bitcoin_configuration` WRITE;
/*!40000 ALTER TABLE `39_bitcoin_configuration` DISABLE KEYS */;
INSERT INTO `39_bitcoin_configuration` VALUES (1,'109c6d771d351712555be3c880f954c0bc9083e8','777e28a3ec0f0916776b984e48bba9150877b30c',0,'ioss_live','ioss_live','ioss_test','ioss_test');
/*!40000 ALTER TABLE `39_bitcoin_configuration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_bitcoin_history`
--

DROP TABLE IF EXISTS `39_bitcoin_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_bitcoin_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(100) DEFAULT NULL,
  `data` text NOT NULL,
  `purpose` varchar(50) NOT NULL,
  `status` varchar(10) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_bitcoin_history`
--

LOCK TABLES `39_bitcoin_history` WRITE;
/*!40000 ALTER TABLE `39_bitcoin_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_bitcoin_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_bitcoin_payment_details`
--

DROP TABLE IF EXISTS `39_bitcoin_payment_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_bitcoin_payment_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bitcoin_history_id` int(11) NOT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `purpose` varchar(20) NOT NULL,
  `amount` double NOT NULL,
  `bitcoin_rate` double NOT NULL,
  `bitcoin_amount_to_be_paid` double NOT NULL,
  `paid_bitcoin_amount` double NOT NULL,
  `bitcoin_address` text NOT NULL,
  `transaction` text NOT NULL,
  `return_address` text NOT NULL,
  `date` datetime NOT NULL,
  `pending_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `pending_id` (`pending_id`),
  CONSTRAINT `39_bitcoin_payment_details_ibfk_15` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_bitcoin_payment_details_ibfk_16` FOREIGN KEY (`pending_id`) REFERENCES `39_pending_registration` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_bitcoin_payment_details`
--

LOCK TABLES `39_bitcoin_payment_details` WRITE;
/*!40000 ALTER TABLE `39_bitcoin_payment_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_bitcoin_payment_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_bitcoin_payment_process_details`
--

DROP TABLE IF EXISTS `39_bitcoin_payment_process_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_bitcoin_payment_process_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registrer` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `regr_data` text NOT NULL,
  `reason` text NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_bitcoin_payment_process_details`
--

LOCK TABLES `39_bitcoin_payment_process_details` WRITE;
/*!40000 ALTER TABLE `39_bitcoin_payment_process_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_bitcoin_payment_process_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_bitcoin_payout_release_error_report`
--

DROP TABLE IF EXISTS `39_bitcoin_payout_release_error_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_bitcoin_payout_release_error_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bitcoin_history_id` int(11) NOT NULL,
  `from_user` int(11) unsigned DEFAULT NULL,
  `to_user_id` int(11) unsigned DEFAULT NULL,
  `amount_payable` float NOT NULL,
  `error_reason` text NOT NULL,
  `date` datetime NOT NULL,
  `bitcoin_amount` float NOT NULL,
  `bitcoin_address` varchar(250) NOT NULL,
  `release_status` varchar(50) NOT NULL DEFAULT 'no',
  `payment_type` varchar(20) NOT NULL DEFAULT 'Bitcoin',
  PRIMARY KEY (`id`),
  KEY `from_user` (`from_user`),
  KEY `to_user_id` (`to_user_id`),
  CONSTRAINT `39_bitcoin_payout_release_error_report_ibfk_15` FOREIGN KEY (`from_user`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_bitcoin_payout_release_error_report_ibfk_16` FOREIGN KEY (`to_user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_bitcoin_payout_release_error_report`
--

LOCK TABLES `39_bitcoin_payout_release_error_report` WRITE;
/*!40000 ALTER TABLE `39_bitcoin_payout_release_error_report` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_bitcoin_payout_release_error_report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_bitgo_configuration`
--

DROP TABLE IF EXISTS `39_bitgo_configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_bitgo_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wallet_id` varchar(100) NOT NULL,
  `token` varchar(100) NOT NULL,
  `mode` varchar(11) NOT NULL,
  `wallet_passphrase` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_bitgo_configuration`
--

LOCK TABLES `39_bitgo_configuration` WRITE;
/*!40000 ALTER TABLE `39_bitgo_configuration` DISABLE KEYS */;
INSERT INTO `39_bitgo_configuration` VALUES (1,'2N3dHjFLsJdqydWtwX3sxEtV99Qh5u3L1Zo','v2xee355d9655a120ef64e70f0efd6796e9e52160181aa256cd48f795cdbb8640b7','test',''),(2,'t37usMEiFL69aVox5NC4DzeNvqAuYeuLYk8','tv2xda6bf5ec5a61618617c1e85bc14260ce7904b5c466052505785e828aee00a00c','live','');
/*!40000 ALTER TABLE `39_bitgo_configuration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_bitgo_payment_history`
--

DROP TABLE IF EXISTS `39_bitgo_payment_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_bitgo_payment_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `regr` text NOT NULL,
  `wallet_id` varchar(100) NOT NULL,
  `send_amount` double DEFAULT NULL,
  `pay_address` varchar(50) NOT NULL,
  `address_result` text NOT NULL,
  `date` datetime NOT NULL,
  `recieved_amount` double DEFAULT NULL,
  `recieved_result` text NOT NULL,
  `bitcoin_payment` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `status` varchar(100) NOT NULL,
  `product_id` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `39_bitgo_payment_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_bitgo_payment_history`
--

LOCK TABLES `39_bitgo_payment_history` WRITE;
/*!40000 ALTER TABLE `39_bitgo_payment_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_bitgo_payment_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_bitgo_payout_release_history`
--

DROP TABLE IF EXISTS `39_bitgo_payout_release_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_bitgo_payout_release_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `tx_id` longtext NOT NULL,
  `hash` longtext NOT NULL,
  `status` varchar(255) DEFAULT 'NA',
  `admin_btc_balance` varchar(255) NOT NULL,
  `payout_release_amount` double NOT NULL,
  `btc_send_amount` varchar(255) NOT NULL,
  `btc_transaction_fee` varchar(255) NOT NULL,
  `btc_admin_debit` varchar(255) NOT NULL,
  `bitcoin_address` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `release` longtext NOT NULL,
  `response` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `39_bitgo_payout_release_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_bitgo_payout_release_history`
--

LOCK TABLES `39_bitgo_payout_release_history` WRITE;
/*!40000 ALTER TABLE `39_bitgo_payout_release_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_bitgo_payout_release_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_blockchain_config`
--

DROP TABLE IF EXISTS `39_blockchain_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_blockchain_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `my_xpub` text NOT NULL,
  `my_api_key` text NOT NULL,
  `secret` varchar(250) NOT NULL DEFAULT 'yes',
  `main_password` varchar(250) NOT NULL DEFAULT 'yes',
  `second_password` varchar(250) NOT NULL DEFAULT 'yes',
  `fee` double NOT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_blockchain_config`
--

LOCK TABLES `39_blockchain_config` WRITE;
/*!40000 ALTER TABLE `39_blockchain_config` DISABLE KEYS */;
INSERT INTO `39_blockchain_config` VALUES (1,'test','test','yes','yes','yes',0,NULL);
/*!40000 ALTER TABLE `39_blockchain_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_blockchain_history`
--

DROP TABLE IF EXISTS `39_blockchain_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_blockchain_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(250) NOT NULL,
  `used_for` varchar(250) NOT NULL,
  `product_id` int(11) NOT NULL,
  `post_data` text NOT NULL,
  `transaction_hash` text NOT NULL,
  `payment_address` varchar(250) NOT NULL,
  `secret` varchar(250) NOT NULL,
  `amount_to_pay` double NOT NULL,
  `total_btc` double NOT NULL,
  `value` double NOT NULL,
  `confirmations` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `call_back_error` text NOT NULL,
  `json_response` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_blockchain_history`
--

LOCK TABLES `39_blockchain_history` WRITE;
/*!40000 ALTER TABLE `39_blockchain_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_blockchain_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_blockchain_payout_release_history`
--

DROP TABLE IF EXISTS `39_blockchain_payout_release_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_blockchain_payout_release_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `status` int(11) NOT NULL,
  `message` varchar(255) DEFAULT 'NA',
  `admin_btc_balance` varchar(255) NOT NULL,
  `response` longtext NOT NULL,
  `payout_release_amount` double NOT NULL,
  `system_currency` varchar(20) NOT NULL,
  `recent_bitcoin_rate` double NOT NULL,
  `btc_send_amount` varchar(255) NOT NULL,
  `btc_transaction_fee` varchar(255) NOT NULL,
  `btc_admin_debit` varchar(255) NOT NULL,
  `bitcoin_address` varchar(255) NOT NULL,
  `account_type` varchar(100) NOT NULL,
  `tx_hash` longtext NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `39_blockchain_payout_release_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_blockchain_payout_release_history`
--

LOCK TABLES `39_blockchain_payout_release_history` WRITE;
/*!40000 ALTER TABLE `39_blockchain_payout_release_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_blockchain_payout_release_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_business_volume`
--

DROP TABLE IF EXISTS `39_business_volume`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_business_volume` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `from_id` int(11) unsigned NOT NULL,
  `left_leg` int(20) NOT NULL DEFAULT '0',
  `right_leg` int(20) NOT NULL DEFAULT '0',
  `left_carry` int(20) NOT NULL,
  `right_carry` int(20) NOT NULL,
  `amount_type` varchar(50) NOT NULL,
  `action` varchar(30) NOT NULL DEFAULT 'added',
  `date_of_submission` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `from_id` (`from_id`),
  CONSTRAINT `39_business_volume_ibfk_15` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_business_volume_ibfk_16` FOREIGN KEY (`from_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_business_volume`
--

LOCK TABLES `39_business_volume` WRITE;
/*!40000 ALTER TABLE `39_business_volume` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_business_volume` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_cart`
--

DROP TABLE IF EXISTS `39_cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `data` text NOT NULL,
  `date_updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_cart`
--

LOCK TABLES `39_cart` WRITE;
/*!40000 ALTER TABLE `39_cart` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_ci_sessions`
--

DROP TABLE IF EXISTS `39_ci_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  PRIMARY KEY (`id`,`ip_address`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_ci_sessions`
--

LOCK TABLES `39_ci_sessions` WRITE;
/*!40000 ALTER TABLE `39_ci_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_ci_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_common_mail_settings`
--

DROP TABLE IF EXISTS `39_common_mail_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_common_mail_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_type` varchar(150) CHARACTER SET utf8 NOT NULL,
  `subject` varchar(200) CHARACTER SET utf8 NOT NULL,
  `mail_content` text CHARACTER SET utf8 NOT NULL,
  `date` datetime DEFAULT NULL,
  `mail_status` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `lang_ref_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mail_type` (`mail_type`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_common_mail_settings`
--

LOCK TABLES `39_common_mail_settings` WRITE;
/*!40000 ALTER TABLE `39_common_mail_settings` DISABLE KEYS */;
INSERT INTO `39_common_mail_settings` VALUES (1,'send_tranpass','Change Transaction Password ','<div class=\"banner\" style=\"background: url({banner_img});height: 58px;color: #fff;font-size: 21px;padding: 43px 20px 20px 40px;\">Transaction password changed successfully!!!</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"><h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">Dear <span style=\"font-weight:bold;\">{first_name},</span> </h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;Your new Transaction Password is {password}</p> </div>','2020-02-05 19:03:07','yes',1),(2,'send_tranpass','Cambiar contraseña de transacción','<div class=\"banner\" style=\"background: url({banner_img});height: 58px;color: #fff;font-size: 21px;padding: 43px 20px 20px 40px;\">La contraseña de la transacción se cambió correctamente!!!</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"><h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">querido <span style=\"font-weight:bold;\">{first_name},</span> </h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;Su nueva contraseña de transacción es {password}</p> </div>','2020-02-05 19:03:07','yes',2),(3,'send_tranpass','更改交易密码','<div class=\"banner\" style=\"background: url({banner_img});height: 58px;color: #fff;font-size: 21px;padding: 43px 20px 20px 40px;\">\"交易密码修改成功!!!</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"><h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">亲 <span style=\"font-weight:bold;\">{first_name},</span> </h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;您的新交易密码为 {password}</p> </div>','2020-02-05 19:03:07','yes',3),(4,'send_tranpass','Transaktionspasswort ändern','<div class=\"banner\" style=\"background: url({banner_img});height: 58px;color: #fff;font-size: 21px;padding: 43px 20px 20px 40px;\">Das Transaktionskennwort wurde erfolgreich geändert!!!</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"><h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">sehr geehrter <span style=\"font-weight:bold;\">{first_name},</span> </h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;Ihr neues Transaktionspasswort lautet {password}</p> </div>','2020-02-05 19:03:07','yes',4),(5,'send_tranpass','Alterar senha da transação','<div class=\"banner\" style=\"background: url({banner_img});height: 58px;color: #fff;font-size: 21px;padding: 43px 20px 20px 40px;\">Senha da transação alterada com sucesso!!!</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"><h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">caro <span style=\"font-weight:bold;\">{first_name},</span> </h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;YSua nova senha de transação é {password}</p> </div>','2020-02-05 19:03:07','yes',5),(6,'send_tranpass','Changer le mot de passe de transaction','<div class=\"banner\" style=\"background: url({banner_img});height: 58px;color: #fff;font-size: 21px;padding: 43px 20px 20px 40px;\">Le mot de passe de transaction a été modifié avec succès!!!</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"><h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">chère <span style=\"font-weight:bold;\">{first_name},</span> </h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;Votre nouveau mot de passe de transaction est {password}</p> </div>','2020-02-05 19:03:07','yes',6),(7,'send_tranpass','Cambia password transazione','<div class=\"banner\" style=\"background: url({banner_img});height: 58px;color: #fff;font-size: 21px;padding: 43px 20px 20px 40px;\">Password della transazione modificata correttamente!!!</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"><h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">caro <span style=\"font-weight:bold;\">{first_name},</span> </h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;La tua nuova password di transazione è {password}</p> </div>','2020-02-05 19:03:07','yes',7),(8,'send_tranpass','İşlem Parolasını Değiştir','<div class=\"banner\" style=\"background: url({banner_img});height: 58px;color: #fff;font-size: 21px;padding: 43px 20px 20px 40px;\">Tİşlem şifresi başarıyla değiştirildi!!!</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"><h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">Sayın <span style=\"font-weight:bold;\">{first_name},</span> </h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;Yeni İşlem Parolanız {password}</p> </div>','2020-02-05 19:03:07','yes',8),(9,'send_tranpass','Zmień hasło transakcji','<div class=\"banner\" style=\"background: url({banner_img});height: 58px;color: #fff;font-size: 21px;padding: 43px 20px 20px 40px;\">Hasło transakcji zostało zmienione!!!</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"><h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">Drogi <span style=\"font-weight:bold;\">{first_name},</span> </h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;\"Nowe hasło do transakcji to {password}</p> </div>','2020-02-05 19:03:07','yes',9),(10,'send_tranpass','تغيير كلمة مرور المعاملة','<div class=\"banner\" style=\"background: url({banner_img});height: 58px;color: #fff;font-size: 21px;padding: 43px 20px 20px 40px;\">تم تغيير كلمة مرور المعاملة بنجاح!!!</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"><h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">العزيز <span style=\"font-weight:bold;\">{first_name},</span> </h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;كلمة المرور الجديدة للمعاملات هي {password}</p> </div>','2020-02-05 19:03:07','yes',10),(11,'send_tranpass','Изменить пароль транзакции','<div class=\"banner\" style=\"background: url({banner_img});height: 58px;color: #fff;font-size: 21px;padding: 43px 20px 20px 40px;\">Пароль транзакции успешно изменен!!!</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"><h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">Уважаемые <span style=\"font-weight:bold;\">{first_name},</span> </h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;Ваш новый пароль для транзакции {password}</p> </div>','2020-02-05 19:03:07','yes',11),(12,'payout_request','Payout Request','<div class=\"banner\" style=\"background: url({banner_img}); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\"> User requested payout</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"> <h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">Dear <span style=\"font-weight:bold;\">{admin_user_name},</span></h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;{username} requested payout of {payout_amount}</p></div>','2020-02-05 19:03:07','yes',1),(13,'payout_request','Solicitud de pago','<div class=\"banner\" style=\"background: url({banner_img}); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\"> Pago solicitado por el usuario</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"> <h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">querido <span style=\"font-weight:bold;\">{admin_user_name},</span></h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;{username} monto de pago solicitado de {payout_amount}</p></div>','2020-02-05 19:03:07','yes',2),(14,'payout_request','付款要求','<div class=\"banner\" style=\"background: url({banner_img}); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\"> 用户请求的付款</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"> <h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">亲 <span style=\"font-weight:bold;\">{admin_user_name},</span></h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;{username} 要求的支出金额{payout_amount}</p></div>','2020-02-05 19:03:07','yes',3),(15,'payout_request','Auszahlungsanforderung','<div class=\"banner\" style=\"background: url({banner_img}); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\">Vom Benutzer angeforderte Auszahlung</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"> <h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">sehr geehrter <span style=\"font-weight:bold;\">{admin_user_name},</span></h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;{username} Gewünschter Auszahlungsbetrag von {payout_amount}</p></div>','2020-02-05 19:03:07','yes',4),(16,'payout_request','Pedido de Pagamento','<div class=\"banner\" style=\"background: url({banner_img}); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\"> Pagamento solicitado pelo usuário</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"> <h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">caro <span style=\"font-weight:bold;\">{admin_user_name},</span></h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;{username} valor do pagamento solicitado de {payout_amount}</p></div>','2020-02-05 19:03:07','yes',5),(17,'payout_request','Demande de paiement','<div class=\"banner\" style=\"background: url({banner_img}); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\"> Paiement demandé par lutilisateur</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"> <h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">chère <span style=\"font-weight:bold;\">{admin_user_name},</span></h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;{username} montant du paiement demandé de {payout_amount}</p></div>','2020-02-05 19:03:07','yes',6),(18,'payout_request','Richiesta di pagamento','<div class=\"banner\" style=\"background: url({banner_img}); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\"> Lutente ha richiesto il pagamento</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"> <h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">caro <span style=\"font-weight:bold;\">{admin_user_name},</span></h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;{username} importo di pagamento richiesto di {payout_amount}</p></div>','2020-02-05 19:03:07','yes',7),(19,'payout_request','değerli müşterimiz','<div class=\"banner\" style=\"background: url({banner_img}); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\">Kullanıcı istediği ödeme</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"> <h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">Sayın <span style=\"font-weight:bold;\">{admin_user_name},</span></h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;{username} istenen ödeme tutarı {payout_amount}</p></div>','2020-02-05 19:03:07','yes',8),(20,'payout_request','Żądanie zapłaty','<div class=\"banner\" style=\"background: url({banner_img}); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\">Żądanie zapłaty przez użytkownika</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"> <h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">Drogi <span style=\"font-weight:bold;\">{admin_user_name},</span></h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;{username} żądana kwota wypłaty w wysokości {payout_amount}</p></div>','2020-02-05 19:03:07','yes',9),(21,'payout_request','طلب دفع تعويضات','<div class=\"banner\" style=\"background: url({banner_img}); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\"> طلب المستخدم دفع تعويضات</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"> <h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">العزيز <span style=\"font-weight:bold;\">{admin_user_name},</span></h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;{username} طلب دفع تعويضات {payout_amount}</p></div>','2020-02-05 19:03:07','yes',10),(22,'payout_request','Запрос на выплату ','<div class=\"banner\" style=\"background: url({banner_img}); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\"> Пользователь запросил выплату</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"> <h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">Уважаемые <span style=\"font-weight:bold;\">{admin_user_name},</span></h1> <p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;{username} запрашиваемая сумма выплаты {payout_amount}</p></div>','2020-02-05 19:03:07','yes',11),(23,'registration_email_verification','Email Verification','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Hi {full_name},</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Thanks for creating {company_name} account. To continue, Please confirm your email address by clicking the link<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',1),(24,'registration_email_verification','verificacion de email','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Hola {full_name},</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Gracias por crear {company_name} cuenta. Continuar, Confirme su dirección de correo electrónico haciendo clic en el enlace<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',2),(25,'registration_email_verification','电子邮件验证','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">你好 {full_name},</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;感谢您的创作 {company_name} 帐户. 接着说, 请通过点击确认您的电子邮件地址 链接<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',3),(26,'registration_email_verification','E-Mail-Verifizierung','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Hallo {full_name},</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vielen Dank für das Erstellen {company_name} Konto. Weitermachen, Bitte bestätigen Sie Ihre E-Mail-Adresse, indem Sie auf den Link klicken<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',4),(27,'registration_email_verification','verificação de e-mail','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Oi {full_name},</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Obrigado por criar {company_name} conta. Continuar, Confirme seu endereço de e-mail clicando no link<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',5),(28,'registration_email_verification','vérification de l\'E-mail','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">salut {full_name},</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Merci d\'avoir créé {company_name} Compte. Continuer, Veuillez confirmer votre adresse e-mail en cliquant sur le lien<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',6),(29,'registration_email_verification','verifica email','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Ciao {full_name},</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Grazie per aver creato {company_name} account. Continuare, Conferma il tuo indirizzo e-mail facendo clic sul collegamento<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',7),(30,'registration_email_verification','Eposta Doğrulama','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Selam {full_name},</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Yarattığın için teşekkürler {company_name} hesap. Devam etmek, Lütfen bağlantıyı tıklayarak e-posta adresinizi onaylayın<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',8),(31,'registration_email_verification','Weryfikacja adresu e-mail','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">cześć {full_name},</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dzięki za stworzenie {company_name} konto. Kontynuować, Potwierdź swój adres e-mail, klikając link<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',9),(32,'registration_email_verification','تأكيد بواسطة البريد الالكتروني','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">مرحبا {full_name},</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;شكرا لانشاء {company_name} الحساب. لاستكمال, يرجى تأكيد عنوان بريدك الإلكتروني من خلال النقر على الرابط<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',10),(33,'registration_email_verification','подтверждение адреса электронной почты','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Здравствуй {full_name},</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Спасибо за создание {company_name} учетная запись. Продолжат, Пожалуйста, подтвердите свой адрес электронной почты, нажав на ссылку<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',11),(34,'forgot_password','Forgot Password','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Dear Customer,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;you are recently requested reset password for that please follow the below link}:<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',1),(35,'forgot_password','Se te olvidó tu contraseña','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">estimado cliente,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;recientemente se le solicitó restablecer la contraseña para eso, siga el siguiente enlace}:<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',2),(36,'forgot_password','忘记密码','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">尊敬的客户,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;您最近被要求为此重置密码，请点击以下链接}:<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',3),(37,'forgot_password','Passwort vergessen','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Sehr geehrter Kunde,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sie wurden vor kurzem aufgefordert, das Passwort zurückzusetzen. Bitte folgen Sie dem untenstehenden Link}:<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',4),(38,'forgot_password','Esqueceu a senha','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Estimado cliente,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\"você foi solicitado recentemente a redefinir a senha para isso, siga o link abaixo}:<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',5),(39,'forgot_password','Mot de passe oublié','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">cher client,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;on vous a récemment demandé de réinitialiser le mot de passe pour cela, veuillez suivre le lien ci-dessous}:<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',6),(40,'forgot_password','Ha dimenticato la password','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Caro cliente,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;di recente ti viene richiesta la reimpostazione della password per favore segui il link qui sotto}:<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',7),(41,'forgot_password','Parolanızı mı unuttunuz','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">değerli müşterimiz,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Son zamanlarda sizden bunun için sıfırlama şifresi istendiğinde lütfen aşağıdaki bağlantıyı izleyin}:<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',8),(42,'forgot_password','Zapomniałeś hasła','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">drogi Kliencie,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ostatnio zostałeś poproszony o zresetowanie hasła, kliknij poniższy link}:<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',9),(43,'forgot_password','هل نسيت كلمة المرور','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">عزيزي العميل,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; تمت مطالبتك مؤخرًا بإعادة تعيين كلمة المرور لذلك يرجى اتباع الرابط أدناه}:<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',10),(44,'forgot_password','Забыл пароль','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Уважаемый клиент,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Вы недавно запросили сброс пароля для этого, пожалуйста, перейдите по ссылке ниже}:<p> <a href=\"{link}\">{link}</a> <br><br><br></font></td></tr></table></body>','2020-02-05 19:03:07','yes',11),(45,'reset_googleAuth','Reset Google Authentication','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Dear Customer,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;you are recently requested reset Google Authentication for that please follow the below link:<p> <a href=\"{link}\">{link}</a> <br><br><br> </td></tr></font></table></body>','2020-02-05 19:03:07','yes',1),(46,'reset_googleAuth','Restablecer autenticación de Google','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">estimado cliente,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;recientemente se le solicitó restablecer la autenticación de Google para eso, siga el siguiente enlace:<p> <a href=\"{link}\">{link}</a> <br><br><br> </td></tr></font></table></body>','2020-02-05 19:03:07','yes',2),(47,'reset_googleAuth','重置Google身份验证','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">尊敬的客户,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;最近要求您为此重置Google身份验证，请点击以下链接:<p> <a href=\"{link}\">{link}</a> <br><br><br> </td></tr></font></table></body>','2020-02-05 19:03:07','yes',3),(48,'reset_googleAuth','Google-Authentifizierung zurücksetzen','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Sehr geehrter Kunde</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sie wurden kürzlich aufgefordert, die Google-Authentifizierung zurückzusetzen. Folgen Sie dazu dem unten stehenden Link:<p> <a href=\"{link}\">{link}</a> <br><br><br> </td></tr></font></table></body>','2020-02-05 19:03:07','yes',4),(49,'reset_googleAuth','Redefinir autenticação do Google','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Estimado cliente,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;você foi solicitado recentemente a redefinir a autenticação do Google para isso, siga o link abaixo:<p> <a href=\"{link}\">{link}</a> <br><br><br> </td></tr></font></table></body>','2020-02-05 19:03:07','yes',5),(50,'reset_googleAuth','Réinitialiser lauthentification Google','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">cher client,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;on vous a récemment demandé de réinitialiser lauthentification Google pour cela, veuillez suivre le lien ci-dessous:<p> <a href=\"{link}\">{link}</a> <br><br><br> </td></tr></font></table></body>','2020-02-05 19:03:07','yes',6),(51,'reset_googleAuth','Ripristina autenticazione Google','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Caro cliente,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;di recente ti viene chiesto di ripristinare lautenticazione di Google per questo segui il link qui sotto:<p> <a href=\"{link}\">{link}</a> <br><br><br> </td></tr></font></table></body>','2020-02-05 19:03:07','yes',7),(52,'reset_googleAuth','Google Kimlik Doğrulamasını Sıfırla','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">değerli müşterimiz,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kısa süre önce sizden bunun için Google Kimlik Doğrulamasını sıfırlamanız isteniyor, lütfen aşağıdaki bağlantıyı izleyin:<p> <a href=\"{link}\">{link}</a> <br><br><br> </td></tr></font></table></body>','2020-02-05 19:03:07','yes',8),(53,'reset_googleAuth','Zresetuj uwierzytelnianie Google','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">drogi Kliencie,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ostatnio zostałeś poproszony o zresetowanie Uwierzytelnienia Google w tym celu, kliknij poniższy link:<p> <a href=\"{link}\">{link}</a> <br><br><br> </td></tr></font></table></body>','2020-02-05 19:03:07','yes',9),(54,'reset_googleAuth','إعادة ضبط مصادقة Google','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">عزيزي العميل,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;تمت مطالبتك مؤخرًا بإعادة تعيين مصادقة Google لذلك يرجى اتباع الرابط أدناه:<p> <a href=\"{link}\">{link}</a> <br><br><br> </td></tr></font></table></body>','2020-02-05 19:03:07','yes',10),(55,'reset_googleAuth','Сбросить аутентификацию Google','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Уважаемый клиент,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Вы недавно запросили сброс Google Authentication для этого, пожалуйста, перейдите по ссылке ниже:<p> <a href=\"{link}\">{link}</a> <br><br><br> </td></tr></font></table></body>','2020-02-05 19:03:07','yes',11),(56,'forgot_transaction_password','Forgot Transaction Password','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Dear Customer,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;You have recently requested to change your Transaction password. Follow the link below to reset the Transaction password<p> <a href=\"{link}\">{link}</a> <br><br><br></font> </td></tr></table></body>','2020-02-05 19:03:07','yes',1),(57,'forgot_transaction_password','Olvidé mi contraseña de transacción','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">estimado cliente,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Recientemente solicitó cambiar la contraseña de su Transacción. Siga el enlace a continuación para restablecer la contraseña de la transacción<p> <a href=\"{link}\">{link}</a> <br><br><br></font> </td></tr></table></body>','2020-02-05 19:03:07','yes',2),(58,'forgot_transaction_password','忘记交易密码','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">尊敬的客户,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;您最近已请求更改您的交易密码。请点击以下链接重设交易密码<p> <a href=\"{link}\">{link}</a> <br><br><br></font> </td></tr></table></body>','2020-02-05 19:03:07','yes',3),(59,'forgot_transaction_password','Transaktionspasswort vergessen','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Sehr geehrter Kunde,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sie haben kürzlich aufgefordert, Ihr Transaktionskennwort zu ändern. Folgen Sie dem Link unten, um das Transaktionskennwort zurückzusetzen<p> <a href=\"{link}\">{link}</a> <br><br><br></font> </td></tr></table></body>','2020-02-05 19:03:07','yes',4),(60,'forgot_transaction_password','Esqueceu a senha da transação','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Estimado cliente,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Você solicitou recentemente a alteração da senha da transação. Siga o link abaixo para redefinir a senha da transação<p> <a href=\"{link}\">{link}</a> <br><br><br></font> </td></tr></table></body>','2020-02-05 19:03:07','yes',5),(61,'forgot_transaction_password','Mot de passe de transaction oublié','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">cher client,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vous avez récemment demandé de modifier votre mot de passe de transaction. Suivez le lien ci-dessous pour réinitialiser le mot de passe de transaction<p> <a href=\"{link}\">{link}</a> <br><br><br></font> </td></tr></table></body>','2020-02-05 19:03:07','yes',6),(62,'forgot_transaction_password','Hai dimenticato la password della transazione','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Caro cliente,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Di recente hai richiesto di modificare la password della transazione. Segui il link seguente per reimpostare la password della transazione<p> <a href=\"{link}\">{link}</a> <br><br><br></font> </td></tr></table></body>','2020-02-05 19:03:07','yes',7),(63,'forgot_transaction_password','İşlem Şifresini Unuttum','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">değerli müşterimiz,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kısa bir süre önce İşlem şifrenizi değiştirmek istediniz. İşlem şifresini sıfırlamak için aşağıdaki bağlantıyı izleyin<p> <a href=\"{link}\">{link}</a> <br><br><br></font> </td></tr></table></body>','2020-02-05 19:03:07','yes',8),(64,'forgot_transaction_password','Zapomniałem hasła transakcji','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">drogi Kliencie,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ostatnio poprosiłeś o zmianę hasła do transakcji. Kliknij poniższy link, aby zresetować hasło transakcji<p> <a href=\"{link}\">{link}</a> <br><br><br></font> </td></tr></table></body>','2020-02-05 19:03:07','yes',9),(65,'forgot_transaction_password','نسيت كلمة المرور','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">عزيزي العميل,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;لقد طلبت مؤخرًا تغيير كلمة المرور الخاصة بالمعاملات. اتبع الرابط أدناه لإعادة تعيين كلمة مرور المعاملة<p> <a href=\"{link}\">{link}</a> <br><br><br></font> </td></tr></table></body>','2020-02-05 19:03:07','yes',10),(66,'forgot_transaction_password','Забыли пароль транзакции','<body><table border=\"0\" width=\"800\" height=\"700\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><br><br><br><font size=\"3\" face=\"Trebuchet MS\">Уважаемый клиент,</b><br> <p syte=\"pading-left:20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Вы недавно запросили сменить пароль транзакции. Перейдите по ссылке ниже, чтобы сбросить пароль транзакции<p> <a href=\"{link}\">{link}</a> <br><br><br></font> </td></tr></table></body>','2020-02-05 19:03:07','yes',11),(67,'external_mail','','<div class=\"banner\" style=\"background: url({banner_img}); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\"></div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px;\"> <h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">Subject: <span style=\"font-weight:bold;\">{subject}</span></h1><table border=\"0\" width=\"800\" align=\"center\"><tr><td colspan=\"4\"valign=\"top\" ><br><font size=\"3\" face=\"Trebuchet MS\"><p syte=\"pading-left:20px;\"><b>Message,</b></p> <p syte=\"pading-left:40px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{content}</p> <br> </td></tr></font></table></div>','2020-02-05 19:03:07','yes',1),(68,'change_password','Change Password ','<div class=\"banner\" style=\"background:url({banner_img});height: 58px;color: #fff;font-size: 21px;padding: 43px 20px 20px 40px;\">Password changed successfully!!!</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"><h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">Dear <span style=\"font-weight:bold;\">{full_name}</span></h1><p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;Your password has been sucessfully changed, Your new password is {new_password}</p></div>','2020-02-05 19:03:07','yes',1),(69,'change_password','Cambia la contraseña','<div class=\"banner\" style=\"background:url({banner_img});height: 58px;color: #fff;font-size: 21px;padding: 43px 20px 20px 40px;\">Contraseña cambiada con éxito!!!</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"><h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">querido <span style=\"font-weight:bold;\">{full_name}</span></h1><p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;Su contraseña ha sido cambiada con éxito, su nueva contraseña es {new_password}</p></div>','2020-02-05 19:03:07','yes',2),(70,'change_password','亲','<div class=\"banner\" style=\"background:url({banner_img});height: 58px;color: #fff;font-size: 21px;padding: 43px 20px 20px 40px;\">密码修改成功!!!</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"><h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">亲 <span style=\"font-weight:bold;\">{full_name}</span></h1><p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;您的密码已成功更改，您的新密码是 {new_password}</p></div>','2020-02-05 19:03:07','yes',3),(71,'change_password','Passwort ändern ','<div class=\"banner\" style=\"background:url({banner_img});height: 58px;color: #fff;font-size: 21px;padding: 43px 20px 20px 40px;\">das Passwort wurde erfolgreich geändert!!!</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"><h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">sehr geehrter <span style=\"font-weight:bold;\">{full_name}</span></h1><p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;hr Passwort wurde erfolgreich geändert. Ihr neues Passwort lautet {new_password}</p></div>','2020-02-05 19:03:07','yes',4),(72,'change_password','Mudar senha','<div class=\"banner\" style=\"background:url({banner_img});height: 58px;color: #fff;font-size: 21px;padding: 43px 20px 20px 40px;\">Senha alterada com sucesso!!!</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"><h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">caro <span style=\"font-weight:bold;\">{full_name}</span></h1><p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;Sua senha foi alterada com sucesso. Sua nova senha é {new_password}</p></div>','2020-02-05 19:03:07','yes',5),(73,'change_password','Changer le mot de passe ','<div class=\"banner\" style=\"background:url({banner_img});height: 58px;color: #fff;font-size: 21px;padding: 43px 20px 20px 40px;\">Le mot de passe a été changé avec succès!!!</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"><h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">chère <span style=\"font-weight:bold;\">{full_name}</span></h1><p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;Votre mot de passe a été changé avec succès, votre nouveau mot de passe est{new_password}</p></div>','2020-02-05 19:03:07','yes',6),(74,'change_password','Cambia la password','<div class=\"banner\" style=\"background:url({banner_img});height: 58px;color: #fff;font-size: 21px;padding: 43px 20px 20px 40px;\">password cambiata con successo!!!</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"><h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">caro <span style=\"font-weight:bold;\">{full_name}</span></h1><p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;La tua password è stata cambiata correttamente, la tua nuova password è {new_password}</p></div>','2020-02-05 19:03:07','yes',7),(75,'change_password','Şifre değiştir','<div class=\"banner\" style=\"background:url({banner_img});height: 58px;color: #fff;font-size: 21px;padding: 43px 20px 20px 40px;\">parola başarıyla değiştirildi!!!</div><div class=\"body_text\" style=\"padding:25px 65px 25px 45px; color:#333333;\"><h1 style=\"font-size:18px; color:#333333; font-weight: normal; font-weight: 300;\">Sayın <span style=\"font-weight:bold;\">{full_name}</span></h1><p style=\"font-size: 14px; line-height: 27px;\">&emsp; &emsp;Parolanız başarıyla değiştirildi, yeni parolanız {new_password}</p></div>','2020-02-05 19:03:07','yes',8),(76,'change_password','Zmień hasło','<div style=\"background: #6799ff;width: 100%;box-sizing: border-box; text-align: center;padding:20px 10px 20px 10px\">\r\n                \r\n                <h3 style=\"color: #fff;    margin-bottom: .5rem;line-height: 1.2;margin-top: 0;font-size: 1.4em;font-weight:400;box-sizing: border-box;\">Hasło zostało zmienione!!!</h3>\r\n            </div>\r\n            <div style=\"background: #eee;padding:35px 30px 10px 30px;width: 100%;box-sizing: border-box;\">\r\n                <h5 style=\"margin-bottom: .5rem;    margin-top: 0;line-height: 1.2;font-size: 1.2em;font-weight: 500;box-sizing: border-box;\">Drogi {full_name}</h5>\r\n                <p style=\"font-size:1.05em;box-sizing: border-box;    line-height: 1.5;\">  Twoje hasło zostało pomyślnie zmienione, Twoje nowe hasło to {new_password} </p>\r\n            </div>','2020-02-05 19:03:07','yes',9),(77,'change_password','تغيير كلمة السر','<div style=\"background: #6799ff;width: 100%;box-sizing: border-box; text-align: center;padding:20px 10px 20px 10px\">\r\n                \r\n                <h3 style=\"color: #fff;    margin-bottom: .5rem;line-height: 1.2;margin-top: 0;font-size: 1.4em;font-weight:400;box-sizing: border-box;\">تم تغيير الرقم السري بنجاح!!!</h3>\r\n            </div>\r\n            <div style=\"background: #eee;padding:35px 30px 10px 30px;width: 100%;box-sizing: border-box;\">\r\n                <h5 style=\"margin-bottom: .5rem;    margin-top: 0;line-height: 1.2;font-size: 1.2em;font-weight: 500;box-sizing: border-box;\">العزيز {full_name}</h5>\r\n                <p style=\"font-size:1.05em;box-sizing: border-box;    line-height: 1.5;\">  تم تغيير كلمة مرورك بنجاح ، كلمة مرورك الجديدة هي {new_password} </p>\r\n            </div>\r\n','2020-02-05 19:03:07','yes',10),(78,'change_password','Изменить пароль','<div style=\"background: #6799ff;width: 100%;box-sizing: border-box; text-align: center;padding:20px 10px 20px 10px\">\r\n                \r\n                <h3 style=\"color: #fff;    margin-bottom: .5rem;line-height: 1.2;margin-top: 0;font-size: 1.4em;font-weight:400;box-sizing: border-box;\">Пароль успешно изменен!!!</h3>\r\n            </div>\r\n            <div style=\"background: #eee;padding:35px 30px 10px 30px;width: 100%;box-sizing: border-box;\">\r\n                <h5 style=\"margin-bottom: .5rem;    margin-top: 0;line-height: 1.2;font-size: 1.2em;font-weight: 500;box-sizing: border-box;\">Уважаемые {full_name}</h5>\r\n                <p style=\"font-size:1.05em;box-sizing: border-box;    line-height: 1.5;\">  Ваш пароль был успешно изменен, Ваш новый пароль {new_password}  </p>\r\n            </div>','2020-02-05 19:03:07','yes',11),(79,'registration','Welcome to ','<p>Congratulations!!! You have been registered successfully!</p><h1>Dear <strong>{fullname},</strong></h1><p> Your MLM software is now active. Please save this message, so you will have a permanent record of your MLM Software. I trust that this mail finds you mutually excited about your new opportunity with {comapny_name}. Each of us will play a role to ensure your successful integration into the company. </p>','2019-10-28 02:21:59','yes',1),(80,'registration','Bienvenido a','<p>¡¡¡Felicidades!!! ¡Te has registrado con éxito!</p><h1>querido <strong>{fullname},</strong></h1><p> Su software MLM ahora está activo. Guarde este mensaje para tener un registro permanente de su software MLM. Confío en que este correo te encuentre mutuamente entusiasmado con tu nueva oportunidad con {company_name}. Cada uno de nosotros desempeñará un papel para garantizar su integración exitosa en la empresa.</p>','2019-10-28 02:21:59','yes',2),(81,'registration','亲 ','<p> 恭喜！！！您已成功注册！</p><h1>Dear <strong>{fullname},</strong></h1><p>您的MLM软件现在处于活动状态。请保存此消息，这样您将拥有MLM软件的永久记录。我相信，这封邮件会让您对{company_name}带来的新机遇感到兴奋。我们每个人都将扮演确保您成功融入公司的角色。 </p>','2019-10-28 02:21:59','yes',3),(82,'registration','Willkommen zu','<p>Herzliche Glückwünsche!!! Sie wurden erfolgreich registriert!</p><h1>sehr geehrter <strong>{fullname},</strong></h1><p>Ihre MLM-Software ist jetzt aktiv. Bitte speichern Sie diese Nachricht, damit Sie eine permanente Aufzeichnung Ihrer MLM-Software haben. Ich vertraue darauf, dass Sie sich in dieser Mail gegenseitig über Ihre neue Gelegenheit mit {company_name} freuen. Jeder von uns wird eine Rolle spielen, um Ihre erfolgreiche Integration in das Unternehmen zu gewährleisten.</p>','2019-10-28 02:21:59','yes',4),(83,'registration','Bem-vindo ao','<p>Parabéns!!! Você foi registrado com sucesso!</p><h1>querida <strong>{fullname},</strong></h1><p>Seu software MLM agora está ativo. Salve esta mensagem para ter um registro permanente do seu software MLM. Confio que este e-mail o ache mutuamente empolgado com sua nova oportunidade com {company_name}. Cada um de nós desempenhará um papel para garantir sua integração bem-sucedida na empresa. </p>','2019-10-28 02:21:59','yes',5),(84,'registration','Bienvenue à','<p>Toutes nos félicitations!!! Vous avez été enregistré avec succès!</p><h1>cher <strong>{fullname},</strong></h1><p> Votre logiciel MLM est maintenant actif. Veuillez enregistrer ce message afin que vous ayez un enregistrement permanent de votre logiciel MLM. J&#39;espère que ce courrier vous trouvera mutuellement enthousiasmé par votre nouvelle opportunité avec {company_name}. Chacun de nous jouera un rôle pour assurer votre intégration réussie dans la société.</p>','2019-10-28 02:21:59','yes',6),(85,'registration','Benvenuto a','<p>Congratulazioni!!! Sei stato registrato con successo!</p><h1>caro <strong>{fullname},</strong></h1><p>Il tuo software MLM è ora attivo. Salva questo messaggio, in modo da avere una registrazione permanente del tuo software MLM. Confido che questa mail ti trovi reciprocamente entusiasta della tua nuova opportunità con {company_name}. Ognuno di noi svolgerà un ruolo per garantire la corretta integrazione nella società.</p>','2019-10-28 02:21:59','yes',7),(86,'registration','Hoşgeldiniz','<p>Tebrikler !!! Başarıyla kaydoldunuz!</p><h1>Sayın <strong>{fullname},</strong></h1><p>MLM yazılımınız şimdi etkin. Lütfen bu mesajı kaydedin, böylece MLM Yazılımınızın kalıcı bir kaydına sahip olacaksınız. Bu mailin, {company_name} ile yeni fırsatınız için karşılıklı olarak sizi heyecanlandıracağına inanıyorum. Her birimiz şirkete başarılı bir şekilde entegrasyonunuzu sağlamada rol oynayacağız.</p>','2019-10-28 02:21:59','yes',8),(87,'registration','Witamy w','<p>Gratulacje!!! Zostałeś pomyślnie zarejestrowany!</p><h1>Drogi <strong>{fullname},</strong></h1><p>Twoje oprogramowanie MLM jest teraz aktywne. Proszę zapisać tę wiadomość, aby mieć stały zapis oprogramowania MLM. Ufam, że ta wiadomość obawia cię wzajemnie podekscytowana nową szansą na {company_name}. Każdy z nas odegra pewną rolę w zapewnieniu udanej integracji z firmą. </p>','2019-10-28 02:21:59','yes',9),(88,'registration','مرحبا بك في','<p>تهانينا!!! لقد تم تسجيلك بنجاح!</p><h1>Dear <strong>{fullname},</strong></h1><p> برنامج الامتيازات الخاص بك نشط الآن. يرجى حفظ هذه الرسالة ، لذلك سيكون لديك سجل دائم لبرامج الامتيازات الخاصة بك. أثق في أن هذا البريد يجدك متحمسًا تجاه فرصتك الجديدة مع {company_name}. سوف يلعب كل منا دورًا لضمان اندماجك الناجح في الشركة. </p>','2019-10-28 02:21:59','yes',10),(89,'registration','Добро пожаловать в','<p>Поздравляем !!! Вы были успешно зарегистрированы!</p><h1>дорогая <strong>{fullname},</strong></h1><p> Ваше программное обеспечение MLM теперь активно. Пожалуйста, сохраните это сообщение, чтобы иметь постоянную запись вашего программного обеспечения MLM. Я верю, что это письмо находит вас взаимно взволнованным вашей новой возможностью с {company_name}. Каждый из нас сыграет свою роль в обеспечении вашей успешной интеграции в компанию.</p>','2019-10-28 02:21:59','yes',11),(90,'payout_release','Payout Release Mail','<div class=\"banner\" style=\"background: url(\"https://infinitemlmsoftware.com/backoffice/public_html/images/banners/banner.jpg\"); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\">Payout released successfully!!!</div>\n<div class=\"body_text\" style=\"padding: 25px 65px 25px 45px;\">\n<h1 style=\"font-size: 18px; color: #333333; font-weight: bold;\">Dear {fullname},</h1>\n<p style=\"font-size: 14px; line-height: 27px; color: #000000;\">&emsp; &emsp; Your payout has been released successfully</p>\n</div>','2019-10-28 02:21:59','yes',1),(91,'payout_release','Payout Release Mail','<div class=\"banner\" style=\"background: url(\"https://infinitemlmsoftware.com/backoffice/public_html/images/banners/banner.jpg\"); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\">Pago lanzado con éxito!!!</div>\n<div class=\"body_text\" style=\"padding: 25px 65px 25px 45px;\">\n<h1 style=\"font-size: 18px; color: #333333; font-weight: bold;\">querido {fullname},</h1>\n<p style=\"font-size: 14px; line-height: 27px; color: #000000;\">&emsp; &emsp; Su pago ha sido liberado exitosamente</p>\n</div>','2019-10-28 02:21:59','yes',2),(92,'payout_release',' 付款发放邮件','<div class=\"banner\" style=\"background: url(\"https://infinitemlmsoftware.com/backoffice/public_html/images/banners/banner.jpg\"); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\">付款成功发布!!!</div>\n<div class=\"body_text\" style=\"padding: 25px 65px 25px 45px;\">\n<h1 style=\"font-size: 18px; color: #333333; font-weight: bold;\">亲 {fullname},</h1>\n<p style=\"font-size: 14px; line-height: 27px; color: #000000;\">&emsp; &emsp; 您的付款已成功释放</p>\n</div>','2019-10-28 02:21:59','yes',3),(93,'payout_release','Auszahlungsfreigabemail','<div class=\"banner\" style=\"background: url(\"https://infinitemlmsoftware.com/backoffice/public_html/images/banners/banner.jpg\"); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\">Auszahlung erfolgreich freigegeben!!!</div>\n<div class=\"body_text\" style=\"padding: 25px 65px 25px 45px;\">\n<h1 style=\"font-size: 18px; color: #333333; font-weight: bold;\">sehr geehrter {fullname},</h1>\n<p style=\"font-size: 14px; line-height: 27px; color: #000000;\">&emsp; &emsp; Ihre Auszahlung wurde erfolgreich freigegeben</p>\n</div>en released successfully</p>\n</div>','2019-10-28 02:21:59','yes',4),(94,'payout_release','Correio de liberação de pagamento','<div class=\"banner\" style=\"background: url(\"https://infinitemlmsoftware.com/backoffice/public_html/images/banners/banner.jpg\"); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\">Pagamento liberado com sucesso!!!</div>\n<div class=\"body_text\" style=\"padding: 25px 65px 25px 45px;\">\n<h1 style=\"font-size: 18px; color: #333333; font-weight: bold;\">querida {fullname},</h1>\n<p style=\"font-size: 14px; line-height: 27px; color: #000000;\">&emsp; &emsp; Seu pagamento foi liberado com sucesso</p>\n</div>','2019-10-28 02:21:59','yes',5),(95,'payout_release','Payout Release Mail','<div class=\"banner\" style=\"background: url(\"https://infinitemlmsoftware.com/backoffice/public_html/images/banners/banner.jpg\"); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\">Paiement terminé avec succès!!!</div>\n<div class=\"body_text\" style=\"padding: 25px 65px 25px 45px;\">\n<h1 style=\"font-size: 18px; color: #333333; font-weight: bold;\">cher {fullname},</h1>\n<p style=\"font-size: 14px; line-height: 27px; color: #000000;\">&emsp; &emsp; Votre paiement a été libéré avec succès</p>\n</div>','2019-10-28 02:21:59','yes',6),(96,'payout_release','Posta di rilascio del pagamento','<div class=\"banner\" style=\"background: url(\"https://infinitemlmsoftware.com/backoffice/public_html/images/banners/banner.jpg\"); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\"> Pagamento rilasciato correttamente!!!</div>\n<div class=\"body_text\" style=\"padding: 25px 65px 25px 45px;\">\n<h1 style=\"font-size: 18px; color: #333333; font-weight: bold;\">caro {fullname},</h1>\n<p style=\"font-size: 14px; line-height: 27px; color: #000000;\">&emsp; &emsp;  Il tuo pagamento è stato rilasciato correttamente</p>\n</div>','2019-10-28 02:21:59','yes',7),(97,'payout_release',' Ödeme Yapma Postası','<div class=\"banner\" style=\"background: url(\"https://infinitemlmsoftware.com/backoffice/public_html/images/banners/banner.jpg\"); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\">Ödeme başarıyla yayınlandı!!!</div>\n<div class=\"body_text\" style=\"padding: 25px 65px 25px 45px;\">\n<h1 style=\"font-size: 18px; color: #333333; font-weight: bold;\">Sayın {fullname},</h1>\n<p style=\"font-size: 14px; line-height: 27px; color: #000000;\">&emsp; &emsp; Ödemeniz başarıyla yayınlandı</p>\n</div>','2019-10-28 02:21:59','yes',8),(98,'payout_release','Poczta zwalniająca zapłatę','<div class=\"banner\" style=\"background: url(\"https://infinitemlmsoftware.com/backoffice/public_html/images/banners/banner.jpg\"); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\">Wypłata wypuszczona pomyślnie!!!</div>\n<div class=\"body_text\" style=\"padding: 25px 65px 25px 45px;\">\n<h1 style=\"font-size: 18px; color: #333333; font-weight: bold;\">Drogi {fullname},</h1>\n<p style=\"font-size: 14px; line-height: 27px; color: #000000;\">&emsp; &emsp; Twoja wypłata została pomyślnie zwolniona</p>\n</div>','2019-10-28 02:21:59','yes',9),(99,'payout_release',' دفع الافراج عن البريد','<div class=\"banner\" style=\"background: url(\"https://infinitemlmsoftware.com/backoffice/public_html/images/banners/banner.jpg\"); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\"> دفع تعويضات صدر بنجاح!!!</div>\n<div class=\"body_text\" style=\"padding: 25px 65px 25px 45px;\">\n<h1 style=\"font-size: 18px; color: #333333; font-weight: bold;\">العزيز {fullname},</h1>\n<p style=\"font-size: 14px; line-height: 27px; color: #000000;\">&emsp; &emsp; تم الافراج عن دفعتك بنجاح</p>\n</div>','2019-10-28 02:21:59','yes',10),(100,'payout_release','Payout Release Mail','<div class=\"banner\" style=\"background: url(\"https://infinitemlmsoftware.com/backoffice/public_html/images/banners/banner.jpg\"); height: 58px; color: #fff; font-size: 21px; padding: 43px 20px 20px 40px;\"> Выплата успешно завершена!!!</div>\n<div class=\"body_text\" style=\"padding: 25px 65px 25px 45px;\">\n<h1 style=\"font-size: 18px; color: #333333; font-weight: bold;\">дорогая {fullname},</h1>\n<p style=\"font-size: 14px; line-height: 27px; color: #000000;\">&emsp; &emsp;  Ваша выплата была успешно выпущена</p>\n</div>','2019-10-28 02:21:59','yes',11),(101,'Forgot_pswd','forgot password','<p>&nbsp;Dear {user_name}</p>\n<table id=\"Table_01\" border=\"0\" width=\"600\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td colspan=\"3\">&nbsp;</td>\n</tr>\n</tbody>\n</table>\n<table border=\"0\" width=\"60%\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td colspan=\"2\" align=\"center\"><strong>Your current password is :{password}</strong></td>\n</tr>\n<tr>\n<td colspan=\"2\">Thanking you,</td>\n</tr>\n<tr>\n<td colspan=\"2\">\n<p align=\"left\">{company_name}<br />Date:{date}<br />Place :{place}</p>\n</td>\n</tr>\n</tbody>\n</table>','2019-10-28 02:21:59','yes',0);
/*!40000 ALTER TABLE `39_common_mail_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_common_settings`
--

DROP TABLE IF EXISTS `39_common_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_common_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `logout_time` int(11) NOT NULL,
  `active` varchar(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_common_settings`
--

LOCK TABLES `39_common_settings` WRITE;
/*!40000 ALTER TABLE `39_common_settings` DISABLE KEYS */;
INSERT INTO `39_common_settings` VALUES (1,7200,'yes');
/*!40000 ALTER TABLE `39_common_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_compensations`
--

DROP TABLE IF EXISTS `39_compensations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_compensations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_commission_status` varchar(5) NOT NULL DEFAULT 'yes',
  `sponsor_commission_status` varchar(5) NOT NULL DEFAULT 'yes',
  `rank_commission_status` varchar(5) NOT NULL DEFAULT 'no',
  `referal_commission_status` varchar(5) NOT NULL DEFAULT 'no',
  `roi_commission_status` varchar(5) NOT NULL DEFAULT 'no',
  `matching_bonus` varchar(5) NOT NULL DEFAULT 'yes',
  `pool_bonus` varchar(5) NOT NULL DEFAULT 'yes',
  `fast_start_bonus` varchar(5) NOT NULL DEFAULT 'yes',
  `performance_bonus` varchar(5) NOT NULL DEFAULT 'yes',
  `sales_commission` varchar(3) NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_compensations`
--

LOCK TABLES `39_compensations` WRITE;
/*!40000 ALTER TABLE `39_compensations` DISABLE KEYS */;
INSERT INTO `39_compensations` VALUES (1,'yes','yes','yes','yes','no','no','yes','no','no','no');
/*!40000 ALTER TABLE `39_compensations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_configuration`
--

DROP TABLE IF EXISTS `39_configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tds` float NOT NULL DEFAULT '0',
  `pair_price` double NOT NULL DEFAULT '0',
  `pair_ceiling` int(11) NOT NULL DEFAULT '100',
  `pair_ceiling_type` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'daily',
  `service_charge` float NOT NULL DEFAULT '0',
  `product_point_value` int(11) NOT NULL DEFAULT '1',
  `pair_value` double NOT NULL DEFAULT '1',
  `payout_release` varchar(50) CHARACTER SET utf8 NOT NULL,
  `start_date` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'NA',
  `end_date` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'NA',
  `sms_status` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT 'enabled',
  `reg_amount` double NOT NULL DEFAULT '0',
  `referal_amount` double NOT NULL DEFAULT '0',
  `max_pincount` int(11) NOT NULL DEFAULT '0',
  `min_payout` double NOT NULL DEFAULT '100',
  `payout_request_validity` int(15) NOT NULL DEFAULT '30',
  `pair_commission_type` varchar(25) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `depth_ceiling` int(11) NOT NULL DEFAULT '0',
  `width_ceiling` int(11) NOT NULL DEFAULT '0',
  `level_commission_type` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT 'Percentage',
  `trans_fee` double NOT NULL DEFAULT '0',
  `max_payout` double NOT NULL DEFAULT '0',
  `override_commission` double NOT NULL DEFAULT '0',
  `profile_updation_history` int(100) NOT NULL DEFAULT '0',
  `donation_type` varchar(100) NOT NULL DEFAULT 'manuel',
  `xup_level` int(11) NOT NULL DEFAULT '1',
  `upload_config` int(11) NOT NULL DEFAULT '10',
  `pair_ceiling_monthly` double NOT NULL DEFAULT '0',
  `matching_bonus` varchar(5) NOT NULL DEFAULT 'yes',
  `pool_bonus` varchar(5) NOT NULL DEFAULT 'yes',
  `pool_bonus_percent` double NOT NULL DEFAULT '5',
  `fast_start_bonus` varchar(5) NOT NULL DEFAULT 'yes',
  `performance_bonus` varchar(5) NOT NULL DEFAULT 'yes',
  `sponsor_commission_type` varchar(20) NOT NULL DEFAULT 'sponsor_package',
  `purchase_income_perc` int(11) NOT NULL DEFAULT '10',
  `commission_criteria` varchar(12) NOT NULL DEFAULT 'genealogy' COMMENT 'genealogy,reg_pck,member_pck',
  `referal_commission_type` varchar(12) NOT NULL DEFAULT 'flat',
  `commission_upto_level` int(12) NOT NULL DEFAULT '3',
  `roi_period` varchar(50) NOT NULL DEFAULT 'daily',
  `roi_days_skip` text NOT NULL,
  `roi_criteria` varchar(50) NOT NULL DEFAULT 'member_pck',
  `skip_blocked_users_commission` varchar(20) NOT NULL DEFAULT 'yes',
  `pool_bonus_period` varchar(12) NOT NULL DEFAULT 'yearly' COMMENT 'monthly,quarterly,half_yearly,yearly',
  `pool_bonus_criteria` varchar(12) NOT NULL DEFAULT 'sales',
  `pool_distribution_criteria` varchar(12) NOT NULL DEFAULT 'equally',
  `matching_criteria` varchar(12) NOT NULL DEFAULT 'genealogy' COMMENT 'genealogy,member_pck',
  `matching_upto_level` int(12) NOT NULL DEFAULT '3',
  `sales_criteria` varchar(12) NOT NULL DEFAULT 'cv',
  `sales_type` varchar(12) NOT NULL DEFAULT 'genealogy',
  `sales_level` int(12) NOT NULL DEFAULT '3',
  `api_key` text NOT NULL,
  `payout_mail_status` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'NO',
  `payout_fee_amount` double NOT NULL DEFAULT '0',
  `payout_fee_mode` varchar(10) NOT NULL DEFAULT 'flat' COMMENT 'flat, percentage',
  `tree_icon_based` varchar(30) NOT NULL DEFAULT 'profile_image',
  `active_tree_icon` varchar(500) NOT NULL DEFAULT 'active.jpg',
  `inactive_tree_icon` varchar(500) NOT NULL DEFAULT 'inactive.png',
  `defualt_package_tree_icon` varchar(500) NOT NULL DEFAULT 'default_package.png',
  `defualt_rank_tree_icon` varchar(500) NOT NULL DEFAULT 'default_rank.png',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_configuration`
--

LOCK TABLES `39_configuration` WRITE;
/*!40000 ALTER TABLE `39_configuration` DISABLE KEYS */;
INSERT INTO `39_configuration` VALUES (1,0,100,100,'daily',0,100,100,'ewallet_request','Sunday','Saturday','enabled',100,25,500,10,30,'flat',3,2,'percentage',0,500,15,0,'manuel',2,10000,0,'no','yes',5,'no','no','sponsor_package',10,'genealogy','flat',3,'daily','Sat,Sun','member_pck','yes','yearly','sales','equally','genealogy',3,'cv','genealogy',3,'','NO',0,'flat','profile_image','active.jpg','inactive.png','default_package.png','default_rank.png');
/*!40000 ALTER TABLE `39_configuration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_configuration_change_history`
--

DROP TABLE IF EXISTS `39_configuration_change_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_configuration_change_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(200) NOT NULL,
  `done_by` int(11) NOT NULL,
  `done_by_type` varchar(50) NOT NULL,
  `activity` varchar(100) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_configuration_change_history`
--

LOCK TABLES `39_configuration_change_history` WRITE;
/*!40000 ALTER TABLE `39_configuration_change_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_configuration_change_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_contacts`
--

DROP TABLE IF EXISTS `39_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `contact_email` varchar(50) CHARACTER SET utf8 NOT NULL,
  `contact_address` varchar(200) CHARACTER SET utf8 NOT NULL,
  `contact_phone` varchar(250) CHARACTER SET utf8 NOT NULL,
  `contact_info` longtext,
  `owner_id` int(50) NOT NULL DEFAULT '0',
  `status` varchar(20) CHARACTER SET utf8 NOT NULL,
  `mailadiddate` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `read_msg` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `owner_id_status` (`owner_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_contacts`
--

LOCK TABLES `39_contacts` WRITE;
/*!40000 ALTER TABLE `39_contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_crm_followups`
--

DROP TABLE IF EXISTS `39_crm_followups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_crm_followups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lead_id` int(11) NOT NULL,
  `followup_entered_by` int(11) NOT NULL,
  `description` longtext NOT NULL,
  `file_name` varchar(25) DEFAULT NULL,
  `followup_date` datetime NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `followup_date` (`followup_date`),
  KEY `date` (`date`),
  KEY `lead_id` (`lead_id`),
  CONSTRAINT `39_crm_followups_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `39_crm_leads` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_crm_followups`
--

LOCK TABLES `39_crm_followups` WRITE;
/*!40000 ALTER TABLE `39_crm_followups` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_crm_followups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_crm_leads`
--

DROP TABLE IF EXISTS `39_crm_leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_crm_leads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lead_id` varchar(10) NOT NULL DEFAULT 'LEAD000000',
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `added_by` int(11) NOT NULL,
  `email_id` varchar(50) NOT NULL,
  `skype_id` varchar(50) NOT NULL,
  `mobile_no` varchar(15) DEFAULT NULL,
  `country` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `interest_status` varchar(20) NOT NULL,
  `followup_date` datetime NOT NULL,
  `lead_status` varchar(10) NOT NULL DEFAULT 'ongoing',
  `date` datetime NOT NULL,
  `confirmation_date` datetime DEFAULT NULL,
  `user_new` varchar(5) NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`),
  KEY `followup_date` (`followup_date`),
  KEY `lead_status` (`lead_status`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_crm_leads`
--

LOCK TABLES `39_crm_leads` WRITE;
/*!40000 ALTER TABLE `39_crm_leads` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_crm_leads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_cron_history`
--

DROP TABLE IF EXISTS `39_cron_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_cron_history` (
  `cron_id` int(11) NOT NULL AUTO_INCREMENT,
  `cron_name` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT 'NA',
  `cron_start_time` datetime NOT NULL DEFAULT '2016-06-02 00:00:00',
  `cron_end_time` datetime NOT NULL DEFAULT '2016-06-02 00:00:00',
  `cron_status` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT 'NA',
  PRIMARY KEY (`cron_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_cron_history`
--

LOCK TABLES `39_cron_history` WRITE;
/*!40000 ALTER TABLE `39_cron_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_cron_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_custom_field_details`
--

DROP TABLE IF EXISTS `39_custom_field_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_custom_field_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `field_name` varchar(50) NOT NULL,
  `field_value` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_custom_field_details`
--

LOCK TABLES `39_custom_field_details` WRITE;
/*!40000 ALTER TABLE `39_custom_field_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_custom_field_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_custom_fields`
--

DROP TABLE IF EXISTS `39_custom_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_custom_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `field_name` varchar(50) NOT NULL,
  `lang` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_custom_fields`
--

LOCK TABLES `39_custom_fields` WRITE;
/*!40000 ALTER TABLE `39_custom_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_custom_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_documents`
--

DROP TABLE IF EXISTS `39_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_documents` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `file_title` varchar(100) CHARACTER SET utf8 NOT NULL,
  `doc_file_name` text CHARACTER SET utf8 NOT NULL,
  `uploaded_date` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `doc_desc` longtext,
  `ctgry` int(20) NOT NULL,
  `read_status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uploaded_date` (`uploaded_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_documents`
--

LOCK TABLES `39_documents` WRITE;
/*!40000 ALTER TABLE `39_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_downline_rank`
--

DROP TABLE IF EXISTS `39_downline_rank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_downline_rank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rank_id` int(11) NOT NULL,
  `downline_rank_id` int(11) NOT NULL,
  `rank_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_downline_rank`
--

LOCK TABLES `39_downline_rank` WRITE;
/*!40000 ALTER TABLE `39_downline_rank` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_downline_rank` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_epin_transfer_history`
--

DROP TABLE IF EXISTS `39_epin_transfer_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_epin_transfer_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `ip` varchar(11) NOT NULL,
  `done_by` int(11) DEFAULT NULL,
  `done_by_type` varchar(100) DEFAULT 'admin',
  `date` datetime NOT NULL,
  `activity` varchar(400) DEFAULT NULL,
  `data` text,
  `from_user_id` int(11) unsigned NOT NULL,
  `epin_id` int(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `user_id` (`user_id`),
  KEY `from_user_id` (`from_user_id`),
  KEY `epin_id` (`epin_id`),
  CONSTRAINT `39_epin_transfer_history_ibfk_22` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_epin_transfer_history_ibfk_23` FOREIGN KEY (`from_user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_epin_transfer_history_ibfk_24` FOREIGN KEY (`epin_id`) REFERENCES `39_pin_numbers` (`pin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_epin_transfer_history`
--

LOCK TABLES `39_epin_transfer_history` WRITE;
/*!40000 ALTER TABLE `39_epin_transfer_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_epin_transfer_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_ewallet_history`
--

DROP TABLE IF EXISTS `39_ewallet_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_ewallet_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `from_id` int(11) unsigned DEFAULT NULL,
  `ewallet_id` int(11) NOT NULL,
  `ewallet_type` varchar(32) NOT NULL,
  `amount` double NOT NULL,
  `purchase_wallet` double NOT NULL DEFAULT '0',
  `amount_type` varchar(32) NOT NULL,
  `type` varchar(32) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `transaction_id` varchar(50) NOT NULL DEFAULT '',
  `transaction_note` varchar(100) NOT NULL DEFAULT '',
  `transaction_fee` double NOT NULL DEFAULT '0',
  `pending_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date_added` (`date_added`),
  KEY `user_id` (`user_id`),
  KEY `from_id` (`from_id`),
  KEY `pending_id` (`pending_id`),
  CONSTRAINT `39_ewallet_history_ibfk_22` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_ewallet_history_ibfk_23` FOREIGN KEY (`from_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_ewallet_history_ibfk_24` FOREIGN KEY (`pending_id`) REFERENCES `39_pending_registration` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_ewallet_history`
--

LOCK TABLES `39_ewallet_history` WRITE;
/*!40000 ALTER TABLE `39_ewallet_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_ewallet_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_ewallet_payment_details`
--

DROP TABLE IF EXISTS `39_ewallet_payment_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_ewallet_payment_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `used_user_id` int(11) unsigned DEFAULT NULL,
  `used_amount` double NOT NULL DEFAULT '0',
  `used_for` varchar(20) CHARACTER SET utf8 NOT NULL,
  `date` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `transaction_id` varchar(100) NOT NULL DEFAULT '0',
  `pending_id` int(11) DEFAULT NULL,
  `pay_type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `used_user_id` (`used_user_id`),
  KEY `pending_id` (`pending_id`),
  CONSTRAINT `39_ewallet_payment_details_ibfk_22` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_ewallet_payment_details_ibfk_23` FOREIGN KEY (`used_user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_ewallet_payment_details_ibfk_24` FOREIGN KEY (`pending_id`) REFERENCES `39_pending_registration` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_ewallet_payment_details`
--

LOCK TABLES `39_ewallet_payment_details` WRITE;
/*!40000 ALTER TABLE `39_ewallet_payment_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_ewallet_payment_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_fast_start_bonus`
--

DROP TABLE IF EXISTS `39_fast_start_bonus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_fast_start_bonus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referral_count` int(11) NOT NULL,
  `days_count` int(11) NOT NULL,
  `bonus_amount` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_fast_start_bonus`
--

LOCK TABLES `39_fast_start_bonus` WRITE;
/*!40000 ALTER TABLE `39_fast_start_bonus` DISABLE KEYS */;
INSERT INTO `39_fast_start_bonus` VALUES (1,15,5,8);
/*!40000 ALTER TABLE `39_fast_start_bonus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_feedback`
--

DROP TABLE IF EXISTS `39_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_feedback` (
  `feedback_id` int(11) NOT NULL AUTO_INCREMENT,
  `feedback_user_id` int(11) unsigned NOT NULL,
  `feedback_company` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `feedback_email` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `feedback_phone` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `feedback_time` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `feedback_remark` longtext,
  `feedback_date` date NOT NULL DEFAULT '0001-01-01',
  `read_status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`feedback_id`),
  KEY `feedback_date` (`feedback_date`),
  KEY `feedback_user_id` (`feedback_user_id`),
  CONSTRAINT `39_feedback_ibfk_1` FOREIGN KEY (`feedback_user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_feedback`
--

LOCK TABLES `39_feedback` WRITE;
/*!40000 ALTER TABLE `39_feedback` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_forget_request`
--

DROP TABLE IF EXISTS `39_forget_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_forget_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'no',
  `request_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_forget_request`
--

LOCK TABLES `39_forget_request` WRITE;
/*!40000 ALTER TABLE `39_forget_request` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_forget_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_ft_individual`
--

DROP TABLE IF EXISTS `39_ft_individual`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_ft_individual` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `oc_customer_ref_id` int(11) NOT NULL DEFAULT '0',
  `order_id` int(20) NOT NULL DEFAULT '0',
  `user_type` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT 'user',
  `user_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `password` varchar(300) DEFAULT NULL,
  `user_rank_id` int(11) DEFAULT NULL,
  `active` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `position` char(1) CHARACTER SET utf8 NOT NULL,
  `leg_position` int(11) NOT NULL DEFAULT '0',
  `father_id` int(11) unsigned DEFAULT NULL,
  `sponsor_id` int(11) unsigned DEFAULT NULL,
  `first_pair` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `total_leg` double NOT NULL DEFAULT '0',
  `total_left_carry` double NOT NULL DEFAULT '0',
  `total_right_carry` double NOT NULL DEFAULT '0',
  `product_id` varchar(50) NOT NULL DEFAULT '',
  `product_validity` datetime DEFAULT NULL,
  `date_of_joining` datetime DEFAULT NULL,
  `user_level` int(20) NOT NULL DEFAULT '0',
  `sponsor_level` int(20) NOT NULL DEFAULT '0',
  `register_by_using` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT 'NA',
  `api_key` varchar(100) NOT NULL DEFAULT '0',
  `default_lang` int(11) NOT NULL DEFAULT '1',
  `default_currency` int(11) DEFAULT NULL,
  `personal_pv` varchar(50) NOT NULL DEFAULT '0',
  `gpv` varchar(50) NOT NULL DEFAULT '0',
  `binary_leg` varchar(50) NOT NULL DEFAULT 'any',
  `goc_key` varchar(50) DEFAULT NULL,
  `inf_token` varchar(15) NOT NULL,
  `force_logout` int(11) NOT NULL DEFAULT '0',
  `google_auth_status` varchar(10) NOT NULL DEFAULT 'no',
  `delete_status` varchar(10) NOT NULL DEFAULT 'active' COMMENT 'deleted | active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `first` (`user_name`),
  UNIQUE KEY `unique_position_father_id` (`position`,`father_id`),
  KEY `active` (`active`),
  KEY `position` (`position`),
  KEY `date_of_joining` (`date_of_joining`),
  KEY `user_rank_id` (`user_rank_id`),
  KEY `father_id` (`father_id`),
  KEY `sponsor_id` (`sponsor_id`),
  CONSTRAINT `39_ft_individual_ibfk_22` FOREIGN KEY (`user_rank_id`) REFERENCES `39_rank_details` (`rank_id`),
  CONSTRAINT `39_ft_individual_ibfk_23` FOREIGN KEY (`father_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_ft_individual_ibfk_24` FOREIGN KEY (`sponsor_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_ft_individual`
--

LOCK TABLES `39_ft_individual` WRITE;
/*!40000 ALTER TABLE `39_ft_individual` DISABLE KEYS */;
INSERT INTO `39_ft_individual` VALUES (32,0,0,'admin','majed','$2y$10$xB5N1d98xv3BoLWKPnJBOumrUHOvfShyQPbAe1ya/JF0jl0DHtula',NULL,'yes','0',0,NULL,NULL,'0',0,0,0,'pck1',NULL,'2021-05-26 17:45:53',0,0,'NA','0',1,NULL,'0','0','any',NULL,'',0,'no','active');
/*!40000 ALTER TABLE `39_ft_individual` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_fund_transfer_details`
--

DROP TABLE IF EXISTS `39_fund_transfer_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_fund_transfer_details` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `from_user_id` int(11) unsigned NOT NULL,
  `to_user_id` int(11) unsigned NOT NULL,
  `amount` double NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `amount_type` varchar(20) CHARACTER SET utf8 NOT NULL,
  `transaction_concept` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `trans_fee` double NOT NULL DEFAULT '0',
  `transaction_id` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `from_user_id` (`from_user_id`),
  KEY `to_user_id` (`to_user_id`),
  CONSTRAINT `39_fund_transfer_details_ibfk_15` FOREIGN KEY (`from_user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_fund_transfer_details_ibfk_16` FOREIGN KEY (`to_user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_fund_transfer_details`
--

LOCK TABLES `39_fund_transfer_details` WRITE;
/*!40000 ALTER TABLE `39_fund_transfer_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_fund_transfer_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_infinite_countries`
--

DROP TABLE IF EXISTS `39_infinite_countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_infinite_countries` (
  `country_id` int(11) NOT NULL AUTO_INCREMENT,
  `country_name` varchar(128) NOT NULL DEFAULT 'NA',
  `country_code` varchar(2) NOT NULL DEFAULT 'NA',
  `phone_code` int(11) NOT NULL DEFAULT '0',
  `iso_code_3` varchar(3) NOT NULL DEFAULT 'NA',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`country_id`),
  KEY `country_name` (`country_name`)
) ENGINE=InnoDB AUTO_INCREMENT=252 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_infinite_countries`
--

LOCK TABLES `39_infinite_countries` WRITE;
/*!40000 ALTER TABLE `39_infinite_countries` DISABLE KEYS */;
INSERT INTO `39_infinite_countries` VALUES (1,'Afghanistan','AF',93,'AFG',1),(2,'Albania','AL',355,'ALB',1),(3,'Algeria','DZ',213,'DZA',1),(4,'American Samoa','AS',1,'ASM',1),(5,'Andorra','AD',376,'AND',1),(6,'Angola','AO',244,'AGO',1),(7,'Anguilla','AI',1,'AIA',1),(8,'Antarctica','AQ',672,'ATA',1),(9,'Antigua and Barbuda','AG',1,'ATG',1),(10,'Argentina','AR',54,'ARG',1),(11,'Armenia','AM',374,'ARM',1),(12,'Aruba','AW',297,'ABW',1),(13,'Australia','AU',61,'AUS',1),(14,'Austria','AT',43,'AUT',1),(15,'Azerbaijan','AZ',994,'AZE',1),(16,'Bahamas','BS',1,'BHS',1),(17,'Bahrain','BH',973,'BHR',1),(18,'Bangladesh','BD',880,'BGD',1),(19,'Barbados','BB',1,'BRB',1),(20,'Belarus','BY',375,'BLR',1),(21,'Belgium','BE',32,'BEL',1),(22,'Belize','BZ',501,'BLZ',1),(23,'Benin','BJ',229,'BEN',1),(24,'Bermuda','BM',1,'BMU',1),(25,'Bhutan','BT',975,'BTN',1),(26,'Bolivia','BO',591,'BOL',1),(27,'Bosnia and Herzegovina','BA',387,'BIH',1),(28,'Botswana','BW',267,'BWA',1),(29,'Bouvet Island','BV',20,'BVT',1),(30,'Brazil','BR',55,'BRA',1),(31,'British Indian Ocean Territory','IO',20,'IOT',1),(32,'Brunei Darussalam','BN',673,'BRN',1),(33,'Bulgaria','BG',359,'BGR',1),(34,'Burkina Faso','BF',226,'BFA',1),(35,'Burundi','BI',257,'BDI',1),(36,'Cambodia','KH',855,'KHM',1),(37,'Cameroon','CM',237,'CMR',1),(38,'Canada','CA',1,'CAN',1),(39,'Cape Verde','CV',238,'CPV',1),(40,'Cayman Islands','KY',1,'CYM',1),(41,'Central African Republic','CF',236,'CAF',1),(42,'Chad','TD',235,'TCD',1),(43,'Chile','CL',56,'CHL',1),(44,'China','CN',86,'CHN',1),(45,'Christmas Island','CX',61,'CXR',1),(46,'Cocos (Keeling) Islands','CC',61,'CCK',1),(47,'Colombia','CO',57,'COL',1),(48,'Comoros','KM',269,'COM',1),(49,'Congo','CG',242,'COG',1),(50,'Cook Islands','CK',682,'COK',1),(51,'Costa Rica','CR',506,'CRI',1),(52,'Cote D\'Ivoire','CI',225,'CIV',1),(53,'Croatia','HR',385,'HRV',1),(54,'Cuba','CU',53,'CUB',1),(55,'Cyprus','CY',357,'CYP',1),(56,'Czech Republic','CZ',420,'CZE',1),(57,'Denmark','DK',45,'DNK',1),(58,'Djibouti','DJ',253,'DJI',1),(59,'Dominica','DM',1,'DMA',1),(60,'Dominican Republic','DO',1,'DOM',1),(61,'East Timor','TL',670,'TLS',1),(62,'Ecuador','EC',593,'ECU',1),(63,'Egypt','EG',20,'EGY',1),(64,'El Salvador','SV',503,'SLV',1),(65,'Equatorial Guinea','GQ',240,'GNQ',1),(66,'Eritrea','ER',291,'ERI',1),(67,'Estonia','EE',372,'EST',1),(68,'Ethiopia','ET',251,'ETH',1),(69,'Falkland Islands (Malvinas)','FK',500,'FLK',1),(70,'Faroe Islands','FO',298,'FRO',1),(71,'Fiji','FJ',679,'FJI',1),(72,'Finland','FI',358,'FIN',1),(74,'France, Metropolitan','FR',0,'FRA',1),(75,'French Guiana','GF',224,'GUF',1),(76,'French Polynesia','PF',689,'PYF',1),(77,'French Southern Territories','TF',262,'ATF',1),(78,'Gabon','GA',241,'GAB',1),(79,'Gambia','GM',220,'GMB',1),(80,'Georgia','GE',995,'GEO',1),(81,'Germany','DE',49,'DEU',1),(82,'Ghana','GH',233,'GHA',1),(83,'Gibraltar','GI',350,'GIB',1),(84,'Greece','GR',30,'GRC',1),(85,'Greenland','GL',299,'GRL',1),(86,'Grenada','GD',1,'GRD',1),(87,'Guadeloupe','GP',20,'GLP',1),(88,'Guam','GU',1,'GUM',1),(89,'Guatemala','GT',502,'GTM',1),(90,'Guinea','GN',224,'GIN',1),(91,'Guinea-Bissau','GW',245,'GNB',1),(92,'Guyana','GY',592,'GUY',1),(93,'Haiti','HT',509,'HTI',1),(94,'Heard and Mc Donald Islands','HM',0,'HMD',1),(95,'Honduras','HN',504,'HND',1),(96,'Hong Kong','HK',852,'HKG',1),(97,'Hungary','HU',36,'HUN',1),(98,'Iceland','IS',354,'ISL',1),(99,'India','IN',91,'IND',1),(100,'Indonesia','ID',62,'IDN',1),(101,'Iran (Islamic Republic of)','IR',98,'IRN',1),(102,'Iraq','IQ',964,'IRQ',1),(103,'Ireland','IE',353,'IRL',1),(104,'Israel','IL',972,'ISR',1),(105,'Italy','IT',39,'ITA',1),(106,'Jamaica','JM',1,'JAM',1),(107,'Japan','JP',81,'JPN',1),(108,'Jordan','JO',962,'JOR',1),(109,'Kazakhstan','KZ',7,'KAZ',1),(110,'Kenya','KE',254,'KEN',1),(111,'Kiribati','KI',686,'KIR',1),(112,'North Korea','KP',850,'PRK',1),(113,'Korea, Republic of','KR',82,'KOR',1),(114,'Kuwait','KW',965,'KWT',1),(115,'Kyrgyzstan','KG',996,'KGZ',1),(116,'Lao People\'s Democratic Republic','LA',856,'LAO',1),(117,'Latvia','LV',371,'LVA',1),(118,'Lebanon','LB',961,'LBN',1),(119,'Lesotho','LS',266,'LSO',1),(120,'Liberia','LR',231,'LBR',1),(121,'Libyan Arab Jamahiriya','LY',218,'LBY',1),(122,'Liechtenstein','LI',423,'LIE',1),(123,'Lithuania','LT',370,'LTU',1),(124,'Luxembourg','LU',352,'LUX',1),(125,'Macau','MO',853,'MAC',1),(126,'FYROM','MK',389,'MKD',1),(127,'Madagascar','MG',261,'MDG',1),(128,'Malawi','MW',265,'MWI',1),(129,'Malaysia','MY',60,'MYS',1),(130,'Maldives','MV',960,'MDV',1),(131,'Mali','ML',223,'MLI',1),(132,'Malta','MT',356,'MLT',1),(133,'Marshall Islands','MH',692,'MHL',1),(134,'Martinique','MQ',222,'MTQ',1),(135,'Mauritania','MR',222,'MRT',1),(136,'Mauritius','MU',230,'MUS',1),(137,'Mayotte','YT',262,'MYT',1),(138,'Mexico','MX',52,'MEX',1),(139,'Micronesia, Federated States of','FM',691,'FSM',1),(140,'Moldova, Republic of','MD',373,'MDA',1),(141,'Monaco','MC',377,'MCO',1),(142,'Mongolia','MN',976,'MNG',1),(143,'Montserrat','MS',1,'MSR',1),(144,'Morocco','MA',212,'MAR',1),(145,'Mozambique','MZ',258,'MOZ',1),(146,'Myanmar','MM',95,'MMR',1),(147,'Namibia','NA',264,'NAM',1),(148,'Nauru','NR',674,'NRU',1),(149,'Nepal','NP',977,'NPL',1),(150,'Netherlands','NL',31,'NLD',1),(151,'Netherlands Antilles','AN',599,'ANT',1),(152,'New Caledonia','NC',687,'NCL',1),(153,'New Zealand','NZ',64,'NZL',1),(154,'Nicaragua','NI',505,'NIC',1),(155,'Niger','NE',227,'NER',1),(156,'Nigeria','NG',234,'NGA',1),(157,'Niue','NU',683,'NIU',1),(158,'Norfolk Island','NF',672,'NFK',1),(159,'Northern Mariana Islands','MP',1,'MNP',1),(160,'Norway','NO',47,'NOR',1),(161,'Oman','OM',968,'OMN',1),(162,'Pakistan','PK',92,'PAK',1),(163,'Palau','PW',680,'PLW',1),(164,'Panama','PA',507,'PAN',1),(165,'Papua New Guinea','PG',675,'PNG',1),(166,'Paraguay','PY',595,'PRY',1),(167,'Peru','PE',51,'PER',1),(168,'Philippines','PH',63,'PHL',1),(169,'Pitcairn','PN',870,'PCN',1),(170,'Poland','PL',48,'POL',1),(171,'Portugal','PT',351,'PRT',1),(172,'Puerto Rico','PR',1,'PRI',1),(173,'Qatar','QA',974,'QAT',1),(174,'Reunion','RE',20,'REU',1),(175,'Romania','RO',40,'ROM',1),(176,'Russian Federation','RU',7,'RUS',1),(177,'Rwanda','RW',250,'RWA',1),(178,'Saint Kitts and Nevis','KN',1,'KNA',1),(179,'Saint Lucia','LC',1,'LCA',1),(180,'Saint Vincent and the Grenadines','VC',1,'VCT',1),(181,'Samoa','WS',685,'WSM',1),(182,'San Marino','SM',378,'SMR',1),(183,'Sao Tome and Principe','ST',239,'STP',1),(184,'Saudi Arabia','SA',966,'SAU',1),(185,'Senegal','SN',221,'SEN',1),(186,'Seychelles','SC',248,'SYC',1),(187,'Sierra Leone','SL',232,'SLE',1),(188,'Singapore','SG',65,'SGP',1),(189,'Slovak Republic','SK',421,'SVK',1),(190,'Slovenia','SI',386,'SVN',1),(191,'Solomon Islands','SB',677,'SLB',1),(192,'Somalia','SO',252,'SOM',1),(193,'South Africa','ZA',27,'ZAF',1),(194,'South Georgia &amp; South Sandwich Islands','GS',500,'SGS',1),(195,'Spain','ES',34,'ESP',1),(196,'Sri Lanka','LK',94,'LKA',1),(197,'St. Helena','SH',290,'SHN',1),(198,'St. Pierre and Miquelon','PM',508,'SPM',1),(199,'Sudan','SD',249,'SDN',1),(200,'Suriname','SR',597,'SUR',1),(201,'Svalbard and Jan Mayen Islands','SJ',47,'SJM',1),(202,'Swaziland','SZ',268,'SWZ',1),(203,'Sweden','SE',46,'SWE',1),(204,'Switzerland','CH',41,'CHE',1),(205,'Syrian Arab Republic','SY',963,'SYR',1),(206,'Taiwan','TW',886,'TWN',1),(207,'Tajikistan','TJ',992,'TJK',1),(208,'Tanzania, United Republic of','TZ',255,'TZA',1),(209,'Thailand','TH',66,'THA',1),(210,'Togo','TG',228,'TGO',1),(211,'Tokelau','TK',690,'TKL',1),(212,'Tonga','TO',676,'TON',1),(213,'Trinidad and Tobago','TT',1,'TTO',1),(214,'Tunisia','TN',216,'TUN',1),(215,'Turkey','TR',90,'TUR',1),(216,'Turkmenistan','TM',993,'TKM',1),(217,'Turks and Caicos Islands','TC',1,'TCA',1),(218,'Tuvalu','TV',688,'TUV',1),(219,'Uganda','UG',256,'UGA',1),(220,'Ukraine','UA',380,'UKR',1),(221,'United Arab Emirates','AE',971,'ARE',1),(222,'United Kingdom','GB',44,'GBR',1),(223,'United States','US',1,'USA',1),(224,'United States Minor Outlying Islands','UM',0,'UMI',1),(225,'Uruguay','UY',598,'URY',1),(226,'Uzbekistan','UZ',998,'UZB',1),(227,'Vanuatu','VU',678,'VUT',1),(228,'Vatican City State (Holy See)','VA',379,'VAT',1),(229,'Venezuela','VE',58,'VEN',1),(230,'Viet Nam','VN',84,'VNM',1),(231,'Virgin Islands (British)','VG',1284,'VGB',1),(232,'Virgin Islands (U.S.)','VI',1340,'VIR',1),(233,'Wallis and Futuna Islands','WF',681,'WLF',1),(234,'Western Sahara','EH',20,'ESH',1),(235,'Yemen','YE',967,'YEM',1),(237,'Democratic Republic of Congo','CD',243,'COD',1),(238,'Zambia','ZM',260,'ZMB',1),(239,'Zimbabwe','ZW',263,'ZWE',1),(240,'Jersey','JE',20,'JEY',1),(241,'Guernsey','GG',20,'GGY',1),(242,'Montenegro','ME',382,'MNE',1),(243,'Serbia','RS',381,'SRB',1),(244,'Aaland Islands','AX',0,'ALA',1),(245,'Bonaire, Sint Eustatius and Saba','BQ',599,'BES',1),(246,'Curacao','CW',599,'CUW',1),(247,'Palestinian Territory, Occupied','PS',970,'PSE',1),(248,'South Sudan','SS',211,'SSD',1),(249,'St. Barthelemy','BL',590,'BLM',1),(250,'St. Martin (French part)','MF',590,'MAF',1),(251,'Canary Islands','IC',34,'ICA',1);
/*!40000 ALTER TABLE `39_infinite_countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_infinite_languages`
--

DROP TABLE IF EXISTS `39_infinite_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_infinite_languages` (
  `lang_id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_code` varchar(10) CHARACTER SET utf8 NOT NULL,
  `lang_name` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT 'NA',
  `lang_name_in_english` varchar(200) CHARACTER SET utf8 NOT NULL,
  `status` varchar(50) CHARACTER SET utf8 NOT NULL,
  `default_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lang_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_infinite_languages`
--

LOCK TABLES `39_infinite_languages` WRITE;
/*!40000 ALTER TABLE `39_infinite_languages` DISABLE KEYS */;
INSERT INTO `39_infinite_languages` VALUES (1,'en','English','english','yes',1),(2,'es','Español','spanish','yes',0),(3,'ch','中文','chinese','yes',0),(4,'de','Deutsch','german','yes',0),(5,'pt','Português','portuguese','yes',0),(6,'fr','français','french','yes',0),(7,'it','italiano','italian','yes',0),(8,'tr','Türk','turkish','yes',0),(9,'po','polski','polish','yes',0),(10,'ar','العربية','arabic','yes',0),(11,'ru','русский','russian','yes',0);
/*!40000 ALTER TABLE `39_infinite_languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_infinite_mlm_menu`
--

DROP TABLE IF EXISTS `39_infinite_mlm_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_infinite_mlm_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link_ref_id` int(11) DEFAULT NULL,
  `icon` varchar(30) CHARACTER SET utf8 NOT NULL DEFAULT 'clip-home-2',
  `status` varchar(200) CHARACTER SET utf8 NOT NULL,
  `perm_admin` int(12) NOT NULL DEFAULT '0',
  `perm_dist` int(12) DEFAULT '0',
  `perm_emp` int(12) NOT NULL DEFAULT '0',
  `main_order_id` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `main_order_id` (`main_order_id`),
  KEY `status` (`status`),
  KEY `link_ref_id` (`link_ref_id`),
  CONSTRAINT `39_infinite_mlm_menu_ibfk_1` FOREIGN KEY (`link_ref_id`) REFERENCES `39_infinite_urls` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_infinite_mlm_menu`
--

LOCK TABLES `39_infinite_mlm_menu` WRITE;
/*!40000 ALTER TABLE `39_infinite_mlm_menu` DISABLE KEYS */;
INSERT INTO `39_infinite_mlm_menu` VALUES (1,7,'fa fa-tachometer','yes',1,1,1,1),(2,NULL,'fa fa-sitemap','yes',1,1,1,2),(7,63,'fa fa-money','yes',0,0,0,11),(9,NULL,'fa fa-money','yes',0,0,0,12),(10,NULL,'fa fa-cogs','yes',1,0,1,20),(11,NULL,'fa fa-wrench','yes',1,1,1,19),(12,NULL,'fa fa-cubes','yes',1,0,1,16),(13,NULL,'fa fa-bookmark-o','no',0,0,0,14),(14,NULL,'fa fa-briefcase','yes',0,0,0,10),(16,NULL,'fa fa-bar-chart','yes',1,0,1,17),(17,NULL,'fa fa-user-circle','no',1,0,1,17),(19,NULL,'fa fa-address-book-o','yes',1,0,1,13),(23,95,'fa fa-envelope-o','yes',1,1,1,18),(24,4,'fa fa-sign-out','yes',1,1,1,24),(32,87,'fa fa-ticket','no',1,0,1,22),(37,98,'fa fa-shopping-cart','no',1,1,1,4),(39,NULL,'fa fa-glass','no',1,1,1,7),(42,NULL,'fa fa-shopping-cart','no',1,1,1,6),(44,NULL,'fa fa-users','no',1,1,1,23),(46,153,'fa fa-shopping-bag','no',0,1,0,16),(52,NULL,'fa fa-user-plus','yes',1,0,1,3),(53,19,'fa fa-user-plus','yes',0,1,0,3),(54,NULL,'fa fa-gift','no',1,1,1,7),(55,216,'fa fa-dot-circle-o','no',1,0,1,8),(56,222,'fa fa-opencart','no',1,0,1,5),(61,NULL,'fa fa-shopping-bag','no',1,0,1,16),(65,NULL,'fa fa-building-o','yes',0,0,0,9),(66,87,'fa fa-ticket','no',0,1,0,22),(67,293,'fa fa-building-o','yes',1,0,1,9),(68,294,'fa fa-briefcase','yes',1,1,1,10),(69,295,'fa fa-money','yes',1,1,1,11),(70,296,'fa fa-bookmark-o','yes',1,1,1,12),(71,196,'fa fa-level-up','yes',0,0,0,14),(72,163,'fa fa-clock-o','yes',1,1,1,15),(73,22,'fa fa-address-book-o','yes',0,1,0,13);
/*!40000 ALTER TABLE `39_infinite_mlm_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_infinite_mlm_sub_menu`
--

DROP TABLE IF EXISTS `39_infinite_mlm_sub_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_infinite_mlm_sub_menu` (
  `sub_id` int(11) NOT NULL AUTO_INCREMENT,
  `sub_link_ref_id` int(11) DEFAULT NULL,
  `icon` varchar(30) CHARACTER SET utf8 NOT NULL DEFAULT 'clip-home-2',
  `sub_status` varchar(100) CHARACTER SET utf8 NOT NULL,
  `sub_refid` int(11) DEFAULT NULL,
  `perm_admin` int(12) NOT NULL DEFAULT '0',
  `perm_dist` int(12) NOT NULL DEFAULT '0',
  `perm_emp` int(12) NOT NULL DEFAULT '0',
  `sub_order_id` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sub_id`),
  KEY `sub_status` (`sub_status`),
  KEY `sub_order_id` (`sub_order_id`),
  KEY `sub_link_ref_id` (`sub_link_ref_id`),
  KEY `sub_refid` (`sub_refid`),
  CONSTRAINT `39_infinite_mlm_sub_menu_ibfk_15` FOREIGN KEY (`sub_link_ref_id`) REFERENCES `39_infinite_urls` (`id`),
  CONSTRAINT `39_infinite_mlm_sub_menu_ibfk_16` FOREIGN KEY (`sub_refid`) REFERENCES `39_infinite_mlm_menu` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=224 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_infinite_mlm_sub_menu`
--

LOCK TABLES `39_infinite_mlm_sub_menu` WRITE;
/*!40000 ALTER TABLE `39_infinite_mlm_sub_menu` DISABLE KEYS */;
INSERT INTO `39_infinite_mlm_sub_menu` VALUES (1,58,'','yes',2,1,1,1,2),(2,59,'','yes',2,1,1,1,4),(3,61,'','yes',2,1,1,1,3),(4,8,'','yes',10,1,0,1,1),(10,16,'','yes',10,1,0,1,2),(11,9,'','yes',10,1,0,1,3),(13,65,'','yes',11,1,0,1,5),(14,64,'','yes',11,1,0,1,6),(31,45,'','yes',16,1,0,1,1),(32,51,'','yes',16,1,0,1,3),(33,52,'','yes',16,1,0,1,5),(34,49,'','yes',16,1,0,1,11),(35,46,'','yes',16,1,1,1,4),(36,47,'','yes',16,1,1,1,12),(39,42,'','no',17,1,0,1,1),(41,43,'','no',17,1,0,1,2),(42,41,'','no',17,1,0,1,3),(43,22,'','yes',19,1,1,1,1),(45,25,'','yes',19,1,0,1,3),(52,66,'','yes',11,0,1,0,6),(56,200,'','yes',11,1,0,1,2),(57,88,'','yes',16,1,0,1,6),(64,97,'','yes',11,0,1,0,5),(65,101,'','no',2,1,1,1,1),(68,103,'','no',39,1,1,1,1),(69,105,'','no',39,1,1,1,2),(70,112,'','no',39,1,1,1,3),(71,114,'','no',39,1,1,1,4),(79,133,'','yes',16,1,1,1,9),(80,123,'','yes',16,1,0,1,2),(82,136,'','no',44,1,1,1,2),(83,137,'','no',44,1,1,1,3),(84,138,'','no',44,1,1,1,4),(85,135,'','no',44,1,1,1,1),(86,156,'','no',17,1,0,1,4),(87,155,'','no',16,1,0,1,8),(89,159,'','no',16,1,0,1,13),(90,160,'','no',16,1,0,1,14),(91,161,'','no',2,1,1,1,1),(93,163,'','yes',19,1,0,1,7),(97,125,'','no',42,1,1,1,1),(98,167,'','no',42,1,0,1,2),(103,183,'','yes',16,1,1,1,10),(105,185,'','yes',11,0,1,0,3),(108,187,'','yes',12,1,0,1,1),(109,188,'','no',12,1,0,1,2),(117,19,'','yes',52,1,0,1,1),(118,182,'','yes',52,1,0,1,2),(119,196,'','no',19,1,0,1,8),(122,77,'','yes',11,1,1,1,1),(124,202,'','no',54,0,0,0,1),(125,205,'','no',54,1,1,1,1),(126,206,'','no',54,0,1,0,2),(131,212,'','no',54,1,0,1,4),(132,213,'','no',54,1,1,1,3),(133,214,'','no',54,0,0,0,8),(136,207,'','no',16,1,0,1,15),(137,219,'','no',54,1,0,1,2),(138,7,'','no',32,1,0,1,1),(139,145,'','no',32,1,0,1,2),(140,146,'','no',32,1,0,1,3),(141,148,'','no',32,1,0,1,4),(142,149,'','no',32,1,0,1,5),(143,150,'','no',32,1,0,1,6),(144,151,'','no',32,1,0,1,7),(145,152,'','no',32,1,0,1,8),(146,4,'','no',32,1,0,1,9),(150,225,'','no',19,1,0,1,10),(151,226,'','no',19,0,1,0,10),(153,229,'','no',16,0,0,0,2),(154,230,'','yes',11,1,0,1,8),(155,231,'','no',16,0,0,0,2),(156,232,'','yes',11,0,1,0,8),(158,234,'','yes',52,1,0,1,3),(171,153,'','no',61,1,0,1,1),(172,252,'','no',61,1,0,1,2),(192,78,'','yes',2,1,1,1,5),(193,79,'','yes',2,1,1,1,6),(202,280,'','yes',65,1,0,1,1),(203,281,'','yes',65,1,0,1,2),(215,285,'','no',11,1,0,1,4),(217,89,'','no',11,0,1,0,4),(219,286,'','yes',10,1,0,1,4),(220,288,'clip-transfer','yes',16,1,0,1,16),(221,290,'clip-bubbles-3','yes',10,0,0,1,5),(222,291,'clip-home-2','yes',16,1,0,1,17),(223,292,'clip-home-2','yes',10,1,0,0,1);
/*!40000 ALTER TABLE `39_infinite_mlm_sub_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_infinite_states`
--

DROP TABLE IF EXISTS `39_infinite_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_infinite_states` (
  `state_id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) NOT NULL,
  `state_name` varchar(128) NOT NULL DEFAULT 'NA',
  `state_code` varchar(32) NOT NULL DEFAULT 'NA',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`state_id`),
  KEY `state_name` (`state_name`),
  KEY `country_id` (`country_id`),
  CONSTRAINT `39_infinite_states_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `39_infinite_countries` (`country_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4235 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_infinite_states`
--

LOCK TABLES `39_infinite_states` WRITE;
/*!40000 ALTER TABLE `39_infinite_states` DISABLE KEYS */;
INSERT INTO `39_infinite_states` VALUES (1,1,'Badakhshan','BDS',1),(2,1,'Badghis','BDG',1),(3,1,'Baghlan','BGL',1),(4,1,'Balkh','BAL',1),(5,1,'Bamian','BAM',1),(6,1,'Farah','FRA',1),(7,1,'Faryab','FYB',1),(8,1,'Ghazni','GHA',1),(9,1,'Ghowr','GHO',1),(10,1,'Helmand','HEL',1),(11,1,'Herat','HER',1),(12,1,'Jowzjan','JOW',1),(13,1,'Kabul','KAB',1),(14,1,'Kandahar','KAN',1),(15,1,'Kapisa','KAP',1),(16,1,'Khost','KHO',1),(17,1,'Konar','KNR',1),(18,1,'Kondoz','KDZ',1),(19,1,'Laghman','LAG',1),(20,1,'Lowgar','LOW',1),(21,1,'Nangrahar','NAN',1),(22,1,'Nimruz','NIM',1),(23,1,'Nurestan','NUR',1),(24,1,'Oruzgan','ORU',1),(25,1,'Paktia','PIA',1),(26,1,'Paktika','PKA',1),(27,1,'Parwan','PAR',1),(28,1,'Samangan','SAM',1),(29,1,'Sar-e Pol','SAR',1),(30,1,'Takhar','TAK',1),(31,1,'Wardak','WAR',1),(32,1,'Zabol','ZAB',1),(33,2,'Berat','BR',1),(34,2,'Bulqize','BU',1),(35,2,'Delvine','DL',1),(36,2,'Devoll','DV',1),(37,2,'Diber','DI',1),(38,2,'Durres','DR',1),(39,2,'Elbasan','EL',1),(40,2,'Kolonje','ER',1),(41,2,'Fier','FR',1),(42,2,'Gjirokaster','GJ',1),(43,2,'Gramsh','GR',1),(44,2,'Has','HA',1),(45,2,'Kavaje','KA',1),(46,2,'Kurbin','KB',1),(47,2,'Kucove','KC',1),(48,2,'Korce','KO',1),(49,2,'Kruje','KR',1),(50,2,'Kukes','KU',1),(51,2,'Librazhd','LB',1),(52,2,'Lezhe','LE',1),(53,2,'Lushnje','LU',1),(54,2,'Malesi e Madhe','MM',1),(55,2,'Mallakaster','MK',1),(56,2,'Mat','MT',1),(57,2,'Mirdite','MR',1),(58,2,'Peqin','PQ',1),(59,2,'Permet','PR',1),(60,2,'Pogradec','PG',1),(61,2,'Puke','PU',1),(62,2,'Shkoder','SH',1),(63,2,'Skrapar','SK',1),(64,2,'Sarande','SR',1),(65,2,'Tepelene','TE',1),(66,2,'Tropoje','TP',1),(67,2,'Tirane','TR',1),(68,2,'Vlore','VL',1),(69,3,'Adrar','ADR',1),(70,3,'Ain Defla','ADE',1),(71,3,'Ain Temouchent','ATE',1),(72,3,'Alger','ALG',1),(73,3,'Annaba','ANN',1),(74,3,'Batna','BAT',1),(75,3,'Bechar','BEC',1),(76,3,'Bejaia','BEJ',1),(77,3,'Biskra','BIS',1),(78,3,'Blida','BLI',1),(79,3,'Bordj Bou Arreridj','BBA',1),(80,3,'Bouira','BOA',1),(81,3,'Boumerdes','BMD',1),(82,3,'Chlef','CHL',1),(83,3,'Constantine','CON',1),(84,3,'Djelfa','DJE',1),(85,3,'El Bayadh','EBA',1),(86,3,'El Oued','EOU',1),(87,3,'El Tarf','ETA',1),(88,3,'Ghardaia','GHA',1),(89,3,'Guelma','GUE',1),(90,3,'Illizi','ILL',1),(91,3,'Jijel','JIJ',1),(92,3,'Khenchela','KHE',1),(93,3,'Laghouat','LAG',1),(94,3,'Muaskar','MUA',1),(95,3,'Medea','MED',1),(96,3,'Mila','MIL',1),(97,3,'Mostaganem','MOS',1),(98,3,'M\'Sila','MSI',1),(99,3,'Naama','NAA',1),(100,3,'Oran','ORA',1),(101,3,'Ouargla','OUA',1),(102,3,'Oum el-Bouaghi','OEB',1),(103,3,'Relizane','REL',1),(104,3,'Saida','SAI',1),(105,3,'Setif','SET',1),(106,3,'Sidi Bel Abbes','SBA',1),(107,3,'Skikda','SKI',1),(108,3,'Souk Ahras','SAH',1),(109,3,'Tamanghasset','TAM',1),(110,3,'Tebessa','TEB',1),(111,3,'Tiaret','TIA',1),(112,3,'Tindouf','TIN',1),(113,3,'Tipaza','TIP',1),(114,3,'Tissemsilt','TIS',1),(115,3,'Tizi Ouzou','TOU',1),(116,3,'Tlemcen','TLE',1),(117,4,'Eastern','E',1),(118,4,'Manu\'a','M',1),(119,4,'Rose Island','R',1),(120,4,'Swains Island','S',1),(121,4,'Western','W',1),(122,5,'Andorra la Vella','ALV',1),(123,5,'Canillo','CAN',1),(124,5,'Encamp','ENC',1),(125,5,'Escaldes-Engordany','ESE',1),(126,5,'La Massana','LMA',1),(127,5,'Ordino','ORD',1),(128,5,'Sant Julia de Loria','SJL',1),(129,6,'Bengo','BGO',1),(130,6,'Benguela','BGU',1),(131,6,'Bie','BIE',1),(132,6,'Cabinda','CAB',1),(133,6,'Cuando-Cubango','CCU',1),(134,6,'Cuanza Norte','CNO',1),(135,6,'Cuanza Sul','CUS',1),(136,6,'Cunene','CNN',1),(137,6,'Huambo','HUA',1),(138,6,'Huila','HUI',1),(139,6,'Luanda','LUA',1),(140,6,'Lunda Norte','LNO',1),(141,6,'Lunda Sul','LSU',1),(142,6,'Malange','MAL',1),(143,6,'Moxico','MOX',1),(144,6,'Namibe','NAM',1),(145,6,'Uige','UIG',1),(146,6,'Zaire','ZAI',1),(147,9,'Saint George','ASG',1),(148,9,'Saint John','ASJ',1),(149,9,'Saint Mary','ASM',1),(150,9,'Saint Paul','ASL',1),(151,9,'Saint Peter','ASR',1),(152,9,'Saint Philip','ASH',1),(153,9,'Barbuda','BAR',1),(154,9,'Redonda','RED',1),(155,10,'Antartida e Islas del Atlantico','AN',1),(156,10,'Buenos Aires','BA',1),(157,10,'Catamarca','CA',1),(158,10,'Chaco','CH',1),(159,10,'Chubut','CU',1),(160,10,'Cordoba','CO',1),(161,10,'Corrientes','CR',1),(162,10,'Distrito Federal','DF',1),(163,10,'Entre Rios','ER',1),(164,10,'Formosa','FO',1),(165,10,'Jujuy','JU',1),(166,10,'La Pampa','LP',1),(167,10,'La Rioja','LR',1),(168,10,'Mendoza','ME',1),(169,10,'Misiones','MI',1),(170,10,'Neuquen','NE',1),(171,10,'Rio Negro','RN',1),(172,10,'Salta','SA',1),(173,10,'San Juan','SJ',1),(174,10,'San Luis','SL',1),(175,10,'Santa Cruz','SC',1),(176,10,'Santa Fe','SF',1),(177,10,'Santiago del Estero','SD',1),(178,10,'Tierra del Fuego','TF',1),(179,10,'Tucuman','TU',1),(180,11,'Aragatsotn','AGT',1),(181,11,'Ararat','ARR',1),(182,11,'Armavir','ARM',1),(183,11,'Geghark\'unik\'','GEG',1),(184,11,'Kotayk\'','KOT',1),(185,11,'Lorri','LOR',1),(186,11,'Shirak','SHI',1),(187,11,'Syunik\'','SYU',1),(188,11,'Tavush','TAV',1),(189,11,'Vayots\' Dzor','VAY',1),(190,11,'Yerevan','YER',1),(191,13,'Australian Capital Territory','ACT',1),(192,13,'New South Wales','NSW',1),(193,13,'Northern Territory','NT',1),(194,13,'Queensland','QLD',1),(195,13,'South Australia','SA',1),(196,13,'Tasmania','TAS',1),(197,13,'Victoria','VIC',1),(198,13,'Western Australia','WA',1),(199,14,'Burgenland','BUR',1),(200,14,'Kärnten','KAR',1),(201,14,'Nieder&ouml;sterreich','NOS',1),(202,14,'Ober&ouml;sterreich','OOS',1),(203,14,'Salzburg','SAL',1),(204,14,'Steiermark','STE',1),(205,14,'Tirol','TIR',1),(206,14,'Vorarlberg','VOR',1),(207,14,'Wien','WIE',1),(208,15,'Ali Bayramli','AB',1),(209,15,'Abseron','ABS',1),(210,15,'AgcabAdi','AGC',1),(211,15,'Agdam','AGM',1),(212,15,'Agdas','AGS',1),(213,15,'Agstafa','AGA',1),(214,15,'Agsu','AGU',1),(215,15,'Astara','AST',1),(216,15,'Baki','BA',1),(217,15,'BabAk','BAB',1),(218,15,'BalakAn','BAL',1),(219,15,'BArdA','BAR',1),(220,15,'Beylaqan','BEY',1),(221,15,'Bilasuvar','BIL',1),(222,15,'Cabrayil','CAB',1),(223,15,'Calilabab','CAL',1),(224,15,'Culfa','CUL',1),(225,15,'Daskasan','DAS',1),(226,15,'Davaci','DAV',1),(227,15,'Fuzuli','FUZ',1),(228,15,'Ganca','GA',1),(229,15,'Gadabay','GAD',1),(230,15,'Goranboy','GOR',1),(231,15,'Goycay','GOY',1),(232,15,'Haciqabul','HAC',1),(233,15,'Imisli','IMI',1),(234,15,'Ismayilli','ISM',1),(235,15,'Kalbacar','KAL',1),(236,15,'Kurdamir','KUR',1),(237,15,'Lankaran','LA',1),(238,15,'Lacin','LAC',1),(239,15,'Lankaran','LAN',1),(240,15,'Lerik','LER',1),(241,15,'Masalli','MAS',1),(242,15,'Mingacevir','MI',1),(243,15,'Naftalan','NA',1),(244,15,'Neftcala','NEF',1),(245,15,'Oguz','OGU',1),(246,15,'Ordubad','ORD',1),(247,15,'Qabala','QAB',1),(248,15,'Qax','QAX',1),(249,15,'Qazax','QAZ',1),(250,15,'Qobustan','QOB',1),(251,15,'Quba','QBA',1),(252,15,'Qubadli','QBI',1),(253,15,'Qusar','QUS',1),(254,15,'Saki','SA',1),(255,15,'Saatli','SAT',1),(256,15,'Sabirabad','SAB',1),(257,15,'Sadarak','SAD',1),(258,15,'Sahbuz','SAH',1),(259,15,'Saki','SAK',1),(260,15,'Salyan','SAL',1),(261,15,'Sumqayit','SM',1),(262,15,'Samaxi','SMI',1),(263,15,'Samkir','SKR',1),(264,15,'Samux','SMX',1),(265,15,'Sarur','SAR',1),(266,15,'Siyazan','SIY',1),(267,15,'Susa','SS',1),(268,15,'Susa','SUS',1),(269,15,'Tartar','TAR',1),(270,15,'Tovuz','TOV',1),(271,15,'Ucar','UCA',1),(272,15,'Xankandi','XA',1),(273,15,'Xacmaz','XAC',1),(274,15,'Xanlar','XAN',1),(275,15,'Xizi','XIZ',1),(276,15,'Xocali','XCI',1),(277,15,'Xocavand','XVD',1),(278,15,'Yardimli','YAR',1),(279,15,'Yevlax','YEV',1),(280,15,'Zangilan','ZAN',1),(281,15,'Zaqatala','ZAQ',1),(282,15,'Zardab','ZAR',1),(283,15,'Naxcivan','NX',1),(284,16,'Acklins','ACK',1),(285,16,'Berry Islands','BER',1),(286,16,'Bimini','BIM',1),(287,16,'Black Point','BLK',1),(288,16,'Cat Island','CAT',1),(289,16,'Central Abaco','CAB',1),(290,16,'Central Andros','CAN',1),(291,16,'Central Eleuthera','CEL',1),(292,16,'City of Freeport','FRE',1),(293,16,'Crooked Island','CRO',1),(294,16,'East Grand Bahama','EGB',1),(295,16,'Exuma','EXU',1),(296,16,'Grand Cay','GRD',1),(297,16,'Harbour Island','HAR',1),(298,16,'Hope Town','HOP',1),(299,16,'Inagua','INA',1),(300,16,'Long Island','LNG',1),(301,16,'Mangrove Cay','MAN',1),(302,16,'Mayaguana','MAY',1),(303,16,'Moore\'s Island','MOO',1),(304,16,'North Abaco','NAB',1),(305,16,'North Andros','NAN',1),(306,16,'North Eleuthera','NEL',1),(307,16,'Ragged Island','RAG',1),(308,16,'Rum Cay','RUM',1),(309,16,'San Salvador','SAL',1),(310,16,'South Abaco','SAB',1),(311,16,'South Andros','SAN',1),(312,16,'South Eleuthera','SEL',1),(313,16,'Spanish Wells','SWE',1),(314,16,'West Grand Bahama','WGB',1),(315,17,'Capital','CAP',1),(316,17,'Central','CEN',1),(317,17,'Muharraq','MUH',1),(318,17,'Northern','NOR',1),(319,17,'Southern','SOU',1),(320,18,'Barisal','BAR',1),(321,18,'Chittagong','CHI',1),(322,18,'Dhaka','DHA',1),(323,18,'Khulna','KHU',1),(324,18,'Rajshahi','RAJ',1),(325,18,'Sylhet','SYL',1),(326,19,'Christ Church','CC',1),(327,19,'Saint Andrew','AND',1),(328,19,'Saint George','GEO',1),(329,19,'Saint James','JAM',1),(330,19,'Saint John','JOH',1),(331,19,'Saint Joseph','JOS',1),(332,19,'Saint Lucy','LUC',1),(333,19,'Saint Michael','MIC',1),(334,19,'Saint Peter','PET',1),(335,19,'Saint Philip','PHI',1),(336,19,'Saint Thomas','THO',1),(337,20,'Brestskaya (Brest)','BR',1),(338,20,'Homyel\'skaya (Homyel\')','HO',1),(339,20,'Horad Minsk','HM',1),(340,20,'Hrodzyenskaya (Hrodna)','HR',1),(341,20,'Mahilyowskaya (Mahilyow)','MA',1),(342,20,'Minskaya','MI',1),(343,20,'Vitsyebskaya (Vitsyebsk)','VI',1),(344,21,'Antwerpen','VAN',1),(345,21,'Brabant Wallon','WBR',1),(346,21,'Hainaut','WHT',1),(347,21,'Liège','WLG',1),(348,21,'Limburg','VLI',1),(349,21,'Luxembourg','WLX',1),(350,21,'Namur','WNA',1),(351,21,'Oost-Vlaanderen','VOV',1),(352,21,'Vlaams Brabant','VBR',1),(353,21,'West-Vlaanderen','VWV',1),(354,22,'Belize','BZ',1),(355,22,'Cayo','CY',1),(356,22,'Corozal','CR',1),(357,22,'Orange Walk','OW',1),(358,22,'Stann Creek','SC',1),(359,22,'Toledo','TO',1),(360,23,'Alibori','AL',1),(361,23,'Atakora','AK',1),(362,23,'Atlantique','AQ',1),(363,23,'Borgou','BO',1),(364,23,'Collines','CO',1),(365,23,'Donga','DO',1),(366,23,'Kouffo','KO',1),(367,23,'Littoral','LI',1),(368,23,'Mono','MO',1),(369,23,'Oueme','OU',1),(370,23,'Plateau','PL',1),(371,23,'Zou','ZO',1),(372,24,'Devonshire','DS',1),(373,24,'Hamilton City','HC',1),(374,24,'Hamilton','HA',1),(375,24,'Paget','PG',1),(376,24,'Pembroke','PB',1),(377,24,'Saint George City','GC',1),(378,24,'Saint George\'s','SG',1),(379,24,'Sandys','SA',1),(380,24,'Smith\'s','SM',1),(381,24,'Southampton','SH',1),(382,24,'Warwick','WA',1),(383,25,'Bumthang','BUM',1),(384,25,'Chukha','CHU',1),(385,25,'Dagana','DAG',1),(386,25,'Gasa','GAS',1),(387,25,'Haa','HAA',1),(388,25,'Lhuntse','LHU',1),(389,25,'Mongar','MON',1),(390,25,'Paro','PAR',1),(391,25,'Pemagatshel','PEM',1),(392,25,'Punakha','PUN',1),(393,25,'Samdrup Jongkhar','SJO',1),(394,25,'Samtse','SAT',1),(395,25,'Sarpang','SAR',1),(396,25,'Thimphu','THI',1),(397,25,'Trashigang','TRG',1),(398,25,'Trashiyangste','TRY',1),(399,25,'Trongsa','TRO',1),(400,25,'Tsirang','TSI',1),(401,25,'Wangdue Phodrang','WPH',1),(402,25,'Zhemgang','ZHE',1),(403,26,'Beni','BEN',1),(404,26,'Chuquisaca','CHU',1),(405,26,'Cochabamba','COC',1),(406,26,'La Paz','LPZ',1),(407,26,'Oruro','ORU',1),(408,26,'Pando','PAN',1),(409,26,'Potosi','POT',1),(410,26,'Santa Cruz','SCZ',1),(411,26,'Tarija','TAR',1),(412,27,'Brcko district','BRO',1),(413,27,'Unsko-Sanski Kanton','FUS',1),(414,27,'Posavski Kanton','FPO',1),(415,27,'Tuzlanski Kanton','FTU',1),(416,27,'Zenicko-Dobojski Kanton','FZE',1),(417,27,'Bosanskopodrinjski Kanton','FBP',1),(418,27,'Srednjebosanski Kanton','FSB',1),(419,27,'Hercegovacko-neretvanski Kanton','FHN',1),(420,27,'Zapadnohercegovacka Zupanija','FZH',1),(421,27,'Kanton Sarajevo','FSA',1),(422,27,'Zapadnobosanska','FZA',1),(423,27,'Banja Luka','SBL',1),(424,27,'Doboj','SDO',1),(425,27,'Bijeljina','SBI',1),(426,27,'Vlasenica','SVL',1),(427,27,'Sarajevo-Romanija or Sokolac','SSR',1),(428,27,'Foca','SFO',1),(429,27,'Trebinje','STR',1),(430,28,'Central','CE',1),(431,28,'Ghanzi','GH',1),(432,28,'Kgalagadi','KD',1),(433,28,'Kgatleng','KT',1),(434,28,'Kweneng','KW',1),(435,28,'Ngamiland','NG',1),(436,28,'North East','NE',1),(437,28,'North West','NW',1),(438,28,'South East','SE',1),(439,28,'Southern','SO',1),(440,30,'Acre','AC',1),(441,30,'Alagoas','AL',1),(442,30,'Amapá','AP',1),(443,30,'Amazonas','AM',1),(444,30,'Bahia','BA',1),(445,30,'Ceará','CE',1),(446,30,'Distrito Federal','DF',1),(447,30,'Espírito Santo','ES',1),(448,30,'Goiás','GO',1),(449,30,'Maranhão','MA',1),(450,30,'Mato Grosso','MT',1),(451,30,'Mato Grosso do Sul','MS',1),(452,30,'Minas Gerais','MG',1),(453,30,'Pará','PA',1),(454,30,'Paraíba','PB',1),(455,30,'Paraná','PR',1),(456,30,'Pernambuco','PE',1),(457,30,'Piauí','PI',1),(458,30,'Rio de Janeiro','RJ',1),(459,30,'Rio Grande do Norte','RN',1),(460,30,'Rio Grande do Sul','RS',1),(461,30,'Rondônia','RO',1),(462,30,'Roraima','RR',1),(463,30,'Santa Catarina','SC',1),(464,30,'São Paulo','SP',1),(465,30,'Sergipe','SE',1),(466,30,'Tocantins','TO',1),(467,31,'Peros Banhos','PB',1),(468,31,'Salomon Islands','SI',1),(469,31,'Nelsons Island','NI',1),(470,31,'Three Brothers','TB',1),(471,31,'Eagle Islands','EA',1),(472,31,'Danger Island','DI',1),(473,31,'Egmont Islands','EG',1),(474,31,'Diego Garcia','DG',1),(475,32,'Belait','BEL',1),(476,32,'Brunei and Muara','BRM',1),(477,32,'Temburong','TEM',1),(478,32,'Tutong','TUT',1),(479,33,'Blagoevgrad','',1),(480,33,'Burgas','',1),(481,33,'Dobrich','',1),(482,33,'Gabrovo','',1),(483,33,'Haskovo','',1),(484,33,'Kardjali','',1),(485,33,'Kyustendil','',1),(486,33,'Lovech','',1),(487,33,'Montana','',1),(488,33,'Pazardjik','',1),(489,33,'Pernik','',1),(490,33,'Pleven','',1),(491,33,'Plovdiv','',1),(492,33,'Razgrad','',1),(493,33,'Shumen','',1),(494,33,'Silistra','',1),(495,33,'Sliven','',1),(496,33,'Smolyan','',1),(497,33,'Sofia','',1),(498,33,'Sofia - town','',1),(499,33,'Stara Zagora','',1),(500,33,'Targovishte','',1),(501,33,'Varna','',1),(502,33,'Veliko Tarnovo','',1),(503,33,'Vidin','',1),(504,33,'Vratza','',1),(505,33,'Yambol','',1),(506,34,'Bale','BAL',1),(507,34,'Bam','BAM',1),(508,34,'Banwa','BAN',1),(509,34,'Bazega','BAZ',1),(510,34,'Bougouriba','BOR',1),(511,34,'Boulgou','BLG',1),(512,34,'Boulkiemde','BOK',1),(513,34,'Comoe','COM',1),(514,34,'Ganzourgou','GAN',1),(515,34,'Gnagna','GNA',1),(516,34,'Gourma','GOU',1),(517,34,'Houet','HOU',1),(518,34,'Ioba','IOA',1),(519,34,'Kadiogo','KAD',1),(520,34,'Kenedougou','KEN',1),(521,34,'Komondjari','KOD',1),(522,34,'Kompienga','KOP',1),(523,34,'Kossi','KOS',1),(524,34,'Koulpelogo','KOL',1),(525,34,'Kouritenga','KOT',1),(526,34,'Kourweogo','KOW',1),(527,34,'Leraba','LER',1),(528,34,'Loroum','LOR',1),(529,34,'Mouhoun','MOU',1),(530,34,'Nahouri','NAH',1),(531,34,'Namentenga','NAM',1),(532,34,'Nayala','NAY',1),(533,34,'Noumbiel','NOU',1),(534,34,'Oubritenga','OUB',1),(535,34,'Oudalan','OUD',1),(536,34,'Passore','PAS',1),(537,34,'Poni','PON',1),(538,34,'Sanguie','SAG',1),(539,34,'Sanmatenga','SAM',1),(540,34,'Seno','SEN',1),(541,34,'Sissili','SIS',1),(542,34,'Soum','SOM',1),(543,34,'Sourou','SOR',1),(544,34,'Tapoa','TAP',1),(545,34,'Tuy','TUY',1),(546,34,'Yagha','YAG',1),(547,34,'Yatenga','YAT',1),(548,34,'Ziro','ZIR',1),(549,34,'Zondoma','ZOD',1),(550,34,'Zoundweogo','ZOW',1),(551,35,'Bubanza','BB',1),(552,35,'Bujumbura','BJ',1),(553,35,'Bururi','BR',1),(554,35,'Cankuzo','CA',1),(555,35,'Cibitoke','CI',1),(556,35,'Gitega','GI',1),(557,35,'Karuzi','KR',1),(558,35,'Kayanza','KY',1),(559,35,'Kirundo','KI',1),(560,35,'Makamba','MA',1),(561,35,'Muramvya','MU',1),(562,35,'Muyinga','MY',1),(563,35,'Mwaro','MW',1),(564,35,'Ngozi','NG',1),(565,35,'Rutana','RT',1),(566,35,'Ruyigi','RY',1),(567,36,'Phnom Penh','PP',1),(568,36,'Preah Seihanu (Kompong Som or Sihanoukville)','PS',1),(569,36,'Pailin','PA',1),(570,36,'Keb','KB',1),(571,36,'Banteay Meanchey','BM',1),(572,36,'Battambang','BA',1),(573,36,'Kampong Cham','KM',1),(574,36,'Kampong Chhnang','KN',1),(575,36,'Kampong Speu','KU',1),(576,36,'Kampong Som','KO',1),(577,36,'Kampong Thom','KT',1),(578,36,'Kampot','KP',1),(579,36,'Kandal','KL',1),(580,36,'Kaoh Kong','KK',1),(581,36,'Kratie','KR',1),(582,36,'Mondul Kiri','MK',1),(583,36,'Oddar Meancheay','OM',1),(584,36,'Pursat','PU',1),(585,36,'Preah Vihear','PR',1),(586,36,'Prey Veng','PG',1),(587,36,'Ratanak Kiri','RK',1),(588,36,'Siemreap','SI',1),(589,36,'Stung Treng','ST',1),(590,36,'Svay Rieng','SR',1),(591,36,'Takeo','TK',1),(592,37,'Adamawa (Adamaoua)','ADA',1),(593,37,'Centre','CEN',1),(594,37,'East (Est)','EST',1),(595,37,'Extreme North (Extreme-Nord)','EXN',1),(596,37,'Littoral','LIT',1),(597,37,'North (Nord)','NOR',1),(598,37,'Northwest (Nord-Ouest)','NOT',1),(599,37,'West (Ouest)','OUE',1),(600,37,'South (Sud)','SUD',1),(601,37,'Southwest (Sud-Ouest).','SOU',1),(602,38,'Alberta','AB',1),(603,38,'British Columbia','BC',1),(604,38,'Manitoba','MB',1),(605,38,'New Brunswick','NB',1),(606,38,'Newfoundland and Labrador','NL',1),(607,38,'Northwest Territories','NT',1),(608,38,'Nova Scotia','NS',1),(609,38,'Nunavut','NU',1),(610,38,'Ontario','ON',1),(611,38,'Prince Edward Island','PE',1),(612,38,'Qu&eacute;bec','QC',1),(613,38,'Saskatchewan','SK',1),(614,38,'Yukon Territory','YT',1),(615,39,'Boa Vista','BV',1),(616,39,'Brava','BR',1),(617,39,'Calheta de Sao Miguel','CS',1),(618,39,'Maio','MA',1),(619,39,'Mosteiros','MO',1),(620,39,'Paul','PA',1),(621,39,'Porto Novo','PN',1),(622,39,'Praia','PR',1),(623,39,'Ribeira Grande','RG',1),(624,39,'Sal','SL',1),(625,39,'Santa Catarina','CA',1),(626,39,'Santa Cruz','CR',1),(627,39,'Sao Domingos','SD',1),(628,39,'Sao Filipe','SF',1),(629,39,'Sao Nicolau','SN',1),(630,39,'Sao Vicente','SV',1),(631,39,'Tarrafal','TA',1),(632,40,'Creek','CR',1),(633,40,'Eastern','EA',1),(634,40,'Midland','ML',1),(635,40,'South Town','ST',1),(636,40,'Spot Bay','SP',1),(637,40,'Stake Bay','SK',1),(638,40,'West End','WD',1),(639,40,'Western','WN',1),(640,41,'Bamingui-Bangoran','BBA',1),(641,41,'Basse-Kotto','BKO',1),(642,41,'Haute-Kotto','HKO',1),(643,41,'Haut-Mbomou','HMB',1),(644,41,'Kemo','KEM',1),(645,41,'Lobaye','LOB',1),(646,41,'Mambere-KadeÔ','MKD',1),(647,41,'Mbomou','MBO',1),(648,41,'Nana-Mambere','NMM',1),(649,41,'Ombella-M\'Poko','OMP',1),(650,41,'Ouaka','OUK',1),(651,41,'Ouham','OUH',1),(652,41,'Ouham-Pende','OPE',1),(653,41,'Vakaga','VAK',1),(654,41,'Nana-Grebizi','NGR',1),(655,41,'Sangha-Mbaere','SMB',1),(656,41,'Bangui','BAN',1),(657,42,'Batha','BA',1),(658,42,'Biltine','BI',1),(659,42,'Borkou-Ennedi-Tibesti','BE',1),(660,42,'Chari-Baguirmi','CB',1),(661,42,'Guera','GU',1),(662,42,'Kanem','KA',1),(663,42,'Lac','LA',1),(664,42,'Logone Occidental','LC',1),(665,42,'Logone Oriental','LR',1),(666,42,'Mayo-Kebbi','MK',1),(667,42,'Moyen-Chari','MC',1),(668,42,'Ouaddai','OU',1),(669,42,'Salamat','SA',1),(670,42,'Tandjile','TA',1),(671,43,'Aisen del General Carlos Ibanez','AI',1),(672,43,'Antofagasta','AN',1),(673,43,'Araucania','AR',1),(674,43,'Atacama','AT',1),(675,43,'Bio-Bio','BI',1),(676,43,'Coquimbo','CO',1),(677,43,'Libertador General Bernardo O\'Higgins','LI',1),(678,43,'Los Lagos','LL',1),(679,43,'Magallanes y de la Antartica Chilena','MA',1),(680,43,'Maule','ML',1),(681,43,'Region Metropolitana','RM',1),(682,43,'Tarapaca','TA',1),(683,43,'Valparaiso','VS',1),(684,44,'Anhui','AN',1),(685,44,'Beijing','BE',1),(686,44,'Chongqing','CH',1),(687,44,'Fujian','FU',1),(688,44,'Gansu','GA',1),(689,44,'Guangdong','GU',1),(690,44,'Guangxi','GX',1),(691,44,'Guizhou','GZ',1),(692,44,'Hainan','HA',1),(693,44,'Hebei','HB',1),(694,44,'Heilongjiang','HL',1),(695,44,'Henan','HE',1),(696,44,'Hong Kong','HK',1),(697,44,'Hubei','HU',1),(698,44,'Hunan','HN',1),(699,44,'Inner Mongolia','IM',1),(700,44,'Jiangsu','JI',1),(701,44,'Jiangxi','JX',1),(702,44,'Jilin','JL',1),(703,44,'Liaoning','LI',1),(704,44,'Macau','MA',1),(705,44,'Ningxia','NI',1),(706,44,'Shaanxi','SH',1),(707,44,'Shandong','SA',1),(708,44,'Shanghai','SG',1),(709,44,'Shanxi','SX',1),(710,44,'Sichuan','SI',1),(711,44,'Tianjin','TI',1),(712,44,'Xinjiang','XI',1),(713,44,'Yunnan','YU',1),(714,44,'Zhejiang','ZH',1),(715,46,'Direction Island','D',1),(716,46,'Home Island','H',1),(717,46,'Horsburgh Island','O',1),(718,46,'South Island','S',1),(719,46,'West Island','W',1),(720,47,'Amazonas','AMZ',1),(721,47,'Antioquia','ANT',1),(722,47,'Arauca','ARA',1),(723,47,'Atlantico','ATL',1),(724,47,'Bogota D.C.','BDC',1),(725,47,'Bolivar','BOL',1),(726,47,'Boyaca','BOY',1),(727,47,'Caldas','CAL',1),(728,47,'Caqueta','CAQ',1),(729,47,'Casanare','CAS',1),(730,47,'Cauca','CAU',1),(731,47,'Cesar','CES',1),(732,47,'Choco','CHO',1),(733,47,'Cordoba','COR',1),(734,47,'Cundinamarca','CAM',1),(735,47,'Guainia','GNA',1),(736,47,'Guajira','GJR',1),(737,47,'Guaviare','GVR',1),(738,47,'Huila','HUI',1),(739,47,'Magdalena','MAG',1),(740,47,'Meta','MET',1),(741,47,'Narino','NAR',1),(742,47,'Norte de Santander','NDS',1),(743,47,'Putumayo','PUT',1),(744,47,'Quindio','QUI',1),(745,47,'Risaralda','RIS',1),(746,47,'San Andres y Providencia','SAP',1),(747,47,'Santander','SAN',1),(748,47,'Sucre','SUC',1),(749,47,'Tolima','TOL',1),(750,47,'Valle del Cauca','VDC',1),(751,47,'Vaupes','VAU',1),(752,47,'Vichada','VIC',1),(753,48,'Grande Comore','G',1),(754,48,'Anjouan','A',1),(755,48,'Moheli','M',1),(756,49,'Bouenza','BO',1),(757,49,'Brazzaville','BR',1),(758,49,'Cuvette','CU',1),(759,49,'Cuvette-Ouest','CO',1),(760,49,'Kouilou','KO',1),(761,49,'Lekoumou','LE',1),(762,49,'Likouala','LI',1),(763,49,'Niari','NI',1),(764,49,'Plateaux','PL',1),(765,49,'Pool','PO',1),(766,49,'Sangha','SA',1),(767,50,'Pukapuka','PU',1),(768,50,'Rakahanga','RK',1),(769,50,'Manihiki','MK',1),(770,50,'Penrhyn','PE',1),(771,50,'Nassau Island','NI',1),(772,50,'Surwarrow','SU',1),(773,50,'Palmerston','PA',1),(774,50,'Aitutaki','AI',1),(775,50,'Manuae','MA',1),(776,50,'Takutea','TA',1),(777,50,'Mitiaro','MT',1),(778,50,'Atiu','AT',1),(779,50,'Mauke','MU',1),(780,50,'Rarotonga','RR',1),(781,50,'Mangaia','MG',1),(782,51,'Alajuela','AL',1),(783,51,'Cartago','CA',1),(784,51,'Guanacaste','GU',1),(785,51,'Heredia','HE',1),(786,51,'Limon','LI',1),(787,51,'Puntarenas','PU',1),(788,51,'San Jose','SJ',1),(789,52,'Abengourou','ABE',1),(790,52,'Abidjan','ABI',1),(791,52,'Aboisso','ABO',1),(792,52,'Adiake','ADI',1),(793,52,'Adzope','ADZ',1),(794,52,'Agboville','AGB',1),(795,52,'Agnibilekrou','AGN',1),(796,52,'Alepe','ALE',1),(797,52,'Bocanda','BOC',1),(798,52,'Bangolo','BAN',1),(799,52,'Beoumi','BEO',1),(800,52,'Biankouma','BIA',1),(801,52,'Bondoukou','BDK',1),(802,52,'Bongouanou','BGN',1),(803,52,'Bouafle','BFL',1),(804,52,'Bouake','BKE',1),(805,52,'Bouna','BNA',1),(806,52,'Boundiali','BDL',1),(807,52,'Dabakala','DKL',1),(808,52,'Dabou','DBU',1),(809,52,'Daloa','DAL',1),(810,52,'Danane','DAN',1),(811,52,'Daoukro','DAO',1),(812,52,'Dimbokro','DIM',1),(813,52,'Divo','DIV',1),(814,52,'Duekoue','DUE',1),(815,52,'Ferkessedougou','FER',1),(816,52,'Gagnoa','GAG',1),(817,52,'Grand-Bassam','GBA',1),(818,52,'Grand-Lahou','GLA',1),(819,52,'Guiglo','GUI',1),(820,52,'Issia','ISS',1),(821,52,'Jacqueville','JAC',1),(822,52,'Katiola','KAT',1),(823,52,'Korhogo','KOR',1),(824,52,'Lakota','LAK',1),(825,52,'Man','MAN',1),(826,52,'Mankono','MKN',1),(827,52,'Mbahiakro','MBA',1),(828,52,'Odienne','ODI',1),(829,52,'Oume','OUM',1),(830,52,'Sakassou','SAK',1),(831,52,'San-Pedro','SPE',1),(832,52,'Sassandra','SAS',1),(833,52,'Seguela','SEG',1),(834,52,'Sinfra','SIN',1),(835,52,'Soubre','SOU',1),(836,52,'Tabou','TAB',1),(837,52,'Tanda','TAN',1),(838,52,'Tiebissou','TIE',1),(839,52,'Tingrela','TIN',1),(840,52,'Tiassale','TIA',1),(841,52,'Touba','TBA',1),(842,52,'Toulepleu','TLP',1),(843,52,'Toumodi','TMD',1),(844,52,'Vavoua','VAV',1),(845,52,'Yamoussoukro','YAM',1),(846,52,'Zuenoula','ZUE',1),(847,53,'Bjelovarsko-bilogorska','BB',1),(848,53,'Grad Zagreb','GZ',1),(849,53,'Dubrovačko-neretvanska','DN',1),(850,53,'Istarska','IS',1),(851,53,'Karlovačka','KA',1),(852,53,'Koprivničko-križevačka','KK',1),(853,53,'Krapinsko-zagorska','KZ',1),(854,53,'Ličko-senjska','LS',1),(855,53,'Međimurska','ME',1),(856,53,'Osječko-baranjska','OB',1),(857,53,'Požeško-slavonska','PS',1),(858,53,'Primorsko-goranska','PG',1),(859,53,'Šibensko-kninska','SK',1),(860,53,'Sisačko-moslavačka','SM',1),(861,53,'Brodsko-posavska','BP',1),(862,53,'Splitsko-dalmatinska','SD',1),(863,53,'Varaždinska','VA',1),(864,53,'Virovitičko-podravska','VP',1),(865,53,'Vukovarsko-srijemska','VS',1),(866,53,'Zadarska','ZA',1),(867,53,'Zagrebačka','ZG',1),(868,54,'Camaguey','CA',1),(869,54,'Ciego de Avila','CD',1),(870,54,'Cienfuegos','CI',1),(871,54,'Ciudad de La Habana','CH',1),(872,54,'Granma','GR',1),(873,54,'Guantanamo','GU',1),(874,54,'Holguin','HO',1),(875,54,'Isla de la Juventud','IJ',1),(876,54,'La Habana','LH',1),(877,54,'Las Tunas','LT',1),(878,54,'Matanzas','MA',1),(879,54,'Pinar del Rio','PR',1),(880,54,'Sancti Spiritus','SS',1),(881,54,'Santiago de Cuba','SC',1),(882,54,'Villa Clara','VC',1),(883,55,'Famagusta','F',1),(884,55,'Kyrenia','K',1),(885,55,'Larnaca','A',1),(886,55,'Limassol','I',1),(887,55,'Nicosia','N',1),(888,55,'Paphos','P',1),(889,56,'Ústecký','U',1),(890,56,'Jihočeský','C',1),(891,56,'Jihomoravský','B',1),(892,56,'Karlovarský','K',1),(893,56,'Královehradecký','H',1),(894,56,'Liberecký','L',1),(895,56,'Moravskoslezský','T',1),(896,56,'Olomoucký','M',1),(897,56,'Pardubický','E',1),(898,56,'Plzeňský','P',1),(899,56,'Praha','A',1),(900,56,'Středočeský','S',1),(901,56,'Vysočina','J',1),(902,56,'Zlínský','Z',1),(903,57,'Arhus','AR',1),(904,57,'Bornholm','BH',1),(905,57,'Copenhagen','CO',1),(906,57,'Faroe Islands','FO',1),(907,57,'Frederiksborg','FR',1),(908,57,'Fyn','FY',1),(909,57,'Kobenhavn','KO',1),(910,57,'Nordjylland','NO',1),(911,57,'Ribe','RI',1),(912,57,'Ringkobing','RK',1),(913,57,'Roskilde','RO',1),(914,57,'Sonderjylland','SO',1),(915,57,'Storstrom','ST',1),(916,57,'Vejle','VK',1),(917,57,'Vestj&aelig;lland','VJ',1),(918,57,'Viborg','VB',1),(919,58,'\'Ali Sabih','S',1),(920,58,'Dikhil','K',1),(921,58,'Djibouti','J',1),(922,58,'Obock','O',1),(923,58,'Tadjoura','T',1),(924,59,'Saint Andrew Parish','AND',1),(925,59,'Saint David Parish','DAV',1),(926,59,'Saint George Parish','GEO',1),(927,59,'Saint John Parish','JOH',1),(928,59,'Saint Joseph Parish','JOS',1),(929,59,'Saint Luke Parish','LUK',1),(930,59,'Saint Mark Parish','MAR',1),(931,59,'Saint Patrick Parish','PAT',1),(932,59,'Saint Paul Parish','PAU',1),(933,59,'Saint Peter Parish','PET',1),(934,60,'Distrito Nacional','DN',1),(935,60,'Azua','AZ',1),(936,60,'Baoruco','BC',1),(937,60,'Barahona','BH',1),(938,60,'Dajabon','DJ',1),(939,60,'Duarte','DU',1),(940,60,'Elias Pina','EL',1),(941,60,'El Seybo','SY',1),(942,60,'Espaillat','ET',1),(943,60,'Hato Mayor','HM',1),(944,60,'Independencia','IN',1),(945,60,'La Altagracia','AL',1),(946,60,'La Romana','RO',1),(947,60,'La Vega','VE',1),(948,60,'Maria Trinidad Sanchez','MT',1),(949,60,'Monsenor Nouel','MN',1),(950,60,'Monte Cristi','MC',1),(951,60,'Monte Plata','MP',1),(952,60,'Pedernales','PD',1),(953,60,'Peravia (Bani)','PR',1),(954,60,'Puerto Plata','PP',1),(955,60,'Salcedo','SL',1),(956,60,'Samana','SM',1),(957,60,'Sanchez Ramirez','SH',1),(958,60,'San Cristobal','SC',1),(959,60,'San Jose de Ocoa','JO',1),(960,60,'San Juan','SJ',1),(961,60,'San Pedro de Macoris','PM',1),(962,60,'Santiago','SA',1),(963,60,'Santiago Rodriguez','ST',1),(964,60,'Santo Domingo','SD',1),(965,60,'Valverde','VA',1),(966,61,'Aileu','AL',1),(967,61,'Ainaro','AN',1),(968,61,'Baucau','BA',1),(969,61,'Bobonaro','BO',1),(970,61,'Cova Lima','CO',1),(971,61,'Dili','DI',1),(972,61,'Ermera','ER',1),(973,61,'Lautem','LA',1),(974,61,'Liquica','LI',1),(975,61,'Manatuto','MT',1),(976,61,'Manufahi','MF',1),(977,61,'Oecussi','OE',1),(978,61,'Viqueque','VI',1),(979,62,'Azuay','AZU',1),(980,62,'Bolivar','BOL',1),(981,62,'Ca&ntilde;ar','CAN',1),(982,62,'Carchi','CAR',1),(983,62,'Chimborazo','CHI',1),(984,62,'Cotopaxi','COT',1),(985,62,'El Oro','EOR',1),(986,62,'Esmeraldas','ESM',1),(987,62,'Gal&aacute;pagos','GPS',1),(988,62,'Guayas','GUA',1),(989,62,'Imbabura','IMB',1),(990,62,'Loja','LOJ',1),(991,62,'Los Rios','LRO',1),(992,62,'Manab&iacute;','MAN',1),(993,62,'Morona Santiago','MSA',1),(994,62,'Napo','NAP',1),(995,62,'Orellana','ORE',1),(996,62,'Pastaza','PAS',1),(997,62,'Pichincha','PIC',1),(998,62,'Sucumb&iacute;os','SUC',1),(999,62,'Tungurahua','TUN',1),(1000,62,'Zamora Chinchipe','ZCH',1),(1001,63,'Ad Daqahliyah','DHY',1),(1002,63,'Al Bahr al Ahmar','BAM',1),(1003,63,'Al Buhayrah','BHY',1),(1004,63,'Al Fayyum','FYM',1),(1005,63,'Al Gharbiyah','GBY',1),(1006,63,'Al Iskandariyah','IDR',1),(1007,63,'Al Isma\'iliyah','IML',1),(1008,63,'Al Jizah','JZH',1),(1009,63,'Al Minufiyah','MFY',1),(1010,63,'Al Minya','MNY',1),(1011,63,'Al Qahirah','QHR',1),(1012,63,'Al Qalyubiyah','QLY',1),(1013,63,'Al Wadi al Jadid','WJD',1),(1014,63,'Ash Sharqiyah','SHQ',1),(1015,63,'As Suways','SWY',1),(1016,63,'Aswan','ASW',1),(1017,63,'Asyut','ASY',1),(1018,63,'Bani Suwayf','BSW',1),(1019,63,'Bur Sa\'id','BSD',1),(1020,63,'Dumyat','DMY',1),(1021,63,'Janub Sina\'','JNS',1),(1022,63,'Kafr ash Shaykh','KSH',1),(1023,63,'Matruh','MAT',1),(1024,63,'Qina','QIN',1),(1025,63,'Shamal Sina\'','SHS',1),(1026,63,'Suhaj','SUH',1),(1027,64,'Ahuachapan','AH',1),(1028,64,'Cabanas','CA',1),(1029,64,'Chalatenango','CH',1),(1030,64,'Cuscatlan','CU',1),(1031,64,'La Libertad','LB',1),(1032,64,'La Paz','PZ',1),(1033,64,'La Union','UN',1),(1034,64,'Morazan','MO',1),(1035,64,'San Miguel','SM',1),(1036,64,'San Salvador','SS',1),(1037,64,'San Vicente','SV',1),(1038,64,'Santa Ana','SA',1),(1039,64,'Sonsonate','SO',1),(1040,64,'Usulutan','US',1),(1041,65,'Provincia Annobon','AN',1),(1042,65,'Provincia Bioko Norte','BN',1),(1043,65,'Provincia Bioko Sur','BS',1),(1044,65,'Provincia Centro Sur','CS',1),(1045,65,'Provincia Kie-Ntem','KN',1),(1046,65,'Provincia Litoral','LI',1),(1047,65,'Provincia Wele-Nzas','WN',1),(1048,66,'Central (Maekel)','MA',1),(1049,66,'Anseba (Keren)','KE',1),(1050,66,'Southern Red Sea (Debub-Keih-Bahri)','DK',1),(1051,66,'Northern Red Sea (Semien-Keih-Bahri)','SK',1),(1052,66,'Southern (Debub)','DE',1),(1053,66,'Gash-Barka (Barentu)','BR',1),(1054,67,'Harjumaa (Tallinn)','HA',1),(1055,67,'Hiiumaa (Kardla)','HI',1),(1056,67,'Ida-Virumaa (Johvi)','IV',1),(1057,67,'Jarvamaa (Paide)','JA',1),(1058,67,'Jogevamaa (Jogeva)','JO',1),(1059,67,'Laane-Virumaa (Rakvere)','LV',1),(1060,67,'Laanemaa (Haapsalu)','LA',1),(1061,67,'Parnumaa (Parnu)','PA',1),(1062,67,'Polvamaa (Polva)','PO',1),(1063,67,'Raplamaa (Rapla)','RA',1),(1064,67,'Saaremaa (Kuessaare)','SA',1),(1065,67,'Tartumaa (Tartu)','TA',1),(1066,67,'Valgamaa (Valga)','VA',1),(1067,67,'Viljandimaa (Viljandi)','VI',1),(1068,67,'Vorumaa (Voru)','VO',1),(1069,68,'Afar','AF',1),(1070,68,'Amhara','AH',1),(1071,68,'Benishangul-Gumaz','BG',1),(1072,68,'Gambela','GB',1),(1073,68,'Hariai','HR',1),(1074,68,'Oromia','OR',1),(1075,68,'Somali','SM',1),(1076,68,'Southern Nations - Nationalities and Peoples Region','SN',1),(1077,68,'Tigray','TG',1),(1078,68,'Addis Ababa','AA',1),(1079,68,'Dire Dawa','DD',1),(1080,71,'Central Division','C',1),(1081,71,'Northern Division','N',1),(1082,71,'Eastern Division','E',1),(1083,71,'Western Division','W',1),(1084,71,'Rotuma','R',1),(1085,72,'Ahvenanmaan lääni','AL',1),(1086,72,'Etelä-Suomen lääni','ES',1),(1087,72,'Itä-Suomen lääni','IS',1),(1088,72,'Länsi-Suomen lääni','LS',1),(1089,72,'Lapin lääni','LA',1),(1090,72,'Oulun lääni','OU',1),(1114,74,'Ain','01',1),(1115,74,'Aisne','02',1),(1116,74,'Allier','03',1),(1117,74,'Alpes de Haute Provence','04',1),(1118,74,'Hautes-Alpes','05',1),(1119,74,'Alpes Maritimes','06',1),(1120,74,'Ard&egrave;che','07',1),(1121,74,'Ardennes','08',1),(1122,74,'Ari&egrave;ge','09',1),(1123,74,'Aube','10',1),(1124,74,'Aude','11',1),(1125,74,'Aveyron','12',1),(1126,74,'Bouches du Rh&ocirc;ne','13',1),(1127,74,'Calvados','14',1),(1128,74,'Cantal','15',1),(1129,74,'Charente','16',1),(1130,74,'Charente Maritime','17',1),(1131,74,'Cher','18',1),(1132,74,'Corr&egrave;ze','19',1),(1133,74,'Corse du Sud','2A',1),(1134,74,'Haute Corse','2B',1),(1135,74,'C&ocirc;te d&#039;or','21',1),(1136,74,'C&ocirc;tes d&#039;Armor','22',1),(1137,74,'Creuse','23',1),(1138,74,'Dordogne','24',1),(1139,74,'Doubs','25',1),(1140,74,'Dr&ocirc;me','26',1),(1141,74,'Eure','27',1),(1142,74,'Eure et Loir','28',1),(1143,74,'Finist&egrave;re','29',1),(1144,74,'Gard','30',1),(1145,74,'Haute Garonne','31',1),(1146,74,'Gers','32',1),(1147,74,'Gironde','33',1),(1148,74,'H&eacute;rault','34',1),(1149,74,'Ille et Vilaine','35',1),(1150,74,'Indre','36',1),(1151,74,'Indre et Loire','37',1),(1152,74,'Is&eacute;re','38',1),(1153,74,'Jura','39',1),(1154,74,'Landes','40',1),(1155,74,'Loir et Cher','41',1),(1156,74,'Loire','42',1),(1157,74,'Haute Loire','43',1),(1158,74,'Loire Atlantique','44',1),(1159,74,'Loiret','45',1),(1160,74,'Lot','46',1),(1161,74,'Lot et Garonne','47',1),(1162,74,'Loz&egrave;re','48',1),(1163,74,'Maine et Loire','49',1),(1164,74,'Manche','50',1),(1165,74,'Marne','51',1),(1166,74,'Haute Marne','52',1),(1167,74,'Mayenne','53',1),(1168,74,'Meurthe et Moselle','54',1),(1169,74,'Meuse','55',1),(1170,74,'Morbihan','56',1),(1171,74,'Moselle','57',1),(1172,74,'Ni&egrave;vre','58',1),(1173,74,'Nord','59',1),(1174,74,'Oise','60',1),(1175,74,'Orne','61',1),(1176,74,'Pas de Calais','62',1),(1177,74,'Puy de D&ocirc;me','63',1),(1178,74,'Pyr&eacute;n&eacute;es Atlantiques','64',1),(1179,74,'Hautes Pyr&eacute;n&eacute;es','65',1),(1180,74,'Pyr&eacute;n&eacute;es Orientales','66',1),(1181,74,'Bas Rhin','67',1),(1182,74,'Haut Rhin','68',1),(1183,74,'Rh&ocirc;ne','69',1),(1184,74,'Haute Sa&ocirc;ne','70',1),(1185,74,'Sa&ocirc;ne et Loire','71',1),(1186,74,'Sarthe','72',1),(1187,74,'Savoie','73',1),(1188,74,'Haute Savoie','74',1),(1189,74,'Paris','75',1),(1190,74,'Seine Maritime','76',1),(1191,74,'Seine et Marne','77',1),(1192,74,'Yvelines','78',1),(1193,74,'Deux S&egrave;vres','79',1),(1194,74,'Somme','80',1),(1195,74,'Tarn','81',1),(1196,74,'Tarn et Garonne','82',1),(1197,74,'Var','83',1),(1198,74,'Vaucluse','84',1),(1199,74,'Vend&eacute;e','85',1),(1200,74,'Vienne','86',1),(1201,74,'Haute Vienne','87',1),(1202,74,'Vosges','88',1),(1203,74,'Yonne','89',1),(1204,74,'Territoire de Belfort','90',1),(1205,74,'Essonne','91',1),(1206,74,'Hauts de Seine','92',1),(1207,74,'Seine St-Denis','93',1),(1208,74,'Val de Marne','94',1),(1209,74,'Val d\'Oise','95',1),(1210,76,'Archipel des Marquises','M',1),(1211,76,'Archipel des Tuamotu','T',1),(1212,76,'Archipel des Tubuai','I',1),(1213,76,'Iles du Vent','V',1),(1214,76,'Iles Sous-le-Vent','S',1),(1215,77,'Iles Crozet','C',1),(1216,77,'Iles Kerguelen','K',1),(1217,77,'Ile Amsterdam','A',1),(1218,77,'Ile Saint-Paul','P',1),(1219,77,'Adelie Land','D',1),(1220,78,'Estuaire','ES',1),(1221,78,'Haut-Ogooue','HO',1),(1222,78,'Moyen-Ogooue','MO',1),(1223,78,'Ngounie','NG',1),(1224,78,'Nyanga','NY',1),(1225,78,'Ogooue-Ivindo','OI',1),(1226,78,'Ogooue-Lolo','OL',1),(1227,78,'Ogooue-Maritime','OM',1),(1228,78,'Woleu-Ntem','WN',1),(1229,79,'Banjul','BJ',1),(1230,79,'Basse','BS',1),(1231,79,'Brikama','BR',1),(1232,79,'Janjangbure','JA',1),(1233,79,'Kanifeng','KA',1),(1234,79,'Kerewan','KE',1),(1235,79,'Kuntaur','KU',1),(1236,79,'Mansakonko','MA',1),(1237,79,'Lower River','LR',1),(1238,79,'Central River','CR',1),(1239,79,'North Bank','NB',1),(1240,79,'Upper River','UR',1),(1241,79,'Western','WE',1),(1242,80,'Abkhazia','AB',1),(1243,80,'Ajaria','AJ',1),(1244,80,'Tbilisi','TB',1),(1245,80,'Guria','GU',1),(1246,80,'Imereti','IM',1),(1247,80,'Kakheti','KA',1),(1248,80,'Kvemo Kartli','KK',1),(1249,80,'Mtskheta-Mtianeti','MM',1),(1250,80,'Racha Lechkhumi and Kvemo Svanet','RL',1),(1251,80,'Samegrelo-Zemo Svaneti','SZ',1),(1252,80,'Samtskhe-Javakheti','SJ',1),(1253,80,'Shida Kartli','SK',1),(1254,81,'Baden-W&uuml;rttemberg','BAW',1),(1255,81,'Bayern','BAY',1),(1256,81,'Berlin','BER',1),(1257,81,'Brandenburg','BRG',1),(1258,81,'Bremen','BRE',1),(1259,81,'Hamburg','HAM',1),(1260,81,'Hessen','HES',1),(1261,81,'Mecklenburg-Vorpommern','MEC',1),(1262,81,'Niedersachsen','NDS',1),(1263,81,'Nordrhein-Westfalen','NRW',1),(1264,81,'Rheinland-Pfalz','RHE',1),(1265,81,'Saarland','SAR',1),(1266,81,'Sachsen','SAS',1),(1267,81,'Sachsen-Anhalt','SAC',1),(1268,81,'Schleswig-Holstein','SCN',1),(1269,81,'Th&uuml;ringen','THE',1),(1270,82,'Ashanti Region','AS',1),(1271,82,'Brong-Ahafo Region','BA',1),(1272,82,'Central Region','CE',1),(1273,82,'Eastern Region','EA',1),(1274,82,'Greater Accra Region','GA',1),(1275,82,'Northern Region','NO',1),(1276,82,'Upper East Region','UE',1),(1277,82,'Upper West Region','UW',1),(1278,82,'Volta Region','VO',1),(1279,82,'Western Region','WE',1),(1280,84,'Attica','AT',1),(1281,84,'Central Greece','CN',1),(1282,84,'Central Macedonia','CM',1),(1283,84,'Crete','CR',1),(1284,84,'East Macedonia and Thrace','EM',1),(1285,84,'Epirus','EP',1),(1286,84,'Ionian Islands','II',1),(1287,84,'North Aegean','NA',1),(1288,84,'Peloponnesos','PP',1),(1289,84,'South Aegean','SA',1),(1290,84,'Thessaly','TH',1),(1291,84,'West Greece','WG',1),(1292,84,'West Macedonia','WM',1),(1293,85,'Avannaa','A',1),(1294,85,'Tunu','T',1),(1295,85,'Kitaa','K',1),(1296,86,'Saint Andrew','A',1),(1297,86,'Saint David','D',1),(1298,86,'Saint George','G',1),(1299,86,'Saint John','J',1),(1300,86,'Saint Mark','M',1),(1301,86,'Saint Patrick','P',1),(1302,86,'Carriacou','C',1),(1303,86,'Petit Martinique','Q',1),(1304,89,'Alta Verapaz','AV',1),(1305,89,'Baja Verapaz','BV',1),(1306,89,'Chimaltenango','CM',1),(1307,89,'Chiquimula','CQ',1),(1308,89,'El Peten','PE',1),(1309,89,'El Progreso','PR',1),(1310,89,'El Quiche','QC',1),(1311,89,'Escuintla','ES',1),(1312,89,'Guatemala','GU',1),(1313,89,'Huehuetenango','HU',1),(1314,89,'Izabal','IZ',1),(1315,89,'Jalapa','JA',1),(1316,89,'Jutiapa','JU',1),(1317,89,'Quetzaltenango','QZ',1),(1318,89,'Retalhuleu','RE',1),(1319,89,'Sacatepequez','ST',1),(1320,89,'San Marcos','SM',1),(1321,89,'Santa Rosa','SR',1),(1322,89,'Solola','SO',1),(1323,89,'Suchitepequez','SU',1),(1324,89,'Totonicapan','TO',1),(1325,89,'Zacapa','ZA',1),(1326,90,'Conakry','CNK',1),(1327,90,'Beyla','BYL',1),(1328,90,'Boffa','BFA',1),(1329,90,'Boke','BOK',1),(1330,90,'Coyah','COY',1),(1331,90,'Dabola','DBL',1),(1332,90,'Dalaba','DLB',1),(1333,90,'Dinguiraye','DGR',1),(1334,90,'Dubreka','DBR',1),(1335,90,'Faranah','FRN',1),(1336,90,'Forecariah','FRC',1),(1337,90,'Fria','FRI',1),(1338,90,'Gaoual','GAO',1),(1339,90,'Gueckedou','GCD',1),(1340,90,'Kankan','KNK',1),(1341,90,'Kerouane','KRN',1),(1342,90,'Kindia','KND',1),(1343,90,'Kissidougou','KSD',1),(1344,90,'Koubia','KBA',1),(1345,90,'Koundara','KDA',1),(1346,90,'Kouroussa','KRA',1),(1347,90,'Labe','LAB',1),(1348,90,'Lelouma','LLM',1),(1349,90,'Lola','LOL',1),(1350,90,'Macenta','MCT',1),(1351,90,'Mali','MAL',1),(1352,90,'Mamou','MAM',1),(1353,90,'Mandiana','MAN',1),(1354,90,'Nzerekore','NZR',1),(1355,90,'Pita','PIT',1),(1356,90,'Siguiri','SIG',1),(1357,90,'Telimele','TLM',1),(1358,90,'Tougue','TOG',1),(1359,90,'Yomou','YOM',1),(1360,91,'Bafata Region','BF',1),(1361,91,'Biombo Region','BB',1),(1362,91,'Bissau Region','BS',1),(1363,91,'Bolama Region','BL',1),(1364,91,'Cacheu Region','CA',1),(1365,91,'Gabu Region','GA',1),(1366,91,'Oio Region','OI',1),(1367,91,'Quinara Region','QU',1),(1368,91,'Tombali Region','TO',1),(1369,92,'Barima-Waini','BW',1),(1370,92,'Cuyuni-Mazaruni','CM',1),(1371,92,'Demerara-Mahaica','DM',1),(1372,92,'East Berbice-Corentyne','EC',1),(1373,92,'Essequibo Islands-West Demerara','EW',1),(1374,92,'Mahaica-Berbice','MB',1),(1375,92,'Pomeroon-Supenaam','PM',1),(1376,92,'Potaro-Siparuni','PI',1),(1377,92,'Upper Demerara-Berbice','UD',1),(1378,92,'Upper Takutu-Upper Essequibo','UT',1),(1379,93,'Artibonite','AR',1),(1380,93,'Centre','CE',1),(1381,93,'Grand\'Anse','GA',1),(1382,93,'Nord','ND',1),(1383,93,'Nord-Est','NE',1),(1384,93,'Nord-Ouest','NO',1),(1385,93,'Ouest','OU',1),(1386,93,'Sud','SD',1),(1387,93,'Sud-Est','SE',1),(1388,94,'Flat Island','F',1),(1389,94,'McDonald Island','M',1),(1390,94,'Shag Island','S',1),(1391,94,'Heard Island','H',1),(1392,95,'Atlantida','AT',1),(1393,95,'Choluteca','CH',1),(1394,95,'Colon','CL',1),(1395,95,'Comayagua','CM',1),(1396,95,'Copan','CP',1),(1397,95,'Cortes','CR',1),(1398,95,'El Paraiso','PA',1),(1399,95,'Francisco Morazan','FM',1),(1400,95,'Gracias a Dios','GD',1),(1401,95,'Intibuca','IN',1),(1402,95,'Islas de la Bahia (Bay Islands)','IB',1),(1403,95,'La Paz','PZ',1),(1404,95,'Lempira','LE',1),(1405,95,'Ocotepeque','OC',1),(1406,95,'Olancho','OL',1),(1407,95,'Santa Barbara','SB',1),(1408,95,'Valle','VA',1),(1409,95,'Yoro','YO',1),(1410,96,'Central and Western Hong Kong Island','HCW',1),(1411,96,'Eastern Hong Kong Island','HEA',1),(1412,96,'Southern Hong Kong Island','HSO',1),(1413,96,'Wan Chai Hong Kong Island','HWC',1),(1414,96,'Kowloon City Kowloon','KKC',1),(1415,96,'Kwun Tong Kowloon','KKT',1),(1416,96,'Sham Shui Po Kowloon','KSS',1),(1417,96,'Wong Tai Sin Kowloon','KWT',1),(1418,96,'Yau Tsim Mong Kowloon','KYT',1),(1419,96,'Islands New Territories','NIS',1),(1420,96,'Kwai Tsing New Territories','NKT',1),(1421,96,'North New Territories','NNO',1),(1422,96,'Sai Kung New Territories','NSK',1),(1423,96,'Sha Tin New Territories','NST',1),(1424,96,'Tai Po New Territories','NTP',1),(1425,96,'Tsuen Wan New Territories','NTW',1),(1426,96,'Tuen Mun New Territories','NTM',1),(1427,96,'Yuen Long New Territories','NYL',1),(1467,98,'Austurland','AL',1),(1468,98,'Hofuoborgarsvaeoi','HF',1),(1469,98,'Norourland eystra','NE',1),(1470,98,'Norourland vestra','NV',1),(1471,98,'Suourland','SL',1),(1472,98,'Suournes','SN',1),(1473,98,'Vestfiroir','VF',1),(1474,98,'Vesturland','VL',1),(1475,99,'Andaman and Nicobar Islands','AN',1),(1476,99,'Andhra Pradesh','AP',1),(1477,99,'Arunachal Pradesh','AR',1),(1478,99,'Assam','AS',1),(1479,99,'Bihar','BI',1),(1480,99,'Chandigarh','CH',1),(1481,99,'Dadra and Nagar Haveli','DA',1),(1482,99,'Daman and Diu','DM',1),(1483,99,'Delhi','DE',1),(1484,99,'Goa','GO',1),(1485,99,'Gujarat','GU',1),(1486,99,'Haryana','HA',1),(1487,99,'Himachal Pradesh','HP',1),(1488,99,'Jammu and Kashmir','JA',1),(1489,99,'Karnataka','KA',1),(1490,99,'Kerala','KE',1),(1491,99,'Lakshadweep Islands','LI',1),(1492,99,'Madhya Pradesh','MP',1),(1493,99,'Maharashtra','MA',1),(1494,99,'Manipur','MN',1),(1495,99,'Meghalaya','ME',1),(1496,99,'Mizoram','MI',1),(1497,99,'Nagaland','NA',1),(1498,99,'Orissa','OR',1),(1499,99,'Pondicherry','PO',1),(1500,99,'Punjab','PU',1),(1501,99,'Rajasthan','RA',1),(1502,99,'Sikkim','SI',1),(1503,99,'Tamil Nadu','TN',1),(1504,99,'Tripura','TR',1),(1505,99,'Uttar Pradesh','UP',1),(1506,99,'West Bengal','WB',1),(1507,100,'Aceh','AC',1),(1508,100,'Bali','BA',1),(1509,100,'Banten','BT',1),(1510,100,'Bengkulu','BE',1),(1511,100,'BoDeTaBek','BD',1),(1512,100,'Gorontalo','GO',1),(1513,100,'Jakarta Raya','JK',1),(1514,100,'Jambi','JA',1),(1515,100,'Jawa Barat','JB',1),(1516,100,'Jawa Tengah','JT',1),(1517,100,'Jawa Timur','JI',1),(1518,100,'Kalimantan Barat','KB',1),(1519,100,'Kalimantan Selatan','KS',1),(1520,100,'Kalimantan Tengah','KT',1),(1521,100,'Kalimantan Timur','KI',1),(1522,100,'Kepulauan Bangka Belitung','BB',1),(1523,100,'Lampung','LA',1),(1524,100,'Maluku','MA',1),(1525,100,'Maluku Utara','MU',1),(1526,100,'Nusa Tenggara Barat','NB',1),(1527,100,'Nusa Tenggara Timur','NT',1),(1528,100,'Papua','PA',1),(1529,100,'Riau','RI',1),(1530,100,'Sulawesi Selatan','SN',1),(1531,100,'Sulawesi Tengah','ST',1),(1532,100,'Sulawesi Tenggara','SG',1),(1533,100,'Sulawesi Utara','SA',1),(1534,100,'Sumatera Barat','SB',1),(1535,100,'Sumatera Selatan','SS',1),(1536,100,'Sumatera Utara','SU',1),(1537,100,'Yogyakarta','YO',1),(1538,101,'Tehran','TEH',1),(1539,101,'Qom','QOM',1),(1540,101,'Markazi','MKZ',1),(1541,101,'Qazvin','QAZ',1),(1542,101,'Gilan','GIL',1),(1543,101,'Ardabil','ARD',1),(1544,101,'Zanjan','ZAN',1),(1545,101,'East Azarbaijan','EAZ',1),(1546,101,'West Azarbaijan','WEZ',1),(1547,101,'Kurdistan','KRD',1),(1548,101,'Hamadan','HMD',1),(1549,101,'Kermanshah','KRM',1),(1550,101,'Ilam','ILM',1),(1551,101,'Lorestan','LRS',1),(1552,101,'Khuzestan','KZT',1),(1553,101,'Chahar Mahaal and Bakhtiari','CMB',1),(1554,101,'Kohkiluyeh and Buyer Ahmad','KBA',1),(1555,101,'Bushehr','BSH',1),(1556,101,'Fars','FAR',1),(1557,101,'Hormozgan','HRM',1),(1558,101,'Sistan and Baluchistan','SBL',1),(1559,101,'Kerman','KRB',1),(1560,101,'Yazd','YZD',1),(1561,101,'Esfahan','EFH',1),(1562,101,'Semnan','SMN',1),(1563,101,'Mazandaran','MZD',1),(1564,101,'Golestan','GLS',1),(1565,101,'North Khorasan','NKH',1),(1566,101,'Razavi Khorasan','RKH',1),(1567,101,'South Khorasan','SKH',1),(1568,102,'Baghdad','BD',1),(1569,102,'Salah ad Din','SD',1),(1570,102,'Diyala','DY',1),(1571,102,'Wasit','WS',1),(1572,102,'Maysan','MY',1),(1573,102,'Al Basrah','BA',1),(1574,102,'Dhi Qar','DQ',1),(1575,102,'Al Muthanna','MU',1),(1576,102,'Al Qadisyah','QA',1),(1577,102,'Babil','BB',1),(1578,102,'Al Karbala','KB',1),(1579,102,'An Najaf','NJ',1),(1580,102,'Al Anbar','AB',1),(1581,102,'Ninawa','NN',1),(1582,102,'Dahuk','DH',1),(1583,102,'Arbil','AL',1),(1584,102,'At Ta\'mim','TM',1),(1585,102,'As Sulaymaniyah','SL',1),(1586,103,'Carlow','CA',1),(1587,103,'Cavan','CV',1),(1588,103,'Clare','CL',1),(1589,103,'Cork','CO',1),(1590,103,'Donegal','DO',1),(1591,103,'Dublin','DU',1),(1592,103,'Galway','GA',1),(1593,103,'Kerry','KE',1),(1594,103,'Kildare','KI',1),(1595,103,'Kilkenny','KL',1),(1596,103,'Laois','LA',1),(1597,103,'Leitrim','LE',1),(1598,103,'Limerick','LI',1),(1599,103,'Longford','LO',1),(1600,103,'Louth','LU',1),(1601,103,'Mayo','MA',1),(1602,103,'Meath','ME',1),(1603,103,'Monaghan','MO',1),(1604,103,'Offaly','OF',1),(1605,103,'Roscommon','RO',1),(1606,103,'Sligo','SL',1),(1607,103,'Tipperary','TI',1),(1608,103,'Waterford','WA',1),(1609,103,'Westmeath','WE',1),(1610,103,'Wexford','WX',1),(1611,103,'Wicklow','WI',1),(1612,104,'Be\'er Sheva','BS',1),(1613,104,'Bika\'at Hayarden','BH',1),(1614,104,'Eilat and Arava','EA',1),(1615,104,'Galil','GA',1),(1616,104,'Haifa','HA',1),(1617,104,'Jehuda Mountains','JM',1),(1618,104,'Jerusalem','JE',1),(1619,104,'Negev','NE',1),(1620,104,'Semaria','SE',1),(1621,104,'Sharon','SH',1),(1622,104,'Tel Aviv (Gosh Dan)','TA',1),(1643,106,'Clarendon Parish','CLA',1),(1644,106,'Hanover Parish','HAN',1),(1645,106,'Kingston Parish','KIN',1),(1646,106,'Manchester Parish','MAN',1),(1647,106,'Portland Parish','POR',1),(1648,106,'Saint Andrew Parish','AND',1),(1649,106,'Saint Ann Parish','ANN',1),(1650,106,'Saint Catherine Parish','CAT',1),(1651,106,'Saint Elizabeth Parish','ELI',1),(1652,106,'Saint James Parish','JAM',1),(1653,106,'Saint Mary Parish','MAR',1),(1654,106,'Saint Thomas Parish','THO',1),(1655,106,'Trelawny Parish','TRL',1),(1656,106,'Westmoreland Parish','WML',1),(1657,107,'Aichi','AI',1),(1658,107,'Akita','AK',1),(1659,107,'Aomori','AO',1),(1660,107,'Chiba','CH',1),(1661,107,'Ehime','EH',1),(1662,107,'Fukui','FK',1),(1663,107,'Fukuoka','FU',1),(1664,107,'Fukushima','FS',1),(1665,107,'Gifu','GI',1),(1666,107,'Gumma','GU',1),(1667,107,'Hiroshima','HI',1),(1668,107,'Hokkaido','HO',1),(1669,107,'Hyogo','HY',1),(1670,107,'Ibaraki','IB',1),(1671,107,'Ishikawa','IS',1),(1672,107,'Iwate','IW',1),(1673,107,'Kagawa','KA',1),(1674,107,'Kagoshima','KG',1),(1675,107,'Kanagawa','KN',1),(1676,107,'Kochi','KO',1),(1677,107,'Kumamoto','KU',1),(1678,107,'Kyoto','KY',1),(1679,107,'Mie','MI',1),(1680,107,'Miyagi','MY',1),(1681,107,'Miyazaki','MZ',1),(1682,107,'Nagano','NA',1),(1683,107,'Nagasaki','NG',1),(1684,107,'Nara','NR',1),(1685,107,'Niigata','NI',1),(1686,107,'Oita','OI',1),(1687,107,'Okayama','OK',1),(1688,107,'Okinawa','ON',1),(1689,107,'Osaka','OS',1),(1690,107,'Saga','SA',1),(1691,107,'Saitama','SI',1),(1692,107,'Shiga','SH',1),(1693,107,'Shimane','SM',1),(1694,107,'Shizuoka','SZ',1),(1695,107,'Tochigi','TO',1),(1696,107,'Tokushima','TS',1),(1697,107,'Tokyo','TK',1),(1698,107,'Tottori','TT',1),(1699,107,'Toyama','TY',1),(1700,107,'Wakayama','WA',1),(1701,107,'Yamagata','YA',1),(1702,107,'Yamaguchi','YM',1),(1703,107,'Yamanashi','YN',1),(1704,108,'\'Amman','AM',1),(1705,108,'Ajlun','AJ',1),(1706,108,'Al \'Aqabah','AA',1),(1707,108,'Al Balqa\'','AB',1),(1708,108,'Al Karak','AK',1),(1709,108,'Al Mafraq','AL',1),(1710,108,'At Tafilah','AT',1),(1711,108,'Az Zarqa\'','AZ',1),(1712,108,'Irbid','IR',1),(1713,108,'Jarash','JA',1),(1714,108,'Ma\'an','MA',1),(1715,108,'Madaba','MD',1),(1716,109,'Almaty','AL',1),(1717,109,'Almaty City','AC',1),(1718,109,'Aqmola','AM',1),(1719,109,'Aqtobe','AQ',1),(1720,109,'Astana City','AS',1),(1721,109,'Atyrau','AT',1),(1722,109,'Batys Qazaqstan','BA',1),(1723,109,'Bayqongyr City','BY',1),(1724,109,'Mangghystau','MA',1),(1725,109,'Ongtustik Qazaqstan','ON',1),(1726,109,'Pavlodar','PA',1),(1727,109,'Qaraghandy','QA',1),(1728,109,'Qostanay','QO',1),(1729,109,'Qyzylorda','QY',1),(1730,109,'Shyghys Qazaqstan','SH',1),(1731,109,'Soltustik Qazaqstan','SO',1),(1732,109,'Zhambyl','ZH',1),(1733,110,'Central','CE',1),(1734,110,'Coast','CO',1),(1735,110,'Eastern','EA',1),(1736,110,'Nairobi Area','NA',1),(1737,110,'North Eastern','NE',1),(1738,110,'Nyanza','NY',1),(1739,110,'Rift Valley','RV',1),(1740,110,'Western','WE',1),(1741,111,'Abaiang','AG',1),(1742,111,'Abemama','AM',1),(1743,111,'Aranuka','AK',1),(1744,111,'Arorae','AO',1),(1745,111,'Banaba','BA',1),(1746,111,'Beru','BE',1),(1747,111,'Butaritari','bT',1),(1748,111,'Kanton','KA',1),(1749,111,'Kiritimati','KR',1),(1750,111,'Kuria','KU',1),(1751,111,'Maiana','MI',1),(1752,111,'Makin','MN',1),(1753,111,'Marakei','ME',1),(1754,111,'Nikunau','NI',1),(1755,111,'Nonouti','NO',1),(1756,111,'Onotoa','ON',1),(1757,111,'Tabiteuea','TT',1),(1758,111,'Tabuaeran','TR',1),(1759,111,'Tamana','TM',1),(1760,111,'Tarawa','TW',1),(1761,111,'Teraina','TE',1),(1762,112,'Chagang-do','CHA',1),(1763,112,'Hamgyong-bukto','HAB',1),(1764,112,'Hamgyong-namdo','HAN',1),(1765,112,'Hwanghae-bukto','HWB',1),(1766,112,'Hwanghae-namdo','HWN',1),(1767,112,'Kangwon-do','KAN',1),(1768,112,'P\'yongan-bukto','PYB',1),(1769,112,'P\'yongan-namdo','PYN',1),(1770,112,'Ryanggang-do (Yanggang-do)','YAN',1),(1771,112,'Rason Directly Governed City','NAJ',1),(1772,112,'P\'yongyang Special City','PYO',1),(1773,113,'Ch\'ungch\'ong-bukto','CO',1),(1774,113,'Ch\'ungch\'ong-namdo','CH',1),(1775,113,'Cheju-do','CD',1),(1776,113,'Cholla-bukto','CB',1),(1777,113,'Cholla-namdo','CN',1),(1778,113,'Inch\'on-gwangyoksi','IG',1),(1779,113,'Kangwon-do','KA',1),(1780,113,'Kwangju-gwangyoksi','KG',1),(1781,113,'Kyonggi-do','KD',1),(1782,113,'Kyongsang-bukto','KB',1),(1783,113,'Kyongsang-namdo','KN',1),(1784,113,'Pusan-gwangyoksi','PG',1),(1785,113,'Soul-t\'ukpyolsi','SO',1),(1786,113,'Taegu-gwangyoksi','TA',1),(1787,113,'Taejon-gwangyoksi','TG',1),(1788,114,'Al \'Asimah','AL',1),(1789,114,'Al Ahmadi','AA',1),(1790,114,'Al Farwaniyah','AF',1),(1791,114,'Al Jahra\'','AJ',1),(1792,114,'Hawalli','HA',1),(1793,115,'Bishkek','GB',1),(1794,115,'Batken','B',1),(1795,115,'Chu','C',1),(1796,115,'Jalal-Abad','J',1),(1797,115,'Naryn','N',1),(1798,115,'Osh','O',1),(1799,115,'Talas','T',1),(1800,115,'Ysyk-Kol','Y',1),(1801,116,'Vientiane','VT',1),(1802,116,'Attapu','AT',1),(1803,116,'Bokeo','BK',1),(1804,116,'Bolikhamxai','BL',1),(1805,116,'Champasak','CH',1),(1806,116,'Houaphan','HO',1),(1807,116,'Khammouan','KH',1),(1808,116,'Louang Namtha','LM',1),(1809,116,'Louangphabang','LP',1),(1810,116,'Oudomxai','OU',1),(1811,116,'Phongsali','PH',1),(1812,116,'Salavan','SL',1),(1813,116,'Savannakhet','SV',1),(1814,116,'Vientiane','VI',1),(1815,116,'Xaignabouli','XA',1),(1816,116,'Xekong','XE',1),(1817,116,'Xiangkhoang','XI',1),(1818,116,'Xaisomboun','XN',1),(1852,119,'Berea','BE',1),(1853,119,'Butha-Buthe','BB',1),(1854,119,'Leribe','LE',1),(1855,119,'Mafeteng','MF',1),(1856,119,'Maseru','MS',1),(1857,119,'Mohale\'s Hoek','MH',1),(1858,119,'Mokhotlong','MK',1),(1859,119,'Qacha\'s Nek','QN',1),(1860,119,'Quthing','QT',1),(1861,119,'Thaba-Tseka','TT',1),(1862,120,'Bomi','BI',1),(1863,120,'Bong','BG',1),(1864,120,'Grand Bassa','GB',1),(1865,120,'Grand Cape Mount','CM',1),(1866,120,'Grand Gedeh','GG',1),(1867,120,'Grand Kru','GK',1),(1868,120,'Lofa','LO',1),(1869,120,'Margibi','MG',1),(1870,120,'Maryland','ML',1),(1871,120,'Montserrado','MS',1),(1872,120,'Nimba','NB',1),(1873,120,'River Cess','RC',1),(1874,120,'Sinoe','SN',1),(1875,121,'Ajdabiya','AJ',1),(1876,121,'Al \'Aziziyah','AZ',1),(1877,121,'Al Fatih','FA',1),(1878,121,'Al Jabal al Akhdar','JA',1),(1879,121,'Al Jufrah','JU',1),(1880,121,'Al Khums','KH',1),(1881,121,'Al Kufrah','KU',1),(1882,121,'An Nuqat al Khams','NK',1),(1883,121,'Ash Shati\'','AS',1),(1884,121,'Awbari','AW',1),(1885,121,'Az Zawiyah','ZA',1),(1886,121,'Banghazi','BA',1),(1887,121,'Darnah','DA',1),(1888,121,'Ghadamis','GD',1),(1889,121,'Gharyan','GY',1),(1890,121,'Misratah','MI',1),(1891,121,'Murzuq','MZ',1),(1892,121,'Sabha','SB',1),(1893,121,'Sawfajjin','SW',1),(1894,121,'Surt','SU',1),(1895,121,'Tarabulus (Tripoli)','TL',1),(1896,121,'Tarhunah','TH',1),(1897,121,'Tubruq','TU',1),(1898,121,'Yafran','YA',1),(1899,121,'Zlitan','ZL',1),(1900,122,'Vaduz','V',1),(1901,122,'Schaan','A',1),(1902,122,'Balzers','B',1),(1903,122,'Triesen','N',1),(1904,122,'Eschen','E',1),(1905,122,'Mauren','M',1),(1906,122,'Triesenberg','T',1),(1907,122,'Ruggell','R',1),(1908,122,'Gamprin','G',1),(1909,122,'Schellenberg','L',1),(1910,122,'Planken','P',1),(1911,123,'Alytus','AL',1),(1912,123,'Kaunas','KA',1),(1913,123,'Klaipeda','KL',1),(1914,123,'Marijampole','MA',1),(1915,123,'Panevezys','PA',1),(1916,123,'Siauliai','SI',1),(1917,123,'Taurage','TA',1),(1918,123,'Telsiai','TE',1),(1919,123,'Utena','UT',1),(1920,123,'Vilnius','VI',1),(1921,124,'Diekirch','DD',1),(1922,124,'Clervaux','DC',1),(1923,124,'Redange','DR',1),(1924,124,'Vianden','DV',1),(1925,124,'Wiltz','DW',1),(1926,124,'Grevenmacher','GG',1),(1927,124,'Echternach','GE',1),(1928,124,'Remich','GR',1),(1929,124,'Luxembourg','LL',1),(1930,124,'Capellen','LC',1),(1931,124,'Esch-sur-Alzette','LE',1),(1932,124,'Mersch','LM',1),(1933,125,'Our Lady Fatima Parish','OLF',1),(1934,125,'St. Anthony Parish','ANT',1),(1935,125,'St. Lazarus Parish','LAZ',1),(1936,125,'Cathedral Parish','CAT',1),(1937,125,'St. Lawrence Parish','LAW',1),(1938,127,'Antananarivo','AN',1),(1939,127,'Antsiranana','AS',1),(1940,127,'Fianarantsoa','FN',1),(1941,127,'Mahajanga','MJ',1),(1942,127,'Toamasina','TM',1),(1943,127,'Toliara','TL',1),(1944,128,'Balaka','BLK',1),(1945,128,'Blantyre','BLT',1),(1946,128,'Chikwawa','CKW',1),(1947,128,'Chiradzulu','CRD',1),(1948,128,'Chitipa','CTP',1),(1949,128,'Dedza','DDZ',1),(1950,128,'Dowa','DWA',1),(1951,128,'Karonga','KRG',1),(1952,128,'Kasungu','KSG',1),(1953,128,'Likoma','LKM',1),(1954,128,'Lilongwe','LLG',1),(1955,128,'Machinga','MCG',1),(1956,128,'Mangochi','MGC',1),(1957,128,'Mchinji','MCH',1),(1958,128,'Mulanje','MLJ',1),(1959,128,'Mwanza','MWZ',1),(1960,128,'Mzimba','MZM',1),(1961,128,'Ntcheu','NTU',1),(1962,128,'Nkhata Bay','NKB',1),(1963,128,'Nkhotakota','NKH',1),(1964,128,'Nsanje','NSJ',1),(1965,128,'Ntchisi','NTI',1),(1966,128,'Phalombe','PHL',1),(1967,128,'Rumphi','RMP',1),(1968,128,'Salima','SLM',1),(1969,128,'Thyolo','THY',1),(1970,128,'Zomba','ZBA',1),(1971,129,'Johor','MY-01',1),(1972,129,'Kedah','MY-02',1),(1973,129,'Kelantan','MY-03',1),(1974,129,'Labuan','MY-15',1),(1975,129,'Melaka','MY-04',1),(1976,129,'Negeri Sembilan','MY-05',1),(1977,129,'Pahang','MY-06',1),(1978,129,'Perak','MY-08',1),(1979,129,'Perlis','MY-09',1),(1980,129,'Pulau Pinang','MY-07',1),(1981,129,'Sabah','MY-12',1),(1982,129,'Sarawak','MY-13',1),(1983,129,'Selangor','MY-10',1),(1984,129,'Terengganu','MY-11',1),(1985,129,'Kuala Lumpur','MY-14',1),(1986,130,'Thiladhunmathi Uthuru','THU',1),(1987,130,'Thiladhunmathi Dhekunu','THD',1),(1988,130,'Miladhunmadulu Uthuru','MLU',1),(1989,130,'Miladhunmadulu Dhekunu','MLD',1),(1990,130,'Maalhosmadulu Uthuru','MAU',1),(1991,130,'Maalhosmadulu Dhekunu','MAD',1),(1992,130,'Faadhippolhu','FAA',1),(1993,130,'Male Atoll','MAA',1),(1994,130,'Ari Atoll Uthuru','AAU',1),(1995,130,'Ari Atoll Dheknu','AAD',1),(1996,130,'Felidhe Atoll','FEA',1),(1997,130,'Mulaku Atoll','MUA',1),(1998,130,'Nilandhe Atoll Uthuru','NAU',1),(1999,130,'Nilandhe Atoll Dhekunu','NAD',1),(2000,130,'Kolhumadulu','KLH',1),(2001,130,'Hadhdhunmathi','HDH',1),(2002,130,'Huvadhu Atoll Uthuru','HAU',1),(2003,130,'Huvadhu Atoll Dhekunu','HAD',1),(2004,130,'Fua Mulaku','FMU',1),(2005,130,'Addu','ADD',1),(2006,131,'Gao','GA',1),(2007,131,'Kayes','KY',1),(2008,131,'Kidal','KD',1),(2009,131,'Koulikoro','KL',1),(2010,131,'Mopti','MP',1),(2011,131,'Segou','SG',1),(2012,131,'Sikasso','SK',1),(2013,131,'Tombouctou','TB',1),(2014,131,'Bamako Capital District','CD',1),(2015,132,'Attard','ATT',1),(2016,132,'Balzan','BAL',1),(2017,132,'Birgu','BGU',1),(2018,132,'Birkirkara','BKK',1),(2019,132,'Birzebbuga','BRZ',1),(2020,132,'Bormla','BOR',1),(2021,132,'Dingli','DIN',1),(2022,132,'Fgura','FGU',1),(2023,132,'Floriana','FLO',1),(2024,132,'Gudja','GDJ',1),(2025,132,'Gzira','GZR',1),(2026,132,'Gargur','GRG',1),(2027,132,'Gaxaq','GXQ',1),(2028,132,'Hamrun','HMR',1),(2029,132,'Iklin','IKL',1),(2030,132,'Isla','ISL',1),(2031,132,'Kalkara','KLK',1),(2032,132,'Kirkop','KRK',1),(2033,132,'Lija','LIJ',1),(2034,132,'Luqa','LUQ',1),(2035,132,'Marsa','MRS',1),(2036,132,'Marsaskala','MKL',1),(2037,132,'Marsaxlokk','MXL',1),(2038,132,'Mdina','MDN',1),(2039,132,'Melliea','MEL',1),(2040,132,'Mgarr','MGR',1),(2041,132,'Mosta','MST',1),(2042,132,'Mqabba','MQA',1),(2043,132,'Msida','MSI',1),(2044,132,'Mtarfa','MTF',1),(2045,132,'Naxxar','NAX',1),(2046,132,'Paola','PAO',1),(2047,132,'Pembroke','PEM',1),(2048,132,'Pieta','PIE',1),(2049,132,'Qormi','QOR',1),(2050,132,'Qrendi','QRE',1),(2051,132,'Rabat','RAB',1),(2052,132,'Safi','SAF',1),(2053,132,'San Giljan','SGI',1),(2054,132,'Santa Lucija','SLU',1),(2055,132,'San Pawl il-Bahar','SPB',1),(2056,132,'San Gwann','SGW',1),(2057,132,'Santa Venera','SVE',1),(2058,132,'Siggiewi','SIG',1),(2059,132,'Sliema','SLM',1),(2060,132,'Swieqi','SWQ',1),(2061,132,'Ta Xbiex','TXB',1),(2062,132,'Tarxien','TRX',1),(2063,132,'Valletta','VLT',1),(2064,132,'Xgajra','XGJ',1),(2065,132,'Zabbar','ZBR',1),(2066,132,'Zebbug','ZBG',1),(2067,132,'Zejtun','ZJT',1),(2068,132,'Zurrieq','ZRQ',1),(2069,132,'Fontana','FNT',1),(2070,132,'Ghajnsielem','GHJ',1),(2071,132,'Gharb','GHR',1),(2072,132,'Ghasri','GHS',1),(2073,132,'Kercem','KRC',1),(2074,132,'Munxar','MUN',1),(2075,132,'Nadur','NAD',1),(2076,132,'Qala','QAL',1),(2077,132,'Victoria','VIC',1),(2078,132,'San Lawrenz','SLA',1),(2079,132,'Sannat','SNT',1),(2080,132,'Xagra','ZAG',1),(2081,132,'Xewkija','XEW',1),(2082,132,'Zebbug','ZEB',1),(2083,133,'Ailinginae','ALG',1),(2084,133,'Ailinglaplap','ALL',1),(2085,133,'Ailuk','ALK',1),(2086,133,'Arno','ARN',1),(2087,133,'Aur','AUR',1),(2088,133,'Bikar','BKR',1),(2089,133,'Bikini','BKN',1),(2090,133,'Bokak','BKK',1),(2091,133,'Ebon','EBN',1),(2092,133,'Enewetak','ENT',1),(2093,133,'Erikub','EKB',1),(2094,133,'Jabat','JBT',1),(2095,133,'Jaluit','JLT',1),(2096,133,'Jemo','JEM',1),(2097,133,'Kili','KIL',1),(2098,133,'Kwajalein','KWJ',1),(2099,133,'Lae','LAE',1),(2100,133,'Lib','LIB',1),(2101,133,'Likiep','LKP',1),(2102,133,'Majuro','MJR',1),(2103,133,'Maloelap','MLP',1),(2104,133,'Mejit','MJT',1),(2105,133,'Mili','MIL',1),(2106,133,'Namorik','NMK',1),(2107,133,'Namu','NAM',1),(2108,133,'Rongelap','RGL',1),(2109,133,'Rongrik','RGK',1),(2110,133,'Toke','TOK',1),(2111,133,'Ujae','UJA',1),(2112,133,'Ujelang','UJL',1),(2113,133,'Utirik','UTK',1),(2114,133,'Wotho','WTH',1),(2115,133,'Wotje','WTJ',1),(2116,135,'Adrar','AD',1),(2117,135,'Assaba','AS',1),(2118,135,'Brakna','BR',1),(2119,135,'Dakhlet Nouadhibou','DN',1),(2120,135,'Gorgol','GO',1),(2121,135,'Guidimaka','GM',1),(2122,135,'Hodh Ech Chargui','HC',1),(2123,135,'Hodh El Gharbi','HG',1),(2124,135,'Inchiri','IN',1),(2125,135,'Tagant','TA',1),(2126,135,'Tiris Zemmour','TZ',1),(2127,135,'Trarza','TR',1),(2128,135,'Nouakchott','NO',1),(2129,136,'Beau Bassin-Rose Hill','BR',1),(2130,136,'Curepipe','CU',1),(2131,136,'Port Louis','PU',1),(2132,136,'Quatre Bornes','QB',1),(2133,136,'Vacoas-Phoenix','VP',1),(2134,136,'Agalega Islands','AG',1),(2135,136,'Cargados Carajos Shoals (Saint Brandon Islands)','CC',1),(2136,136,'Rodrigues','RO',1),(2137,136,'Black River','BL',1),(2138,136,'Flacq','FL',1),(2139,136,'Grand Port','GP',1),(2140,136,'Moka','MO',1),(2141,136,'Pamplemousses','PA',1),(2142,136,'Plaines Wilhems','PW',1),(2143,136,'Port Louis','PL',1),(2144,136,'Riviere du Rempart','RR',1),(2145,136,'Savanne','SA',1),(2146,138,'Baja California Norte','BN',1),(2147,138,'Baja California Sur','BS',1),(2148,138,'Campeche','CA',1),(2149,138,'Chiapas','CI',1),(2150,138,'Chihuahua','CH',1),(2151,138,'Coahuila de Zaragoza','CZ',1),(2152,138,'Colima','CL',1),(2153,138,'Distrito Federal','DF',1),(2154,138,'Durango','DU',1),(2155,138,'Guanajuato','GA',1),(2156,138,'Guerrero','GE',1),(2157,138,'Hidalgo','HI',1),(2158,138,'Jalisco','JA',1),(2159,138,'Mexico','ME',1),(2160,138,'Michoacan de Ocampo','MI',1),(2161,138,'Morelos','MO',1),(2162,138,'Nayarit','NA',1),(2163,138,'Nuevo Leon','NL',1),(2164,138,'Oaxaca','OA',1),(2165,138,'Puebla','PU',1),(2166,138,'Queretaro de Arteaga','QA',1),(2167,138,'Quintana Roo','QR',1),(2168,138,'San Luis Potosi','SA',1),(2169,138,'Sinaloa','SI',1),(2170,138,'Sonora','SO',1),(2171,138,'Tabasco','TB',1),(2172,138,'Tamaulipas','TM',1),(2173,138,'Tlaxcala','TL',1),(2174,138,'Veracruz-Llave','VE',1),(2175,138,'Yucatan','YU',1),(2176,138,'Zacatecas','ZA',1),(2177,139,'Chuuk','C',1),(2178,139,'Kosrae','K',1),(2179,139,'Pohnpei','P',1),(2180,139,'Yap','Y',1),(2181,140,'Gagauzia','GA',1),(2182,140,'Chisinau','CU',1),(2183,140,'Balti','BA',1),(2184,140,'Cahul','CA',1),(2185,140,'Edinet','ED',1),(2186,140,'Lapusna','LA',1),(2187,140,'Orhei','OR',1),(2188,140,'Soroca','SO',1),(2189,140,'Tighina','TI',1),(2190,140,'Ungheni','UN',1),(2191,140,'St‚nga Nistrului','SN',1),(2192,141,'Fontvieille','FV',1),(2193,141,'La Condamine','LC',1),(2194,141,'Monaco-Ville','MV',1),(2195,141,'Monte-Carlo','MC',1),(2196,142,'Ulanbaatar','1',1),(2197,142,'Orhon','035',1),(2198,142,'Darhan uul','037',1),(2199,142,'Hentiy','039',1),(2200,142,'Hovsgol','041',1),(2201,142,'Hovd','043',1),(2202,142,'Uvs','046',1),(2203,142,'Tov','047',1),(2204,142,'Selenge','049',1),(2205,142,'Suhbaatar','051',1),(2206,142,'Omnogovi','053',1),(2207,142,'Ovorhangay','055',1),(2208,142,'Dzavhan','057',1),(2209,142,'DundgovL','059',1),(2210,142,'Dornod','061',1),(2211,142,'Dornogov','063',1),(2212,142,'Govi-Sumber','064',1),(2213,142,'Govi-Altay','065',1),(2214,142,'Bulgan','067',1),(2215,142,'Bayanhongor','069',1),(2216,142,'Bayan-Olgiy','071',1),(2217,142,'Arhangay','073',1),(2218,143,'Saint Anthony','A',1),(2219,143,'Saint Georges','G',1),(2220,143,'Saint Peter','P',1),(2221,144,'Agadir','AGD',1),(2222,144,'Al Hoceima','HOC',1),(2223,144,'Azilal','AZI',1),(2224,144,'Beni Mellal','BME',1),(2225,144,'Ben Slimane','BSL',1),(2226,144,'Boulemane','BLM',1),(2227,144,'Casablanca','CBL',1),(2228,144,'Chaouen','CHA',1),(2229,144,'El Jadida','EJA',1),(2230,144,'El Kelaa des Sraghna','EKS',1),(2231,144,'Er Rachidia','ERA',1),(2232,144,'Essaouira','ESS',1),(2233,144,'Fes','FES',1),(2234,144,'Figuig','FIG',1),(2235,144,'Guelmim','GLM',1),(2236,144,'Ifrane','IFR',1),(2237,144,'Kenitra','KEN',1),(2238,144,'Khemisset','KHM',1),(2239,144,'Khenifra','KHN',1),(2240,144,'Khouribga','KHO',1),(2241,144,'Laayoune','LYN',1),(2242,144,'Larache','LAR',1),(2243,144,'Marrakech','MRK',1),(2244,144,'Meknes','MKN',1),(2245,144,'Nador','NAD',1),(2246,144,'Ouarzazate','ORZ',1),(2247,144,'Oujda','OUJ',1),(2248,144,'Rabat-Sale','RSA',1),(2249,144,'Safi','SAF',1),(2250,144,'Settat','SET',1),(2251,144,'Sidi Kacem','SKA',1),(2252,144,'Tangier','TGR',1),(2253,144,'Tan-Tan','TAN',1),(2254,144,'Taounate','TAO',1),(2255,144,'Taroudannt','TRD',1),(2256,144,'Tata','TAT',1),(2257,144,'Taza','TAZ',1),(2258,144,'Tetouan','TET',1),(2259,144,'Tiznit','TIZ',1),(2260,144,'Ad Dakhla','ADK',1),(2261,144,'Boujdour','BJD',1),(2262,144,'Es Smara','ESM',1),(2263,145,'Cabo Delgado','CD',1),(2264,145,'Gaza','GZ',1),(2265,145,'Inhambane','IN',1),(2266,145,'Manica','MN',1),(2267,145,'Maputo (city)','MC',1),(2268,145,'Maputo','MP',1),(2269,145,'Nampula','NA',1),(2270,145,'Niassa','NI',1),(2271,145,'Sofala','SO',1),(2272,145,'Tete','TE',1),(2273,145,'Zambezia','ZA',1),(2274,146,'Ayeyarwady','AY',1),(2275,146,'Bago','BG',1),(2276,146,'Magway','MG',1),(2277,146,'Mandalay','MD',1),(2278,146,'Sagaing','SG',1),(2279,146,'Tanintharyi','TN',1),(2280,146,'Yangon','YG',1),(2281,146,'Chin State','CH',1),(2282,146,'Kachin State','KC',1),(2283,146,'Kayah State','KH',1),(2284,146,'Kayin State','KN',1),(2285,146,'Mon State','MN',1),(2286,146,'Rakhine State','RK',1),(2287,146,'Shan State','SH',1),(2288,147,'Caprivi','CA',1),(2289,147,'Erongo','ER',1),(2290,147,'Hardap','HA',1),(2291,147,'Karas','KR',1),(2292,147,'Kavango','KV',1),(2293,147,'Khomas','KH',1),(2294,147,'Kunene','KU',1),(2295,147,'Ohangwena','OW',1),(2296,147,'Omaheke','OK',1),(2297,147,'Omusati','OT',1),(2298,147,'Oshana','ON',1),(2299,147,'Oshikoto','OO',1),(2300,147,'Otjozondjupa','OJ',1),(2301,148,'Aiwo','AO',1),(2302,148,'Anabar','AA',1),(2303,148,'Anetan','AT',1),(2304,148,'Anibare','AI',1),(2305,148,'Baiti','BA',1),(2306,148,'Boe','BO',1),(2307,148,'Buada','BU',1),(2308,148,'Denigomodu','DE',1),(2309,148,'Ewa','EW',1),(2310,148,'Ijuw','IJ',1),(2311,148,'Meneng','ME',1),(2312,148,'Nibok','NI',1),(2313,148,'Uaboe','UA',1),(2314,148,'Yaren','YA',1),(2315,149,'Bagmati','BA',1),(2316,149,'Bheri','BH',1),(2317,149,'Dhawalagiri','DH',1),(2318,149,'Gandaki','GA',1),(2319,149,'Janakpur','JA',1),(2320,149,'Karnali','KA',1),(2321,149,'Kosi','KO',1),(2322,149,'Lumbini','LU',1),(2323,149,'Mahakali','MA',1),(2324,149,'Mechi','ME',1),(2325,149,'Narayani','NA',1),(2326,149,'Rapti','RA',1),(2327,149,'Sagarmatha','SA',1),(2328,149,'Seti','SE',1),(2329,150,'Drenthe','DR',1),(2330,150,'Flevoland','FL',1),(2331,150,'Friesland','FR',1),(2332,150,'Gelderland','GE',1),(2333,150,'Groningen','GR',1),(2334,150,'Limburg','LI',1),(2335,150,'Noord Brabant','NB',1),(2336,150,'Noord Holland','NH',1),(2337,150,'Overijssel','OV',1),(2338,150,'Utrecht','UT',1),(2339,150,'Zeeland','ZE',1),(2340,150,'Zuid Holland','ZH',1),(2341,152,'Iles Loyaute','L',1),(2342,152,'Nord','N',1),(2343,152,'Sud','S',1),(2344,153,'Auckland','AUK',1),(2345,153,'Bay of Plenty','BOP',1),(2346,153,'Canterbury','CAN',1),(2347,153,'Coromandel','COR',1),(2348,153,'Gisborne','GIS',1),(2349,153,'Fiordland','FIO',1),(2350,153,'Hawke\'s Bay','HKB',1),(2351,153,'Marlborough','MBH',1),(2352,153,'Manawatu-Wanganui','MWT',1),(2353,153,'Mt Cook-Mackenzie','MCM',1),(2354,153,'Nelson','NSN',1),(2355,153,'Northland','NTL',1),(2356,153,'Otago','OTA',1),(2357,153,'Southland','STL',1),(2358,153,'Taranaki','TKI',1),(2359,153,'Wellington','WGN',1),(2360,153,'Waikato','WKO',1),(2361,153,'Wairarapa','WAI',1),(2362,153,'West Coast','WTC',1),(2363,154,'Atlantico Norte','AN',1),(2364,154,'Atlantico Sur','AS',1),(2365,154,'Boaco','BO',1),(2366,154,'Carazo','CA',1),(2367,154,'Chinandega','CI',1),(2368,154,'Chontales','CO',1),(2369,154,'Esteli','ES',1),(2370,154,'Granada','GR',1),(2371,154,'Jinotega','JI',1),(2372,154,'Leon','LE',1),(2373,154,'Madriz','MD',1),(2374,154,'Managua','MN',1),(2375,154,'Masaya','MS',1),(2376,154,'Matagalpa','MT',1),(2377,154,'Nuevo Segovia','NS',1),(2378,154,'Rio San Juan','RS',1),(2379,154,'Rivas','RI',1),(2380,155,'Agadez','AG',1),(2381,155,'Diffa','DF',1),(2382,155,'Dosso','DS',1),(2383,155,'Maradi','MA',1),(2384,155,'Niamey','NM',1),(2385,155,'Tahoua','TH',1),(2386,155,'Tillaberi','TL',1),(2387,155,'Zinder','ZD',1),(2388,156,'Abia','AB',1),(2389,156,'Abuja Federal Capital Territory','CT',1),(2390,156,'Adamawa','AD',1),(2391,156,'Akwa Ibom','AK',1),(2392,156,'Anambra','AN',1),(2393,156,'Bauchi','BC',1),(2394,156,'Bayelsa','BY',1),(2395,156,'Benue','BN',1),(2396,156,'Borno','BO',1),(2397,156,'Cross River','CR',1),(2398,156,'Delta','DE',1),(2399,156,'Ebonyi','EB',1),(2400,156,'Edo','ED',1),(2401,156,'Ekiti','EK',1),(2402,156,'Enugu','EN',1),(2403,156,'Gombe','GO',1),(2404,156,'Imo','IM',1),(2405,156,'Jigawa','JI',1),(2406,156,'Kaduna','KD',1),(2407,156,'Kano','KN',1),(2408,156,'Katsina','KT',1),(2409,156,'Kebbi','KE',1),(2410,156,'Kogi','KO',1),(2411,156,'Kwara','KW',1),(2412,156,'Lagos','LA',1),(2413,156,'Nassarawa','NA',1),(2414,156,'Niger','NI',1),(2415,156,'Ogun','OG',1),(2416,156,'Ondo','ONG',1),(2417,156,'Osun','OS',1),(2418,156,'Oyo','OY',1),(2419,156,'Plateau','PL',1),(2420,156,'Rivers','RI',1),(2421,156,'Sokoto','SO',1),(2422,156,'Taraba','TA',1),(2423,156,'Yobe','YO',1),(2424,156,'Zamfara','ZA',1),(2425,159,'Northern Islands','N',1),(2426,159,'Rota','R',1),(2427,159,'Saipan','S',1),(2428,159,'Tinian','T',1),(2429,160,'Akershus','AK',1),(2430,160,'Aust-Agder','AA',1),(2431,160,'Buskerud','BU',1),(2432,160,'Finnmark','FM',1),(2433,160,'Hedmark','HM',1),(2434,160,'Hordaland','HL',1),(2435,160,'More og Romdal','MR',1),(2436,160,'Nord-Trondelag','NT',1),(2437,160,'Nordland','NL',1),(2438,160,'Ostfold','OF',1),(2439,160,'Oppland','OP',1),(2440,160,'Oslo','OL',1),(2441,160,'Rogaland','RL',1),(2442,160,'Sor-Trondelag','ST',1),(2443,160,'Sogn og Fjordane','SJ',1),(2444,160,'Svalbard','SV',1),(2445,160,'Telemark','TM',1),(2446,160,'Troms','TR',1),(2447,160,'Vest-Agder','VA',1),(2448,160,'Vestfold','VF',1),(2449,161,'Ad Dakhiliyah','DA',1),(2450,161,'Al Batinah','BA',1),(2451,161,'Al Wusta','WU',1),(2452,161,'Ash Sharqiyah','SH',1),(2453,161,'Az Zahirah','ZA',1),(2454,161,'Masqat','MA',1),(2455,161,'Musandam','MU',1),(2456,161,'Zufar','ZU',1),(2457,162,'Balochistan','B',1),(2458,162,'Federally Administered Tribal Areas','T',1),(2459,162,'Islamabad Capital Territory','I',1),(2460,162,'North-West Frontier','N',1),(2461,162,'Punjab','P',1),(2462,162,'Sindh','S',1),(2463,163,'Aimeliik','AM',1),(2464,163,'Airai','AR',1),(2465,163,'Angaur','AN',1),(2466,163,'Hatohobei','HA',1),(2467,163,'Kayangel','KA',1),(2468,163,'Koror','KO',1),(2469,163,'Melekeok','ME',1),(2470,163,'Ngaraard','NA',1),(2471,163,'Ngarchelong','NG',1),(2472,163,'Ngardmau','ND',1),(2473,163,'Ngatpang','NT',1),(2474,163,'Ngchesar','NC',1),(2475,163,'Ngeremlengui','NR',1),(2476,163,'Ngiwal','NW',1),(2477,163,'Peleliu','PE',1),(2478,163,'Sonsorol','SO',1),(2479,164,'Bocas del Toro','BT',1),(2480,164,'Chiriqui','CH',1),(2481,164,'Cocle','CC',1),(2482,164,'Colon','CL',1),(2483,164,'Darien','DA',1),(2484,164,'Herrera','HE',1),(2485,164,'Los Santos','LS',1),(2486,164,'Panama','PA',1),(2487,164,'San Blas','SB',1),(2488,164,'Veraguas','VG',1),(2489,165,'Bougainville','BV',1),(2490,165,'Central','CE',1),(2491,165,'Chimbu','CH',1),(2492,165,'Eastern Highlands','EH',1),(2493,165,'East New Britain','EB',1),(2494,165,'East Sepik','ES',1),(2495,165,'Enga','EN',1),(2496,165,'Gulf','GU',1),(2497,165,'Madang','MD',1),(2498,165,'Manus','MN',1),(2499,165,'Milne Bay','MB',1),(2500,165,'Morobe','MR',1),(2501,165,'National Capital','NC',1),(2502,165,'New Ireland','NI',1),(2503,165,'Northern','NO',1),(2504,165,'Sandaun','SA',1),(2505,165,'Southern Highlands','SH',1),(2506,165,'Western','WE',1),(2507,165,'Western Highlands','WH',1),(2508,165,'West New Britain','WB',1),(2509,166,'Alto Paraguay','AG',1),(2510,166,'Alto Parana','AN',1),(2511,166,'Amambay','AM',1),(2512,166,'Asuncion','AS',1),(2513,166,'Boqueron','BO',1),(2514,166,'Caaguazu','CG',1),(2515,166,'Caazapa','CZ',1),(2516,166,'Canindeyu','CN',1),(2517,166,'Central','CE',1),(2518,166,'Concepcion','CC',1),(2519,166,'Cordillera','CD',1),(2520,166,'Guaira','GU',1),(2521,166,'Itapua','IT',1),(2522,166,'Misiones','MI',1),(2523,166,'Neembucu','NE',1),(2524,166,'Paraguari','PA',1),(2525,166,'Presidente Hayes','PH',1),(2526,166,'San Pedro','SP',1),(2527,167,'Amazonas','AM',1),(2528,167,'Ancash','AN',1),(2529,167,'Apurimac','AP',1),(2530,167,'Arequipa','AR',1),(2531,167,'Ayacucho','AY',1),(2532,167,'Cajamarca','CJ',1),(2533,167,'Callao','CL',1),(2534,167,'Cusco','CU',1),(2535,167,'Huancavelica','HV',1),(2536,167,'Huanuco','HO',1),(2537,167,'Ica','IC',1),(2538,167,'Junin','JU',1),(2539,167,'La Libertad','LD',1),(2540,167,'Lambayeque','LY',1),(2541,167,'Lima','LI',1),(2542,167,'Loreto','LO',1),(2543,167,'Madre de Dios','MD',1),(2544,167,'Moquegua','MO',1),(2545,167,'Pasco','PA',1),(2546,167,'Piura','PI',1),(2547,167,'Puno','PU',1),(2548,167,'San Martin','SM',1),(2549,167,'Tacna','TA',1),(2550,167,'Tumbes','TU',1),(2551,167,'Ucayali','UC',1),(2552,168,'Abra','ABR',1),(2553,168,'Agusan del Norte','ANO',1),(2554,168,'Agusan del Sur','ASU',1),(2555,168,'Aklan','AKL',1),(2556,168,'Albay','ALB',1),(2557,168,'Antique','ANT',1),(2558,168,'Apayao','APY',1),(2559,168,'Aurora','AUR',1),(2560,168,'Basilan','BAS',1),(2561,168,'Bataan','BTA',1),(2562,168,'Batanes','BTE',1),(2563,168,'Batangas','BTG',1),(2564,168,'Biliran','BLR',1),(2565,168,'Benguet','BEN',1),(2566,168,'Bohol','BOL',1),(2567,168,'Bukidnon','BUK',1),(2568,168,'Bulacan','BUL',1),(2569,168,'Cagayan','CAG',1),(2570,168,'Camarines Norte','CNO',1),(2571,168,'Camarines Sur','CSU',1),(2572,168,'Camiguin','CAM',1),(2573,168,'Capiz','CAP',1),(2574,168,'Catanduanes','CAT',1),(2575,168,'Cavite','CAV',1),(2576,168,'Cebu','CEB',1),(2577,168,'Compostela','CMP',1),(2578,168,'Davao del Norte','DNO',1),(2579,168,'Davao del Sur','DSU',1),(2580,168,'Davao Oriental','DOR',1),(2581,168,'Eastern Samar','ESA',1),(2582,168,'Guimaras','GUI',1),(2583,168,'Ifugao','IFU',1),(2584,168,'Ilocos Norte','INO',1),(2585,168,'Ilocos Sur','ISU',1),(2586,168,'Iloilo','ILO',1),(2587,168,'Isabela','ISA',1),(2588,168,'Kalinga','KAL',1),(2589,168,'Laguna','LAG',1),(2590,168,'Lanao del Norte','LNO',1),(2591,168,'Lanao del Sur','LSU',1),(2592,168,'La Union','UNI',1),(2593,168,'Leyte','LEY',1),(2594,168,'Maguindanao','MAG',1),(2595,168,'Marinduque','MRN',1),(2596,168,'Masbate','MSB',1),(2597,168,'Mindoro Occidental','MIC',1),(2598,168,'Mindoro Oriental','MIR',1),(2599,168,'Misamis Occidental','MSC',1),(2600,168,'Misamis Oriental','MOR',1),(2601,168,'Mountain','MOP',1),(2602,168,'Negros Occidental','NOC',1),(2603,168,'Negros Oriental','NOR',1),(2604,168,'North Cotabato','NCT',1),(2605,168,'Northern Samar','NSM',1),(2606,168,'Nueva Ecija','NEC',1),(2607,168,'Nueva Vizcaya','NVZ',1),(2608,168,'Palawan','PLW',1),(2609,168,'Pampanga','PMP',1),(2610,168,'Pangasinan','PNG',1),(2611,168,'Quezon','QZN',1),(2612,168,'Quirino','QRN',1),(2613,168,'Rizal','RIZ',1),(2614,168,'Romblon','ROM',1),(2615,168,'Samar','SMR',1),(2616,168,'Sarangani','SRG',1),(2617,168,'Siquijor','SQJ',1),(2618,168,'Sorsogon','SRS',1),(2619,168,'South Cotabato','SCO',1),(2620,168,'Southern Leyte','SLE',1),(2621,168,'Sultan Kudarat','SKU',1),(2622,168,'Sulu','SLU',1),(2623,168,'Surigao del Norte','SNO',1),(2624,168,'Surigao del Sur','SSU',1),(2625,168,'Tarlac','TAR',1),(2626,168,'Tawi-Tawi','TAW',1),(2627,168,'Zambales','ZBL',1),(2628,168,'Zamboanga del Norte','ZNO',1),(2629,168,'Zamboanga del Sur','ZSU',1),(2630,168,'Zamboanga Sibugay','ZSI',1),(2631,170,'Dolnoslaskie','DO',1),(2632,170,'Kujawsko-Pomorskie','KP',1),(2633,170,'Lodzkie','LO',1),(2634,170,'Lubelskie','LL',1),(2635,170,'Lubuskie','LU',1),(2636,170,'Malopolskie','ML',1),(2637,170,'Mazowieckie','MZ',1),(2638,170,'Opolskie','OP',1),(2639,170,'Podkarpackie','PP',1),(2640,170,'Podlaskie','PL',1),(2641,170,'Pomorskie','PM',1),(2642,170,'Slaskie','SL',1),(2643,170,'Swietokrzyskie','SW',1),(2644,170,'Warminsko-Mazurskie','WM',1),(2645,170,'Wielkopolskie','WP',1),(2646,170,'Zachodniopomorskie','ZA',1),(2647,198,'Saint Pierre','P',1),(2648,198,'Miquelon','M',1),(2649,171,'A&ccedil;ores','AC',1),(2650,171,'Aveiro','AV',1),(2651,171,'Beja','BE',1),(2652,171,'Braga','BR',1),(2653,171,'Bragan&ccedil;a','BA',1),(2654,171,'Castelo Branco','CB',1),(2655,171,'Coimbra','CO',1),(2656,171,'&Eacute;vora','EV',1),(2657,171,'Faro','FA',1),(2658,171,'Guarda','GU',1),(2659,171,'Leiria','LE',1),(2660,171,'Lisboa','LI',1),(2661,171,'Madeira','ME',1),(2662,171,'Portalegre','PO',1),(2663,171,'Porto','PR',1),(2664,171,'Santar&eacute;m','SA',1),(2665,171,'Set&uacute;bal','SE',1),(2666,171,'Viana do Castelo','VC',1),(2667,171,'Vila Real','VR',1),(2668,171,'Viseu','VI',1),(2669,173,'Ad Dawhah','DW',1),(2670,173,'Al Ghuwayriyah','GW',1),(2671,173,'Al Jumayliyah','JM',1),(2672,173,'Al Khawr','KR',1),(2673,173,'Al Wakrah','WK',1),(2674,173,'Ar Rayyan','RN',1),(2675,173,'Jarayan al Batinah','JB',1),(2676,173,'Madinat ash Shamal','MS',1),(2677,173,'Umm Sa\'id','UD',1),(2678,173,'Umm Salal','UL',1),(2679,175,'Alba','AB',1),(2680,175,'Arad','AR',1),(2681,175,'Arges','AG',1),(2682,175,'Bacau','BC',1),(2683,175,'Bihor','BH',1),(2684,175,'Bistrita-Nasaud','BN',1),(2685,175,'Botosani','BT',1),(2686,175,'Brasov','BV',1),(2687,175,'Braila','BR',1),(2688,175,'Bucuresti','B',1),(2689,175,'Buzau','BZ',1),(2690,175,'Caras-Severin','CS',1),(2691,175,'Calarasi','CL',1),(2692,175,'Cluj','CJ',1),(2693,175,'Constanta','CT',1),(2694,175,'Covasna','CV',1),(2695,175,'Dimbovita','DB',1),(2696,175,'Dolj','DJ',1),(2697,175,'Galati','GL',1),(2698,175,'Giurgiu','GR',1),(2699,175,'Gorj','GJ',1),(2700,175,'Harghita','HR',1),(2701,175,'Hunedoara','HD',1),(2702,175,'Ialomita','IL',1),(2703,175,'Iasi','IS',1),(2704,175,'Ilfov','IF',1),(2705,175,'Maramures','MM',1),(2706,175,'Mehedinti','MH',1),(2707,175,'Mures','MS',1),(2708,175,'Neamt','NT',1),(2709,175,'Olt','OT',1),(2710,175,'Prahova','PH',1),(2711,175,'Satu-Mare','SM',1),(2712,175,'Salaj','SJ',1),(2713,175,'Sibiu','SB',1),(2714,175,'Suceava','SV',1),(2715,175,'Teleorman','TR',1),(2716,175,'Timis','TM',1),(2717,175,'Tulcea','TL',1),(2718,175,'Vaslui','VS',1),(2719,175,'Valcea','VL',1),(2720,175,'Vrancea','VN',1),(2721,176,'Abakan','AB',1),(2722,176,'Aginskoye','AG',1),(2723,176,'Anadyr','AN',1),(2724,176,'Arkahangelsk','AR',1),(2725,176,'Astrakhan','AS',1),(2726,176,'Barnaul','BA',1),(2727,176,'Belgorod','BE',1),(2728,176,'Birobidzhan','BI',1),(2729,176,'Blagoveshchensk','BL',1),(2730,176,'Bryansk','BR',1),(2731,176,'Cheboksary','CH',1),(2732,176,'Chelyabinsk','CL',1),(2733,176,'Cherkessk','CR',1),(2734,176,'Chita','CI',1),(2735,176,'Dudinka','DU',1),(2736,176,'Elista','EL',1),(2737,176,'Gomo-Altaysk','GO',1),(2738,176,'Gorno-Altaysk','GA',1),(2739,176,'Groznyy','GR',1),(2740,176,'Irkutsk','IR',1),(2741,176,'Ivanovo','IV',1),(2742,176,'Izhevsk','IZ',1),(2743,176,'Kalinigrad','KA',1),(2744,176,'Kaluga','KL',1),(2745,176,'Kasnodar','KS',1),(2746,176,'Kazan','KZ',1),(2747,176,'Kemerovo','KE',1),(2748,176,'Khabarovsk','KH',1),(2749,176,'Khanty-Mansiysk','KM',1),(2750,176,'Kostroma','KO',1),(2751,176,'Krasnodar','KR',1),(2752,176,'Krasnoyarsk','KN',1),(2753,176,'Kudymkar','KU',1),(2754,176,'Kurgan','KG',1),(2755,176,'Kursk','KK',1),(2756,176,'Kyzyl','KY',1),(2757,176,'Lipetsk','LI',1),(2758,176,'Magadan','MA',1),(2759,176,'Makhachkala','MK',1),(2760,176,'Maykop','MY',1),(2761,176,'Moscow','MO',1),(2762,176,'Murmansk','MU',1),(2763,176,'Nalchik','NA',1),(2764,176,'Naryan Mar','NR',1),(2765,176,'Nazran','NZ',1),(2766,176,'Nizhniy Novgorod','NI',1),(2767,176,'Novgorod','NO',1),(2768,176,'Novosibirsk','NV',1),(2769,176,'Omsk','OM',1),(2770,176,'Orel','OR',1),(2771,176,'Orenburg','OE',1),(2772,176,'Palana','PA',1),(2773,176,'Penza','PE',1),(2774,176,'Perm','PR',1),(2775,176,'Petropavlovsk-Kamchatskiy','PK',1),(2776,176,'Petrozavodsk','PT',1),(2777,176,'Pskov','PS',1),(2778,176,'Rostov-na-Donu','RO',1),(2779,176,'Ryazan','RY',1),(2780,176,'Salekhard','SL',1),(2781,176,'Samara','SA',1),(2782,176,'Saransk','SR',1),(2783,176,'Saratov','SV',1),(2784,176,'Smolensk','SM',1),(2785,176,'St. Petersburg','SP',1),(2786,176,'Stavropol','ST',1),(2787,176,'Syktyvkar','SY',1),(2788,176,'Tambov','TA',1),(2789,176,'Tomsk','TO',1),(2790,176,'Tula','TU',1),(2791,176,'Tura','TR',1),(2792,176,'Tver','TV',1),(2793,176,'Tyumen','TY',1),(2794,176,'Ufa','UF',1),(2795,176,'Ul\'yanovsk','UL',1),(2796,176,'Ulan-Ude','UU',1),(2797,176,'Ust\'-Ordynskiy','US',1),(2798,176,'Vladikavkaz','VL',1),(2799,176,'Vladimir','VA',1),(2800,176,'Vladivostok','VV',1),(2801,176,'Volgograd','VG',1),(2802,176,'Vologda','VD',1),(2803,176,'Voronezh','VO',1),(2804,176,'Vyatka','VY',1),(2805,176,'Yakutsk','YA',1),(2806,176,'Yaroslavl','YR',1),(2807,176,'Yekaterinburg','YE',1),(2808,176,'Yoshkar-Ola','YO',1),(2809,177,'Butare','BU',1),(2810,177,'Byumba','BY',1),(2811,177,'Cyangugu','CY',1),(2812,177,'Gikongoro','GK',1),(2813,177,'Gisenyi','GS',1),(2814,177,'Gitarama','GT',1),(2815,177,'Kibungo','KG',1),(2816,177,'Kibuye','KY',1),(2817,177,'Kigali Rurale','KR',1),(2818,177,'Kigali-ville','KV',1),(2819,177,'Ruhengeri','RU',1),(2820,177,'Umutara','UM',1),(2821,178,'Christ Church Nichola Town','CCN',1),(2822,178,'Saint Anne Sandy Point','SAS',1),(2823,178,'Saint George Basseterre','SGB',1),(2824,178,'Saint George Gingerland','SGG',1),(2825,178,'Saint James Windward','SJW',1),(2826,178,'Saint John Capesterre','SJC',1),(2827,178,'Saint John Figtree','SJF',1),(2828,178,'Saint Mary Cayon','SMC',1),(2829,178,'Saint Paul Capesterre','CAP',1),(2830,178,'Saint Paul Charlestown','CHA',1),(2831,178,'Saint Peter Basseterre','SPB',1),(2832,178,'Saint Thomas Lowland','STL',1),(2833,178,'Saint Thomas Middle Island','STM',1),(2834,178,'Trinity Palmetto Point','TPP',1),(2835,179,'Anse-la-Raye','AR',1),(2836,179,'Castries','CA',1),(2837,179,'Choiseul','CH',1),(2838,179,'Dauphin','DA',1),(2839,179,'Dennery','DE',1),(2840,179,'Gros-Islet','GI',1),(2841,179,'Laborie','LA',1),(2842,179,'Micoud','MI',1),(2843,179,'Praslin','PR',1),(2844,179,'Soufriere','SO',1),(2845,179,'Vieux-Fort','VF',1),(2846,180,'Charlotte','C',1),(2847,180,'Grenadines','R',1),(2848,180,'Saint Andrew','A',1),(2849,180,'Saint David','D',1),(2850,180,'Saint George','G',1),(2851,180,'Saint Patrick','P',1),(2852,181,'A\'ana','AN',1),(2853,181,'Aiga-i-le-Tai','AI',1),(2854,181,'Atua','AT',1),(2855,181,'Fa\'asaleleaga','FA',1),(2856,181,'Gaga\'emauga','GE',1),(2857,181,'Gagaifomauga','GF',1),(2858,181,'Palauli','PA',1),(2859,181,'Satupa\'itea','SA',1),(2860,181,'Tuamasaga','TU',1),(2861,181,'Va\'a-o-Fonoti','VF',1),(2862,181,'Vaisigano','VS',1),(2863,182,'Acquaviva','AC',1),(2864,182,'Borgo Maggiore','BM',1),(2865,182,'Chiesanuova','CH',1),(2866,182,'Domagnano','DO',1),(2867,182,'Faetano','FA',1),(2868,182,'Fiorentino','FI',1),(2869,182,'Montegiardino','MO',1),(2870,182,'Citta di San Marino','SM',1),(2871,182,'Serravalle','SE',1),(2872,183,'Sao Tome','S',1),(2873,183,'Principe','P',1),(2874,184,'Al Bahah','BH',1),(2875,184,'Al Hudud ash Shamaliyah','HS',1),(2876,184,'Al Jawf','JF',1),(2877,184,'Al Madinah','MD',1),(2878,184,'Al Qasim','QS',1),(2879,184,'Ar Riyad','RD',1),(2880,184,'Ash Sharqiyah (Eastern)','AQ',1),(2881,184,'\'Asir','AS',1),(2882,184,'Ha\'il','HL',1),(2883,184,'Jizan','JZ',1),(2884,184,'Makkah','ML',1),(2885,184,'Najran','NR',1),(2886,184,'Tabuk','TB',1),(2887,185,'Dakar','DA',1),(2888,185,'Diourbel','DI',1),(2889,185,'Fatick','FA',1),(2890,185,'Kaolack','KA',1),(2891,185,'Kolda','KO',1),(2892,185,'Louga','LO',1),(2893,185,'Matam','MA',1),(2894,185,'Saint-Louis','SL',1),(2895,185,'Tambacounda','TA',1),(2896,185,'Thies','TH',1),(2897,185,'Ziguinchor','ZI',1),(2898,186,'Anse aux Pins','AP',1),(2899,186,'Anse Boileau','AB',1),(2900,186,'Anse Etoile','AE',1),(2901,186,'Anse Louis','AL',1),(2902,186,'Anse Royale','AR',1),(2903,186,'Baie Lazare','BL',1),(2904,186,'Baie Sainte Anne','BS',1),(2905,186,'Beau Vallon','BV',1),(2906,186,'Bel Air','BA',1),(2907,186,'Bel Ombre','BO',1),(2908,186,'Cascade','CA',1),(2909,186,'Glacis','GL',1),(2910,186,'Grand\' Anse (on Mahe)','GM',1),(2911,186,'Grand\' Anse (on Praslin)','GP',1),(2912,186,'La Digue','DG',1),(2913,186,'La Riviere Anglaise','RA',1),(2914,186,'Mont Buxton','MB',1),(2915,186,'Mont Fleuri','MF',1),(2916,186,'Plaisance','PL',1),(2917,186,'Pointe La Rue','PR',1),(2918,186,'Port Glaud','PG',1),(2919,186,'Saint Louis','SL',1),(2920,186,'Takamaka','TA',1),(2921,187,'Eastern','E',1),(2922,187,'Northern','N',1),(2923,187,'Southern','S',1),(2924,187,'Western','W',1),(2925,189,'Banskobystrický','BA',1),(2926,189,'Bratislavský','BR',1),(2927,189,'Košický','KO',1),(2928,189,'Nitriansky','NI',1),(2929,189,'Prešovský','PR',1),(2930,189,'Trenčiansky','TC',1),(2931,189,'Trnavský','TV',1),(2932,189,'Žilinský','ZI',1),(2933,191,'Central','CE',1),(2934,191,'Choiseul','CH',1),(2935,191,'Guadalcanal','GC',1),(2936,191,'Honiara','HO',1),(2937,191,'Isabel','IS',1),(2938,191,'Makira','MK',1),(2939,191,'Malaita','ML',1),(2940,191,'Rennell and Bellona','RB',1),(2941,191,'Temotu','TM',1),(2942,191,'Western','WE',1),(2943,192,'Awdal','AW',1),(2944,192,'Bakool','BK',1),(2945,192,'Banaadir','BN',1),(2946,192,'Bari','BR',1),(2947,192,'Bay','BY',1),(2948,192,'Galguduud','GA',1),(2949,192,'Gedo','GE',1),(2950,192,'Hiiraan','HI',1),(2951,192,'Jubbada Dhexe','JD',1),(2952,192,'Jubbada Hoose','JH',1),(2953,192,'Mudug','MU',1),(2954,192,'Nugaal','NU',1),(2955,192,'Sanaag','SA',1),(2956,192,'Shabeellaha Dhexe','SD',1),(2957,192,'Shabeellaha Hoose','SH',1),(2958,192,'Sool','SL',1),(2959,192,'Togdheer','TO',1),(2960,192,'Woqooyi Galbeed','WG',1),(2961,193,'Eastern Cape','EC',1),(2962,193,'Free State','FS',1),(2963,193,'Gauteng','GT',1),(2964,193,'KwaZulu-Natal','KN',1),(2965,193,'Limpopo','LP',1),(2966,193,'Mpumalanga','MP',1),(2967,193,'North West','NW',1),(2968,193,'Northern Cape','NC',1),(2969,193,'Western Cape','WC',1),(2970,195,'La Coru&ntilde;a','CA',1),(2971,195,'&Aacute;lava','AL',1),(2972,195,'Albacete','AB',1),(2973,195,'Alicante','AC',1),(2974,195,'Almeria','AM',1),(2975,195,'Asturias','AS',1),(2976,195,'&Aacute;vila','AV',1),(2977,195,'Badajoz','BJ',1),(2978,195,'Baleares','IB',1),(2979,195,'Barcelona','BA',1),(2980,195,'Burgos','BU',1),(2981,195,'C&aacute;ceres','CC',1),(2982,195,'C&aacute;diz','CZ',1),(2983,195,'Cantabria','CT',1),(2984,195,'Castell&oacute;n','CL',1),(2985,195,'Ceuta','CE',1),(2986,195,'Ciudad Real','CR',1),(2987,195,'C&oacute;rdoba','CD',1),(2988,195,'Cuenca','CU',1),(2989,195,'Girona','GI',1),(2990,195,'Granada','GD',1),(2991,195,'Guadalajara','GJ',1),(2992,195,'Guip&uacute;zcoa','GP',1),(2993,195,'Huelva','HL',1),(2994,195,'Huesca','HS',1),(2995,195,'Ja&eacute;n','JN',1),(2996,195,'La Rioja','RJ',1),(2997,195,'Las Palmas','PM',1),(2998,195,'Leon','LE',1),(2999,195,'Lleida','LL',1),(3000,195,'Lugo','LG',1),(3001,195,'Madrid','MD',1),(3002,195,'Malaga','MA',1),(3003,195,'Melilla','ML',1),(3004,195,'Murcia','MU',1),(3005,195,'Navarra','NV',1),(3006,195,'Ourense','OU',1),(3007,195,'Palencia','PL',1),(3008,195,'Pontevedra','PO',1),(3009,195,'Salamanca','SL',1),(3010,195,'Santa Cruz de Tenerife','SC',1),(3011,195,'Segovia','SG',1),(3012,195,'Sevilla','SV',1),(3013,195,'Soria','SO',1),(3014,195,'Tarragona','TA',1),(3015,195,'Teruel','TE',1),(3016,195,'Toledo','TO',1),(3017,195,'Valencia','VC',1),(3018,195,'Valladolid','VD',1),(3019,195,'Vizcaya','VZ',1),(3020,195,'Zamora','ZM',1),(3021,195,'Zaragoza','ZR',1),(3022,196,'Central','CE',1),(3023,196,'Eastern','EA',1),(3024,196,'North Central','NC',1),(3025,196,'Northern','NO',1),(3026,196,'North Western','NW',1),(3027,196,'Sabaragamuwa','SA',1),(3028,196,'Southern','SO',1),(3029,196,'Uva','UV',1),(3030,196,'Western','WE',1),(3032,197,'Saint Helena','S',1),(3034,199,'A\'ali an Nil','ANL',1),(3035,199,'Al Bahr al Ahmar','BAM',1),(3036,199,'Al Buhayrat','BRT',1),(3037,199,'Al Jazirah','JZR',1),(3038,199,'Al Khartum','KRT',1),(3039,199,'Al Qadarif','QDR',1),(3040,199,'Al Wahdah','WDH',1),(3041,199,'An Nil al Abyad','ANB',1),(3042,199,'An Nil al Azraq','ANZ',1),(3043,199,'Ash Shamaliyah','ASH',1),(3044,199,'Bahr al Jabal','BJA',1),(3045,199,'Gharb al Istiwa\'iyah','GIS',1),(3046,199,'Gharb Bahr al Ghazal','GBG',1),(3047,199,'Gharb Darfur','GDA',1),(3048,199,'Gharb Kurdufan','GKU',1),(3049,199,'Janub Darfur','JDA',1),(3050,199,'Janub Kurdufan','JKU',1),(3051,199,'Junqali','JQL',1),(3052,199,'Kassala','KSL',1),(3053,199,'Nahr an Nil','NNL',1),(3054,199,'Shamal Bahr al Ghazal','SBG',1),(3055,199,'Shamal Darfur','SDA',1),(3056,199,'Shamal Kurdufan','SKU',1),(3057,199,'Sharq al Istiwa\'iyah','SIS',1),(3058,199,'Sinnar','SNR',1),(3059,199,'Warab','WRB',1),(3060,200,'Brokopondo','BR',1),(3061,200,'Commewijne','CM',1),(3062,200,'Coronie','CR',1),(3063,200,'Marowijne','MA',1),(3064,200,'Nickerie','NI',1),(3065,200,'Para','PA',1),(3066,200,'Paramaribo','PM',1),(3067,200,'Saramacca','SA',1),(3068,200,'Sipaliwini','SI',1),(3069,200,'Wanica','WA',1),(3070,202,'Hhohho','H',1),(3071,202,'Lubombo','L',1),(3072,202,'Manzini','M',1),(3073,202,'Shishelweni','S',1),(3074,203,'Blekinge','K',1),(3075,203,'Dalarna','W',1),(3076,203,'G&auml;vleborg','X',1),(3077,203,'Gotland','I',1),(3078,203,'Halland','N',1),(3079,203,'J&auml;mtland','Z',1),(3080,203,'J&ouml;nk&ouml;ping','F',1),(3081,203,'Kalmar','H',1),(3082,203,'Kronoberg','G',1),(3083,203,'Norrbotten','BD',1),(3084,203,'&Ouml;rebro','T',1),(3085,203,'&Ouml;sterg&ouml;tland','E',1),(3086,203,'Sk&aring;ne','M',1),(3087,203,'S&ouml;dermanland','D',1),(3088,203,'Stockholm','AB',1),(3089,203,'Uppsala','C',1),(3090,203,'V&auml;rmland','S',1),(3091,203,'V&auml;sterbotten','AC',1),(3092,203,'V&auml;sternorrland','Y',1),(3093,203,'V&auml;stmanland','U',1),(3094,203,'V&auml;stra G&ouml;taland','O',1),(3095,204,'Aargau','AG',1),(3096,204,'Appenzell Ausserrhoden','AR',1),(3097,204,'Appenzell Innerrhoden','AI',1),(3098,204,'Basel-Stadt','BS',1),(3099,204,'Basel-Landschaft','BL',1),(3100,204,'Bern','BE',1),(3101,204,'Fribourg','FR',1),(3102,204,'Gen&egrave;ve','GE',1),(3103,204,'Glarus','GL',1),(3104,204,'Graub&uuml;nden','GR',1),(3105,204,'Jura','JU',1),(3106,204,'Luzern','LU',1),(3107,204,'Neuch&acirc;tel','NE',1),(3108,204,'Nidwald','NW',1),(3109,204,'Obwald','OW',1),(3110,204,'St. Gallen','SG',1),(3111,204,'Schaffhausen','SH',1),(3112,204,'Schwyz','SZ',1),(3113,204,'Solothurn','SO',1),(3114,204,'Thurgau','TG',1),(3115,204,'Ticino','TI',1),(3116,204,'Uri','UR',1),(3117,204,'Valais','VS',1),(3118,204,'Vaud','VD',1),(3119,204,'Zug','ZG',1),(3120,204,'Z&uuml;rich','ZH',1),(3121,205,'Al Hasakah','HA',1),(3122,205,'Al Ladhiqiyah','LA',1),(3123,205,'Al Qunaytirah','QU',1),(3124,205,'Ar Raqqah','RQ',1),(3125,205,'As Suwayda','SU',1),(3126,205,'Dara','DA',1),(3127,205,'Dayr az Zawr','DZ',1),(3128,205,'Dimashq','DI',1),(3129,205,'Halab','HL',1),(3130,205,'Hamah','HM',1),(3131,205,'Hims','HI',1),(3132,205,'Idlib','ID',1),(3133,205,'Rif Dimashq','RD',1),(3134,205,'Tartus','TA',1),(3135,206,'Chang-hua','CH',1),(3136,206,'Chia-i','CI',1),(3137,206,'Hsin-chu','HS',1),(3138,206,'Hua-lien','HL',1),(3139,206,'I-lan','IL',1),(3140,206,'Kao-hsiung county','KH',1),(3141,206,'Kin-men','KM',1),(3142,206,'Lien-chiang','LC',1),(3143,206,'Miao-li','ML',1),(3144,206,'Nan-t\'ou','NT',1),(3145,206,'P\'eng-hu','PH',1),(3146,206,'P\'ing-tung','PT',1),(3147,206,'T\'ai-chung','TG',1),(3148,206,'T\'ai-nan','TA',1),(3149,206,'T\'ai-pei county','TP',1),(3150,206,'T\'ai-tung','TT',1),(3151,206,'T\'ao-yuan','TY',1),(3152,206,'Yun-lin','YL',1),(3153,206,'Chia-i city','CC',1),(3154,206,'Chi-lung','CL',1),(3155,206,'Hsin-chu','HC',1),(3156,206,'T\'ai-chung','TH',1),(3157,206,'T\'ai-nan','TN',1),(3158,206,'Kao-hsiung city','KC',1),(3159,206,'T\'ai-pei city','TC',1),(3160,207,'Gorno-Badakhstan','GB',1),(3161,207,'Khatlon','KT',1),(3162,207,'Sughd','SU',1),(3163,208,'Arusha','AR',1),(3164,208,'Dar es Salaam','DS',1),(3165,208,'Dodoma','DO',1),(3166,208,'Iringa','IR',1),(3167,208,'Kagera','KA',1),(3168,208,'Kigoma','KI',1),(3169,208,'Kilimanjaro','KJ',1),(3170,208,'Lindi','LN',1),(3171,208,'Manyara','MY',1),(3172,208,'Mara','MR',1),(3173,208,'Mbeya','MB',1),(3174,208,'Morogoro','MO',1),(3175,208,'Mtwara','MT',1),(3176,208,'Mwanza','MW',1),(3177,208,'Pemba North','PN',1),(3178,208,'Pemba South','PS',1),(3179,208,'Pwani','PW',1),(3180,208,'Rukwa','RK',1),(3181,208,'Ruvuma','RV',1),(3182,208,'Shinyanga','SH',1),(3183,208,'Singida','SI',1),(3184,208,'Tabora','TB',1),(3185,208,'Tanga','TN',1),(3186,208,'Zanzibar Central/South','ZC',1),(3187,208,'Zanzibar North','ZN',1),(3188,208,'Zanzibar Urban/West','ZU',1),(3189,209,'Amnat Charoen','Amnat Charoen',1),(3190,209,'Ang Thong','Ang Thong',1),(3191,209,'Ayutthaya','Ayutthaya',1),(3192,209,'Bangkok','Bangkok',1),(3193,209,'Buriram','Buriram',1),(3194,209,'Chachoengsao','Chachoengsao',1),(3195,209,'Chai Nat','Chai Nat',1),(3196,209,'Chaiyaphum','Chaiyaphum',1),(3197,209,'Chanthaburi','Chanthaburi',1),(3198,209,'Chiang Mai','Chiang Mai',1),(3199,209,'Chiang Rai','Chiang Rai',1),(3200,209,'Chon Buri','Chon Buri',1),(3201,209,'Chumphon','Chumphon',1),(3202,209,'Kalasin','Kalasin',1),(3203,209,'Kamphaeng Phet','Kamphaeng Phet',1),(3204,209,'Kanchanaburi','Kanchanaburi',1),(3205,209,'Khon Kaen','Khon Kaen',1),(3206,209,'Krabi','Krabi',1),(3207,209,'Lampang','Lampang',1),(3208,209,'Lamphun','Lamphun',1),(3209,209,'Loei','Loei',1),(3210,209,'Lop Buri','Lop Buri',1),(3211,209,'Mae Hong Son','Mae Hong Son',1),(3212,209,'Maha Sarakham','Maha Sarakham',1),(3213,209,'Mukdahan','Mukdahan',1),(3214,209,'Nakhon Nayok','Nakhon Nayok',1),(3215,209,'Nakhon Pathom','Nakhon Pathom',1),(3216,209,'Nakhon Phanom','Nakhon Phanom',1),(3217,209,'Nakhon Ratchasima','Nakhon Ratchasima',1),(3218,209,'Nakhon Sawan','Nakhon Sawan',1),(3219,209,'Nakhon Si Thammarat','Nakhon Si Thammarat',1),(3220,209,'Nan','Nan',1),(3221,209,'Narathiwat','Narathiwat',1),(3222,209,'Nong Bua Lamphu','Nong Bua Lamphu',1),(3223,209,'Nong Khai','Nong Khai',1),(3224,209,'Nonthaburi','Nonthaburi',1),(3225,209,'Pathum Thani','Pathum Thani',1),(3226,209,'Pattani','Pattani',1),(3227,209,'Phangnga','Phangnga',1),(3228,209,'Phatthalung','Phatthalung',1),(3229,209,'Phayao','Phayao',1),(3230,209,'Phetchabun','Phetchabun',1),(3231,209,'Phetchaburi','Phetchaburi',1),(3232,209,'Phichit','Phichit',1),(3233,209,'Phitsanulok','Phitsanulok',1),(3234,209,'Phrae','Phrae',1),(3235,209,'Phuket','Phuket',1),(3236,209,'Prachin Buri','Prachin Buri',1),(3237,209,'Prachuap Khiri Khan','Prachuap Khiri Khan',1),(3238,209,'Ranong','Ranong',1),(3239,209,'Ratchaburi','Ratchaburi',1),(3240,209,'Rayong','Rayong',1),(3241,209,'Roi Et','Roi Et',1),(3242,209,'Sa Kaeo','Sa Kaeo',1),(3243,209,'Sakon Nakhon','Sakon Nakhon',1),(3244,209,'Samut Prakan','Samut Prakan',1),(3245,209,'Samut Sakhon','Samut Sakhon',1),(3246,209,'Samut Songkhram','Samut Songkhram',1),(3247,209,'Sara Buri','Sara Buri',1),(3248,209,'Satun','Satun',1),(3249,209,'Sing Buri','Sing Buri',1),(3250,209,'Sisaket','Sisaket',1),(3251,209,'Songkhla','Songkhla',1),(3252,209,'Sukhothai','Sukhothai',1),(3253,209,'Suphan Buri','Suphan Buri',1),(3254,209,'Surat Thani','Surat Thani',1),(3255,209,'Surin','Surin',1),(3256,209,'Tak','Tak',1),(3257,209,'Trang','Trang',1),(3258,209,'Trat','Trat',1),(3259,209,'Ubon Ratchathani','Ubon Ratchathani',1),(3260,209,'Udon Thani','Udon Thani',1),(3261,209,'Uthai Thani','Uthai Thani',1),(3262,209,'Uttaradit','Uttaradit',1),(3263,209,'Yala','Yala',1),(3264,209,'Yasothon','Yasothon',1),(3265,210,'Kara','K',1),(3266,210,'Plateaux','P',1),(3267,210,'Savanes','S',1),(3268,210,'Centrale','C',1),(3269,210,'Maritime','M',1),(3270,211,'Atafu','A',1),(3271,211,'Fakaofo','F',1),(3272,211,'Nukunonu','N',1),(3273,212,'Ha\'apai','H',1),(3274,212,'Tongatapu','T',1),(3275,212,'Vava\'u','V',1),(3276,213,'Couva/Tabaquite/Talparo','CT',1),(3277,213,'Diego Martin','DM',1),(3278,213,'Mayaro/Rio Claro','MR',1),(3279,213,'Penal/Debe','PD',1),(3280,213,'Princes Town','PT',1),(3281,213,'Sangre Grande','SG',1),(3282,213,'San Juan/Laventille','SL',1),(3283,213,'Siparia','SI',1),(3284,213,'Tunapuna/Piarco','TP',1),(3285,213,'Port of Spain','PS',1),(3286,213,'San Fernando','SF',1),(3287,213,'Arima','AR',1),(3288,213,'Point Fortin','PF',1),(3289,213,'Chaguanas','CH',1),(3290,213,'Tobago','TO',1),(3291,214,'Ariana','AR',1),(3292,214,'Beja','BJ',1),(3293,214,'Ben Arous','BA',1),(3294,214,'Bizerte','BI',1),(3295,214,'Gabes','GB',1),(3296,214,'Gafsa','GF',1),(3297,214,'Jendouba','JE',1),(3298,214,'Kairouan','KR',1),(3299,214,'Kasserine','KS',1),(3300,214,'Kebili','KB',1),(3301,214,'Kef','KF',1),(3302,214,'Mahdia','MH',1),(3303,214,'Manouba','MN',1),(3304,214,'Medenine','ME',1),(3305,214,'Monastir','MO',1),(3306,214,'Nabeul','NA',1),(3307,214,'Sfax','SF',1),(3308,214,'Sidi','SD',1),(3309,214,'Siliana','SL',1),(3310,214,'Sousse','SO',1),(3311,214,'Tataouine','TA',1),(3312,214,'Tozeur','TO',1),(3313,214,'Tunis','TU',1),(3314,214,'Zaghouan','ZA',1),(3315,215,'Adana','ADA',1),(3316,215,'Adıyaman','ADI',1),(3317,215,'Afyonkarahisar','AFY',1),(3318,215,'Ağrı','AGR',1),(3319,215,'Aksaray','AKS',1),(3320,215,'Amasya','AMA',1),(3321,215,'Ankara','ANK',1),(3322,215,'Antalya','ANT',1),(3323,215,'Ardahan','ARD',1),(3324,215,'Artvin','ART',1),(3325,215,'Aydın','AYI',1),(3326,215,'Balıkesir','BAL',1),(3327,215,'Bartın','BAR',1),(3328,215,'Batman','BAT',1),(3329,215,'Bayburt','BAY',1),(3330,215,'Bilecik','BIL',1),(3331,215,'Bingöl','BIN',1),(3332,215,'Bitlis','BIT',1),(3333,215,'Bolu','BOL',1),(3334,215,'Burdur','BRD',1),(3335,215,'Bursa','BRS',1),(3336,215,'Çanakkale','CKL',1),(3337,215,'Çankırı','CKR',1),(3338,215,'Çorum','COR',1),(3339,215,'Denizli','DEN',1),(3340,215,'Diyarbakır','DIY',1),(3341,215,'Düzce','DUZ',1),(3342,215,'Edirne','EDI',1),(3343,215,'Elazığ','ELA',1),(3344,215,'Erzincan','EZC',1),(3345,215,'Erzurum','EZR',1),(3346,215,'Eskişehir','ESK',1),(3347,215,'Gaziantep','GAZ',1),(3348,215,'Giresun','GIR',1),(3349,215,'Gümüşhane','GMS',1),(3350,215,'Hakkari','HKR',1),(3351,215,'Hatay','HTY',1),(3352,215,'Iğdır','IGD',1),(3353,215,'Isparta','ISP',1),(3354,215,'İstanbul','IST',1),(3355,215,'İzmir','IZM',1),(3356,215,'Kahramanmaraş','KAH',1),(3357,215,'Karabük','KRB',1),(3358,215,'Karaman','KRM',1),(3359,215,'Kars','KRS',1),(3360,215,'Kastamonu','KAS',1),(3361,215,'Kayseri','KAY',1),(3362,215,'Kilis','KLS',1),(3363,215,'Kırıkkale','KRK',1),(3364,215,'Kırklareli','KLR',1),(3365,215,'Kırşehir','KRH',1),(3366,215,'Kocaeli','KOC',1),(3367,215,'Konya','KON',1),(3368,215,'Kütahya','KUT',1),(3369,215,'Malatya','MAL',1),(3370,215,'Manisa','MAN',1),(3371,215,'Mardin','MAR',1),(3372,215,'Mersin','MER',1),(3373,215,'Muğla','MUG',1),(3374,215,'Muş','MUS',1),(3375,215,'Nevşehir','NEV',1),(3376,215,'Niğde','NIG',1),(3377,215,'Ordu','ORD',1),(3378,215,'Osmaniye','OSM',1),(3379,215,'Rize','RIZ',1),(3380,215,'Sakarya','SAK',1),(3381,215,'Samsun','SAM',1),(3382,215,'Şanlıurfa','SAN',1),(3383,215,'Siirt','SII',1),(3384,215,'Sinop','SIN',1),(3385,215,'Şırnak','SIR',1),(3386,215,'Sivas','SIV',1),(3387,215,'Tekirdağ','TEL',1),(3388,215,'Tokat','TOK',1),(3389,215,'Trabzon','TRA',1),(3390,215,'Tunceli','TUN',1),(3391,215,'Uşak','USK',1),(3392,215,'Van','VAN',1),(3393,215,'Yalova','YAL',1),(3394,215,'Yozgat','YOZ',1),(3395,215,'Zonguldak','ZON',1),(3396,216,'Ahal Welayaty','A',1),(3397,216,'Balkan Welayaty','B',1),(3398,216,'Dashhowuz Welayaty','D',1),(3399,216,'Lebap Welayaty','L',1),(3400,216,'Mary Welayaty','M',1),(3401,217,'Ambergris Cays','AC',1),(3402,217,'Dellis Cay','DC',1),(3403,217,'French Cay','FC',1),(3404,217,'Little Water Cay','LW',1),(3405,217,'Parrot Cay','RC',1),(3406,217,'Pine Cay','PN',1),(3407,217,'Salt Cay','SL',1),(3408,217,'Grand Turk','GT',1),(3409,217,'South Caicos','SC',1),(3410,217,'East Caicos','EC',1),(3411,217,'Middle Caicos','MC',1),(3412,217,'North Caicos','NC',1),(3413,217,'Providenciales','PR',1),(3414,217,'West Caicos','WC',1),(3415,218,'Nanumanga','NMG',1),(3416,218,'Niulakita','NLK',1),(3417,218,'Niutao','NTO',1),(3418,218,'Funafuti','FUN',1),(3419,218,'Nanumea','NME',1),(3420,218,'Nui','NUI',1),(3421,218,'Nukufetau','NFT',1),(3422,218,'Nukulaelae','NLL',1),(3423,218,'Vaitupu','VAI',1),(3424,219,'Kalangala','KAL',1),(3425,219,'Kampala','KMP',1),(3426,219,'Kayunga','KAY',1),(3427,219,'Kiboga','KIB',1),(3428,219,'Luwero','LUW',1),(3429,219,'Masaka','MAS',1),(3430,219,'Mpigi','MPI',1),(3431,219,'Mubende','MUB',1),(3432,219,'Mukono','MUK',1),(3433,219,'Nakasongola','NKS',1),(3434,219,'Rakai','RAK',1),(3435,219,'Sembabule','SEM',1),(3436,219,'Wakiso','WAK',1),(3437,219,'Bugiri','BUG',1),(3438,219,'Busia','BUS',1),(3439,219,'Iganga','IGA',1),(3440,219,'Jinja','JIN',1),(3441,219,'Kaberamaido','KAB',1),(3442,219,'Kamuli','KML',1),(3443,219,'Kapchorwa','KPC',1),(3444,219,'Katakwi','KTK',1),(3445,219,'Kumi','KUM',1),(3446,219,'Mayuge','MAY',1),(3447,219,'Mbale','MBA',1),(3448,219,'Pallisa','PAL',1),(3449,219,'Sironko','SIR',1),(3450,219,'Soroti','SOR',1),(3451,219,'Tororo','TOR',1),(3452,219,'Adjumani','ADJ',1),(3453,219,'Apac','APC',1),(3454,219,'Arua','ARU',1),(3455,219,'Gulu','GUL',1),(3456,219,'Kitgum','KIT',1),(3457,219,'Kotido','KOT',1),(3458,219,'Lira','LIR',1),(3459,219,'Moroto','MRT',1),(3460,219,'Moyo','MOY',1),(3461,219,'Nakapiripirit','NAK',1),(3462,219,'Nebbi','NEB',1),(3463,219,'Pader','PAD',1),(3464,219,'Yumbe','YUM',1),(3465,219,'Bundibugyo','BUN',1),(3466,219,'Bushenyi','BSH',1),(3467,219,'Hoima','HOI',1),(3468,219,'Kabale','KBL',1),(3469,219,'Kabarole','KAR',1),(3470,219,'Kamwenge','KAM',1),(3471,219,'Kanungu','KAN',1),(3472,219,'Kasese','KAS',1),(3473,219,'Kibaale','KBA',1),(3474,219,'Kisoro','KIS',1),(3475,219,'Kyenjojo','KYE',1),(3476,219,'Masindi','MSN',1),(3477,219,'Mbarara','MBR',1),(3478,219,'Ntungamo','NTU',1),(3479,219,'Rukungiri','RUK',1),(3480,220,'Cherkas\'ka Oblast\'','71',1),(3481,220,'Chernihivs\'ka Oblast\'','74',1),(3482,220,'Chernivets\'ka Oblast\'','77',1),(3483,220,'Crimea','43',1),(3484,220,'Dnipropetrovs\'ka Oblast\'','12',1),(3485,220,'Donets\'ka Oblast\'','14',1),(3486,220,'Ivano-Frankivs\'ka Oblast\'','26',1),(3487,220,'Khersons\'ka Oblast\'','65',1),(3488,220,'Khmel\'nyts\'ka Oblast\'','68',1),(3489,220,'Kirovohrads\'ka Oblast\'','35',1),(3490,220,'Kyiv','30',1),(3491,220,'Kyivs\'ka Oblast\'','32',1),(3492,220,'Luhans\'ka Oblast\'','09',1),(3493,220,'L\'vivs\'ka Oblast\'','46',1),(3494,220,'Mykolayivs\'ka Oblast\'','48',1),(3495,220,'Odes\'ka Oblast\'','51',1),(3496,220,'Poltavs\'ka Oblast\'','53',1),(3497,220,'Rivnens\'ka Oblast\'','56',1),(3498,220,'Sevastopol\'','40',1),(3499,220,'Sums\'ka Oblast\'','59',1),(3500,220,'Ternopil\'s\'ka Oblast\'','61',1),(3501,220,'Vinnyts\'ka Oblast\'','05',1),(3502,220,'Volyns\'ka Oblast\'','07',1),(3503,220,'Zakarpats\'ka Oblast\'','21',1),(3504,220,'Zaporiz\'ka Oblast\'','23',1),(3505,220,'Zhytomyrs\'ka oblast\'','18',1),(3506,221,'Abu Dhabi','ADH',1),(3507,221,'\'Ajman','AJ',1),(3508,221,'Al Fujayrah','FU',1),(3509,221,'Ash Shariqah','SH',1),(3510,221,'Dubai','DU',1),(3511,221,'R\'as al Khaymah','RK',1),(3512,221,'Umm al Qaywayn','UQ',1),(3513,222,'Aberdeen','ABN',1),(3514,222,'Aberdeenshire','ABNS',1),(3515,222,'Anglesey','ANG',1),(3516,222,'Angus','AGS',1),(3517,222,'Argyll and Bute','ARY',1),(3518,222,'Bedfordshire','BEDS',1),(3519,222,'Berkshire','BERKS',1),(3520,222,'Blaenau Gwent','BLA',1),(3521,222,'Bridgend','BRI',1),(3522,222,'Bristol','BSTL',1),(3523,222,'Buckinghamshire','BUCKS',1),(3524,222,'Caerphilly','CAE',1),(3525,222,'Cambridgeshire','CAMBS',1),(3526,222,'Cardiff','CDF',1),(3527,222,'Carmarthenshire','CARM',1),(3528,222,'Ceredigion','CDGN',1),(3529,222,'Cheshire','CHES',1),(3530,222,'Clackmannanshire','CLACK',1),(3531,222,'Conwy','CON',1),(3532,222,'Cornwall','CORN',1),(3533,222,'Denbighshire','DNBG',1),(3534,222,'Derbyshire','DERBY',1),(3535,222,'Devon','DVN',1),(3536,222,'Dorset','DOR',1),(3537,222,'Dumfries and Galloway','DGL',1),(3538,222,'Dundee','DUND',1),(3539,222,'Durham','DHM',1),(3540,222,'East Ayrshire','ARYE',1),(3541,222,'East Dunbartonshire','DUNBE',1),(3542,222,'East Lothian','LOTE',1),(3543,222,'East Renfrewshire','RENE',1),(3544,222,'East Riding of Yorkshire','ERYS',1),(3545,222,'East Sussex','SXE',1),(3546,222,'Edinburgh','EDIN',1),(3547,222,'Essex','ESX',1),(3548,222,'Falkirk','FALK',1),(3549,222,'Fife','FFE',1),(3550,222,'Flintshire','FLINT',1),(3551,222,'Glasgow','GLAS',1),(3552,222,'Gloucestershire','GLOS',1),(3553,222,'Greater London','LDN',1),(3554,222,'Greater Manchester','MCH',1),(3555,222,'Gwynedd','GDD',1),(3556,222,'Hampshire','HANTS',1),(3557,222,'Herefordshire','HWR',1),(3558,222,'Hertfordshire','HERTS',1),(3559,222,'Highlands','HLD',1),(3560,222,'Inverclyde','IVER',1),(3561,222,'Isle of Wight','IOW',1),(3562,222,'Kent','KNT',1),(3563,222,'Lancashire','LANCS',1),(3564,222,'Leicestershire','LEICS',1),(3565,222,'Lincolnshire','LINCS',1),(3566,222,'Merseyside','MSY',1),(3567,222,'Merthyr Tydfil','MERT',1),(3568,222,'Midlothian','MLOT',1),(3569,222,'Monmouthshire','MMOUTH',1),(3570,222,'Moray','MORAY',1),(3571,222,'Neath Port Talbot','NPRTAL',1),(3572,222,'Newport','NEWPT',1),(3573,222,'Norfolk','NOR',1),(3574,222,'North Ayrshire','ARYN',1),(3575,222,'North Lanarkshire','LANN',1),(3576,222,'North Yorkshire','YSN',1),(3577,222,'Northamptonshire','NHM',1),(3578,222,'Northumberland','NLD',1),(3579,222,'Nottinghamshire','NOT',1),(3580,222,'Orkney Islands','ORK',1),(3581,222,'Oxfordshire','OFE',1),(3582,222,'Pembrokeshire','PEM',1),(3583,222,'Perth and Kinross','PERTH',1),(3584,222,'Powys','PWS',1),(3585,222,'Renfrewshire','REN',1),(3586,222,'Rhondda Cynon Taff','RHON',1),(3587,222,'Rutland','RUT',1),(3588,222,'Scottish Borders','BOR',1),(3589,222,'Shetland Islands','SHET',1),(3590,222,'Shropshire','SPE',1),(3591,222,'Somerset','SOM',1),(3592,222,'South Ayrshire','ARYS',1),(3593,222,'South Lanarkshire','LANS',1),(3594,222,'South Yorkshire','YSS',1),(3595,222,'Staffordshire','SFD',1),(3596,222,'Stirling','STIR',1),(3597,222,'Suffolk','SFK',1),(3598,222,'Surrey','SRY',1),(3599,222,'Swansea','SWAN',1),(3600,222,'Torfaen','TORF',1),(3601,222,'Tyne and Wear','TWR',1),(3602,222,'Vale of Glamorgan','VGLAM',1),(3603,222,'Warwickshire','WARKS',1),(3604,222,'West Dunbartonshire','WDUN',1),(3605,222,'West Lothian','WLOT',1),(3606,222,'West Midlands','WMD',1),(3607,222,'West Sussex','SXW',1),(3608,222,'West Yorkshire','YSW',1),(3609,222,'Western Isles','WIL',1),(3610,222,'Wiltshire','WLT',1),(3611,222,'Worcestershire','WORCS',1),(3612,222,'Wrexham','WRX',1),(3613,223,'Alabama','AL',1),(3614,223,'Alaska','AK',1),(3615,223,'American Samoa','AS',1),(3616,223,'Arizona','AZ',1),(3617,223,'Arkansas','AR',1),(3618,223,'Armed Forces Africa','AF',1),(3619,223,'Armed Forces Americas','AA',1),(3620,223,'Armed Forces Canada','AC',1),(3621,223,'Armed Forces Europe','AE',1),(3622,223,'Armed Forces Middle East','AM',1),(3623,223,'Armed Forces Pacific','AP',1),(3624,223,'California','CA',1),(3625,223,'Colorado','CO',1),(3626,223,'Connecticut','CT',1),(3627,223,'Delaware','DE',1),(3628,223,'District of Columbia','DC',1),(3629,223,'Federated States Of Micronesia','FM',1),(3630,223,'Florida','FL',1),(3631,223,'Georgia','GA',1),(3632,223,'Guam','GU',1),(3633,223,'Hawaii','HI',1),(3634,223,'Idaho','ID',1),(3635,223,'Illinois','IL',1),(3636,223,'Indiana','IN',1),(3637,223,'Iowa','IA',1),(3638,223,'Kansas','KS',1),(3639,223,'Kentucky','KY',1),(3640,223,'Louisiana','LA',1),(3641,223,'Maine','ME',1),(3642,223,'Marshall Islands','MH',1),(3643,223,'Maryland','MD',1),(3644,223,'Massachusetts','MA',1),(3645,223,'Michigan','MI',1),(3646,223,'Minnesota','MN',1),(3647,223,'Mississippi','MS',1),(3648,223,'Missouri','MO',1),(3649,223,'Montana','MT',1),(3650,223,'Nebraska','NE',1),(3651,223,'Nevada','NV',1),(3652,223,'New Hampshire','NH',1),(3653,223,'New Jersey','NJ',1),(3654,223,'New Mexico','NM',1),(3655,223,'New York','NY',1),(3656,223,'North Carolina','NC',1),(3657,223,'North Dakota','ND',1),(3658,223,'Northern Mariana Islands','MP',1),(3659,223,'Ohio','OH',1),(3660,223,'Oklahoma','OK',1),(3661,223,'Oregon','OR',1),(3662,223,'Palau','PW',1),(3663,223,'Pennsylvania','PA',1),(3664,223,'Puerto Rico','PR',1),(3665,223,'Rhode Island','RI',1),(3666,223,'South Carolina','SC',1),(3667,223,'South Dakota','SD',1),(3668,223,'Tennessee','TN',1),(3669,223,'Texas','TX',1),(3670,223,'Utah','UT',1),(3671,223,'Vermont','VT',1),(3672,223,'Virgin Islands','VI',1),(3673,223,'Virginia','VA',1),(3674,223,'Washington','WA',1),(3675,223,'West Virginia','WV',1),(3676,223,'Wisconsin','WI',1),(3677,223,'Wyoming','WY',1),(3678,224,'Baker Island','BI',1),(3679,224,'Howland Island','HI',1),(3680,224,'Jarvis Island','JI',1),(3681,224,'Johnston Atoll','JA',1),(3682,224,'Kingman Reef','KR',1),(3683,224,'Midway Atoll','MA',1),(3684,224,'Navassa Island','NI',1),(3685,224,'Palmyra Atoll','PA',1),(3686,224,'Wake Island','WI',1),(3687,225,'Artigas','AR',1),(3688,225,'Canelones','CA',1),(3689,225,'Cerro Largo','CL',1),(3690,225,'Colonia','CO',1),(3691,225,'Durazno','DU',1),(3692,225,'Flores','FS',1),(3693,225,'Florida','FA',1),(3694,225,'Lavalleja','LA',1),(3695,225,'Maldonado','MA',1),(3696,225,'Montevideo','MO',1),(3697,225,'Paysandu','PA',1),(3698,225,'Rio Negro','RN',1),(3699,225,'Rivera','RV',1),(3700,225,'Rocha','RO',1),(3701,225,'Salto','SL',1),(3702,225,'San Jose','SJ',1),(3703,225,'Soriano','SO',1),(3704,225,'Tacuarembo','TA',1),(3705,225,'Treinta y Tres','TT',1),(3706,226,'Andijon','AN',1),(3707,226,'Buxoro','BU',1),(3708,226,'Farg\'ona','FA',1),(3709,226,'Jizzax','JI',1),(3710,226,'Namangan','NG',1),(3711,226,'Navoiy','NW',1),(3712,226,'Qashqadaryo','QA',1),(3713,226,'Qoraqalpog\'iston Republikasi','QR',1),(3714,226,'Samarqand','SA',1),(3715,226,'Sirdaryo','SI',1),(3716,226,'Surxondaryo','SU',1),(3717,226,'Toshkent City','TK',1),(3718,226,'Toshkent Region','TO',1),(3719,226,'Xorazm','XO',1),(3720,227,'Malampa','MA',1),(3721,227,'Penama','PE',1),(3722,227,'Sanma','SA',1),(3723,227,'Shefa','SH',1),(3724,227,'Tafea','TA',1),(3725,227,'Torba','TO',1),(3726,229,'Amazonas','AM',1),(3727,229,'Anzoategui','AN',1),(3728,229,'Apure','AP',1),(3729,229,'Aragua','AR',1),(3730,229,'Barinas','BA',1),(3731,229,'Bolivar','BO',1),(3732,229,'Carabobo','CA',1),(3733,229,'Cojedes','CO',1),(3734,229,'Delta Amacuro','DA',1),(3735,229,'Dependencias Federales','DF',1),(3736,229,'Distrito Federal','DI',1),(3737,229,'Falcon','FA',1),(3738,229,'Guarico','GU',1),(3739,229,'Lara','LA',1),(3740,229,'Merida','ME',1),(3741,229,'Miranda','MI',1),(3742,229,'Monagas','MO',1),(3743,229,'Nueva Esparta','NE',1),(3744,229,'Portuguesa','PO',1),(3745,229,'Sucre','SU',1),(3746,229,'Tachira','TA',1),(3747,229,'Trujillo','TR',1),(3748,229,'Vargas','VA',1),(3749,229,'Yaracuy','YA',1),(3750,229,'Zulia','ZU',1),(3751,230,'An Giang','AG',1),(3752,230,'Bac Giang','BG',1),(3753,230,'Bac Kan','BK',1),(3754,230,'Bac Lieu','BL',1),(3755,230,'Bac Ninh','BC',1),(3756,230,'Ba Ria-Vung Tau','BR',1),(3757,230,'Ben Tre','BN',1),(3758,230,'Binh Dinh','BH',1),(3759,230,'Binh Duong','BU',1),(3760,230,'Binh Phuoc','BP',1),(3761,230,'Binh Thuan','BT',1),(3762,230,'Ca Mau','CM',1),(3763,230,'Can Tho','CT',1),(3764,230,'Cao Bang','CB',1),(3765,230,'Dak Lak','DL',1),(3766,230,'Dak Nong','DG',1),(3767,230,'Da Nang','DN',1),(3768,230,'Dien Bien','DB',1),(3769,230,'Dong Nai','DI',1),(3770,230,'Dong Thap','DT',1),(3771,230,'Gia Lai','GL',1),(3772,230,'Ha Giang','HG',1),(3773,230,'Hai Duong','HD',1),(3774,230,'Hai Phong','HP',1),(3775,230,'Ha Nam','HM',1),(3776,230,'Ha Noi','HI',1),(3777,230,'Ha Tay','HT',1),(3778,230,'Ha Tinh','HH',1),(3779,230,'Hoa Binh','HB',1),(3780,230,'Ho Chi Minh City','HC',1),(3781,230,'Hau Giang','HU',1),(3782,230,'Hung Yen','HY',1),(3783,232,'Saint Croix','C',1),(3784,232,'Saint John','J',1),(3785,232,'Saint Thomas','T',1),(3786,233,'Alo','A',1),(3787,233,'Sigave','S',1),(3788,233,'Wallis','W',1),(3789,235,'Abyan','AB',1),(3790,235,'Adan','AD',1),(3791,235,'Amran','AM',1),(3792,235,'Al Bayda','BA',1),(3793,235,'Ad Dali','DA',1),(3794,235,'Dhamar','DH',1),(3795,235,'Hadramawt','HD',1),(3796,235,'Hajjah','HJ',1),(3797,235,'Al Hudaydah','HU',1),(3798,235,'Ibb','IB',1),(3799,235,'Al Jawf','JA',1),(3800,235,'Lahij','LA',1),(3801,235,'Ma\'rib','MA',1),(3802,235,'Al Mahrah','MR',1),(3803,235,'Al Mahwit','MW',1),(3804,235,'Sa\'dah','SD',1),(3805,235,'San\'a','SN',1),(3806,235,'Shabwah','SH',1),(3807,235,'Ta\'izz','TA',1),(3812,237,'Bas-Congo','BC',1),(3813,237,'Bandundu','BN',1),(3814,237,'Equateur','EQ',1),(3815,237,'Katanga','KA',1),(3816,237,'Kasai-Oriental','KE',1),(3817,237,'Kinshasa','KN',1),(3818,237,'Kasai-Occidental','KW',1),(3819,237,'Maniema','MA',1),(3820,237,'Nord-Kivu','NK',1),(3821,237,'Orientale','OR',1),(3822,237,'Sud-Kivu','SK',1),(3823,238,'Central','CE',1),(3824,238,'Copperbelt','CB',1),(3825,238,'Eastern','EA',1),(3826,238,'Luapula','LP',1),(3827,238,'Lusaka','LK',1),(3828,238,'Northern','NO',1),(3829,238,'North-Western','NW',1),(3830,238,'Southern','SO',1),(3831,238,'Western','WE',1),(3832,239,'Bulawayo','BU',1),(3833,239,'Harare','HA',1),(3834,239,'Manicaland','ML',1),(3835,239,'Mashonaland Central','MC',1),(3836,239,'Mashonaland East','ME',1),(3837,239,'Mashonaland West','MW',1),(3838,239,'Masvingo','MV',1),(3839,239,'Matabeleland North','MN',1),(3840,239,'Matabeleland South','MS',1),(3841,239,'Midlands','MD',1),(3842,105,'Agrigento','AG',1),(3843,105,'Alessandria','AL',1),(3844,105,'Ancona','AN',1),(3845,105,'Aosta','AO',1),(3846,105,'Arezzo','AR',1),(3847,105,'Ascoli Piceno','AP',1),(3848,105,'Asti','AT',1),(3849,105,'Avellino','AV',1),(3850,105,'Bari','BA',1),(3851,105,'Belluno','BL',1),(3852,105,'Benevento','BN',1),(3853,105,'Bergamo','BG',1),(3854,105,'Biella','BI',1),(3855,105,'Bologna','BO',1),(3856,105,'Bolzano','BZ',1),(3857,105,'Brescia','BS',1),(3858,105,'Brindisi','BR',1),(3859,105,'Cagliari','CA',1),(3860,105,'Caltanissetta','CL',1),(3861,105,'Campobasso','CB',1),(3862,105,'Carbonia-Iglesias','CI',1),(3863,105,'Caserta','CE',1),(3864,105,'Catania','CT',1),(3865,105,'Catanzaro','CZ',1),(3866,105,'Chieti','CH',1),(3867,105,'Como','CO',1),(3868,105,'Cosenza','CS',1),(3869,105,'Cremona','CR',1),(3870,105,'Crotone','KR',1),(3871,105,'Cuneo','CN',1),(3872,105,'Enna','EN',1),(3873,105,'Ferrara','FE',1),(3874,105,'Firenze','FI',1),(3875,105,'Foggia','FG',1),(3876,105,'Forli-Cesena','FC',1),(3877,105,'Frosinone','FR',1),(3878,105,'Genova','GE',1),(3879,105,'Gorizia','GO',1),(3880,105,'Grosseto','GR',1),(3881,105,'Imperia','IM',1),(3882,105,'Isernia','IS',1),(3883,105,'L&#39;Aquila','AQ',1),(3884,105,'La Spezia','SP',1),(3885,105,'Latina','LT',1),(3886,105,'Lecce','LE',1),(3887,105,'Lecco','LC',1),(3888,105,'Livorno','LI',1),(3889,105,'Lodi','LO',1),(3890,105,'Lucca','LU',1),(3891,105,'Macerata','MC',1),(3892,105,'Mantova','MN',1),(3893,105,'Massa-Carrara','MS',1),(3894,105,'Matera','MT',1),(3895,105,'Medio Campidano','VS',1),(3896,105,'Messina','ME',1),(3897,105,'Milano','MI',1),(3898,105,'Modena','MO',1),(3899,105,'Napoli','NA',1),(3900,105,'Novara','NO',1),(3901,105,'Nuoro','NU',1),(3902,105,'Ogliastra','OG',1),(3903,105,'Olbia-Tempio','OT',1),(3904,105,'Oristano','OR',1),(3905,105,'Padova','PD',1),(3906,105,'Palermo','PA',1),(3907,105,'Parma','PR',1),(3908,105,'Pavia','PV',1),(3909,105,'Perugia','PG',1),(3910,105,'Pesaro e Urbino','PU',1),(3911,105,'Pescara','PE',1),(3912,105,'Piacenza','PC',1),(3913,105,'Pisa','PI',1),(3914,105,'Pistoia','PT',1),(3915,105,'Pordenone','PN',1),(3916,105,'Potenza','PZ',1),(3917,105,'Prato','PO',1),(3918,105,'Ragusa','RG',1),(3919,105,'Ravenna','RA',1),(3920,105,'Reggio Calabria','RC',1),(3921,105,'Reggio Emilia','RE',1),(3922,105,'Rieti','RI',1),(3923,105,'Rimini','RN',1),(3924,105,'Roma','RM',1),(3925,105,'Rovigo','RO',1),(3926,105,'Salerno','SA',1),(3927,105,'Sassari','SS',1),(3928,105,'Savona','SV',1),(3929,105,'Siena','SI',1),(3930,105,'Siracusa','SR',1),(3931,105,'Sondrio','SO',1),(3932,105,'Taranto','TA',1),(3933,105,'Teramo','TE',1),(3934,105,'Terni','TR',1),(3935,105,'Torino','TO',1),(3936,105,'Trapani','TP',1),(3937,105,'Trento','TN',1),(3938,105,'Treviso','TV',1),(3939,105,'Trieste','TS',1),(3940,105,'Udine','UD',1),(3941,105,'Varese','VA',1),(3942,105,'Venezia','VE',1),(3943,105,'Verbano-Cusio-Ossola','VB',1),(3944,105,'Vercelli','VC',1),(3945,105,'Verona','VR',1),(3946,105,'Vibo Valentia','VV',1),(3947,105,'Vicenza','VI',1),(3948,105,'Viterbo','VT',1),(3949,222,'County Antrim','ANT',1),(3950,222,'County Armagh','ARM',1),(3951,222,'County Down','DOW',1),(3952,222,'County Fermanagh','FER',1),(3953,222,'County Londonderry','LDY',1),(3954,222,'County Tyrone','TYR',1),(3955,222,'Cumbria','CMA',1),(3956,190,'Pomurska','1',1),(3957,190,'Podravska','2',1),(3958,190,'Koroška','3',1),(3959,190,'Savinjska','4',1),(3960,190,'Zasavska','5',1),(3961,190,'Spodnjeposavska','6',1),(3962,190,'Jugovzhodna Slovenija','7',1),(3963,190,'Osrednjeslovenska','8',1),(3964,190,'Gorenjska','9',1),(3965,190,'Notranjsko-kraška','10',1),(3966,190,'Goriška','11',1),(3967,190,'Obalno-kraška','12',1),(3968,33,'Ruse','',1),(3969,101,'Alborz','ALB',1),(3970,21,'Brussels-Capital Region','BRU',1),(3971,138,'Aguascalientes','AG',1),(3973,242,'Andrijevica','01',1),(3974,242,'Bar','02',1),(3975,242,'Berane','03',1),(3976,242,'Bijelo Polje','04',1),(3977,242,'Budva','05',1),(3978,242,'Cetinje','06',1),(3979,242,'Danilovgrad','07',1),(3980,242,'Herceg-Novi','08',1),(3981,242,'Kolašin','09',1),(3982,242,'Kotor','10',1),(3983,242,'Mojkovac','11',1),(3984,242,'Nikšić','12',1),(3985,242,'Plav','13',1),(3986,242,'Pljevlja','14',1),(3987,242,'Plužine','15',1),(3988,242,'Podgorica','16',1),(3989,242,'Rožaje','17',1),(3990,242,'Šavnik','18',1),(3991,242,'Tivat','19',1),(3992,242,'Ulcinj','20',1),(3993,242,'Žabljak','21',1),(3994,243,'Belgrade','00',1),(3995,243,'North Bačka','01',1),(3996,243,'Central Banat','02',1),(3997,243,'North Banat','03',1),(3998,243,'South Banat','04',1),(3999,243,'West Bačka','05',1),(4000,243,'South Bačka','06',1),(4001,243,'Srem','07',1),(4002,243,'Mačva','08',1),(4003,243,'Kolubara','09',1),(4004,243,'Podunavlje','10',1),(4005,243,'Braničevo','11',1),(4006,243,'Šumadija','12',1),(4007,243,'Pomoravlje','13',1),(4008,243,'Bor','14',1),(4009,243,'Zaječar','15',1),(4010,243,'Zlatibor','16',1),(4011,243,'Moravica','17',1),(4012,243,'Raška','18',1),(4013,243,'Rasina','19',1),(4014,243,'Nišava','20',1),(4015,243,'Toplica','21',1),(4016,243,'Pirot','22',1),(4017,243,'Jablanica','23',1),(4018,243,'Pčinja','24',1),(4020,245,'Bonaire','BO',1),(4021,245,'Saba','SA',1),(4022,245,'Sint Eustatius','SE',1),(4023,248,'Central Equatoria','EC',1),(4024,248,'Eastern Equatoria','EE',1),(4025,248,'Jonglei','JG',1),(4026,248,'Lakes','LK',1),(4027,248,'Northern Bahr el-Ghazal','BN',1),(4028,248,'Unity','UY',1),(4029,248,'Upper Nile','NU',1),(4030,248,'Warrap','WR',1),(4031,248,'Western Bahr el-Ghazal','BW',1),(4032,248,'Western Equatoria','EW',1),(4035,129,'Putrajaya','MY-16',1),(4036,117,'Ainaži, Salacgrīvas novads','0661405',1),(4037,117,'Aizkraukle, Aizkraukles novads','0320201',1),(4038,117,'Aizkraukles novads','0320200',1),(4039,117,'Aizpute, Aizputes novads','0640605',1),(4040,117,'Aizputes novads','0640600',1),(4041,117,'Aknīste, Aknīstes novads','0560805',1),(4042,117,'Aknīstes novads','0560800',1),(4043,117,'Aloja, Alojas novads','0661007',1),(4044,117,'Alojas novads','0661000',1),(4045,117,'Alsungas novads','0624200',1),(4046,117,'Alūksne, Alūksnes novads','0360201',1),(4047,117,'Alūksnes novads','0360200',1),(4048,117,'Amatas novads','0424701',1),(4049,117,'Ape, Apes novads','0360805',1),(4050,117,'Apes novads','0360800',1),(4051,117,'Auce, Auces novads','0460805',1),(4052,117,'Auces novads','0460800',1),(4053,117,'Ādažu novads','0804400',1),(4054,117,'Babītes novads','0804900',1),(4055,117,'Baldone, Baldones novads','0800605',1),(4056,117,'Baldones novads','0800600',1),(4057,117,'Baloži, Ķekavas novads','0800807',1),(4058,117,'Baltinavas novads','0384400',1),(4059,117,'Balvi, Balvu novads','0380201',1),(4060,117,'Balvu novads','0380200',1),(4061,117,'Bauska, Bauskas novads','0400201',1),(4062,117,'Bauskas novads','0400200',1),(4063,117,'Beverīnas novads','0964700',1),(4064,117,'Brocēni, Brocēnu novads','0840605',1),(4065,117,'Brocēnu novads','0840601',1),(4066,117,'Burtnieku novads','0967101',1),(4067,117,'Carnikavas novads','0805200',1),(4068,117,'Cesvaine, Cesvaines novads','0700807',1),(4069,117,'Cesvaines novads','0700800',1),(4070,117,'Cēsis, Cēsu novads','0420201',1),(4071,117,'Cēsu novads','0420200',1),(4072,117,'Ciblas novads','0684901',1),(4073,117,'Dagda, Dagdas novads','0601009',1),(4074,117,'Dagdas novads','0601000',1),(4075,117,'Daugavpils','0050000',1),(4076,117,'Daugavpils novads','0440200',1),(4077,117,'Dobele, Dobeles novads','0460201',1),(4078,117,'Dobeles novads','0460200',1),(4079,117,'Dundagas novads','0885100',1),(4080,117,'Durbe, Durbes novads','0640807',1),(4081,117,'Durbes novads','0640801',1),(4082,117,'Engures novads','0905100',1),(4083,117,'Ērgļu novads','0705500',1),(4084,117,'Garkalnes novads','0806000',1),(4085,117,'Grobiņa, Grobiņas novads','0641009',1),(4086,117,'Grobiņas novads','0641000',1),(4087,117,'Gulbene, Gulbenes novads','0500201',1),(4088,117,'Gulbenes novads','0500200',1),(4089,117,'Iecavas novads','0406400',1),(4090,117,'Ikšķile, Ikšķiles novads','0740605',1),(4091,117,'Ikšķiles novads','0740600',1),(4092,117,'Ilūkste, Ilūkstes novads','0440807',1),(4093,117,'Ilūkstes novads','0440801',1),(4094,117,'Inčukalna novads','0801800',1),(4095,117,'Jaunjelgava, Jaunjelgavas novads','0321007',1),(4096,117,'Jaunjelgavas novads','0321000',1),(4097,117,'Jaunpiebalgas novads','0425700',1),(4098,117,'Jaunpils novads','0905700',1),(4099,117,'Jelgava','0090000',1),(4100,117,'Jelgavas novads','0540200',1),(4101,117,'Jēkabpils','0110000',1),(4102,117,'Jēkabpils novads','0560200',1),(4103,117,'Jūrmala','0130000',1),(4104,117,'Kalnciems, Jelgavas novads','0540211',1),(4105,117,'Kandava, Kandavas novads','0901211',1),(4106,117,'Kandavas novads','0901201',1),(4107,117,'Kārsava, Kārsavas novads','0681009',1),(4108,117,'Kārsavas novads','0681000',1),(4109,117,'Kocēnu novads ,bij. Valmieras)','0960200',1),(4110,117,'Kokneses novads','0326100',1),(4111,117,'Krāslava, Krāslavas novads','0600201',1),(4112,117,'Krāslavas novads','0600202',1),(4113,117,'Krimuldas novads','0806900',1),(4114,117,'Krustpils novads','0566900',1),(4115,117,'Kuldīga, Kuldīgas novads','0620201',1),(4116,117,'Kuldīgas novads','0620200',1),(4117,117,'Ķeguma novads','0741001',1),(4118,117,'Ķegums, Ķeguma novads','0741009',1),(4119,117,'Ķekavas novads','0800800',1),(4120,117,'Lielvārde, Lielvārdes novads','0741413',1),(4121,117,'Lielvārdes novads','0741401',1),(4122,117,'Liepāja','0170000',1),(4123,117,'Limbaži, Limbažu novads','0660201',1),(4124,117,'Limbažu novads','0660200',1),(4125,117,'Līgatne, Līgatnes novads','0421211',1),(4126,117,'Līgatnes novads','0421200',1),(4127,117,'Līvāni, Līvānu novads','0761211',1),(4128,117,'Līvānu novads','0761201',1),(4129,117,'Lubāna, Lubānas novads','0701413',1),(4130,117,'Lubānas novads','0701400',1),(4131,117,'Ludza, Ludzas novads','0680201',1),(4132,117,'Ludzas novads','0680200',1),(4133,117,'Madona, Madonas novads','0700201',1),(4134,117,'Madonas novads','0700200',1),(4135,117,'Mazsalaca, Mazsalacas novads','0961011',1),(4136,117,'Mazsalacas novads','0961000',1),(4137,117,'Mālpils novads','0807400',1),(4138,117,'Mārupes novads','0807600',1),(4139,117,'Mērsraga novads','0887600',1),(4140,117,'Naukšēnu novads','0967300',1),(4141,117,'Neretas novads','0327100',1),(4142,117,'Nīcas novads','0647900',1),(4143,117,'Ogre, Ogres novads','0740201',1),(4144,117,'Ogres novads','0740202',1),(4145,117,'Olaine, Olaines novads','0801009',1),(4146,117,'Olaines novads','0801000',1),(4147,117,'Ozolnieku novads','0546701',1),(4148,117,'Pārgaujas novads','0427500',1),(4149,117,'Pāvilosta, Pāvilostas novads','0641413',1),(4150,117,'Pāvilostas novads','0641401',1),(4151,117,'Piltene, Ventspils novads','0980213',1),(4152,117,'Pļaviņas, Pļaviņu novads','0321413',1),(4153,117,'Pļaviņu novads','0321400',1),(4154,117,'Preiļi, Preiļu novads','0760201',1),(4155,117,'Preiļu novads','0760202',1),(4156,117,'Priekule, Priekules novads','0641615',1),(4157,117,'Priekules novads','0641600',1),(4158,117,'Priekuļu novads','0427300',1),(4159,117,'Raunas novads','0427700',1),(4160,117,'Rēzekne','0210000',1),(4161,117,'Rēzeknes novads','0780200',1),(4162,117,'Riebiņu novads','0766300',1),(4163,117,'Rīga','0010000',1),(4164,117,'Rojas novads','0888300',1),(4165,117,'Ropažu novads','0808400',1),(4166,117,'Rucavas novads','0648500',1),(4167,117,'Rugāju novads','0387500',1),(4168,117,'Rundāles novads','0407700',1),(4169,117,'Rūjiena, Rūjienas novads','0961615',1),(4170,117,'Rūjienas novads','0961600',1),(4171,117,'Sabile, Talsu novads','0880213',1),(4172,117,'Salacgrīva, Salacgrīvas novads','0661415',1),(4173,117,'Salacgrīvas novads','0661400',1),(4174,117,'Salas novads','0568700',1),(4175,117,'Salaspils novads','0801200',1),(4176,117,'Salaspils, Salaspils novads','0801211',1),(4177,117,'Saldus novads','0840200',1),(4178,117,'Saldus, Saldus novads','0840201',1),(4179,117,'Saulkrasti, Saulkrastu novads','0801413',1),(4180,117,'Saulkrastu novads','0801400',1),(4181,117,'Seda, Strenču novads','0941813',1),(4182,117,'Sējas novads','0809200',1),(4183,117,'Sigulda, Siguldas novads','0801615',1),(4184,117,'Siguldas novads','0801601',1),(4185,117,'Skrīveru novads','0328200',1),(4186,117,'Skrunda, Skrundas novads','0621209',1),(4187,117,'Skrundas novads','0621200',1),(4188,117,'Smiltene, Smiltenes novads','0941615',1),(4189,117,'Smiltenes novads','0941600',1),(4190,117,'Staicele, Alojas novads','0661017',1),(4191,117,'Stende, Talsu novads','0880215',1),(4192,117,'Stopiņu novads','0809600',1),(4193,117,'Strenči, Strenču novads','0941817',1),(4194,117,'Strenču novads','0941800',1),(4195,117,'Subate, Ilūkstes novads','0440815',1),(4196,117,'Talsi, Talsu novads','0880201',1),(4197,117,'Talsu novads','0880200',1),(4198,117,'Tērvetes novads','0468900',1),(4199,117,'Tukuma novads','0900200',1),(4200,117,'Tukums, Tukuma novads','0900201',1),(4201,117,'Vaiņodes novads','0649300',1),(4202,117,'Valdemārpils, Talsu novads','0880217',1),(4203,117,'Valka, Valkas novads','0940201',1),(4204,117,'Valkas novads','0940200',1),(4205,117,'Valmiera','0250000',1),(4206,117,'Vangaži, Inčukalna novads','0801817',1),(4207,117,'Varakļāni, Varakļānu novads','0701817',1),(4208,117,'Varakļānu novads','0701800',1),(4209,117,'Vārkavas novads','0769101',1),(4210,117,'Vecpiebalgas novads','0429300',1),(4211,117,'Vecumnieku novads','0409500',1),(4212,117,'Ventspils','0270000',1),(4213,117,'Ventspils novads','0980200',1),(4214,117,'Viesīte, Viesītes novads','0561815',1),(4215,117,'Viesītes novads','0561800',1),(4216,117,'Viļaka, Viļakas novads','0381615',1),(4217,117,'Viļakas novads','0381600',1),(4218,117,'Viļāni, Viļānu novads','0781817',1),(4219,117,'Viļānu novads','0781800',1),(4220,117,'Zilupe, Zilupes novads','0681817',1),(4221,117,'Zilupes novads','0681801',1),(4222,43,'Arica y Parinacota','AP',1),(4223,43,'Los Rios','LR',1),(4224,220,'Kharkivs\'ka Oblast\'','63',1),(4225,118,'Beirut','LB-BR',1),(4226,118,'Bekaa','LB-BE',1),(4227,118,'Mount Lebanon','LB-ML',1),(4228,118,'Nabatieh','LB-NB',1),(4229,118,'North','LB-NR',1),(4230,118,'South','LB-ST',1),(4231,99,'Chhattisgarh','CG',1),(4232,99,'Jharkhand','JH',1),(4233,99,'Uttarakhand','CG',1),(4234,99,'Telangana','TG',1);
/*!40000 ALTER TABLE `39_infinite_states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_infinite_urls`
--

DROP TABLE IF EXISTS `39_infinite_urls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_infinite_urls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link` varchar(200) CHARACTER SET utf8 NOT NULL,
  `status` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `target` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'none',
  `sub_menu_ref_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `link` (`link`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=300 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_infinite_urls`
--

LOCK TABLES `39_infinite_urls` WRITE;
/*!40000 ALTER TABLE `39_infinite_urls` DISABLE KEYS */;
INSERT INTO `39_infinite_urls` VALUES (1,'login/forgot_password','yes','none',0),(2,'login/index','yes','none',0),(3,'login/login_employee','yes','none',0),(4,'login/logout','yes','none',0),(5,'login/reset_password','yes','none',0),(6,'login/unsubscribe_email','yes','none',0),(7,'home/index','yes','none',0),(8,'configuration/commission_settings','yes','none',0),(9,'configuration/content_management','yes','none',0),(10,'configuration/mail_settings','yes','none',0),(11,'configuration/my_referal','yes','none',0),(12,'profile/user_account','yes','none',0),(13,'configuration/pin_config','yes','none',0),(14,'configuration/rank_configuration','yes','none',0),(15,'configuration/set_module_status','yes','none',0),(16,'configuration/site_information','yes','none',0),(17,'configuration/sms_settings','yes','none',0),(19,'register/user_register','yes','none',0),(22,'profile/profile_view','yes','none',0),(24,'password/change_password','yes','none',46),(25,'member/search_member','yes','none',0),(26,'epin/allocate_pin_user','yes','none',0),(27,'epin/epin_management','yes','none',0),(28,'epin/my_epin','yes','none',0),(29,'epin/request_epin','yes','none',0),(30,'epin/view_epin_request','yes','none',0),(32,'ewallet/ewallet_pin_purchase','yes','none',0),(33,'ewallet/fund_management','yes','none',0),(34,'ewallet/fund_transfer','yes','none',0),(35,'ewallet/my_ewallet','yes','none',0),(36,'ewallet/my_transfer_details','yes','none',0),(38,'payout/my_income','yes','none',0),(39,'payout/payout_release','yes','none',0),(40,'payout/payout_release_request','yes','none',0),(41,'employee/employee_change_password','yes','none',0),(42,'employee/employee_register','yes','none',0),(43,'employee/search_employee','yes','none',0),(44,'employee/set_employee_permission','yes','none',0),(45,'select_report/admin_profile_report','yes','none',0),(46,'select_report/commission_report','yes','none',0),(47,'select_report/epin_report','yes','none',0),(48,'select_report/my_transfer_details','yes','none',0),(49,'select_report/rank_achievers_report','yes','none',0),(50,'select_report/sales_report','yes','none',0),(51,'select_report/total_joining_report','yes','none',0),(52,'select_report/total_payout_report','yes','none',0),(54,'sms/send_sms','yes','none',0),(55,'sms/sms_balance','yes','none',0),(56,'leg_count/view_leg_count','yes','none',0),(57,'boardview/board_view_management','yes','none',0),(58,'tree/genology_tree','yes','none',0),(59,'tree/select_tree','yes','none',0),(61,'tree/sponsor_tree','yes','none',0),(62,'feedback/feedback_view','yes','none',0),(63,'income_details/income','yes','none',0),(64,'news/add_news','yes','none',0),(65,'news/upload_materials','yes','none',0),(66,'news/view_news','yes','none',0),(67,'tran_pass/change_passcode','yes','none',0),(69,'epin/view_pin_user','yes','none',0),(70,'configuration/payment_view','yes','none',0),(71,'employee/view_all_employee','yes','none',0),(74,'configuration/payment_gateway_configuration','yes','none',4),(77,'member/leads','yes','none',0),(78,'my_report/binary_history','yes','none',1),(79,'my_report/unilevel_history','yes','none',3),(81,'currency/currency_management','yes','none',0),(82,'currency/edit_currency','yes','none',4),(84,'mail/compose_mail','yes','none',0),(85,'mail/mail_sent','yes','none',0),(86,'configuration/language_settings','yes','none',0),(87,'ticket/ticket_system','yes','_blank',0),(88,'select_report/top_earners_report','yes','none',0),(89,'member/invites','yes','none',0),(90,'member/text_invite_configuration','yes','none',0),(91,'member/edit_invite_text','yes','none',58),(92,'member/invite_wallpost_config','yes','none',0),(93,'member/invite_banner_config','yes','none',0),(95,'mail/mail_management','yes','none',0),(97,'document/download_document','yes','none',0),(98,'configuration/opencart','yes','_blank',0),(101,'boardview/view_board_details','yes','none',0),(103,'party_setup/create_party','yes','none',0),(104,'party_setup/promote_party','yes','none',0),(105,'myparty/party_portal','yes','none',0),(106,'party/invite_guest','yes','none',0),(107,'party/create_guest','yes','none',0),(108,'party_guest_order/guest_orders','yes','none',0),(109,'party_guest_order/select_product','yes','none',0),(110,'party_guest_order/edit_order','yes','none',0),(111,'party_guest_order/view_order','yes','none',0),(112,'party/host_manager','yes','none',0),(113,'party/create_host','yes','none',0),(114,'party/guest_manager','yes','none',0),(115,'party/view_order','yes','none',0),(116,'activate/activate_deactivate','yes','none',0),(120,'tree/board_view','yes','none',0),(121,'configuration/payout_setting','yes','none',0),(122,'mail/reply_mail','yes','none',0),(123,'select_report/activate_deactivate_report','yes','none',0),(124,'select_report/total_joining_report','yes','none',0),(125,'order/order_history','yes','none',0),(127,'activity_history/activity_history_view','yes','none',0),(128,'ewallet/business_wallet','yes','none',0),(133,'select_report/payout_release_report','yes','none',0),(135,'crm/index','yes','none',0),(136,'crm/add_lead','yes','none',0),(137,'crm/view_lead','yes','none',0),(138,'crm/graph','yes','none',0),(139,'crm/add_followup','yes','none',83),(140,'crm/timeline','yes','none',83),(141,'crm/add_followup','yes','none',0),(142,'crm/view_followups','yes','none',0),(145,'ticket_system/admin_home_page','yes','none',0),(146,'ticket_system/view_ticket','yes','none',0),(147,'ticket_system/ticket','yes','none',0),(148,'ticket_system/category','yes','none',0),(149,'ticket_system/configuration','yes','none',0),(150,'ticket_system/ticket_assign','yes','none',0),(151,'ticket_system/resolved_ticket','yes','none',0),(152,'ticket_system/faq','yes','none',0),(153,'repurchase/repurchase_product','yes','none',0),(154,'repurchase/checkout_product','yes','none',0),(155,'select_report/repurchase_report','yes','none',0),(156,'employee/activity_history','yes','none',0),(157,'profile/business_volume','yes','none',0),(158,'configuration/stairstep_configuration','yes','none',0),(159,'select_report/stair_step_report','yes','none',0),(160,'select_report/override_report','yes','none',0),(161,'tree/step_view','yes','none',0),(162,'member/package_validity','yes','none',0),(163,'member/upgrade_package_validity','yes','none',92),(164,'tree_updation/delete_user','yes','none',0),(165,'tree_updation/change_placement','yes','none',0),(166,'tree_updation/change_sponsor','yes','none',0),(167,'order/order_activation_pending','yes','none',0),(168,'configuration/clear_cache','yes','none',0),(170,'configuration/add_new_rank','yes','none',0),(173,'epin/add_new_epin','yes','none',0),(180,'epin/search_epin','yes','none',0),(181,'configuration/signup_settings','yes','none',0),(182,'member/pending_registration','yes','none',0),(183,'select_report/rank_performance_report','yes','none',0),(184,'select_report/config_changes_report','yes','none',0),(185,'configuration/replica_configuration','yes','none',0),(187,'product/membership_package','yes','none',0),(188,'product/repurchase_package','yes','none',0),(189,'product/add_membership_package','yes','none',108),(190,'product/add_repurchase_package','yes','none',109),(191,'product/edit_membership_package','yes','none',108),(192,'product/edit_repurchase_package','yes','none',109),(193,'epin/epin_transfer','yes','none',0),(194,'configuration/inactivity_logout','yes','none',0),(195,'payout/mark_paid','yes','none',0),(196,'upgrade/package_upgrade','yes','none',0),(198,'tran_pass/forgot_trans_password','yes','none',0),(200,'auto_responder/auto_responder_details','yes','none',0),(201,'epin/allocate_user','yes','none',17),(202,'donation/donation_view','yes','none',0),(204,'configuration/menu_permission','yes','none',0),(205,'donation/recieve_donation_report','yes','none',0),(206,'donation/sent_donation_report','yes','none',0),(207,'select_report/roi_report','yes','none',0),(212,'donation/manage_userlevel','yes','none',0),(213,'donation/missed_donation_report','yes','none',0),(214,'donation/pending_donation','yes','none',0),(216,'configuration/cron_status','yes','none',0),(217,'configuration/holidays_settings','yes','none',0),(219,'donation/given_donation_report','yes','none',0),(220,'profile/forget_me_request','yes','none',0),(221,'profile/users_forget_request','yes','none',0),(222,'configuration/Opencartadmin','yes','_blank',0),(223,'configuration/kyc_configuration','yes','none',0),(224,'member/upgrade_package','yes','none',0),(225,'profile/kyc','yes','none',0),(226,'profile/kyc_upload','yes','none',0),(227,'payout/my_withdrawal_request','yes','none',0),(228,'product/repurchase_category','yes','none',0),(229,'report/revenue_report','yes','none',0),(230,'news/add_faq','yes','none',0),(231,'report/transaction_errors','yes','none',0),(232,'news/faq','yes','none',0),(233,'member/update_pv','yes','none',0),(234,'excel_register/excel_register','yes','none',0),(235,'signup/signup','yes','none',0),(236,'home/campaign','yes','none',0),(237,'project/new_project','yes','none',0),(238,'report/personal_data_export','yes','0',0),(239,'product/add_repurchase_category','yes','none',0),(240,'product/edit_repurchase_category','yes','none',0),(242,'ewallet/purchase_ewallet','yes','0',0),(252,'member/pending_orders','yes','none',0),(275,'member/manage_members','yes','none',0),(276,'ewallet/purchase_wallet','yes','none',0),(277,'ewallet/all_transactions','yes','none',0),(278,'ewallet/outward_funds','yes','none',0),(279,'configuration/pin_settings','yes','none',0),(280,'ewallet/business_summary','yes','none',0),(281,'ewallet/business_transactions','yes','none',0),(282,'ewallet/balance_report','yes','none',0),(283,'configuration/plan_settings','yes','none',0),(285,'member/promotion_tools','yes','none',0),(286,'configuration/mail_content','yes','none',0),(287,'admin/configuration/generate_api_key','yes','none',0),(288,'select_report/package_upgrade_report','yes','none',0),(289,'sms/edit_sms_content','yes','none',0),(290,'sms/sms_content','yes','none',0),(291,'select_report/subscription_report','yes','none',0),(292,'configuration/profile_setting','yes','none',0),(293,'business','yes','none',0),(294,'ewallet','yes','none',0),(295,'payout','yes','none',0),(296,'epin','no','none',0),(297,'user/ewallet','yes','none',0),(298,'user/epin','yes','none',0),(299,'user/payout','yes','none',0);
/*!40000 ALTER TABLE `39_infinite_urls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_infinite_user_registration_details`
--

DROP TABLE IF EXISTS `39_infinite_user_registration_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_infinite_user_registration_details` (
  `reg_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `user_name` varchar(200) CHARACTER SET utf8 NOT NULL,
  `sponsor_id` int(11) unsigned NOT NULL,
  `placement_id` int(11) unsigned NOT NULL,
  `position` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'NA',
  `first_name` varchar(200) CHARACTER SET utf8 NOT NULL,
  `last_name` varchar(200) DEFAULT NULL,
  `address` varchar(200) CHARACTER SET utf8 NOT NULL,
  `address_line2` varchar(200) CHARACTER SET utf8 NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `country_name` varchar(200) CHARACTER SET utf8 NOT NULL,
  `state_id` int(11) DEFAULT NULL,
  `state_name` varchar(200) CHARACTER SET utf8 NOT NULL,
  `city` varchar(200) CHARACTER SET utf8 NOT NULL,
  `email` varchar(500) CHARACTER SET utf8 NOT NULL,
  `mobile` varchar(200) CHARACTER SET utf8 NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT 'NA',
  `product_pv` double NOT NULL DEFAULT '0',
  `product_amount` double NOT NULL DEFAULT '0',
  `reg_amount` double NOT NULL DEFAULT '0',
  `total_amount` int(11) NOT NULL DEFAULT '0',
  `reg_date` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  PRIMARY KEY (`reg_id`),
  KEY `user_id` (`user_id`),
  KEY `sponsor_id` (`sponsor_id`),
  KEY `placement_id` (`placement_id`),
  KEY `country_id` (`country_id`),
  KEY `state_id` (`state_id`),
  CONSTRAINT `39_infinite_user_registration_details_ibfk_36` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_infinite_user_registration_details_ibfk_37` FOREIGN KEY (`sponsor_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_infinite_user_registration_details_ibfk_38` FOREIGN KEY (`placement_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_infinite_user_registration_details_ibfk_39` FOREIGN KEY (`country_id`) REFERENCES `39_infinite_countries` (`country_id`),
  CONSTRAINT `39_infinite_user_registration_details_ibfk_40` FOREIGN KEY (`state_id`) REFERENCES `39_infinite_states` (`state_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_infinite_user_registration_details`
--

LOCK TABLES `39_infinite_user_registration_details` WRITE;
/*!40000 ALTER TABLE `39_infinite_user_registration_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_infinite_user_registration_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_invite_history`
--

DROP TABLE IF EXISTS `39_invite_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_invite_history` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `mail_id` varchar(100) CHARACTER SET utf8 NOT NULL,
  `subject` varchar(100) CHARACTER SET utf8 NOT NULL,
  `message` text CHARACTER SET utf8 NOT NULL,
  `date` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `39_invite_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_invite_history`
--

LOCK TABLES `39_invite_history` WRITE;
/*!40000 ALTER TABLE `39_invite_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_invite_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_invites_configuration`
--

DROP TABLE IF EXISTS `39_invites_configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_invites_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(100) CHARACTER SET utf8 NOT NULL,
  `content` longtext,
  `type` varchar(100) CHARACTER SET utf8 NOT NULL,
  `target_url` longtext,
  `uploaded_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_invites_configuration`
--

LOCK TABLES `39_invites_configuration` WRITE;
/*!40000 ALTER TABLE `39_invites_configuration` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_invites_configuration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_joinee_rank`
--

DROP TABLE IF EXISTS `39_joinee_rank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_joinee_rank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rank_id` int(11) NOT NULL,
  `package_id` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_joinee_rank`
--

LOCK TABLES `39_joinee_rank` WRITE;
/*!40000 ALTER TABLE `39_joinee_rank` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_joinee_rank` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_kyc_category`
--

DROP TABLE IF EXISTS `39_kyc_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_kyc_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` text NOT NULL,
  `status` text,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_kyc_category`
--

LOCK TABLES `39_kyc_category` WRITE;
/*!40000 ALTER TABLE `39_kyc_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_kyc_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_kyc_docs`
--

DROP TABLE IF EXISTS `39_kyc_docs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_kyc_docs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `file_name` varchar(100) NOT NULL,
  `type` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `reason` varchar(2000) NOT NULL DEFAULT 'NA',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  KEY `status` (`status`),
  CONSTRAINT `39_kyc_docs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_kyc_docs_ibfk_2` FOREIGN KEY (`type`) REFERENCES `39_kyc_category` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_kyc_docs`
--

LOCK TABLES `39_kyc_docs` WRITE;
/*!40000 ALTER TABLE `39_kyc_docs` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_kyc_docs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_leg_amount`
--

DROP TABLE IF EXISTS `39_leg_amount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_leg_amount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `from_id` int(11) unsigned DEFAULT NULL,
  `total_leg` int(11) NOT NULL DEFAULT '0',
  `left_leg` int(11) NOT NULL DEFAULT '0',
  `right_leg` int(11) NOT NULL DEFAULT '0',
  `total_amount` float NOT NULL DEFAULT '0',
  `amount_payable` float NOT NULL DEFAULT '0',
  `purchase_wallet` double NOT NULL DEFAULT '0',
  `amount_type` varchar(50) CHARACTER SET utf8 NOT NULL,
  `tds` float NOT NULL DEFAULT '0',
  `date_of_submission` datetime DEFAULT NULL,
  `service_charge` float NOT NULL DEFAULT '0',
  `user_level` int(20) DEFAULT '0',
  `released_date` date NOT NULL DEFAULT '0001-01-01',
  `product_id` int(11) NOT NULL DEFAULT '0',
  `pair_value` int(11) NOT NULL DEFAULT '0',
  `product_value` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `amount_type` (`amount_type`),
  KEY `date_of_submission` (`date_of_submission`),
  KEY `user_id` (`user_id`),
  KEY `from_id` (`from_id`),
  CONSTRAINT `39_leg_amount_ibfk_15` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_leg_amount_ibfk_16` FOREIGN KEY (`from_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_leg_amount`
--

LOCK TABLES `39_leg_amount` WRITE;
/*!40000 ALTER TABLE `39_leg_amount` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_leg_amount` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_leg_details`
--

DROP TABLE IF EXISTS `39_leg_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_leg_details` (
  `no` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) unsigned NOT NULL,
  `total_left_count` double NOT NULL DEFAULT '0',
  `total_right_count` double NOT NULL DEFAULT '0',
  `total_left_carry` double NOT NULL DEFAULT '0',
  `total_right_carry` double NOT NULL DEFAULT '0',
  `total_active` double NOT NULL DEFAULT '0',
  `total_inactive` double NOT NULL DEFAULT '0',
  `left_carry_forward` int(11) NOT NULL DEFAULT '0',
  `right_carry_forward` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`no`),
  KEY `id` (`id`),
  CONSTRAINT `39_leg_details_ibfk_1` FOREIGN KEY (`id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_leg_details`
--

LOCK TABLES `39_leg_details` WRITE;
/*!40000 ALTER TABLE `39_leg_details` DISABLE KEYS */;
INSERT INTO `39_leg_details` VALUES (1,32,0,0,0,0,0,0,0,0);
/*!40000 ALTER TABLE `39_leg_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_letter_config`
--

DROP TABLE IF EXISTS `39_letter_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_letter_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(500) CHARACTER SET utf8 NOT NULL DEFAULT 'NA',
  `address_of_company` text CHARACTER SET utf8,
  `main_matter` text CHARACTER SET utf8,
  `logo` varchar(500) CHARACTER SET utf8 NOT NULL DEFAULT 'NA',
  `place` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT 'Calicut',
  `lang_ref_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_letter_config`
--

LOCK TABLES `39_letter_config` WRITE;
/*!40000 ALTER TABLE `39_letter_config` DISABLE KEYS */;
INSERT INTO `39_letter_config` VALUES (1,'Your Company Name','REC,NIT,calicut,kerala','<p>Dear Distributor,\nCongratulations on your decision...!\nA journey of thousand miles must begin with a single step.\nI&#39;d like to welcome you to COMPANY NAME. We are excited that you have accepted our business offer and agreed upon your start date. I trusted that this letter finds you mutually excited about your new opportunity with COMPANY NAME. Each of us will play a role to ensure your successful integration into the company. Your agenda will involve planning your orientation with company and setting some initial work goals so that you feel immediately productive in your new role. And furthur growing into an integral part of this business. Please note we are providing you an opportunity to earn money which is optional, your earnings will depend directly in the amount of efforts you put to develop your business.\nAgain, welcome to the team. If you have questions prior to your start date, please call me at any time, or send email if that is more convenient. We look forward to having you come onboard.\nThe secret of success is constancy to purpose.asdas\nALL THE BEST, SEE YOU AT TOP!!</p>','logo1.png','Calicut',1),(2,'Your Company Name','REC,NIT,calicut,kerala','<p> Estimado Distribuidor,\n¡Felicitaciones por tu decisión ...!\nUn viaje de mil millas debe comenzar con un solo paso.\nMe gustaría darte la bienvenida a COMPANY NAME. Nos complace que haya aceptado nuestra oferta comercial y acordado su fecha de inicio. Confié en que esta carta lo encuentre mutuamente entusiasmado con su nueva oportunidad con COMPANY NAME. Cada uno de nosotros desempeñará un papel para garantizar su integración exitosa en la empresa. Su agenda implicará planificar su orientación con la empresa y establecer algunos objetivos de trabajo iniciales para que se sienta productivo de inmediato en su nuevo cargo. Y, además, convertirse en una parte integral de este negocio. Tenga en cuenta que le brindamos la oportunidad de ganar dinero, que es opcional, sus ganancias dependerán directamente de la cantidad de esfuerzos que realice para desarrollar su negocio.\nDe nuevo, bienvenido al equipo. Si tiene preguntas antes de su fecha de inicio, llámeme en cualquier momento o envíe un correo electrónico si es más conveniente. Esperamos contar con usted a bordo.\nEl secreto del éxito es la constancia en el propósito.\nTODO LO MEJOR, ¡TE VOCIENDO EN LA PARTE SUPERIOR!','ioss_logo.gif','Calicut',2),(3,'Your Company Name','REC，NIT，卡利卡特，喀拉拉邦\r\n','<p>尊敬的经销商，\n祝贺您的决定...！\n一千英里的旅程必须从第一步开始。\n我想欢迎您加入COMPANY NAME。很高兴您接受我们的业务报价并同意您的开始日期。我相信，这封信会让您为与“公司名称”带来的新机遇而感到兴奋。我们每个人都将扮演确保您成功融入公司的角色。您的议程将包括计划在公司的入职培训并设定一些初始工作目标，以使您在担任新职务时立即感到富有成效。并且进一步发展成为该业务不可或缺的一部分。请注意，我们为您提供了赚钱的机会，这是可选的，您的收入将直接取决于您为发展业务而付出的努力。\n再次欢迎您加入团队。如果您在开始日期之前有任何疑问，请随时致电我，或者如果方便的话可以发送电子邮件。我们期待您的加入。\n成功的秘诀在于坚持目标。\n祝一切顺利，见到您！</ p>','ioss_logo.gif','卡利卡特',3),(4,'Your Company Name','REC,NIT,calicut,kerala','<p> Lieber Distributor,\nHerzlichen Glückwunsch zu Ihrer Entscheidung ...!\nEine Reise von tausend Meilen muss mit einem einzigen Schritt beginnen.\nIch möchte Sie bei FIRMENNAME begrüßen. Wir freuen uns, dass Sie unser Geschäftsangebot angenommen und Ihren Starttermin vereinbart haben. Ich vertraute darauf, dass Sie sich in diesem Brief gegenseitig über Ihre neue Chance mit FIRMENNAME freuen. Jeder von uns wird eine Rolle spielen, um Ihre erfolgreiche Integration in das Unternehmen zu gewährleisten. Ihre Agenda beinhaltet die Planung Ihrer Ausrichtung mit dem Unternehmen und die Festlegung einiger anfänglicher Arbeitsziele, damit Sie sich in Ihrer neuen Rolle sofort produktiv fühlen. Und weiter wachsen zu einem integralen Bestandteil dieses Geschäfts. Bitte beachten Sie, dass wir Ihnen die Möglichkeit bieten, optionales Geld zu verdienen. Ihre Einnahmen hängen direkt von den Anstrengungen ab, die Sie zur Entwicklung Ihres Geschäfts unternommen haben.\nNochmals herzlich willkommen im Team. Wenn Sie Fragen vor Ihrem Starttermin haben, rufen Sie mich bitte jederzeit an oder senden Sie eine E-Mail, wenn dies praktischer ist. Wir freuen uns, Sie an Bord zu haben.\nDas Erfolgsgeheimnis ist Konstanz zum Zweck\nAlles Gute, wir sehen uns oben !! </ p>','ioss_logo.gif','Calicut',4),(5,'Your Company Name','REC,NIT,calicut,kerala','<p>Prezado Distribuidor,\nParabéns pela sua decisão ...!\nUma jornada de mil milhas deve começar com um único passo.\nGostaria de recebê-lo no nome da empresa. Estamos empolgados por você ter aceitado nossa oferta comercial e ter concordado com sua data de início. Confiei que esta carta o deixasse empolgado com sua nova oportunidade com a NOME DA EMPRESA. Cada um de nós desempenhará um papel para garantir sua integração bem-sucedida na empresa. Sua agenda envolverá o planejamento de sua orientação com a empresa e o estabelecimento de algumas metas de trabalho iniciais, para que você se sinta imediatamente produtivo em sua nova função. E a furthur está se tornando parte integrante desse negócio. Observe que estamos oferecendo a você uma oportunidade de ganhar dinheiro opcional, seus ganhos dependerão diretamente da quantidade de esforços que você fizer para desenvolver seus negócios.\nMais uma vez, bem-vindo à equipe. Se você tiver dúvidas antes da data de início, ligue para mim a qualquer momento ou envie um e-mail, se for mais conveniente. Esperamos ter você a bordo.\nO segredo do sucesso é a constância para o propósito.\nTODO O MELHOR, Vejo você no topo !! </p>','ioss_logo.gif','Calicut',5),(6,'Your Company Name','REC,NIT,calicut,kerala','<p> Cher distributeur,\nFélicitations pour votre décision ...!\nUn voyage de mille kilomètres doit commencer par un seul pas.\nJe souhaite vous souhaiter la bienvenue à COMPANY NAME. Nous sommes ravis que vous ayez accepté notre offre commerciale et convenu de votre date de début. J\'espère que cette lettre vous trouvera mutuellement enthousiasmé par votre nouvelle opportunité avec COMPANY NAME. Chacun de nous jouera un rôle pour assurer votre intégration réussie dans la société. Votre ordre du jour impliquera de planifier votre orientation avec l’entreprise et de fixer des objectifs de travail initiaux afin que vous vous sentiez immédiatement productif dans votre nouveau rôle. Et furthur devenant une partie intégrante de cette entreprise. Veuillez noter que nous vous offrons la possibilité de gagner de l\'argent, ce qui est facultatif. Vos revenus dépendront directement des efforts que vous déploierez pour développer votre entreprise.\nEncore une fois, bienvenue dans l\'équipe. Si vous avez des questions avant votre date de début, appelez-moi à tout moment ou envoyez un e-mail si cela vous convient mieux. Nous sommes impatients de vous voir à bord.\nLe secret du succès est la constance à purpose.asdas\nTOUS LES MEILLEURS, Rendez-vous au sommet! </ P>','ioss_logo.gif','Calicut',6),(7,'Your Company Name','REC,NIT,calicut,kerala','<p> Gentile distributore,\nCongratulazioni per la tua decisione ...!\nUn viaggio di mille miglia deve iniziare con un solo passo.\nMi piacerebbe darti il ​​benvenuto in NOME AZIENDA. Siamo lieti che tu abbia accettato la nostra offerta commerciale e concordato la tua data di inizio. Mi fidavo che questa lettera ti trovasse reciprocamente entusiasta della tua nuova opportunità con COMPANY NAME. Ognuno di noi svolgerà un ruolo per garantire la corretta integrazione nella società. La tua agenda coinvolgerà la pianificazione del tuo orientamento con l\'azienda e la definizione di alcuni obiettivi di lavoro iniziali in modo da sentirti immediatamente produttivo nel tuo nuovo ruolo. E Furthur sta diventando parte integrante di questo business. Ti preghiamo di notare che ti stiamo offrendo l\'opportunità di guadagnare denaro che è facoltativo, i tuoi guadagni dipenderanno direttamente dalla quantità di sforzi che fai per sviluppare la tua attività.\nAncora una volta, benvenuto nella squadra. Se hai domande prima della data di inizio, chiamami in qualsiasi momento o invia un\'e-mail se è più conveniente. Non vediamo l\'ora di farti salire a bordo.\nIl segreto del successo è la costanza allo scopo.asdas\nTUTTO IL MEGLIO, CI VEDIAMO AL TOP !! </p>','REC,NIT,calicut,kerala','Calicut',7),(8,'Your Company Name','REC,NIT,calicut,kerala','<p> Sayın Bayimiz,\nKararın için tebrikler ...!\nBin mil yolculuk tek bir adımla başlamalıdır.\nŞİRKET ADI \'na hoş geldiniz demek istiyorum. İş teklifimizi kabul ettiğiniz ve başlangıç ​​tarihinizi kabul ettiğiniz için çok heyecanlıyız. Bu mektubun, COMPANY NAME ile yeni fırsatınız için karşılıklı olarak sizi heyecanlandıracağına inandım. Her birimiz şirkete başarılı bir şekilde entegrasyonunuzu sağlamada rol oynayacağız. Gündeminiz, şirketinize yöneliminizi planlamayı ve yeni görevinizde derhal üretken hissetmenizi sağlayacak bazı başlangıç ​​çalışma hedefleri belirlemeyi içerecektir. Ve furthur bu işin ayrılmaz bir parçası haline geliyor. Lütfen, isteğe bağlı olarak para kazanma fırsatı sunduğumuzu unutmayın, kazancınız doğrudan işinizi geliştirmek için harcadığınız çaba miktarına bağlı olacaktır.\nYine takıma hoş geldin. Başlama tarihinden önce sorularınız varsa, lütfen beni istediğiniz zaman arayın veya daha uygunsa e-posta gönderin. Gemiye gelmeni dört gözle bekliyoruz.\nBaşarının sırrı amaç için tutarlılıktır.\nTÜM EN İYİ, TOP SİZE GÖRMEK !!</p>','ioss_logo.gif','Calicut',8),(9,'Your Company Name','REC,NIT,calicut,kerala','<p> Drogi dystrybutorze,\nGratulujemy twojej decyzji ...!\nPodróż tysiąca mil musi rozpocząć się od jednego kroku.\nChciałbym powitać Cię w NAZWIE FIRMY. Cieszymy się, że zaakceptowałeś naszą ofertę biznesową i ustaliłeś datę rozpoczęcia. Ufałem, że ten list wzbudza wzajemne podekscytowanie nowymi możliwościami w COMPANY NAME. Każdy z nas odegra pewną rolę w zapewnieniu pomyślnej integracji z firmą. Twój plan będzie obejmował planowanie orientacji w firmie i ustalenie początkowych celów pracy, abyś od razu poczuł się produktywny w nowej roli. I stając się integralną częścią tego biznesu. Pamiętaj, że zapewniamy Ci możliwość zarobienia pieniędzy, która jest opcjonalna, Twoje zarobki będą zależeć bezpośrednio od nakładów włożonych w rozwój Twojej firmy.\nPonownie witamy w zespole. Jeśli masz pytania przed datą rozpoczęcia, zadzwoń do mnie w dowolnym momencie lub wyślij e-mail, jeśli jest to wygodniejsze. Z niecierpliwością czekamy na Ciebie.\nSekret sukcesu tkwi w stałości celu. Asdas\nWSZYSTKO NAJLEPSZE, DO ZOBACZENIA NA GÓRĘ !!</p>','ioss_logo.gif','Calicut',9),(10,'Your Company Name','REC ، NIT ، كاليكت ، ولاية كيرالا','<p> عزيزي الموزع ،\nمبروك على قرارك ...!\nيجب أن تبدأ رحلة الألف ميل بخطوة واحدة.\nأرغب في الترحيب بكم في اسم الشركة. نحن متحمسون لأنك قبلت عرض أعمالنا ووافقت على تاريخ البدء. لقد وثقت في أن هذه الرسالة تجدك متحمسًا بشأن فرصتك الجديدة مع اسم الشركة. سوف يلعب كل منا دورًا لضمان اندماجك الناجح في الشركة. سوف يتضمن جدول أعمالك تخطيط توجهك مع الشركة وتحديد بعض أهداف العمل الأولية بحيث تشعر على الفور بالإنتاجية في دورك الجديد. وزيادة فورثور إلى جزء لا يتجزأ من هذا العمل. يرجى ملاحظة أننا نوفر لك فرصة لكسب المال وهو أمر اختياري ، وسوف تعتمد أرباحك مباشرة في مقدار الجهود التي تبذلها لتطوير عملك.\nمرة أخرى ، مرحبا بكم في الفريق. إذا كانت لديك أسئلة قبل تاريخ البدء ، فيرجى الاتصال بي في أي وقت ، أو إرسال بريد إلكتروني إذا كان ذلك أكثر ملاءمة. ونحن نتطلع إلى أن تأتي على متن الطائرة.\nسر النجاح هو الثبات على الغرض. asdas\nكل التوفيق ، أراك في الأعلى !! </ p>','logo_login_page.png','Calicut',10),(11,'Your Company Name','REC,NIT,calicut,kerala','<p> Уважаемый дистрибьютор!\nПоздравляю с решением ...!\nПуть в тысячу миль должен начинаться с одного шага.\nЯ хотел бы приветствовать вас в НАИМЕНОВАНИИ КОМПАНИИ. Мы рады, что вы приняли наше деловое предложение и согласовали дату начала. Я полагал, что это письмо находит вас взаимно взволнованными по поводу вашей новой возможности с ИМЯ КОМПАНИИ. Каждый из нас сыграет свою роль в обеспечении вашей успешной интеграции в компанию. Ваша повестка дня будет включать планирование вашей ориентации в компании и установление некоторых начальных рабочих целей, чтобы вы сразу почувствовали себя продуктивными в своей новой роли. И дальнейшее превращение в неотъемлемую часть этого бизнеса. Обратите внимание, что мы предоставляем вам возможность зарабатывать деньги, что является необязательным, ваш заработок будет напрямую зависеть от того, сколько усилий вы приложите для развития своего бизнеса.\nЕще раз добро пожаловать в команду. Если у вас есть вопросы до даты начала, пожалуйста, позвоните мне в любое время или отправьте электронное письмо, если это более удобно. Мы с нетерпением ждем, чтобы вы пришли на борт.\nСекрет успеха в постоянстве цели. Asdas\nВСЕХ ЛУЧШИХ, Увидимся на вершине !! </ p>','logo_login_page.png','Calicut',11);
/*!40000 ALTER TABLE `39_letter_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_level_commision`
--

DROP TABLE IF EXISTS `39_level_commision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_level_commision` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `level_no` int(12) NOT NULL DEFAULT '0',
  `level_percentage` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `level_no` (`level_no`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_level_commision`
--

LOCK TABLES `39_level_commision` WRITE;
/*!40000 ALTER TABLE `39_level_commision` DISABLE KEYS */;
INSERT INTO `39_level_commision` VALUES (1,1,3),(3,2,2),(5,3,1);
/*!40000 ALTER TABLE `39_level_commision` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_level_commission_reg_pck`
--

DROP TABLE IF EXISTS `39_level_commission_reg_pck`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_level_commission_reg_pck` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` int(12) NOT NULL,
  `pck_id` varchar(10) CHARACTER SET utf8 NOT NULL,
  `cmsn_reg_pck` double NOT NULL DEFAULT '0',
  `cmsn_member_pck` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_level_commission_reg_pck`
--

LOCK TABLES `39_level_commission_reg_pck` WRITE;
/*!40000 ALTER TABLE `39_level_commission_reg_pck` DISABLE KEYS */;
INSERT INTO `39_level_commission_reg_pck` VALUES (1,1,'pck1',3,3),(3,1,'pck2',3,3),(5,1,'pck3',3,3),(7,2,'pck1',2,2),(9,2,'pck2',2,2),(11,2,'pck3',2,2),(13,3,'pck1',1,1),(15,3,'pck2',1,1),(17,3,'pck3',1,1),(18,1,'pck1',3,3),(19,1,'pck2',3,3),(20,1,'pck3',3,3),(21,2,'pck1',2,2),(22,2,'pck2',2,2),(23,2,'pck3',2,2),(24,3,'pck1',1,1),(25,3,'pck2',1,1),(26,3,'pck3',1,1);
/*!40000 ALTER TABLE `39_level_commission_reg_pck` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_mail_from_lead`
--

DROP TABLE IF EXISTS `39_mail_from_lead`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_mail_from_lead` (
  `mail_id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_from` int(11) unsigned NOT NULL,
  `mail_to` int(11) unsigned NOT NULL,
  `mail_sub` varchar(50) CHARACTER SET utf8 NOT NULL,
  `message` longtext,
  `mail_date` datetime DEFAULT NULL,
  `status` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `read_msg` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `type` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT 'team',
  PRIMARY KEY (`mail_id`),
  KEY `mail_from` (`mail_from`),
  KEY `mail_to` (`mail_to`),
  CONSTRAINT `39_mail_from_lead_ibfk_15` FOREIGN KEY (`mail_from`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_mail_from_lead_ibfk_16` FOREIGN KEY (`mail_to`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_mail_from_lead`
--

LOCK TABLES `39_mail_from_lead` WRITE;
/*!40000 ALTER TABLE `39_mail_from_lead` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_mail_from_lead` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_mail_from_lead_cumulative`
--

DROP TABLE IF EXISTS `39_mail_from_lead_cumulative`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_mail_from_lead_cumulative` (
  `mail_id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_from` int(11) unsigned NOT NULL,
  `mail_to` varchar(20) CHARACTER SET utf8 NOT NULL,
  `mail_sub` varchar(50) CHARACTER SET utf8 NOT NULL,
  `message` longtext,
  `mail_date` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `status` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `read_msg` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `type` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT 'team',
  PRIMARY KEY (`mail_id`),
  KEY `mail_from` (`mail_from`),
  CONSTRAINT `39_mail_from_lead_cumulative_ibfk_1` FOREIGN KEY (`mail_from`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_mail_from_lead_cumulative`
--

LOCK TABLES `39_mail_from_lead_cumulative` WRITE;
/*!40000 ALTER TABLE `39_mail_from_lead_cumulative` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_mail_from_lead_cumulative` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_mail_settings`
--

DROP TABLE IF EXISTS `39_mail_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_mail_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `from_email` varchar(50) CHARACTER SET utf8 NOT NULL,
  `smtp_host` varchar(50) CHARACTER SET utf8 NOT NULL,
  `smtp_username` varchar(50) CHARACTER SET utf8 NOT NULL,
  `smtp_password` varchar(50) CHARACTER SET utf8 NOT NULL,
  `smtp_port` varchar(50) CHARACTER SET utf8 NOT NULL,
  `smtp_timeout` varchar(50) CHARACTER SET utf8 NOT NULL,
  `reg_mail_status` varchar(10) CHARACTER SET utf8 NOT NULL,
  `reg_mail_content` text CHARACTER SET utf8 NOT NULL,
  `reg_mail_type` varchar(50) CHARACTER SET utf8 NOT NULL,
  `smtp_authentication` varchar(5) CHARACTER SET utf8 NOT NULL DEFAULT '1',
  `smtp_protocol` varchar(5) CHARACTER SET utf8 NOT NULL DEFAULT 'none',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_mail_settings`
--

LOCK TABLES `39_mail_settings` WRITE;
/*!40000 ALTER TABLE `39_mail_settings` DISABLE KEYS */;
INSERT INTO `39_mail_settings` VALUES (1,'Infinitemlmsoftware.com','info@infinitemlmsoftware.com','mail.ioss.in','iossmlm@ioss.in','ceadecs001','25','360','yes','<p>yuytu</p>','normal','1','tls');
/*!40000 ALTER TABLE `39_mail_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_mailgun_configuration`
--

DROP TABLE IF EXISTS `39_mailgun_configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_mailgun_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_name` varchar(100) NOT NULL,
  `from_email` varchar(100) NOT NULL,
  `reply_to` varchar(100) NOT NULL,
  `domain` varchar(100) NOT NULL,
  `api_key` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_mailgun_configuration`
--

LOCK TABLES `39_mailgun_configuration` WRITE;
/*!40000 ALTER TABLE `39_mailgun_configuration` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_mailgun_configuration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_mailtoadmin`
--

DROP TABLE IF EXISTS `39_mailtoadmin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_mailtoadmin` (
  `mailadid` int(11) NOT NULL AUTO_INCREMENT,
  `mailaduser` int(11) unsigned NOT NULL,
  `mailadsubject` longtext,
  `mailadiddate` datetime DEFAULT '0001-01-01 00:00:00',
  `status` varchar(20) CHARACTER SET utf8 NOT NULL,
  `mailadidmsg` longtext CHARACTER SET utf8 NOT NULL,
  `read_msg` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `deleted_by` varchar(50) NOT NULL,
  `thread` int(11) NOT NULL,
  PRIMARY KEY (`mailadid`),
  KEY `mailadiddate` (`mailadiddate`),
  KEY `thread` (`thread`),
  KEY `status` (`status`),
  KEY `mailaduser` (`mailaduser`),
  CONSTRAINT `39_mailtoadmin_ibfk_1` FOREIGN KEY (`mailaduser`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_mailtoadmin`
--

LOCK TABLES `39_mailtoadmin` WRITE;
/*!40000 ALTER TABLE `39_mailtoadmin` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_mailtoadmin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_mailtouser`
--

DROP TABLE IF EXISTS `39_mailtouser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_mailtouser` (
  `mailtousid` int(11) NOT NULL AUTO_INCREMENT,
  `mailtoususer` int(11) unsigned NOT NULL,
  `mailfromuser` int(11) unsigned NOT NULL,
  `mailtoussub` text CHARACTER SET utf8 NOT NULL,
  `mailtousmsg` longtext CHARACTER SET utf8 NOT NULL,
  `mailtousdate` datetime DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `read_msg` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `deleted_by` varchar(50) NOT NULL,
  `thread` int(11) NOT NULL,
  PRIMARY KEY (`mailtousid`),
  KEY `mailtousdate` (`mailtousdate`),
  KEY `thread` (`thread`),
  KEY `status` (`status`),
  KEY `mailtoususer` (`mailtoususer`),
  KEY `mailfromuser` (`mailfromuser`),
  CONSTRAINT `39_mailtouser_ibfk_15` FOREIGN KEY (`mailtoususer`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_mailtouser_ibfk_16` FOREIGN KEY (`mailfromuser`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_mailtouser`
--

LOCK TABLES `39_mailtouser` WRITE;
/*!40000 ALTER TABLE `39_mailtouser` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_mailtouser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_mailtouser_cumulativ`
--

DROP TABLE IF EXISTS `39_mailtouser_cumulativ`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_mailtouser_cumulativ` (
  `mailtousid` int(11) NOT NULL AUTO_INCREMENT,
  `mailtoususer` varchar(250) NOT NULL,
  `mailtoussub` text CHARACTER SET utf8 NOT NULL,
  `mailtousmsg` longtext CHARACTER SET utf8 NOT NULL,
  `mailtousdate` datetime DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `read_msg` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `type` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT 'team',
  PRIMARY KEY (`mailtousid`),
  KEY `type` (`type`),
  KEY `mailtousdate` (`mailtousdate`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_mailtouser_cumulativ`
--

LOCK TABLES `39_mailtouser_cumulativ` WRITE;
/*!40000 ALTER TABLE `39_mailtouser_cumulativ` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_mailtouser_cumulativ` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_manual_pv_update_history`
--

DROP TABLE IF EXISTS `39_manual_pv_update_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_manual_pv_update_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `pv_added` int(11) NOT NULL,
  `new_pv` int(11) NOT NULL,
  `old_pv` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_manual_pv_update_history`
--

LOCK TABLES `39_manual_pv_update_history` WRITE;
/*!40000 ALTER TABLE `39_manual_pv_update_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_manual_pv_update_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_mass_payout_history`
--

DROP TABLE IF EXISTS `39_mass_payout_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_mass_payout_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mass_payout_details` text NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_mass_payout_history`
--

LOCK TABLES `39_mass_payout_history` WRITE;
/*!40000 ALTER TABLE `39_mass_payout_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_mass_payout_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_matching_bonus`
--

DROP TABLE IF EXISTS `39_matching_bonus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_matching_bonus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level_no` int(11) NOT NULL,
  `bonus_percent` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_matching_bonus`
--

LOCK TABLES `39_matching_bonus` WRITE;
/*!40000 ALTER TABLE `39_matching_bonus` DISABLE KEYS */;
INSERT INTO `39_matching_bonus` VALUES (1,1,3),(2,2,2),(3,3,1);
/*!40000 ALTER TABLE `39_matching_bonus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_matching_commissions`
--

DROP TABLE IF EXISTS `39_matching_commissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_matching_commissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` int(12) NOT NULL,
  `pck_id` varchar(10) CHARACTER SET utf8 NOT NULL,
  `cmsn_member_pck` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_matching_commissions`
--

LOCK TABLES `39_matching_commissions` WRITE;
/*!40000 ALTER TABLE `39_matching_commissions` DISABLE KEYS */;
INSERT INTO `39_matching_commissions` VALUES (1,1,'pck1',3),(3,1,'pck2',3),(5,1,'pck3',3),(7,2,'pck1',2),(9,2,'pck2',2),(11,2,'pck3',2),(13,3,'pck1',1),(15,3,'pck2',1),(17,3,'pck3',1),(18,1,'pck1',3),(19,1,'pck2',3),(20,1,'pck3',3),(21,2,'pck1',2),(22,2,'pck2',2),(23,2,'pck3',2),(24,3,'pck1',1),(25,3,'pck2',1),(26,3,'pck3',1);
/*!40000 ALTER TABLE `39_matching_commissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_matching_level_commision`
--

DROP TABLE IF EXISTS `39_matching_level_commision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_matching_level_commision` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `level_no` int(12) NOT NULL DEFAULT '0',
  `level_percentage` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_matching_level_commision`
--

LOCK TABLES `39_matching_level_commision` WRITE;
/*!40000 ALTER TABLE `39_matching_level_commision` DISABLE KEYS */;
INSERT INTO `39_matching_level_commision` VALUES (1,1,3),(3,2,2),(5,3,1),(6,1,3),(7,2,2),(8,3,1);
/*!40000 ALTER TABLE `39_matching_level_commision` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_migrations`
--

DROP TABLE IF EXISTS `39_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_migrations` (
  `version` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_migrations`
--

LOCK TABLES `39_migrations` WRITE;
/*!40000 ALTER TABLE `39_migrations` DISABLE KEYS */;
INSERT INTO `39_migrations` VALUES (20210226172930);
/*!40000 ALTER TABLE `39_migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_mlm_curl_history`
--

DROP TABLE IF EXISTS `39_mlm_curl_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_mlm_curl_history` (
  `curl_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) NOT NULL DEFAULT '0',
  `curl_url` varchar(150) CHARACTER SET utf8 NOT NULL DEFAULT 'NA',
  `curl_type` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'register',
  `curl_data` text CHARACTER SET utf8 NOT NULL,
  `curl_result` text CHARACTER SET utf8 NOT NULL,
  `curl_date` datetime DEFAULT NULL,
  PRIMARY KEY (`curl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_mlm_curl_history`
--

LOCK TABLES `39_mlm_curl_history` WRITE;
/*!40000 ALTER TABLE `39_mlm_curl_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_mlm_curl_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_module_status`
--

DROP TABLE IF EXISTS `39_module_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_module_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mlm_plan` varchar(50) CHARACTER SET utf8 NOT NULL,
  `first_pair` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '1:1',
  `pin_status` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `product_status` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `sms_status` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `mailbox_status` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `referal_status` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `ewallet_status` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `employee_status` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `payout_release_status` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'ewallet_request',
  `upload_status` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `sponsor_tree_status` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `rank_status` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `rank_status_demo` varchar(5) NOT NULL DEFAULT 'no',
  `lang_status` varchar(11) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `help_status` varchar(11) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `shuffle_status` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `statcounter_status` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `footer_demo_status` varchar(11) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `captcha_status` varchar(11) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `sponsor_commission_status` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `multy_currency_status` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `lead_capture_status` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `ticket_system_status` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `currency_conversion_status` varchar(11) CHARACTER SET utf8 NOT NULL DEFAULT 'manual',
  `opencart_status` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `live_chat_status` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `opencart_status_demo` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `multy_currency_status_demo` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `lead_capture_status_demo` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `ticket_system_status_demo` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `autoresponder_status` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `autoresponder_status_demo` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `replicated_site_status` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `replicated_site_status_demo` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `table_status` varchar(5) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `lcp_type` varchar(10) NOT NULL DEFAULT 'lcp',
  `payment_gateway_status` varchar(10) NOT NULL DEFAULT 'no',
  `bitcoin_status` varchar(10) NOT NULL DEFAULT 'no',
  `repurchase_status` varchar(10) NOT NULL DEFAULT 'no',
  `repurchase_status_demo` varchar(10) NOT NULL DEFAULT 'no',
  `google_auth_status` varchar(5) NOT NULL DEFAULT 'no',
  `package_upgrade` varchar(5) NOT NULL DEFAULT 'no',
  `package_upgrade_demo` varchar(5) NOT NULL DEFAULT 'no',
  `maintenance_status_demo` varchar(5) NOT NULL DEFAULT 'no',
  `maintenance_status` varchar(5) NOT NULL DEFAULT 'no',
  `lang_status_demo` varchar(5) NOT NULL DEFAULT 'no',
  `employee_status_demo` varchar(5) NOT NULL DEFAULT 'no',
  `sms_status_demo` varchar(5) NOT NULL DEFAULT 'no',
  `pin_status_demo` varchar(5) NOT NULL DEFAULT 'no',
  `roi_status` varchar(11) DEFAULT 'no',
  `basic_demo_status` varchar(11) NOT NULL DEFAULT 'no',
  `xup_status` varchar(11) NOT NULL DEFAULT 'no',
  `hyip_status` varchar(11) NOT NULL DEFAULT 'no',
  `group_pv` varchar(50) NOT NULL DEFAULT 'no',
  `personal_pv` varchar(50) NOT NULL DEFAULT 'no',
  `kyc_status` varchar(50) NOT NULL DEFAULT 'no',
  `signup_config` varchar(5) NOT NULL DEFAULT 'yes',
  `mail_gun_status` varchar(3) NOT NULL DEFAULT 'no',
  `auto_ship_status` varchar(20) NOT NULL DEFAULT 'no',
  `downline_count_rank` varchar(50) NOT NULL DEFAULT 'no',
  `downline_purchase_rank` varchar(50) NOT NULL DEFAULT 'no',
  `otp_modal` varchar(3) NOT NULL DEFAULT 'no',
  `gdpr` varchar(5) DEFAULT 'no',
  `purchase_wallet` varchar(11) NOT NULL DEFAULT 'no',
  `crowd_fund` varchar(3) NOT NULL DEFAULT 'no',
  `compression_status` varchar(50) NOT NULL DEFAULT 'no',
  `promotion_status` varchar(10) NOT NULL DEFAULT 'no',
  `promotion_status_demo` varchar(10) NOT NULL DEFAULT 'no',
  `subscription_status` varchar(30) NOT NULL DEFAULT 'no',
  `subscription_status_demo` varchar(3) NOT NULL DEFAULT 'no',
  `tree_updation` varchar(3) NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_module_status`
--

LOCK TABLES `39_module_status` WRITE;
/*!40000 ALTER TABLE `39_module_status` DISABLE KEYS */;
INSERT INTO `39_module_status` VALUES (1,'Binary','1:1','yes','yes','no','yes','yes','yes','no','ewallet_request','yes','yes','yes','yes','no','no','no','yes','yes','yes','yes','no','yes','no','no','no','no','no','no','yes','no','yes','yes','yes','yes','no','lcp','no','no','no','no','no','no','no','yes','yes','no','no','no','yes','no','no','no','no','no','no','no','yes','no','no','no','no','no','no','no','no','no','no','yes','yes','yes','no');
/*!40000 ALTER TABLE `39_module_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_news`
--

DROP TABLE IF EXISTS `39_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_news` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `news_title` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `news_desc` longtext,
  `news_date` datetime DEFAULT NULL,
  `news_image` varchar(255) DEFAULT 'default.png',
  PRIMARY KEY (`news_id`),
  KEY `news_date` (`news_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_news`
--

LOCK TABLES `39_news` WRITE;
/*!40000 ALTER TABLE `39_news` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_other_expenses`
--

DROP TABLE IF EXISTS `39_other_expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_other_expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` double DEFAULT '0',
  `description` text,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_other_expenses`
--

LOCK TABLES `39_other_expenses` WRITE;
/*!40000 ALTER TABLE `39_other_expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_other_expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_package`
--

DROP TABLE IF EXISTS `39_package`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_package` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `type_of_package` varchar(50) NOT NULL DEFAULT 'registration',
  `active` varchar(10) CHARACTER SET utf8 NOT NULL,
  `date_of_insertion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `prod_id` varchar(10) CHARACTER SET utf8 NOT NULL,
  `product_value` decimal(16,8) NOT NULL DEFAULT '0.00000000',
  `bv_value` double NOT NULL DEFAULT '0',
  `pair_value` double NOT NULL DEFAULT '0',
  `product_qty` int(11) NOT NULL DEFAULT '0',
  `referral_commission` double NOT NULL DEFAULT '0',
  `pair_price` double NOT NULL DEFAULT '0',
  `prod_img` varchar(100) NOT NULL DEFAULT 'no',
  `days` int(11) DEFAULT '0',
  `roi` decimal(11,2) DEFAULT '0.00',
  `joinee_commission` double NOT NULL DEFAULT '0',
  `description` text,
  `category_id` int(11) DEFAULT NULL,
  `subscription_period` int(11) DEFAULT '1',
  `tree_icon` text NOT NULL,
  PRIMARY KEY (`product_id`),
  KEY `prod_id` (`prod_id`),
  KEY `type_of_package` (`type_of_package`),
  KEY `active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_package`
--

LOCK TABLES `39_package` WRITE;
/*!40000 ALTER TABLE `39_package` DISABLE KEYS */;
INSERT INTO `39_package` VALUES (1,'Reg Pack1','registration','yes','2020-07-30 05:10:02','pck1',100.00000000,50,50,0,5,5,'no',5,10.00,0,NULL,NULL,1,'package1.png'),(2,'Reg Pack2','registration','yes','2020-07-30 05:10:02','pck2',200.00000000,100,100,0,10,10,'no',6,12.00,0,NULL,NULL,1,'package2.png'),(3,'Reg Pack3','registration','yes','2020-07-30 05:10:02','pck3',500.00000000,250,250,0,15,15,'no',8,15.00,0,NULL,NULL,1,'package3.png'),(4,'Repurchase Pack1','repurchase','yes','2019-02-27 23:38:20','pck4',100.00000000,50,50,0,0,3,'no',3,10.00,0,NULL,1,1,''),(5,'Repurchase Pack2','repurchase','yes','2019-02-27 23:38:24','pck5',200.00000000,100,100,0,0,5,'no',3,12.00,0,NULL,2,1,''),(6,'Repurchase Pack3','repurchase','yes','2019-02-27 23:38:28','pck6',500.00000000,250,250,0,0,10,'no',3,15.00,0,NULL,3,1,'');
/*!40000 ALTER TABLE `39_package` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_package_validity_extend_history`
--

DROP TABLE IF EXISTS `39_package_validity_extend_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_package_validity_extend_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `invoice_id` varchar(20) NOT NULL,
  `package_id` varchar(50) NOT NULL,
  `payment_type_used` varchar(50) NOT NULL,
  `date_submitted` datetime NOT NULL,
  `total_amount` double NOT NULL DEFAULT '0',
  `product_pv` double NOT NULL DEFAULT '0',
  `pay_type` varchar(20) DEFAULT NULL,
  `renewal_details` text NOT NULL,
  `renewal_status` text NOT NULL,
  `receipt` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `39_package_validity_extend_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_package_validity_extend_history`
--

LOCK TABLES `39_package_validity_extend_history` WRITE;
/*!40000 ALTER TABLE `39_package_validity_extend_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_package_validity_extend_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_password_policy`
--

DROP TABLE IF EXISTS `39_password_policy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_password_policy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enable_policy` tinyint(4) NOT NULL,
  `lowercase` tinyint(4) NOT NULL COMMENT 'lowercase status',
  `uppercase` tinyint(4) NOT NULL COMMENT 'upper case status',
  `number` tinyint(4) NOT NULL COMMENT 'number status',
  `sp_char` tinyint(4) NOT NULL COMMENT 'sp. character status',
  `min_length` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_password_policy`
--

LOCK TABLES `39_password_policy` WRITE;
/*!40000 ALTER TABLE `39_password_policy` DISABLE KEYS */;
INSERT INTO `39_password_policy` VALUES (1,0,1,1,1,1,8);
/*!40000 ALTER TABLE `39_password_policy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_password_reset_table`
--

DROP TABLE IF EXISTS `39_password_reset_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_password_reset_table` (
  `password_reset_id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` bigint(20) NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL,
  `reset_status` varchar(30) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  PRIMARY KEY (`password_reset_id`),
  KEY `keyword` (`keyword`),
  KEY `reset_status` (`reset_status`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `39_password_reset_table_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_password_reset_table`
--

LOCK TABLES `39_password_reset_table` WRITE;
/*!40000 ALTER TABLE `39_password_reset_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_password_reset_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_payeer_settings`
--

DROP TABLE IF EXISTS `39_payeer_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_payeer_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merchant_id` varchar(250) NOT NULL,
  `merchant_key` varchar(250) NOT NULL,
  `encryption_key` varchar(250) NOT NULL,
  `account` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_payeer_settings`
--

LOCK TABLES `39_payeer_settings` WRITE;
/*!40000 ALTER TABLE `39_payeer_settings` DISABLE KEYS */;
INSERT INTO `39_payeer_settings` VALUES (1,'7124685','15289685','152789','mlmm');
/*!40000 ALTER TABLE `39_payeer_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_payment_gateway_config`
--

DROP TABLE IF EXISTS `39_payment_gateway_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_payment_gateway_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gateway_name` varchar(50) NOT NULL,
  `status` varchar(10) NOT NULL,
  `logo` varchar(100) NOT NULL,
  `sort_order` int(10) NOT NULL,
  `mode` varchar(50) NOT NULL,
  `payout_status` varchar(10) NOT NULL,
  `payout_sort_order` int(10) NOT NULL,
  `registration` int(1) NOT NULL,
  `repurchase` int(1) NOT NULL,
  `membership_renewal` int(1) NOT NULL,
  `upgradation` int(1) NOT NULL,
  `admin_only` int(1) NOT NULL,
  `gate_way` int(1) NOT NULL,
  `payment_only` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_payment_gateway_config`
--

LOCK TABLES `39_payment_gateway_config` WRITE;
/*!40000 ALTER TABLE `39_payment_gateway_config` DISABLE KEYS */;
INSERT INTO `39_payment_gateway_config` VALUES (1,'Paypal','no','paypal-logo.png',1,'test','no',1,1,1,1,1,0,1,0),(2,'Authorize.Net','no','Authorizenet_logo.png',2,'test','no',0,1,1,1,1,0,1,1),(3,'Blockchain','no','blockchain.png',3,'test','no',2,1,1,1,1,0,1,0),(4,'Bitgo','no','bitgo.png',4,'test','no',3,1,1,1,1,0,1,0),(5,'Payeer','no','payeer_logo.png',5,'test','no',0,1,1,1,1,0,1,1),(6,'Sofort','no','sofort.png',6,'test','no',0,1,1,1,1,0,1,1),(7,'SquareUp','no','squareup.png',7,'test','no',0,1,1,1,1,0,1,1),(8,'E-pin','no','',8,'test','no',0,1,1,1,1,0,0,1),(9,'E-wallet','no','',9,'test','no',0,1,1,1,1,0,0,1),(10,'Free Joining','no','',11,'test','no',0,1,1,1,1,0,0,1),(11,'Bank Transfer','no','',10,'test','no',4,1,1,1,1,0,0,0);
/*!40000 ALTER TABLE `39_payment_gateway_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_payment_methods`
--

DROP TABLE IF EXISTS `39_payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_type` varchar(100) CHARACTER SET utf8 NOT NULL,
  `status` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_type` (`payment_type`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_payment_methods`
--

LOCK TABLES `39_payment_methods` WRITE;
/*!40000 ALTER TABLE `39_payment_methods` DISABLE KEYS */;
INSERT INTO `39_payment_methods` VALUES (1,'Payment Gateway','yes'),(2,'E-pin','yes'),(3,'E-wallet','yes'),(4,'Free Joining','yes'),(5,'Bank Transfer','yes');
/*!40000 ALTER TABLE `39_payment_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_payment_receipt`
--

DROP TABLE IF EXISTS `39_payment_receipt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_payment_receipt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL,
  `uploaded_date` varchar(50) NOT NULL,
  `reciept_name` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'register',
  `order_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_name` (`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_payment_receipt`
--

LOCK TABLES `39_payment_receipt` WRITE;
/*!40000 ALTER TABLE `39_payment_receipt` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_payment_receipt` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_payment_registration_details`
--

DROP TABLE IF EXISTS `39_payment_registration_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_payment_registration_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) CHARACTER SET utf8 NOT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `acceptance` varchar(20) CHARACTER SET utf8 NOT NULL,
  `payer_id` varchar(200) CHARACTER SET utf8 NOT NULL,
  `order_id` varchar(100) CHARACTER SET utf8 NOT NULL,
  `amount` double NOT NULL DEFAULT '0',
  `currency` varchar(20) CHARACTER SET utf8 NOT NULL,
  `status` varchar(20) CHARACTER SET utf8 NOT NULL,
  `card_number` varchar(50) CHARACTER SET utf8 NOT NULL,
  `ED` int(20) NOT NULL DEFAULT '0',
  `card_holder_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `date_of_submission` date NOT NULL DEFAULT '0001-01-01',
  `pay_id` int(200) NOT NULL DEFAULT '0',
  `error_status` int(11) NOT NULL DEFAULT '0',
  `brand` varchar(20) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `39_payment_registration_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_payment_registration_details`
--

LOCK TABLES `39_payment_registration_details` WRITE;
/*!40000 ALTER TABLE `39_payment_registration_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_payment_registration_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_payout_release_requests`
--

DROP TABLE IF EXISTS `39_payout_release_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_payout_release_requests` (
  `req_id` int(11) NOT NULL AUTO_INCREMENT,
  `requested_user_id` int(11) unsigned NOT NULL,
  `requested_amount` double NOT NULL DEFAULT '0',
  `requested_amount_balance` double NOT NULL DEFAULT '0',
  `payout_fee` double NOT NULL DEFAULT '0' COMMENT 'fee amount, after calculations',
  `requested_date` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `updated_date` datetime DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8 NOT NULL,
  `read_status` tinyint(4) NOT NULL DEFAULT '2',
  `payment_method` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`req_id`),
  KEY `status` (`status`),
  KEY `requested_date` (`requested_date`),
  KEY `requested_user_id` (`requested_user_id`),
  CONSTRAINT `39_payout_release_requests_ibfk_1` FOREIGN KEY (`requested_user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_payout_release_requests`
--

LOCK TABLES `39_payout_release_requests` WRITE;
/*!40000 ALTER TABLE `39_payout_release_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_payout_release_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_paypal_config`
--

DROP TABLE IF EXISTS `39_paypal_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_paypal_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `api_username` varchar(100) CHARACTER SET utf8 NOT NULL,
  `api_password` varchar(100) CHARACTER SET utf8 NOT NULL,
  `api_signature` longtext,
  `mode` varchar(100) CHARACTER SET utf8 NOT NULL,
  `currency` varchar(100) CHARACTER SET utf8 NOT NULL,
  `return_url` longtext,
  `cancel_url` longtext,
  `repurchase_return_url` varchar(50) NOT NULL DEFAULT 'repurchase/payment_success',
  `repurchase_cancel_url` varchar(50) NOT NULL DEFAULT 'repurchase/repurchase_product',
  `package_validity_return_url` varchar(200) NOT NULL,
  `package_validity_cancel_url` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_paypal_config`
--

LOCK TABLES `39_paypal_config` WRITE;
/*!40000 ALTER TABLE `39_paypal_config` DISABLE KEYS */;
INSERT INTO `39_paypal_config` VALUES (1,'business_api1.ioss.in','1400571384','ALnz-uC-Rm29guXy62muZVvYZTIVAZt6p6YLdh5JenpLbnHJW02gWPlt','test','USD','register/payment_success','register/user_register','repurchase/payment_success','repurchase/repurchase_product','member/payment_success','member/package_validity');
/*!40000 ALTER TABLE `39_paypal_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_pending_registration`
--

DROP TABLE IF EXISTS `39_pending_registration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_pending_registration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(20) NOT NULL,
  `updated_id` int(11) unsigned DEFAULT NULL,
  `payment_method` varchar(20) NOT NULL,
  `data` longtext NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'pending',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL,
  `email_verification_status` varchar(60) NOT NULL DEFAULT 'no',
  `default_currency` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `user_name` (`user_name`),
  KEY `updated_id` (`updated_id`),
  CONSTRAINT `39_pending_registration_ibfk_1` FOREIGN KEY (`updated_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_pending_registration`
--

LOCK TABLES `39_pending_registration` WRITE;
/*!40000 ALTER TABLE `39_pending_registration` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_pending_registration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_pending_signup_config`
--

DROP TABLE IF EXISTS `39_pending_signup_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_pending_signup_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_method` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_pending_signup_config`
--

LOCK TABLES `39_pending_signup_config` WRITE;
/*!40000 ALTER TABLE `39_pending_signup_config` DISABLE KEYS */;
INSERT INTO `39_pending_signup_config` VALUES (1,'E-wallet',0),(2,'E-pin',0),(3,'Free Joining',0),(4,'Paypal',0),(5,'Creditcard',0),(6,'EPDQ',0),(7,'Authorize.Net',0),(8,'Bitcoin',0),(9,'Bank Transfer',1),(10,'Blockchain',0),(11,'BitGo',0),(12,'Payeer',0),(13,'Sofort',0),(14,'SquareUp',0);
/*!40000 ALTER TABLE `39_pending_signup_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_performance_bonus`
--

DROP TABLE IF EXISTS `39_performance_bonus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_performance_bonus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bonus_name` varchar(50) NOT NULL,
  `personal_pv` double NOT NULL,
  `group_pv` double NOT NULL,
  `bonus_percent` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_performance_bonus`
--

LOCK TABLES `39_performance_bonus` WRITE;
/*!40000 ALTER TABLE `39_performance_bonus` DISABLE KEYS */;
INSERT INTO `39_performance_bonus` VALUES (1,'vacation_fund',50,100,2),(2,'education_fund',100,200,4),(3,'car_fund',150,300,6),(4,'house_fund',200,400,8);
/*!40000 ALTER TABLE `39_performance_bonus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_pin_amount_details`
--

DROP TABLE IF EXISTS `39_pin_amount_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_pin_amount_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `amount` (`amount`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_pin_amount_details`
--

LOCK TABLES `39_pin_amount_details` WRITE;
/*!40000 ALTER TABLE `39_pin_amount_details` DISABLE KEYS */;
INSERT INTO `39_pin_amount_details` VALUES (1,1),(2,5),(3,10),(4,20),(5,50),(6,100),(7,500),(8,1000);
/*!40000 ALTER TABLE `39_pin_amount_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_pin_config`
--

DROP TABLE IF EXISTS `39_pin_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_pin_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pin_amount` double DEFAULT NULL,
  `pin_length` int(11) DEFAULT NULL,
  `pin_type` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `pin_character_set` varchar(150) CHARACTER SET utf8 NOT NULL,
  `pin_maxcount` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_pin_config`
--

LOCK TABLES `39_pin_config` WRITE;
/*!40000 ALTER TABLE `39_pin_config` DISABLE KEYS */;
INSERT INTO `39_pin_config` VALUES (1,49,10,'','alphanumeric',100);
/*!40000 ALTER TABLE `39_pin_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_pin_numbers`
--

DROP TABLE IF EXISTS `39_pin_numbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_pin_numbers` (
  `pin_id` int(11) NOT NULL AUTO_INCREMENT,
  `pin_numbers` varchar(15) CHARACTER SET utf8 NOT NULL,
  `pin_alloc_date` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `status` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'active',
  `used_user` int(11) unsigned DEFAULT NULL,
  `generated_user_id` int(11) unsigned DEFAULT NULL,
  `allocated_user_id` int(11) unsigned DEFAULT NULL,
  `purchase_status` varchar(5) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `pin_uploded_date` datetime DEFAULT NULL,
  `pin_expiry_date` date DEFAULT NULL,
  `pin_amount` double NOT NULL DEFAULT '0',
  `pin_balance_amount` double NOT NULL DEFAULT '0',
  `transaction_id` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`pin_id`),
  KEY `pin_numbers` (`pin_numbers`),
  KEY `pin_expiry_date` (`pin_expiry_date`),
  KEY `allocated_user_id` (`allocated_user_id`),
  KEY `generated_user_id` (`generated_user_id`),
  CONSTRAINT `39_pin_numbers_ibfk_15` FOREIGN KEY (`allocated_user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_pin_numbers_ibfk_16` FOREIGN KEY (`generated_user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_pin_numbers`
--

LOCK TABLES `39_pin_numbers` WRITE;
/*!40000 ALTER TABLE `39_pin_numbers` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_pin_numbers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_pin_purchases`
--

DROP TABLE IF EXISTS `39_pin_purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_pin_purchases` (
  `pin_id` int(11) NOT NULL AUTO_INCREMENT,
  `pin_numbers` varchar(15) CHARACTER SET utf8 NOT NULL,
  `pin_alloc_date` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `status` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'active',
  `used_user` int(11) unsigned DEFAULT NULL,
  `generated_user_id` int(11) unsigned DEFAULT NULL,
  `allocated_user_id` int(11) unsigned DEFAULT NULL,
  `purchase_status` varchar(5) CHARACTER SET utf8 NOT NULL DEFAULT 'no',
  `pin_uploded_date` datetime DEFAULT NULL,
  `pin_expiry_date` date DEFAULT NULL,
  `pin_amount` double NOT NULL DEFAULT '0',
  `pin_balance_amount` double NOT NULL DEFAULT '0',
  `transaction_id` varchar(100) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pin_id`),
  KEY `used_user` (`used_user`),
  KEY `allocated_user_id` (`allocated_user_id`),
  KEY `generated_user_id` (`generated_user_id`),
  CONSTRAINT `39_pin_purchases_ibfk_22` FOREIGN KEY (`used_user`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_pin_purchases_ibfk_23` FOREIGN KEY (`allocated_user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_pin_purchases_ibfk_24` FOREIGN KEY (`generated_user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_pin_purchases`
--

LOCK TABLES `39_pin_purchases` WRITE;
/*!40000 ALTER TABLE `39_pin_purchases` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_pin_purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_pin_request`
--

DROP TABLE IF EXISTS `39_pin_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_pin_request` (
  `req_id` int(11) NOT NULL AUTO_INCREMENT,
  `req_user_id` int(11) unsigned NOT NULL,
  `req_pin_count` int(11) NOT NULL DEFAULT '0',
  `req_rec_pin_count` int(11) NOT NULL DEFAULT '0',
  `req_date` date NOT NULL DEFAULT '0001-01-01',
  `status` varchar(50) CHARACTER SET utf8 NOT NULL,
  `remark` varchar(100) NOT NULL DEFAULT 'NA',
  `pin_expiry_date` date NOT NULL DEFAULT '0001-01-01',
  `pin_amount` double DEFAULT '0',
  `read_status` tinyint(4) NOT NULL DEFAULT '2',
  PRIMARY KEY (`req_id`),
  KEY `status` (`status`),
  KEY `req_user_id` (`req_user_id`),
  CONSTRAINT `39_pin_request_ibfk_1` FOREIGN KEY (`req_user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_pin_request`
--

LOCK TABLES `39_pin_request` WRITE;
/*!40000 ALTER TABLE `39_pin_request` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_pin_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_pin_used`
--

DROP TABLE IF EXISTS `39_pin_used`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_pin_used` (
  `pin_id` int(11) NOT NULL AUTO_INCREMENT,
  `pin_number` varchar(15) CHARACTER SET utf8 NOT NULL,
  `used_user` int(11) unsigned DEFAULT NULL,
  `pin_alloc_date` date DEFAULT '0001-01-01',
  `status` varchar(5) CHARACTER SET utf8 NOT NULL,
  `pin_amount` double NOT NULL DEFAULT '0',
  `pin_balance_amount` double NOT NULL DEFAULT '0',
  `pending_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`pin_id`),
  KEY `used_user` (`used_user`),
  KEY `pending_id` (`pending_id`),
  CONSTRAINT `39_pin_used_ibfk_15` FOREIGN KEY (`used_user`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_pin_used_ibfk_16` FOREIGN KEY (`pending_id`) REFERENCES `39_pending_registration` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_pin_used`
--

LOCK TABLES `39_pin_used` WRITE;
/*!40000 ALTER TABLE `39_pin_used` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_pin_used` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_placement_change_history`
--

DROP TABLE IF EXISTS `39_placement_change_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_placement_change_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `old_placement_id` int(11) unsigned DEFAULT NULL,
  `old_position` varchar(5) NOT NULL,
  `new_placement_id` int(11) unsigned DEFAULT NULL,
  `new_position` varchar(5) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `old_placement_id` (`old_placement_id`),
  KEY `new_placement_id` (`new_placement_id`),
  CONSTRAINT `39_placement_change_history_ibfk_22` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_placement_change_history_ibfk_23` FOREIGN KEY (`old_placement_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_placement_change_history_ibfk_24` FOREIGN KEY (`new_placement_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_placement_change_history`
--

LOCK TABLES `39_placement_change_history` WRITE;
/*!40000 ALTER TABLE `39_placement_change_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_placement_change_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_pool_bonus`
--

DROP TABLE IF EXISTS `39_pool_bonus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_pool_bonus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level_no` int(11) NOT NULL,
  `bonus_percent` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_pool_bonus`
--

LOCK TABLES `39_pool_bonus` WRITE;
/*!40000 ALTER TABLE `39_pool_bonus` DISABLE KEYS */;
INSERT INTO `39_pool_bonus` VALUES (1,1,60),(2,2,40);
/*!40000 ALTER TABLE `39_pool_bonus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_public_holidays`
--

DROP TABLE IF EXISTS `39_public_holidays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_public_holidays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `reason` varchar(50) DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_public_holidays`
--

LOCK TABLES `39_public_holidays` WRITE;
/*!40000 ALTER TABLE `39_public_holidays` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_public_holidays` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_purchase_rank`
--

DROP TABLE IF EXISTS `39_purchase_rank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_purchase_rank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rank_id` int(11) NOT NULL,
  `package_id` varchar(10) NOT NULL,
  `package_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `rank_id` (`rank_id`),
  KEY `package_id` (`package_id`),
  CONSTRAINT `39_purchase_rank_ibfk_1` FOREIGN KEY (`rank_id`) REFERENCES `39_rank_details` (`rank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_purchase_rank`
--

LOCK TABLES `39_purchase_rank` WRITE;
/*!40000 ALTER TABLE `39_purchase_rank` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_purchase_rank` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_purchase_wallet_history`
--

DROP TABLE IF EXISTS `39_purchase_wallet_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_purchase_wallet_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_user_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `ewallet_refid` int(11) DEFAULT NULL,
  `transaction_id` int(11) NOT NULL DEFAULT '0',
  `amount` double NOT NULL,
  `purchase_wallet` double NOT NULL,
  `amount_type` varchar(200) NOT NULL,
  `tds` double NOT NULL,
  `type` varchar(32) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `from_user_id` (`from_user_id`),
  CONSTRAINT `39_purchase_wallet_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_purchase_wallet_history_ibfk_2` FOREIGN KEY (`from_user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_purchase_wallet_history`
--

LOCK TABLES `39_purchase_wallet_history` WRITE;
/*!40000 ALTER TABLE `39_purchase_wallet_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_purchase_wallet_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_pv_history_details`
--

DROP TABLE IF EXISTS `39_pv_history_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_pv_history_details` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `from_id` int(11) NOT NULL,
  `personal_pv` int(100) NOT NULL,
  `group_pv` int(11) NOT NULL,
  `pv_obtained_by` text NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_pv_history_details`
--

LOCK TABLES `39_pv_history_details` WRITE;
/*!40000 ALTER TABLE `39_pv_history_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_pv_history_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_rank_configuration`
--

DROP TABLE IF EXISTS `39_rank_configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_rank_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rank_expiry` varchar(50) NOT NULL,
  `default_rank_id` int(11) NOT NULL,
  `referal_count` int(11) NOT NULL DEFAULT '1',
  `personal_pv` int(11) NOT NULL DEFAULT '0',
  `group_pv` int(11) NOT NULL DEFAULT '0',
  `joinee_package` int(11) NOT NULL DEFAULT '0',
  `downline_member_count` int(11) NOT NULL DEFAULT '0',
  `downline_purchase_count` int(11) NOT NULL DEFAULT '0',
  `downline_rank` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_rank_configuration`
--

LOCK TABLES `39_rank_configuration` WRITE;
/*!40000 ALTER TABLE `39_rank_configuration` DISABLE KEYS */;
INSERT INTO `39_rank_configuration` VALUES (1,'fixed',0,1,0,0,0,0,0,0);
/*!40000 ALTER TABLE `39_rank_configuration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_rank_details`
--

DROP TABLE IF EXISTS `39_rank_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_rank_details` (
  `rank_id` int(11) NOT NULL AUTO_INCREMENT,
  `rank_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `referal_count` int(100) NOT NULL DEFAULT '0',
  `rank_bonus` varchar(20) CHARACTER SET utf8 NOT NULL,
  `rank_status` varchar(250) CHARACTER SET utf8 NOT NULL,
  `delete_status` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `party_comm` int(11) NOT NULL DEFAULT '0',
  `personal_pv` int(50) NOT NULL DEFAULT '0',
  `gpv` int(50) NOT NULL DEFAULT '0',
  `downline_count` int(50) NOT NULL DEFAULT '0',
  `referal_commission` double NOT NULL DEFAULT '0',
  `rank_color` varchar(50) NOT NULL,
  `downline_rank_id` int(11) NOT NULL,
  `downline_rank_count` int(11) NOT NULL DEFAULT '0',
  `team_member_count` int(11) NOT NULL DEFAULT '0',
  `pool_bonus_perc` double NOT NULL DEFAULT '0',
  `pool_status` varchar(3) NOT NULL DEFAULT 'no',
  `tree_icon` text NOT NULL,
  PRIMARY KEY (`rank_id`),
  KEY `rank_status` (`rank_status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_rank_details`
--

LOCK TABLES `39_rank_details` WRITE;
/*!40000 ALTER TABLE `39_rank_details` DISABLE KEYS */;
INSERT INTO `39_rank_details` VALUES (1,'Bronze',2,'100','active','yes',0,50,100,2,5,'#cd7f32',0,0,0,10,'yes','rank1.png'),(2,'Silver',4,'200','active','yes',0,100,200,4,10,'#C0C0C0',0,0,0,20,'yes','rank2.png'),(3,'Gold',6,'300','active','yes',0,150,300,6,15,'#FFD700',0,0,0,0,'no','rank3.png'),(4,'Platinum',8,'400','active','yes',0,200,400,8,20,'#e5e4e2',0,0,0,0,'no','rank4.png');
/*!40000 ALTER TABLE `39_rank_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_rank_history`
--

DROP TABLE IF EXISTS `39_rank_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_rank_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `current_rank` int(11) DEFAULT NULL,
  `new_rank` int(11) DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `user_id` (`user_id`),
  KEY `current_rank` (`current_rank`),
  KEY `new_rank` (`new_rank`),
  CONSTRAINT `39_rank_history_ibfk_22` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_rank_history_ibfk_23` FOREIGN KEY (`current_rank`) REFERENCES `39_rank_details` (`rank_id`),
  CONSTRAINT `39_rank_history_ibfk_24` FOREIGN KEY (`new_rank`) REFERENCES `39_rank_details` (`rank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_rank_history`
--

LOCK TABLES `39_rank_history` WRITE;
/*!40000 ALTER TABLE `39_rank_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_rank_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_rawaddr_response`
--

DROP TABLE IF EXISTS `39_rawaddr_response`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_rawaddr_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(250) NOT NULL,
  `txn_hash` text NOT NULL,
  `response` text NOT NULL,
  `invoice_id` varchar(250) NOT NULL,
  `date` datetime NOT NULL,
  `status` varchar(250) NOT NULL,
  `user_group_id` int(11) NOT NULL,
  `used_for` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_rawaddr_response`
--

LOCK TABLES `39_rawaddr_response` WRITE;
/*!40000 ALTER TABLE `39_rawaddr_response` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_rawaddr_response` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_react_menu`
--

DROP TABLE IF EXISTS `39_react_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_react_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL DEFAULT '',
  `icon` varchar(30) NOT NULL DEFAULT 'fa fa-circle',
  `perm_user` tinyint(4) NOT NULL DEFAULT '0',
  `perm_app` tinyint(4) NOT NULL DEFAULT '0',
  `show_order` int(11) NOT NULL DEFAULT '100',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  UNIQUE KEY `id_2` (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_react_menu`
--

LOCK TABLES `39_react_menu` WRITE;
/*!40000 ALTER TABLE `39_react_menu` DISABLE KEYS */;
INSERT INTO `39_react_menu` VALUES (1,'network','fa fa-sitemap',1,1,3,1),(2,'register','fa fa-user-plus',1,1,6,1),(3,'ewallet','fa fa-briefcase',1,1,9,1),(4,'payout','fa fa-money',1,1,12,1),(5,'profile','fa fa-address-book-o',1,1,15,1),(6,'package','fa fa-circle',1,1,18,1),(7,'epin','fa fa-bookmark-o',1,1,21,1),(8,'shopping','fa fa-shopping-bag',1,1,24,1),(9,'reports','fa fa-bar-chart',1,1,27,1),(10,'mailbox','fa fa-envelope',1,1,30,1),(11,'tools','fa fa-wrench',1,1,33,1),(12,'support','fa fa-ticket',1,1,36,1),(13,'crm','fa fa-users',1,1,36,1);
/*!40000 ALTER TABLE `39_react_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_replica_banner`
--

DROP TABLE IF EXISTS `39_replica_banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_replica_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `banner` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `39_replica_banner_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_replica_banner`
--

LOCK TABLES `39_replica_banner` WRITE;
/*!40000 ALTER TABLE `39_replica_banner` DISABLE KEYS */;
INSERT INTO `39_replica_banner` VALUES (1,NULL,'default_banner.jpg');
/*!40000 ALTER TABLE `39_replica_banner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_replica_content`
--

DROP TABLE IF EXISTS `39_replica_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_replica_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `user_id` int(20) DEFAULT NULL,
  `lang_id` int(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_replica_content`
--

LOCK TABLES `39_replica_content` WRITE;
/*!40000 ALTER TABLE `39_replica_content` DISABLE KEYS */;
INSERT INTO `39_replica_content` VALUES (1,'home_title1','software name v1.1',NULL,1),(3,'home_title2','software title and some heading content',NULL,1),(5,'plan','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">OU PLAN</h2>\n\n<h3 class=\"subheading\">software is integrated with Replicating Website</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan11.jpg\" />\n<h3>Plan header 1</h3>\n\n<p>The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans. This is developed by a</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.9s\" style=\"visibility: visible; animation-delay: 0.9s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan12.jpg\" />\n<h3>Plan header 2</h3>\n\n<p>The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans. This is developed by a</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"1s\" style=\"visibility: visible; animation-delay: 1s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan14.jpg\" />\n<h3>Plan header 3</h3>\n\n<p>The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans. This is developed by a</p>\n</div>\n</div>\n',NULL,1),(7,'contact_phone','99999999',NULL,1),(9,'contact_mail','companyname@mail.in',NULL,1),(11,'contact_address','address                      \r\n                           \r\n                           \r\n                           \r\n                           \r\n                           \r\n                           \r\n                           ',NULL,1),(13,'policy','<p>All subscribers of MLM services agree to be bound by the terms of this service. The MLM software is an entire solution for all type of business plan like Binary, Matrix, Unilevel and many other compensation plans. This is developed by a leading MLM software development company COMPANY NAME. More over these we are keen to construct MLM software as per the business plan suggested by the clients.This MLM software is featured of with integrated with SMS, E-Wallet,Replicating Website,E-Pin,E-Commerce, Shopping Cart,Web Design and more</p>\r\n',NULL,1),(15,'terms','<p>All subscribers of MLM services agree to be bound by the terms of this service. The MLM software is an entire solution for all type of business plan like Binary, Matrix, Unilevel and many other compensation plans. This is developed by a leading MLM software development company COMPANY NAME. More over these we are keen to construct MLM software as per the business plan suggested by the clients.This MLM software is featured of with integrated with SMS, E-Wallet,Replicating Website,E-Pin,E-Commerce, Shopping Cart,Web Design and more</p>\r\n',NULL,1),(17,'about','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">ABOUT US</h2>\n\n<h3 class=\"subheading\">software is integrated with Replicating Website</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-6 col-md-7 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\">\n<h3>about title and some description about title and some description about title and some.</h3>\n\n<p>The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans. This is developed by a leading MLM software development company Company name. More over these we are keen to construct MLM software as per the business plan suggested by the clients.This MLM software is featured of with integrated with SMS, E-Wallet, Replicating Website, E-Pin, E-Commerce Shopping Cart,Web Design</p>\n</div>\n\n<div class=\"col-lg-6 col-md-5 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/overview-img.jpg\" /></div>\n</div>\n',NULL,1),(19,'home_title1','nombre del software v1.1',NULL,2),(21,'home_title2','título del software y algún contenido de encabezado',NULL,2),(23,'plan','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">NUESTRO PLAN</h2>\n\n<h3 class=\"subheading\">el software está integrado con el sitio web replicante</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan11.jpg\" />\n<h3>Plan de encabezado 1</h3>\n\n<p>El software es una solución completa para todo tipo de plan de negocios como Binary, Matrix, Unilevel y muchos otros planes de compensación. Esto es desarrollado por un</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.9s\" style=\"visibility: visible; animation-delay: 0.9s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan12.jpg\" />\n<h3>Plan de encabezado 2</h3>\n\n<p>El software es una solución completa para todo tipo de plan de negocios como Binary, Matrix, Unilevel y muchos otros planes de compensación. Esto es desarrollado por un</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"1s\" style=\"visibility: visible; animation-delay: 1s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan14.jpg\" />\n<h3>Plan de encabezado 3</h3>\n\n<p>El software es una solución completa para todo tipo de plan de negocios como Binary, Matrix, Unilevel y muchos otros planes de compensación. Esto es desarrollado por un </p>\n</div>\n</div>\n',NULL,2),(25,'contact_phone','99999999',NULL,2),(27,'contact_mail','companyname@mail.in',NULL,2),(29,'contact_address','dirección',NULL,2),(31,'policy','<p>Todos los suscriptores de los servicios de MLM aceptan regirse por los términos de este servicio. El software MLM es una solución completa para todo tipo de plan de negocios como Binary, Matrix, Unilevel y muchos otros planes de compensación. Esto es desarrollado por una empresa líder de desarrollo de software MLM NOMBRE DE LA COMPAÑÍA. Además, estamos interesados en construir un software MLM según el plan de negocios sugerido por los clientes. Este software MLM se presenta integrado con SMS, billetera electrónica, sitio web replicante, pin electrónico, comercio electrónico, carrito de compras, web Diseño y mas</p>\n',NULL,2),(33,'terms','<p>Todos los suscriptores de los servicios de MLM aceptan regirse por los términos de este servicio. El software MLM es una solución completa para todo tipo de plan de negocios como Binary, Matrix, Unilevel y muchos otros planes de compensación. Esto es desarrollado por una empresa líder de desarrollo de software MLM NOMBRE DE LA COMPAÑÍA. Además, estamos interesados en construir un software MLM según el plan de negocios sugerido por los clientes. Este software MLM se presenta integrado con SMS, billetera electrónica, sitio web replicante, pin electrónico, comercio electrónico, carrito de compras, web Diseño y mas</p>\n',NULL,2),(35,'about','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">SOBRE NOSOTROS</h2>\n\n<h3 class=\"subheading\">el software está integrado con el sitio web replicante</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-6 col-md-7 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\">\n<h3>sobre el título y alguna descripción sobre el título y alguna descripción sobre el título y algo.</h3>\n\n<p>El software es una solución completa para todo tipo de plan de negocios como Binary, Matrix, Unilevel y muchos otros planes de compensación. Esto es desarrollado por una compañía líder en desarrollo de software MLM Nombre de la compañía. Además, estamos interesados en construir un software MLM según el plan de negocios sugerido por los clientes. Este software MLM se presenta integrado con SMS, billetera electrónica, sitio web replicante, pin electrónico, carrito de compras de comercio electrónico, diseño web</p>\n</div>\n\n<div class=\"col-lg-6 col-md-5 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/overview-img.jpg\" /></div>\n</div>\n',NULL,2),(37,'home_title1','软件名称v1.1',NULL,3),(39,'home_title2','软件标题和一些标题内容',NULL,3),(41,'plan','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">我们的计划</h2>\n\n<h3 class=\"subheading\">该软件与复制网站集成在一起</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan11.jpg\" />\n<h3>计划标题1</h3>\n\n<p>该软件是针对所有业务计划（例如Binary，Matrix，Unilevel和许多其他薪酬计划）的完整解决方案。 这是由</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.9s\" style=\"visibility: visible; animation-delay: 0.9s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan12.jpg\" />\n<h3>计划标题2</h3>\n\n<p>该软件是针对所有业务计划（例如Binary，Matrix，Unilevel和许多其他薪酬计划）的完整解决方案。 这是由</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"1s\" style=\"visibility: visible; animation-delay: 1s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan14.jpg\" />\n<h3>计划标题3</h3>\n\n<p>该软件是针对所有业务计划（例如Binary，Matrix，Unilevel和许多其他薪酬计划）的完整解决方案。 这是由</p>\n</div>\n</div>\n\n',NULL,3),(43,'contact_phone','99999999',NULL,3),(45,'contact_mail','companyname@mail.in',NULL,3),(47,'contact_address','地址',NULL,3),(49,'policy','<p>所有MLM服务的订户均同意受该服务条款的约束。 MLM软件是针对所有类型的业务计划（例如二进制，矩阵，Unilevel和许多其他薪酬计划）的完整解决方案。 这是由领先的MLM软件开发公司COMPANY NAME开发的。 此外，我们热衷于根据客户建议的业务计划构建MLM软件。该MLM软件具有与SMS，E-Wallet，复制网站，E-Pin，电子商务，购物车，Web集成的功能。 设计及更多</p>\n',NULL,3),(51,'terms','<p>所有MLM服务的订户均同意受该服务条款的约束。 MLM软件是针对所有类型的业务计划（例如二进制，矩阵，Unilevel和许多其他薪酬计划）的完整解决方案。 这是由领先的MLM软件开发公司COMPANY NAME开发的。 此外，我们热衷于根据客户建议的业务计划构建MLM软件。该MLM软件具有与SMS，E-Wallet，复制网站，E-Pin，电子商务，购物车，Web集成的功能。 设计及更多</p>\n',NULL,3),(53,'about','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">关于我们</h2>\n\n<h3 class=\"subheading\">该软件与复制网站集成在一起</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-6 col-md-7 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\">\n<h3>关于标题和一些有关标题的描述，以及一些有关标题的描述，以及一些。</h3>\n\n<p>该软件是针对所有业务计划（例如Binary，Matrix，Unilevel和许多其他薪酬计划）的完整解决方案。 这是由领先的MLM软件开发公司Company name开发的。 此外，我们热衷于根据客户建议的业务计划构建MLM软件。该MLM软件具有与SMS，E-Wallet，复制网站，E-Pin，电子商务购物车，网页设计集成的功能。</p>\n</div>\n\n<div class=\"col-lg-6 col-md-5 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/overview-img.jpg\" /></div>\n</div>\n',NULL,3),(55,'home_title1','Name der Software v1.1',NULL,4),(57,'home_title2','Softwaretitel und einige Überschriften',NULL,4),(59,'plan','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">UNSER PLAN</h2>\n\n<h3 class=\"subheading\">Die Software ist in Replicating Website integriert</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan11.jpg\" />\n<h3>Plankopf 1</h3>\n\n<p>Die Software ist eine Komplettlösung für alle Arten von Geschäftsplänen wie Binary, Matrix, Unilevel und viele andere Vergütungspläne. Dies wird entwickelt von a</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.9s\" style=\"visibility: visible; animation-delay: 0.9s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan12.jpg\" />\n<h3>Plankopf 2</h3>\n\n<p>Die Software ist eine Komplettlösung für alle Arten von Geschäftsplänen wie Binary, Matrix, Unilevel und viele andere Vergütungspläne. Dies wird entwickelt von a</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"1s\" style=\"visibility: visible; animation-delay: 1s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan14.jpg\" />\n<h3>Plankopf 3</h3>\n\n<p>Die Software ist eine Komplettlösung für alle Arten von Geschäftsplänen wie Binary, Matrix, Unilevel und viele andere Vergütungspläne. Dies wird entwickelt von a</p>\n</div>\n</div>\n',NULL,4),(61,'contact_phone','99999999',NULL,4),(63,'contact_mail','companyname@mail.in',NULL,4),(65,'contact_address','Adresse',NULL,4),(67,'policy','<p>Alle Abonnenten von MLM-Diensten stimmen zu, an die Bedingungen dieses Dienstes gebunden zu sein. Die MLM-Software ist eine Komplettlösung für alle Arten von Geschäftsplänen wie Binary, Matrix, Unilevel und viele andere Vergütungspläne. Dies wird von einem führenden MLM-Softwareentwicklungsunternehmen FIRMENNAME entwickelt. Darüber hinaus sind wir bestrebt, MLM-Software gemäß dem von den Kunden vorgeschlagenen Geschäftsplan zu entwickeln. Diese MLM-Software ist mit SMS, E-Wallet, Website-Replikation, E-Pin, E-Commerce, Warenkorb und Web integriert Design und mehr</p>\n',NULL,4),(69,'terms','<p>Alle Abonnenten von MLM-Diensten stimmen zu, an die Bedingungen dieses Dienstes gebunden zu sein. Die MLM-Software ist eine Komplettlösung für alle Arten von Geschäftsplänen wie Binary, Matrix, Unilevel und viele andere Vergütungspläne. Dies wird von einem führenden MLM-Softwareentwicklungsunternehmen FIRMENNAME entwickelt. Darüber hinaus sind wir bestrebt, MLM-Software gemäß dem von den Kunden vorgeschlagenen Geschäftsplan zu entwickeln. Diese MLM-Software ist mit SMS, E-Wallet, Website-Replikation, E-Pin, E-Commerce, Warenkorb und Web integriert Design und mehr</p>\n',NULL,4),(71,'about','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">ÜBER UNS</h2>\n\n<h3 class=\"subheading\">Die Software ist in Replicating Website integriert</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-6 col-md-7 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\">\n<h3>über den Titel und eine Beschreibung über den Titel und eine Beschreibung über den Titel und einige.</h3>\n\n<p>Die Software ist eine Komplettlösung für alle Arten von Geschäftsplänen wie Binary, Matrix, Unilevel und viele andere Vergütungspläne. Dies wird von einem führenden MLM-Softwareentwicklungsunternehmen entwickelt. Darüber hinaus sind wir bestrebt, MLM-Software gemäß dem von den Kunden vorgeschlagenen Geschäftsplan zu entwickeln. Diese MLM-Software ist mit SMS, E-Wallet, Website-Replikation, E-Pin, E-Commerce-Warenkorb und Webdesign ausgestattet</p>\n</div>\n\n<div class=\"col-lg-6 col-md-5 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/overview-img.jpg\" /></div>\n</div>\n',NULL,4),(73,'home_title1','nome do software v1.1',NULL,5),(75,'home_title2','título do software e algum conteúdo do cabeçalho',NULL,5),(77,'plan','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">NOSSO PLANO</h2>\n\n<h3 class=\"subheading\">software é integrado ao site de replicação</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan11.jpg\" />\n<h3>Planejar cabeçalho 1</h3>\n\n<p>O software é uma solução completa para todo tipo de plano de negócios, como Binário, Matriz, Unilevel e muitos outros planos de remuneração. Isso é desenvolvido por um</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.9s\" style=\"visibility: visible; animation-delay: 0.9s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan12.jpg\" />\n<h3>Cabeçalho 2 do plano</h3>\n\n<p>O software é uma solução completa para todo tipo de plano de negócios, como Binário, Matriz, Unilevel e muitos outros planos de remuneração. Isso é desenvolvido por um</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"1s\" style=\"visibility: visible; animation-delay: 1s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan14.jpg\" />\n<h3>Cabeçalho do plano 3</h3>\n\n<p>O software é uma solução completa para todo tipo de plano de negócios, como Binário, Matriz, Unilevel e muitos outros planos de remuneração. Isso é desenvolvido por um</p>\n</div>\n</div>\n',NULL,5),(79,'contact_phone','99999999',NULL,5),(81,'contact_mail','companyname@mail.in',NULL,5),(83,'contact_address','endereço',NULL,5),(85,'policy','<p>Todos os assinantes dos serviços de MLM concordam em ficar vinculados aos termos deste serviço. O software MLM é uma solução completa para todo tipo de plano de negócios, como Binário, Matriz, Unilevel e muitos outros planos de remuneração. Isso é desenvolvido por uma empresa líder em desenvolvimento de software MLM NOME DA EMPRESA. Além disso, estamos empenhados em construir o software MLM de acordo com o plano de negócios sugerido pelos clientes. Design e mais</p>\n',NULL,5),(87,'terms','<p>Todos os assinantes dos serviços de MLM concordam em ficar vinculados aos termos deste serviço. O software MLM é uma solução completa para todo tipo de plano de negócios, como Binário, Matriz, Unilevel e muitos outros planos de remuneração. Isso é desenvolvido por uma empresa líder em desenvolvimento de software MLM NOME DA EMPRESA. Além disso, estamos empenhados em construir o software MLM de acordo com o plano de negócios sugerido pelos clientes. Design e mais</p>\n',NULL,5),(89,'about','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">SOBRE NÓS</h2>\n\n<h3 class=\"subheading\">software é integrado ao site de replicação</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-6 col-md-7 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\">\n<h3>sobre título e alguma descrição sobre título e alguma descrição sobre título e outras.</h3>\n\n<p>O software é uma solução completa para todo tipo de plano de negócios, como Binário, Matriz, Unilevel e muitos outros planos de remuneração. Isso é desenvolvido por uma empresa líder no desenvolvimento de software de MLM, nome da empresa. Além disso, estamos empenhados em construir o software MLM de acordo com o plano de negócios sugerido pelos clientes.</p>\n</div>\n\n<div class=\"col-lg-6 col-md-5 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/overview-img.jpg\" /></div>\n</div>\n',NULL,5),(91,'home_title1','nom du logiciel v1.1',NULL,6),(93,'home_title2','titre du logiciel et contenu de la rubrique',NULL,6),(95,'plan','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">NOTRE PLAN</h2>\n\n<h3 class=\"subheading\">le logiciel est intégré à Replicating Website</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan11.jpg\" />\n<h3>En-tête de plan 1</h3>\n\n<p>Le logiciel est une solution complète pour tout type de plan d\'affaires comme Binary, Matrix, Unilevel et de nombreux autres plans de rémunération. Ceci est développé par un</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.9s\" style=\"visibility: visible; animation-delay: 0.9s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan12.jpg\" />\n<h3>En-tête de plan 2</h3>\n\n<p>Le logiciel est une solution complète pour tout type de plan d\'affaires comme Binary, Matrix, Unilevel et de nombreux autres plans de rémunération. Ceci est développé par un</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"1s\" style=\"visibility: visible; animation-delay: 1s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan14.jpg\" />\n<h3>En-tête de plan 3</h3>\n\n<p>Le logiciel est une solution complète pour tout type de plan d\'affaires comme Binary, Matrix, Unilevel et de nombreux autres plans de rémunération. Ceci est développé par un</p>\n</div>\n</div>\n',NULL,6),(97,'contact_phone','99999999',NULL,6),(99,'contact_mail','companyname@mail.in',NULL,6),(101,'contact_address','adresse',NULL,6),(103,'policy','<p>Tous les abonnés des services MLM acceptent d\'être liés par les termes de ce service. Le logiciel MLM est une solution complète pour tout type de plan d\'affaires comme Binary, Matrix, Unilevel et de nombreux autres plans de rémunération. Il est développé par une société leader dans le développement de logiciels MLM NOM DE L\'ENTREPRISE. De plus, nous tenons à construire un logiciel MLM selon le plan d\'affaires suggéré par les clients.Ce logiciel MLM est présenté avec intégré avec SMS, E-Wallet, Replicating Website, E-Pin, E-Commerce, Shopping Cart, Web Design et plus</p>\n',NULL,6),(105,'terms','<p>Tous les abonnés des services MLM acceptent d\'être liés par les termes de ce service. Le logiciel MLM est une solution complète pour tout type de plan d\'affaires comme Binary, Matrix, Unilevel et de nombreux autres plans de rémunération. Il est développé par une société leader dans le développement de logiciels MLM NOM DE L\'ENTREPRISE. De plus, nous tenons à construire un logiciel MLM selon le plan d\'affaires suggéré par les clients.Ce logiciel MLM est présenté avec intégré avec SMS, E-Wallet, Replicating Website, E-Pin, E-Commerce, Shopping Cart, Web Design et plus</p>\n',NULL,6),(107,'about','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">À PROPOS DE NOUS</h2>\n\n<h3 class=\"subheading\">le logiciel est intégré à Replicating Website</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-6 col-md-7 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\">\n<h3>sur le titre et une description sur le titre et une description sur le titre et certains.</h3>\n\n<p>Le logiciel est une solution complète pour tout type de plan d\'affaires comme Binary, Matrix, Unilevel et de nombreux autres plans de rémunération. Il est développé par une société leader dans le développement de logiciels MLM. Nom de l\'entreprise. De plus, nous tenons à construire un logiciel MLM selon le plan d\'affaires suggéré par les clients.Ce logiciel MLM est présenté avec intégré avec SMS, E-Wallet, Replicating Website, E-Pin, E-Commerce Shopping Cart, Web Design</p>\n</div>\n\n<div class=\"col-lg-6 col-md-5 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/overview-img.jpg\" /></div>\n</div>\n',NULL,6),(109,'home_title1','nome del software v1.1',NULL,7),(111,'home_title2','titolo del software e alcuni contenuti dell\'intestazione',NULL,7),(113,'plan','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">IL NOSTRO PIANO</h2>\n\n<h3 class=\"subheading\">il software è integrato con Replicating Website</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan11.jpg\" />\n<h3>Intestazione del piano 1</h3>\n\n<p>Il software è un\'intera soluzione per tutti i tipi di piani aziendali come Binary, Matrix, Unilevel e molti altri piani di compensazione. Questo è sviluppato da a</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.9s\" style=\"visibility: visible; animation-delay: 0.9s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan12.jpg\" />\n<h3>Intestazione del piano 2</h3>\n\n<p>Il software è un\'intera soluzione per tutti i tipi di piani aziendali come Binary, Matrix, Unilevel e molti altri piani di compensazione. Questo è sviluppato da a</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"1s\" style=\"visibility: visible; animation-delay: 1s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan14.jpg\" />\n<h3>Intestazione del piano 3</h3>\n\n<p>Il software è un\'intera soluzione per tutti i tipi di piani aziendali come Binary, Matrix, Unilevel e molti altri piani di compensazione. Questo è sviluppato da a</p>\n</div>\n</div>\n',NULL,7),(115,'contact_phone','99999999',NULL,7),(117,'contact_mail','companyname@mail.in',NULL,7),(119,'contact_address','indirizzo',NULL,7),(121,'policy','<p>Tutti gli abbonati ai servizi MLM accettano di essere vincolati dai termini di questo servizio. Il software MLM è un\'intera soluzione per tutti i tipi di piani aziendali come Binary, Matrix, Unilevel e molti altri piani di compensazione. Questo è sviluppato da un\'azienda leader nello sviluppo di software MLM COMPANY NAME. Inoltre, desideriamo costruire software MLM secondo il piano aziendale suggerito dai clienti. Questo software MLM è dotato di integrato con SMS, E-Wallet, Sito Web replicante, E-Pin, E-Commerce, Carrello, Web Design e altro ancora</p>\n',NULL,7),(123,'terms','<p>Tutti gli abbonati ai servizi MLM accettano di essere vincolati dai termini di questo servizio. Il software MLM è un\'intera soluzione per tutti i tipi di piani aziendali come Binary, Matrix, Unilevel e molti altri piani di compensazione. Questo è sviluppato da un\'azienda leader nello sviluppo di software MLM COMPANY NAME. Inoltre, desideriamo costruire software MLM secondo il piano aziendale suggerito dai clienti. Questo software MLM è dotato di integrato con SMS, E-Wallet, Sito Web replicante, E-Pin, E-Commerce, Carrello, Web Design e altro ancora</p>\n',NULL,7),(125,'about','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">RIGUARDO A NOI</h2>\n\n<h3 class=\"subheading\">il software è integrato con Replicating Website</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-6 col-md-7 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\">\n<h3>sul titolo e alcune descrizioni sul titolo e alcune descrizioni sul titolo e altre.</h3>\n\n<p>Il software è un\'intera soluzione per tutti i tipi di piani aziendali come Binary, Matrix, Unilevel e molti altri piani di compensazione. Questo è sviluppato da una delle principali società di sviluppo software MLM Nome dell\'azienda. Inoltre, desideriamo costruire software MLM secondo il piano aziendale suggerito dai clienti. Questo software MLM è dotato di integrato con SMS, E-Wallet, Sito Web replicante, E-Pin, Carrello e-commerce, Web design</p>\n</div>\n\n<div class=\"col-lg-6 col-md-5 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/overview-img.jpg\" /></div>\n</div>\n',NULL,7),(127,'home_title1','yazılım adı v1.1',NULL,8),(129,'home_title2','yazılım başlığı ve bazı başlık içeriği',NULL,8),(131,'plan','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">PLANIMIZ</h2>\n\n<h3 class=\"subheading\">Yazılım Replicating Website ile entegre edilmiştir</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan11.jpg\" />\n<h3>Başlık 1\'i planla</h3>\n\n<p>Yazılım, Binary, Matrix, Unilevel ve diğer birçok tazminat planı gibi her türlü iş planı için tam bir çözümdür. Bu bir tarafından geliştirilmiştir</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.9s\" style=\"visibility: visible; animation-delay: 0.9s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan12.jpg\" />\n<h3>Başlık 2\'yi planlayın</h3>\n\n<p>Yazılım, Binary, Matrix, Unilevel ve diğer birçok tazminat planı gibi her türlü iş planı için tam bir çözümdür. Bu bir tarafından geliştirilmiştir</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"1s\" style=\"visibility: visible; animation-delay: 1s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan14.jpg\" />\n<h3>Başlık 3\'ü planlayın</h3>\n\n<p>Yazılım, Binary, Matrix, Unilevel ve diğer birçok tazminat planı gibi her türlü iş planı için tam bir çözümdür. Bu bir tarafından geliştirilmiştir</p>\n</div>\n</div>\n',NULL,8),(133,'contact_phone','99999999',NULL,8),(135,'contact_mail','companyname@mail.in',NULL,8),(137,'contact_address','adres',NULL,8),(139,'policy','<p>Tüm MLM servislerinin aboneleri bu servisin şartlarına bağlı kalmayı kabul eder. MLM yazılımı, Binary, Matrix, Unilevel ve diğer birçok tazminat planı gibi her türlü iş planı için eksiksiz bir çözümdür. Bu, önde gelen bir MLM yazılım geliştirme şirketi ŞİRKET ADI tarafından geliştirilmiştir. Bunların ötesinde, müşterilerimiz tarafından önerilen iş planına göre MLM yazılımı kurmak istiyoruz. Bu MLM yazılımı, SMS, E-Cüzdan, Web Sitesini Kopyalama, E-Pin, E-Ticaret, Alışveriş Sepeti, Web ile entegre özellikli Tasarım ve daha fazlası</p>\n',NULL,8),(141,'terms','<p>Tüm MLM servislerinin aboneleri bu servisin şartlarına bağlı kalmayı kabul eder. MLM yazılımı, Binary, Matrix, Unilevel ve diğer birçok tazminat planı gibi her türlü iş planı için eksiksiz bir çözümdür. Bu, önde gelen bir MLM yazılım geliştirme şirketi ŞİRKET ADI tarafından geliştirilmiştir. Bunların ötesinde, müşterilerimiz tarafından önerilen iş planına göre MLM yazılımı kurmak istiyoruz. Bu MLM yazılımı, SMS, E-Cüzdan, Web Sitesini Kopyalama, E-Pin, E-Ticaret, Alışveriş Sepeti, Web ile entegre özellikli Tasarım ve daha fazlası</p>\n',NULL,8),(143,'about','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">HAKKIMIZDA</h2>\n\n<h3 class=\"subheading\">Yazılım Replicating Website ile entegre edilmiştir</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-6 col-md-7 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\">\n<h3>unvan ve unvan ile ilgili açıklama ve unvan ve unvan ile ilgili açıklama hakkında.</h3>\n\n<p>Yazılım, Binary, Matrix, Unilevel ve diğer birçok tazminat planı gibi her türlü iş planı için tam bir çözümdür. Bu, lider bir MLM yazılım geliştirme şirketi olan Şirket adı tarafından geliştirilmiştir. Bunların ötesinde, müşterilerimiz tarafından önerilen iş planına göre MLM yazılımı kurmak istiyoruz. Bu MLM yazılımı, SMS, E-Cüzdan, Web Sitesini Kopyalama, E-Pin, E-Ticaret Alışveriş Sepeti, Web Tasarımı ile entegre edilmiştir.</p>\n</div>\n\n<div class=\"col-lg-6 col-md-5 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/overview-img.jpg\" /></div>\n</div>\n',NULL,8),(145,'home_title1','nazwa oprogramowania v1.1',NULL,9),(147,'home_title2','tytuł oprogramowania i niektóre treści nagłówka',NULL,9),(149,'plan','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">PLANIMIZ</h2>\n\n<h3 class=\"subheading\">Yazılım Replicating Website ile entegre edilmiştir</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan11.jpg\" />\n<h3>Başlık 1\'i planla</h3>\n\n<p>Yazılım, Binary, Matrix, Unilevel ve diğer birçok tazminat planı gibi her türlü iş planı için tam bir çözümdür. Bu bir tarafından geliştirilmiştir</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.9s\" style=\"visibility: visible; animation-delay: 0.9s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan12.jpg\" />\n<h3>Başlık 2\'yi planlayın</h3>\n\n<p>Yazılım, Binary, Matrix, Unilevel ve diğer birçok tazminat planı gibi her türlü iş planı için tam bir çözümdür. Bu bir tarafından geliştirilmiştir</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"1s\" style=\"visibility: visible; animation-delay: 1s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan14.jpg\" />\n<h3>Başlık 3\'ü planlayın</h3>\n\n<p>Yazılım, Binary, Matrix, Unilevel ve diğer birçok tazminat planı gibi her türlü iş planı için tam bir çözümdür. Bu bir tarafından geliştirilmiştir</p>\n</div>\n</div>\n',NULL,9),(151,'contact_phone','99999999',NULL,9),(153,'contact_mail','companyname@mail.in',NULL,9),(155,'contact_address','adres',NULL,9),(157,'policy','<p>Tüm MLM servislerinin aboneleri bu servisin şartlarına bağlı kalmayı kabul eder. MLM yazılımı, Binary, Matrix, Unilevel ve diğer birçok tazminat planı gibi her türlü iş planı için eksiksiz bir çözümdür. Bu, önde gelen bir MLM yazılım geliştirme şirketi ŞİRKET ADI tarafından geliştirilmiştir. Bunların ötesinde, müşterilerimiz tarafından önerilen iş planına göre MLM yazılımı kurmak istiyoruz. Bu MLM yazılımı, SMS, E-Cüzdan, Web Sitesini Kopyalama, E-Pin, E-Ticaret, Alışveriş Sepeti, Web ile entegre özellikli Tasarım ve daha fazlası</p>\n',NULL,9),(159,'terms','<p>Tüm MLM servislerinin aboneleri bu servisin şartlarına bağlı kalmayı kabul eder. MLM yazılımı, Binary, Matrix, Unilevel ve diğer birçok tazminat planı gibi her türlü iş planı için eksiksiz bir çözümdür. Bu, önde gelen bir MLM yazılım geliştirme şirketi ŞİRKET ADI tarafından geliştirilmiştir. Bunların ötesinde, müşterilerimiz tarafından önerilen iş planına göre MLM yazılımı kurmak istiyoruz. Bu MLM yazılımı, SMS, E-Cüzdan, Web Sitesini Kopyalama, E-Pin, E-Ticaret, Alışveriş Sepeti, Web ile entegre özellikli Tasarım ve daha fazlası</p>\n',NULL,9),(161,'about','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">HAKKIMIZDA</h2>\n\n<h3 class=\"subheading\">Yazılım Replicating Website ile entegre edilmiştir</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-6 col-md-7 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\">\n<h3>o tytule i niektóre opisy dotyczące tytułu oraz niektóre opisy dotyczące tytułu i niektóre.</h3>\n\n<p>Oprogramowanie jest kompletnym rozwiązaniem dla wszystkich rodzajów biznesplanów, takich jak Binary, Matrix, Unilevel i wielu innych planów wynagrodzeń. Zostało to opracowane przez wiodącą firmę produkującą oprogramowanie MLM. Ponadto chcemy tworzyć oprogramowanie MLM zgodnie z biznesplanem zaproponowanym przez klientów. Oprogramowanie MLM jest zintegrowane z SMS, e-portfelem, replikacją strony internetowej, e-pinem, koszykiem e-commerce, projektowaniem stron internetowych</p>\n</div>\n\n<div class=\"col-lg-6 col-md-5 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/overview-img.jpg\" /></div>\n</div>\n',NULL,9),(163,'home_title1','اسم البرنامج v1.1',NULL,10),(165,'home_title2','عنوان البرنامج وبعض محتوى العنوان',NULL,10),(167,'plan','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">خطتنا</h2>\n\n<h3 class=\"subheading\">تم دمج البرنامج مع Replicating Website</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan11.jpg\" />\n<h3>رأس الخطة 1</h3>\n\n<p>يعد البرنامج حلاً كاملاً لجميع أنواع خطط الأعمال مثل Binary و Matrix و Unilevel والعديد من خطط التعويض الأخرى. تم تطوير هذا من قبل</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.9s\" style=\"visibility: visible; animation-delay: 0.9s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan12.jpg\" />\n<h3>رأس الخطة 2</h3>\n\n<p>يعد البرنامج حلاً كاملاً لجميع أنواع خطط الأعمال مثل Binary و Matrix و Unilevel والعديد من خطط التعويض الأخرى. تم تطوير هذا من قبل</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"1s\" style=\"visibility: visible; animation-delay: 1s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan14.jpg\" />\n<h3>خطة رأس 3</h3>\n\n<p>يعد البرنامج حلاً كاملاً لجميع أنواع خطط الأعمال مثل Binary و Matrix و Unilevel والعديد من خطط التعويض الأخرى. تم تطوير هذا من قبل</p>\n</div>\n</div>\n',NULL,10),(169,'contact_phone','99999999',NULL,10),(171,'contact_mail','companyname@mail.in',NULL,10),(173,'contact_address','عنوان',NULL,10),(175,'policy','<p>يوافق جميع المشتركين في خدمات الامتيازات والرهون البحرية على الالتزام بشروط هذه الخدمة. برنامج الامتيازات والرهون البحرية هو الحل الكامل لجميع أنواع خطط العمل مثل Binary و Matrix و Unilevel والعديد من خطط التعويض الأخرى. تم تطوير هذا من قبل شركة تطوير البرمجيات الامتيازات الرائدة. علاوة على ذلك ، نحن حريصون على إنشاء برنامج الامتيازات وفقًا لخطة العمل المقترحة من قبل العملاء. يتميز برنامج الامتيازات هذا بتكامل مع الرسائل النصية القصيرة ، المحفظة الإلكترونية ، موقع النسخ المتماثل ، البريد الإلكتروني ، التجارة الإلكترونية ، سلة التسوق ، الويب تصميم وأكثر من ذلك</p>\n',NULL,10),(177,'terms','<p>يوافق جميع المشتركين في خدمات الامتيازات والرهون البحرية على الالتزام بشروط هذه الخدمة. برنامج الامتيازات والرهون البحرية هو الحل الكامل لجميع أنواع خطط العمل مثل Binary و Matrix و Unilevel والعديد من خطط التعويض الأخرى. تم تطوير هذا من قبل شركة تطوير البرمجيات الامتيازات الرائدة. علاوة على ذلك ، نحن حريصون على إنشاء برنامج الامتيازات وفقًا لخطة العمل المقترحة من قبل العملاء. يتميز برنامج الامتيازات هذا بتكامل مع الرسائل النصية القصيرة ، المحفظة الإلكترونية ، موقع النسخ المتماثل ، البريد الإلكتروني ، التجارة الإلكترونية ، سلة التسوق ، الويب تصميم وأكثر من ذلك</p>\n',NULL,10),(179,'about','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">معلومات عنا</h2>\n\n<h3 class=\"subheading\">تم دمج البرنامج مع Replicating Website</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-6 col-md-7 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\">\n<h3>حول العنوان وبعض الوصف حول العنوان وبعض الوصف حول العنوان وبعض.</h3>\n\n<p>يعد البرنامج حلاً كاملاً لجميع أنواع خطط الأعمال مثل Binary و Matrix و Unilevel والعديد من خطط التعويض الأخرى. تم تطوير هذا من قبل الشركة الرائدة في تطوير الامتيازات والرهون البحرية اسم الشركة علاوة على ذلك ، نحن حريصون على إنشاء برنامج الامتيازات والرهون البحرية وفقًا لخطة العمل المقترحة من قبل العملاء. يتميز برنامج الامتيازات والرهون البحرية هذا بميزات مدمجة مع الرسائل القصيرة ، المحفظة الإلكترونية ، موقع الويب المتكرر ، البريد الإلكتروني ، سلة التسوق الإلكترونية ، تصميم المواقع الإلكترونية</p>\n</div>\n\n<div class=\"col-lg-6 col-md-5 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/overview-img.jpg\" /></div>\n</div>\n',NULL,10),(181,'home_title1','Название программного обеспечения v1.1',NULL,11),(183,'home_title2','название программного обеспечения и заголовок',NULL,11),(185,'plan','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">Наш план</h2>\n\n<h3 class=\"subheading\">Программа интегрирована с реплицирующимся сайтом</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan11.jpg\" />\n<h3>Верх плана 1</h3>\n\n<p> Программа представляет собой комплексное решение для всех видов бизнес-планов, таких как Binary, Matrix, Unilevel и многих других компенсационных планов. Это было разработано ранее</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"0.9s\" style=\"visibility: visible; animation-delay: 0.9s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan12.jpg\" />\n<h3>Верх плана 2</h3>\n\n<p>Программа представляет собой комплексное решение для всех видов бизнес-планов, таких как Binary, Matrix, Unilevel и многих других компенсационных планов. Это было разработано ранее</p>\n</div>\n\n<div class=\"col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeInUp animated\" data-wow-delay=\"1s\" style=\"visibility: visible; animation-delay: 1s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/plan14.jpg\" />\n<h3>План заголовка 3</h3>\n\n<p>Программа представляет собой комплексное решение для всех видов бизнес-планов, таких как Binary, Matrix, Unilevel и многих других компенсационных планов. Это было разработано ранее</p>\n</div>\n</div>\n',NULL,11),(187,'contact_phone','99999999',NULL,11),(189,'contact_mail','companyname@mail.in',NULL,11),(191,'contact_address','адрес',NULL,11),(193,'policy','<p>Все абоненты услуг MLM соглашаются соблюдать условия этой услуги. Программное обеспечение MLM представляет собой комплексное решение для всех типов бизнес-планов, таких как Binary, Matrix, Unilevel и многих других компенсационных планов. Это разработано ведущей компанией по разработке программного обеспечения MLM COMPANY NAME. Более того, мы стремимся создавать программное обеспечение MLM в соответствии с бизнес-планом, предложенным клиентами. Это программное обеспечение MLM оснащено интегрированным с SMS, электронным кошельком, веб-сайтом для репликации, E-Pin, электронной коммерцией, корзиной покупок, Интернетом. Дизайн и многое другое</p>\n',NULL,11),(195,'terms','<p>Все абоненты услуг MLM соглашаются соблюдать условия этой услуги. Программное обеспечение MLM представляет собой комплексное решение для всех типов бизнес-планов, таких как Binary, Matrix, Unilevel и многих других компенсационных планов. Это разработано ведущей компанией по разработке программного обеспечения MLM COMPANY NAME. Более того, мы стремимся создавать программное обеспечение MLM в соответствии с бизнес-планом, предложенным клиентами. Это программное обеспечение MLM оснащено интегрированным с SMS, электронным кошельком, веб-сайтом для репликации, E-Pin, электронной коммерцией, корзиной покупок, Интернетом. Дизайн и многое другое</p>\n',NULL,11),(197,'about','\n<div class=\"row\">\n<div class=\"col-md-12 col-sm-12 text-center\">\n<h2 class=\"heading\">О НАС</h2>\n\n<h3 class=\"subheading\">программное обеспечение интегрировано с реплицирующим сайтом</h3>\n</div>\n</div>\n\n<div class=\"row\">\n<div class=\"col-lg-6 col-md-7 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\">\n<h3>о названии и некоторые описания о названии и некоторые описания о названии и некоторые.</h3>\n\n<p>Программное обеспечение представляет собой комплексное решение для всех типов бизнес-планов, таких как Binary, Matrix, Unilevel и многих других компенсационных планов. Это разработано ведущей компанией по разработке программного обеспечения MLM Название компании. Более того, мы стремимся создавать программное обеспечение MLM в соответствии с бизнес-планом, предложенным клиентами. Это программное обеспечение MLM оснащено интегрированным с SMS, электронным кошельком, реплицирующимся веб-сайтом, E-Pin, корзиной электронной коммерции, веб-дизайном.</p>\n</div>\n\n<div class=\"col-lg-6 col-md-5 col-sm-12 wow fadeInUp animated\" data-wow-delay=\"0.6s\" style=\"visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;\"><img alt=\"\" src=\"http://localhost/WC/10.0.2/uploads/images/ckeditor/overview-img.jpg\" /></div>\n</div>\n',NULL,11);
/*!40000 ALTER TABLE `39_replica_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_repurchase_address`
--

DROP TABLE IF EXISTS `39_repurchase_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_repurchase_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `pin` int(11) NOT NULL,
  `town` varchar(11) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `default_address` enum('0','1') NOT NULL,
  `delete_status` varchar(50) NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`),
  KEY `default_address` (`default_address`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `39_repurchase_address_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_repurchase_address`
--

LOCK TABLES `39_repurchase_address` WRITE;
/*!40000 ALTER TABLE `39_repurchase_address` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_repurchase_address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_repurchase_category`
--

DROP TABLE IF EXISTS `39_repurchase_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_repurchase_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` text,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_repurchase_category`
--

LOCK TABLES `39_repurchase_category` WRITE;
/*!40000 ALTER TABLE `39_repurchase_category` DISABLE KEYS */;
INSERT INTO `39_repurchase_category` VALUES (1,'Repurchase Category 1','no','yes','2019-02-10 17:24:40'),(2,'Repurchase Category 2','no','yes','2019-02-10 17:24:40'),(3,'Repurchase Category 3','no','yes','2019-02-10 17:24:40');
/*!40000 ALTER TABLE `39_repurchase_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_repurchase_order`
--

DROP TABLE IF EXISTS `39_repurchase_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_repurchase_order` (
  `order_id` int(50) NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(30) NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `order_address_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL,
  `total_amount` double NOT NULL,
  `total_pv` double NOT NULL DEFAULT '0',
  `order_status` varchar(20) NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  PRIMARY KEY (`order_id`),
  KEY `order_date` (`order_date`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `39_repurchase_order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_repurchase_order`
--

LOCK TABLES `39_repurchase_order` WRITE;
/*!40000 ALTER TABLE `39_repurchase_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_repurchase_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_repurchase_order_details`
--

DROP TABLE IF EXISTS `39_repurchase_order_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_repurchase_order_details` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `amount` double NOT NULL,
  `product_pv` double NOT NULL DEFAULT '0',
  `order_status` varchar(50) NOT NULL,
  `prod_id` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `39_repurchase_order_details_ibfk_15` FOREIGN KEY (`order_id`) REFERENCES `39_repurchase_order` (`order_id`),
  CONSTRAINT `39_repurchase_order_details_ibfk_16` FOREIGN KEY (`product_id`) REFERENCES `39_package` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_repurchase_order_details`
--

LOCK TABLES `39_repurchase_order_details` WRITE;
/*!40000 ALTER TABLE `39_repurchase_order_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_repurchase_order_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_roi_order`
--

DROP TABLE IF EXISTS `39_roi_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_roi_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prod_id` varchar(50) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `date_submission` datetime DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `pending_status` tinyint(1) NOT NULL,
  `roi` decimal(11,2) DEFAULT NULL,
  `days` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_roi_order`
--

LOCK TABLES `39_roi_order` WRITE;
/*!40000 ALTER TABLE `39_roi_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_roi_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_sales_commissions`
--

DROP TABLE IF EXISTS `39_sales_commissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_sales_commissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` int(12) NOT NULL,
  `pck_id` varchar(10) CHARACTER SET utf8 NOT NULL,
  `sales` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_sales_commissions`
--

LOCK TABLES `39_sales_commissions` WRITE;
/*!40000 ALTER TABLE `39_sales_commissions` DISABLE KEYS */;
INSERT INTO `39_sales_commissions` VALUES (1,1,'pck1',3),(3,1,'pck2',3),(5,1,'pck3',3),(7,2,'pck1',2),(9,2,'pck2',2),(11,2,'pck3',2),(13,3,'pck1',1),(15,3,'pck2',1),(17,3,'pck3',1),(18,1,'pck1',3),(19,1,'pck2',3),(20,1,'pck3',3),(21,2,'pck1',2),(22,2,'pck2',2),(23,2,'pck3',2),(24,3,'pck1',1),(25,3,'pck2',1),(26,3,'pck3',1);
/*!40000 ALTER TABLE `39_sales_commissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_sales_level_commision`
--

DROP TABLE IF EXISTS `39_sales_level_commision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_sales_level_commision` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `level_no` int(12) NOT NULL DEFAULT '0',
  `level_percentage` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_sales_level_commision`
--

LOCK TABLES `39_sales_level_commision` WRITE;
/*!40000 ALTER TABLE `39_sales_level_commision` DISABLE KEYS */;
INSERT INTO `39_sales_level_commision` VALUES (1,1,3),(3,2,2),(5,3,1),(6,1,3),(7,2,2),(8,3,1);
/*!40000 ALTER TABLE `39_sales_level_commision` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_sales_order`
--

DROP TABLE IF EXISTS `39_sales_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_sales_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_no` int(11) NOT NULL DEFAULT '0',
  `prod_id` varchar(50) NOT NULL DEFAULT '',
  `user_id` int(11) unsigned DEFAULT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  `product_pv` double NOT NULL DEFAULT '0',
  `date_submission` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `payment_method` varchar(50) CHARACTER SET utf8 NOT NULL,
  `pending_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prod_id` (`prod_id`),
  KEY `date_submission` (`date_submission`),
  KEY `user_id` (`user_id`),
  KEY `pending_id` (`pending_id`),
  CONSTRAINT `39_sales_order_ibfk_15` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_sales_order_ibfk_16` FOREIGN KEY (`pending_id`) REFERENCES `39_pending_registration` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_sales_order`
--

LOCK TABLES `39_sales_order` WRITE;
/*!40000 ALTER TABLE `39_sales_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_sales_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_sales_rank_commissions`
--

DROP TABLE IF EXISTS `39_sales_rank_commissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_sales_rank_commissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` int(12) NOT NULL,
  `rank_id` varchar(10) CHARACTER SET utf8 NOT NULL,
  `sales` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_sales_rank_commissions`
--

LOCK TABLES `39_sales_rank_commissions` WRITE;
/*!40000 ALTER TABLE `39_sales_rank_commissions` DISABLE KEYS */;
INSERT INTO `39_sales_rank_commissions` VALUES (1,1,'1',3),(3,1,'2',3),(5,1,'3',3),(7,1,'4',3),(9,2,'1',2),(11,2,'2',2),(13,2,'3',2),(15,2,'4',2),(17,3,'1',1),(19,3,'2',1),(21,3,'3',1),(23,3,'4',1),(24,1,'1',3),(25,1,'2',3),(26,1,'3',3),(27,1,'4',3),(28,2,'1',2),(29,2,'2',2),(30,2,'3',2),(31,2,'4',2),(32,3,'1',1),(33,3,'2',1),(34,3,'3',1),(35,3,'4',1);
/*!40000 ALTER TABLE `39_sales_rank_commissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_signup_fields`
--

DROP TABLE IF EXISTS `39_signup_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_signup_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_name` varchar(250) NOT NULL,
  `status` varchar(250) NOT NULL,
  `required` varchar(250) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `delete_status` varchar(50) NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_signup_fields`
--

LOCK TABLES `39_signup_fields` WRITE;
/*!40000 ALTER TABLE `39_signup_fields` DISABLE KEYS */;
INSERT INTO `39_signup_fields` VALUES (1,'first_name','yes','yes',1,'yes'),(2,'last_name','yes','no',2,'yes'),(3,'date_of_birth','yes','yes',3,'yes'),(4,'gender','no','no',4,'yes'),(5,'adress_line1','no','no',5,'yes'),(6,'adress_line2','no','no',6,'yes'),(7,'country','no','no',7,'yes'),(8,'state','no','no',8,'yes'),(9,'city','no','no',9,'yes'),(10,'pin','no','no',10,'yes'),(11,'email','yes','yes',11,'yes'),(12,'mobile','yes','yes',12,'yes'),(13,'land_line','no','no',13,'yes');
/*!40000 ALTER TABLE `39_signup_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_signup_settings`
--

DROP TABLE IF EXISTS `39_signup_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_signup_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registration_allowed` varchar(5) NOT NULL DEFAULT 'yes',
  `sponsor_required` varchar(5) NOT NULL DEFAULT 'yes',
  `mail_notification` varchar(5) NOT NULL DEFAULT 'no',
  `binary_leg` varchar(10) NOT NULL DEFAULT 'any',
  `age_limit` int(11) NOT NULL DEFAULT '18',
  `bank_info_required` varchar(5) NOT NULL DEFAULT 'yes',
  `compression_commission` varchar(30) NOT NULL DEFAULT 'no',
  `default_country` int(11) NOT NULL DEFAULT '99',
  `email_verification` varchar(60) NOT NULL DEFAULT 'no',
  `login_unapproved` varchar(60) NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_signup_settings`
--

LOCK TABLES `39_signup_settings` WRITE;
/*!40000 ALTER TABLE `39_signup_settings` DISABLE KEYS */;
INSERT INTO `39_signup_settings` VALUES (1,'yes','yes','no','any',18,'yes','no',184,'no','no');
/*!40000 ALTER TABLE `39_signup_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_site_information`
--

DROP TABLE IF EXISTS `39_site_information`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_site_information` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(250) CHARACTER SET utf8 NOT NULL,
  `logo` varchar(100) CHARACTER SET utf8 NOT NULL,
  `email` varchar(150) CHARACTER SET utf8 NOT NULL,
  `phone` varchar(25) CHARACTER SET utf8 NOT NULL,
  `favicon` varchar(200) CHARACTER SET utf8 NOT NULL,
  `company_address` longtext,
  `default_lang` int(11) NOT NULL DEFAULT '1',
  `admin_theme_folder` varchar(30) CHARACTER SET utf8 NOT NULL,
  `user_theme_folder` varchar(30) CHARACTER SET utf8 NOT NULL,
  `fb_link` longtext NOT NULL,
  `twitter_link` longtext NOT NULL,
  `inst_link` longtext NOT NULL,
  `gplus_link` longtext NOT NULL,
  `fb_count` bigint(200) NOT NULL DEFAULT '0',
  `twitter_count` bigint(200) NOT NULL DEFAULT '0',
  `inst_count` bigint(200) NOT NULL DEFAULT '0',
  `gplus_count` bigint(200) NOT NULL DEFAULT '0',
  `login_logo` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT 'logo_login.png',
  `logo_shrink` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT 'logo_icon.png',
  `logo-shrink` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT 'logo_icon.png',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_site_information`
--

LOCK TABLES `39_site_information` WRITE;
/*!40000 ALTER TABLE `39_site_information` DISABLE KEYS */;
INSERT INTO `39_site_information` VALUES (1,'Company Name','logo_default.png','companyname@emil.com','9999999999','favicon.ico','Company address',1,'default','default','','','','',0,0,0,0,'logo_login.png','logo_icon.png','logo_icon.png');
/*!40000 ALTER TABLE `39_site_information` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_site_maintenance`
--

DROP TABLE IF EXISTS `39_site_maintenance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_site_maintenance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL DEFAULT '0',
  `title` varchar(250) NOT NULL DEFAULT 'Site is Under Maintenance',
  `description` longtext NOT NULL,
  `date_of_availability` date DEFAULT NULL,
  `block_login` int(11) NOT NULL DEFAULT '0',
  `block_register` int(11) NOT NULL DEFAULT '0',
  `block_ecommerce` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_site_maintenance`
--

LOCK TABLES `39_site_maintenance` WRITE;
/*!40000 ALTER TABLE `39_site_maintenance` DISABLE KEYS */;
INSERT INTO `39_site_maintenance` VALUES (1,0,'Site is Under Maintenance','<p>Site is Under Maintenance&nbsp;</p>','2016-05-21',0,0,0);
/*!40000 ALTER TABLE `39_site_maintenance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_sms_contents`
--

DROP TABLE IF EXISTS `39_sms_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_sms_contents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sms_type_id` int(11) NOT NULL,
  `sms_content` text CHARACTER SET utf8 NOT NULL,
  `lang_id` int(11) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_sms_contents`
--

LOCK TABLES `39_sms_contents` WRITE;
/*!40000 ALTER TABLE `39_sms_contents` DISABLE KEYS */;
INSERT INTO `39_sms_contents` VALUES (1,1,'You have been registered successfully in {company_name}!',1,'2020-07-25 00:58:17'),(2,1,'¡Te has registrado correctamente en {company_name}!',2,'2020-07-25 00:58:17'),(3,1,'您已成功在 {company_name} 中注册！',3,'2020-07-25 00:58:17'),(4,1,'Sie wurden erfolgreich in {company_name} registriert!',4,'2020-07-25 00:58:17'),(5,1,'Você foi registrado com sucesso em {company_name}!',5,'2020-07-25 00:58:17'),(6,1,'Vous avez été enregistré avec succès dans {company_name}!',6,'2020-07-25 00:58:17'),(7,1,'Sei stato registrato con successo in {company_name}!',7,'2020-07-25 00:58:17'),(8,1,'{company_name} sitesine başarıyla kaydoldunuz!',8,'2020-07-25 00:58:17'),(9,1,'Zostałeś pomyślnie zarejestrowany w {company_name}!',9,'2020-07-25 00:58:17'),(10,1,'لقد تم تسجيلك بنجاح في {company_name}!',10,'2020-07-25 00:58:17'),(11,1,'Вы были успешно зарегистрированы в {company_name}!',11,'2020-07-25 00:58:17'),(12,2,'Dear {fullname}, Your payout has been released successfully',1,'2020-07-25 00:58:17'),(13,2,'Estimado {fullname}, Su pago ha sido liberado con éxito',2,'2020-07-25 00:58:17'),(14,2,'尊敬的 {fullname}，您的付款已成功释放',3,'2020-07-25 00:58:17'),(15,2,'Lieber {fullname}, Ihre Auszahlung wurde erfolgreich freigegeben',4,'2020-07-25 00:58:17'),(16,2,'Prezado {fullname}, seu pagamento foi liberado com sucesso',5,'2020-07-25 00:58:17'),(17,2,'Cher {fullname}, Votre paiement a été validé',6,'2020-07-25 00:58:17'),(18,2,'Gentile {fullname}, il tuo pagamento è stato rilasciato correttamente',7,'2020-07-25 00:58:17'),(19,2,'Sayın {fullname}, Ödemeniz başarıyla onaylandı',8,'2020-07-25 00:58:17'),(20,2,'Drogi {fullname}, Twoja wypłata została pomyślnie zwolniona',9,'2020-07-25 00:58:17'),(21,2,'عزيزي {fullname} ، لقد تم إصدار دفعتك بنجاح',10,'2020-07-25 00:58:17'),(22,2,'Уважаемый {fullname}, ваша выплата была успешно выпущена',11,'2020-07-25 00:58:17'),(23,3,'Dear {fullname}, Your password has been successfully changed, Your new password is {new_password}',1,'2020-07-25 00:58:17'),(24,3,'Estimado {fullname}, su contraseña se ha cambiado correctamente, su nueva contraseña es {new_password}',2,'2020-07-25 00:58:17'),(25,3,'尊敬的 {fullname}，您的密码已成功更改，您的新密码为 {new_password}',3,'2020-07-25 00:58:17'),(26,3,'Lieber {fullname}, Ihr Passwort wurde erfolgreich geändert. Ihr neues Passwort lautet {new_password}',4,'2020-07-25 00:58:17'),(27,3,'Prezado {fullname}, sua senha foi alterada com sucesso, sua nova senha é {new_password}',5,'2020-07-25 00:58:17'),(28,3,'Cher {fullname}, Votre mot de passe a été modifié avec succès, Votre nouveau mot de passe est {new_password}',6,'2020-07-25 00:58:17'),(29,3,'Gentile {fullname}, la tua password è stata cambiata correttamente, la tua nuova password è {new_password}',7,'2020-07-25 00:58:17'),(30,3,'Sayın {fullname}, Şifreniz başarıyla değiştirildi, Yeni şifreniz {new_password}',8,'2020-07-25 00:58:17'),(31,3,'Drogi {fullname}, Twoje hasło zostało pomyślnie zmienione, Twoje nowe hasło to {new_password}',9,'2020-07-25 00:58:17'),(32,3,'عزيزي {fullname} ، تم تغيير كلمة مرورك بنجاح ، كلمة مرورك الجديدة {new_password}',10,'2020-07-25 00:58:17'),(33,3,'Уважаемый {fullname}, Ваш пароль был успешно изменен, Ваш новый пароль {new_password}',11,'2020-07-25 00:58:17'),(34,4,'Dear {fullname}, Your new Transaction Password is {password}',1,'2020-07-25 00:58:17'),(35,4,'Estimado {fullname}, su nueva contraseña de transacción es {password}',2,'2020-07-25 00:58:17'),(36,4,'尊敬的 {fullname}，您的新交易密码为 {password}',3,'2020-07-25 00:58:17'),(37,4,'Lieber {fullname}, Ihr neues Transaktionskennwort lautet {password}',4,'2020-07-25 00:58:17'),(38,4,'Prezado {fullname}, sua nova senha de transação é {password}',5,'2020-07-25 00:58:17'),(39,4,'Cher {fullname}, votre nouveau mot de passe de transaction est {password}',6,'2020-07-25 00:58:17'),(40,4,'Gentile {fullname}, la tua nuova password di transazione è {password}',7,'2020-07-25 00:58:17'),(41,4,'Sayın {fullname}, Yeni İşlem Parolanız {password}',8,'2020-07-25 00:58:17'),(42,4,'Drogi {fullname}, nowe hasło do transakcji to {password}',9,'2020-07-25 00:58:17'),(43,4,'عزيزي {fullname} ، كلمة المرور الجديدة للمعاملات الخاصة بك هي {password}',10,'2020-07-25 00:58:17'),(44,4,'Уважаемый {fullname}, Ваш новый пароль для транзакции - {password}',11,'2020-07-25 00:58:17'),(45,5,'Dear {admin_user_name}, {username} requested payout of {payout_amount}',1,'2020-07-25 00:58:17'),(46,5,'Estimado {admin_user_name}, {username} solicitó el pago de {payout_amount}',2,'2020-07-25 00:58:17'),(47,5,'尊敬的 {admin_user_name}，{username} 请求支付 {payout_amount}',3,'2020-07-25 00:58:17'),(48,5,'Sehr geehrter {admin_user_name}, {username} hat die Auszahlung von {payout_amount} angefordert',4,'2020-07-25 00:58:17'),(49,5,'Prezado {admin_user_name}, {username} solicitou pagamento de {payout_amount}',5,'2020-07-25 00:58:17'),(50,5,'Cher {admin_user_name}, {username} a demandé le paiement de {payout_amount}',6,'2020-07-25 00:58:17'),(51,5,'Gentile {admin_user_name}, {username} ha richiesto il pagamento di {payout_amount}',7,'2020-07-25 00:58:17'),(52,5,'Sayın {admin_user_name}, {username}, {payout_amount} tutarında ödeme yapılmasını istedi',8,'2020-07-25 00:58:17'),(53,5,'Drogi {admin_user_name}, {username} zażądał wypłaty w wysokości {payout_amount}',9,'2020-07-25 00:58:17'),(54,5,'عزيزي {admin_user_name} ، {username} طلب دفع تعويضات {payout_amount}',10,'2020-07-25 00:58:17'),(55,5,'Уважаемый {admin_user_name}, {username} запросил выплату {payout_amount}',11,'2020-07-25 00:58:17');
/*!40000 ALTER TABLE `39_sms_contents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_sms_types`
--

DROP TABLE IF EXISTS `39_sms_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_sms_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sms_type` varchar(30) NOT NULL,
  `variables` text NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_sms_types`
--

LOCK TABLES `39_sms_types` WRITE;
/*!40000 ALTER TABLE `39_sms_types` DISABLE KEYS */;
INSERT INTO `39_sms_types` VALUES (1,'registration','fullname,company_name,link',1,'2020-07-25 00:58:17'),(2,'payout_release','fullname,company_name,amount',1,'2020-07-25 00:58:17'),(3,'change_password','fullname,company_name,new_password',1,'2020-07-25 00:58:17'),(4,'change_transaction_password','fullname,company_name,password',1,'2020-07-25 00:58:17'),(5,'payout_request','fullname,company_name,admin_user_name,username,payout_amount',1,'2020-07-25 00:58:17');
/*!40000 ALTER TABLE `39_sms_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_sofort_configuration`
--

DROP TABLE IF EXISTS `39_sofort_configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_sofort_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` varchar(250) NOT NULL,
  `customer_id` varchar(250) NOT NULL,
  `project_pass` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_sofort_configuration`
--

LOCK TABLES `39_sofort_configuration` WRITE;
/*!40000 ALTER TABLE `39_sofort_configuration` DISABLE KEYS */;
INSERT INTO `39_sofort_configuration` VALUES (1,'11111','000000','admin123');
/*!40000 ALTER TABLE `39_sofort_configuration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_sofort_payment_history`
--

DROP TABLE IF EXISTS `39_sofort_payment_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_sofort_payment_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `purpose` varchar(250) NOT NULL,
  `amount` int(50) NOT NULL,
  `status` varchar(250) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `invoice_number` varchar(250) NOT NULL,
  `transaction_id` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_sofort_payment_history`
--

LOCK TABLES `39_sofort_payment_history` WRITE;
/*!40000 ALTER TABLE `39_sofort_payment_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_sofort_payment_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_sofort_payment_response`
--

DROP TABLE IF EXISTS `39_sofort_payment_response`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_sofort_payment_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registrar` int(11) unsigned NOT NULL,
  `regr_data` longtext NOT NULL,
  `reason` varchar(250) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_sofort_payment_response`
--

LOCK TABLES `39_sofort_payment_response` WRITE;
/*!40000 ALTER TABLE `39_sofort_payment_response` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_sofort_payment_response` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_sponsor_change_history`
--

DROP TABLE IF EXISTS `39_sponsor_change_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_sponsor_change_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `old_sponsor_id` int(11) unsigned NOT NULL,
  `new_sponsor_id` int(11) unsigned NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `old_sponsor_id` (`old_sponsor_id`),
  KEY `new_sponsor_id` (`new_sponsor_id`),
  CONSTRAINT `39_sponsor_change_history_ibfk_22` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_sponsor_change_history_ibfk_23` FOREIGN KEY (`old_sponsor_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_sponsor_change_history_ibfk_24` FOREIGN KEY (`new_sponsor_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_sponsor_change_history`
--

LOCK TABLES `39_sponsor_change_history` WRITE;
/*!40000 ALTER TABLE `39_sponsor_change_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_sponsor_change_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_sponsor_treepath`
--

DROP TABLE IF EXISTS `39_sponsor_treepath`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_sponsor_treepath` (
  `ancestor` int(10) unsigned NOT NULL,
  `descendant` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ancestor`,`descendant`),
  KEY `descendant` (`descendant`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_sponsor_treepath`
--

LOCK TABLES `39_sponsor_treepath` WRITE;
/*!40000 ALTER TABLE `39_sponsor_treepath` DISABLE KEYS */;
INSERT INTO `39_sponsor_treepath` VALUES (32,32);
/*!40000 ALTER TABLE `39_sponsor_treepath` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_squareup_config`
--

DROP TABLE IF EXISTS `39_squareup_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_squareup_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_token` varchar(250) NOT NULL,
  `location_id` varchar(250) NOT NULL,
  `application_id` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_squareup_config`
--

LOCK TABLES `39_squareup_config` WRITE;
/*!40000 ALTER TABLE `39_squareup_config` DISABLE KEYS */;
INSERT INTO `39_squareup_config` VALUES (1,'aaaaa','bbbbbbb','cccccccc');
/*!40000 ALTER TABLE `39_squareup_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_squareup_payment_history`
--

DROP TABLE IF EXISTS `39_squareup_payment_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_squareup_payment_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `name` varchar(250) NOT NULL,
  `purpose` varchar(250) NOT NULL,
  `amount` int(11) NOT NULL,
  `currency` varchar(250) NOT NULL,
  `idempotency_key` varchar(250) NOT NULL,
  `transaction_id` varchar(250) NOT NULL,
  `status` varchar(250) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_squareup_payment_history`
--

LOCK TABLES `39_squareup_payment_history` WRITE;
/*!40000 ALTER TABLE `39_squareup_payment_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_squareup_payment_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_squareup_payment_response`
--

DROP TABLE IF EXISTS `39_squareup_payment_response`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_squareup_payment_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registrar` int(11) unsigned NOT NULL,
  `regr_data` longtext NOT NULL,
  `reason` varchar(250) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_squareup_payment_response`
--

LOCK TABLES `39_squareup_payment_response` WRITE;
/*!40000 ALTER TABLE `39_squareup_payment_response` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_squareup_payment_response` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_subscription_config`
--

DROP TABLE IF EXISTS `39_subscription_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_subscription_config` (
  `based_on` varchar(30) NOT NULL,
  `reg_status` varchar(30) NOT NULL DEFAULT 'yes',
  `commission_status` varchar(30) NOT NULL DEFAULT 'yes',
  `payout_status` varchar(30) NOT NULL DEFAULT 'yes',
  `fixed_amount` int(11) DEFAULT '0',
  `subscription_period` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_subscription_config`
--

LOCK TABLES `39_subscription_config` WRITE;
/*!40000 ALTER TABLE `39_subscription_config` DISABLE KEYS */;
INSERT INTO `39_subscription_config` VALUES ('member_package','yes','yes','yes',0,0);
/*!40000 ALTER TABLE `39_subscription_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_terms_conditions`
--

DROP TABLE IF EXISTS `39_terms_conditions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_terms_conditions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `terms_conditions` text CHARACTER SET utf8,
  `lang_ref_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_terms_conditions`
--

LOCK TABLES `39_terms_conditions` WRITE;
/*!40000 ALTER TABLE `39_terms_conditions` DISABLE KEYS */;
INSERT INTO `39_terms_conditions` VALUES (1,'<p>All subscribers of SOFTWARE NAME services agree to be bound by the terms of this service. The SOFTWARE NAME software is an entire solution for all type of business plan like Binary, Matrix, Unilevel and many other compensation plans. This is developed by a leading MLM software development company COMPANY NAME. More over these we are keen to construct MLM software as per the business plan suggested by the clients.This MLM software is featured of with integrated with SMS, E-Wallet,Replicating Website,E-Pin,E-Commerce, Shopping Cart,Web Design and more.</p>',1),(2,'<p> Todos los suscriptores de los servicios de SOFTWARE NAME aceptan los términos de este servicio. El software SOFTWARE NAME es una solución completa para todo tipo de plan de negocios como Binary, Matrix, Unilevel y muchos otros planes de compensación. Esto es desarrollado por una empresa líder de desarrollo de software MLM NOMBRE DE LA COMPAÑÍA. Además, estamos interesados en construir un software MLM según el plan de negocios sugerido por los clientes. Este software MLM se presenta integrado con SMS, billetera electrónica, sitio web replicante, pin electrónico, comercio electrónico, carrito de compras, web Diseño y más. </p>',2),(3,'<p>“软件名称”服务的所有订阅者均同意受该服务条款的约束。 SOFTWARE NAME软件是针对所有类型的业务计划（例如Binary，Matrix，Unilevel和许多其他薪酬计划）的完整解决方案。 这是由领先的MLM软件开发公司COMPANY NAME开发的。 此外，我们热衷于根据客户建议的业务计划构建MLM软件。该MLM软件具有与SMS，E-Wallet，复制网站，E-Pin，电子商务，购物车，Web集成的功能。 设计等等。</ p>',3),(4,'<p> Alle Abonnenten von SOFTWARE NAME-Diensten stimmen zu, an die Bedingungen dieses Dienstes gebunden zu sein. Die SOFTWARE NAME-Software ist eine Komplettlösung für alle Arten von Geschäftsplänen wie Binary, Matrix, Unilevel und viele andere Vergütungspläne. Dies wird von einem führenden MLM-Softwareentwicklungsunternehmen FIRMENNAME entwickelt. Darüber hinaus sind wir bestrebt, MLM-Software gemäß dem von den Kunden vorgeschlagenen Geschäftsplan zu entwickeln. Diese MLM-Software ist mit SMS, E-Wallet, Website-Replikation, E-Pin, E-Commerce, Warenkorb und Web integriert Design und mehr. </ P>',4),(5,'<p> Todos os assinantes dos serviços SOFTWARE NAME concordam em ficar vinculados aos termos deste serviço. O software SOFTWARE NAME é uma solução completa para todo tipo de plano de negócios, como Binário, Matriz, Unilevel e muitos outros planos de remuneração. Isso é desenvolvido por uma empresa líder em desenvolvimento de software MLM NOME DA EMPRESA. Além disso, estamos empenhados em construir o software MLM de acordo com o plano de negócios sugerido pelos clientes. Design e muito mais. </p>',5),(7,'<p> Tutti gli abbonati ai servizi di NOME SOFTWARE accettano di essere vincolati dai termini di questo servizio. Il software NOME SOFTWARE è un\'intera soluzione per tutti i tipi di piani aziendali come Binary, Matrix, Unilevel e molti altri piani di compensazione. Questo è sviluppato da un\'azienda leader nello sviluppo di software MLM COMPANY NAME. Inoltre, desideriamo costruire software MLM secondo il piano aziendale suggerito dai clienti. Questo software MLM è integrato con SMS, E-Wallet, Sito Web di replica, E-Pin, E-Commerce, Carrello, Web Design e altro. </p>',7),(8,'<p> YAZILIM ADI servislerinin tüm aboneleri, bu servis şartlarına bağlı kalmayı kabul eder. YAZILIM ADI yazılımı, İkili, Matris, Unilevel ve diğer birçok tazminat planı gibi her türlü iş planı için eksiksiz bir çözümdür. Bu, önde gelen bir MLM yazılım geliştirme şirketi ŞİRKET ADI tarafından geliştirilmiştir. Bunların ötesinde, müşterilerimiz tarafından önerilen iş planına göre MLM yazılımı kurmak istiyoruz. Bu MLM yazılımı, SMS, E-Cüzdan, Web Sitesini Kopyalama, E-Pin, E-Ticaret, Alışveriş Sepeti, Web ile entegre özellikli Tasarım ve daha fazlası. </p>',8),(9,'<p> Wszyscy subskrybenci usług NAZWA OPROGRAMOWANIA zgadzają się na warunki tej usługi. Oprogramowanie SOFTWARE NAME to całe rozwiązanie dla wszystkich rodzajów biznesplanów, takich jak Binary, Matrix, Unilevel i wielu innych planów wynagrodzeń. Zostało to opracowane przez wiodącą firmę programistyczną MLM COMPANY NAME. Ponadto chcemy stworzyć oprogramowanie MLM zgodnie z biznesplanem zaproponowanym przez klientów. Oprogramowanie MLM jest zintegrowane z SMS, e-portfelem, replikacją strony internetowej, e-pinem, e-commerce, koszykiem, siecią Design i nie tylko. </p>',9),(10,'<p> يوافق جميع المشتركين في خدمات SOFTWARE NAME على الالتزام بشروط هذه الخدمة. يعد برنامج SOFTWARE NAME حلاً كاملاً لجميع أنواع خطط الأعمال مثل Binary و Matrix و Unilevel والعديد من خطط التعويض الأخرى. تم تطوير هذا من قبل شركة تطوير البرمجيات الامتيازات الرائدة. علاوة على ذلك ، نحن حريصون على إنشاء برنامج الامتيازات وفقًا لخطة العمل المقترحة من قبل العملاء. يتميز برنامج الامتيازات هذا بتكامل مع الرسائل النصية القصيرة ، المحفظة الإلكترونية ، موقع النسخ المتماثل ، البريد الإلكتروني ، التجارة الإلكترونية ، سلة التسوق ، الويب تصميم وأكثر من ذلك. </ p>',10),(11,'<p> Все подписчики услуг ИМЯ ПРОГРАММНОЕ ОБЕСПЕЧЕНИЕ соглашаются соблюдать условия этой услуги. Программное обеспечение ИМЯ ПРОГРАММНОЕ ОБЕСПЕЧЕНИЕ является полным решением для всех типов бизнес-планов, таких как Binary, Matrix, Unilevel и многих других компенсационных планов. Это разработано ведущей компанией по разработке программного обеспечения MLM COMPANY NAME. Более того, мы стремимся создавать программное обеспечение MLM в соответствии с бизнес-планом, предложенным клиентами. Это программное обеспечение MLM оснащено интегрированным с SMS, электронным кошельком, веб-сайтом для репликации, E-Pin, электронной коммерцией, корзиной покупок, Интернетом. Дизайн и многое другое. </ P>',11),(12,'\r\n<p> Все подписчики услуг Infinite MLM соглашаются соблюдать условия этой услуги. Программное обеспечение Infinite MLM представляет собой комплексное решение для всех видов бизнес-планов, таких как Binary, Matrix, Unilevel и многие другие планы вознаграждения. Это разработано ведущей компанией по разработке программного обеспечения MLM Infinite Open Source Solutions LLP. Более того, мы стремимся создавать программное обеспечение MLM в соответствии с бизнес-планом, предлагаемым клиентами. Это программное обеспечение MLM отличается интегрированным с SMS, E-Wallet, реплицируемым сайтом, E-Pin, электронной коммерцией, корзиной покупок, веб-сайтом Дизайн и многое другое. </ P>',12);
/*!40000 ALTER TABLE `39_terms_conditions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_theme_setting`
--

DROP TABLE IF EXISTS `39_theme_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_theme_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type` varchar(20) NOT NULL,
  `theme_id` int(11) NOT NULL,
  `navbar_header_color` varchar(20) NOT NULL,
  `navbar_collapse_color` varchar(20) NOT NULL,
  `aside_color` varchar(20) NOT NULL,
  `header_fixed` tinyint(1) NOT NULL,
  `aside_fixed` tinyint(1) NOT NULL,
  `aside_folded` tinyint(1) NOT NULL,
  `aside_dock` tinyint(1) NOT NULL,
  `container` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_theme_setting`
--

LOCK TABLES `39_theme_setting` WRITE;
/*!40000 ALTER TABLE `39_theme_setting` DISABLE KEYS */;
INSERT INTO `39_theme_setting` VALUES (1,'admin',13,'bg-black','bg-white-only','bg-black',1,1,0,0,0),(2,'user',13,'bg-black','bg-white-only','bg-black',1,1,0,0,0);
/*!40000 ALTER TABLE `39_theme_setting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_to_do_list`
--

DROP TABLE IF EXISTS `39_to_do_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_to_do_list` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `task` varchar(500) NOT NULL,
  `time` datetime NOT NULL,
  `status` varchar(100) NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`task_id`),
  KEY `time` (`time`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `39_to_do_list_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_to_do_list`
--

LOCK TABLES `39_to_do_list` WRITE;
/*!40000 ALTER TABLE `39_to_do_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_to_do_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_tooltip_config`
--

DROP TABLE IF EXISTS `39_tooltip_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_tooltip_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_name` varchar(100) NOT NULL,
  `label` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'yes',
  `view_status` varchar(50) NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_tooltip_config`
--

LOCK TABLES `39_tooltip_config` WRITE;
/*!40000 ALTER TABLE `39_tooltip_config` DISABLE KEYS */;
INSERT INTO `39_tooltip_config` VALUES (1,'user_detail_name','first_name','yes','yes'),(2,'date_of_joining','join_date','yes','yes'),(3,'left','left','yes','no'),(4,'right','right','yes','no'),(5,'left_carry','left_carry','yes','no'),(6,'right_carry','right_carry','yes','no'),(7,'personal_PV','personal_pv','yes','yes'),(8,'group_PV','gpv','yes','yes'),(9,'donation_level','donation_level','no','no'),(10,'rank_name','rank_status','yes','yes');
/*!40000 ALTER TABLE `39_tooltip_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_tran_password`
--

DROP TABLE IF EXISTS `39_tran_password`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_tran_password` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `tran_password` varchar(250) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `39_tran_password_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_tran_password`
--

LOCK TABLES `39_tran_password` WRITE;
/*!40000 ALTER TABLE `39_tran_password` DISABLE KEYS */;
INSERT INTO `39_tran_password` VALUES (1,32,'$2y$10$O21A7QX.lLeDyCAEuAI1B.YWJT879n0G6ScHmVl.EQPbz1gzCKOpO');
/*!40000 ALTER TABLE `39_tran_password` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_tran_password_reset_table`
--

DROP TABLE IF EXISTS `39_tran_password_reset_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_tran_password_reset_table` (
  `password_reset_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `keyword` bigint(20) NOT NULL,
  `reset_status` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`password_reset_id`),
  KEY `keyword` (`keyword`),
  KEY `reset_status` (`reset_status`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `39_tran_password_reset_table_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_tran_password_reset_table`
--

LOCK TABLES `39_tran_password_reset_table` WRITE;
/*!40000 ALTER TABLE `39_tran_password_reset_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_tran_password_reset_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_transaction_id`
--

DROP TABLE IF EXISTS `39_transaction_id`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_transaction_id` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_id` (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_transaction_id`
--

LOCK TABLES `39_transaction_id` WRITE;
/*!40000 ALTER TABLE `39_transaction_id` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_transaction_id` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_treepath`
--

DROP TABLE IF EXISTS `39_treepath`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_treepath` (
  `ancestor` int(10) unsigned NOT NULL,
  `descendant` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ancestor`,`descendant`),
  KEY `descendant` (`descendant`),
  CONSTRAINT `39_treepath_ibfk_1` FOREIGN KEY (`ancestor`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_treepath_ibfk_2` FOREIGN KEY (`descendant`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_treepath`
--

LOCK TABLES `39_treepath` WRITE;
/*!40000 ALTER TABLE `39_treepath` DISABLE KEYS */;
INSERT INTO `39_treepath` VALUES (32,32);
/*!40000 ALTER TABLE `39_treepath` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_upload_categorys`
--

DROP TABLE IF EXISTS `39_upload_categorys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_upload_categorys` (
  `c_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`c_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_upload_categorys`
--

LOCK TABLES `39_upload_categorys` WRITE;
/*!40000 ALTER TABLE `39_upload_categorys` DISABLE KEYS */;
INSERT INTO `39_upload_categorys` VALUES (1,'Documents'),(2,'Images'),(3,'Videos');
/*!40000 ALTER TABLE `39_upload_categorys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_user_activation_deactivation_history`
--

DROP TABLE IF EXISTS `39_user_activation_deactivation_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_user_activation_deactivation_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `type` varchar(10) CHARACTER SET utf8 NOT NULL,
  `status` varchar(15) CHARACTER SET utf8 NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `time` (`time`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `39_user_activation_deactivation_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_user_activation_deactivation_history`
--

LOCK TABLES `39_user_activation_deactivation_history` WRITE;
/*!40000 ALTER TABLE `39_user_activation_deactivation_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_user_activation_deactivation_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_user_balance_amount`
--

DROP TABLE IF EXISTS `39_user_balance_amount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_user_balance_amount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `balance_amount` double NOT NULL DEFAULT '0',
  `purchase_wallet` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `39_user_balance_amount_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_user_balance_amount`
--

LOCK TABLES `39_user_balance_amount` WRITE;
/*!40000 ALTER TABLE `39_user_balance_amount` DISABLE KEYS */;
INSERT INTO `39_user_balance_amount` VALUES (1,32,0,0);
/*!40000 ALTER TABLE `39_user_balance_amount` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_user_dashboard_items`
--

DROP TABLE IF EXISTS `39_user_dashboard_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_user_dashboard_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item` varchar(50) NOT NULL,
  `master_item` varchar(50) NOT NULL DEFAULT '' COMMENT 'only if the item is sub of another item',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_user_dashboard_items`
--

LOCK TABLES `39_user_dashboard_items` WRITE;
/*!40000 ALTER TABLE `39_user_dashboard_items` DISABLE KEYS */;
INSERT INTO `39_user_dashboard_items` VALUES (1,'commission_earned','',1),(2,'payout_released','',1),(3,'payout_pending','',1),(4,'total_sales','',1),(5,'ewallet','',1),(6,'member_joinings','',1),(7,'summary_or_promotions','',1),(8,'members_map','',1),(9,'rank_details','',1),(10,'earnings_nd_expenses','',1),(11,'earnings','earnings_nd_expenses',1),(12,'expenses','earnings_nd_expenses',1),(13,'payout_status','earnings_nd_expenses',1),(14,'team_perfomance','',1),(15,'top_earners','team_perfomance',1),(16,'top_recruiters','team_perfomance',1),(17,'package_overview','team_perfomance',1),(18,'rank_overview','team_perfomance',1),(19,'latest_news','',1),(23,'profile','',1),(24,'pv','',1),(25,'new_members','',1);
/*!40000 ALTER TABLE `39_user_dashboard_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_user_deletion_history`
--

DROP TABLE IF EXISTS `39_user_deletion_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_user_deletion_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `ewallet_balance` double NOT NULL,
  `tran_password` varchar(50) NOT NULL,
  `registration_details` longtext NOT NULL,
  `ft_details` longtext NOT NULL,
  `user_details` longtext NOT NULL,
  `leg_details` longtext NOT NULL,
  `customer_details` longtext NOT NULL,
  `customer_address` longtext NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `39_user_deletion_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `39_ft_individual` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_user_deletion_history`
--

LOCK TABLES `39_user_deletion_history` WRITE;
/*!40000 ALTER TABLE `39_user_deletion_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_user_deletion_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_user_details`
--

DROP TABLE IF EXISTS `39_user_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_user_details` (
  `user_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_detail_refid` int(11) unsigned NOT NULL,
  `user_details_ref_user_id` int(11) unsigned DEFAULT NULL,
  `user_detail_name` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `user_detail_second_name` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `user_detail_address` text CHARACTER SET utf8,
  `user_detail_address2` varchar(300) CHARACTER SET utf8 DEFAULT NULL,
  `user_detail_country` int(11) DEFAULT NULL,
  `user_detail_state` int(11) DEFAULT NULL,
  `user_detail_city` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `user_detail_pin` varchar(11) CHARACTER SET utf8 NOT NULL DEFAULT 'NA',
  `user_detail_mobile` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `user_detail_land` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `user_detail_email` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `user_detail_dob` date DEFAULT NULL,
  `user_detail_gender` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `bitcoin_address` varchar(255) DEFAULT NULL,
  `user_detail_acnumber` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `user_detail_ifsc` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `user_detail_nbank` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `user_detail_nacct_holder` varchar(100) NOT NULL DEFAULT 'NA',
  `user_detail_nbranch` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `user_detail_pan` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `join_date` datetime DEFAULT NULL,
  `user_photo` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT 'nophoto.jpg',
  `user_detail_facebook` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT 'http://facebook.com',
  `user_detail_twitter` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT 'http://twitter.com',
  `bank_info_required` varchar(5) NOT NULL DEFAULT 'yes',
  `user_detail_paypal` varchar(255) DEFAULT NULL,
  `user_detail_blockchain_wallet_id` varchar(255) DEFAULT NULL,
  `user_detail_bitgo_wallet_id` varchar(255) DEFAULT NULL,
  `upload_count` int(11) NOT NULL DEFAULT '0',
  `kyc_status` varchar(50) NOT NULL DEFAULT 'no',
  `payout_type` varchar(50) NOT NULL DEFAULT 'Bank Transfer',
  `user_banner` varchar(250) NOT NULL DEFAULT 'banner-tchnoly.jpg',
  `read_doc_count` int(11) NOT NULL DEFAULT '0',
  `read_news_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_detail_id`),
  KEY `user_detail_refid` (`user_detail_refid`),
  KEY `user_details_ref_user_id` (`user_details_ref_user_id`),
  KEY `user_detail_country` (`user_detail_country`),
  KEY `user_detail_state` (`user_detail_state`),
  CONSTRAINT `39_user_details_ibfk_1` FOREIGN KEY (`user_detail_refid`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_user_details_ibfk_2` FOREIGN KEY (`user_details_ref_user_id`) REFERENCES `39_ft_individual` (`id`),
  CONSTRAINT `39_user_details_ibfk_3` FOREIGN KEY (`user_detail_country`) REFERENCES `39_infinite_countries` (`country_id`),
  CONSTRAINT `39_user_details_ibfk_4` FOREIGN KEY (`user_detail_state`) REFERENCES `39_infinite_states` (`state_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_user_details`
--

LOCK TABLES `39_user_details` WRITE;
/*!40000 ALTER TABLE `39_user_details` DISABLE KEYS */;
INSERT INTO `39_user_details` VALUES (1,32,0,'Majed','','majed','majed',184,0,'NA','0','+966501611613','+966501611613','majed40@gmail.com',NULL,'M','NA','NA','NA','NA','NA','NA','NA','2021-05-26 17:45:53','nophoto.jpg','https://facebook.com','https://twitter.com','yes',NULL,NULL,NULL,0,'no','Bank Transfer','banner-tchnoly.jpg',0,0);
/*!40000 ALTER TABLE `39_user_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_user_forget_history`
--

DROP TABLE IF EXISTS `39_user_forget_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_user_forget_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ft_details` longtext NOT NULL,
  `user_details` longtext NOT NULL,
  `forget_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_user_forget_history`
--

LOCK TABLES `39_user_forget_history` WRITE;
/*!40000 ALTER TABLE `39_user_forget_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `39_user_forget_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `39_username_config`
--

DROP TABLE IF EXISTS `39_username_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `39_username_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `length` varchar(30) NOT NULL DEFAULT '17',
  `prefix_status` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT 'yes',
  `prefix` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT 'INFINITE',
  `user_name_type` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'dynamic',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `39_username_config`
--

LOCK TABLES `39_username_config` WRITE;
/*!40000 ALTER TABLE `39_username_config` DISABLE KEYS */;
INSERT INTO `39_username_config` VALUES (1,'6,20','no','','static');
/*!40000 ALTER TABLE `39_username_config` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-05-26 17:47:26

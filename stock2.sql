CREATE DATABASE /*!32312 IF NOT EXISTS*/ `ktdb2` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `ktdb2`;

DROP TABLE IF EXISTS `stock_config`;
CREATE TABLE `stock_config` (
  `name` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL
);
INSERT INTO `stock_config` VALUES ('transaction','Sale'),('transaction','Purchase'),('transaction','Hold'),('trans_type','Cash'),('currency','Dhs'),('company_name','Khurshid Traders'),('hold_type','Main Branch'),('hold_type','Work Site'),('price_code','KHURSIDTAE');

DROP TABLE IF EXISTS `stock_itemgroups`;
CREATE TABLE `stock_itemgroups` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`group_id`)
);
INSERT INTO `stock_itemgroups` VALUES ('1','Ungrouped');

DROP TABLE IF EXISTS `stock_items`;
CREATE TABLE `stock_items` (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL DEFAULT '1',
  `item_name` varchar(50) DEFAULT NULL,
  `item_detail` text DEFAULT NULL,
  PRIMARY KEY (`item_id`)
);

DROP TABLE IF EXISTS `stock_transactions`;
CREATE TABLE `stock_transactions` (
  `trans_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `item_id` int(10) unsigned NOT NULL,
  `transaction` varchar(20) DEFAULT NULL,
  `trans_type` varchar(20) DEFAULT NULL,
  `invoice_no` varchar(20) DEFAULT NULL,
  `uprice` decimal(10,3) unsigned DEFAULT NULL,
  `qty` decimal(10,3) unsigned DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `timestamp` int(8) DEFAULT NULL,
  PRIMARY KEY (`trans_id`)
);

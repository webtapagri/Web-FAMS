-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.23 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for tap_fams_dev
CREATE DATABASE IF NOT EXISTS `tap_fams_dev` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `tap_fams_dev`;

-- Dumping structure for table tap_fams_dev.tr_disposal_temp
CREATE TABLE IF NOT EXISTS `tr_disposal_temp` (
  `ID` int(5) NOT NULL AUTO_INCREMENT,
  `KODE_ASSET_AMS` varchar(12) NOT NULL,
  `KODE_ASSET_SAP` varchar(12) NOT NULL,
  `NAMA_MATERIAL` varchar(100) NOT NULL,
  `BA_PEMILIK_ASSET` char(4) NOT NULL,
  `LOKASI_BA_CODE` char(4) NOT NULL,
  `LOKASI_BA_DESCRIPTION` varchar(75) NOT NULL,
  `NAMA_ASSET_1` varchar(50) NOT NULL,
  `JENIS_PENGAJUAN` tinyint(1) NOT NULL,
  `CREATED_BY` varchar(50) DEFAULT NULL,
  `CREATED_AT` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `CHECKLIST` int(16) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table tap_fams_dev.tr_disposal_temp: 0 rows
/*!40000 ALTER TABLE `tr_disposal_temp` DISABLE KEYS */;
/*!40000 ALTER TABLE `tr_disposal_temp` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

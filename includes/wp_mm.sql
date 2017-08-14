/*
 Target Server Type    : MySQL
 Target Server Version : 50635
 File Encoding         : utf-8

 Date: 08/14/2017 15:45:28 PM
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `wp_mm`
-- ----------------------------
DROP TABLE IF EXISTS `wp_mm`;
CREATE TABLE `wp_mm` (
  `WorldID` varchar(255) NOT NULL,
  `Zone_Name` varchar(255) DEFAULT NULL,
  `CntyID` varchar(255) DEFAULT NULL,
  `Cnty_Name` varchar(255) DEFAULT NULL,
  `Adm1ID` varchar(255) DEFAULT NULL,
  `Adm1_Name` varchar(255) DEFAULT NULL,
  `Adm2ID` varchar(255) DEFAULT NULL,
  `Adm2_Name` varchar(255) DEFAULT NULL,
  `Adm3ID` varchar(255) DEFAULT NULL,
  `Adm3_Name` varchar(255) DEFAULT NULL,
  `Adm4ID` varchar(255) DEFAULT NULL,
  `Adm4_Name` varchar(255) DEFAULT NULL,
  `World` varchar(255) DEFAULT NULL,
  `Population` float DEFAULT NULL,
  `Shape_Leng` float DEFAULT NULL,
  `Cen_x` float DEFAULT NULL,
  `Cen_y` float DEFAULT NULL,
  `Region` varchar(50) DEFAULT NULL,
  `Field` varchar(50) DEFAULT NULL,
  `geometry` longtext,
  `OBJECTID_1` int(11) DEFAULT NULL,
  `OBJECTID` int(11) DEFAULT NULL,
  `Notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`WorldID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

SET FOREIGN_KEY_CHECKS = 1;

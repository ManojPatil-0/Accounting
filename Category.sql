CREATE TABLE `accounting`.`category` ( `CATID` TINYINT NOT NULL , `CATNAME` VARCHAR(25) NOT NULL , `ACTIVE` CHAR(1) NOT NULL DEFAULT 'Y' , `UID` INT(3) NOT NULL , PRIMARY KEY (`CATID`)) ENGINE = MyISAM;

ALTER TABLE `transactions` ADD `CATID` TINYINT NOT NULL AFTER `CRDR`;
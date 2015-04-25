SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

SHOW WARNINGS;
DROP SCHEMA IF EXISTS `kostis_SoftEngin` ;
CREATE SCHEMA IF NOT EXISTS `kostis_SoftEngin` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
SHOW WARNINGS;
USE `kostis_SoftEngin` ;

-- -----------------------------------------------------
-- Table `kostis_SoftEngin`.`Users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `kostis_SoftEngin`.`Users` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `kostis_SoftEngin`.`Users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `password` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `fullname` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `vat` INT(11) NOT NULL ,
  `phone_number` VARCHAR(25) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `email` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `type` ENUM('admin','manager','seller','storekeeper') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT 'seller' ,
  `status` ENUM('active','deleted') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'active' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `username_UNIQUE` (`username` ASC) ,
  UNIQUE INDEX `email` (`email` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `kostis_SoftEngin`.`ConfirmationLink`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `kostis_SoftEngin`.`ConfirmationLink` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `kostis_SoftEngin`.`ConfirmationLink` (
  `url` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `email` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `creation_date` DATETIME NOT NULL ,
  PRIMARY KEY (`url`) ,
  INDEX `fk_confirmationLink_Users` (`email` ASC) ,
  CONSTRAINT `fk_confirmationLink_Users`
    FOREIGN KEY (`email` )
    REFERENCES `kostis_SoftEngin`.`Users` (`email` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `kostis_SoftEngin`.`Customers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `kostis_SoftEngin`.`Customers` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `kostis_SoftEngin`.`Customers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `fullname` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `vat` INT(11) NOT NULL ,
  `location` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `phone_number` VARCHAR(25) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `email` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `status` ENUM('active','deleted') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'active' ,
  `inserted_by` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `fullname_UNIQUE` (`fullname` ASC) ,
  INDEX `fk_Customers_Users1` (`inserted_by` ASC) ,
  CONSTRAINT `fk_Customers_Users1`
    FOREIGN KEY (`inserted_by` )
    REFERENCES `kostis_SoftEngin`.`Users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `kostis_SoftEngin`.`CustomerOrder`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `kostis_SoftEngin`.`CustomerOrder` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `kostis_SoftEngin`.`CustomerOrder` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `seller` INT(11) NOT NULL ,
  `customer` INT(11) NOT NULL ,
  `order_date` DATE NOT NULL ,
  `expected_date` DATE NULL DEFAULT NULL ,
  `receipt_date` DATE NULL DEFAULT NULL ,
  `comments` TEXT NULL DEFAULT NULL ,
  `state` ENUM('completed','incompleted', 'modified', 'cancelled') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'incompleted' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_SupplierOrder_Users1` (`seller` ASC) ,
  INDEX `fk_SupplierOrder_Customers1` (`customer` ASC) ,
  CONSTRAINT `fk_SupplierOrder_Customers1`
    FOREIGN KEY (`customer` )
    REFERENCES `kostis_SoftEngin`.`Customers` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_SupplierOrder_Users1`
    FOREIGN KEY (`seller` )
    REFERENCES `kostis_SoftEngin`.`Users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `kostis_SoftEngin`.`Suppliers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `kostis_SoftEngin`.`Suppliers` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `kostis_SoftEngin`.`Suppliers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `fullname` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `vat` INT(11) NOT NULL ,
  `location` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `phone_number` VARCHAR(25) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `email` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `status` ENUM('active','deleted') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'active' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `fullname_UNIQUE` (`fullname` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `kostis_SoftEngin`.`Products`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `kostis_SoftEngin`.`Products` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `kostis_SoftEngin`.`Products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `description` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `metric_units` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `market_value` DOUBLE NOT NULL ,
  `sell_value` DOUBLE NULL DEFAULT NULL ,
  `total_quantity` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
  `available_quantity` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
  `status` ENUM('active','deleted') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'active' ,
  `supplied_by` INT(11) NOT NULL ,
  `reservedOrder_quantity` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
  `limit` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) ,
  INDEX `fk_Products_Suppliers1` (`supplied_by` ASC) ,
  CONSTRAINT `fk_Products_Suppliers1`
    FOREIGN KEY (`supplied_by` )
    REFERENCES `kostis_SoftEngin`.`Suppliers` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `kostis_SoftEngin`.`CustomerOrderDetail`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `kostis_SoftEngin`.`CustomerOrderDetail` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `kostis_SoftEngin`.`CustomerOrderDetail` (
  `order` INT(11) NOT NULL ,
  `product` INT(11) NOT NULL ,
  `quantity` INT(11) NOT NULL ,
  `modified_quantity` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `deliverable_quantity` INT(11) UNSIGNED NULL DEFAULT NULL ,
  `reservedQuantity` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
  `market_value` DOUBLE NOT NULL DEFAULT '0' ,
  `sell_value` DOUBLE NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`order`, `product`) ,
  INDEX `fk_SupplyOrderDetail_SupplyOrder1` (`order` ASC) ,
  INDEX `fk_SupplyOrderDetail_Products1` (`product` ASC) ,
  CONSTRAINT `fk_SupplyOrderDetail_Products1`
    FOREIGN KEY (`product` )
    REFERENCES `kostis_SoftEngin`.`Products` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_SupplyOrderDetail_SupplyOrder1`
    FOREIGN KEY (`order` )
    REFERENCES `kostis_SoftEngin`.`CustomerOrder` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `kostis_SoftEngin`.`Discounts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `kostis_SoftEngin`.`Discounts` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `kostis_SoftEngin`.`Discounts` (
  `product` INT(11) NOT NULL ,
  `customer` INT(11) NOT NULL ,
  `discount` DOUBLE NOT NULL ,
  PRIMARY KEY (`product`, `customer`) ,
  INDEX `fk_Products_has_Customers_Customers1` (`customer` ASC) ,
  INDEX `fk_Products_has_Customers_Products1` (`product` ASC) ,
  CONSTRAINT `fk_Products_has_Customers_Customers1`
    FOREIGN KEY (`customer` )
    REFERENCES `kostis_SoftEngin`.`Customers` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Products_has_Customers_Products1`
    FOREIGN KEY (`product` )
    REFERENCES `kostis_SoftEngin`.`Products` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `kostis_SoftEngin`.`SupplyOrder`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `kostis_SoftEngin`.`SupplyOrder` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `kostis_SoftEngin`.`SupplyOrder` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `Suppliers_id` INT(11) NOT NULL ,
  `order_date` DATE NOT NULL ,
  `expected_date` DATE NULL DEFAULT NULL ,
  `receipt_date` DATE NULL DEFAULT NULL ,
  `state` ENUM('completed','incompleted') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'incompleted' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_SupplyOrder_Suppliers1` (`Suppliers_id` ASC) ,
  CONSTRAINT `fk_SupplyOrder_Suppliers1`
    FOREIGN KEY (`Suppliers_id` )
    REFERENCES `kostis_SoftEngin`.`Suppliers` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `kostis_SoftEngin`.`ExceptionalMessage`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `kostis_SoftEngin`.`ExceptionalMessage` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `kostis_SoftEngin`.`ExceptionalMessage` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `supplyOrder` INT(11) NULL DEFAULT NULL ,
  `customerOrder` INT(11) NULL DEFAULT NULL ,
  `product` INT(11) NOT NULL ,
  `quantity` INT(11) NOT NULL ,
  `type` ENUM('missing','abounding') NULL DEFAULT NULL ,
  `date` DATE NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_exceptionalMessage_product` (`product` ASC) ,
  INDEX `fk_exceptionalMessage_customerOrder` (`customerOrder` ASC) ,
  INDEX `fk_exceptionalMessage_supplyOrder` (`supplyOrder` ASC) ,
  CONSTRAINT `fk_exceptionalMessage_product`
    FOREIGN KEY (`product` )
    REFERENCES `kostis_SoftEngin`.`Products` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exceptionalMessage_customerOrder`
    FOREIGN KEY (`customerOrder` )
    REFERENCES `kostis_SoftEngin`.`CustomerOrder` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exceptionalMessage_supplyOrder`
    FOREIGN KEY (`supplyOrder` )
    REFERENCES `kostis_SoftEngin`.`SupplyOrder` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `kostis_SoftEngin`.`SupplyOrderDetail`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `kostis_SoftEngin`.`SupplyOrderDetail` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `kostis_SoftEngin`.`SupplyOrderDetail` (
  `order` INT(11) NOT NULL ,
  `product` INT(11) NOT NULL ,
  `quantity` INT(11) UNSIGNED NOT NULL ,
  `receipt_quantity` INT(11) NULL DEFAULT NULL ,
  `price` DOUBLE NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`order`, `product`) ,
  INDEX `fk_SupplyOrder_has_Products_Products1` (`product` ASC) ,
  INDEX `fk_SupplyOrder_has_Products_SupplyOrder1` (`order` ASC) ,
  CONSTRAINT `fk_SupplyOrder_has_Products_Products1`
    FOREIGN KEY (`product` )
    REFERENCES `kostis_SoftEngin`.`Products` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_SupplyOrder_has_Products_SupplyOrder1`
    FOREIGN KEY (`order` )
    REFERENCES `kostis_SoftEngin`.`SupplyOrder` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `kostis_SoftEngin`.`Wishlist`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `kostis_SoftEngin`.`Wishlist` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `kostis_SoftEngin`.`Wishlist` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `order` INT(11) NOT NULL ,
  `product` INT(11) NOT NULL ,
  `quantity` INT(11) UNSIGNED NOT NULL ,
  `date` DATE NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_wishlist_customerOrder` (`order` ASC) ,
  INDEX `fk_wishlist_products` (`product` ASC) ,
  CONSTRAINT `fk_wishlist_customerOrder`
    FOREIGN KEY (`order` )
    REFERENCES `kostis_SoftEngin`.`CustomerOrder` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_wishlist_products`
    FOREIGN KEY (`product` )
    REFERENCES `kostis_SoftEngin`.`Products` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

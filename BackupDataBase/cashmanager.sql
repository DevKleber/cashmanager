
USE `cashmanager` ;



CREATE TABLE IF NOT EXISTS `cashmanager`.`user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` TEXT NULL,
  `email` TEXT NULL,
  `password` TEXT NULL,
  `email_verified_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
;




CREATE TABLE IF NOT EXISTS `cashmanager`.`transaction` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `value` DECIMAL(10,2) NULL,
  `date` DATE NULL,
  `description` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `name` TEXT NULL,
  `is_income` TINYINT NULL,
  `id_user` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_transaction_user1_idx` (`id_user` ASC) ,
  CONSTRAINT `fk_transaction_user1`
    FOREIGN KEY (`id_user`)
    REFERENCES `cashmanager`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;




CREATE TABLE IF NOT EXISTS `cashmanager`.`category` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_category_parent` INT NULL,
  `name` TEXT NULL,
  `is_active` TINYINT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `icon` TEXT NULL,
  `is_income` TINYINT NULL,
  `id_user` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_category_user1_idx` (`id_user` ASC) ,
  CONSTRAINT `fk_category_user1`
    FOREIGN KEY (`id_user`)
    REFERENCES `cashmanager`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;




CREATE TABLE IF NOT EXISTS `cashmanager`.`planned_expenses` (
  `id_category` INT NOT NULL,
  `value_percent` INT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id_category`),
  INDEX `fk_planned_expenses_category_expenses_idx` (`id_category` ASC) ,
  CONSTRAINT `fk_planned_expenses_category_expenses`
    FOREIGN KEY (`id_category`)
    REFERENCES `cashmanager`.`category` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;




CREATE TABLE IF NOT EXISTS `cashmanager`.`credit_card` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` TEXT NULL,
  `closing_day` TEXT NULL,
  `due_day` TEXT NULL,
  `id_user` INT NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_credit_card_user1_idx` (`id_user` ASC) ,
  CONSTRAINT `fk_credit_card_user1`
    FOREIGN KEY (`id_user`)
    REFERENCES `cashmanager`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;




CREATE TABLE IF NOT EXISTS `cashmanager`.`expense_credit_card` (
  `id_transaction` INT NOT NULL,
  `id_credit_card` INT NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  INDEX `fk_expense_credit_card_transaction1_idx` (`id_transaction` ASC) ,
  INDEX `fk_expense_credit_card_credit_card1_idx` (`id_credit_card` ASC) ,
  CONSTRAINT `fk_expense_credit_card_transaction1`
    FOREIGN KEY (`id_transaction`)
    REFERENCES `cashmanager`.`transaction` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_expense_credit_card_credit_card1`
    FOREIGN KEY (`id_credit_card`)
    REFERENCES `cashmanager`.`credit_card` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;




CREATE TABLE IF NOT EXISTS `cashmanager`.`banking_institution` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` TEXT NULL,
  `logo` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
;




CREATE TABLE IF NOT EXISTS `cashmanager`.`account` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `description` TEXT NULL,
  `id_banking` INT NOT NULL,
  `current_balance` DECIMAL(10,2) NULL,
  `id_user` INT NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_account_banking_institution1_idx` (`id_banking` ASC) ,
  INDEX `fk_account_user1_idx` (`id_user` ASC) ,
  CONSTRAINT `fk_account_banking_institution1`
    FOREIGN KEY (`id_banking`)
    REFERENCES `cashmanager`.`banking_institution` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_account_user1`
    FOREIGN KEY (`id_user`)
    REFERENCES `cashmanager`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;




CREATE TABLE IF NOT EXISTS `cashmanager`.`expense_account` (
  `transaction_id` INT NOT NULL,
  `account_id` INT NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  INDEX `fk_expense_account_transaction1_idx` (`transaction_id` ASC) ,
  INDEX `fk_expense_account_account1_idx` (`account_id` ASC) ,
  CONSTRAINT `fk_expense_account_transaction1`
    FOREIGN KEY (`transaction_id`)
    REFERENCES `cashmanager`.`transaction` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_expense_account_account1`
    FOREIGN KEY (`account_id`)
    REFERENCES `cashmanager`.`account` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;




CREATE TABLE IF NOT EXISTS `cashmanager`.`transaction_item` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `is_paid` TINYINT NULL,
  `due_date` DATE NULL,
  `value` DECIMAL(10,2) NULL,
  `currenct_installment` INT NULL,
  `installment` INT NULL,
  `id_transaction` INT NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_transaction_item_transaction1_idx` (`id_transaction` ASC) ,
  CONSTRAINT `fk_transaction_item_transaction1`
    FOREIGN KEY (`id_transaction`)
    REFERENCES `cashmanager`.`transaction` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;

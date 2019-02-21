ALTER TABLE `protokol_export`
  ADD COLUMN `visible` INT(1) NULL DEFAULT NULL AFTER `date_update`,
  ADD COLUMN `type` INT(1) NULL DEFAULT NULL AFTER `visible`;
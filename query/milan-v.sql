-- ALTER TABLE `stock_transaction` ADD INDEX(`batch_no`);
-- ALTER TABLE `packing_master` ADD INDEX(`trans_number`);

-- 28-10-2025 --
-- ALTER TABLE `material_master` ADD `density` FLOAT NOT NULL DEFAULT '0' AFTER `scrap_per`;
-- ALTER TABLE `product_costing` ADD `density` FLOAT NOT NULL DEFAULT '0' AFTER `active_revision`, ADD `shape` VARCHAR(20) NULL DEFAULT 'round_dia' AFTER `density`, ADD `field1` FLOAT NOT NULL DEFAULT '0' AFTER `shape`, ADD `field2` FLOAT NOT NULL DEFAULT '0' AFTER `field1`;
-- ALTER TABLE `product_costing` ADD `field3` FLOAT NOT NULL DEFAULT '0' AFTER `field2`;

-- 01-11-2025 --
ALTER TABLE `product_costing` ADD `total_gross_wt` DOUBLE NOT NULL DEFAULT '0' COMMENT 'Total Gross Weight Piece' AFTER `gross_wt`;

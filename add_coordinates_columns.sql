-- Add latitude and longitude columns to eq2_projects table
-- Run this SQL in your database to enable coordinates functionality

ALTER TABLE `eq2_projects` 
ADD COLUMN `latitude` DECIMAL(10, 8) NULL DEFAULT NULL AFTER `financial_year`,
ADD COLUMN `longitude` DECIMAL(11, 8) NULL DEFAULT NULL AFTER `latitude`;

-- Add index for faster location-based queries
CREATE INDEX `idx_location` ON `eq2_projects` (`latitude`, `longitude`);


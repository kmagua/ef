-- Fix user_otp table: Add AUTO_INCREMENT to id column
ALTER TABLE `user_otp` 
MODIFY COLUMN `id` int(11) NOT NULL AUTO_INCREMENT;


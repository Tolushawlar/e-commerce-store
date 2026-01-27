-- Fix subscription_plan ENUM values to match frontend
-- Run this in phpMyAdmin or MySQL CLI

ALTER TABLE `clients` 
MODIFY COLUMN `subscription_plan` ENUM('basic', 'standard', 'premium') DEFAULT 'basic';

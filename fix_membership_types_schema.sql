-- Fix membership_types table to make progression fields nullable
-- Run these commands in your MySQL database:

ALTER TABLE membership_types MODIFY COLUMN birthday_discount_rate DECIMAL(5,2) NULL;
ALTER TABLE membership_types MODIFY COLUMN consecutive_visits_for_bonus INT NULL;
ALTER TABLE membership_types MODIFY COLUMN consecutive_visit_bonus_rate DECIMAL(5,2) NULL;

-- Verify the changes:
DESCRIBE membership_types;

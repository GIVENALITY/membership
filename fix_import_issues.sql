-- Fix import issues for member import
-- Run these commands in your MySQL database:

-- 1. Make birth_date nullable in members table
ALTER TABLE members MODIFY COLUMN birth_date DATE NULL;

-- 2. Make join_date nullable as well (in case it's missing)
ALTER TABLE members MODIFY COLUMN join_date DATE NULL;

-- Verify the changes:
DESCRIBE members;

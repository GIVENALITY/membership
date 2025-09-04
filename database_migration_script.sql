-- Database Migration Script for members.co.tz
-- Update any existing domain references to use members.co.tz

-- Update system_settings if they contain domain references
UPDATE system_settings 
SET value = REPLACE(value, 'membership.kinara.co.tz', 'members.co.tz')
WHERE value LIKE '%membership.kinara.co.tz%';

-- Update email_logs content if they contain domain references
UPDATE email_logs 
SET content = REPLACE(content, 'membership.kinara.co.tz', 'members.co.tz')
WHERE content LIKE '%membership.kinara.co.tz%';

-- Update hotels table email_logo_url if it contains domain references
UPDATE hotels 
SET email_logo_url = REPLACE(email_logo_url, 'membership.kinara.co.tz', 'members.co.tz')
WHERE email_logo_url LIKE '%membership.kinara.co.tz%';

-- Update any other tables that might contain domain references
-- (Add more tables as needed based on your specific schema)

-- Verify the changes
SELECT 'System settings updated:' as table_name, COUNT(*) as count 
FROM system_settings 
WHERE value LIKE '%members.co.tz%'
UNION ALL
SELECT 'Email logs updated:' as table_name, COUNT(*) as count 
FROM email_logs 
WHERE content LIKE '%members.co.tz%'
UNION ALL
SELECT 'Hotels email_logo_url updated:' as table_name, COUNT(*) as count 
FROM hotels 
WHERE email_logo_url LIKE '%members.co.tz%';

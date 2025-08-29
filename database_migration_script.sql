-- Database Migration Script for members.co.tz
-- Update any existing domain references to use members.co.tz

-- Update hotel domains if they exist
UPDATE hotels 
SET domain = REPLACE(domain, 'membership.kinara.co.tz', 'members.co.tz')
WHERE domain LIKE '%membership.kinara.co.tz%';

-- Update system settings if they contain domain references
UPDATE system_settings 
SET value = REPLACE(value, 'membership.kinara.co.tz', 'members.co.tz')
WHERE value LIKE '%membership.kinara.co.tz%';

-- Update any email templates that might contain the old domain
UPDATE email_notifications 
SET subject = REPLACE(subject, 'membership.kinara.co.tz', 'members.co.tz')
WHERE subject LIKE '%membership.kinara.co.tz%';

UPDATE email_notifications 
SET body = REPLACE(body, 'membership.kinara.co.tz', 'members.co.tz')
WHERE body LIKE '%membership.kinara.co.tz%';

-- Update any other tables that might contain domain references
-- (Add more tables as needed based on your specific schema)

-- Verify the changes
SELECT 'Hotels updated:' as table_name, COUNT(*) as count 
FROM hotels 
WHERE domain LIKE '%members.co.tz%'
UNION ALL
SELECT 'System settings updated:' as table_name, COUNT(*) as count 
FROM system_settings 
WHERE value LIKE '%members.co.tz%'
UNION ALL
SELECT 'Email notifications updated:' as table_name, COUNT(*) as count 
FROM email_notifications 
WHERE subject LIKE '%members.co.tz%' OR body LIKE '%members.co.tz%';

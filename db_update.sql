-- Add token column to users table if it doesn't exist
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS token VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS last_login DATETIME DEFAULT NULL; 
-- Add token column to users table if it doesn't exist
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS token VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS last_login DATETIME DEFAULT NULL;

-- Add website column to training_partners table if it doesn't exist
ALTER TABLE training_partners
ADD COLUMN IF NOT EXISTS website VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS registration_doc VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS agreement_doc VARCHAR(255) DEFAULT NULL; 
-- Drop the existing table if it exists
DROP TABLE IF EXISTS training_centers;

-- Create the training_centers table with all required fields
CREATE TABLE training_centers (
    center_id INT(11) NOT NULL AUTO_INCREMENT,
    partner_id INT(11) NOT NULL,
    center_name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(15),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    pincode VARCHAR(10),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (center_id),
    FOREIGN KEY (partner_id) REFERENCES training_partners(partner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add new columns to the training_centers table if they don't exist
ALTER TABLE training_centers
ADD COLUMN IF NOT EXISTS city VARCHAR(50) AFTER address,
ADD COLUMN IF NOT EXISTS state VARCHAR(50) AFTER city,
ADD COLUMN IF NOT EXISTS pincode VARCHAR(10) AFTER state; 
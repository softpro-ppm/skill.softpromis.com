-- Create training partners table
CREATE TABLE IF NOT EXISTS training_partners (
    partner_id int(11) NOT NULL AUTO_INCREMENT,
    partner_name varchar(100) NOT NULL,
    contact_person varchar(100) DEFAULT NULL,
    email varchar(100) DEFAULT NULL,
    phone varchar(15) DEFAULT NULL,
    address text DEFAULT NULL,
    status enum('active','inactive') DEFAULT 'active',
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (partner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 
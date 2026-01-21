-- BikeClean Database Schema
-- Create the database
CREATE DATABASE IF NOT EXISTS bikeclean;
USE bikeclean;

-- Mechanics table
CREATE TABLE IF NOT EXISTS mechanics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(60) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bikes table
CREATE TABLE IF NOT EXISTS bikes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mechanic_id INT NULL,
    description VARCHAR(100) NOT NULL,
    -- Repair items (0 = not done, 1 = done)
    frame_clean TINYINT(1) DEFAULT 0,
    wheels_clean TINYINT(1) DEFAULT 0,
    wheels_true TINYINT(1) DEFAULT 0,
    spokes_clean TINYINT(1) DEFAULT 0,
    kickstand_tighten TINYINT(1) DEFAULT 0,
    seat_inspect TINYINT(1) DEFAULT 0,
    tires_valve_stems TINYINT(1) DEFAULT 0,
    tires_inflate TINYINT(1) DEFAULT 0,
    rear_derailleur TINYINT(1) DEFAULT 0,
    cassette_clean TINYINT(1) DEFAULT 0,
    chain_clean TINYINT(1) DEFAULT 0,
    chainrings_clean TINYINT(1) DEFAULT 0,
    front_derailleur TINYINT(1) DEFAULT 0,
    cranks TINYINT(1) DEFAULT 0,
    pedals TINYINT(1) DEFAULT 0,
    headset_tighten TINYINT(1) DEFAULT 0,
    brakes TINYINT(1) DEFAULT 0,
    reflectors_check TINYINT(1) DEFAULT 0,
    chrome_clean TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (mechanic_id) REFERENCES mechanics(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert some sample mechanics
INSERT INTO mechanics (full_name) VALUES 
    ('John Smith'),
    ('Sarah Johnson'),
    ('Mike Davis');

-- Insert a sample bike
INSERT INTO bikes (mechanic_id, description) VALUES 
    (1, 'Red Trek Mountain Bike - Customer: Alice Brown');

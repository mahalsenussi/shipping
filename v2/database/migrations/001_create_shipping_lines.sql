-- Create shipping_lines table
CREATE TABLE IF NOT EXISTS shipping_lines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Update vessels table to reference shipping_lines
ALTER TABLE vessels 
MODIFY COLUMN shipping_line_id INT,
ADD CONSTRAINT fk_vessels_shipping_line 
FOREIGN KEY (shipping_line_id) REFERENCES shipping_lines(id) ON DELETE SET NULL;

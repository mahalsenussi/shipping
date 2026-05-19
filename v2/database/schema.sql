-- Create database
CREATE DATABASE IF NOT EXISTS shipping_v2;
USE shipping_v2;

-- Companies table (shipping companies, customers, etc.)
CREATE TABLE IF NOT EXISTS companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type ENUM('customer', 'shipping_line', 'local_agent') NOT NULL,
    address TEXT,
    phone VARCHAR(50),
    email VARCHAR(255),
    tax_number VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Ports table
CREATE TABLE IF NOT EXISTS ports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NOT NULL,
    country VARCHAR(100) NOT NULL,
    type ENUM('seaport', 'dry_port', 'airport') NOT NULL
);

-- Vessels table
CREATE TABLE IF NOT EXISTS vessels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    imo_number VARCHAR(100),
    shipping_line_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shipping_line_id) REFERENCES companies(id)
);

-- Container types
CREATE TABLE IF NOT EXISTS container_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL,
    description VARCHAR(255),
    length_ft INT,
    height_ft INT,
    width_ft INT,
    max_weight_kg DECIMAL(10,2)
);

-- Shipments table (main entity)
CREATE TABLE IF NOT EXISTS shipments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT NOT NULL,
    shipping_type ENUM('naval', 'air', 'land') NOT NULL DEFAULT 'naval',
    shipping_line_id INT,
    local_agent_id INT,
    status ENUM('draft', 'quotation_sent', 'approved', 'in_transit', 'arrived', 'customs_cleared', 'delivered', 'cancelled') DEFAULT 'draft',
    origin_port_id INT,
    destination_port_id INT,
    vessel_id INT,
    voyage_number VARCHAR(100),
    estimated_departure_date DATE,
    estimated_arrival_date DATE,
    actual_arrival_date DATE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES companies(id),
    FOREIGN KEY (shipping_line_id) REFERENCES companies(id),
    FOREIGN KEY (local_agent_id) REFERENCES companies(id),
    FOREIGN KEY (origin_port_id) REFERENCES ports(id),
    FOREIGN KEY (destination_port_id) REFERENCES ports(id),
    FOREIGN KEY (vessel_id) REFERENCES vessels(id)
);

-- Containers table
CREATE TABLE IF NOT EXISTS containers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shipment_id INT NOT NULL,
    container_number VARCHAR(50) UNIQUE NOT NULL,
    container_type_id INT,
    seal_number VARCHAR(100),
    weight_kg DECIMAL(10,2),
    volume_cbm DECIMAL(10,3),
    status ENUM('empty', 'loaded', 'in_transit', 'arrived', 'delivered') DEFAULT 'empty',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shipment_id) REFERENCES shipments(id),
    FOREIGN KEY (container_type_id) REFERENCES container_types(id)
);

-- Cargo items
CREATE TABLE IF NOT EXISTS cargo_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shipment_id INT NOT NULL,
    description TEXT NOT NULL,
    hs_code VARCHAR(50),
    quantity INT NOT NULL,
    unit_type VARCHAR(20) DEFAULT 'PCS',
    weight_kg DECIMAL(10,2),
    volume_cbm DECIMAL(10,3),
    container_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shipment_id) REFERENCES shipments(id),
    FOREIGN KEY (container_id) REFERENCES containers(id)
);

-- Documents
CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shipment_id INT NOT NULL,
    document_type ENUM('quotation', 'bill_of_lading', 'commercial_invoice', 'packing_list', 'customs_declaration', 'delivery_order', 'receipt') NOT NULL,
    document_number VARCHAR(100) NOT NULL,
    file_path VARCHAR(512),
    issue_date DATE,
    expiry_date DATE,
    status ENUM('draft', 'issued', 'approved', 'rejected') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shipment_id) REFERENCES shipments(id)
);

-- Quotations
CREATE TABLE IF NOT EXISTS quotations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_id INT NOT NULL,
    valid_until DATE,
    currency VARCHAR(3) DEFAULT 'USD',
    subtotal DECIMAL(12,2) NOT NULL,
    tax_amount DECIMAL(12,2) DEFAULT 0,
    total_amount DECIMAL(12,2) NOT NULL,
    notes TEXT,
    FOREIGN KEY (document_id) REFERENCES documents(id)
);

-- Quotation items
CREATE TABLE IF NOT EXISTS quotation_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quotation_id INT NOT NULL,
    description TEXT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    tax_rate DECIMAL(5,2) DEFAULT 0,
    tax_amount DECIMAL(12,2) DEFAULT 0,
    FOREIGN KEY (quotation_id) REFERENCES quotations(id)
);

-- Bills of Lading
CREATE TABLE IF NOT EXISTS bills_of_lading (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_id INT NOT NULL,
    bl_number VARCHAR(100) UNIQUE NOT NULL,
    shipper_id INT NOT NULL,
    consignee_id INT,
    notify_party_id INT,
    place_of_receipt VARCHAR(255),
    place_of_delivery VARCHAR(255),
    freight_terms ENUM('prepaid', 'collect') NOT NULL,
    FOREIGN KEY (document_id) REFERENCES documents(id),
    FOREIGN KEY (shipper_id) REFERENCES companies(id),
    FOREIGN KEY (consignee_id) REFERENCES companies(id),
    FOREIGN KEY (notify_party_id) REFERENCES companies(id)
);

-- Shipment status history
CREATE TABLE IF NOT EXISTS shipment_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shipment_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shipment_id) REFERENCES shipments(id)
);

-- Insert some initial data
INSERT INTO container_types (code, description, length_ft, height_ft, width_ft, max_weight_kg) VALUES
('20ST', '20'' Standard Dry Container', 20, 8.5, 8, 28123),
('40ST', '40'' Standard Dry Container', 40, 8.5, 8, 26500),
('40HQ', '40'' High Cube Container', 40, 9.5, 8, 26400),
('45HQ', '45'' High Cube Container', 45, 9.5, 8, 25800);

-- Payments table - tracks payments when shipments are approved
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shipment_id INT NOT NULL,
    quotation_id INT,
    amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    payment_date DATE NOT NULL,
    payment_method ENUM('cash', 'bank_transfer', 'check', 'credit_card') DEFAULT 'bank_transfer',
    reference_number VARCHAR(100),
    notes TEXT,
    status ENUM('pending', 'paid', 'overdue', 'cancelled') DEFAULT 'paid',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shipment_id) REFERENCES shipments(id),
    FOREIGN KEY (quotation_id) REFERENCES quotations(id)
);

-- Insert some sample container types if they don't exist
INSERT IGNORE INTO container_types (code, description, length_ft, height_ft, width_ft, max_weight_kg) VALUES
('20ST', '20\' Standard Dry Container', 20, 8.5, 8, 28123),
('40ST', '40\' Standard Dry Container', 40, 8.5, 8, 26500),
('40HQ', '40\' High Cube Container', 40, 9.5, 8, 26400),
('45HQ', '45\' High Cube Container', 45, 9.5, 8, 25800);

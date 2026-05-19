CREATE DATABASE IF NOT EXISTS harmony1_shipping;

USE harmony1_shipping;

CREATE TABLE IF NOT EXISTS company (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    company_address VARCHAR(255) NOT NULL,
    company_phone VARCHAR(20) NOT NULL,
    company_email VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS broker (
    id INT AUTO_INCREMENT PRIMARY KEY,
    broker_name VARCHAR(255) NOT NULL,
    broker_address VARCHAR(255) NOT NULL,
    broker_phone VARCHAR(20) NOT NULL,
    broker_email VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS shipments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    container_number VARCHAR(255) NOT NULL,
    company_id INT NOT NULL,
    contact_person VARCHAR(255) NOT NULL,
    container_size VARCHAR(50) NOT NULL,
    broker_id INT NOT NULL,
    date DATE NOT NULL,
    shipping_company VARCHAR(255) NOT NULL,
    shipping_port VARCHAR(255) NOT NULL,
    arrival_port VARCHAR(255) NOT NULL,
    FOREIGN KEY (company_id) REFERENCES company(id),
    FOREIGN KEY (broker_id) REFERENCES broker(id)
);

CREATE TABLE IF NOT EXISTS bill_of_lading (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shipment_id INT NOT NULL,
    bill_number VARCHAR(255) NOT NULL,
    issue_date DATE NOT NULL,
    FOREIGN KEY (shipment_id) REFERENCES shipments(id)
);

CREATE TABLE IF NOT EXISTS terms_conditions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    terms TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS freight_charges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bill_number VARCHAR(255) NOT NULL,
    rate VARCHAR(255) NOT NULL,
    currency VARCHAR(50) NOT NULL,
    amount VARCHAR(255) NOT NULL,
    prepaid VARCHAR(255) NOT NULL,
    carriers_receipt VARCHAR(255) NOT NULL,
    place_of_issue VARCHAR(255) NOT NULL,
    number_sequence VARCHAR(255) NOT NULL,
    date_of_issue DATE NOT NULL,
    declared_value VARCHAR(255) NOT NULL,
    shipped_on_board_date DATE NOT NULL
);

CREATE TABLE IF NOT EXISTS shipment_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shipment_id INT NOT NULL,
    quantity VARCHAR(255) NOT NULL,
    weight VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    marks_numbers TEXT NOT NULL,
    container_seal_number VARCHAR(255) NOT NULL,
    FOREIGN KEY (shipment_id) REFERENCES shipments(id)
);

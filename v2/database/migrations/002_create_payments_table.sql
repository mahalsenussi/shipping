-- Migration: Create payments table
-- Date: 2024-01-01
-- Description: Creates payments table to track payments when shipments are approved

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

-- Create index for better performance
CREATE INDEX idx_payments_shipment_id ON payments(shipment_id);
CREATE INDEX idx_payments_status ON payments(status);
CREATE INDEX idx_payments_date ON payments(payment_date);

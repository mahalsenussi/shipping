-- Add shipping_type field to shipments table
ALTER TABLE shipments ADD COLUMN shipping_type ENUM('naval', 'air', 'land') NOT NULL DEFAULT 'naval';

-- Add index for better query performance
CREATE INDEX idx_shipments_shipping_type ON shipments(shipping_type);

-- Update existing records to have naval as default (since they were all naval before)
UPDATE shipments SET shipping_type = 'naval' WHERE shipping_type IS NULL OR shipping_type = '';

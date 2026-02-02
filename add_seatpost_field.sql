-- Add seatpost_clean_grease field to bikes table
ALTER TABLE bikes 
ADD COLUMN seatpost_clean_grease TINYINT(1) DEFAULT 0 AFTER spokes_clean;

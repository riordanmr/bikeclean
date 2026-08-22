-- Add seatpost_clean_grease field to bikes table
ALTER TABLE bikes 
ADD COLUMN wheelhubs_tighten TINYINT(1) DEFAULT 0 AFTER seat_inspect;

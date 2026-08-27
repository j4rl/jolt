USE jolt;
ALTER TABLE jolt_questions
 ADD COLUMN media_credit VARCHAR(1000) NULL AFTER media_type,
 ADD COLUMN media_source_url VARCHAR(1000) NULL AFTER media_credit;

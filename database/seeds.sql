USE lu_academic_hub;

-- Roles
INSERT IGNORE INTO roles (name, description) VALUES
('super_admin','Full system control'),
('administrator','Admin user'),
('lecturer','Lecturer / instructor'),
('student','Student user');

-- Example admin user (replace PASSWORD_HASH with PHP password_hash('YourPassword', PASSWORD_DEFAULT))
INSERT INTO users (role_id, email, username, password_hash, first_name, last_name, is_email_verified)
VALUES (
  (SELECT id FROM roles WHERE name='super_admin'),
  'admin@example.com',
  'admin',
  '$2y$10$REPLACE_WITH_PASSWORD_HASH',
  'LU',
  'Admin',
  1
);

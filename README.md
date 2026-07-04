# Lu Academic Hub (LIRA UNIVERSITY PAST PAPER REPOSITORY)

Description: Student academic support repository designed for Lira University.

This repository contains the web app scaffold for LU Academic Hub — a modern past paper repository with role-based auth, upload and review workflows, search, forum, and dashboards.

Getting started:
1. Create a MySQL database and import database/schema.sql
2. Copy config/config.example.php -> config/config.php and set credentials
3. Seed initial roles and admin (database/seeds.sql)
4. Configure your webserver to point the project root to the public directory (or root if using single-entry index.php)
5. Use PHP >= 8.0 and enable required extensions (pdo_mysql, fileinfo, gd/imagemagick recommended)

Security:
- Uses prepared statements with PDO.
- Passwords hashed with password_hash().
- CSRF tokens for forms.
- File validation and safe storage.

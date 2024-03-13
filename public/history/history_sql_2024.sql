-- Tambah kolom jabatan_id di table employee | exca, 10 Maret 2024
ALTER TABLE employees ADD jabatan_id INT NULL AFTER role_id;
-- Tambah kolom jabatan_id di table employee

-- Tambah kolom role dan status di users | exca, 13 Maret 2024
ALTER TABLE `users`
    ADD `role` TINYINT NOT NULL DEFAULT '0' AFTER `employee_id`,
    ADD `status` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `role`;
-- Tambah kolom role di users

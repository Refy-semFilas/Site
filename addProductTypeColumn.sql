USE cantina;
ALTER TABLE usuarios ADD COLUMN TIPO ENUM('cliente', 'vendedor', 'admin') DEFAULT 'cliente';
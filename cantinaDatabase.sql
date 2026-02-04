create database cantina;

use cantina;

create table usuarios (
    ID int(11) PRIMARY KEY AUTO_INCREMENT,
    USERNAME varchar(150) NOT NULL UNIQUE,
    EMAIL varchar(150) NOT NULL UNIQUE,
    SENHA varchar(255) NOT NULL,
    TIPO ENUM('cliente', 'admin') DEFAULT 'cliente'
);

create table venda(
    ID int(11) PRIMARY KEY AUTO_INCREMENT, 
    CLIENTE_ID INT NOT NULL,
    DATA date NOT NULL,
    FOREIGN KEY (CLIENTE_ID) REFERENCES usuarios(ID)
);

create table produto(
    ID int(11) PRIMARY KEY AUTO_INCREMENT,
    NOME varchar(150) NOT NULL,
    DESCRICAO varchar(200),
    VALOR decimal(10,2) NOT NULL,
    CODIGO_DE_BARRAS VARCHAR(255) UNIQUE,
    IMAGEM varchar(255),
    CATEGORIA VARCHAR(50) NOT NULL
);

create table itens_venda (
    ID int(11) PRIMARY KEY AUTO_INCREMENT,
    VENDA_ID int,
    PRODUTO_ID int,
    QUANTIDADE int NOT NULL,
    PRECO_UNITARIO decimal(10,2) NOT NULL,
    FOREIGN KEY (VENDA_ID) REFERENCES venda(ID),
    FOREIGN KEY (PRODUTO_ID) REFERENCES produto(ID)
);


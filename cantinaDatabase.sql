DO $$
BEGIN
  IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'usuario_tipo') THEN
    CREATE TYPE usuario_tipo AS ENUM ('cliente', 'admin');
  END IF;
END$$;

CREATE TABLE IF NOT EXISTS usuarios (
  id bigserial PRIMARY KEY,
  username varchar(150) NOT NULL UNIQUE,
  email varchar(150) NOT NULL UNIQUE,
  senha varchar(255) NOT NULL,
  tipo usuario_tipo DEFAULT 'cliente'
);

CREATE TABLE IF NOT EXISTS venda (
  id bigserial PRIMARY KEY,
  cliente_id bigint NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
  data date NOT NULL
);

CREATE TABLE IF NOT EXISTS produto (
  id bigserial PRIMARY KEY,
  nome varchar(150) NOT NULL,
  descricao varchar(200),
  valor numeric(10,2) NOT NULL,
  codigo_de_barras varchar(255) UNIQUE,
  imagem varchar(255),
  categoria varchar(50) NOT NULL,
  estoque integer NOT NULL
);

CREATE TABLE IF NOT EXISTS itens_venda (
  id bigserial PRIMARY KEY,
  venda_id bigint NOT NULL REFERENCES venda(id) ON DELETE CASCADE,
  produto_id bigint NOT NULL REFERENCES produto(id) ON DELETE RESTRICT,
  quantidade integer NOT NULL,
  preco_unitario numeric(10,2) NOT NULL
);

CREATE TABLE IF NOT EXISTS password_resets (
  id bigserial PRIMARY KEY,
  user_id bigint NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
  token varchar(255) NOT NULL,
  expires_at timestamp not null,
  created_at timestamp DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_password_resets_token ON password_resets(token);
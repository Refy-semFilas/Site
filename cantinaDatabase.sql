-- WARNING: This schema is for context only and is not meant to be run.
-- Table order and constraints may not be valid for execution.

CREATE TABLE public.itens_venda (
  id bigint NOT NULL DEFAULT nextval('itens_venda_id_seq'::regclass),
  venda_id bigint NOT NULL,
  produto_id bigint NOT NULL,
  quantidade integer NOT NULL,
  preco_unitario numeric NOT NULL,
  CONSTRAINT itens_venda_pkey PRIMARY KEY (id),
  CONSTRAINT itens_venda_venda_id_fkey FOREIGN KEY (venda_id) REFERENCES public.venda(id),
  CONSTRAINT itens_venda_produto_id_fkey FOREIGN KEY (produto_id) REFERENCES public.produto(id)
);
CREATE TABLE public.pagamento (
  id bigint NOT NULL DEFAULT nextval('pagamento_id_seq'::regclass),
  venda_id bigint,
  mp_payment_id bigint,
  mp_status text DEFAULT 'pending'::text,
  valor numeric NOT NULL,
  vendedor_id integer,
  qr_code text,
  qr_code_base64 text,
  created_at timestamp without time zone DEFAULT now(),
  CONSTRAINT pagamento_pkey PRIMARY KEY (id),
  CONSTRAINT pagamento_venda_id_fkey FOREIGN KEY (venda_id) REFERENCES public.venda(id),
  CONSTRAINT pagamento_vendedor_id_fkey FOREIGN KEY (vendedor_id) REFERENCES public.usuarios(id)
);
CREATE TABLE public.produto (
  id bigint NOT NULL DEFAULT nextval('produto_id_seq'::regclass),
  nome character varying NOT NULL,
  descricao character varying,
  valor numeric NOT NULL,
  codigo_de_barras character varying UNIQUE,
  imagem character varying,
  categoria character varying NOT NULL,
  estoque integer NOT NULL,
  usuario_id integer,
  CONSTRAINT produto_pkey PRIMARY KEY (id),
  CONSTRAINT produto_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id)
);
CREATE TABLE public.usuarios (
  id integer NOT NULL DEFAULT nextval('usuarios_id_seq'::regclass),
  username character varying NOT NULL UNIQUE,
  email character varying NOT NULL UNIQUE,
  senha character varying NOT NULL,
  tipo character varying DEFAULT 'cliente'::character varying,
  chave_pix text,
  mp_access_token text,
  CONSTRAINT usuarios_pkey PRIMARY KEY (id)
);
CREATE TABLE public.venda (
  id bigint NOT NULL DEFAULT nextval('venda_id_seq'::regclass),
  cliente_id bigint NOT NULL,
  data date NOT NULL,
  status text DEFAULT 'aguardando'::text,
  CONSTRAINT venda_pkey PRIMARY KEY (id)
);
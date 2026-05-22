ALTER TABLE public.usuarios ADD COLUMN IF NOT EXISTS mp_access_token TEXT;

ALTER TABLE public.venda ADD COLUMN IF NOT EXISTS status TEXT DEFAULT 'aguardando';

CREATE TABLE IF NOT EXISTS public.pagamento (
  id BIGSERIAL PRIMARY KEY,
  venda_id BIGINT REFERENCES public.venda(id),
  mp_payment_id BIGINT,
  mp_status TEXT DEFAULT 'pending',
  valor NUMERIC NOT NULL,
  vendedor_id INTEGER REFERENCES public.usuarios(id),
  qr_code TEXT,
  qr_code_base64 TEXT,
  created_at TIMESTAMP DEFAULT NOW()
);

-- Executar no SQL Editor do Supabase
ALTER TABLE public.produto ADD COLUMN usuario_id INTEGER REFERENCES public.usuarios(id);
ALTER TABLE public.usuarios ADD COLUMN chave_pix TEXT;

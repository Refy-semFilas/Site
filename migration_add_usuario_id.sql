-- Executar no SQL Editor do Supabase
ALTER TABLE public.produto ADD COLUMN usuario_id INTEGER REFERENCES public.usuarios(id);

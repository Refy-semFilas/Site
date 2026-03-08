document.addEventListener('click', function (e) {
    if (e.target.classList.contains('comprar')) {
        const botao = e.target;
        const estoque = parseInt(botao.dataset.estoque);
        const produtoId = botao.dataset.id;

        if (estoque <= 0) {
            alert('Produto indisponível no momento!');
            return;
        }

        const produto = {
            id: produtoId,
            nome: botao.dataset.nome,
            preco: Number(botao.dataset.preco),
            imagem: botao.dataset.imagem,
            estoque: estoque,
            quantidade: 1
        };

        let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];

        const existente = carrinho.find(p => p.id === produtoId);

        if (existente) {
            if (existente.quantidade >= estoque) {
                alert('Quantidade máxima em estoque atingida!');
                return;
            }
            existente.quantidade += 1;
        } else {
            carrinho.push(produto);
        }

        localStorage.setItem('carrinho', JSON.stringify(carrinho));

        window.location.href = "shoppingCart.html";
    }
});

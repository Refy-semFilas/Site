document.addEventListener('click', function (e) {
    if (e.target.classList.contains('comprar')) {
        const botao = e.target;

        const produto = {
            nome: botao.dataset.nome,
            preco: Number(botao.dataset.preco),
            imagem: botao.dataset.imagem,
            quantidade: 1
        };

        let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];

        const existente = carrinho.find(p => p.nome === produto.nome);

        if (existente) {
            existente.quantidade += 1;
        } else {
            carrinho.push(produto);
        }

        localStorage.setItem('carrinho', JSON.stringify(carrinho));

        window.location.href = "shoppingCart.html";
    }
});

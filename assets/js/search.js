// Sistema de busca com animação única
class ProductSearch {
    constructor() {
        this.searchInput = document.querySelector('.barraPesquisa input');
        this.container = document.getElementById('produtos');
        this.allProducts = [];
        this.animatedCards = new Set(); // Controla quais cards já foram animados
        
        this.init();
    }

    init() {
        if (!this.searchInput || !this.container) return;
        
        this.searchInput.addEventListener('input', this.handleSearch.bind(this));
        
        // Observa mudanças no container para detectar novos cards
        this.observer = new MutationObserver(this.observeChanges.bind(this));
        this.observer.observe(this.container, { 
            childList: true, 
            subtree: true 
        });
    }

    handleSearch(event) {
        const searchTerm = event.target.value.toLowerCase().trim();
        const cards = this.container.querySelectorAll('.card');
        
        cards.forEach(card => {
            const productName = card.querySelector('.descricao p')?.textContent.toLowerCase() || '';
            const shouldShow = productName.includes(searchTerm);
            
            if (shouldShow) {
                card.classList.remove('card-hidden');
                
                // Adiciona animação apenas na primeira vez que o card aparece
                const cardId = this.getCardId(card);
                if (!this.animatedCards.has(cardId)) {
                    this.animateCard(card);
                    this.animatedCards.add(cardId);
                }
            } else {
                card.classList.add('card-hidden');
            }
        });
    }

    observeChanges(mutations) {
        mutations.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
                if (node.classList && node.classList.contains('card')) {
                    // Anima novos cards quando são adicionados
                    setTimeout(() => {
                        this.animateCard(node);
                        const cardId = this.getCardId(node);
                        this.animatedCards.add(cardId);
                    }, 100);
                }
            });
        });
    }

    animateCard(card) {
        card.classList.add('card-appear');
    }

    getCardId(card) {
        // Gera um ID único baseado no nome do produto
        const productName = card.querySelector('.descricao p')?.textContent || '';
        return productName.toLowerCase().replace(/\s+/g, '-');
    }

    destroy() {
        if (this.observer) {
            this.observer.disconnect();
        }
    }
}

// Inicializa o sistema quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    window.productSearch = new ProductSearch();
});

// Limpa quando a página for descarregada
window.addEventListener('beforeunload', () => {
    if (window.productSearch) {
        window.productSearch.destroy();
    }
});
import BaseController from './base_controller';

export default class extends BaseController {
    static targets = ['count'];
    static values = {
        csrfToken: String,
    };

    connect() {
        // Слушаем глобальное событие cart:updated
        window.addEventListener('cart:updated', () => {
            this.refreshCount();
        });

        // При загрузке сразу обновляем количество
        this.refreshCount();
    }

    // document.querySelector('[name="csrf-token"]').content,
    refreshCount() {
        fetch('/api/cart', {
            headers: this.getHeaders()
        })
            .then(response => response.json())
            .then(data => {
                this.countTarget.textContent = data.cart.items_count;
            })
            .catch(() => {
                this.countTarget.textContent = '0';
            });
    }
}

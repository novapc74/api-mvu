import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
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

    refreshCount() {
        fetch('/api/cart', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': this.csrfTokenValue,
            }
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

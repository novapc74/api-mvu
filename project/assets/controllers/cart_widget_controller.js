import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['count', 'dropdown', 'dropdownContent'];

    connect() {
        this.dropdownVisible = false;
        this.timeoutId = null;
    }

    showDropdown() {
        if (this.dropdownVisible) return;

        // Загружаем данные AJAX-ом
        fetch('/api/cart/dropdown')
            .then(response => response.text())
            .then(html => {
                this.dropdownContentTarget.innerHTML = html;
                this.dropdownTarget.style.display = 'block';
                this.dropdownVisible = true;
            });
    }

    hideDropdown() {
        // Чтобы не дергался дропдаун при быстром уходе мыши,
        // можно добавить небольшой таймаут
        this.timeoutId = setTimeout(() => {
            this.dropdownTarget.style.display = 'none';
            this.dropdownVisible = false;
        }, 300);
    }

    cancelHide() {
        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
            this.timeoutId = null;
        }
    }

    goToCart(event) {
        event.preventDefault();
        window.location.href = this.element.querySelector('a').href;
    }
}

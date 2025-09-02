import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["quantity", "addButton", "quantityControls"];
    static values = {
        csrfToken: String,
        productId: String
    }

    connect() {
        console.log('Controller connected, CSRF token:', this.csrfTokenValue);
    }

    async incrementCartItem(event) {
        event.preventDefault();
        await this.updateCart(1, 'inc');
    }

    async decrementCartItem(event) {
        event.preventDefault();
        await this.updateCart(1, 'dec');
    }

    switchButton(type) {
        if (type === 'on') {
            if (this.hasAddButtonTarget) {
                this.addButtonTarget.style.display = 'block';
            }
            if (this.hasQuantityControlsTarget) {
                this.quantityControlsTarget.style.display = 'none';
            }
            return;
        }

        if (type === 'off') {
            if (this.hasAddButtonTarget) {
                this.addButtonTarget.style.display = 'none';
            }
            if (this.hasQuantityControlsTarget) {
                this.quantityControlsTarget.style.display = 'block';
            }
        }
    }

    async handleQuantityChange(event) {
        event.preventDefault();

        let quantity = parseInt(this.quantityTarget.value, 10);
        if (quantity < 0) {
            alert('Введите корректное количество (от 1 и выше)');
            this.quantityTarget.value = quantity;

            return;
        }

        await this.updateCart(quantity, 'set');
    }

    async updateCart(quantity, type) {
        const productId = this.productIdValue;

        try {
            let response = await fetch('/api/cart/update', {
                method: 'POST',
                headers: this.getHeaders(),
                body: JSON.stringify({productId: productId, quantity: quantity, type: type}),
            });

            let data = await response.json();

            // Если корзина не найдена и тип не на уменьшение - создаем и повторяем
            if (!data.success && data.error?.code === 404 && data.error?.type === 'cart_not_found' && type !== 'dec') {
                await this.createCart();

                response = await fetch('/api/cart/update', {
                    method: 'POST',
                    headers: this.getHeaders(),
                    body: JSON.stringify({productId: productId, quantity: quantity, type: type}),
                });

                data = await response.json();
            }

            if (data.success) {
                let newQuantity = data.data.quantity;

                this.quantityTarget.value = newQuantity;

                if (newQuantity === 0) {
                    this.switchButton('on');
                    return;
                }


                if (newQuantity > 0) {
                    this.switchButton('off');
                }


            } else {
                console.log(data.error?.message || 'Ошибка обновления корзины')
            }

        } catch (error) {
            console.log('Ошибка:', error);
        }
    }

    async createCart() {
        try {
            const response = await fetch('/api/cart', {
                method: 'POST',
                headers: this.getHeaders(),
            });

            const data = await response.json();

            if (!data.success) {
                console.log(data.error.message);
            }

        } catch (error) {
            console.log('Ошибка создания корзины:', error);
            throw error;
        }
    }

    getHeaders() {
        return {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-Token': this.csrfTokenValue,
        };
    }
}

import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ["quantity", "productId", 'type'];
    static values = {
        csrfToken: String,
        productId: String
    }

    connect() {
        console.log('Controller connected, CSRF token:', this.csrfTokenValue);
    }

    setCartToCookie(event) {
        event.preventDefault(); // если клик по ссылке или кнопке, отменяем дефолтное действие

        fetch('/api/cart', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': this.csrfTokenValue,
            },
            // body: JSON.stringify({ /* ваши данные */ }),
            // credentials: 'same-origin',
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Success:', data);
                } else {
                    alert(data.error.message);
                }
            })
            .catch(error => {
                console.error('Ошибка запроса:', error);
            });
    }

    updateCartProduct(event) {
        event.preventDefault();

        // Получаем значение из поля ввода
        const quantity = parseInt(this.quantityTarget.value, 10);
        const productId = this.productIdValue;
        const type = event.currentTarget.dataset.type;

        console.log(type);

        // Выполняем логику добавления товара в корзину
        // Например, отправка запроса на сервер
        fetch('/api/cart', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': this.csrfTokenValue,
            },
            // body: JSON.stringify({productId: productId, quantity: quantity, type: type}
            body: JSON.stringify({})
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Success:', data);
                } else {
                    alert(data.error.message);
                }
                // this.quantityTarget.value = data.newQuantity; // Предполагаем, что сервер возвращает новое количество
            })
            .catch(error => console.error('Ошибка:', error));
    }
}

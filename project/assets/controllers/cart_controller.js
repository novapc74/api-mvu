import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        csrfToken: String,
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

    addProductToCart(event) {
        event.preventDefault(); // если клик по ссылке или кнопке, отменяем дефолтное действие

        fetch('/api/cart', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': this.csrfTokenValue,
            },
            body: JSON.stringify()
        })
    }
}

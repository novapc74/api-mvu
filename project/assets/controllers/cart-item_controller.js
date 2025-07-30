import {Controller} from '@hotwired/stimulus';
import { useClickOutside } from 'stimulus-use';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    connect() {
        useClickOutside(this);
        this.element.textContent = 'Привет! ЭТО КОНТРОЛЛЕР КОРЗИНЫ';
    }
    clickOutside(event) {
        console.log('Клик вне окна!');
        // здесь ваш код при клике вне
    }

}

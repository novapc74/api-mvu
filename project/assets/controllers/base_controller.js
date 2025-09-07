import { Controller } from '@hotwired/stimulus';

export default class BaseController extends Controller {
    getHeaders() {
        return {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-Token': this.csrfTokenValue,
        };
    }
}

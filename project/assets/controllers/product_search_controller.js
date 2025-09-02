import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [];
    static values = {
        csrfToken: String,
        path: String
    }

    connect() {
        console.log(this.csrfTokenValue, this.pathValue);
    }
}

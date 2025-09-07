import BaseController from './base_controller';
import {useClickOutside} from 'stimulus-use';

export default class extends BaseController {
    static targets = ['result'];
    static values = {
        csrfToken: String,
        url: String
    }

    connect() {
        useClickOutside(this);
    }

    onSearchInput(event) {
        const query = event.currentTarget.value;
        this.search(query);
    }

    async search(query) {
        const params = new URLSearchParams({
            search: query,
            preview: 1,
        });

        const response = await fetch(
            `${this.urlValue}?${params.toString()}`, {
                method: 'GET',
                headers: this.getHeaders()
            }
        );

        const data = await response.json();

        if (!data.success) {
            return;
        }

        this.resultTarget.innerHTML = data.data;
    }

    clickOutside(event) {
        this.resultTarget.innerHTML = '';
    }
}

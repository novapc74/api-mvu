import BaseController from './base_controller';
import {useClickOutside, useDebounce} from 'stimulus-use';

export default class extends BaseController {
    static targets = ['result'];
    static values = {
        csrfToken: String,
        url: String
    }
    static debounces = ['search'];

    connect() {
        useClickOutside(this);
        useDebounce(this);
    }

    async onSearchInput(event) {
        const params = new URLSearchParams({
            search: event.currentTarget.value,
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

        if (data.success) {
            // console.log(data.data);
            this.resultTarget.innerHTML = data.data;
        }
    }

    clickOutside(event) {
        this.resultTarget.innerHTML = '';
    }
}

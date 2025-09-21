import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["list", "item"];

    connect() {
        this.focusedIndex = -1;
        this.element.addEventListener("keydown", this.onKeyDown.bind(this));
    }

    onKeyDown(event) {
        const items = this.itemTargets;
        if (!items.length) return;

        switch(event.key) {
            case "ArrowDown":
                event.preventDefault();
                this.focusedIndex = (this.focusedIndex + 1) % items.length;
                this.focusItem(this.focusedIndex);
                break;

            case "ArrowUp":
                event.preventDefault();
                this.focusedIndex = (this.focusedIndex - 1 + items.length) % items.length;
                this.focusItem(this.focusedIndex);
                break;

            case "Enter":
                event.preventDefault();
                if (this.focusedIndex >= 0) {
                    items[this.focusedIndex].click();
                }
                break;

            case "Escape":
                // Если нужно, можно сбросить фокус или скрыть список
                this.focusedIndex = -1;
                this.element.blur();
                break;
        }
    }

    focusItem(index) {
        this.itemTargets.forEach((el, i) => {
            if (i === index) {
                el.focus();
            }
        });
    }
}

import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['price']; // Цели для элементов
    static values = {data: Object}; // Значение для данных продукта

    connect() {
        this.filters = {color: null, size: null, gender: null};
        this.autoSelected = {color: false, size: false, gender: false}; // Флаги для отслеживания авто-выбора
        this.productData = this.dataValue; // Данные из data-product-variant-data-value
        this.updateFilters();
        this.updatePrice();
    }

    selectFilter(event) {
        const type = event.currentTarget.dataset.type;
        const value = event.currentTarget.dataset.value;

        // Если уже выбран, снимаем выбор
        if (this.filters[type] === value) {
            this.filters[type] = null;
        } else {
            this.filters[type] = value;
        }

        // Устанавливаем флаг ручного выбора для этого типа
        this.autoSelected[type] = false;

        this.updateFilters();
        this.updatePrice();
    }

    resetFilters() {
        // Сбрасываем все фильтры и авто-выборы
        this.filters = {color: null, size: null, gender: null};
        this.autoSelected = {color: false, size: false, gender: false};
        this.updateFilters();
        this.updatePrice();
    }

    updateFilters() {
        // Получаем все кнопки
        const buttons = this.element.querySelectorAll('.filter-btn');

        buttons.forEach(btn => {
            const type = btn.dataset.type;
            const value = btn.dataset.value;
            const isSelected = this.filters[type] === value;
            const isAvailable = this.isValueAvailable(type, value);

            btn.classList.toggle('selected', isSelected);
            btn.classList.toggle('disabled', !isAvailable && !isSelected);
        });

        // Логика авто-выбора: если выбраны ровно два фильтра и для третьего есть только один доступный вариант
        const selectedCount = Object.values(this.filters).filter(f => f !== null).length;
        if (selectedCount === 2) {
            const nullType = Object.keys(this.filters).find(type => this.filters[type] === null);
            if (nullType) {
                // Если третий фильтр был автоматически выбран, сбрасываем его
                if (this.autoSelected[nullType]) {
                    this.filters[nullType] = null;
                    this.autoSelected[nullType] = false;
                    // Рекурсивно обновляем после сброса
                    this.updateFilters();
                    return; // Прерываем, чтобы не продолжать авто-выбор
                }

                const availableValues = this.getAvailableValues(nullType);
                if (availableValues.length === 1) {
                    this.filters[nullType] = availableValues[0];
                    this.autoSelected[nullType] = true;
                    // Рекурсивно обновляем
                    this.updateFilters();
                    this.updatePrice();
                }
            }
        }

        // Новая логика: если выбран ровно один фильтр, и для каждого из двух других есть ровно один доступный вариант, авто-выбираем их
        if (selectedCount === 1) {
            const nullTypes = Object.keys(this.filters).filter(type => this.filters[type] === null);
            if (nullTypes.length === 2) {
                let canAutoSelect = true;
                const toSelect = {};
                nullTypes.forEach(type => {
                    const available = this.getAvailableValues(type);
                    if (available.length === 1) {
                        toSelect[type] = available[0];
                    } else {
                        canAutoSelect = false;
                    }
                });
                if (canAutoSelect) {
                    nullTypes.forEach(type => {
                        this.filters[type] = toSelect[type];
                        this.autoSelected[type] = true;
                    });
                    // Рекурсивно обновляем
                    this.updateFilters();
                    this.updatePrice();
                }
            }
        }
    }

    getAvailableValues(type) {
        const activeFilters = { ...this.filters };
        delete activeFilters[type];

        const values = [];
        if (type === 'color') {
            Object.keys(this.productData.colors).forEach(color => {
                if (this.isValueAvailable(type, color)) {
                    values.push(color);
                }
            });
        } else if (type === 'size') {
            // Собираем уникальные размеры из доступных цветов
            const sizes = new Set();
            Object.keys(this.productData.colors).forEach(color => {
                if (!activeFilters.color || color === activeFilters.color) {
                    Object.keys(this.productData.colors[color] || {}).forEach(size => {
                        if (this.isValueAvailable(type, size)) {
                            sizes.add(size);
                        }
                    });
                }
            });
            values.push(...sizes);
        } else if (type === 'gender') {
            // Собираем уникальные гендеры из доступных цветов и размеров
            const genders = new Set();
            Object.keys(this.productData.colors).forEach(color => {
                if (!activeFilters.color || color === activeFilters.color) {
                    Object.keys(this.productData.colors[color] || {}).forEach(size => {
                        if (!activeFilters.size || size === activeFilters.size) {
                            Object.keys(this.productData.colors[color][size] || {}).forEach(gender => {
                                if (this.isValueAvailable(type, gender)) {
                                    genders.add(gender);
                                }
                            });
                        }
                    });
                }
            });
            values.push(...genders);
        }
        return values;
    }

    isValueAvailable(type, value) {
        const activeFilters = { ...this.filters };
        delete activeFilters[type];

        // Если нет других фильтров, все доступны
        if (!activeFilters.color && !activeFilters.size && !activeFilters.gender) {
            return true;
        }

        if (type === 'color') {
            const sizes = this.productData.colors[value] || {};
            return Object.keys(sizes).some(size => {
                if (activeFilters.size && size !== activeFilters.size) return false;
                const genders = sizes[size] || {};
                return Object.keys(genders).some(gender => {
                    return !activeFilters.gender || gender === activeFilters.gender;
                });
            });
        } else if (type === 'size') {
            return Object.keys(this.productData.colors).some(color => {
                if (activeFilters.color && color !== activeFilters.color) return false;
                const genders = this.productData.colors[color]?.[value] || {};
                return Object.keys(genders).some(gender => {
                    return !activeFilters.gender || gender === activeFilters.gender;
                });
            });
        } else if (type === 'gender') {
            return Object.keys(this.productData.colors).some(color => {
                if (activeFilters.color && color !== activeFilters.color) return false;
                const sizes = this.productData.colors[color] || {};
                return Object.keys(sizes).some(size => {
                    if (activeFilters.size && size !== activeFilters.size) return false;
                    return !!sizes[size]?.[value];
                });
            });
        }
        return false;
    }

    updatePrice() {
        if (this.filters.color && this.filters.size && this.filters.gender) {
            const stock = this.productData.colors[this.filters.color]?.[this.filters.size]?.[this.filters.gender];
            this.priceTarget.textContent = stock ? 'Цена: ' + stock.price + ' руб.' : '';
        } else {
            this.priceTarget.textContent = 'Выберите параметры.';
        }
    }
}

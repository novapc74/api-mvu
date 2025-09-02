## 1. Symfony Live Component для виджета корзины

Создадим Live Component, который рендерит количество товаров в корзине и базовую разметку виджета.

```php
// src/Component/CartWidgetComponent.php
namespace App\Component;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use App\Service\CartService;

#[AsLiveComponent('cart_widget')]
class CartWidgetComponent
{
    use DefaultActionTrait;

    public int $totalItems = 0;

    public function __construct(private CartService $cartService)
    {
    }

    public function mount(): void
    {
        $this->totalItems = $this->cartService->getTotalItems();
    }

    // Метод для обновления количества (если нужно)
    public function refreshCount(): void
    {
        $this->totalItems = $this->cartService->getTotalItems();
    }
}
```

---

## 2. Twig-шаблон компонента

```twig
{# templates/components/cart_widget.html.twig #}
<div {{ $attributes->merge(['data-controller' => 'cart-widget']) }}>
    <a href="{{ path('cart_show') }}" data-action="click->cart-widget#goToCart">
        Корзина (<span data-cart-widget-target="count">{{ this.totalItems }}</span>)
    </a>

    <div data-cart-widget-target="dropdown" class="cart-dropdown" style="display: none;">
        {# Контент дропдауна будет загружаться AJAX-ом #}
        <div data-cart-widget-target="dropdownContent">
            <!-- Тут будет AJAX-контент -->
        </div>
    </div>
</div>
```

---

## 3. Stimulus контроллер

Создайте Stimulus контроллер, который при наведении на ссылку корзины отправит AJAX-запрос и покажет дропдаун с карточками товаров.

```js
// assets/controllers/cart_widget_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['count', 'dropdown', 'dropdownContent'];

  connect() {
    this.dropdownVisible = false;
    this.timeoutId = null;
  }

  showDropdown() {
    if (this.dropdownVisible) return;

    // Загружаем данные AJAX-ом
    fetch('/cart/dropdown')
      .then(response => response.text())
      .then(html => {
        this.dropdownContentTarget.innerHTML = html;
        this.dropdownTarget.style.display = 'block';
        this.dropdownVisible = true;
      });
  }

  hideDropdown() {
    // Чтобы не дергался дропдаун при быстром уходе мыши,
    // можно добавить небольшой таймаут
    this.timeoutId = setTimeout(() => {
      this.dropdownTarget.style.display = 'none';
      this.dropdownVisible = false;
    }, 300);
  }

  cancelHide() {
    if (this.timeoutId) {
      clearTimeout(this.timeoutId);
      this.timeoutId = null;
    }
  }

  goToCart(event) {
    event.preventDefault();
    window.location.href = this.element.querySelector('a').href;
  }
}
```

---

## 4. Подключение событий в шаблоне компонента

Добавим в шаблон обработчики hover с помощью `mouseenter` и `mouseleave` на сам элемент и дропдаун, чтобы дропдаун не закрывался при наведении на него:

```twig
<div {{ $attributes->merge(['data-controller' => 'cart-widget']) }}
     data-action="mouseenter->cart-widget#showDropdown mouseleave->cart-widget#hideDropdown">
    <a href="{{ path('cart_show') }}" data-action="click->cart-widget#goToCart">
        Корзина (<span data-cart-widget-target="count">{{ this.totalItems }}</span>)
    </a>

    <div data-cart-widget-target="dropdown"
         data-action="mouseenter->cart-widget#cancelHide mouseleave->cart-widget#hideDropdown"
         class="cart-dropdown"
         style="display: none;">
        <div data-cart-widget-target="dropdownContent">
            <!-- AJAX-контент -->
        </div>
    </div>
</div>
```

---

## 5. Контроллер для AJAX-запроса дропдауна

Создайте контроллер, который возвращает HTML с карточками товаров в корзине.

```php
// src/Controller/CartDropdownController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CartService;

class CartDropdownController extends AbstractController
{
    #[Route('/cart/dropdown', name: 'cart_dropdown')]
    public function dropdown(CartService $cartService): Response
    {
        $items = $cartService->getItems();

        return $this->render('cart/_dropdown_items.html.twig', [
            'items' => $items,
        ]);
    }
}
```

---

## 6. Twig-шаблон карточек товаров для дропдауна

```twig
{# templates/cart/_dropdown_items.html.twig #}
{% if items is empty %}
    <p>Корзина пуста</p>
{% else %}
    <ul class="cart-dropdown-list" style="list-style: none; margin: 0; padding: 0;">
        {% for item in items %}
            <li class="cart-dropdown-item" style="display: flex; align-items: center; margin-bottom: 10px;">
                <img src="{{ item.product.imageUrl }}" alt="{{ item.product.name }}" style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                <div>
                    <div>{{ item.product.name }}</div>
                    <div>Цена: {{ item.product.price|number_format(2, ',', ' ') }} ₽</div>
                    <div>Кол-во: {{ item.quantity }}</div>
                </div>
            </li>
        {% endfor %}
    </ul>
    <a href="{{ path('cart_show') }}" style="display: block; margin-top: 10px; text-align: right;">Перейти в корзину</a>
{% endif %}
```

---

## 7. Регистрация Stimulus контроллера

В `assets/controllers/index.js` (или аналогичном) зарегистрируйте контроллер:

```js
import { Application } from '@hotwired/stimulus';
import CartWidgetController from './cart_widget_controller';

const application = Application.start();
application.register('cart-widget', CartWidgetController);
```

---

## 8. Использование компонента в шаблоне навбара

В вашем основном шаблоне навбара вставьте компонент:

```twig
{{ live_component('cart_widget') }}
```

---

## Итоги

- Количество товаров рендерится и обновляется через UX Live Component.
- Дропдаун с карточками загружается AJAX-запросом при наведении.
- Hover и клик обрабатываются Stimulus-контроллером.
- Переход на страницу корзины — обычный переход по ссылке.
- Вы легко можете добавить вызов `refreshCount()` в Live Component, если корзина меняется на других страницах, чтобы обновлять количество.

Если нужна помощь с CartService или дополнительным функционалом — обращайтесь!

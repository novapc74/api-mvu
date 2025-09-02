#### Добавляем права
```dotenv
docker exec -it ${APP_NAME}-maria-db bash
mariadb -u root -p

CREATE USER 'dev'@'%' IDENTIFIED BY 'dev';
GRANT ALL PRIVILEGES ON dev.* TO 'dev'@'%';
FLUSH PRIVILEGES;
EXIT;

exit
```

```
php bin/console doctrine:database:create
php bin/console d:m:m
```
#### Наполнение базы данных (фейковые).
```
bin/console doctrine:fixtures:load --no-interaction
```
***
TODO search
 - Поиск по популярным товарам (рейтинг товаров).
 - Рейтинг обновляем по событию, через очередь.
 - Данные поиска в редис (объект товара).
 - В окне поиска - карточки товаров с возможностью добавления в корзину.
***
TODO cart
- Корзину в nav (количество позиций).
- Выпадающий список при наведении.
- Переход на страницу корзины при клике.
- На странице реализовать секцию cart-info (оплата, доставка, скидка, итого, и т.д).
- Фильтры с типом оплат, доставкой, и т.д. 
***
TODO payment
- Подключить платежную систему (Yookassa)
- webhook на обновление данных по платежам.
***
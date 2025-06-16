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
```

```
bin/console doctrine:fixtures:load --no-interaction
```

##### TODO - сделать проверку на дублирование product_id PostDecoder (не добавлять дубли!!!)
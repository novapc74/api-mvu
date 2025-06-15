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

```dotenv
php bin/console doctrine:database:create
```
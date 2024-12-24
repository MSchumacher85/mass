1) Настроить подключение к БД
2) Запустить миграции коммандой `php yii migrate`
3) Пропишите свой email и укажите пароль от внешнего приложения, в файле web.php и RequestController, места отметил TODO
Установка завершена.

# Примеры 
1) POST `http://mass/api/requests` - создание заявки
2) GET `http://mass/api/requests' - получение всех заявок
3) GET `http://mass/api/requests?date_from=2024-12-23&date_to=2024-12-23` - получение и фильтрация по дате
4) GET `http://mass/api/requests?status=Active` - получение и фильтрация по статусу
5) PUT `http://mass/api/requests/1` - редактирование заявки

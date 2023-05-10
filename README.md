# Тестовое задание по направлению "Бэкенд-разработчик статистики сообществ"
## Задание
Сделать набор API-методов для сохранения событий и получения статистики.

Первый метод должен принимать в качестве входных параметров название события и статус пользователя (авторизован или нет). Затем сервер должен добавить вспомогательную информацию и сохранить событие. В качестве хранилища можно использовать mysql/mongo/postgre/sqlite/etc

Второй метод должен позволять фильтровать (по дате и названию события обязательно) и получать агрегированную информацию (одна из трех агрегация за раз) в формате JSON:

- счетчики конкретных событий;
- счетчики событий по пользователю (по IP-адресу);
- счетчик событий по статусу пользователя.

## Установка
Чтобы установить API, необходимо иметь Docker.
Сначала нужно создать `.env` файл. Для этого заполните `.env-example` нужными данными и используйте

    cp .env-example .env

После этого используйте 

    docker-compose up -d --build

Для того, чтобы остановить API, используйте

    docker-compose down

## Описание API
### Метод для добавления событий

`POST /event` - создание нового события. 

Входные параметры:

`name` - название события (string);

`auth` - статус пользователя - авторизован или нет (bool).

Пример запроса:
```http
{
    "name": "subscribe",
    "auth": true
}
```

Пример ответа:
```http
{
    "status": "200 OK",
    "response": {
        "name": "subscribe",
        "auth": true,
        "userIp": "172.18.0.1",
        "eventDate": "2023-05-10"
    }
}
```

### Метод для фильтрации и агрегации 

`POST /event` - фильтрация (по дате и названию события) и агрегирование информации.

Входные параметры:

`daterange` - массив из двух строк, представляющих из себя даты в формате yyyy-mm-dd. Отфильтровываются события, не поподающие в диапазон `daterange`. Необязательный параметр.

`names` - массив из названий событий. Отфильтровываются события, названия которых не указаны в `names`. Необязательный параметр.

`aggregation` - выбор агрегации. В качестве доступных значений параметра можно использовать:
- `byname` - счетчик конкретных событий (по названию события);
- `byuserip` - счетчик событий по пользователю (по IP-адресу);
- `bystatus` - счетчик событий по статусу пользователя.

Пример запроса:
```
http://localhost/statistics?daterange[]=2023-05-09&daterange[]=2023-05-10&name[]=sub&name[]=some&aggregation=byuserip
```

Пример ответа:
```http
[
    {
        "count": "4",
        "user_ip": "172.18.0.1"
    },
    {
        "count": "3",
        "user_ip": "172.22.0.1"
    },
    {
        "count": "11",
        "user_ip": "192.168.112.1"
    }
]
```


# Онлайн-аукцион

Учебный проект. Laravel + Blade, без фронтенд-фреймворков, обычный CSS и немного JS.
Три роли: админ, модератор, клиент. Клиенты ставят, модераторы держат лоты, админ правит всех.

## Что тут есть

- Лендинг с лотами, поиск, фильтры, сортировка, пагинация
- Регистрация/вход, роль при регистрации всегда client
- Ставки: первая ставка не ниже стартовой цены, дальше текущая + шаг, перебивать свою же ставку нельзя
- Проверка ставок и обновление цены идут в одной транзакции с lockForUpdate, чтобы параллельные ставки не перетирали друг друга
- Модерация лотов: черновик → на модерации → запланирован → активен → завершён (плюс отклонён и отменён)
- Истёкшие лоты закрывает команда `auctions:finish` (расписание раз в минуту в routes/console.php)
- Блокировка пользователей, удалить юзера со ставками нельзя
- Язык ru/en и валюта TJS/USD переключаются через сессию
- Тесты на ставки, роли и доступы

## Запуск

Нужен PHP 8.3+, Composer, MySQL 8.

```bash
composer install
cp .env.example .env
php artisan key:generate
# пропишите базу в .env
php artisan migrate --seed
php artisan serve
```

Раз в минуту нужен `php artisan schedule:run` (в cron или планировщике Windows).
Вручную закрыть лоты: `php artisan auctions:finish`.

## Тесты

```bash
php artisan test
```

Сейчас 16 тестов, бегут на sqlite in-memory.

## Учётные записи после сидера

Пароль у всех `password`:

- админ: `admin@auction.tj`
- модераторы: `moderator1@auction.tj`, `moderator2@auction.tj`
- клиенты: `client1@auction.tj` … `client5@auction.tj`
- заблокированный: `blocked@auction.tj`

Сидер можно перезапускать, лоты пересоздаются с датами от текущего момента.

## Структура

```
app/Services/AuctionService.php — логика ставок и закрытия лотов
app/Policies/AuctionPolicy.php — модератор видит только свои лоты
app/Http/Middleware/ — role: и язык
routes/web.php — маршруты, админка в /admin, модератор в /moderator, кабинет в /account
resources/views/ — шаблоны
database/seeders/DatabaseSeeder.php — демо-данные
lang/ru.json, lang/en.json — переводы
```

Время в БД в UTC, на сайте Asia/Dushanbe. Деньги хранятся в сомони, в долларах по курсу из config/currency.php.

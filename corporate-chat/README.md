# Corporate Chat - Корпоративный чат на Laravel 12

## Описание
Корпоративный чат для внутреннего общения сотрудников компании. Разработан на Laravel 12 с использованием PHP 8.4 и SQLite.

## Требования
- PHP 8.4+
- Composer
- SQLite
- Windows 10 (для локальной разработки)

## Функционал
1. **Регистрация сотрудников администратором** - только администраторы могут создавать новых сотрудников
2. **Личные чаты** - один на один между сотрудниками
3. **Групповые чаты** - с возможностью установки названия
4. **Профиль сотрудника** - редактирование ФИО, должности, описания, статуса, графика работы

## Установка

### Шаг 1: Установка зависимостей
```bash
cd corporate-chat
composer install
```

### Шаг 2: Настройка окружения
Файл `.env` уже настроен. При необходимости измените параметры.

### Шаг 3: Создание базы данных и миграции
```bash
php artisan migrate
```

### Шаг 4: Заполнение тестовыми данными
```bash
php artisan db:seed
```

### Шаг 5: Генерация ключа приложения (если не сгенерирован)
```bash
php artisan key:generate
```

## Запуск приложения

### 1. Запуск веб-сервера
```bash
php artisan serve
```
Приложение будет доступно по адресу: http://localhost:8000

### 2. Запуск WebSocket сервера (Laravel Reverb)
Откройте новое окно терминала и выполните:
```bash
php artisan reverb:start
```
WebSocket сервер будет слушать порт 8080.

### 3. Запуск очереди (опционально, для обработки событий)
Откройте третье окно терминала:
```bash
php artisan queue:work
```

## Учетные данные для входа

После выполнения `php artisan db:seed` будут созданы следующие пользователи:

| Email | Пароль | Роль |
|-------|--------|------|
| admin@example.com | password | Администратор |
| ivanov@example.com | password | Сотрудник |
| petrov@example.com | password | Сотрудник |
| sidorova@example.com | password | Сотрудник |

## Основные команды php artisan

```bash
# Миграции
php artisan migrate              # Применить миграции
php artisan migrate:rollback     # Откатить последнюю миграцию
php artisan migrate:refresh      # Пересоздать базу данных

# Сидеры
php artisan db:seed              # Заполнить базу данными
php artisan db:wipe              # Очистить базу данных

# Reverb (WebSocket)
php artisan reverb:start         # Запустить WebSocket сервер
php artisan reverb:start --host=0.0.0.0  # Запустить на всех интерфейсах

# Очереди
php artisan queue:work           # Запустить воркер очередей
php artisan queue:listen         # Слушать очереди

# Кэш
php artisan cache:clear          # Очистить кэш
php artisan config:clear         # Очистить кэш конфигурации
php artisan route:clear          # Очистить кэш маршрутов
php artisan view:clear           # Очистить кэш представлений

# Пользователи (создание нового администратора)
php artisan tinker
>>> App\Models\User::create(['email' => 'new@example.com', 'password' => Hash::make('password'), 'is_admin' => true])
```

## Структура проекта

```
corporate-chat/
├── app/
│   ├── Events/
│   │   └── MessageSent.php        # Событие отправки сообщения
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php # Аутентификация
│   │       ├── AdminController.php# Управление сотрудниками
│   │       ├── ChatController.php # Чаты и сообщения
│   │       ├── ProfileController.php # Профиль
│   │       └── DashboardController.php # Дашборд
│   └── Models/
│       ├── User.php               # Модель пользователя
│       ├── Chat.php               # Модель чата
│       └── Message.php            # Модель сообщения
├── config/
│   ├── broadcasting.php           # Настройки вещания
│   ├── database.php               # Настройки БД
│   └── reverb.php                 # Настройки Reverb
├── database/
│   ├── migrations/                # Миграции БД
│   └── seeders/                   # Сидеры
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php      # Основной шаблон
│       ├── auth/
│       │   └── login.blade.php    # Страница входа
│       ├── chats/                 # Представления чатов
│       ├── profile/               # Представления профиля
│       └── admin/                 # Админ-панель
├── routes/
│   ├── web.php                    # Маршруты
│   └── channels.php               # Каналы WebSocket
└── .env                           # Конфигурация окружения
```

## Важные замечания

### Совместимость
- **Laravel Reverb** используется вместо beyondcode/laravel-websockets, так как последний не совместим с PHP 8.4 и Laravel 12
- Reverb встроен в Laravel 11+ и полностью совместим с PHP 8.4
- Для работы WebSocket не требуется внешний сервис - всё работает локально

### Безопасность
- Пароли хешируются алгоритмом bcrypt
- Только администраторы могут создавать/редактировать/удалять сотрудников
- Пользователи могут видеть только свои чаты
- CSRF защита на всех формах

### Производительность
- SQLite подходит для небольших команд (до 50 человек)
- Для больших проектов рекомендуется использовать PostgreSQL или MySQL
- Включена пагинация списков чатов и сотрудников

## Решение проблем

### WebSocket не работает
1. Убедитесь, что Reverb запущен: `php artisan reverb:start`
2. Проверьте настройки в `.env`:
   ```
   REVERB_HOST=127.0.0.1
   REVERB_PORT=8080
   BROADCAST_CONNECTION=reverb
   ```

### Ошибки миграции
```bash
php artisan migrate:fresh --seed
```

### Очистка всего кэша
```bash
php artisan optimize:clear
```

## Лицензия
MIT License

# Vue Media Library With Laravel + Websocket

## Project Setup

- Copy `.env.example` to `.env`.
- Change env configuration in `.env` file.
- Change broadcast driver to pusher from env file.
- Change queue connection to database from env file.
- Change filesystem disk to public from env file.
- Change pusher configuration from env like this -

```dotenv
PUSHER_APP_ID=anyId
PUSHER_APP_KEY=anyKey
PUSHER_APP_SECRET=anySecret
PUSHER_HOST=0.0.0.0
PUSHER_PORT=6001
PUSHER_SCHEME=http
PUSHER_APP_CLUSTER=ap2
```

- Run `composer install` or `composer update`.
- Run `php artisan key:generate`.
- Run `php artisan migrate`.
- Run `php artisan serve`.
- Run `php artisan websocket:serve` to make app realtime.
- Run `php artisan queue:work` to run queue jobs.
- Now go to the browser and hit the served url.

### Note: Before run the app must run websocket.

### Thank you

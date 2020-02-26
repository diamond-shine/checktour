# Description
Tour check in and waiting room system

Environment and technologies:
- Framework Laravel 6 https://laravel.com/docs/6.x
- Webpack
- Node v12.13.0
- PHP 7.2
- Nginx (/laradock/nginx/sites/tc-bookeo.local.conf)

# Deployment
### Step 1
Run following command inside project root directory

    cp .env.example .env

It will create **.env** file within project root directory from .env.example file.

### Step 2
Set app configs in **.env** file
- Set ***APP_URL*** and ***APP_STATIC_URL***
- DB connection configs (***DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD***)
- Mail connfigs
- Bookeo API keys (***BOOKEO_API_KEY, BOOKEO_SECRET_KEY***)
- Set ***TIMEZONE***

### Step 3
Run the following command

    composer install
    php artisan key:generate
    php artisan migrate
    php artisan db:seed

### Step 4
Export bookeo webhook listeners.

**ATTENTION**
*THIS COMMAND WILL REPLACE ALREADY EXISTING LISTENERS WITH PARAMETER* **domain: bookings**


    php artisan export:webhooks

Add schedule:run command into crontab list as in the **example below**

    * * * * * php /home/www/{project-folder-name}/artisan schedule:run >> /dev/null 2>&1

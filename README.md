# saas-starter
A saas-starter - using Laravel Nova and hyn/multi-tenant

Heads up: You need a Laravel Nova license in order to use this app. Purchase one at https://nova.laravel.com.

# Installation
- Clone this repo
- `cp .env.example .env`
- Create a db user with all privileges (set this user as primary db user in .env)
- Create one database for the app, one for tests (e.g. "saas" and "saas-tests"), see phpunit.xml
- Set other .env variables (DB_*, MAIL_* and APP_FQDN)
- `php artisan key:generate`
- `composer install`
- `yarn run dev`

Secure your site with `valet secure saas-starter`
Now you can visit https://saas-starter.test

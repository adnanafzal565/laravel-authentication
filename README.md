<p align="center"><img src="public/img/laravel-react.png" width="400" alt="Laravel and React" /></p>

## Authentication boilerplate

<p>This boilerplate will help you speed-up the development process by providing all the features you need for user authentication. So you don't have to re-develop the user authentication module for each new project.</p>

### Features

1. User registration
2. Login with JWT (Json Web Token)
3. Logout
4. Forgot password
5. User profile
6. Edit name and profile picture
7. Change password
8. Admin panel
9. Dynamic SMTP settings from admin panel
10. Chat between user and admin (with attachments)

### Tech stack

- PHP +8.2
- Laravel +10
- React +18
- Bootstrap +5

### How to setup

1. Goto file "config/database.php" and set your database credentials.

```
'mysql' => [
    ...

    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'laravel_authentication',
    'username' => 'root',
    'password' => '',

    ...
],
```

Create a database named "laravel_authentication" in your phpMyAdmin.

2. Rename the file ".env.example" to just ".env"

3. Set your API URL in "config/config.php" variable "api_url".

4. At root folder, run the following commands:

(You can write any name, email or password of your choice for super admin while running 5th command)

```
1) COMPOSER_MEMORY_LIMIT=-1 composer update
2) php artisan key:generate
3) php artisan storage:link
4) php artisan migrate
5) name="Admin" email="admin@adnan-tech.com" password="admin" php artisan db:seed --class=DatabaseSeeder
6) php artisan serve
```

You can access the project from:
http://localhost:8000

For deployment, check our <a href="https://www.youtube.com/watch?v=EKJnV_-ZX0o" target="_blank">tutorial</a>.

If you face any issue in this, kindly let me know: support@adnan-tech.com
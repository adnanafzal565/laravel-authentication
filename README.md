<p align="center"><img src="img/laravel-react.png" width="400" alt="Laravel and React" /></p>

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

### Tech stack

- Laravel +10
- React +18
- Bootstrap +5

### How to setup

1. Goto file "server/config/database.php" and set your database credentials.

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

Create a database of that name in your phpMyAdmin too.

2. Goto file "server/config/mail.php" and set your SMTP credentials.

```
'mailers' => [
    'smtp' => [
        ...

        "host" => "mail.adnan-tech.com",
        "port" => 587,
        "encryption" => "tls",
        "username" => "support@adnan-tech.com",
        "password" => "",

        ...
    ],
]
```

You can access the project from:
http://localhost/laravel-authentication

If you face any issue in this, kindly let me know: support@adnan-tech.com
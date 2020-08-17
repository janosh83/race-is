Install
pre-install tasks
* install symphony, composer
* install php, mysql and some web server (XAMPP pack for windows is working fine)

clone git repository
```
> git clone https://github.com/janosh83/race-is.git
```

install dependencies
```
> composer install --no-dev --optimize-autoloader
```

setup Mysql
```
# file .env.local
APP_ENV=prod
DATABASE_URL="mysql://name:password@127.0.0.1:3306/database"
MAILER_DSN=smtp://name:password@smtp.picnicadventures.com:587
```

create database and run migration
```
> php bin/console doctrine:database:create
> php bin/console doctrine:migrations:migrate
```
load test data
```
> php bin/console doctrine:fixtures:load
```

You may also need todownload CKEditor (rich text editor)
```
php bin/console ckeditor:install --release=basic
```

setup Apache pack (it will allow to run project easily at shared webhosting)
```
> composer require symfony/apache-pack
```
Install
pre-install tasks
* install symfony, composer
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

Data import
* Dowmload kml and kmz files from map with peaks
* Rename kmz to zip and delete all icons from zip file
* Process main xml file extraced from kmz with script gen_maps_file.py
* Store updated xml into zip and rename it back to kmz -> this will creale kmz file, which is then possible to import into maps.me

* Prosess kml file by script proc_peaks.py -> this will kreate json file with all the peaks, which is then possible import in admin interface


SQL stats
```
SELECT * FROM `visit` WHERE race_id = [raceid] GROUP BY peak_id
```
shows all visited peaks


```
SELECT * FROM `peak` WHERE race_id = [raceid]
```
shows all peaks prepared for the race

```
SELECT visit.peak_id, peak.title, count(*) 
FROM visit LEFt JOIN peak ON visit.peak_id = peak.id 
WHERE visit.race_id = 27 
GROUP BY peak_id ORDER BY `count(*)` DESC
```
number of visits per peak
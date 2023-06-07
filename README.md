## Installation 
### Server Requirements

- PHP version 7.4.11
- MySQL version 8.0.21
- Composer
- Git
- NPM

### === Change config PHP .int file  ====
```terminal

max_input_time=6000
max_execution_time=1200
upload_max_filesize=1G
memory_limit=1G

```
### 1. Command install 

```terminal
chmod -R 777 storage/

composer update

#install npm
npm install
```

### 2. Make environment configuration  
```terminal
cp .env.example .env

cp .env.testing.example .env.testing

```

### 3. Configuration database connection in .env file
```terminal
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=my_db_name
DB_USERNAME=my_db_user
DB_PASSWORD=my_password
```

### 4. Add more variable in .env file
```terminal
MIX_BASE_API="/api"
MIX_LARAVEL_LANG="ja"
MIX_SHOW_LANG=false
MIX_STORE_IMAGE_URL= "${APP_URL}/storage/"
```

### 5. Migrate database and seeder

```terminal
php artisan key:generate

php artisan jwt:secret

php artisan l5-swagger:generate 

php artisan reload:cache

php artisan migrate

php artisan db:seed
```


### 6. Run project in localhost

```terminal
npm run prod
php artisan queue:w --timeout=0
```

### 7. Run test
```terminal
Before Unit test, setting enviroment test
--create file 'database.sqlite' in folder database

-For FE test
npm run test

After running the test, please refresh the DB to avoid heavy test data file and recreate the account. Because after each test run the user table will lose data.

command to fresh test DB: php artisan migrate:fresh --seed --env=testing 

#run unit test
-run all file Unit Test
php artisan test
-run one file unit test
php artisan test tests/Unit/Http/Controllers/+filename
example:php artisan test tests/Unit/Http/Controllers/UserTest.php

#install dusk test
php artisan dusk:install

# run IT test
-run one file
php artisan dusk tests/Browser/+filename
Example:php artisan dusk tests/Browser/UserTest.php

# run system test
php artisan dusk tests/Browser/Systems/SystemTest.php
```

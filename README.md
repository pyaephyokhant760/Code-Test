# career_Tech
## generate .env
copy .env.example and rename it .env

## install dependencies
```
composer install
```

## generate key
```
php artisan key:generate
```

# for migrate
```
 php artisan migrate
```
# Setting up for artisan cmd
# run project
```
 php artisan serve
 ```
# for seeding
```
 php artisan db:seed --class=UserSeeder
 php artisan db:seed --class=CompanySeeder
```

# test api in postman

# api endpoints of companies
```
 Get /api/v1/companys
 Post /api/v1/companys
 Get /api/v1/companys/{id}     
 Put /api/v1/companys/{id}
 Delete /api/v1/companys/{id}
 ```

# api endpoints of employees
```
 Get /api/v1/employees
 Post /api/v1/employees
 Get /api/v1/employees/{id}     
 Put /api/v1/employees/{id}
 Delete /api/v1/employees/{id}
 ```

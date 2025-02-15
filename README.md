<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Application Boot

```
cd cavu-api
```
```
cp .env.example .env
```
```
docker-compose up -d
```
```
docker-compose exec app composer install
```
```
docker-compose exec app php artisan key:generate
```

## Testing

```
docker-compose exec -it app bash
```
```
XDEBUG_MODE=coverage php artisan test --coverage
```
![img.png](img.png)

## API

```
docker-compose exec app php artisan migrate:fresh --seed
```

### Register

```
curl --location 'http://127.0.0.1/api/register' \
--form 'email="test.user@example.com"' \
--form 'password="password123"' \
--form 'name="test user"'
```
![img_1.png](img_1.png)
### Login
```
curl --location 'http://127.0.0.1/api/login' \
--form 'email="test.user@example.com"' \
--form 'password="password123"'
```
![img_2.png](img_2.png)
### Logout
```
curl --location --request POST 'http://127.0.0.1/api/logout' \
--header 'Authorization: Bearer 1|6NcxCEaItg3V5XhCaQabhzI8rYsNDwVKSg9jcKX822b0635d'
```
![img_3.png](img_3.png)
### Get all available spaces - no login required
```
curl --location 'http://127.0.0.1/api/bookings?start_at=2025-02-23&end_at=2025-03-28'
```
![img_4.png](img_4.png)
### Create the booking
```
curl --location 'http://127.0.0.1/api/bookings' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer 2|8Kp4l8lu2CSPQmFKFhoOKW31o3aHYxFcqEvBMq7P025e95a0' \
--data '{
    "start_at": "2025-02-20",
    "end_at": "2025-02-25"
}'
```
![img_5.png](img_5.png)
### To see created booking details
```
curl --location 'http://127.0.0.1/api/bookings/details' \
--header 'Authorization: Bearer 2|8Kp4l8lu2CSPQmFKFhoOKW31o3aHYxFcqEvBMq7P025e95a0'
```
![img_6.png](img_6.png)
### Update active booking
```
curl --location --request PUT 'http://127.0.0.1/api/bookings' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer 2|8Kp4l8lu2CSPQmFKFhoOKW31o3aHYxFcqEvBMq7P025e95a0' \
--data '{
    "start_at": "2025-02-21",
    "end_at": "2025-02-26"
}'
```
![img_7.png](img_7.png)
### Delete active booking
```
curl --location --request DELETE 'http://127.0.0.1/api/bookings' \
--header 'Authorization: Bearer 2|8Kp4l8lu2CSPQmFKFhoOKW31o3aHYxFcqEvBMq7P025e95a0'
```
![img_8.png](img_8.png)

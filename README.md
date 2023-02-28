# V3
## SET UP
1. docker-compose build
2. docker-compose up -d
3. docker-compose exec php cp .env.example .env
4. docker-compose exec php composer install
5. docker-compose exec nodejs npm install
6. docker-compose exec php php artisan key:generate

## CONFIG
- Open file hosts and add "127.0.0.1 admin.retailstudiov3.test" into it.

## access on Browser  
- http://admin.retailstudiov3.test:8082

## Database
- use your local  database for this project
- dump your local databae and import to the database 
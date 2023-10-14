1. ```copy .env.example .env```
2. ```docker-compose up -d```
3. Go to docker\
   ```docker-compose exec app bash```
4. Run inside docker\
   ```composer install```\
   Sometimes you can receive problem from google-api\
   ```rm -rf ~/.composer/cache```
5. ```php artisan migrate```
6. Exit from docker\
   ```exit```
7. Run from local environment\
   ```npm i```\
   ```npm run dev```
8. Configuration local environment\
   ```sudo nano /etc/hosts```\
   ```127.0.0.1 operatis-loc.ai```

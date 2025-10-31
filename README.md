# Laravel API Media store

Laravel application implementing:
A simple Laravel API that accepts image/video uploads, stores files on public,
creates a DB record, and returns metadata including file type, size, and a public URL.

## Requirements

- Used PHP v8.3.6 & Laravel 12.36.1
- Mysql 8+
- Composer

## Setup

1. Clone repo https://github.com/dusanCemovic/mediaStore
2. Composer:
   ```
    composer install
   ```
3. NPM:
   ```
    npm install & npm run build
   ```
4. COPY env example
   ```
    cp .env.example .env
   ```
5. You may:
    1. Just change database credentials for your mysql and run migration which will automatically create db
    2. Or do by yourself in console `mysql -u user -p` and `create database media_store` then change credentials in env
       file
6. Run:
   ```
    php artisan key:generate
   ```
7. Setup API token in your env file `API_TOKEN=XXX`
8. Run Migration:
    ```
    php artisan migrate
   ```
9. Create storage symlink so files are publicly accessible
    ```
    php artisan storage:link
    ```
10. Start Server:
    ```
    php artisan serve
    ```
11. Open `http://127.0.0.1:8000/api/ping` which will show that connection to api is enabled

## Notes

- Rules:
    - To use api for posting image and video, user need to have proper token
    - In API call you have to have:
        - Authorization
        - Title
        - Description
        - File url
- Other:
    - On Endpoint `api/upload-media`, you can post an image or video.
    - API returns the response: file type, size and public path to the file.
    - Image/Video is saved on public folder and in database, so it can be visible:
        - e.g. in db path column is: `media/your-video.webm`
        - so url is http://127.0.0.1:8000/storage/media/your-video.webm

## Descriptions

- MODEL:
    - `app/Models/Media` model which is just simple model with fillable attributes, without factory or seeder
- DB:
    - beside default migration for cache and jobs, we created media table `2025_10_30_162957_create_media_table` with
      information for storing image/video
- Middleware
    - `app/Http/Middleware/ApiTokenMiddleware` handle authorizations via Authorization or X-API-TOKEN
    - if everything is ok, it allows to go to request and controller
    - api route and this middleware is added by default for all api group in file bootstrap/api.php
      ```
      $middleware->api(append: [ApiTokenMiddleware::class]);
      ```
- Requests
    - `app/Http/Requests/StoreMediaRequest` extends default `Request` which gets all arguments for storing assets.
    - By default, it has `authorize` method, and it is set true, because real authorization goes via middleware
    - That class setup `rules` which needs to be fulfilled for arguments like title, description and file
- Controller:
    - `app/Http/Controller/MediaController` get request and handle it and return response
    - Controller just get file and send it to MediaService for handling saving.
    - At the end it returns json response based on result that MediaService done.
- Service:
    - `app/Http/Controller/MediaService` handle saving via method store
    - It is done like this to separate logic from controller and to be able to test easier. It is good concept to break
      this is smaller classes.
    - It returns Media object to controller
- Router
    - `routes/api.php` is used. Middleware is added by default for this group.
    - one route `ping` is created just for showcase and is it used without this middleware. This is done just for
      showcase.
    - This can be done differently by not adding middleware by default, but with new group.
- Test

## Posible improvements


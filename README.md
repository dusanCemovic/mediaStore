# Laravel API Media store

Laravel application implementing:
A simple Laravel API that accepts image/video uploads, stores files on public,
creates a DB record, and returns metadata including file type, size, and a public URL.

## Requirements

- Used PHP v8.3.6 & Laravel 12.36.1
- Mysql 8+
- Composer

## Setup

1. Clone repo https://github.com/dusanCemovic/mediaStore and go into folder `cd mediaStore`
2. Composer:
   ```
    composer install
   ```
3. NPM:
   ```
    npm install && npm run build
   ```
4. COPY env example
   ```
    cp .env.example .env
   ```
5. In env file you need to:
    1. Just change database credentials for your mysql and run migration which will automatically create db
    2. Or do by yourself in console `mysql -u user -p` login with your password and `create database media_store` then
       change credentials in env file
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
11. Open `http://127.0.0.1:8000/api/ping` which will show that connection to api is enabled (this api is excluded from
    auth)

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
    - `app/Models/Media` model which is just simple model with fillable attributes, without factory or seeder.
- DB:
    - beside default migration for cache and jobs, media table `2025_10_30_162957_create_media_table` is created with
      information for storing image/video.
- Middleware
    - `app/Http/Middleware/ApiTokenMiddleware` handle authorizations via Authorization or X-API-TOKEN.
    - if everything is ok, it allows to go to request and controller.
    - api route and this middleware is added by default for all api group in file bootstrap/api.php
      ```
      $middleware->api(append: [ApiTokenMiddleware::class]);
      ```
- Requests
    - `app/Http/Requests/StoreMediaRequest` extends default `Request` which gets all arguments for storing assets.
    - By default, it has `authorize` method, and it is set true, because real authorization goes via middleware.
    - That class setup `rules` which needs to be fulfilled for arguments like title, description and file.
- Controller:
    - `app/Http/Controller/MediaController` get request and handle it and return response.
    - Controller just get file from request and send it to MediaService for handling saving.
    - At the end it returns json response based on result that MediaService done.
    - Additionally, there is method ping (used for checking if api is live without api key) and list (listing all added media)
- Service:
    - `app/Http/Controller/MediaService` handle saving via method store.
    - It is done like this to separate logic from controller and to be able to test easier. It is good concept to break
      this is smaller classes.
    - It returns Media object to controller.
- Router
    - `routes/api.php` is used. Middleware is added by default for this group.
    - `api/upload-media` is used for adding image, which is main part of this project
    - `api/list` is just for showcase all added images with urls. It returns json with ids and urls
    - one route `ping` is created just for showcase and is it used without this middleware. This is done just for
      showcase.
    - This can be done differently by not adding middleware by default, but with new group.
- Test
    - For testing we have 8 tests in total. One class for Feature and three classes for Test units
    - `Feature/UploadMediaTest` - which simulate behavior from start to end which multi asserts (1)
    - `Unit/AuthTest` - with testing auth with token added in env file (2), invalid token (3) and without token (4)
    - `Unit/PostRequestTest` - with testing post request with missing title (5), invalid type (6), large file (7)
    - `Unit/MediaServiceTest` - with testing Media Service if it is handling well test file and return proper Media
      object (8)
- Other
  - Accepted Token is stored in config/auth/ `custom-api-key` 

## EXAMPLE:

### POST /api/upload-media

#### Headers

`Authorization: Bearer {API_TOKEN}`

#### Body

- title: string
- description: string
- file: image or video

#### Example

```
curl -X POST http://127.0.0.1:8000/api/upload-media \
-H "Authorization: Bearer {API_TOKEN}" \
-F "title=My Photo" \
-F "description=Uploaded via API" \
-F "file=@/home/dusan/Documents/test.png"
```
- In this example just change {API_TOKEN} with real one and be careful with route of your file.
- This is my route to Document folder in my Ubuntu

#### Example for listing with just urls

```
curl -X GET http://127.0.0.1:8000/api/list \
-H "Authorization: Bearer {API_TOKEN}"
```

## Possible improvements

- Always more tests
- For listing, create pagination
- Allow only to add via https and set this project on docker and allow https on local
- For security, it can be used some library like Laravel Sanctum.
- Media can be organized into folder or some specific structure.
- Thumbnail can be created when listing file on front-end if we used also web routes.
- Download endpoint can be created while listing on api call

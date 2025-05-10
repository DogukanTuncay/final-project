# Dockerize Laravel App

A simplified Docker Compose workflow that sets up a Laravel network of containers for local Laravel development with Adminer & PGAdmin.

## Usage

To get started, make sure you have [Docker installed](https://docs.docker.com/docker-for-mac/install/) on your system, and then clone this repo.

Next, navigate in your terminal to the directory you cloned this, and spin up the containers for the web server by running `docker compose up -d --build`.

After that completes, follow the steps from the [src/README.md](src/README.md) file to get your Laravel project added in (or create a new blank Laravel app).

**Note**: Your Postgres database host name should be `postgres`, **note** `localhost`. The username and database should both be `homestead` with a password of `secret`.

The following are built for our web server, with their exposed ports detailed:

-   **nginx** - `:80`
-   **postgres** - `:5432`
-   **php** - `:9000`
-   **redis** - `:6379`
-   **adminer** - `:8091`
-   **pgadmin** - `:8090`

Three additional containers are included that handle Composer, NPM, and Artisan commands _without_ having to have these platforms installed on your local computer. Use the following command examples from your project root, modifying them to fit your particular use case.

-   `docker compose run --rm composer install`
-   `docker compose run --rm npm run dev`
-   `docker compose run --rm artisan migrate`

## Makefile

There is a `makefile` which can help you to run every docker or artisan command easily. If you're not familiar with [GNU Makefile](https://www.gnu.org/software/make/manual/make.html) it's ok and you can still use this repository (even you can delete `makefile`), but with `makefile` you can manage different commands easier and better! Before using a `makefile` just install it from [GNU Makefile](https://www.gnu.org/software/make/manual/make.html) and run `make` command in repository root directory and you will see a help result to use it. some of `make` command example to simplify workflow:

```
# run docker compose up -d
make up

# run docker compose down --volumes
make down-volumes

# run migrations
make migrate

# run tinker
make tinker

# run artisan commands
make art db:seed
```

## Docker exec container

Access container as interactive shell and see output:

```
docker exec -it <container id> sh
```

Tip: You may use /bin/bash or just bash so after installing bash, you should inspect your image to understand CMD part and change current
option to whatever you want. For this purpose run:

```
docker inspect [imageID]
```

## Usage in Production

Tip: Don't forget to install and configure opcache

While I originally created this template for local development, it's robust enough to be used in basic Laravel application deployments. The biggest recommendation would be to ensure that HTTPS is enabled by making additions to the `nginx/default.conf` file and utilizing something like [Let's Encrypt](https://hub.docker.com/r/linuxserver/letsencrypt) to produce an SSL certificate.

## Compiling Assets

This configuration should be able to compile assets with both [laravel mix](https://laravel-mix.com/) and [vite](https://vitejs.dev/). In order to get started, you first need to add ` --host 0.0.0.0` after the end of your relevant dev command in `package.json`. So for example, with a Laravel project using Vite, you should see:

```json
"scripts": {
  "dev": "vite --host 0.0.0.0",
  "build": "vite build"
},
```

Then, run the following commands to install your dependencies and start the dev server:

-   `docker compose run --rm npm install`
-   `docker compose run --rm --service-ports npm run dev`

Want to build for production? Simply run `docker compose run --rm npm run build`.

# AI Chat İşleyiş Akışı

Davah uygulamasında, kullanıcı ile AI arasındaki chat işleyiş süreci aşağıdaki şekilde çalışmaktadır:

## Adım 1: Yeni Chat Oluşturma

- Kullanıcı yeni bir chat başlatır (AiChat modeli ile oluşturulur).
- Chat oluşturulduğunda bir başlık ve kullanıcı ID'si ile kaydedilir.

## Adım 2: Kullanıcı Prompt Gönderir

- Kullanıcı chat ekranında mesajını yazar ve gönderir.
- Frontend tarafından `/api/ai-chat-messages/send` endpoint'ine istek yapılır.
- İstek içeriği:
  ```json
  {
    "ai_chat_id": "<chat_id>",
    "message": "<kullanıcı_mesajı>"
  }
  ```

## Adım 3: Backend İşlem Süreci

1. **Güvenlik Kontrolü**: 
   - Kullanıcının mesajı yasaklı kelimeler ve içerik açısından kontrol edilir.
   - Mesaj uzunluğu limitleri kontrol edilir.

2. **Kullanıcı Mesajını Kaydetme**:
   - Kullanıcı mesajı veritabanına kaydedilir (`is_from_ai = false`).

3. **AI İşlem Süreci**:
   - Mesaj, geçmiş mesajlar ile birlikte AI API'sine gönderilir.
   - AI yanıtı alınır.
   - AI yanıtı veritabanına kaydedilir (`is_from_ai = true`).

4. **Yanıt Dönüş**:
   - Kullanıcı mesajı, AI yanıtı ve güncellenmiş mesaj geçmişi frontend'e döndürülür.

## Teknik Detaylar

### Endpoint: `/api/ai-chat-messages/send` (POST)

**İstek Body:**
```json
{
  "ai_chat_id": "<chat_id>",
  "message": "<kullanıcı_mesajı>"
}
```

**Başarılı Yanıt (200):**
```json
{
  "status": "success",
  "message": "AI yanıtı başarıyla alındı",
  "data": {
    "user_message": { /* kullanıcı mesajı bilgileri */ },
    "ai_message": { /* AI yanıtı bilgileri */ },
    "chat_history": [ /* Tüm mesaj geçmişi */ ]
  }
}
```

**Hata Yanıtı (400):**
```json
{
  "status": "error",
  "message": "<hata_mesajı>",
  "errors": { /* varsa hata detayları */ }
}
```

### Diğer Endpointler

- `GET /api/ai-chat-messages/chat/{chatId}`: Belirli bir chat'in tüm mesajlarını getirir
- `GET /api/ai-chat-messages/{id}`: Belirli bir mesajın detaylarını getirir
- `DELETE /api/ai-chat-messages/{id}`: Bir mesajı siler

### Güvenlik Kontrolleri

- Yasaklı kelimeler listesi ayarlardan alınır (`ai_banned_words`)
- Maksimum mesaj uzunluğu ayarlardan alınır (`ai_max_message_length`)
- Tüm istekler için JWT token ile authentication kontrolü yapılır

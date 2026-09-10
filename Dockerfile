FROM dunglas/frankenphp:php8.4

# Extensiile PHP de care are nevoie aplicatia
RUN install-php-extensions pdo_mysql intl opcache zip

# Composer, luat din imaginea lui oficiala
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Intai doar fisierele de dependinte: stratul asta ramane in cache
# cat timp composer.json/composer.lock nu se schimba
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --no-progress

# Apoi restul codului
COPY . .

# Autoloader-ul are nevoie de src/ ca sa mapeze toate clasele
RUN composer dump-autoload --no-dev --optimize --no-interaction

# Compileaza asset-urile AssetMapper in public/assets/.
# Valorile de mai jos sunt doar pentru aceasta comanda, nu raman in imagine —
# Symfony are nevoie ca variabilele sa existe ca sa porneasca, dar nu se
# conecteaza nicaieri.
RUN APP_ENV=prod \
    APP_DEBUG=0 \
    APP_SECRET=build \
    DATABASE_URL="mysql://u:p@127.0.0.1:3306/db?serverVersion=8.0" \
    MAILER_DSN=null://null \
    MAILER_FROM=build@example.com \
    MESSENGER_TRANSPORT_DSN=doctrine://default \
    DEFAULT_URI=http://localhost \
    php bin/console asset-map:compile

# Symfony scrie cache si loguri; uploads trebuie sa existe la pornire
RUN mkdir -p var/cache var/log public/uploads/ads public/uploads/profiles \
    && chmod -R 777 var public/uploads

# Configuratia serverului
COPY Caddyfile /etc/frankenphp/Caddyfile

EXPOSE 8080

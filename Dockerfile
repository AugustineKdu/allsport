FROM webdevops/php-nginx:8.2-alpine

# Install Node.js and npm
RUN apk add --no-cache nodejs npm

# Set working directory
WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy package files
COPY package.json package-lock.json ./
RUN npm install

# Copy application code
COPY . .

# Build frontend assets
RUN npm run build

# Create database file
RUN touch /app/database/database.sqlite

# Set permissions
RUN chown -R application:application /app \
    && chmod -R 755 /app/storage \
    && chmod -R 755 /app/bootstrap/cache \
    && chmod 664 /app/database/database.sqlite

# Setup Laravel
RUN if [ ! -f /app/.env ]; then cp /app/.env.example /app/.env; fi \
    && php artisan key:generate --force \
    && php artisan migrate --force \
    && php artisan db:seed --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 80
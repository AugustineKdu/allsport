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

# Setup Laravel - defer to runtime
RUN chmod +x /app/docker-entrypoint.sh 2>/dev/null || true

# Create entrypoint script
RUN echo '#!/bin/bash' > /app/docker-entrypoint.sh && \
    echo 'set -e' >> /app/docker-entrypoint.sh && \
    echo '' >> /app/docker-entrypoint.sh && \
    echo '# Setup Laravel environment' >> /app/docker-entrypoint.sh && \
    echo 'if [ ! -f /app/.env ]; then' >> /app/docker-entrypoint.sh && \
    echo '    cp /app/.env.example /app/.env' >> /app/docker-entrypoint.sh && \
    echo 'fi' >> /app/docker-entrypoint.sh && \
    echo '' >> /app/docker-entrypoint.sh && \
    echo '# Generate app key' >> /app/docker-entrypoint.sh && \
    echo 'php artisan key:generate --force' >> /app/docker-entrypoint.sh && \
    echo '' >> /app/docker-entrypoint.sh && \
    echo '# Create database if not exists' >> /app/docker-entrypoint.sh && \
    echo 'if [ ! -f /app/database/database.sqlite ]; then' >> /app/docker-entrypoint.sh && \
    echo '    touch /app/database/database.sqlite' >> /app/docker-entrypoint.sh && \
    echo '    chmod 664 /app/database/database.sqlite' >> /app/docker-entrypoint.sh && \
    echo 'fi' >> /app/docker-entrypoint.sh && \
    echo '' >> /app/docker-entrypoint.sh && \
    echo '# Run migrations' >> /app/docker-entrypoint.sh && \
    echo 'php artisan migrate --force' >> /app/docker-entrypoint.sh && \
    echo '' >> /app/docker-entrypoint.sh && \
    echo '# Seed database' >> /app/docker-entrypoint.sh && \
    echo 'php artisan db:seed --force' >> /app/docker-entrypoint.sh && \
    echo '' >> /app/docker-entrypoint.sh && \
    echo '# Clear and cache config' >> /app/docker-entrypoint.sh && \
    echo 'php artisan config:clear' >> /app/docker-entrypoint.sh && \
    echo 'php artisan cache:clear' >> /app/docker-entrypoint.sh && \
    echo 'php artisan view:clear' >> /app/docker-entrypoint.sh && \
    echo 'php artisan route:clear' >> /app/docker-entrypoint.sh && \
    echo '' >> /app/docker-entrypoint.sh && \
    echo '# Fix permissions' >> /app/docker-entrypoint.sh && \
    echo 'chown -R application:application /app/storage /app/bootstrap/cache /app/database' >> /app/docker-entrypoint.sh && \
    echo 'chmod -R 775 /app/storage /app/bootstrap/cache' >> /app/docker-entrypoint.sh && \
    echo 'chmod 664 /app/database/database.sqlite' >> /app/docker-entrypoint.sh && \
    echo '' >> /app/docker-entrypoint.sh && \
    echo '# Start supervisor' >> /app/docker-entrypoint.sh && \
    echo 'exec /usr/bin/supervisord -c /opt/docker/etc/supervisor.conf' >> /app/docker-entrypoint.sh && \
    chmod +x /app/docker-entrypoint.sh

CMD ["/app/docker-entrypoint.sh"]

EXPOSE 80
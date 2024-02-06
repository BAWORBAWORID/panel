# Use an appropriate base image for your application
FROM mariadb:10.5 AS database

# Set environment variables
ENV MYSQL_ROOT_PASSWORD="ROOTED"
ENV MYSQL_PASSWORD="ROOTED"
ENV MYSQL_DATABASE="panel"
ENV MYSQL_USER="pterodactyl"

# Copy initialization script
COPY init.sql /docker-entrypoint-initdb.d/

# Set up panel image
FROM ghcr.io/pterodactyl/panel:latest AS panel

# Set environment variables
ENV APP_URL="https://pterodactyl.example.com"
ENV APP_TIMEZONE="UTC"
ENV APP_SERVICE_AUTHOR="noreply@example.com"
ENV TRUSTED_PROXIES="*"
ENV MAIL_FROM="noreply@example.com"
ENV MAIL_DRIVER="smtp"
ENV MAIL_HOST="mail"
ENV MAIL_PORT="1025"
ENV MAIL_USERNAME=""
ENV MAIL_PASSWORD=""
ENV MAIL_ENCRYPTION="true"
ENV DB_PASSWORD="CHANGE_ME"
ENV APP_ENV="production"
ENV APP_ENVIRONMENT_ONLY="false"
ENV CACHE_DRIVER="redis"
ENV SESSION_DRIVER="redis"
ENV QUEUE_DRIVER="redis"
ENV REDIS_HOST="cache"
ENV DB_HOST="database"
ENV DB_PORT="3306"

# Expose ports
EXPOSE 80
EXPOSE 443

# Add volumes
VOLUME ["/srv/pterodactyl/var/", "/srv/pterodactyl/nginx/", "/srv/pterodactyl/certs/", "/srv/pterodactyl/logs/"]

# Start services
CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port", "80"]

services:
  pterodactyl:
    image: ghcr.io/pterodactyl/panel:latest
    restart: always
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - "/workspaces/workspace:/app/var/"
      - "/workspaces/workspace/nginx/:/etc/nginx/http.d/"
      - "/workspaces/workspace/certs/:/etc/letsencrypt/"
      - "/workspaces/workspace/logs/:/app/storage/logs"
    environment:
      APP_URL: "https://pterodactyl.example.com"
      APP_TIMEZONE: "UTC"
      APP_SERVICE_AUTHOR: "noreply@example.com"
      TRUSTED_PROXIES: "*"
      LE_EMAIL: ""
      MAIL_FROM: "noreply@example.com"
      MAIL_DRIVER: "smtp"
      MAIL_HOST: "mail"
      MAIL_PORT: "1025"
      MAIL_USERNAME: ""
      MAIL_PASSWORD: ""
      MAIL_ENCRYPTION: "true"
      DB_PASSWORD: *db-password
      APP_ENV: "production"
      APP_ENVIRONMENT_ONLY: "false"
      CACHE_DRIVER: "redis"
      SESSION_DRIVER: "redis"
      QUEUE_DRIVER: "redis"
      REDIS_HOST: "cache"
      DB_HOST: "database"
      DB_PORT: "3306"
    networks:
      - default
    depends_on:
      - database
      - cache
    links:
      - database
      - cache
  database:
    image: mariadb:10.5
    restart: always
    command: --default-authentication-plugin=mysql_native_password
    volumes:
      - "/workspaces/workspace/database:/var/lib/mysql"
    environment:
      <<: *db-environment
      MYSQL_DATABASE: "panel"
      MYSQL_USER: "pterodactyl"
    networks:
      - default
    depends_on:
      - cache
  cache:
    image: redis:alpine
    restart: always
    networks:
      - default
networks:
  default:
    ipam:
      config:
        - subnet: 172.20.0.0/16

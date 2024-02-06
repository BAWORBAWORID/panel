#!/bin/bash

# Lokasi file docker-compose.yml
DOCKER_COMPOSE_FILE="docker-compose.yml"

# Perintah untuk menjalankan docker-compose
DOCKER_COMPOSE_COMMAND="docker-compose -f $DOCKER_COMPOSE_FILE up -d"

# Fungsi untuk menjalankan perintah docker-compose
run_docker_compose() {
    $DOCKER_COMPOSE_COMMAND
    echo "Docker Compose has been executed."
}

# Menjalankan docker-compose saat skrip pertama kali dijalankan
run_docker_compose

# Loop utama
while true; do
    # Tidur selama 5 menit
    sleep 300

    # Menjalankan perintah docker-compose
    run_docker_compose
done

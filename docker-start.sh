#!/bin/bash
set -e

# Copy docker env if no .env exists
if [ ! -f .env ]; then
    cp .env.docker .env
    echo "Created .env from .env.docker"
fi

# Build and start containers
docker compose up -d --build

# Wait for app container to be ready
echo "Waiting for containers to start..."
sleep 5

# Generate app key if not set
if ! grep -q "APP_KEY=base64" .env; then
    docker compose exec app php artisan key:generate
fi

# Run migrations
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed

# Cache config for performance
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

echo ""
echo "App is running at http://localhost:8000"
echo "Run 'docker compose logs -f' to see logs"
echo "Enjoy"
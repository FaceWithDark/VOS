#!/bin/bash

# Set the environment (dev, stage, prod)
ENV=$1

if [ -z "$ENV" ]; then
  echo "No environment specified. Use 'dev', 'stage', or 'prod'."
  exit 1
fi

# Export the environment variable
export ENV

# Shutdown Docker Compose
echo "Shutting down Docker Compose for environment: $ENV"
docker-compose down

# Clean up unused Docker images
echo "Removing unused Docker images..."
docker image prune -f

# Clean up unused Docker volumes
echo "Removing unused Docker volumes..."
docker volume prune -f

# Clean up unused Docker networks
echo "Removing unused Docker networks..."
docker network prune -f

# Clean up unused Docker build cache
echo "Removing unused Docker build cache..."
docker builder prune -f

# Clean up all unused Docker objects (images, containers, volumes, and networks)
echo "Removing all unused Docker objects..."
docker system prune -af

# Remove all stopped containers
echo "Removing all stopped containers..."
docker container prune -f

# Remove all dangling images
echo "Removing all dangling images..."
docker image prune -f --filter "dangling=true"

# Remove all unused local volumes not used by at least one container
echo "Removing all unused local volumes..."
docker volume prune -f

echo "Docker session for environment $ENV has been shut down and thoroughly cleaned up."

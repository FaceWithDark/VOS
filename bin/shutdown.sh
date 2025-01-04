#!/bin/bash

# Set the environment (dev, stage, prod)
ENV=$1

if [ -z "$ENV" ]; then
  echo "Please use one of the following accepted environment names: 'dev', 'stage', and 'prod'!"
  exit 1
fi

# Define the docker-compose.env file based on the environment
ENV_FILE="../docker-compose.env-$ENV"

# Check if the docker-compose.env file exists
if [ ! -f "$ENV_FILE" ]; then
  echo "Requested environment file: $ENV_FILE not found! Consider create one on your own using the same file format and try again."
  exit 1
fi

# Load the environment variables from the docker-compose.env file
export $(grep -v '^#' "$ENV_FILE" | xargs)

# Validate required environment variables
ENV_VARS=("DATABASE_ROOT_PASSWORD" "DATABASE_NAME" "DATABASE_USER" "DATABASE_PASSWORD")
for env_var in "${ENV_VARS[@]}"; do
  if [ -z "${!env_var}" ]; then
    echo "Required environment variable: $var is not set!"
    exit 1
  fi
done

# Export the environment variable
export ENV

# Shutdown Docker Compose
echo "Shutting down Docker for '$ENV' stage..."
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

echo "Docker session for stage '$ENV' has been shut down and cleaned!"

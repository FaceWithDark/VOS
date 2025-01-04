#!/bin/bash

# Set the environment (dev, stage, prod)
ENV=$1

if [ -z "$ENV" ]; then
  echo "Please use one of the following accepted build phases: 'dev', 'stage', and 'prod'!"
  exit 1
fi

# Define Docker-needed configuration files based on the environment
ENV_FILE="../docker-compose.$ENV.env"
YAML_FILE="../docker-compose.$ENV.yaml"

# Make sure the script found .env file based on the environment
if [ ! -f "$ENV_FILE" ]; then
  echo "Requested environment file: $ENV_FILE not found! Consider create one on your own using the same file format and try again."
  exit 1
fi

# Make sure the script found .yaml file based on the environment
if [ ! -f "$YAML_FILE" ]; then
  echo "Requested YAML file: $YAML_FILE not found! Consider create one on your own using the same file format and try again."
  exit 1
fi

# Load needed variables from the environment file based on the environment
export $(grep -v '^#' "$ENV_FILE" | xargs)

# Validate required environment variables
ENV_VARS=("DATABASE_ROOT_PASSWORD" "DATABASE_NAME" "DATABASE_USER" "DATABASE_PASSWORD")
for env_var in "${ENV_VARS[@]}"; do
  if [ -z "${!env_var}" ]; then
    echo "Required environment variable: '$env_var' is not set!"
    exit 1
  fi
done

# Export the environment variable
export ENV

# Shutdown Docker Compose
echo "Shutting down Docker for '$ENV' phase..."
docker-compose -f "$YAML_FILE" down

# Clean up unused Docker images
echo "Removing unused Docker images..."
docker image prune --force

# Clean up unused Docker volumes
echo "Removing unused Docker volumes..."
docker volume prune --force

# Clean up unused Docker networks
echo "Removing unused Docker networks..."
docker network prune --force

# Clean up unused Docker build cache
echo "Removing unused Docker build cache..."
docker builder prune --force

# Clean up all unused Docker objects (images, containers, volumes, and networks)
echo "Removing all unused Docker objects..."
docker system prune -a --force

# Remove all stopped containers
echo "Removing all stopped containers..."
docker container prune --force

# Remove all dangling images
echo "Removing all dangling images..."
docker image prune --force --filter "dangling=true"

# Remove all unused local volumes not used by at least one container
echo "Removing all unused local volumes..."
docker volume prune --force

echo "Docker session for phase '$ENV' has been shut down and cleaned!"

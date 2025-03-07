#!/bin/bash

BUILD_STAGE=$1

ENV_FILE="docker.$BUILD_STAGE.env"
YAML_FILE="docker-compose.$BUILD_STAGE.yaml"

# Make sure all minimum required files are found
if [ -z "$BUILD_STAGE" ]; then
    echo "Invalid build stage found! Please use one of the following build stage: <dev>, <stage>, and <prod>"
    exit 1
else
  echo "Valid build stage found! Process to the next step..."
fi

if [ ! -f "$ENV_FILE" ]; then
  echo "Invalid .env file found! Consider create one on your own based on the repository's file format and try again."
  exit 1
else
  echo "Valid .env file found! Process to the next step..."
fi

if [ ! -f "$YAML_FILE" ]; then
  echo "Invalid .yaml file found! Consider create one on your own based on the repository's file format and try again."
  exit 1
else
  echo "Valid .yaml file found! Process to the next step..."
fi

export BUILD_STAGE

# Fully shutdown Docker Compose usages
echo "Shutting down Docker for <$BUILD_STAGE> phase..."
docker-compose -f "$YAML_FILE" --env-file "$ENV_FILE" down

echo "Removing unused Docker images..."
docker image prune --force

echo "Removing unused Docker volumes..."
docker volume prune --force

echo "Removing unused Docker networks..."
docker network prune --force

echo "Removing unused Docker build cache..."
docker builder prune --force

echo "Removing all unused Docker objects..."
docker system prune -a --force

echo "Removing all stopped containers..."
docker container prune --force

echo "Removing all dangling images..."
docker image prune --force --filter "dangling=true"

echo "Removing all unused local volumes..."
docker volume prune --force

echo "Completed shutting down Docker session for <$BUILD_STAGE> phase."

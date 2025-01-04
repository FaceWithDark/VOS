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

echo "Docker session for phase '$ENV' has been shut down! Waiting for rebuild..."

# Run Docker Compose with the specified environment
echo "Rebuild for '$ENV' phase..."
docker-compose -f "$YAML_FILE" up --build -d

echo "Rebuild successfully for '$ENV' phase!"

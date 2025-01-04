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

# Run Docker Compose with the specified environment
echo "Setting up Docker for '$ENV' stage..."
docker-compose up --build -d

echo "Setting up successfully for '$ENV' stage!"

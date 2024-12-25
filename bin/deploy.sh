#!/bin/bash

# Set the environment (dev, stage, prod)
ENV=$1

if [ -z "$ENV" ]; then
  echo "No environment specified. Use 'dev', 'stage', or 'prod'."
  exit 1
fi

# Define the .env file based on the environment
ENV_FILE=".env.$ENV"

# Check if the .env file exists
if [ ! -f "$ENV_FILE" ]; then
  echo "Environment file $ENV_FILE not found."
  exit 1
fi

# Load the environment variables from the .env file
export $(grep -v '^#' "$ENV_FILE" | xargs)

# Validate required environment variables
REQUIRED_VARS=("DATABASE_NAME" "DATABASE_USER" "DATABASE_PASSWORD" "DATABASE_ROOT_PASSWORD")
for var in "${REQUIRED_VARS[@]}"; do
  if [ -z "${!var}" ]; then
    echo "Required environment variable $var is not set."
    exit 1
  fi
done

# Export the environment variable
export ENV

# Run Docker Compose with the specified environment
echo "Deploying for environment: $ENV"
docker-compose up -d --build

echo "Deployed successfully for environment: $ENV"

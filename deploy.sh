#!/bin/bash

# Set the environment (dev, stage, prod)
ENV=$1

if [ -z "$ENV" ]; then
  echo "No environment specified. Use 'dev', 'stage', or 'prod'."
  exit 1
fi

# Export the environment variable
export ENV

# Run Docker Compose with the specified environment
docker-compose up -d --build

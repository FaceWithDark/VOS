#!/bin/bash

BUILD_STAGE=${1,,}

if [ -z "$BUILD_STAGE" ]; then
    echo 'Invalid build stage provided, please use one of the following option: [dev], [stage], [prod]!!'
    exit 1
fi

case $BUILD_STAGE in
    dev|stage|prod)
        echo "[$BUILD_STAGE] build stage found! Move on to the next step..."
        ;;
    *)
        echo 'Invalid build stage provided, please use one of the following option: [dev], [stage], [prod]!!'
        exit 1
        ;;
esac


YAML_FILE="docker-compose.$BUILD_STAGE.yaml"

if [ ! -f "$YAML_FILE" ]; then
    echo "Missing <$YAML_FILE> file!! Please double check with the setup guide (SETUP.md) again."
    exit 1
else
    echo "Found <$YAML_FILE> file!! Move on to the next step..."
fi


ENV_FILE="docker.$BUILD_STAGE.env"

if [ ! -f "$ENV_FILE" ]; then
    echo "Missing <$ENV_FILE> file!! Please double check with the setup guide (SETUP.md) again."
    exit 1
else
    echo "Found <$ENV_FILE> file!! Move on to the next step..."
fi


echo '==========================================================='
echo "Rebuild docker containers for [$BUILD_STAGE] build stage..."
echo '==========================================================='


# Stop all running Docker containers first
echo "Turn off running docker containers for [$BUILD_STAGE] build stage..."
sudo docker compose --file "$YAML_FILE" --env-file "$ENV_FILE" down

# Rebuild to apply new changes
echo "Rebuild for [$BUILD_STAGE] build stage..."

sudo docker compose --file "$YAML_FILE" --env-file "$ENV_FILE" up --build -d
echo "Finish rebuild docker containers for [$BUILD_STAGE] build stage."

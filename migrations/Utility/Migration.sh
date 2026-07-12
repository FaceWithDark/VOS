#!/bin/sh

# NOTE:
# On production:
# 1. Restrict to hosting platform IP and remove `--allow-all-ip` flag
# 2. Generate SQL files and execute manually to ensure attackers not assuming we
#    run migration scripts by default
symfony console doctrine:migrations:migrate --no-interaction
symfony console doctrine:migrations:execute "$PWD/migrations/Version20260712010015.php" --up --no-interaction
symfony --allow-all-ip local:server:start

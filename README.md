<div align="center" width="100%">
    <h1>VOS<br />-<br />Vietnamese Osu!taiko Showdown</h1>
    <img
        src="assets/trademarks/trademark-fallback.png"
        alt="VOS Trademark Logo"
        srcset="assets/trademarks/trademark.svg"
        width="25%"
        height="auto"
    />
    <br />
    <strong>This is the source code for <a href="https://vososu.site">VOS website</a>, built by a <i>mentally unstable uni student</i></strong>
</div>

---
# Quick Start

```bash
# Clone this repo
git clone https://github.com/FaceWithDark/VOS.git   # HTTPS method
git clone git@github.com:FaceWithDark/VOS.git       # SSH method

# Create required directory for extra Docker setup (for db & GUI credentials)
mkdir -p docker/secrets

# Copy example files to newly created directory and remove the `.example` suffix
cp ./examples/postgres_*.example.txt ./docker/secrets/postgres_*.txt
cp ./examples/pgadmin_*.example.txt ./docker/secrets/pgadmin_*.txt

# Copy non-sensitive `.env` files to project root directory and remove the `.example` suffix
cp ./examples/.env.example.* ./.env.*

# Create another required directory for extra Docker setup (for preset db servers connection)
mkdir -p docker/configs

# Copy preset files to newly created directory and remove the `.example` suffix
cp ./examples/pgadmin_*.example.json ./docker/configs/pgadmin_*.json


## Use the default value or modify it (if needed) ##


# Then, start building all Docker services
docker compose up --build -d
```


Once the containers are running, you can verify that all services are working correctly by these way:

1. **Symfony**: [localhost:8001](http://localhost:8001/)
2. **Postgres**:
- Direct access is disable by default for security reasons. However, you can still do it by typing:

```bash
docker exec -it vos-postgres psql -U demo -d postgres

# Please enter the database password (read from `postgres_db.txt` file) here if
# there is a prompt asking you to do it
```

> [!TIP]
> You can also go to **Docker Desktop**, search for `vos-postgres` container
> (under `vos` project), click on it and then click on **Terminal** icon near
> top right corner.

- A successful **PostgreSQL connection** would look like below:

> [!NOTE]
> `psql` version number and text format may vary depending on the system you are running on.

```txt
psql (18.3)
Type "help" for help.

postgres=#
```
3. **pgAdmin**: [localhost:5051](http://localhost:5051/)

---
# Interactions with APIs

Please head to [localhost:8001/api](http://localhost:8001/api) to play around with all available APIs in this project.

---
# Testing

To run test file under `/tests` directory, please follow these steps:

```bash
# Get inside `vos-symfony` Docker container
docker exec -it vos-symfony sh

# Run the `phpunit` binary file to do different kinds of testing techniques
php bin/phpunit tests/
```

---
# Conventions

> [!TIP]
> You know it's a good and well maintained project if they've a dedicated section for these kind of stuffs.

There're many conventions that this project followed to ensure that it's not a big of a burden for someone else (e.g., current devs, maintainers, reviewers, etc) when they've to touch on things that almost no one willing to do it. If you want to be a good contributor/dev, please take a look and follow them accordingly here:

1. [**Git Commit Convention**](./docs/conventions/GIT.md)
2. [**Domain Specificity Convention**](./docs/conventions/TRAEFIK.md)

---
# Contributing

Got bug fixes, new features, or ideas? We appreciate the help! Before you open an **Issue/PR**, please take a read at our [Contributing Guidelines](CONTRIBUTING.md) to learn how to properly use **GitHub template files** customised for this project.

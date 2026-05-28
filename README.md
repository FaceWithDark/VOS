<div align="center" width="100%">
    <h1>VOS<br />-<br />Vietnamese Osu!taiko Showdown</h1>
    <img src="assets/imgs/VOT.webp" alt="VOT Image" width="50%" height="auto">
    <br />
    <strong>This is the source code for <a href="https://vososu.site">VOS website</a>, built by a <i>mentally unstable uni student</i></strong>
</div>

# Quick Start

```bash
# Clone this repo
git clone https://github.com/FaceWithDark/VOS.git   # HTTPS method
git clone git@github.com:FaceWithDark/VOS.git       # SSH method

# Create required directory for extra Docker setup
mkdir -p docker/secrets

# Copy example files to newly created directory and remove the `.example` suffix
cp ./examples/postgres_*.example.txt ./docker/secrets/postgres_*.txt

# Use the default value or modify it (if needed)

# Then, start building all Docker services
docker compose up --build -d
```


Once the containers are running, you can verify that all services are working correctly by these way:

1. **Symfony**: [localhost:8001](http://localhost:8001/)
2. **Postgres**:
- Direct access is disable by default for security reasons. However, you can still do it by typing:

```bash
docker exec -it vos-postgres psql -U demo

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

demo=#
```

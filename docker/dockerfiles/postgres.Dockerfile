FROM postgres:alpine AS base


# NOTE:
# '127.0.0.1' forces a TCP check: during first-run initialization the image's
# temporary server listens on the Unix socket only, so a socket check would
# report ready before the real server accepts connections. Credentials come
# from the compose secrets, so the check does not log auth failures.
HEALTHCHECK --interval=5s --timeout=5s --retries=10 --start-period=10s \
	CMD pg_isready -h 127.0.0.1 -U "$(cat /run/secrets/usr.txt)" -d "$(cat /run/secrets/db.txt)"


EXPOSE 5432/tcp

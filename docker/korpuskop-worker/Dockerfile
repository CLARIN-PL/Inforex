FROM debian:bookworm-slim

RUN apt-get update -y \
    && apt-get install -y python3 ca-certificates \
    && rm -rf /var/lib/apt/lists/*

COPY docker/www/korpuskop-runtime /opt/korpuskop
COPY docker/korpuskop-worker/worker.py /opt/korpuskop-worker/worker.py

RUN chmod +x /opt/korpuskop/bin/korpuskop \
    && mkdir -p /opt/korpuskop/var/output /opt/korpuskop/var/progress /opt/korpuskop-worker

EXPOSE 8090

CMD ["python3", "/opt/korpuskop-worker/worker.py"]

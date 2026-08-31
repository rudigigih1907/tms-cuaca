FROM mysql:8.4

ENV TZ=Asia/Jakarta

COPY backup-db.sh /usr/local/bin/backup-db.sh
COPY backup-upload.sh /usr/local/bin/backup-upload.sh

RUN chmod +x /usr/local/bin/backup-db.sh \
    && chmod +x /usr/local/bin/backup-upload.sh

CMD ["sleep", "infinity"]
FROM ubuntu

RUN apt update && apt install -y openjdk-11-jre

WORKDIR /srv

COPY target/crawler-1.0-SNAPSHOT.jar /srv/run.jar

COPY entry.sh /srv/entry.sh
COPY config.sh /srv/config.sh

ENTRYPOINT [ "bash", "/srv/entry.sh" ]
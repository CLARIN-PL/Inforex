FROM eclipse-temurin:11-jdk

WORKDIR /workdir

RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    wget \
    && rm -rf /var/lib/apt/lists/*

RUN wget https://github.com/liquibase/liquibase/releases/download/liquibase-parent-3.5.5/liquibase-3.5.5-bin.tar.gz
RUN tar -xvf liquibase-3.5.5-bin.tar.gz
RUN chmod a+x liquibase

RUN wget https://repo1.maven.org/maven2/mysql/mysql-connector-java/5.1.49/mysql-connector-java-5.1.49.jar

COPY liquibase.properties /workdir
COPY run.sh /workdir

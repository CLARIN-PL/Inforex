FROM openjdk:8

WORKDIR /workdir

RUN apt-get install unzip -y
RUN apt-get update && apt-get install -y 

RUN wget https://github.com/liquibase/liquibase/releases/download/v4.15.0/liquibase-4.15.0.tar.gz
RUN tar -xvf liquibase-4.15.0.tar.gz
RUN chmod a+x liquibase

RUN wget https://repo1.maven.org/maven2/mysql/mysql-connector-java/8.0.30/mysql-connector-java-8.0.30.jar

COPY liquibase.properties /workdir
COPY run.sh /workdir

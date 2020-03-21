FROM openjdk:8

WORKDIR /workdir

RUN apt-get install unzip -y
RUN apt-get update && apt-get install -y netcat

RUN wget https://github.com/liquibase/liquibase/releases/download/liquibase-parent-3.5.5/liquibase-3.5.5-bin.tar.gz
RUN tar -xvf liquibase-3.5.5-bin.tar.gz
RUN chmod a+x liquibase

RUN wget http://www.java2s.com/Code/JarDownload/mysql/mysql-connector-java-5.1.21.jar.zip
RUN unzip mysql-connector-java-5.1.21.jar.zip

COPY liquibase.properties /workdir
COPY run.sh /workdir
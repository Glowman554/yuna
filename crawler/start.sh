source config.sh

export THREADS=8
export INITIAL=https://glowman554.de/,https://adrian-cxll.github.io/,http://www.lowlevel.eu/wiki/Hauptseite/,https://nickreutlinger.de/

screen -dmS crawler mvn compile exec:java
screen -r crawler

export DB=yuna
export DB_USER=yuna
export DB_PASSWORD=eifzhqviupehfvrupiqehvupqehrhpqeriuphvqiuperh
export DB_URL=cloud.glowman554.de
export THREADS=4

export INITIAL=https://glowman554.de/,https://adrian-cxll.github.io/,http://www.lowlevel.eu/wiki/Hauptseite/,https://nickreutlinger.de/

screen -dmS crawler mvn compile exec:java
screen -r crawler
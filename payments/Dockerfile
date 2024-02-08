FROM glowman554/deno

WORKDIR /srv
COPY . .

RUN deno cache index.ts

ENTRYPOINT [ "deno", "run", "-A", "index.ts" ]
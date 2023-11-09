FROM glowman554/deno

COPY . /src/
RUN deno cache /src/index.ts

ENTRYPOINT [ "deno", "run", "-A", "/src/index.ts", "/config/" ]
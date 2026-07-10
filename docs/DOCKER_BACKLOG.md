# Backlog de Dockerización — IoT_Project

> **Este archivo es para un agente de terminal (OpenCode / Claude Code / similar) que tiene
> acceso directo al repositorio.** Está escrito como instrucciones de trabajo, no como
> documentación de referencia. Si eres un humano leyendo esto: es el plan de ejecución que le
> vamos a dar al agente, revísalo y ajústalo antes de correrlo.

---

## 0. Contexto para el agente (LEE ESTO ANTES DE TOCAR NADA)

Este es **IoT_Project**, un sistema Laravel 12 (PHP 8.2) para monitoreo/control de dispositivos
IoT (ESP32). El objetivo de esta tarea es **contenerizar todo el stack con Docker** para
desplegarlo en un servidor de una escuela (proyecto de servicio social).

Stack real (confirmado por documentación del proyecto, verificar contra el código):

| Componente | Tecnología | Notas |
|---|---|---|
| Backend | Laravel 12.x, PHP >= 8.2 | Blade + Controllers, sin API REST completa (solo endpoints puntuales) |
| Frontend build | Node.js >= 18, npm | Probablemente Vite (verificar `package.json`) |
| DB relacional | MySQL/MariaDB >= 10.4 | Users, ubicaciones, maestros, esclavos, componentes, lecturas (buffer) |
| DB temporal | InfluxDB 2.x | Bucket `biobit`, telemetría de sensores, escrita **directo por los ESP32**, sin pasar por Laravel |
| Broker | Eclipse Mosquitto | Puerto 1883, control de actuadores vía tópicos `v1/usuarios/{id}/nodos/{serie}/{campo}` |
| Auth | Laravel Auth nativo (sesiones en MySQL) | Roles `Admin` / `Usuario` vía middleware `Checkrol` |

Documentación completa del proyecto (léela si necesitas más contexto):
`ARQUITECTURA.md`, `API.md`, `INSTALACION.md`, `MANUAL_USUARIO.md`, `CONTRIBUCION.md`
(deberían estar en la raíz o en `docs/` del repo — si no están, pregunta al usuario dónde están).

### Decisiones de arquitectura ya tomadas (no las reabras, ejecútalas)

- El servidor de destino **tiene Docker instalado**, con acceso root/sudo probable, y **es
  accesible tanto desde LAN interna como potencialmente desde internet** — por lo tanto los
  puertos de MQTT (1883) e InfluxDB (8086) **deben poder exponerse al host**, no solo a la red
  interna de Docker.
- MySQL/MariaDB **nunca se expone al host**. Solo lo consume el contenedor `app` vía red interna
  de Docker.
- Todos los puertos expuestos (`nginx`, `mosquitto`, `influxdb`) se parametrizan por variables de
  entorno en `.env`, no se hardcodean en el `docker-compose.yml`.
- Se preparará la arquitectura para poder añadir TLS más adelante (Traefik o certbot) sin
  reescribir el compose — pero **no lo implementes todavía**, eso es una fase futura marcada
  como tal más abajo.
- Imagen de `app` en **multi-stage build**: build de Composer + build de Node en etapas
  separadas, imagen final solo con runtime (sin `node_modules`, sin `.git`, sin devDependencies).
- PHP-FPM corre como **usuario no-root**.
- Persistencia con **volúmenes nombrados**, nunca bind mounts hacia el filesystem del contenedor
  para datos (sí bind mount de código en modo desarrollo si aplica).

### Reglas de trabajo (respétalas en todas las fases)

1. **Audita antes de generar.** No asumas contenido de `composer.json`, `package.json`,
   `.env.example` ni de la carpeta `docker/` — léelos primero. Si algo en este backlog no
   coincide con lo que encuentres en el repo (ej. otra versión de PHP, otro bundler), avisa y
   ajusta la tarea, no la ignores.
2. **No toques lógica de negocio.** Nada de tocar controladores, modelos, rutas, vistas Blade,
   ni modificar la estructura de `app/`. Esta tarea es 100% infraestructura.
3. **Commits atómicos por tarea.** Un commit por cada tarea numerada de este backlog (o al
   menos por fase), con mensaje siguiendo la convención de `CONTRIBUCION.md`
   (`feat(docker): ...`, `chore(docker): ...`).
4. **Cada tarea tiene un criterio de aceptación.** No la marques como hecha hasta que el
   criterio se cumpla y lo hayas verificado tú mismo con el comando indicado.
5. **No inventes credenciales ni secretos.** Usa placeholders en `.env.example`, nunca valores
   reales en archivos versionados.
6. **Si una tarea depende de una decisión que no está en este documento, detente y pregunta**
   en lugar de asumir (ejemplo: si no hay `package.json`/no hay build de frontend, no inventes
   un stage de Node que no aplica).

---

## 1. Fase 0 — Auditoría del repositorio

- [ ] **0.1** Leer `composer.json` y confirmar versión exacta de PHP y extensiones requeridas.
      *Criterio:* documentar en un comentario del PR/commit qué versión real se usará en el
      Dockerfile (`php:X.Y-fpm-alpine`).
- [ ] **0.2** Leer `package.json` y confirmar bundler (Vite/Mix) y comando de build.
      *Criterio:* confirmar si es `npm run build` (Vite) u otro.
- [ ] **0.3** Revisar si ya existe `Dockerfile`, `docker-compose.yml` o carpeta `docker/` en el
      repo (se menciona `docker/mosquitto/config` en `INSTALACION.md`).
      *Criterio:* listar qué existe y decidir si se reutiliza o se reemplaza.
- [ ] **0.4** Revisar `.env.example` actual y listar todas las variables existentes.
      *Criterio:* tener el inventario completo antes de escribir el nuevo `.env.example`.
- [ ] **0.5** Confirmar si el proyecto usa colas (`queue`), `schedule:run`, websockets (Reverb/
      Pusher) o algún otro proceso en background además de `php artisan serve`.
      *Criterio:* si no hay evidencia de colas/cron, se omite el servicio `scheduler` de este
      backlog (no se crea "por si acaso").

---

## 2. Fase 1 — Dockerfile de la aplicación (Laravel)

- [ ] **1.1** Crear `Dockerfile` en la raíz con 3 etapas:
  1. `composer` (imagen `composer:2` — instala dependencias PHP sin dev en prod)
  2. `node` (imagen `node:18-alpine` o superior según 0.2 — build de assets)
  3. `runtime` (imagen `php:X.Y-fpm-alpine` — copia vendor/ y public/build de las etapas
     anteriores, instala solo extensiones necesarias: `pdo_mysql`, `openssl`, `mbstring`,
     `xml`, `bcmath`, `gd` o similares según lo auditado en 0.1)
  *Criterio de aceptación:* `docker build -t iot-project-app .` termina sin errores y
  `docker images` muestra la imagen final por debajo de ~250MB.
- [ ] **1.2** Configurar el contenedor para correr `php-fpm` como usuario no-root
      (crear usuario `www` uid 1000, ajustar permisos de `storage/` y `bootstrap/cache/`).
      *Criterio:* `docker run --rm iot-project-app whoami` no devuelve `root`.
- [ ] **1.3** Crear `.dockerignore` (excluir `node_modules`, `.git`, `vendor` local, `.env`,
      `storage/logs/*`, `storage/framework/cache/*`).
      *Criterio:* build no copia esos directorios (`docker history` o build más rápido/liviano).
- [ ] **1.4** Crear script de entrypoint (`docker/entrypoint.sh`) que:
      - Espere a que MySQL esté disponible antes de arrancar PHP-FPM (usar el healthcheck de
        compose, no un `sleep` fijo — ver Fase 3).
      - Ejecute `php artisan config:cache`, `route:cache`, `view:cache` solo en modo producción.
      - Ejecute `php artisan migrate --force` de forma condicional (variable `RUN_MIGRATIONS=true`).
      *Criterio:* contenedor arranca limpio con `docker compose up app` y el log muestra los
      pasos anteriores en orden.

---

## 3. Fase 2 — Nginx

- [ ] **2.1** Crear `docker/nginx/default.conf` sirviendo `public/` y haciendo proxy a
      `app:9000` (fastcgi).
      *Criterio:* `curl http://localhost:$NGINX_PORT/` devuelve la página de login (HTTP 200).
- [ ] **2.2** Configurar límites razonables (`client_max_body_size` para subida de imagen de
      perfil, `fastcgi_read_timeout` acorde a reportes que consultan InfluxDB).
      *Criterio:* subir una imagen de perfil de prueba (~5MB) no falla por límite de nginx.

---

## 4. Fase 3 — docker-compose.yml (base)

- [ ] **3.1** Crear `docker-compose.yml` con servicios: `app`, `webserver` (nginx), `mysql`,
      `influxdb`, `mosquitto`. Red interna dedicada (`iot_network`, driver `bridge`).
      *Criterio:* `docker compose config` valida sin errores.
- [ ] **3.2** Definir volúmenes nombrados: `mysql_data`, `influxdb_data`, `mosquitto_data`,
      `mosquitto_log`, `storage_data` (mapeado a `storage/app/public` para imágenes de perfil
      y exports de reportes).
      *Criterio:* `docker compose down && docker compose up` conserva los datos (probar
      creando un registro, bajando y subiendo el stack).
- [ ] **3.3** Healthchecks:
      - `mysql`: `mysqladmin ping`
      - `influxdb`: `curl -f http://localhost:8086/health` (ver Fase 5)
      - `mosquitto`: verificar proceso escuchando en 1883
      - `app`: depende de `mysql` con `condition: service_healthy` (no solo `depends_on` simple)
      *Criterio:* `docker compose up`, y `app` no intenta migrar hasta que `mysql` reporte
      `healthy` (verificar en logs, sin errores de "Connection refused").
- [ ] **3.4** Exponer puertos parametrizados desde `.env`:
      ```yaml
      ports:
        - "${NGINX_PORT:-80}:80"
      ```
      aplicar el mismo patrón a `mosquitto` (`${MQTT_PORT:-1883}`) e `influxdb`
      (`${INFLUXDB_PORT:-8086}`).
      *Criterio:* cambiar `NGINX_PORT=8080` en `.env` y levantar el stack sin tocar el compose.
- [ ] **3.5** Crear `docker-compose.override.yml` (desarrollo local): monta el código como bind
      mount, corre `npm run dev` en un servicio adicional `vite`, expone puertos de debug.
      *Criterio:* `docker compose up` (sin `-f prod`) permite editar código y ver cambios sin
      rebuild de imagen.
- [ ] **3.6** Crear `docker-compose.prod.yml`: sin bind mounts de código, `restart: unless-stopped`
      en todos los servicios, sin exponer puertos de debug.
      *Criterio:* `docker compose -f docker-compose.yml -f docker-compose.prod.yml config`
      no incluye ningún bind mount de código fuente.

---

## 5. Fase 4 — Mosquitto

- [ ] **4.1** Crear/adaptar `docker/mosquitto/config/mosquitto.conf` (ya se menciona en
      `INSTALACION.md`, revisar si existe y reutilizar).
      *Criterio:* contenedor mosquitto arranca y `mosquitto_sub`/`mosquitto_pub` de prueba
      funcionan contra el puerto expuesto.
- [ ] **4.2** Decidir y documentar (no implementar aún, solo dejar comentado/preparado):
      autenticación de Mosquitto (`allow_anonymous true` es aceptable solo si el broker no está
      expuesto a internet sin restricción — marcar como ítem de seguridad pendiente si el
      servidor resulta tener IP pública abierta).
      *Criterio:* queda un comentario `# TODO seguridad` en el conf si se deja anónimo.

---

## 6. Fase 5 — InfluxDB

- [ ] **5.1** Configurar el servicio `influxdb` en modo de inicialización automática usando las
      variables `DOCKER_INFLUXDB_INIT_*` de la imagen oficial (usuario, password, org, bucket
      `biobit`, token) en vez de configurarlo a mano vía UI como describe `INSTALACION.md`.
      *Criterio:* al primer `docker compose up`, `curl http://localhost:$INFLUXDB_PORT/health`
      responde `{"status":"pass"}` sin pasos manuales.
- [ ] **5.2** Verificar que el token generado automáticamente se pueda inyectar al `.env` de
      Laravel (puede requerir un script que lo lea del volumen de influxdb en el primer arranque
      y lo escriba en `.env`, o fijarlo con `DOCKER_INFLUXDB_INIT_ADMIN_TOKEN` explícito).
      *Criterio:* Laravel puede autenticarse contra InfluxDB sin configuración manual post-deploy.

---

## 7. Fase 6 — Variables de entorno

- [ ] **6.1** Actualizar `.env.example` agregando (sin quitar lo existente, auditado en 0.4):
      ```env
      # Puertos expuestos al host
      NGINX_PORT=80
      MQTT_PORT=1883
      INFLUXDB_PORT=8086

      # InfluxDB init (contenedor)
      DOCKER_INFLUXDB_INIT_USERNAME=admin
      DOCKER_INFLUXDB_INIT_PASSWORD=changeme
      DOCKER_INFLUXDB_INIT_ORG=iot_project
      DOCKER_INFLUXDB_INIT_BUCKET=biobit
      DOCKER_INFLUXDB_INIT_ADMIN_TOKEN=changeme_generate_a_real_token

      # Control de migraciones automáticas en el entrypoint
      RUN_MIGRATIONS=true
      ```
      *Criterio:* `.env.example` no contiene ningún valor real/secreto, solo placeholders.
- [ ] **6.2** Confirmar que `DB_HOST`, `MQTT_HOST`, `INFLUXDB_URL` en `.env.example` apunten a
      los **nombres de servicio de Docker** (`mysql`, `mosquitto`, `influxdb`) y no a
      `127.0.0.1` como está hoy documentado para el entorno sin Docker.
      *Criterio:* Laravel dentro del contenedor `app` se conecta usando esos hostnames, no IPs.

---

## 8. Fase 7 — Validación end-to-end

- [ ] **7.1** `docker compose up --build` desde cero (sin volúmenes previos) levanta los 5
      servicios sin errores.
- [ ] **7.2** `docker compose exec app php artisan migrate --seed` corre sin errores.
- [ ] **7.3** Login con credenciales de `admin@admin.com` / `admin` funciona vía nginx.
- [ ] **7.4** `POST /api/lecturas` (simulando un ESP32 con `curl`) inserta correctamente y no
      da error 400/500.
- [ ] **7.5** Publicar un mensaje MQTT de prueba contra el broker expuesto y confirmar que
      Laravel/el navegador lo recibe (o al menos que el broker lo enruta, según lo que exista
      implementado).
- [ ] **7.6** `docker compose down` + `docker compose up` (sin `-v`) conserva usuarios,
      ubicaciones y datos de InfluxDB.

---

## 9. Fase 8 — Documentación

- [ ] **8.1** Crear `DEPLOY.md` con instrucciones específicas de Docker (reemplaza/complementa
      la sección de instalación manual de `INSTALACION.md`): `git clone`, `cp .env.example .env`,
      generar `APP_KEY`, `docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d`.
      *Criterio:* alguien sin contexto previo puede desplegar siguiendo solo ese archivo.
- [ ] **8.2** Documentar en `DEPLOY.md` los requisitos que el servidor de la escuela debe
      cumplir a nivel de firewall: **puertos 1883 (MQTT) y 8086 (InfluxDB) deben estar
      accesibles desde la red donde estarán los ESP32**, además del puerto web (80/443).
      *Criterio:* queda como sección explícita, fácil de copiar/pegar en un correo o ticket a
      soporte técnico de la escuela.

---

## 10. Fase futura (NO ejecutar todavía — solo dejar la arquitectura lista)

- [ ] **F.1** Si se confirma que el servidor tendrá IP/dominio público real: añadir Traefik o
      Nginx + Certbot para TLS en el puerto web, y evaluar MQTT sobre TLS (8883) si los ESP32
      lo soportan.
- [ ] **F.2** Si se confirma firewall restrictivo institucional: documentar el proceso de
      solicitud de apertura de puertos con el área de sistemas de la escuela.

---

## Notas para el agente sobre el orden de ejecución

Ejecuta las fases **en orden** (0 → 1 → 2 → ... → 8). No saltes a Fase 3 sin haber hecho la
Fase 0, y no marques una fase como completa si algún criterio de aceptación de esa fase no pasó.
Si te atoras en un criterio de aceptación (por ejemplo, el healthcheck de InfluxDB nunca pasa a
`healthy`), reporta el log completo del contenedor antes de intentar "arreglarlo" con cambios
no relacionados.

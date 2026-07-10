# Arquitectura del Sistema — IoT_Project

## Tabla de Contenidos

1. [Visión General](#1-visión-general)
2. [Arquitectura de Software](#2-arquitectura-de-software)
3. [Arquitectura de Datos](#3-arquitectura-de-datos)
4. [Flujo de Datos IoT](#4-flujo-de-datos-iot)
5. [Modelo de Seguridad](#5-modelo-de-seguridad)
6. [Patrones de Diseño Aplicados](#6-patrones-de-diseño-aplicados)

---

## 1. Visión General

IoT_Project implementa una **arquitectura híbrida** que separa el plano de control transaccional (MySQL) del plano de telemetría de alta frecuencia (InfluxDB), comunicados mediante un broker MQTT para control asíncrono de dispositivos.

### Diagrama de Alto Nivel

```
┌─────────────────────────────────────────────────────────────────┐
│                        CAPA DE PRESENTACIÓN                     │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────────┐  │
│  │  Dashboard   │  │  Monitoreo   │  │  Panel Administrador │  │
│  │  Usuario     │  │  Tiempo Real │  │  (CRUD + Reportes)   │  │
│  └──────────────┘  └──────────────┘  └──────────────────────┘  │
│  Blade + Tailwind CSS + Chart.js + DataTables                   │
└───────────────────────────┬─────────────────────────────────────┘
                            │ HTTP
┌───────────────────────────▼─────────────────────────────────────┐
│                        CAPA DE APLICACIÓN                       │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                    Laravel 12.x                           │  │
│  │  ┌─────────────┐  ┌──────────────┐  ┌──────────────┐   │  │
│  │  │ Controllers │  │   Models     │  │  Middleware   │   │  │
│  │  │  (24 clases)│  │ (12 modelos) │  │  (Checkrol)  │   │  │
│  │  └─────────────┘  └──────────────┘  └──────────────┘   │  │
│  │  ┌─────────────┐  ┌──────────────┐  ┌──────────────┐   │  │
│  │  │  Services   │  │   Routes     │  │   Config     │   │  │
│  │  │  (IoT/MQTT) │  │  (web.php)   │  │ (services)   │   │  │
│  │  └─────────────┘  └──────────────┘  └──────────────┘   │  │
│  └──────────────────────────────────────────────────────────┘  │
└───────┬───────────────────────────────────┬────────────────────┘
        │                                   │
┌───────▼──────────────┐     ┌──────────────▼────────────────────┐
│   MySQL / MariaDB    │     │           InfluxDB                │
│   (Plano Relacional) │     │       (Plano Temporal)            │
│                      │     │                                    │
│  • users             │     │  • Bucket: biobit                  │
│  • ubicaciones       │     │  • Métricas de sensores           │
│  • maestros_*        │     │  • Lecturas por dispositivo       │
│  • esclavos_*        │     │  • Índice por timestamp           │
│  • componentes       │     │                                    │
│  • lecturas (buffer) │     │  • Consultas vía Flux             │
│  • comandos          │     │                                    │
└──────────────────────┘     └───────────────────────────────────┘
                                     ▲
                                     │ Ingestión directa
┌────────────────────────────────────┴───────────────────────────┐
│                    CAPA IoT / HARDWARE                         │
│                                                                │
│  ┌────────────────┐     MQTT      ┌────────────────────────┐  │
│  │  Eclipse       │◄────────────►│   ESP32 Maestros       │  │
│  │  Mosquitto     │               │   (Gateways)           │  │
│  │  (Docker)      │               └────────────────────────┘  │
│  │  Puerto 1883   │                                            │
│  └────────────────┘               ┌────────────────────────┐  │
│                                   │   ESP32 Esclavos       │  │
│                                   │   (Sensores/Actuadores)│  │
│                                   └────────────────────────┘  │
└───────────────────────────────────────────────────────────────┘
```

---

## 2. Arquitectura de Software

### 2.1 Patrón MVC (Model-View-Controller)

El proyecto sigue el patrón MVC de Laravel:

| Componente | Responsabilidad | Ejemplo |
|-----------|----------------|---------|
| **Model** | Lógica de negocio y acceso a datos | `User`, `Componente`, `MaestroUsuario` |
| **View** | Presentación HTML (Blade templates) | `modules/dashboard/home.blade.php` |
| **Controller** | Orquestación de peticiones HTTP | `AuthController`, `ComponenteController` |

### 2.2 Capa de Servicios (IoT)

El acceso a servicios externos se centraliza en `config/services.php`:

```php
'mqtt' => [
    'host' => env('MQTT_HOST', '127.0.0.1'),
    'port' => env('MQTT_PORT', 1883),
],

'influxdb' => [
    'url' => env('INFLUXDB_URL'),
    'token' => env('INFLUXDB_TOKEN'),
    'bucket' => env('INFLUXDB_BUCKET'),
    'org' => env('INFLUXDB_ORG'),
],
```

Los controladores acceden mediante `config('services.mqtt.host')` en lugar de `env()` directo, garantizando compatibilidad con `php artisan config:cache`.

### 2.3 Autenticación y Autorización

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Request   │───►│    Auth     │───►│  Checkrol   │───► Controller
│             │    │ Middleware  │    │ Middleware  │
└─────────────┘    └─────────────┘    └─────────────┘
                     Verifica             Verifica
                     sesión              rol (Admin/Usuario)
```

- **Autenticación**: Laravel Auth nativo (sesiones MySQL).
- **Autorización**: Middleware `Checkrol` compara case-insensitive el campo `rol` del usuario con el rol requerido en la ruta.
- **Segregación de datos**: Cada controlador de usuario filtra por `Auth::id()` para garantizar que solo acceda a sus propios recursos.

### 2.4 Estructura de Controladores

```
app/Http/Controllers/
├── AuthController.php              # Login/Logout
├── DashboardController.php         # Dashboard administrador
├── UsuarioController.php           # CRUD usuarios
├── UnidadMedidaController.php      # CRUD unidades de medida
├── ComponenteController.php        # CRUD componentes (sensores/actuadores)
├── ComandoController.php           # CRUD comandos
├── EsclavoCatalogoController.php   # CRUD catálogo esclavos
├── MaestroCatalogoController.php   # CRUD catálogo maestros
├── MaestroUsuarioController.php    # Asignación maestro-usuario
├── MaestroEsclavoController.php    # Vinculación maestro-esclavo
├── UbicacionController.php         # CRUD ubicaciones (admin)
├── AdminReportesController.php     # Reportes administrativos
├── LecturaController.php           # API recepción datos ESP32
├── user/
│   ├── UserDashboardController.php     # Dashboard usuario
│   ├── UserMaestroController.php       # Mis maestros
│   ├── UserEsclavoController.php       # Mis esclavos + monitoreo
│   ├── UserComponenteController.php    # Control MQTT de actuadores
│   ├── UserUbicacionController.php     # Mis ubicaciones
│   └── ReportesController.php          # Reportes usuario
└── compartidos/
    ├── PerfilController.php            # Perfil de usuario
    ├── ConfiguracionController.php     # Configuración (pendiente)
    └── NotificacionesController.php    # Notificaciones (pendiente)
```

---

## 3. Arquitectura de Datos

### 3.1 Diagrama Entidad-Relación

```
┌─────────────────┐         ┌─────────────────┐
│      users      │         │   ubicaciones   │
├─────────────────┤    1:N  ├─────────────────┤
│ id (PK)         │────────►│ id (PK)         │
│ name            │         │ user_id (FK)    │
│ apellido        │         │ nombre          │
│ usuario (UQ)    │         └────────┬────────┘
│ email (UQ)      │                  │
│ password        │                  │ 1:N
│ rol (enum)      │                  │
│ activo          │         ┌────────▼────────┐
│ imagen_url      │         │maestros_usuarios│
└────────┬────────┘         ├─────────────────┤
         │ 1:N              │ id (PK)         │
         │                  │ maestro_id (FK) │──► maestros_catalogo
         │                  │ user_id (FK)    │
         │                  │ ubicacion_id(FK)│
         │                  │ numero_serie(UQ) │
         │                  │ topico          │
         │                  │ broker          │
         │                  └────────┬────────┘
         │                           │ 1:N
         │                  ┌────────▼────────┐
         │                  │maestros_esclavos│
         │                  ├─────────────────┤
         │                  │ id (PK)         │
         │                  │ maestro_id (FK) │
         │                  │ esclavo_id (FK) │──► esclavos_catalogo
         │                  │ ubicacion_id(FK)│
         │                  │ numero_serie(UQ) │
         │                  │ nombre          │
         │                  └─────────────────┘

┌─────────────────────┐    ┌─────────────────┐
│unidades_de_medida   │    │   componentes   │
├─────────────────────┤    ├─────────────────┤
│ id (PK)             │◄───│ unidad_id (FK)  │
│ nombre              │    │ id (PK)         │
└─────────────────────┘    │ nombre          │
                           │ tipo            │
                           │ ruta_icono      │
                           └────────┬────────┘
                                    │ N:M
                           ┌────────▼────────────────┐
                           │detalle_esclavo_componentes│
                           ├─────────────────────────┤
                           │ esclavo_id (FK)         │
                           │ componente_id (FK)      │
                           └─────────────────────────┘

┌─────────────────┐    ┌─────────────────┐
│    lecturas     │    │    comandos     │
├─────────────────┤    ├─────────────────┤
│ id (PK)         │    │ id (PK)         │
│ componente_id   │    │ nombre          │
│ valor           │    │ comandos        │
│ created_at      │    └─────────────────┘
└─────────────────┘
```

### 3.2 Diccionario de Datos

| Tabla | Propósito | Registros Iniciales |
|-------|-----------|---------------------|
| `users` | Cuentas de operadores y administradores | 1 (admin) |
| `sessions` | Persistencia de sesiones HTTP | - |
| `ubicaciones` | Espacios físicos del usuario | - |
| `maestros_catalogo` | Plantillas de hardware concentrador | 1 (Maestro biobit M01) |
| `esclavos_catalogo` | Plantillas de hardware de campo | 1 (Esclavo biobit E01) |
| `unidades_de_medida` | Magnitudes físicas estandarizadas | 5 (°C, %, V, A, mm) |
| `maestros_usuarios` | Instancia física de gateway por usuario | - |
| `maestros_esclavos` | Nodo de campo vinculado a un gateway | - |
| `componentes` | Catálogo de sensores y actuadores | 7 (4 sensores + 3 actuadores) |
| `detalle_esclavo_componentes` | Vinculación N:M esclavo-componente | - |
| `lecturas` | Buffer relacional de telemetría | - |
| `comandos` | Catálogo de comandos MQTT | - |
| `comando_componentes` | Vinculación comando-componente | - |

### 3.3 Integridad Referencial

Todas las foreign keys utilizan `ON DELETE CASCADE` para garantizar la limpieza automática de registros huérfanos:

| Tabla Hijo | FK | Tabla Padre | Efecto |
|-----------|-----|------------|--------|
| `ubicaciones` | `user_id` | `users` | Eliminar usuario elimina sus ubicaciones |
| `maestros_usuarios` | `user_id` | `users` | Eliminar usuario elimina sus gateways |
| `maestros_usuarios` | `ubicacion_id` | `ubicaciones` | Eliminar ubicación elimina gateways |
| `maestros_esclavos` | `maestro_id` | `maestros_usuarios` | Eliminar gateway elimina esclavos |
| `maestros_esclavos` | `esclavo_id` | `esclavos_catalogo` | Eliminar tipo esclavo elimina instancias |
| `componentes` | `unidad_id` | `unidades_de_medida` | Eliminar unidad elimina componentes |
| `lecturas` | `componente_id` | `componentes` | Eliminar componente elimina lecturas |

---

## 4. Flujo de Datos IoT

### 4.1 Flujo Inbound: Telemetría de Sensores

```
ESP32 Esclavo          InfluxDB              Laravel           Navegador
     │                    │                     │                  │
     │  1. Leer sensor    │                     │                  │
     │  2. Empaquetar JSON│                     │                  │
     │───────────────────►│                     │                  │
     │   POST /write      │                     │                  │
     │   (puerto 8086)    │                     │                  │
     │                    │                     │                  │
     │                    │  3. Almacenar       │                  │
     │                    │     timestamp +     │                  │
     │                    │     tags + fields   │                  │
     │                    │                     │                  │
     │                    │  4. Consulta Flux   │                  │
     │                    │◄────────────────────│                  │
     │                    │  (via SDK PHP)      │                  │
     │                    │                     │                  │
     │                    │  5. Retornar datos  │                  │
     │                    │────────────────────►│                  │
     │                    │                     │  6. JSON API     │
     │                    │                     │─────────────────►│
     │                    │                     │                  │
     │                    │                     │  7. Chart.js     │
     │                    │                     │     renderizar   │
```

**Nota:** Laravel NO participa en la ingesta de telemetría. Los ESP32 escriben directamente en InfluxDB para maximizar el rendimiento I/O.

### 4.2 Flujo Outbound: Control de Actuadores

```
Navegador              Laravel              MySQL            MQTT Broker        ESP32
   │                     │                    │                  │                │
   │ 1. Toggle switch    │                    │                  │                │
   │   (POST /controlar) │                    │                  │                │
   │────────────────────►│                    │                  │                │
   │                     │ 2. Actualizar      │                  │                │
   │                     │    estado en BD    │                  │                │
   │                     │───────────────────►│                  │                │
   │                     │                    │                  │                │
   │                     │ 3. Publicar MQTT   │                  │                │
   │                     │──────────────────────────────────────►│                │
   │                     │   topic: v1/usuarios/{id}/nodos/{sn} │                │
   │                     │                    │                  │ 4. Suscribir   │
   │                     │                    │                  │───────────────►│
   │                     │                    │                  │                │
   │                     │                    │                  │ 5. Conmutar    │
   │                     │                    │                  │    GPIO pin    │
   │                     │                    │                  │                │
   │ 6. Refrescar estado │                    │                  │                │
   │◄────────────────────│                    │                  │                │
```

### 4.3 Tópicos MQTT

Estructura de tópicos del sistema:

```
v1/usuarios/{user_id}/nodos/{numero_serie}/{campo}
```

Ejemplo:
```
v1/usuarios/1/nodos/ESP32_ABC123/temperatura
v1/usuarios/1/nodos/ESP32_ABC123/relay1
```

---

## 5. Modelo de Seguridad

### 5.1 Capas de Seguridad

| Capa | Mecanismo | Implementación |
|------|-----------|----------------|
| **Transporte** | HTTP (desarrollo) / HTTPS (producción) | Configuración de servidor web |
| **Autenticación** | Sesiones MySQL | Laravel Auth nativo |
| **Autorización** | Middleware de roles | `Checkrol` (case-insensitive) |
| **Protección de datos** | Bcrypt (12 rondas) | `Hash::make()` en seeders |
| **Rate Limiting** | Throttle en login | `throttle:5,1` (5 intentos/min) |
| **Segregación** | Filtro por `user_id` | En cada controlador |

### 5.2 Flujo de Autenticación

```
1. Usuario envía credenciales (POST /logear)
   └─► throttle:5,1 verifica intentos

2. AuthController valida email + password
   └─► Hash::check() compara con Bcrypt

3. Verifica que usuario esté activo ($user->activo)

4. Auth::login($user) crea sesión
   └─► $request->session()->regenerate()

5. Redirige según rol:
   └─► Admin → /home
   └─► Usuario → /User_home
```

### 5.3 Protección de Recursos

Cada controlador de usuario verifica la propiedad antes de cualquier operación:

```php
// Ejemplo en UserEsclavoController
$esclavo = DB::table('maestros_esclavos as me')
    ->join('maestros_usuarios as mu', 'me.maestro_id', '=', 'mu.id')
    ->where('me.id', $id)
    ->where('mu.user_id', Auth::id())  // ← Candado de seguridad
    ->firstOrFail();
```

---

## 6. Patrones de Diseño Aplicados

### 6.1 Repository Pattern (Simplificado)

Los controladores acceden a datos mediante Eloquent Query Builder o `DB::table()`, actuando como capa de abstracción sobre la base de datos.

### 6.2 Service Layer

La configuración de servicios externos (MQTT, InfluxDB) se centraliza en `config/services.php`, permitiendo inyección de dependencias y testing.

### 6.3 Middleware Pipeline

```
Request → [throttle] → [auth] → [checkrol:Admin] → Controller → Response
```

### 6.4 Observer Pattern (Implícito)

Las foreign keys con `ON DELETE CASCADE` actúan como observadores de integridad referencial a nivel de base de datos.

### 6.5 API Pattern

Los endpoints que retornan JSON siguen la estructura:

```json
{
    "status": "success|error",
    "data": { ... },
    "message": "Descripción del resultado"
}
```

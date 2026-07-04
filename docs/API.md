# API y Rutas — IoT_Project

## Tabla de Contenidos

1. [Convenciones](#1-convenciones)
2. [Autenticación](#2-autenticación)
3. [Rutas Públicas](#3-rutas-públicas)
4. [Rutas de Usuario Autenticado](#4-rutas-de-usuario-autenticado)
5. [Rutas Administrativas](#5-rutas-administrativas)
6. [APIs JSON](#6-apis-json)
7. [API Externa (ESP32)](#7-api-externa-esp32)

---

## 1. Convenciones

| Convención | Descripción |
|-----------|------------|
| `GET` | Consulta de datos (read) |
| `POST` | Creación de registros (create) |
| `PUT` | Actualización de registros (update) |
| `DELETE` | Eliminación de registros (delete) |
| `{id}` | Parámetro de ruta (ID numérico) |
| `[auth]` | Requiere autenticación |
| `[admin]` | Requiere rol Admin |

---

## 2. Autenticación

El sistema utiliza sesiones de Laravel. Las rutas protegidas requieren que el usuario haya iniciado sesión. El middleware `checkrol:Admin` restringe acceso exclusivo a administradores.

### Login

```
POST /logear
```

**Parámetros (form-data):**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `email` | string | Sí | Correo electrónico |
| `password` | string | Sí | Contraseña |

**Respuesta (éxito):** Redirección a `/home` (Admin) o `/User_home` (Usuario).

**Respuesta (error):** Redirección a `/` con mensaje de error.

---

## 3. Rutas Públicas

| Método | Ruta | Controlador | Descripción |
|--------|------|-------------|-------------|
| `GET` | `/` | `AuthController@index` | Formulario de login |
| `POST` | `/logear` | `AuthController@logear` | Procesar login (throttle: 5/min) |

---

## 4. Rutas de Usuario Autenticado

Rutas accesibles por cualquier usuario autenticado (Admin o Usuario).

| Método | Ruta | Controlador | Descripción |
|--------|------|-------------|-------------|
| `GET` | `/User_home` | `UserDashboardController@index` | Dashboard del usuario |
| `GET` | `/logout` | `AuthController@logout` | Cerrar sesión |
| `GET` | `/pendiente` | `DashboardController@pendiente` | Vista en construcción |
| `GET` | `/comandos` | `ComandoController@index` | Listado de comandos |
| `GET` | `/mi-perfil` | `PerfilController@index` | Ver perfil |
| `PUT` | `/perfil/actualizar` | `PerfilController@update` | Actualizar perfil |
| `PUT` | `/perfil/password` | `PerfilController@updatePassword` | Cambiar contraseña |
| `GET` | `/configuracion` | `ConfiguracionController@index` | Configuración |
| `GET` | `/notificaciones` | `NotificacionesController@index` | Notificaciones |

---

## 5. Rutas Administrativas

Rutas exclusivas para usuarios con rol `Admin`.

### 5.1 Dashboard

| Método | Ruta | Controlador | Descripción |
|--------|------|-------------|-------------|
| `GET` | `/home` | `DashboardController@index` | Dashboard administrador |

### 5.2 Gestión de Usuarios

| Método | Ruta | Controlador | Descripción |
|--------|------|-------------|-------------|
| `GET` | `/users` | `UsuarioController@index` | Listar usuarios |
| `GET` | `/users/create` | `UsuarioController@create` | Formulario crear |
| `POST` | `/users` | `UsuarioController@store` | Guardar usuario |
| `GET` | `/users/{id}/edit` | `UsuarioController@edit` | Formulario editar |
| `PUT` | `/users/{id}` | `UsuarioController@update` | Actualizar usuario |
| `DELETE` | `/users/{id}` | `UsuarioController@destroy` | Eliminar usuario |
| `GET` | `/users/cambiar-estado/{id}/{estado}` | `UsuarioController@estado` | Activar/Desactivar |
| `GET` | `/users/{id}/recursos` | `UsuarioController@recursos` | Ver recursos del usuario |

### 5.3 Unidades de Medida

| Método | Ruta | Controlador | Descripción |
|--------|------|-------------|-------------|
| `GET` | `/unidades-medida` | `UnidadMedidaController@index` | Listar unidades |
| `GET` | `/unidades-medida/create` | `UnidadMedidaController@create` | Formulario crear |
| `POST` | `/unidades-medida` | `UnidadMedidaController@store` | Guardar unidad |
| `GET` | `/unidades-medida/{id}/edit` | `UnidadMedidaController@edit` | Formulario editar |
| `PUT` | `/unidades-medida/{id}` | `UnidadMedidaController@update` | Actualizar unidad |
| `DELETE` | `/unidades-medida/{id}` | `UnidadMedidaController@destroy` | Eliminar unidad |

### 5.4 Catálogo de Componentes

| Método | Ruta | Controlador | Descripción |
|--------|------|-------------|-------------|
| `GET` | `/componentes` | `ComponenteController@index` | Listar componentes |
| `GET` | `/componentes/create` | `ComponenteController@create` | Formulario crear |
| `POST` | `/componentes` | `ComponenteController@store` | Guardar componente |
| `GET` | `/componentes/{id}/edit` | `ComponenteController@edit` | Formulario editar |
| `PUT` | `/componentes/{id}` | `ComponenteController@update` | Actualizar componente |
| `DELETE` | `/componentes/{id}` | `ComponenteController@destroy` | Eliminar componente |

### 5.5 Catálogo de Maestros

| Método | Ruta | Controlador | Descripción |
|--------|------|-------------|-------------|
| `GET` | `/maestros-catalogo` | `MaestroCatalogoController@index` | Listar maestros |
| `GET` | `/maestros-catalogo/create` | `MaestroCatalogoController@create` | Formulario crear |
| `POST` | `/maestros-catalogo/store` | `MaestroCatalogoController@store` | Guardar maestro |
| `GET` | `/maestros-catalogo/edit/{id}` | `MaestroCatalogoController@edit` | Formulario editar |
| `PUT` | `/maestros-catalogo/update/{id}` | `MaestroCatalogoController@update` | Actualizar maestro |
| `DELETE` | `/maestros-catalogo/{id}` | `MaestroCatalogoController@destroy` | Eliminar maestro |
| `GET` | `/maestros-catalogo/show-esclavos/{id}` | `MaestroCatalogoController@administrar_esclavos` | Ver esclavos |
| `POST` | `/maestros-catalogo/vincular-esclavo` | `MaestroCatalogoController@vincular_esclavo` | Vincular esclavo |

### 5.6 Catálogo de Esclavos

| Método | Ruta | Controlador | Descripción |
|--------|------|-------------|-------------|
| `GET` | `/esclavos-catalogo` | `EsclavoCatalogoController@index` | Listar esclavos |
| `GET` | `/esclavos-catalogo/create` | `EsclavoCatalogoController@create` | Formulario crear |
| `POST` | `/esclavos-catalogo` | `EsclavoCatalogoController@store` | Guardar esclavo |
| `GET` | `/esclavos-catalogo/{id}/edit` | `EsclavoCatalogoController@edit` | Formulario editar |
| `PUT` | `/esclavos-catalogo/{id}` | `EsclavoCatalogoController@update` | Actualizar esclavo |
| `DELETE` | `/esclavos-catalogo/{id}` | `EsclavoCatalogoController@destroy` | Eliminar esclavo |
| `GET` | `/esclavos-catalogo/administrar/{id}` | `EsclavoCatalogoController@administrar` | Detalle técnico |

### 5.7 Vinculación Maestro-Esclavo

| Método | Ruta | Controlador | Descripción |
|--------|------|-------------|-------------|
| `GET` | `/maestro_esclavo/maestros/{id}/administrar` | `MaestroCatalogoController@administrar_esclavos` | Listar esclavos |
| `GET` | `/maestro_esclavo/maestros/{id}/vincular-esclavo` | `MaestroEsclavoController@asignarNuevoEsclavo` | Formulario vincular |
| `POST` | `/maestro_esclavo/maestros/vincular-esclavo` | `MaestroEsclavoController@storeVinculo` | Guardar vinculación |
| `DELETE` | `/maestro_esclavo/maestros/desvincular/{id}` | `MaestroEsclavoController@desvincularEsclavo` | Desvincular esclavo |

### 5.8 Vinculación Maestro-Usuario

| Método | Ruta | Controlador | Descripción |
|--------|------|-------------|-------------|
| `GET` | `/maestros_usuarios/administrar/{id}` | `MaestroUsuarioController@administrar` | Ver asignación |
| `POST` | `/maestros_usuarios/vincular_maestro` | `MaestroUsuarioController@vincular_maestro` | Asignar maestro |
| `POST` | `/maestros_usuarios/desvincular_maestro` | `MaestroUsuarioController@desvincular_maestro` | Desasignar maestro |

---

## 6. APIs JSON

### 6.1 Reportes Administrativos

#### Listar Usuarios

```
GET /admin/reportes/api/usuarios
```

**Respuesta:**
```json
[
    {
        "id": 1,
        "name": "Admin",
        "email": "admin@admin.com",
        "rol": "Admin",
        "ubicaciones_count": 2,
        "maestros_count": 1
    }
]
```

#### Catálogo de Maestros

```
GET /admin/reportes/api/maestros-catalogo
```

#### Catálogo de Esclavos

```
GET /admin/reportes/api/esclavos-catalogo
```

#### Tabla Maestra (Relaciones Completas)

```
GET /admin/reportes/api/tabla-maestra
```

#### Verificar Estado InfluxDB

```
POST /admin/reportes/api/influx-status
```

### 6.2 Panel de Usuario

#### Datos en Tiempo Real (Dashboard)

```
GET /mis-equipos/dashboard-data
```

**Respuesta:**
```json
{
    "status": "success",
    "data": {
        "sensores": [...],
        "timestamp": "2026-07-04T12:00:00Z"
    }
}
```

#### Última Lectura de Dispositivo

```
GET /mis-equipos/esclavo/{id}/ultima-lectura
```

**Respuesta:**
```json
[
    {
        "nombre": "Sensor de Temperatura (DHT22)",
        "valor": 25.6,
        "unidad": "Grados Celsius (°C)",
        "created_at": "2026-07-04T12:00:00Z"
    }
]
```

#### Configuración del Dispositivo (ESP32)

```
GET /mis-equipos/configurar-dispositivo/{serie}
```

**Respuesta:**
```json
{
    "mqtt_host": "192.168.0.106",
    "mqtt_port": 1883,
    "user_id": 1,
    "base_topic": "v1/usuarios/1/nodos/ESP32_ABC123/",
    "client_id": "ESP32_ESP32_ABC123",
    "status": "authorized"
}
```

#### Esclavos por Maestro

```
GET /mis-equipos/obtener-esclavos/{maestro_id}
```

#### Componentes por Esclavo

```
GET /mis-equipos/obtener-componentes/{esclavo_id}
```

#### Generar Reporte

```
GET /mis-equipos/generar?maestro_id=1&esclavo_id=1&componente_id=1&fecha_inicio=2026-07-01&fecha_fin=2026-07-04
```

---

## 7. API Externa (ESP32)

### 7.1 Recepción de Telemetría

El endpoint `LecturaController@store` recibe datos de los ESP32 y los almacena en la tabla `lecturas`.

```
POST /api/lecturas
```

**Payload (JSON):**
```json
{
    "esclavo_id": 1,
    "temperatura": 25.6,
    "humedad": 65.2,
    "suelo": 45.0,
    "luz": 780,
    "rssi": -45
}
```

**Respuesta (éxito):**
```json
{
    "status": "success"
}
```
Código HTTP: `201 Created`

**Respuesta (error):**
```json
{
    "error": "Falta esclavo_id"
}
```
Código HTTP: `400 Bad Request`

### 7.2 Control de Actuadores (MQTT)

```
POST /mis-equipos/componente/{esclavoId}/controlar
```

**Parámetros (form-data):**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `nombre_field` | string | Nombre del campo a controlar (ej. `relay1`) |
| `estado` | string | Estado deseado (`0` o `1`) |

**Respuesta:**
```json
{
    "status": "success"
}
```

### 7.3 Tópicos MQTT

Estructura de tópicos para comunicación bidireccional:

```
Publicación (Laravel → ESP32):
  v1/usuarios/{user_id}/nodos/{numero_serie}/{campo}

Suscripción (ESP32 → Laravel):
  v1/usuarios/{user_id}/nodos/{numero_serie}/+
```

**Ejemplo de publicación:**
```
Topic:    v1/usuarios/1/nodos/ESP32_ABC123/relay1
Payload:  "1"
QoS:      0
```

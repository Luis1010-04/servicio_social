# Manual de Usuario — IoT_Project

## Tabla de Contenidos

1. [Introducción](#1-introducción)
2. [Acceso al Sistema](#2-acceso-al-sistema)
3. [Panel del Administrador](#3-panel-del-administrador)
4. [Panel del Usuario](#4-panel-del-usuario)
5. [Monitoreo en Tiempo Real](#5-monitoreo-en-tiempo-real)
6. [Gestión de Reportes](#6-gestión-de-reportes)
7. [Perfil de Usuario](#7-perfil-de-usuario)

---

## 1. Introducción

IoT_Project es una plataforma web para el monitoreo y control de dispositivos IoT. Permite a los usuarios administrar hardware (maestros y esclavos), visualizar telemetría de sensores en tiempo real y controlar actuadores remotamente.

### Roles del Sistema

| Rol | Descripción | Accesos |
|-----|------------|---------|
| **Admin** | Supervisor del sistema | Control total: usuarios, hardware, catálogos, reportes globales |
| **Usuario** | Operador de campo | Gestión de sus propios dispositivos, monitoreo, reportes personales |

---

## 2. Acceso al Sistema

### 2.1 Iniciar Sesión

1. Abrir el navegador web (Chrome, Firefox, Edge).
2. Navegar a la dirección de la plataforma.
3. Ingresar el **correo electrónico** y la **contraseña**.
4. Hacer clic en **Ingresar**.

### 2.2 Credenciales por Defecto (Desarrollo)

| Campo | Valor |
|-------|-------|
| Email | `admin@admin.com` |
| Contraseña | `admin` |

> **Nota:** Estas credenciales son solo para desarrollo. En producción, el administrador debe crear cuentas individuales.

### 2.3 Cerrar Sesión

Hacer clic en el menú desplegable del perfil (esquina superior derecha) y seleccionar **Cerrar Sesión**.

---

## 3. Panel del Administrador

### 3.1 Dashboard

Al iniciar sesión como administrador, se muestra el panel principal con:

- **KPI Cards:** Conteo de usuarios, maestros, esclavos y estado de InfluxDB.
- **Gráfica de Inventario:** Comparativa visual de registros en base de datos.
- **Equipos Recientes:** Últimos maestros registrados.

### 3.2 Gestión de Usuarios

**Crear Usuario:**

1. Navegar a **Configuración Maestra > Gestión de Usuarios**.
2. Hacer clic en **Nuevo Usuario**.
3. Completar los campos obligatorios:
   - Nombre, Apellido, Usuario (ID único), Email, Contraseña, Rol.
4. Hacer clic en **Guardar Usuario**.

**Editar Usuario:**

1. En la tabla de usuarios, hacer clic en el botón **Editar** (lápiz amarillo).
2. Modificar los campos requeridos.
3. Hacer clic en **Guardar Cambios**.

**Cambiar Estado:**

1. Hacer clic en el interruptor de estado (Activo/Inactivo) en la columna de estado.
2. El sistema confirma el cambio automáticamente.

**Eliminar Usuario:**

1. Hacer clic en el botón **Eliminar** (papelera roja).
2. Confirmar la acción en el diálogo de confirmación.

> **Precaución:** No se puede eliminar la propia cuenta del administrador actual.

### 3.3 Catálogo de Hardware

#### Unidades de Medida

1. Navegar a **Configuración Maestra > Unidades de Medida**.
2. La tabla muestra las unidades preconfiguradas: °C, %, V, A, mm.
3. Para agregar una nueva unidad, hacer clic en **Nueva Unidad**.

#### Componentes (Sensores y Actuadores)

1. Navegar a **Configuración Maestra > Catálogo de Componentes**.
2. La tabla muestra los 7 componentes de fábrica:
   - **Sensores:** Temperatura (DHT22), Humedad (DHT22), Distancia (HC-SR04), Humedad Suelo (YL-69).
   - **Actuadores:** Relevador 5V, Servomotor (SG90), Bomba de Agua DC.
3. Para crear un componente, hacer clic en **+ Nuevo Componente**.

#### Catálogo de Maestros

1. Navegar a **Catálogo de Hardware > Catálogo Maestros**.
2. Listado de modelos de hardware concentrador disponibles.
3. Para crear un nuevo modelo, hacer clic en **Crear nuevo maestro**.

#### Catálogo de Esclavos

1. Navegar a **Catálogo de Hardware > Catálogo Esclavos**.
2. Listado de modelos de hardware de campo disponibles.
3. Para crear un nuevo modelo, hacer clic en **+ Registrar Nuevo Modelo**.

### 3.4 Reportes Administrativos

1. Navegar a **Reportes Globales**.
2. Seleccionar filtros:
   - **Usuario:** Filtrar por usuario específico.
   - **Maestro:** Filtrar por gateway.
   - **Esclavo:** Filtrar por nodo de campo.
3. Hacer clic en **Generar Reporte** para consultar datos de InfluxDB.

---

## 4. Panel del Usuario

### 4.1 Dashboard del Usuario

Al iniciar sesión como usuario, se muestra:

- **Bienvenida:** Mensaje informativo si no hay dispositivos vinculados.
- **Dispositivos:** Resumen de maestros y esclavos asignados.
- **Gráficas:** Telemetría en tiempo real de los sensores.

### 4.2 Gestión de Ubicaciones

Las ubicaciones representan los espacios físicos donde se distribuye el hardware.

**Crear Ubicación:**

1. Navegar a **Mis Ubicaciones**.
2. Hacer clic en **Nueva Ubicación**.
3. Ingresar el nombre (ej. "Invernadero", "Sala de Servidores").
4. Hacer clic en **Guardar**.

**Editar Ubicación:**

1. En la tabla de ubicaciones, hacer clic en **Editar**.
2. Modificar el nombre.
3. Hacer clic en **Guardar Cambios**.

### 4.3 Gestión de Maestros

Los maestros son los dispositivos concentradores (gateways) que reciben datos de los esclavos.

**Vincular Maestro:**

1. Navegar a **Mis Equipos > Mis Maestros**.
2. Hacer clic en **Vincular Nuevo Maestro**.
3. Seleccionar:
   - **Modelo:** Tipo de hardware maestro.
   - **Ubicación:** Espacio físico donde se instalará.
   - **Número de Serie:** Identificador único del chip.
4. Hacer clic en **Guardar Maestro**.

**Administrar Esclavos del Maestro:**

1. En la tabla de maestros, hacer clic en el botón **Administrar** (engranajes).
2. Se muestra la lista de esclavos vinculados a ese maestro.
3. Para vincular un nuevo esclavo, hacer clic en **Vincular Esclavo**.

### 4.4 Gestión de Esclavos

Los esclavos son los dispositivos de campo que capturan datos de los sensores.

**Vincular Esclavo:**

1. Navegar a **Mis Equipos > Mis Esclavos**.
2. Hacer clic en **Vincular Nuevo Esclavo**.
3. Seleccionar:
   - **Maestro:** Gateway al que reportará.
   - **Modelo:** Tipo de hardware esclavo.
   - **Ubicación:** Espacio físico del dispositivo.
   - **Número de Serie:** Identificador único del chip.
4. Hacer clic en **Guardar Esclavo**.

**Editar Esclavo:**

1. En la tabla de esclavos, hacer clic en **Editar** (lápiz amarillo).
2. Modificar nombre, ubicación u otros campos.
3. Hacer clic en **Guardar Cambios**.

**Eliminar Esclavo:**

1. Hacer clic en **Eliminar** (papelera roja).
2. Confirmar la acción.

---

## 5. Monitoreo en Tiempo Real

### 5.1 Acceder al Monitor

1. En **Mis Esclavos**, localizar el dispositivo a monitorear.
2. Hacer clic en el botón **Monitorear** (icono de pantalla).
3. Se abre la vista de monitoreo en tiempo real.

### 5.2 Interpretación de Datos

La pantalla de monitoreo muestra:

- **Información del Dispositivo:** Nombre, modelo, número de serie.
- **Gráficas de Telemetría:** Comportamiento histórico de cada sensor.
- **Última Lectura:** Valor más reciente de cada variable con su unidad.
- **Controles de Actuadores:** Interruptores On/Off para dispositivos de salida.

### 5.3 Control de Actuadores

Para encender o apagar un actuador (relevador, bomba, servo):

1. En la pantalla de monitoreo, localizar el interruptor del actuador.
2. Hacer clic en el toggle switch.
3. El sistema envía el comando MQTT al dispositivo.
4. El estado se actualiza automáticamente en la interfaz.

---

## 6. Gestión de Reportes

### 6.1 Generar Reporte

1. Navegar a **Mis Reportes**.
2. Seleccionar los filtros:
   - **Maestro:** Gateway del que provienen los datos.
   - **Esclavo:** Nodo de campo específico.
   - **Componente:** Sensor a consultar.
   - **Fecha Inicio / Fecha Fin:** Rango de consulta.
3. Hacer clic en **Generar Reporte**.
4. Los resultados se muestran en tabla y gráfica.

### 6.2 Exportar Datos

Desde la tabla de resultados, es posible:

- **Copiar:** Copiar los datos al portapapeles.
- **CSV:** Descargar en formato separado por comas.
- **Excel:** Descargar en formato .xlsx.
- **PDF:** Generar documento PDF para impresión.

---

## 7. Perfil de Usuario

### 7.1 Ver Perfil

1. Hacer clic en **Profile** en la barra superior.
2. Seleccionar **Mi Perfil**.
3. Se muestran los datos actuales del usuario.

### 7.2 Actualizar Datos

1. En la página de perfil, modificar los campos requeridos.
2. Opcionalmente, subir una nueva imagen de perfil.
3. Hacer clic en **Actualizar Información**.

### 7.3 Cambiar Contraseña

1. En la página de perfil, localizar la sección **Cambiar Contraseña**.
2. Ingresar la contraseña actual.
3. Ingresar la nueva contraseña (mínimo 8 caracteres).
4. Confirmar la nueva contraseña.
5. Hacer clic en **Guardar Nueva Contraseña**.

---

## Glosario

| Término | Definición |
|---------|-----------|
| **Maestro** | Dispositivo concentrador (gateway) que recibe datos de múltiples esclavos |
| **Esclavo** | Dispositivo de campo que captura datos de sensores o controla actuadores |
| **Componente** | Sensor o actuador físico vinculado a un esclavo |
| **Telemetría** | Datos medidos por los sensores transmitidos al sistema |
| **MQTT** | Protocolo de mensajería ligero para comunicación IoT |
| **InfluxDB** | Base de datos especializada en series temporales |
| **Bucket** | Almacén de datos en InfluxDB (similar a una base de datos) |
| **Tópico** | Canal de comunicación MQTT donde se publican/suscriben mensajes |

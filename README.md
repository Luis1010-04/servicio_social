# Plataforma de Gestión IoT - Monitoreo de Variables Ambientales 

Este proyecto es una plataforma web híbrida desarrollada en **Laravel 12** orientada a la administración, centralización y análisis volumétrico de datos provenientes de nodos sensores de variables ambientales. El sistema organiza los dispositivos en una topología jerárquica de hardware (Maestros y Esclavos) comunicados a través del protocolo de mensajería asíncrona MQTT.

## Características Principales

* **Persistencia Políglota (Dual-Database):** Uso de **MySQL** para la gestión relacional (usuarios, roles, ubicaciones e infraestructura de red) en coexistencia con **InfluxDB** para almacenar series temporales de alta densidad (telemetría de los sensores).
* **Gestión de Infraestructura:** Mapeo de ubicaciones físicas y asignación dinámica de hardware.
* **Seguridad por Capas:** Control de accesos mediante autenticación nativa y Middlewares personalizados para la segregación de funciones por roles (`Admin` / `Usuario`).
* **Arquitectura IoT Perimetral:** * **Nodos Esclavos:** Captura cíclica de variables ambientales y transmisión local.
    * **Nodos Maestros (Gateway):** Concentración de ráfagas de datos de esclavos y puente directo de inserción a InfluxDB.
    * **Broker MQTT:** Instancia virtualizada de **Eclipse Mosquitto** mediante Docker.

---

## 🛠️ Stack Tecnológico

* **Backend:** PHP 8.2.12 & Framework Laravel 12.x
* **Bases de Datos:** MySQL / MariaDB + InfluxDB
* **Servidor Web Local:** Apache 2.4 (Entorno LAMPP / XAMPP para Linux)
* **Mensajería / IoT:** Eclipse Mosquitto (MQTT Broker) hospedado en un contenedor de Docker.

---

## 💻 Requisitos Previos

Antes de comenzar la instalación, asegúrate de tener instalado en tu entorno UNIX/Linux:
* [XAMPP/LAMPP for Linux](https://www.apachefriends.org/) (PHP >= 8.2 y MySQL).
* [Composer](https://getcomposer.org/) (Gestor de dependencias de PHP).
* [Docker](https://docs.docker.com/engine/install/) y Docker Compose.
* Git.

---

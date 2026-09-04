---



# 🛒 Products API Challenge

![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![Apache](https://img.shields.io/badge/apache-%23D22128.svg?style=for-the-badge&logo=apache&logoColor=white)
![MySQL](https://img.shields.io/badge/mysql-%234479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/docker-%232496ED.svg?style=for-the-badge&logo=docker&logoColor=white)
![FastRoute](https://img.shields.io/badge/fast--route-%23000000.svg?style=for-the-badge&logo=php&logoColor=white)

This project is a native PHP REST API for product management (no frameworks). Dockerized with Apache and MySQL.
Exposes a clean CRUD for `productos` and converts prices to USD using the `PRECIO_USD` environment variable.

---

## :electric_plug: Installation

This project runs with Docker Compose.

1. **Pre-requisites**: Ensure you have Docker and Docker Compose installed.
2. Copy the environment file: `cp backend/.env.example backend/.env`
3. Set the USD exchange rate (optional, default is `1500`):
4. Run `docker compose up --build` to start the API and MySQL.

Ports mapping can be changed in `docker-compose.yml`, by default:

API will be at `http://localhost:8081`  
MySQL will be at `localhost:3306`

---

## 🧐 Features:

- **Routing**: `nikic/fast-route` for routing.
- **Config**: `vlucas/phpdotenv` for environment variables

## 🗄️ Database Management

Database schema and seed are in `backend/database/init.sql` and run automatically on MySQL entrypoint `/docker-entrypoint-initdb.d/`.

Default credentials:

- Database: `productos`
- User / password: `productos` / `secret`
- Root password: `root`

## 📚 API Documentation

TODO

:wave: Bah-bye!
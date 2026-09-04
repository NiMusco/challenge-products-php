---



# 🛒 Products API Challenge

![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/mysql-%234479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/docker-%232496ED.svg?style=for-the-badge&logo=docker&logoColor=white)
![Swagger](https://img.shields.io/badge/swagger-%2385EA2D.svg?style=for-the-badge&logo=swagger&logoColor=black)
![FastRoute](https://img.shields.io/badge/fast--route-%23000000.svg?style=for-the-badge&logo=php&logoColor=white)

Native PHP REST API for product management (no frameworks). Dockerized with Apache and MySQL.
<br>Exposes a clean CRUD for `productos` and converts prices to USD using the `PRECIO_USD` environment variable.

---

## :electric_plug: Installation

This project runs with Docker Compose from the repository root.

---

## 🧐 Features:

- **Routing**: `nikic/fast-route` for routing
- **Config**: `vlucas/phpdotenv` for environment variables
- **USD Conversion**: Responses include `precio` (ARS) and `precio_usd` from `PRECIO_USD`
- **Pagination**: `GET /productos?page=1&per_page=10`
- **Swagger Docs**: Separate Docker service (`swagger/openapi.yaml`)

## 🗄️ Database Management

Database schema and seed at `backend/database/init.sql`
<br>Runs automatically on MySQL entrypoint `/docker-entrypoint-initdb.d/`.

Default credentials:

- Database: `productos`
- User / password: `productos` / `secret`
- Root password: `root`

## 📚 API Documentation

Swagger UI at `http://localhost:8082`

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/productos?page=&per_page=` | List products (paginated) |
| `GET` | `/productos/{id}` | Get a product by ID |
| `POST` | `/productos` | Create a product |
| `PUT` | `/productos/{id}` | Update a product |
| `DELETE` | `/productos/{id}` | Delete a product (`204`) |

Error responses format:

```json
{
  "error": "Validation failed.",
  "code": "VALIDATION_ERROR",
  "details": {
    "nombre": "Field \"nombre\" is required."
  }
}
```

---

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
- **Swagger Docs**: Separate Docker service (`swagger/openapi.yaml`)
- **Tests**: Pest feature tests for each API endpoint

## 🗄️ Database Management

App schema + seed: `backend/database/init.sql` (MySQL service `db`)
<br>Test schema only: `backend/database/schema.sql` (MySQL service `db_test`)

Both run automatically on each MySQL entrypoint `/docker-entrypoint-initdb.d/`.

Default credentials (same on both containers):

- Database: `productos`
- User / password: `productos` / `secret`
- Root password: `root`
- App MySQL: Docker service `db` (host port `3306`)
- Test MySQL: Docker service `db_test` (no host port; Docker network only)

## 🧪 Tests

Pest feature tests cover each API endpoint (`GET` list/show, `POST`, `PUT`, `DELETE`).

### Database isolation (Docker)

Feature tests call the API over HTTP, so Pest cannot wrap writes in a DB transaction and roll them back (another PHP process owns the connection). A second table—or even a second schema—on the **same** MySQL container still shares the app’s server.

Isolation here is a **dedicated MySQL container** (`db_test`) with its own Docker volume (`mysql_test_data`):

1. `bin/ensure-testing-database.php` waits until `db_test` is reachable
2. `composer test` boots a temporary PHP built-in server with `DB_HOST=db_test` (Apache keeps using `db`)
3. Each test truncates `productos` on `db_test` only

```bash
docker compose up -d
docker compose exec api composer test
```

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

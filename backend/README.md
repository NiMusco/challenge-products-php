# 🛒 PHP Products Challenge (Backend)

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Apache](https://img.shields.io/badge/Apache-2.4-D22128?style=for-the-badge&logo=apache&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Pest](https://img.shields.io/badge/Pest-2.36.1-FF3E00?style=for-the-badge&logo=php&logoColor=white)
![Swagger](https://img.shields.io/badge/OpenAPI-3.0-85EA2D?style=for-the-badge&logo=swagger&logoColor=black)

Native PHP REST API for product management (no frameworks). Dockerized with Apache and MySQL.
<br>Exposes a clean CRUD for products and converts prices to USD using the `PRECIO_USD` environment variable.

---

## 📚 Documentation

- **[Monorepo README](../README.md)**

## :electric_plug: Installation

This project runs with Docker Compose from the repository root.

```bash
docker compose up -d
```

### 🧩 Libraries

| Library | Package | Role |
|---------|---------|------|
| phpdotenv | [`vlucas/phpdotenv`](https://github.com/vlucas/phpdotenv) | Environment variables |
| FastRoute | [`nikic/fast-route`](https://github.com/nikic/FastRoute) | HTTP routing |
| Respect | [`respect/validation`](https://github.com/Respect/Validation) | Request validation |
| Pest | [`pestphp/pest`](https://github.com/pestphp/pest) | Test cases |

### 📂 Content

```text
📁 backend/src
├── 📁 Controllers   HTTP actions
├── 📁 Routing       URL mapping
├── 📁 Validators    Request body validation
├── 📁 Repositories  PDO database queries
├── 📁 Http          JSON responses + pagination headers
├── 📁 Exceptions    Typed HTTP errors
├── 📁 Config        Database Singleton
└── 📁 Utils         Support/utility functions
```

### 🗄️ Database

Schema + seed: `backend/database/init.sql`
<br>Runs automatically on MySQL entrypoint `/docker-entrypoint-initdb.d/`.

DB credentials: see [`docker-compose.yml`](../docker-compose.yml)

## 📚 API Documentation

Swagger UI at http://localhost:8082

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/productos?page=&per_page=` | List products (X-Page headers) |
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

## 🧪 Tests

HTTP API tests use **Pest**.

Running them will create/update/delete rows in `productos` table.
<br>Tests that need an existing row, will create it first with `POST`.

| File | Endpoint |
|------|----------|
| `ListProductsTest.php` | `GET /productos` |
| `GetProductTest.php` | `GET /productos/{id}` |
| `CreateProductTest.php` | `POST /productos` |
| `UpdateProductTest.php` | `PUT /productos/{id}` |
| `DeleteProductTest.php` | `DELETE /productos/{id}` |

### Run

```bash
docker compose up -d
docker compose --profile test run --rm tests
```
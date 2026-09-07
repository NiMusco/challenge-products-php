# 🛒 PHP Products Challenge

![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/mysql-%234479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/docker-%232496ED.svg?style=for-the-badge&logo=docker&logoColor=white)
![Swagger](https://img.shields.io/badge/swagger-%2385EA2D.svg?style=for-the-badge&logo=swagger&logoColor=black)
![Pest](https://img.shields.io/badge/pest-%23FF3E00.svg?style=for-the-badge&logo=php&logoColor=white)

Monorepo for a products management challenge.

---

## 📚 Documentation

- **[Backend / API](backend/README.md)** — PHP REST API, MySQL, Swagger, Pest.
- **[Frontend](frontend/README.md)** — Vanilla HTML/JS UI with Tailwind.

---

## :electric_plug: Installation

**Pre-requisites**: Ensure you have Docker and Docker Compose installed.
<br>See [`docker-compose.yml`](docker-compose.yml).

### Run

```bash
docker compose up -d
```

### Tests

Starts (or waits for) healthy `db` + `api`, then runs Pest:

```bash
docker compose --profile test run --rm tests
```

### ⚙️ Environment variables

| Variable | Default |
|----------|---------|
| `MYSQL_DATABASE` | `productos` |
| `MYSQL_USER` | `productos` |
| `MYSQL_PASSWORD` | `secret` |
| `MYSQL_ROOT_PASSWORD` | `root` |
| `DB_HOST` | `db` |
| `DB_PORT` | `3306` |
| `PRECIO_USD` | `1500` |
| `API_BASE_URL` | `http://localhost:8081` |

### 📦 Containers launched:

| Service | Container role | URL / port |
|---------|----------------|------------|
| `api` | PHP Apache REST API | http://localhost:8081 |
| `frontend` | Nginx UI | http://localhost:3000 |
| `swagger` | Swagger UI | http://localhost:8082 |
| `db` | MySQL | localhost:3306 |

---

:wave: Bah-bye!

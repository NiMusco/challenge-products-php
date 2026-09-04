---



# 🛒 Products Challenge

![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/mysql-%234479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/docker-%232496ED.svg?style=for-the-badge&logo=docker&logoColor=white)
![Swagger](https://img.shields.io/badge/swagger-%2385EA2D.svg?style=for-the-badge&logo=swagger&logoColor=black)

Monorepo for a products management challenge.
<br>Everything runs with Docker Compose.

---

## :electric_plug: Installation

1. **Pre-requisites**: Ensure you have Docker and Docker Compose installed.
2. Copy the environment file: `cp backend/.env.example backend/.env`
3. Set the USD exchange rate (optional, default is `1500`).
4. Run `docker compose up --build` to start the full stack.

| Service | URL |
|---------|-----|
| Frontend | http://localhost:3000 |
| API | http://localhost:8081 |
| Swagger | http://localhost:8082 |
| MySQL | localhost:3306 |

---

## 📚 Documentation

- **[Backend / API](backend/README.md)** — PHP REST API, MySQL, Swagger, `PRECIO_USD`, Pest + Docker `db_test`
- **[Frontend](frontend/README.md)** — Vanilla HTML/JS UI with Tailwind

---

:wave: Bah-bye!

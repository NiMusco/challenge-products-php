---



# 🛒 Products Frontend Challenge

![JavaScript](https://img.shields.io/badge/javascript-%23F7DF1E.svg?style=for-the-badge&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=for-the-badge&logo=css3&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/tailwindcss-%2306B6D4.svg?style=for-the-badge&logo=tailwindcss&logoColor=white)
![Docker](https://img.shields.io/badge/docker-%232496ED.svg?style=for-the-badge&logo=docker&logoColor=white)

Vanilla HTML, CSS, and JavaScript UI (with Tailwind utility classes) that consumes the Products REST API.
---

## :electric_plug: Installation

This project runs with Docker Compose from the repository root.

---

## 🧐 Features:

- **List products**: Table with name, description, ARS price, and USD price from the API
- **Create / edit**: Form to add new products or update existing ones
- **Delete**: Remove products with confirmation
- **Feedback**: Success and error messages for API responses
- **Tailwind CSS**: Compiled inside the frontend Docker image (no CDN)

## 🖥️ Usage

Open `http://localhost:3000` and manage products against the API.

If you change Tailwind classes, rebuild the frontend image:

```bash
docker compose up --build frontend
```
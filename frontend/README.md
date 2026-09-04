# 🛒 PHP Products Challenge (Frontend)

![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES2020+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![Node.js](https://img.shields.io/badge/Node.js-20-339933?style=for-the-badge&logo=nodedotjs&logoColor=white)
![Nginx](https://img.shields.io/badge/Nginx-1.27-009639?style=for-the-badge&logo=nginx&logoColor=white)

Vanilla HTML, CSS, and JavaScript UI (with Tailwind utility classes) that consumes the Products REST API.

---

## 📚 Documentation

- **[Monorepo README](../README.md)**

## :electric_plug: Installation

This project runs with Docker Compose from the repository root.

```bash
docker compose up -d
```

## 🧩 Features

- **List products**: Simple HTML+CSS table with pagination.
- **Create / edit**: Dialogue form to add new products or update existing ones.
- **Delete**: Remove products with confirmation.
- **Tailwind CSS**: Compiled inside the frontend, no CDN.

If you change Tailwind classes, rebuild the frontend image:

```bash
docker compose up --build frontend
```
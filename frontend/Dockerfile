FROM node:20-alpine AS assets

WORKDIR /app

COPY package.json ./
RUN npm install

COPY tailwind.config.js ./
COPY src ./src
COPY index.html ./
COPY js ./js

RUN mkdir -p css \
  && npx tailwindcss -i ./src/input.css -o ./css/styles.css --minify

FROM nginx:1.27-alpine

COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY index.html /usr/share/nginx/html/index.html
COPY js /usr/share/nginx/html/js
COPY icons /usr/share/nginx/html/icons
COPY --from=assets /app/css/styles.css /usr/share/nginx/html/css/styles.css

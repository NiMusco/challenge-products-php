#!/bin/sh
set -e

API_BASE_URL="${API_BASE_URL:-http://localhost:8081}"

cat > /usr/share/nginx/html/runtime-config.js <<EOF
window.APP_CONFIG = {
  apiBaseUrl: "${API_BASE_URL}",
};
EOF

exec nginx -g 'daemon off;'

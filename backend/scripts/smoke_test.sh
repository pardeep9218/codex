#!/usr/bin/env bash
set -euo pipefail

API_BASE="http://127.0.0.1:8000/api/v1"

curl -s "$API_BASE/health" >/dev/null
curl -s "$API_BASE/fabrics" >/dev/null
curl -s -X POST "$API_BASE/configurations/price-preview" \
  -H 'Content-Type: application/json' \
  -d '{"fabric_id":"fab_navy","options":{"lapel":"peak","vents":"double","pockets":"jetted"}}' >/dev/null
curl -s "$API_BASE/admin/fabrics" -H 'X-Admin-Key: demo-admin-key' >/dev/null
curl -s -X POST "$API_BASE/me/saved-configurations" \
  -H 'Content-Type: application/json' \
  -d '{"email":"demo@example.com","fabric_id":"fab_navy","options":{"lapel":"notch","vents":"single","pockets":"flap"}}' >/dev/null
curl -s -X POST "$API_BASE/orders" \
  -H 'Content-Type: application/json' \
  -d '{"email":"demo@example.com","configuration_id":"cfg_seed_001","total":91900}' >/dev/null
curl -s "$API_BASE/admin/orders" -H 'X-Admin-Key: demo-admin-key' >/dev/null

echo "Smoke test passed."

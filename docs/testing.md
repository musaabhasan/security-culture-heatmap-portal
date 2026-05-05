# Testing Guide

Run:

```bash
php bin/lint.php
php bin/test.php
```

The test suite validates configuration integrity, scoring logic, maturity bands, stable dimension keys, and the public book reference.

## Manual Smoke Test

1. Start Docker with `docker compose up --build`.
2. Open `http://localhost:8080`.
3. Confirm the dashboard renders dimensions, initiatives, and book alignment.
4. Open `/assessment`, save a sample record, and confirm it appears in recent assessments.
5. Open `/api/summary` and confirm JSON output.

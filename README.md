# Indian Bank IFSC Code Information

A simple REST API for looking up Indian bank branch information by IFSC code.

## Setup

```bash
npm install
npm start
```

The server runs on `http://localhost:3000` by default (set `PORT` to override).

## Endpoints

- `GET /health` — health check
- `GET /ifsc/:code` — look up branch details for an IFSC code (e.g. `/ifsc/SBIN0000001`)

## Data

`src/data/ifsc-data.json` currently contains a small set of sample entries for demonstration.
Replace or extend it with a full IFSC dataset (e.g. RBI-published bank branch data) to make the
API useful in practice.

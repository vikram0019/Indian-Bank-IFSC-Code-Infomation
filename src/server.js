const express = require('express');
const ifscData = require('./data/ifsc-data.json');

const app = express();
const PORT = process.env.PORT || 3000;

const ifscIndex = new Map(ifscData.map((entry) => [entry.ifsc.toUpperCase(), entry]));

app.get('/health', (req, res) => {
  res.json({ status: 'ok' });
});

app.get('/ifsc/:code', (req, res) => {
  const code = req.params.code.toUpperCase();
  const entry = ifscIndex.get(code);

  if (!entry) {
    return res.status(404).json({ error: `No branch found for IFSC code ${code}` });
  }

  res.json(entry);
});

app.listen(PORT, () => {
  console.log(`IFSC lookup API listening on port ${PORT}`);
});

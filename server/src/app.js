const express = require('express');
const cors = require('cors');
const { clientOrigin } = require('./config/env');

const ifscRoutes = require('./routes/ifsc.routes');
const searchRoutes = require('./routes/search.routes');
const branchRoutes = require('./routes/branch.routes');
const suggestRoutes = require('./routes/suggest.routes');
const sitemapRoutes = require('./routes/sitemap.routes');
const adminRoutes = require('./routes/admin.routes');
const errorHandler = require('./middleware/errorHandler');

const app = express();

app.use(cors({ origin: clientOrigin }));
app.use(express.json());

app.get('/api/health', (req, res) => res.json({ status: 'ok' }));
app.use('/api/ifsc', ifscRoutes);
app.use('/api/search', searchRoutes);
app.use('/api/branch', branchRoutes);
app.use('/api/suggest', suggestRoutes);
app.use('/api/sitemap-data', sitemapRoutes);
app.use('/api/admin', adminRoutes);

app.use(errorHandler);

module.exports = app;

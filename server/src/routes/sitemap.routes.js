const express = require('express');
const { sitemapData } = require('../controllers/sitemap.controller');

const router = express.Router();

router.get('/', sitemapData);

module.exports = router;

const express = require('express');
const { suggest } = require('../controllers/suggest.controller');

const router = express.Router();

router.get('/', suggest);

module.exports = router;

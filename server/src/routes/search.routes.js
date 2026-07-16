const express = require('express');
const { searchByBankAndCity } = require('../controllers/search.controller');

const router = express.Router();

router.get('/', searchByBankAndCity);

module.exports = router;

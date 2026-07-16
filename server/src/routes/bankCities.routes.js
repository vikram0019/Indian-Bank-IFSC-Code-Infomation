const express = require('express');
const { citiesForBank } = require('../controllers/search.controller');

const router = express.Router();

router.get('/', citiesForBank);

module.exports = router;

const express = require('express');
const { searchByBranchName } = require('../controllers/search.controller');

const router = express.Router();

router.get('/search', searchByBranchName);

module.exports = router;

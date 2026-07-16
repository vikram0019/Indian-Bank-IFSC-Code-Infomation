const express = require('express');
const { getByIfsc } = require('../controllers/ifsc.controller');

const router = express.Router();

router.get('/:code', getByIfsc);

module.exports = router;

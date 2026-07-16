const express = require('express');
const adminAuth = require('../middleware/adminAuth');
const { refresh, stats } = require('../controllers/admin.controller');

const router = express.Router();

router.use(adminAuth);
router.post('/refresh', refresh);
router.get('/stats', stats);

module.exports = router;

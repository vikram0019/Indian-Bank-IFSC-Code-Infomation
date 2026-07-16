const mongoose = require('mongoose');
const { mongodbUri } = require('../config/env');

async function connectDB() {
  mongoose.set('strictQuery', true);
  await mongoose.connect(mongodbUri);
  console.log(`MongoDB connected: ${mongodbUri}`);
}

module.exports = connectDB;

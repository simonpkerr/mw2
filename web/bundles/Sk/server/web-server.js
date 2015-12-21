var express = require('express');
var path = require('path');
var wall = require('./MemoryWallController');
var app = express();
var rootPath = path.normalize(__dirname + '/../');
var bodyParser = require('body-parser');

app.use(bodyParser.urlencoded({extended: true}));
app.use(bodyParser.json());
app.use(express.static(rootPath + '/app'));

app.get('/web/app_dev.php/api/memorywalls/:decade', wall.get);

app.listen(8000);

console.log('listening on port 8000...');

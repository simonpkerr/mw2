var express = require('express');
var path = require('path');
var fs = require('fs');
//var wall = require('./MemoryWallController');
var app = express();
var rootPath = path.normalize(__dirname + '/../');
var bodyParser = require('body-parser');

app.use(bodyParser.urlencoded({extended: true}));
app.use(bodyParser.json());
app.use(express.static(rootPath + '/app'));

app.get('/web/app_dev.php/api/memorywalls/:decade', function (req, res) {
  var data = fs.readFileSync('test/data/sample-wall-1980s.json', 'utf-8');
  //console.log(req.params.decade);
  res.setHeader('Content-Type', 'application/json');
  res.send(data);
});


app.get('/web/app_dev.php/api/memorywall/item/:provider/:id', function (req, res) {
  var data = fs.readFileSync('test/data/' + req.params.provider + '-' + req.params.id + '.json', 'utf-8');
  res.setHeader('Content-Type', 'application/json');
  res.send(data);
});



//app.listen(9001);

//console.log('listening on port 9001...');

module.exports = app;

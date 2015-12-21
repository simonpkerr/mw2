var fs = require('fs');

module.exports.get = function(req, res) {
  var event = fs.readFileSync('test/data/sample-wall-1980s.json', 'utf-8');
  res.setHeader('Content-Type', 'application/json');
  res.send(event);
};

// module.exports.save = function(req, res) {
//   var event = req.body;
//   fs.writeFileSync('app/data/event/' + req.params.id + '.json', JSON.stringify(event));
//   res.send(event);
// };

// module.exports.getAll = function (req, res) {
//   var path = 'app/data/event/';
//   var files = [];

//   try {
//     files = fs.readdirSync(path);
//   } catch (e) {
//     console.log(e);
//     res.send('[]');
//     res.end();
//     return;
//   }

//   var results = '[';

//   for (var idx = 0; idx < files.length ; idx+=1) {
//     if (files[idx].indexOf('.json') > 0) {
//       results += fs.readFileSync(path + '/' + files[idx]) + ",";
//       console.log(path + '/' + files[idx]);
//     }
//   }

//   results = results.substr(0, results.length - 1);
//   results += ']';

//   res.setHeader('Content-Type', 'application/json');
//   res.send(results);
//   res.end();

// };

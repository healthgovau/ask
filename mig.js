var fs = require('fs');
var fz = require('fs');
var files = fs.readdirSync('dev_modules/custom/health_sample_content/config/install');

var dependencies = []

files.map(fn => {
  if (fn.includes('migrate_plus.migration.') && fn.split('.').slice(0, -1).join('.').split('migrate_plus.migration.')[1]) {

    let line = fn.split('.').slice(0, -1).join('.').split('migrate_plus.migration.')[1];
    console.log(line)
    if (line !== 'sample_run_all') {
      dependencies.push(line)
    }

  }
});


var file = 'dev_modules/custom/health_sample_content/config/install/migrate_plus.migration.sample_run_all.yml';

var lines = []

try {
  const data = fs.readFileSync(file, 'utf8')
  let lr = data.toString().split(String.fromCharCode(10));

  for (var i = 0; i < lr.length; i++) {
    if (lr[i] === 'migration_dependencies:') {
      break;
    }
    lines.push(lr[i])
  }

  lines.push('migration_dependencies:')
  lines.push('  required:')

  dependencies.map(d => {
    lines.push('\t- ' + d)
  })

  try {
    const data = fz.writeFileSync('customfilehealth.yml', lines.join("\r\n"), err => {
      console.log('err')
    })
    console.log('file written successfully');
  } catch (err) {
    console.error(err)
  }

} catch (err) {
  console.error(err)
}


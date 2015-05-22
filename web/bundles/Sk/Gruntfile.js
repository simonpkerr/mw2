// Generated on 2014-10-29 using generator-angular 0.9.8
'use strict';

// # Globbing
// for performance reasons we're only matching one level down:
// 'test/spec/{,*/}*.js'
// use this if you want to recursively match all subfolders:
// 'test/spec/**/*.js'

module.exports = function (grunt) {

  // Load grunt tasks automatically
  require('load-grunt-tasks')(grunt);

  // Time how long tasks take. Can help when optimizing build times
  require('time-grunt')(grunt);

  // Configurable paths for the application
  var appConfig = {
    app: require('./bower.json').appPath || 'app',
    dist: 'dist'
  };

  // Define the configuration for all the tasks
  grunt.initConfig({

    // Project settings
    yeoman: appConfig,

    // Watches files for changes and runs tasks based on the changed files
    watch: {
      bower: {
        files: ['bower.json'],
        tasks: ['wiredep']
      },
      js: {
        files: ['<%= yeoman.app %>/{,*/}*.js'],
        tasks: ['newer:jshint:all'],
        options: {
          livereload: '<%= connect.options.livereload %>'
        }
      },
      jsTest: {
        files: ['test/spec/{,*/}*.js'],
        tasks: ['newer:jshint:test', 'karma']
      },
      compass: {
        files: ['styles/scss/*.{scss,sass}'],
        tasks: ['compass:server', 'autoprefixer']
      },
      gruntfile: {
        files: ['Gruntfile.js']
      }
    },

    // The actual grunt server settings
    connect: {
      options: {
        port: 9000,
        // Change this to '0.0.0.0' to access the server from outside.
        hostname: '0.0.0.0',
        base: '',
        livereload: 35729
      },
      test: {
        options: {
          port: 9001,
          middleware: function (connect) {
            return [
              connect.static('.tmp'),
              connect.static('test'),
              connect().use(
                '/bower_components',
                connect.static('./bower_components')
              ),
              connect.static(appConfig.app)
            ];
          }
        }
      },
      dist: {
        options: {
          open: true,
          base: '<%= yeoman.dist %>'
        }
      }
    },

    // Make sure code styles are up to par and there are no obvious mistakes
    jshint: {
      options: {
        jshintrc: '.jshintrc',
        reporter: require('jshint-stylish')
      },
      all: {
        src: [
          'Gruntfile.js',
          '<%= yeoman.app %>{,*/}*.js'
        ]
      },
      test: {
        options: {
          jshintrc: 'test/.jshintrc'
        },
        src: ['test/spec/{,*/}*.js']
      }
    },

    // Empties folders to start fresh
    clean: {
      dist: {
        files: [{
          dot: true,
          src: [
            '.tmp',
            '<%= yeoman.dist %>/{,*/}*',
            '!<%= yeoman.dist %>/.git*'
          ]
        }]
      },
      server: '.tmp'
    },

    // Add vendor prefixed styles
    autoprefixer: {
      dist: {
        files: [{
          expand: true,
          //cwd: '<%= yeoman.app %>/styles/css',
          src: 'styles/css/*.css',
          browsers: ['last 2 versions', 'ie 9']
          //dest: '<%= yeoman.app %>/styles/css/compiled/main.css'
        }]
      }
    },

    // Compiles Sass to CSS and generates necessary files if requested
    compass: {
      options: {
        sassDir: 'styles/scss',
        cssDir: 'styles/css',
        generatedImagesDir: '.tmp/images/generated',
        imagesDir: 'images',
        javascriptsDir: 'scripts',
        fontsDir: 'styles/fonts',
        importPath: 'bower_components',
        httpImagesPath: 'images',
        httpGeneratedImagesPath: 'images/generated',
        httpFontsPath: 'styles/fonts',
        relativeAssets: false,
        assetCacheBuster: false,
        raw: 'Sass::Script::Number.precision = 10\n',
        require: [
          'breakpoint',
          'normalize-scss',
          'susy'          
        ]
      },
      dist: {
        options: {
          generatedImagesDir: 'images/generated',
          outputStyle: 'compressed'
         
        }
      },
      test: {
        options: {
          outputStyle: 'expanded'
        }
      },
      server: {
        options: {
          debugInfo: true,
          outputStyle: 'expanded'
        }
      }
    },

    // The following *-min tasks will produce minified files in the dist folder
    // By default, your `index.html`'s <!-- Usemin block --> will take care of
    // minification. These next options are pre-configured if you do not wish
    // to use the Usemin blocks.
     cssmin: {
       dist: {
         files: {
           'styles/css/main.css': [
             'styles/css/*.css'
           ]
         }
       }
     },
     uglify: {
       dist: {
         files: {
           '<%= yeoman.dist %>/mw.js': [
             '<%= yeoman.dist %>/mw.js'
           ]
         }
       }
     },
     concat: {
       dist: {}
     },

//    svgmin: {
//      dist: {
//        files: [{
//          expand: true,
//          cwd: '<%= yeoman.app %>/images',
//          src: '{,*/}*.svg',
//          dest: '<%= yeoman.dist %>/images'
//        }]
//      }
//    },

//    htmlmin: {
//      dist: {
//        options: {
//          collapseWhitespace: true,
//          conservativeCollapse: true,
//          collapseBooleanAttributes: true,
//          removeCommentsFromCDATA: true,
//          removeOptionalTags: true
//        },
//        files: [{
//          expand: true,
//          cwd: '<%= yeoman.dist %>',
//          src: ['*.html', 'views/{,*/}*.html'],
//          dest: '<%= yeoman.dist %>'
//        }]
//      }
//    },

    // ng-annotate tries to make the code safe for minification automatically
    // by using the Angular long form for dependency injection.
//    ngAnnotate: {
//      dist: {
//        files: [{
//          expand: true,
//          cwd: '.tmp/concat/scripts',
//          src: ['*.js', '!oldieshim.js'],
//          dest: '.tmp/concat/scripts'
//        }]
//      }
//    },

    // Replace Google CDN references
//    cdnify: {
//      dist: {
//        html: ['<%= yeoman.dist %>/*.html']
//      }
//    },

    // Copies remaining files to places other tasks can use
    copy: {
      dist: {
        files: [{
          expand: true,
          dot: true,
          cwd: '<%= yeoman.app %>',
          dest: '<%= yeoman.dist %>',
          src: [
            '*.{ico,png,txt}',
            '.htaccess',
            '*.html',
            'views/{,*/}*.html',
            'images/{,*/}*.{webp}',
            'fonts/*'
          ]
        }, {
          expand: true,
          cwd: '.tmp/images',
          dest: '<%= yeoman.dist %>/images',
          src: ['generated/*']
        }]
      },
      styles: {
        expand: true,
        cwd: 'styles',
        dest: '.tmp/styles/',
        src: '{,*/}*.css'
      }
    },

    // Run some tasks in parallel to speed up the build process
    concurrent: {
      server: [
        'compass:server'
      ],
      test: [
        'compass:test'
      ],
      dist: [
        'compass:dist'
        //'imagemin',
        //'svgmin'
      ]
    },

    // Test settings
    karma: {
      unit: {
        configFile: 'test/karma.conf.js'
        //singleRun: true
      }
    }
  });


  grunt.registerTask('serve', 'Compile then start a connect web server', function (target) {
    if (target === 'dist') {
      return grunt.task.run(['build', 'connect:dist:keepalive']);
    }

    grunt.task.run([
      //'clean:server',
      //'wiredep',
      'concurrent:server',
      'autoprefixer',
      'watch'
    ]);
  });

  grunt.registerTask('test', [
    'clean:server',
    'concurrent:test',
    'autoprefixer',
    'connect:test',
    'karma'
  ]);
  
  grunt.registerTask('dev', [
    'concurrent:test',
    'autoprefixer'
  ]);

  grunt.registerTask('build', [
    'clean:dist',
    //'wiredep',
    //useminPrepare',
    'concurrent:dist',
    'autoprefixer'
    //'concat'
    //'ngAnnotate',
    //'copy:dist',
    //'cdnify',
    //'cssmin'
    //'uglify',
    //'filerev',
    //'usemin',
    //'htmlmin'
  ]);

  grunt.registerTask('default', [
    'newer:jshint',
    'test',
    'build'
  ]);
};

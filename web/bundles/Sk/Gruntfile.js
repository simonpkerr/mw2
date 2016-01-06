// Generated on 2014-10-29 using generator-angular 0.9.8
'use strict';

// # Globbing
// for performance reasons we're only matching one level down:
// 'test/spec/{,*/}*.js'
// use this if you want to recursively match all subfolders:
// 'test/spec/**/*.js'

var path = require('path');

module.exports = function (grunt) {

  // Load grunt tasks automatically
  require('load-grunt-tasks')(grunt);

  // Time how long tasks take. Can help when optimizing build times
  require('time-grunt')(grunt);

  // Configurable paths for the application
  var appConfig = {
    app: require('./bower.json').appPath || 'app',
    bower: 'bower_components',
    dist: 'dist'
  };

  // Define the configuration for all the tasks
  grunt.initConfig({

    // Project settings
    yeoman: appConfig,

    // Watches files for changes and runs tasks based on the changed files
    watch: {
      js: {
        files: ['<%= yeoman.app %>/{,*/}*.js'],
        tasks: ['jshint:all', 'uglify:app']
      },
      jsTest: {
        files: ['test/spec/{,*/}*.js'],
        tasks: ['jshint:test', 'karma']
      },
      compass: {
        files: ['styles/scss/*.{scss,sass}'],
        tasks: ['compass:server', 'autoprefixer']
      },
      gruntfile: {
        files: ['Gruntfile.js']
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
          browsers: ['last 5 versions', 'ie 9', 'ie 8']
          //dest: '<%= yeoman.app %>/styles/css/compiled/main.css'
        }]
      }
    },

    // Compiles Sass to CSS and generates necessary files if requested
    compass: {
      options: {
        sassDir: 'styles/scss',
        cssDir: 'styles/css',
        //generatedImagesDir: '.tmp/images/generated',
        //imagesDir: 'images',
        //javascriptsDir: 'scripts',
        fontsDir: 'styles/fonts',
        importPath: 'bower_components',
        //httpImagesPath: 'images',
        //httpGeneratedImagesPath: 'images/generated',
        httpFontsPath: 'styles/fonts',
        relativeAssets: false,
        assetCacheBuster: false,
        raw: 'Sass::Script::Number.precision = 10\n',
        require: [
          'breakpoint',
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


    uglify: {
      vendors: {
        options: {
          compress: true,
          mangle: true,
          beautify: false
        },
        files: {
          '<%= yeoman.dist %>/js/vendors.js': [
            '<%= yeoman.bower %>/jquery/dist/jquery.js',
            '<%= yeoman.bower %>/jquery-ui/ui/core.js',
            '<%= yeoman.bower %>/jquery-ui/ui/widget.js',
            '<%= yeoman.bower %>/jquery-ui/ui/mouse.js',
            '<%= yeoman.bower %>/jquery-ui/ui/slider.js',
            '<%= yeoman.bower %>/jquery-ui/ui/selectmenu.js',
            '<%= yeoman.bower %>/es5-shim/es5-shim.js',
            '<%= yeoman.bower %>/angular/angular.js',
            '<%= yeoman.bower %>/angular-route/angular-route.js',
            '<%= yeoman.bower %>/angular-resource/angular-resource.js',
            '<%= yeoman.bower %>/angular-sanitize/angular-sanitize.js',
            '<%= yeoman.bower %>/angular-animate/angular-animate.js',
            '<%= yeoman.bower %>/json3/lib/json3.js',
            // '<%= yeoman.bower %>/owlcarousel/owl-carousel/owl.carousel.js',
            // '<%= yeoman.bower %>/owl.carousel/dist/owl.carousel.js',
            '<%= yeoman.bower %>/angular-loading-bar/build/loading-bar.js'
          ]
        }
      },
      app: {
        options: {
          compress: false,
          beautify: true,
          mangle: false
        },
        files: {
          '<%= yeoman.dist %>/js/app.js': [
            '<%= yeoman.app %>/*.js',
            '<%= yeoman.app %>/core/*.js',
            '<%= yeoman.app %>/memoryWall/*.js'
          ]
        }
      }
    },

    express: {
      options: {
        port: 9000,
        hostname: '*'
      },
      livereload: {
        options: {
          server: path.resolve('./server'),
          livereload: true,
          serverreload: true,
          bases: [path.resolve('./app'), path.resolve(__dirname, appConfig.app), path.resolve('./')]
        }
      },
      test: {
        options: {
          server: path.resolve('./server'),
          bases: [path.resolve('./app'), path.resolve(__dirname, 'test'), path.resolve('./')]
        }
      },
      dist: {
        options: {
          server: path.resolve('./server'),
          bases: path.resolve(__dirname, appConfig.dist)
        }
      }
    },
    open: {
      server: {
        url: 'http://localhost:<%= express.options.port %>'
      }
    },


    // Run some tasks in parallel to speed up the build process
    concurrent: {
      server: [
        'compass:server',
        'uglify'
      ],
      test: [
        'compass:test',
        'uglify',
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
      return grunt.task.run(['build', 'express:dist:keepalive']);
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
    'express:test',
    'karma'
  ]);

  grunt.registerTask('server', [
    'express:livereload',
    'open'

  ]);

  grunt.registerTask('dev', [
    'concurrent:test',
  ]);

  grunt.registerTask('build', [
    'clean:dist',
    //'wiredep',
    //useminPrepare',
    'concurrent:dist',
    'autoprefixer'
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
    'test',
    'build'
  ]);
};

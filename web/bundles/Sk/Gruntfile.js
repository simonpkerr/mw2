// Generated on 2014-10-29 using generator-angular 0.9.8
'use strict';

// # Globbing
// for performance reasons we're only matching one level down:
// 'test/spec/{,*/}*.js'
// use this if you want to recursively match all subfolders:
// 'test/spec/**/*.js'

var path = require('path');

module.exports = function (grunt) {

  // Time how long tasks take. Can help when optimizing build times
  require('time-grunt')(grunt);

  grunt.loadNpmTasks('grunt-contrib-watch');

  // Load grunt tasks automatically
  // require('load-grunt-tasks')(grunt);
  require('jit-grunt')(grunt, {
    useminPrepare: 'grunt-usemin',
    ngtemplates: 'grunt-angular-templates'
  });



  // Configurable paths for the application
  var appConfig = {
    app: require('./bower.json').appPath || 'app',
    bower: 'bower_components',
    dist: 'dist',
    scripts: [
      // JS files to be included by includeSource task into index.html
      'app/app.module.js',
      'app/**/*.module.js',
      'app/**/*.js',
      '!app/lib/*.js',
      '!app/**/*.spec.js'
    ]
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

    express: {
      options: {
        port: 9000,
        hostname: '*'
      },
      livereload: {
        options: {
          server: path.resolve('./server'),
          livereload: true,
          serverreload: false,
          bases: [
            path.resolve(__dirname, appConfig.app),
            path.resolve('./'),
            path.resolve('./tmp'),
            // path.resolve('./app'),
            path.resolve('/bower_components')
          ]
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

    postcss: {
      options: {
        processors: [
          require('autoprefixer-core')({browsers: ['last 1 version']})
        ]
      },
      server: {
        options: {
          map: true
        },
        files: [{
          expand: true,
          cwd: '.tmp/styles/',
          src: '{,*/}*.css',
          dest: '.tmp/styles/'
        }]
      },
      dist: {
        files: [{
          expand: true,
          cwd: '.tmp/styles/',
          src: '{,*/}*.css',
          dest: '.tmp/styles/'
        }]
      }
    },

    wiredep: {
      app: {
        src: ['index.html'],
        ignorePath:  /\.\.\//,
        exclude: [
          'bower_components/requirejs',
          'bower_components/jquery-ui'
        ]
      },
      test: {
        devDependencies: true,
        src: '<%= karma.unit.configFile %>',
        ignorePath:  /\.\.\//,
        exclude: [
        ],
        fileTypes:{
          js: {
            block: /(([\s\t]*)\/{2}\s*?bower:\s*?(\S*))(\n|\r|.)*?(\/{2}\s*endbower)/gi,
              detect: {
                js: /'(.*\.js)'/gi
              },
              replace: {
                js: '\'{{filePath}}\','
              }
            }
          }
      },
      sass: {
        src: ['styles/{,*/}*.{scss,sass}'],
        ignorePath: /(\.\.\/){1,2}bower_components\//
      }
    },

    // Compiles Sass to CSS and generates necessary files if requested
    compass: {
      options: {
        sassDir: 'styles/scss',
        cssDir: '.tmp/css',
        generatedImagesDir: '.tmp/images/generated',
        imagesDir: 'images',
        javascriptsDir: '<%= yeoman.app %>',
        fontsDir: 'styles/fonts',
        importPath: ['./bower_components', './styles'],
        httpImagesPath: 'images',
        httpGeneratedImagesPath: 'images/generated',
        httpFontsPath: 'styles/fonts',
        relativeAssets: false,
        assetCacheBuster: false,
        raw: 'Sass::Script::Number.precision = 10\n',
        require: [
          'breakpoint',
          'susy',
          'ceaser-easing'
        ],
        sourcemap: true
      },
      dist: {
        options: {
          generatedImagesDir: '<%= yeoman.dist %>/images/generated',
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
          outputStyle: 'expanded',
          sourcemap: true
        }
      }
    },

    useminPrepare: {
      html: 'index.html',
      options: {
        dest: '<%= yeoman.dist %>',
        flow: {
          html: {
            steps: {
              js: ['concat', 'uglifyjs'],
              css: ['cssmin']
            },
            post: {}
          }
        }
      }
    },

    // Performs rewrites based on filerev and the useminPrepare configuration
    usemin: {
      html: ['<%= yeoman.dist %>/{,*/}*.html'],
      css: ['<%= yeoman.dist %>/styles/{,*/}*.css'],
      js: ['<%= yeoman.dist %>/scripts/{,*/}*.js'],
      options: {
        assetsDirs: [
          '<%= yeoman.dist %>',
          '<%= yeoman.dist %>/images',
          '<%= yeoman.dist %>/styles'
        ],
        patterns: {
          js: [[/(images\/[^''""]*\.(png|jpg|jpeg|gif|webp|svg))/g, 'Replacing references to images']]
        }
      }
    },

    htmlmin: {
      dist: {
        options: {
          collapseWhitespace: true,
          conservativeCollapse: true,
          collapseBooleanAttributes: true,
          removeCommentsFromCDATA: true
        },
        files: [{
          expand: true,
          cwd: '<%= yeoman.dist %>',
          src: ['*.html'],
          dest: '<%= yeoman.dist %>'
        }]
      }
    },

    ngtemplates: {
      dist: {
        options: {
          module: 'app',
          htmlmin: '<%= htmlmin.dist.options %>',
          usemin: 'scripts/scripts.js'
        },
        cwd: '<%= yeoman.app %>',
        src: '{,**/}*.html',
        //this gets added to scripts.js during the build task
        dest: '.tmp/templateCache.js'
      }
    },

    copy: {
      dist: {
        files: [{
          expand: true,
          dot: true,
          cwd: './',
          dest: '<%= yeoman.dist %>',
          src: [
            '*.{ico,png,txt}',
            '*.html',
            'images/{,*/}*.{webp}',
            'styles/fonts/{,*/}*.*'
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

    /*uglify: {
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
    },*/




    // Run some tasks in parallel to speed up the build process
    concurrent: {
      server: [
        'compass:server'
        // 'uglify'
      ],
      test: [
        'compass:test'
        // 'uglify',
      ],
      dist: [
        'compass:dist'
        //'imagemin',
        //'svgmin'
      ]
    },

    includeSource: {
      options: {
        basePath: './'
      },

      app: {
        files: {'index.html' : 'index.html'}
      }
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
      'clean:server',
      'wiredep',
      'includeSource:app',
      'concurrent:server',
      'postcss:server',
      'express:livereload',
      'open',
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
    // 'autoprefixer'
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

'use strict';

// # Globbing
// for performance reasons we're only matching one level down:
// 'test/spec/{,*/}*.js'
// use this if you want to match all subfolders:
// 'test/spec/**/*.js'

module.exports = function (grunt) {

    // Load grunt tasks automatically
    require('load-grunt-tasks')(grunt);

    // configurable paths
    var config = {
        app: 'upload/',
        dist: '../'
    };

    grunt.initConfig({
        config: config,
        watch: {
            upload: {
                files: ['<%= config.app %>/**'],
                tasks: ['sync:main']
            }
        },
        sync: {
            main: {
                files: [{
                    cwd: '<%= config.app %>',
                    src: [
                        '**'
                    ],
                    dest: '<%= config.dist %>'
                }]
            }
        },
        copy: {
            dist: {
                files: [{
                    expand: true,
                    dot: true,
                    cwd: '<%= config.app %>',
                    dest: '<%= config.dist %>',
                    src: [
                        '**'
                    ]
                }]
            }
        }
    });
    grunt.loadNpmTasks('grunt-newer');

    grunt.registerTask('live', [
        'build',
        'watch'
    ]);

    grunt.registerTask('build', [
        'newer:copy:dist'
    ]);

    grunt.registerTask('default', [
        'build'
    ]);
};
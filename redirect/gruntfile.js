/**
 * Created by Chanx on 3/4/2016.
 */

module.exports = function (grunt) {
    grunt.initConfig({
        sass: {
            compile: {
                files: {
                    'public/css/app.css' : 'resources/assets/sass/app.scss'
                }
            }
        }
    });
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.registerTask('default',['sass']);
};
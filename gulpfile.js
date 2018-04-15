// npm install gulp-concat gulp-uglify gulp-rename browser-sync --save-dev


// Include gulp
var gulp = require('gulp');

// Include Our Plugins
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var browserSync = require('browser-sync').create();



// Concatenate JS
//gulp.task('scripts', function() {
//    return gulp.src('js/*.js')
//        .pipe(concat('all.js'))
//        .pipe(gulp.dest('js/'));
//});


////////
// localhost address with a port + watching scss/html files
gulp.task('serve', function() {

    browserSync.init({
        server:true
    });

    gulp.watch("**/*.css").on('change', browserSync.reload);
    gulp.watch('js/*.js');
    gulp.watch("**/*.html").on('change', browserSync.reload);
});
/////////

// Default Task
gulp.task('default', ['serve']);

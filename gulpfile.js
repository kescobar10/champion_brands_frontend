// npm install gulp-sass browser-sync --save-dev


// Include gulp
var gulp = require('gulp');

// Include Our Plugins
var sass = require('gulp-sass');
var browserSync = require('browser-sync').create();


////////
// directory server + watching scss/html files
gulp.task('serve', ['sass'], function() {

    browserSync.init({
        server:true
    });
    gulp.watch("**/*.css").on('change', browserSync.reload);
    gulp.watch("scss/*.scss", ['sass']);
    gulp.watch("**/*.html").on('change', browserSync.reload);
});

// Compile sass into CSS & auto-inject into browsers
gulp.task('sass', function() {
    return gulp.src("scss/*.scss")
        .pipe(sass())
        .pipe(gulp.dest("css/"))
        .pipe(browserSync.stream());
});
/////////


// Default Task
gulp.task('default', ['serve']);

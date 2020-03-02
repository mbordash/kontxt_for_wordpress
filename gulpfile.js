// Example of how to zip a directory
var gulp = require('gulp');
var zip = require('gulp-zip');

gulp.task('zip', function () {
    return gulp.src([
        './**/*',
        '!./{node_modules,node_modules/**/*}',
        '!./assets/{sass,sass/*}',
        '!./gulpfile.js',
        '!./package.json',
        '!./package-lock.json',
        '!./admin/js/kontxt-admin-panel/{node_modules,node_modules/**/*}',
        '!./admin/js/kontxt-admin-panel/package.json',
        '!./admin/js/kontxt-admin-panel/package-lock.json',
        '!./admin/js/kontxt-admin-panel/{src,src/*}',
    ])
        .pipe(zip('kontxt-for-wordpress.zip'))
        .pipe(gulp.dest('./../'));
});

const gulp = require('gulp');
const browserSync = require('browser-sync').create();

gulp.task('serve', function () {
    browserSync.init({
        proxy: "http://localhost:8000",
        notify: false,
        open: true
    });

    gulp.watch("app/**/*.php").on('change', browserSync.reload);
    gulp.watch("config/**/*.php").on('change', browserSync.reload);
    gulp.watch("core/**/*.php").on('change', browserSync.reload);
    gulp.watch("public/css/**/*.css").on('change', browserSync.reload);
    gulp.watch("public/js/**/*.js").on('change', browserSync.reload);
    gulp.watch("routes/**/*.php").on('change', browserSync.reload);
    gulp.watch("src/**/*.php").on('change', browserSync.reload);
});

gulp.task('default', gulp.series('serve'));
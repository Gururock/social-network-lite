var gulp = require('gulp'),
	browserify = require('browserify'),
	babelify = require('babelify'),
	source = require('vinyl-source-stream'),
    buffer = require('vinyl-buffer'),
    uglify = require('gulp-uglify');
  
gulp.task('build', function () {
  process.env.NODE_ENV = 'production';
  browserify({
    entries: [ './react/app.jsx' ],
    extensions: [ '.js', '.jsx' ],
    debug: true
  })
  .transform(babelify.configure({
    presets: ["es2015", "react"]
  }))
  .bundle()
  .pipe(source('bundle.js'))
  .pipe(buffer())
  .pipe(uglify())
  .pipe(gulp.dest('./web/js'))
});

gulp.task('watch', function(){
	gulp.watch(['./react/*.jsx', './react/components/*.jsx', './react/layout/*.jsx'], ['default']);
});

gulp.task('default', ['build', 'watch']);
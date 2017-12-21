const gulp = require('gulp');
const babelify = require('babelify');
const babel = require('gulp-babel');
const jshint = require('gulp-jshint');
const sass = require('gulp-sass');
const uglify = require('gulp-uglify');
const rename = require('gulp-rename');
const sourcemaps = require('gulp-sourcemaps');

// TODO: add autoprefixer

// Compile SASS
gulp.task('sass', function() {
  return gulp
    .src('./src/frontend/web/**/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({ outputStyle: 'compressed' }))
    .pipe(
      rename({
        extname: '.min.css'
      })
    )
    .pipe(sourcemaps.write('.'))
    .pipe(
      gulp.dest(function(file) {
        return file.base.replace(/\/src/, '/view');
      })
    );
});

// Compile JS
gulp.task('scripts', function() {
  return gulp
    .src('./src/frontend/web/**/*.js')
    .pipe(sourcemaps.init())
    .pipe(
      babel({
        presets: ['es2015']
      })
    )
    .pipe(uglify())
    .pipe(
      rename({
        extname: '.min.js'
      })
    )
    .pipe(sourcemaps.write('.'))
    .pipe(
      gulp.dest(function(file) {
        return file.base.replace(/\/src/, '/view');
      })
    );
});

// Watch Files For Changes
gulp.task('watch', function() {
  gulp.watch('./src/**/*.js', ['scripts']);
  gulp.watch('./src/**/*.scss', ['sass']);
});

// Default Task
gulp.task('default', ['sass', 'scripts']);
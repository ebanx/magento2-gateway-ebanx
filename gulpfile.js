var gulp = require("gulp"),
  babelify = require("babelify"),
  babel = require("gulp-babel"),
  jshint = require("gulp-jshint"),
  sass = require("gulp-sass"),
  uglify = require("gulp-uglify"),
  rename = require("gulp-rename"),
  sourcemaps = require("gulp-sourcemaps");

//autoprefixer

// Compile SASS
gulp.task("sass", function() {
  return gulp
    .src("./src/frontend/web/**/*.scss")
    .pipe(sourcemaps.init())
    .pipe(sass({ outputStyle: "compressed" }))
    .pipe(
      rename({
        extname: ".min.css"
      })
    )
    .pipe(sourcemaps.write("."))
    .pipe(
      gulp.dest(function(file) {
        var src = file.base.replace(/\/src/, "/view");
        return src;
      })
    );
});

// Compile JS
gulp.task("scripts", function() {
  return gulp
    .src("./src/frontend/web/**/*.js")
    .pipe(sourcemaps.init())
    .pipe(
      babel({
        presets: ["es2015"]
      })
    )
    .pipe(uglify())
    .pipe(
      rename({
        extname: ".min.js"
      })
    )
    .pipe(sourcemaps.write("."))
    .pipe(
      gulp.dest(function(file) {
        var src = file.base.replace(/\/src/, "/view");
        return src;
      })
    );
});

var somethingToDo = function(file) {
  console.log(file);
};

// Watch Files For Changes
gulp.task("watch", function() {
  gulp.watch("./src/**/*.js", ["scripts"]);
  gulp.watch("./src/**/*.scss", ["sass"]);
});

// Default Task
gulp.task("default", ["sass", "scripts"]);

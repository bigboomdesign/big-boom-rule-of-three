var	gulp 		= require('gulp'),
	gutil 		= require('gulp-util'),
	compass 	= require("gulp-compass"),
	gulpif 		= require("gulp-if"),
	uglify 		= require("gulp-uglify"),
	removeEmpty = require("gulp-remove-empty-lines");
	 
var env,
	jsSources,
	sassSources,
	sassStyle,
	outputDir;
function panel(str){ return "components/"+str.toString();}

env = process.env.NODE_ENV || "development";
if(env==="development"){
	outputDir = 'builds/development/';
	sassStyle = "expanded";
}
else{ 
	outputDir = 'builds/production/';
	sassStyle = "compressed";
}
sassSources = [panel('/sass/comp.scss'), panel('/sass/admin_comp.scss')];

// php and js read from development
phpSources = ['builds/development/**/*.php'];
jsSources = ['builds/development/js/*.js'];

gulp.task("default", ['php', 'js', 'compass', 'watch']);
gulp.task("php", function(){
	gulp.src(phpSources)
		.pipe(gulpif(env==="production", removeEmpty()))
		.pipe(gulpif(env==="production", gulp.dest("builds/production")))
});
gulp.task("js", function(){
	gulp.src(jsSources)
		.pipe(gulpif(env==="production", uglify()))
		.pipe(gulp.dest(outputDir+'js'))
});
gulp.task("compass", function(){
	gulp.src(sassSources)
		.pipe(compass({
			sass: panel('/sass'),
			css: outputDir+'/css',
			image: outputDir+'images',
			style: sassStyle,
			comments: (env==="development") ? true : false,
			require: ['breakpoint', 'susy'],
			})
			.on("error", gutil.log)
		)
		.pipe(gulp.dest(outputDir+'css'))
});
gulp.task("watch", function(){
//	gulp.watch(sassSources, ['compass']);
	gulp.watch(jsSources, ["js"]);
	gulp.watch(panel('/sass/*.scss'), ["compass"]);
	gulp.watch(phpSources, ['php']);
});
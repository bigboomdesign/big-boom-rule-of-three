var	gulp 		= require('gulp'),
	compass 	= require("gulp-compass");

	 
var sassSources;

function panel(str){ return "components/"+str.toString();}

outputDir = 'trunk/';
sassStyle = "compressed";

sassSources = [panel('sass/comp.scss'), panel('sass/admin_comp.scss')];

gulp.task( "default", [ 'compass', 'watch' ] );

gulp.task("compass", function(){
	gulp.src( sassSources )
		.pipe(compass({
			sass: panel('sass'),
			css: outputDir + 'css',
			image: outputDir + 'images',
			style: sassStyle,
			comments: false,
			require: ['breakpoint', 'susy'],
			})
		)
		.pipe( gulp.dest( outputDir + 'css' ) )
});
gulp.task("watch", function(){
	gulp.watch( panel('sass/*.scss'), ['compass'] );
});
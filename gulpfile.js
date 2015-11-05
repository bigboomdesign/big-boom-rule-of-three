var	gulp 		= require('gulp'),
	compass 	= require("gulp-compass");

	 
var sassSources;

outputDir = 'trunk/';
sassStyle = "compressed";

sassSources = [ 'sass/comp.scss', 'sass/admin_comp.scss' ];

gulp.task( "default", [ 'compass', 'watch' ] );

gulp.task("compass", function(){
	gulp.src( sassSources )
		.pipe(compass({
			sass: 'sass',
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
	gulp.watch( 'sass/*.scss', ['compass'] );
});
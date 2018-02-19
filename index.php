<?php
/**
 * @author Nicholas Sardo
 *
 * quick and dirty reference sketch on using the Flight Micro-Framework for developing
 * an HTTP Rest API in PHP
 *
*/

require 'flight/Flight.php';
require 'db.php';

/* set an instance of database w/in Flight Framework */
Flight::set( 'd', DB::get_Instance() );


/**
 * check credentials before allowing access to API
*/
Flight::before( 'validate', function( &$params, &$output ){
	#rough sketch for validation
	#$hash = hmac( 'sha1', $_POST['json'].$_POST['time'].$_POST['public-key'], $secret );
	#if ( $hash === $_POST['hash']){
		#proceed
	#} else {
	#	sendResponse( 401, 'Unauthorized' );
	#}

});

/**
 * retrieve data about a student
 * Example URL:  localhost/student/1
*/
Flight::route('GET /student/@id:[0-9]+', function( $id ){
	$d = Flight::get('d');
	Flight::json( array( $d->quer( $id ) ) );
});

/**
 * create new students contact info
 * Example URL:  localhost/student/new/contact
 * send record in body as json
*/
Flight::route('POST /student/new/contact', function(){
	$d = Flight::get('d');

	#send json coming in body to the db
	$d->createContactInfo( Flight::request()->body );
});

/**
 * retrieve data about students courses
 * Example URL:  localhost/student/1/courses
*/
Flight::route('GET /student/@id:[0-9]+/courses', function( $id ){
	$d = Flight::get( 'd' );
	$c = $d->getStudentCourses( $id );
	#return json
	Flight::json( array( $c ) );
});

/**
 * retrieve data about students grade for specific course
 * Example URL:  localhost/student/1/grade/course_name
*/
Flight::route('GET /student/@id:[0-9]+/grade/@course/', function( $id, $course ){
	$d = Flight::get( 'd' );
	$g = $d->getGrade( $id, $course );

	#return json
	Flight::json( array( $g ) );
});

/**
 * insert data student class-info and grade
 * Example URL:  localhost/student/1/course_name/letter_grade/start_date/end_date
 * prob. should be json record sent via POST request
*/
Flight::route('PUT /student/@id:[0-9]+/create/@course/@grade/@start/@end', function( $id, $course, $grade, $start, $end ){
	$d = Flight::get( 'd' );
	$d->createStudentCourse( $id, $course, $grade, $start, $end );
});

/**
 * update data student grade
 * Example URL:  localhost/student/1/course_name/new_grade
*/
Flight::route('POST /student/@id:[0-9]+/@course/@grade', function( $id, $course, $grade ){
	$d = Flight::get( 'd' );
	$d->updateGrade( $id, $course, $grade );
});

/**
 * EXAMPLE CODE FOR PUT REQUEST
 * Obtain variables w/in body of PUT Request
*/
Flight::route('PUT /', function(){
	
	 if ( $_SERVER['REQUEST_METHOD'] == "PUT" ){

	 	#separate key=value pairs in request body
	 	parse_str(Flight::request()->body, $put_vars );

	 	/* doesn't work because this is already done in Flight */
	 	#parse_str( file_get_contents('php://input'), $post_vars );

	 	#show that keys/values are now separated
	 	print_r( $put_vars );
	 }
});

#EXAMPLE ROUTE
Flight::route('GET /', function(){
    echo 'not implemented.' . "\n";
});

#EXAMPLE:  add  foo=bar to request body, and send POST request
Flight::route('POST /', function(){
	echo Flight::request()->data['foo'];
});


Flight::start();
?>

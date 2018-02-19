<?php
/**
 * @author Nicholas Sardo
 *
 * Quick and crude SQLite Class to handle Database for Flight Rest Reference Impl.
*/
class DB {

	private $db;

	private function __construct(){
		$this->db = new PDO( 'sqlite:School.db' );
		if ( !$this->db ) die ( $error );
		return $this->get_db();
	}

	private function __clone(){}

	private function __wakeup(){}

	function get_db(){
		return $this->db;
	}

	public static function get_instance(){
		static $instance = null;

		if ( (is_null( $instance )) )
			$instance = new DB();
		return $instance;

	}

	function quer( $id ){
		$stmt = $this->db->prepare( "SELECT * from Students WHERE id= :id" );
		$stmt->bindParam( ':id', $id, PDO::PARAM_INT );

		$stmt->execute();

		$result = $stmt->fetchAll();
		foreach( $result as $row){
			return array( 'id' => $row['id'], 'full_name' => $row['full_name'],
				         'school_name' => $row['school_name'], 'phone' => $row['phone'],
				         'email' => $row['email'] );
		}
	}

	function createContactInfo( $js ){
		$ar = json_decode( $js, true );
		extract( $ar[0] );
		#(null,"moe","eschool","12334","em@the")
		$sql = "INSERT INTO Students( id,full_name, school_name, phone, email ) VALUES ( null,'$full_name', '$school_name', '$phone', '$email' )";

		$this->db->exec( $sql );
	}

	function updateGrade( $id, $course, $grade ){
		$stmt = $this->db->prepare( "Update Courses set grade= :grade where student_id= :id and name= :course" );
		$stmt->bindParam( ':id', $id, PDO::PARAM_INT );
		$stmt->bindParam( ':course', $course, PDO::PARAM_STR, 128 );
		$stmt->bindParam( ':grade', $grade, PDO::PARAM_STR, 128 );

		$stmt->execute();
	}

	function createStudentCourse( $id, $course, $grade, $start_date, $end_date ){
		$stmt = "INSERT INTO Courses ( id, student_id, name, start_date, end_date, grade ) VALUES ( null, '$id', '$course', '$start_date', '$end_date', '$grade' ";

		$this->db->exec( $stmt );
	}

	function getGrade( $id, $course ){
		$stmt = $this->db->prepare( "SELECT * FROM Courses WHERE student_id = :id and name = :course LIMIT 1" );
		$stmt->bindParam( ':id', $id, PDO::PARAM_INT );
		$stmt->bindParam( ':course', $course, PDO::PARAM_STR, 128 );

		$stmt->execute();

		$result = $stmt->fetchAll();
		foreach( $result as $row ){
			return $row['grade'];
		}
	}

	function getStudentCourses( $id ){
		$a = array();
		$stmt = $this->db->prepare( "SELECT * FROM Courses WHERE student_id = :id" );
		$stmt->bindParam( ':id', $id, PDO::PARAM_INT );

		$stmt->execute();

		$result = $stmt->fetchAll(); #fetch(PDO::FETCH_OBJ); #fetch(); #fetch(PDO::FETCH_ASSOC); #fetchAll(PDO::FETCH_ASSOC);
		foreach( $result as $row ){
			$a[] = array( 'course' => $row['name'], 'grade' => $row['grade'], 'start_date' => $row['start_date'], 'end_date' => $row['end_date'] );
		}
		return $a;
	}

}
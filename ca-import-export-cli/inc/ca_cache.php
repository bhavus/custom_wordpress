<?php
/**
 * Class to dynamically create and handle xml files
 */
class CA_Cache {

	public $ca = array();

	public $oldOffset = 0;

	public function get_ca_stack(){

		return $this->ca;
	}
    /**
     * @return array retruns a stack of generatet xml files
     */
	public function unlink_ca( $ca_cache_name ){

		$ca_filepath = apply_filters( 'ca_filepath', WP_CONTENT_DIR . '/uploads/xmlfiles/' );
		$ca_filename = $ca_filepath . apply_filters( 'ca_filename', $ca_cache_name );

		if ( file_exists( $ca_filename ) ) {

			unlink( $ca_filename );

		}

	}
    /**
     * Function to add file(s) to the specified directory
     *
     * @param string $ca_cache_name
     * @param string $ca_data
     *
     * @return void
     * @access public
     */
	public function write( $ca_data, $ca_cache_name, $gz = false ) {

		$ca_filepath = apply_filters( 'ca_filepath', WP_CONTENT_DIR . '/uploads/xmlfiles/' );
		$ca_filename = $ca_filepath . apply_filters( 'ca_filename', $ca_cache_name );

		if ( ! file_exists( $ca_filepath ) ) {
			mkdir( $ca_filepath, 0777, TRUE );
		}

		file_put_contents( $ca_filename, $ca_data, FILE_APPEND | LOCK_EX );
		$this->ca[ $ca_cache_name ] = $ca_filename;

		if ( $gz === TRUE ) {
			$this->ca[ $ca_cache_name ] = $this->gzCompressFile( $ca_filename );
		}
	}

}

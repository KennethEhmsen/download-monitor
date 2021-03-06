<?php

class DLM_Unit_Test_Case extends WP_UnitTestCase {

	/**
	 * tearDown
	 */
	public function tearDown() {
		global $wpdb;
		parent::tearDown();
		DLM_Test_WP_DB_Helper::truncate( $wpdb->posts );
		DLM_Test_WP_DB_Helper::truncate( $wpdb->postmeta );
		DLM_Test_WP_DB_Helper::truncate( $wpdb->term_relationships );
		DLM_Test_WP_DB_Helper::truncate( $wpdb->term_taxonomy );
		DLM_Test_WP_DB_Helper::truncate( $wpdb->termmeta );
		DLM_Test_WP_DB_Helper::truncate( $wpdb->terms );
		DLM_Test_WP_DB_Helper::truncate( $wpdb->download_log );
	}

	/**
	 * Setup test case
	 */
	public function setUp() {

		parent::setUp();

		$this->setOutputCallback( array( $this, 'filter_output' ) );
	}


	/**
	 * Strip newlines and tabs when using expectedOutputString() as otherwise
	 * the most template-related tests will fail due to indentation/alignment in
	 * the template not matching the sample strings set in the tests
	 */
	public function filter_output( $output ) {

		$output = preg_replace( '/[\n]+/S', '', $output );
		$output = preg_replace( '/[\t]+/S', '', $output );

		return $output;
	}

	/**
	 * Asserts thing is not WP_Error
	 *
	 * @param mixed $actual
	 * @param string $message
	 */
	public function assertNotWPError( $actual, $message = '' ) {
		$this->assertNotInstanceOf( 'WP_Error', $actual, $message );
	}

	/**
	 * Asserts thing is WP_Error
	 *
	 * @param $actual
	 * @param string $message
	 */
	public function assertIsWPError( $actual, $message = '' ) {
		$this->assertInstanceOf( 'WP_Error', $actual, $message );
	}

	/**
	 * Backport assertNotFalse to PHPUnit 3.6.12 which only runs in PHP 5.2
	 *
	 * @param $condition
	 * @param string $message
	 * @return mixed
	 */
	public static function assertNotFalse( $condition, $message = '' ) {

		if ( version_compare( phpversion(), '5.3', '<' ) ) {

			self::assertThat( $condition, self::logicalNot( self::isFalse() ), $message );

		} else {

			parent::assertNotFalse( $condition, $message );
		}
	}

}

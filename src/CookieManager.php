<?php

class DLM_Cookie_Manager {

	private $js_cookies = array();

	const KEY_DOWNLOADING = 'wp_dlm_downloading';

	/**
	 * DLM_Cookie_Manager constructor.
	 */
	public function __construct() {

		// set JavaScript cookies in footer
		add_action( 'wp_footer', array( $this, 'set_js_cookies' ) );
	}

	/**
	 * Get cookie data
	 *
	 * @param $key string
	 *
	 * @return array|null
	 */
	public function get_cookie_data( $key ) {
		$cdata = null;
		if ( ! empty( $_COOKIE[ $key ] ) ) {
			$cdata = json_decode( base64_decode( $_COOKIE[ $key ] ), true );
		}

		return $cdata;
	}

	/**
	 * Check if the cookie is exists for this download & version. If it does exists the requester has requested the exact same download & version in the past minute.
	 *
	 * @param DLM_Download $download
	 *
	 * @return bool
	 */
	public function downloading_exists( $download ) {
		$exists = false;

		// get JSON data
		$cdata = self::get_cookie_data( self::KEY_DOWNLOADING );

		// check if no parse errors occurred
		if ( null != $cdata && is_array( $cdata ) && ! empty( $cdata ) ) {

			// check in cookie data for download AND version ID
			if ( $cdata['download'] == $download->get_id() && $cdata['version'] == $download->get_version()->get_version_number() ) {
				$exists = true;
			}
		}


		return $exists;
	}

	/**
	 * Set cookie
	 *
	 * @param DLM_Download $download
	 */
	public function set_downloading_cookie( $download ) {

		// set cookie
		$this->set_cookie( self::KEY_DOWNLOADING, array(
			'download' => $download->get_id(),
			'version'  => $download->get_version()->get_version_number()
		), 60, true );
	}

	/**
	 * Set the actual cookie
	 *
	 * @param string $key
	 * @param array $data
	 * @param int $expire_seconds
	 * @param bool $server_side
	 */
	public function set_cookie( $key, $data, $expire_seconds = 60, $server_side = false ) {

		if ( true === $server_side ) {

			// set cookie via PHP
			setcookie( $key, base64_encode( json_encode( $data ) ), time() + $expire_seconds, COOKIEPATH, COOKIE_DOMAIN, false, true );
		} else {

			// JS cookies will be added later
			$js_cookies[] = array(
				'key'    => $key,
				'data'   => $data,
				'expire' => $expire_seconds
			);
		}

	}

	/**
	 * Set JS cookies in wp_footer
	 */
	public function set_js_cookies() {
		if ( ! empty( $this->js_cookies ) ) {
			echo "<script type='text/javascript'>" . PHP_EOL;
			foreach ( $this->js_cookies as $cookie ) {

				$dt = new \DateTime();
				$dt->setTimezone( new DateTimeZone( "UTC" ) );
				$dt->modify( "+" . $cookie['expire'] . " seconds" );

				echo 'document.cookie = "' . $cookie['key'] . '=' . base64_encode( json_encode( $cookie['data'] ) ) . '; expires= ' . $dt->format( "D, d M Y H:i:s" ) . ' UTC; path=/";' . PHP_EOL;
			}
			echo "</script>" . PHP_EOL;
		}
	}

}
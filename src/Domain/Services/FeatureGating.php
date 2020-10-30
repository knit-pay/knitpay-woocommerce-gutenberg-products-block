<?php
namespace Automattic\WooCommerce\Blocks\Domain\Services;

/**
 * Service class that handles the feature flags.
 *
 * @internal
 */
class FeatureGating {

	/**
	 * Current flag value.
	 *
	 * @var int
	 */
	private static $flag;
	const EXPERIMENTAL_FLAG   = 3;
	const FEATURE_PLUGIN_FLAG = 2;
	const CORE_FLAG           = 1;

	/**
	 * Set correct flag.
	 */
	public function init() {
		$default_flag  = defined( 'WC_BLOCKS_IS_FEATURE_PLUGIN' ) ? self::FEATURE_PLUGIN_FLAG : self::CORE_FLAG;
		$allowed_flags = [ self::EXPERIMENTAL_FLAG, self::FEATURE_PLUGIN_FLAG, self::CORE_FLAG ];

		if ( file_exists( __DIR__ . '/../../../blocks.ini' ) ) {
			$woo_options = parse_ini_file( __DIR__ . '/../../../blocks.ini' );
			self::$flag  = is_array( $woo_options ) && in_array( intval( $woo_options['woocommerce_blocks_phase'] ), $allowed_flags, true ) ? $woo_options['woocommerce_blocks_phase'] : $default_flag;
		} else {
			self::$flag = $default_flag;
		}
	}

	/**
	 * Returns the current flag value.
	 *
	 * @return int
	 */
	public static function get_flag() {
		return self::$flag;
	}

	/**
	 * Checks if we're executing the code in an experimental build mode.
	 *
	 * @return boolean
	 */
	public static function is_experimental_build() {
		return self::$flag >= self::EXPERIMENTAL_FLAG;
	}

	/**
	 * Checks if we're executing the code in an feature plugin or experimental build mode.
	 *
	 * @return boolean
	 */
	public static function is_feature_plugin_build() {
		return self::$flag >= self::FEATURE_PLUGIN_FLAG;
	}
}

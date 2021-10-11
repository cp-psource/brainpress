<?php

class BrainPress_Helper_SocialMedia {
	/**
	 * Function all to get Social Sharing services with labels.
	 *
	 * @since 2.0.5
	 *
	 * @return array Array of social services.
	 */
	public static function get_social_sharing_array() {
		$services = array(
			'facebook' => __( 'Facebook', 'brainpress' ),
			'twitter' => __( 'Twitter', 'brainpress' ),
			'linkedin' => __( 'LinkedIn', 'brainpress' ),
			'email' => __( 'Email', 'brainpress' ),
		);
		/**
		 * Filter allow to add some social media.
		 *
		 * Filter allow to add some social media. You need to use
		 * "brainpress_social_link_{$service}" filter to handle frontend output.
		 *
		 * @see BrainPress_Data_Shortcode_CourseTemplatecourse_social_links()
		 *
		 * @since 2.0.5
		 */
		return apply_filters( 'brainpress_social_media_social_sharing_array', $services );
	}
	/**
	 * Function all to get Social Sharing services keys.
	 *
	 * @since 2.0.5
	 *
	 * @return array Array of social services keys.
	 */
	public static function get_social_sharing_keys() {
		$services = self::get_social_sharing_array();
		$services = array_keys( $services );
		return $services;
	}
}
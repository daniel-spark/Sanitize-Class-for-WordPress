<?php

class Sanitize {
	
	/**
 	 * Mapping of data types to their respective sanitization and escape functions.
     * 
     * - text: Sanitizes by removing any HTML tags. Outputs safely for HTML.
     * - textarea: Sanitizes for a textarea context (preserving newlines). Outputs safely for a textarea.
     * - email: Sanitizes to ensure a valid email format. Outputs safely for HTML.
     * - url: Sanitizes to ensure a valid URL format. Outputs with special chars safely encoded.
     * - key: Sanitizes by removing unwanted characters. Outputs safely for HTML.
     * - filename: Sanitizes to remove potential vulnerability-causing characters. Outputs safely for HTML attributes.
     * - html: Filters to only allow specific HTML tags (based on WP kses rules). Outputs safely for HTML.
     * - js: Sanitizes by neutralizing potential JS code execution. Outputs safely for JS contexts.
     * - attribute: Sanitizes to ensure no malicious content. Outputs safely for HTML attributes.
     * 
     * @var array
     */
	
    private static $typeMapping = [
		'text'      => ['input' => 'sanitize_text_field',		'output' => 'esc_html'],
		'textarea'  => ['input' => 'sanitize_textarea_field',	'output' => 'esc_textarea'],
		'email'     => ['input' => 'sanitize_email',			'output' => 'esc_html'],
		'url'       => ['input' => 'esc_url_raw',				'output' => 'esc_url'],
		'key'       => ['input' => 'sanitize_key',				'output' => 'esc_html'],
		'filename'  => ['input' => 'sanitize_file_name',		'output' => 'esc_attr'],
		'html'      => ['input' => 'wp_kses_post',				'output' => 'esc_html'],
		'js'		=> ['input' => 'self::sanitize_js',			'output' => 'esc_js'],
		'attribute' => ['input' => 'sanitize_text_field',		'output' => 'esc_attr'],
	];
	
	/**
     * Sanitize input data based on type or custom function.
     *
     * @param mixed  $data The data to sanitize.
     * @param string $key  The key (or attribute name).
     * @param string|array $type The type or custom function mapping.
     * 
     * @return mixed The sanitized data.
     * 
     * @throws InvalidArgumentException If the type is invalid.
     */

    public static function input( $data, $key, $type ) {
		
        $data = apply_filters( "sanitize_before_input_{$key}", $data );

        $functionName = self::getTypeMapping( $key, $type, 'input' );
        $functionName = apply_filters( "custom_function_mapping_for_input_{$key}", $functionName );
        
        if		( $type === 'custom' )								$data = apply_filters( "sanitize_custom_input_{$key}", $data );
		elseif	( method_exists( __CLASS__, $functionName ) )		$data = call_user_func( [ __CLASS__, $functionName ], $data );
		elseif	( function_exists( $functionName ) ) 				$data = $functionName( $data );
		else														throw new InvalidArgumentException( "Invalid input type for key: $key" );

        return apply_filters( "sanitize_after_input_{$key}", $data );
		
    }
	
	/**
     * Escape data for output based on type or custom function.
     *
     * @param mixed  $data The data to escape.
     * @param string $key  The key (or attribute name).
     * @param string|array $type The type or custom function mapping.
     * 
     * @return mixed The escaped data.
     * 
     * @throws InvalidArgumentException If the type is invalid.
     */

    public static function output( $data, $key, $type ) {
		
        $data = apply_filters( "sanitize_before_output_{$key}", $data );

        $functionName = self::getTypeMapping( $key, $type, 'output' );
        $functionName = apply_filters( "custom_function_mapping_for_output_{$key}", $functionName );
        
        if		( $type === 'custom' )						$data = apply_filters( "sanitize_custom_output_{$key}", $data );
        elseif	( function_exists( $functionName ) )		$data = $functionName( $data );
        else												throw new InvalidArgumentException( "Invalid output type for key: $key" );

        return apply_filters( "sanitize_after_output_{$key}", $data );
		
    }

	/**
     * Merge the default attributes with the provided attributes and sanitize them.
     *
     * @param array $defaults The default attributes.
     * @param array $atts     The provided attributes.
     * @param array $types    The types or custom function mappings.
     * 
     * @return array The merged and sanitized attributes.
     */
	
    public static function mergeAndSanitize( $defaults, $atts, $types ) {
		
        $parsed_atts = shortcode_atts( $defaults, $atts );
        
		foreach ( $parsed_atts as $key => $value ) $parsed_atts[ $key ] = self::input( $value, $key, $types[ $key ] );
		
        return $parsed_atts;
    
	}
	
	/**
     * Merge the default attributes with the provided attributes, then sanitize and escape them.
     *
     * @param array $defaults The default attributes.
     * @param array $atts     The provided attributes.
     * @param array $types    The types or custom function mappings.
     * 
     * @return array The merged, sanitized, and escaped attributes.
     */
	
    public static function mergeSanitizeAndEscape( $defaults, $atts, $types ) {
		
        $sanitized_atts = self::mergeAndSanitize( $defaults, $atts, $types );
		
		foreach ( $sanitized_atts as $key => $value ) $sanitized_atts[ $key ] = self::output( $value, $key, $types[ $key ] );
		
        return $sanitized_atts;
    
	}
	
	/**
     * Decode a JS encoded string.
     *
     * @param string $encoded_js The encoded JS string.
     * 
     * @return string The decoded JS string.
     */
	
	public static function decode_js( $encoded_js ) {
		return htmlspecialchars_decode( $encoded_js, ENT_QUOTES );
	}
	
	/**
	 * Get the type mapping function based on the context.
	 *
	 * @param string       $key     The key (or attribute name).
	 * @param string|array $type    The type or custom function mapping.
	 * @param string       $context The context (either 'input' or 'output').
	 * 
	 * @return string|null The function name, or null if not found.
	 * 
	 * @throws InvalidArgumentException If the type is an array but missing required keys.
	 */
	
	private static function getTypeMapping( $key, $type, $context ) {

		if ( is_array( $type ) ) {
			
			if ( isset( $type['input'] ) && isset( $type['output'] ) ) return $type[$context] ?? null;
			else throw new InvalidArgumentException("The provided array for type mapping is missing 'input' or 'output' keys.");
		
		}

		$mapping = apply_filters( 'modify_type_mapping', self::$typeMapping );

		return $mapping[ $type ][ $context ] ?? null;
	}

	
	/**
	 * Sanitize a string to neutralize potential JS code execution.
	 * 
	 * Uses the htmlspecialchars function to convert special characters to their HTML entities. 
	 * Specifically, this function will handle quotes, double quotes, less than and greater than signs.
	 * This is primarily used to neutralize strings before they are used within a JavaScript context.
	 *
	 * @param string $data The string to sanitize.
	 * 
	 * @return string Sanitized string with special characters converted to their HTML entities.
	 */
	
	private static function sanitize_js( $data ) {
		return htmlspecialchars( $data, ENT_QUOTES, 'UTF-8' );
	}
	
}

?>

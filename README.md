# Sanitize Class for WordPress

**This is a work in progress, while overall the class should function well the input sanitization and output escaping needs to be adjusted to work as "expected" out of the box. The current examples are inline with the current operation which shows that at minimum the output escaping is not ideal for the cases presented.**

The `Sanitize` class provides a robust solution for cleaning up and securing data in WordPress, especially when dealing with shortcode attributes. By utilizing this class, you can ensure that only safe data is presented to your users and stored in your database.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
  - [Direct Input/Output](#direct-inputoutput)
  - [Shortcode Attributes](#shortcode-attributes)
  - [Advanced Usage](#advanced-usage)
- [Examples](#examples)
- [Documentation](#documentation)
- [Contributing](#contributing)

## Installation

1. Download the `Sanitize.php` file.
2. Integrate the `Sanitize.php` file into your WordPress theme or plugin:

```php
include_once( 'path/to/Sanitize.php' );
```

## Usage

### Direct Input/Output

For direct sanitation of input:

```php
$input = "Your input data";
$type = "text"; // This is based on the type of data e.g. "text", "url", etc.
$sanitized_input = Sanitize::input($input, $type);
```

For direct escaping of output:

```php
$output = "Your data for output";
$type = "text"; // This is based on the type of data e.g. "text", "url", etc.
$escaped_output = Sanitize::output($output, $type);
```

### Shortcode Attributes

Here's a demonstration on how to use the Sanitize class for managing shortcode attributes:

```php
function custom_shortcode( $atts ) {
    $defaults = [
        'text' => '',
        'url' => ''
    ];

    $types = [
        'text' => 'text',
        'url' => 'url'
    ];

    $sanitized_atts = Sanitize::mergeSanitizeAndEscape( $defaults, $atts, $types );
    // Now, use $sanitized_atts in your shortcode implementation
}
add_shortcode( 'custom', 'custom_shortcode' );
```

### Advanced Usage

For more specialized sanitization and escaping, you can designate your own functions:

```php
$types = [
    'value' => [
        'input' => 'custom_sanitize_function',
        'output' => 'custom_escape_function'
    ]
];
```

## Examples

The following examples demonstrate the power of the `Sanitize` class in ensuring input safety. By utilizing the provided `custom` shortcode, we'll observe the transformation and protection of various input values.

**Shortcode Function**:
```php
function custom_shortcode( $atts ) {
    $defaults = [
        'text' => '',
        'url' => ''
    ];

    $types = [
        'text' => 'text',
        'url' => 'url'
    ];

    $sanitized_atts = Sanitize::mergeSanitizeAndEscape( $defaults, $atts, $types );
    return '<a href="' . $sanitized_atts['url'] . '">' . $sanitized_atts['text'] . '</a>';
}
add_shortcode( 'custom', 'custom_shortcode' );
```

### Examples for Sanitize Class Type Mappings

In this section, we'll showcase the effect of each type mapping on a given input:

- **text**:
  * Input: `<strong>Bold</strong> text here`
  * Output: `Bold text here`

- **textarea**:
  * Input: `This is <strong>bold</strong> text`
  * Output: `This is &lt;strong&gt;bold&lt;/strong&gt; text`

- **email**:
  * Input: `user<at>example.com`
  * Output: `user@example.com`

- **url**:
  * Input: `http://example.com<script>alert('xss');</script>`
  * Output: `http://example.com`

- **key**:
  * Input: `my_Cool Key123!`
  * Output: `my_coolkey123`

- **filename**:
  * Input: `myImage<script>.jpg`
  * Output: `myImage.jpg`

- **html**:
  * Input: `<strong>bold</strong><script>alert('xss');</script>text`
  * Output: `<strong>bold</strong>text`

- **js**:
  * Input: `alert('test');`
  * Output: `alert(\'test\');`

- **attribute**:
  * Input: `data-"something" other`
  * Output: `data-something other`

These examples help to visualize how each type mapping in the Sanitize class processes potentially unsafe input.

## Documentation

### Filters

The Sanitize class offers a suite of filters that enable developers to tailor its behavior:

- **modify_type_mapping**: Adjust the global type mapping structure.
  
  ```php
  add_filter( 'modify_type_mapping', function( $mappings ) {
      $mappings['new_type'] = ['input' => 'my_input_function', 'output' => 'my_output_function'];
      return $mappings;
  });
  ```

- **sanitize_before_input_{$type}** and **sanitize_after_input_{$type}**: Fine-tune data prior to and following its sanitation.
  
  ```php
  // Before sanitizing text
  add_filter( 'sanitize_before_input_text', function( $data ) {
      return strtoupper($data); // Converts text to uppercase before sanitizing
  });
  
  // After sanitizing text
  add_filter( 'sanitize_after_input_text', function( $data ) {
      return trim($data); // Trims whitespace after sanitizing
  });
  ```

- **sanitize_before_output_{$type}** and **sanitize_after_output_{$type}**: Modify data before its readiness for output and post-output.
  
  ```php
  // Before outputting text
  add_filter( 'sanitize_before_output_text', function( $data ) {
      return '<strong>' . $data . '</strong>'; // Wraps text in strong tags before output
  });
  
  // After outputting text
  add_filter( 'sanitize_after_output_text', function( $data ) {
      return str_replace(' ', '&nbsp;', $data); // Replaces spaces with non-breaking spaces after output
  });
  ```

- **sanitize_custom_input** and **sanitize_custom_output**: Customized sanitation and escape mechanisms for the 'custom' type.
  
  ```php
  // Custom input sanitation
  add_filter( 'sanitize_custom_input', function( $data ) {
      return strip_tags($data); // Strips all HTML tags for custom input
  });
  
  // Custom output preparation
  add_filter( 'sanitize_custom_output', function( $data ) {
      return htmlspecialchars($data); // Converts special characters to HTML entities for custom output
  });
  ```

These filters grant developers extensive control, making the Sanitize class highly adaptable to diverse needs.


## Contributing

Interested in contributing? Awesome! You can either push a pull request or initiate an issue for discussing alterations or fixes.

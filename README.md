# Sanitize Class for WordPress

The Sanitize class helps you clean up and secure data in WordPress. It's particularly good for handling shortcode attributes. With this class, you can make sure that only safe data is shown to your users and saved in your database.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
  - [Basic Usage](#basic-usage)
  - [Advanced Usage](#advanced-usage)
- [Documentation](#documentation)
- [Contributing](#contributing)

## Installation

1. Download the `Sanitize.php` file.
2. Add the `Sanitize.php` file to your WordPress theme or plugin:

```php
include_once( 'path/to/Sanitize.php' );
```

## Usage

### Basic Usage

Here's how you can use the Sanitize class to clean up shortcode attributes:

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
    // Now, use $sanitized_atts in your shortcode logic
}
add_shortcode( 'custom', 'custom_shortcode' );
```

### Advanced Usage

For more tailored sanitization and escaping, you can specify your own functions:

```php
$types = [
    'value' => [
        'input' => 'custom_sanitize_function',
        'output' => 'custom_escape_function'
    ]
];
```

## Documentation

### Filters

The Sanitize class has several filters to let developers tweak how it works:

**modify_type_mapping**: This changes the global type mapping structure.

```php
add_filter( 'modify_type_mapping', function( $mappings ) {
    $mappings['custom'] = ['input' => 'my_input_function', 'output' => 'my_output_function'];
    return $mappings;
});
```

**sanitize_before_input_{$type}** and **sanitize_after_input_{$type}**: Adjust data before and after it's sanitized.

```php
add_filter( 'sanitize_before_input_text', function( $data ) {
    // Tweak $data before it's sanitized as text
    return $data;
});
```

**sanitize_before_output_{$type}** and **sanitize_after_output_{$type}**: Adjust data before and after it's prepared for output.

```php
add_filter( 'sanitize_before_output_text', function( $data ) {
    // Change $data before it's prepped for output as text
    return $data;
});
```

**sanitize_custom_input** and **sanitize_custom_output**: Custom sanitization and escaping for the 'custom' type.

```php
add_filter( 'sanitize_custom_input', function( $data ) {
    // Custom sanitization logic here
    return $data;
});
```

## Contributing

Want to help out? Great! You can either submit a pull request or create an issue to chat about changes or fixes.


# Sanitize Class for WordPress

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

In this section, we'll demonstrate the functionality of the `Sanitize` class using the provided shortcode example. Let's observe the transformation of input values after sanitation.

### Example 1: Text and URL Shortcode

#### Input:

Using the shortcode in a post:

```[custom text="Click <strong>Here</strong>!" url="http://example.com"]```

#### Output:

The rendered HTML after sanitation would be:

```html
<a href="http://example.com">Click &lt;strong&gt;Here&lt;/strong&gt;!</a>
```

Note: The text content has been escaped for secure output, ensuring that it does not render the HTML tags but instead displays them as plain text.

### Example 2: Using a Non-Valid URL

#### Input:
Using the shortcode in a post:

```[custom text="Invalid URL Example" url="javascript:alert('Hacked!');"]```

#### Output:
The rendered HTML after sanitation would be:

```html
<a href="">Invalid URL Example</a>
```

Note: The potentially harmful JavaScript URL has been stripped away, ensuring a safer output.

### Example 3: Including Script Tags in Text

#### Input:
Using the shortcode in a post:

```[custom text="<script>alert('Malicious code');</script>Visit our website" url="http://legitwebsite.com"]```

#### Output:
The rendered HTML after sanitation would be:

```html
<a href="http://legitwebsite.com"><script>alert('Malicious code');</script>Visit our website</a>
```

Note: The script tags in the text content have been escaped, ensuring they're displayed as plain text and not executed.

### Example 4: Mixing Safe and Unsafe Inputs

#### Input:
Using the shortcode in a post:

```[custom text="Click <em>Here</em> to <strong>win</strong>!" url="http://safeplace.com<script>alert('Gotcha!');</script>"]```

#### Output:
The rendered HTML after sanitation would be:

```html
<a href="http://safeplace.com">Click <em>Here</em> to <strong>win</strong>!</a>
```

Note: While the URL contained both safe and unsafe portions, only the safe part was allowed. Similarly, the text content was properly escaped to ensure it displays HTML tags as plain text.

These examples serve to illustrate how the Sanitize class can prevent various forms of potentially malicious input from causing harm or displaying incorrectly.

## Documentation

### Filters

The Sanitize class incorporates various filters allowing developers to fine-tune its functionality:

- **modify_type_mapping**: Modify the global type mapping configuration.
- **sanitize_before_input_{$type}** and **sanitize_after_input_{$type}**: Modify data before and after sanitation.
- **sanitize_before_output_{$type}** and **sanitize_after_output_{$type}**: Adjust data prior to and after its preparation for output.
- **sanitize_custom_input** and **sanitize_custom_output**: Personalized sanitation and escape processes for the 'custom' type.

Refer to the given code in the original README for examples on how to utilize these filters.

## Contributing

Interested in contributing? Awesome! You can either push a pull request or initiate an issue for discussing alterations or fixes.

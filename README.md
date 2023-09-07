# Sanitize Class for WordPress

**This is a work in progress, while overall the class should function well the input sanitization and output escaping needs to be adjusted to work as "expected" out of the box.**

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

### Example 1: Basic Text and URL Shortcode

#### Input:

Shortcode usage:
```[custom text="Click <strong>Here</strong>!" url="http://example.com"]```

#### Output:

Resulting sanitized HTML:
```html
<a href="http://example.com">Click &lt;strong&gt;Here&lt;/strong&gt;!</a>
```

**Observation**: The text content is securely escaped, preventing the HTML tags from rendering and instead displaying them as plaintext.

### Example 2: Attempt with a Malicious URL

#### Input:

Shortcode usage:
```[custom text="Invalid URL Example" url="javascript:alert('Hacked!');"]```

#### Output:

Resulting sanitized HTML:
```html
<a href="">Invalid URL Example</a>
```

**Observation**: The potentially dangerous JavaScript URL gets removed, safeguarding the output.

### Example 3: Script Tags within Text

#### Input:

Shortcode usage:
```[custom text="<script>alert('Malicious code');</script>Visit our website" url="http://legitwebsite.com"]```

#### Output:

Resulting sanitized HTML:
```html
<a href="http://legitwebsite.com">&lt;script&gt;alert('Malicious code');&lt;/script&gt;Visit our website</a>
```

**Observation**: The embedded script tags in the text are escaped, ensuring they're represented as plaintext and won't execute.

### Example 4: Blending of Safe and Unsafe Inputs

#### Input:

Shortcode usage:
```[custom text="Click <em>Here</em> to <strong>win</strong>!" url="http://safeplace.com<script>alert('Gotcha!');</script>"]```

#### Output:

Resulting sanitized HTML:
```html
<a href="http://safeplace.com">Click &lt;em&gt;Here&lt;/em&gt; to &lt;strong&gt;win&lt;/strong&gt;!</a>
```

**Observation**: While the URL has a mix of safe and unsafe components, the `Sanitize` class ensures only the safe part remains. The text, meanwhile, is properly escaped to display HTML tags as plaintext.

Through these examples, it becomes evident how the `Sanitize` class proficiently prevents potential threats and ensures clean and safe outputs.


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

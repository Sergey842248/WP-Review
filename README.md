# WP Reviews

A simple WordPress plugin to manage reviews submitted by users through a form on your website and display them in another location.

## Features

- **User Review Submission**: Allow users to submit reviews with their name, email, and message
- **Admin Management**: Easily manage all reviews from the WordPress admin panel
- **Review Responses**: Administrators can respond to reviews with text and images
- **Visibility Control**: Toggle the visibility of reviews (show/hide)
- **Shortcode Integration**: Display reviews and the review form anywhere on your site using shortcodes
- **Responsive Design**: Works well on all devices

## Installation

1. Download the plugin files with "Code" > "Download ZIP"
2. Open your WordPress admin panel go to "Plugins" menu > "Add new Plugin" > "Upload Plugin" > "Choose File"
3. Choose your downloaded ZIP
4. Activate the plugin through the "Plugins" menu in WordPress


## Usage

### Displaying the Review Form

To display the review submission form on your website, use the following shortcode:

```
[review_form]
```

This will display a form where users can submit their reviews.

### Displaying Reviews

To display the published reviews on your website, use the following shortcode:

```
[show_reviews]
```

This will display all reviews that have been marked as visible in the admin panel.

### Managing Reviews

1. Go to the WordPress admin panel
2. Click on "Reviews" in the admin menu
3. From here you can:
   - View all submitted reviews
   - Edit review details
   - Toggle review visibility
   - Respond to reviews with text and images
   - Delete reviews

## Customization

The plugin uses standard WordPress styling that can be customized using your theme's CSS. The main CSS classes used are:

- `.review-form` - The review submission form
- `.review-output` - The container for displayed reviews
- `.review-box` - Individual review display
- `.review-admin` - Admin response section

## Requirements

- WordPress 5.2 or higher
- PHP 7.2 or higher

## Credits

Original by [Flonik](https://flonik.de)



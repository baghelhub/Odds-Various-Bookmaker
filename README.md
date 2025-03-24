# Odds Various Bookmaker Plugin for WordPress

## Overview

The **Odds Various Bookmaker Plugin** allows WordPress users to fetch and display live odds from approximately 10 different bookmakers. This plugin is designed with a strong focus on object-oriented programming, comprehensive documentation, and adherence to best practices for performance and programming principles.

## Working Features 

- Fetch live odds from multiple bookmakers.
- Admin dashboard interface to select which bookmakers to display.
- Control over the specific markets to show.
- Dynamic links to the bookmakers.
- Gutenberg block for adding Odds Various Bookmaker to posts and pages.
- Easily extendable to accommodate additional bookmakers in the future.



## Installation

1. **Download the Plugin**  
   Clone or download the repository:
   ```bash
   git clone <repository-url>
   ```


2. **Upload to WordPress**  
   - Navigate to `wp-content/plugins/` and upload the plugin folder.
   - Alternatively, upload the ZIP file from the WordPress admin dashboard (`Plugins` → `Add New` → `Upload Plugin`).

3. **Activate the Plugin**  
   - Go to `Plugins` in your WordPress admin panel.
   - Find `Odds Various Bookmaker Plugin` and click `Activate`.

## Usage

### 1. Gutenberg Block
- The plugin provides a Gutenberg block named **Odds Various Bookmaker Block** that allows you to insert odds into posts and pages.
- Add the block via the WordPress block editor (`+` → search for `Odds Various Bookmaker` → insert it into the page/post).

### 2. Admin Settings
- Navigate to `Settings` → `Odds Various Bookmaker` in the WordPress dashboard.
- Select the bookmakers you want to display.
- Choose the markets to showcase.
- Save your settings.

## Extensibility
This plugin is built with extensibility in mind:
- Developers can add additional bookmakers by extending the API handler.
- Hooks and filters allow customization of displayed odds.

## Support
For issues, contributions, or feature requests, please open an issue in the repository or contact the development team.

## License
This plugin is released under the MIT License.






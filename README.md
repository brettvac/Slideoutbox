# Slide Out Box Module
A Joomla! module for displaying a customizable slideout box that appears when users scroll to a specified page depth.

Want to engage your website visitors with a call-to-action or promotional content? Slideoutbox lets you display a sleek popup box triggered by scroll depth, perfect for capturing attention and driving conversions.

This is your first step to turning passive website visitors into engaged users who interact with your offers.

## How To Use Slideoutbox
1. Install the module via the Joomla! Extensions Manager. You can install via URL by using this URL: [https://github.com/brettvac/slideoutbox/releases/download/1.0/mod_slideoutbox.zip](https://github.com/brettvac/slideoutbox/releases/download/1.0/mod_slideoutbox.zip)
2. Navigate to Extensions > Modules and find "Slideoutbox".
3. Configure the module settings, including scroll depth (e.g., 50%), heading tag (h1-h6), main text, button URL, and cookie expiration (days).
4. Publish the module in a custom position (e.g., `slideoutbox` or `footer`). The slideout will appear when users scroll to the set depth.

## Features
- **Supports Prepared Content**: You can enable content preparation and show Joomla plugins inside the box.
- **Customizable Content**: Configure heading (h1-h6), main text, and optional button with URL.
- **Cookie Persistence**: Remembers closed state with a module-specific cookie to prevent reappearance.
- **Responsive Design**: Adapts to mobile devices with smaller screens.

## Requirements
This module works with Joomla! versions greater than 4.0 and requires PHP 8.1 or later.
No external accounts or APIs are needed, just a Joomla installation.

## FAQ
**Q: Can I display multiple slideout boxes on one page?**  
**A:** Currently, the module supports one slideout per page. Multi-instance support may be added in a future update.

**Q: Why is the module ID used in the cookie name?**  
**A:** The module ID ensures unique cookies (e.g., `mod_slideoutbox_closed_<moduleId>`) to avoid conflicts, even for a single instance.

## Credits
- **itoctopus** and **convertbox** for the original idea

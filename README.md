# Custom Tabs Plugin

A customized tabs plugin for WordPress that allows you to add dynamic content tabs and a global logos section.

## Instructions

1. **Install the plugin**: Upload the plugin folder to your `/wp-content/plugins/` directory and activate it through the 'Plugins' menu in WordPress.
2. **Configure tabs**: A new **Custom Tabs** menu tab will show up in your WordPress admin sidebar. Click it to add your tabs, content, images, and global logos.
3. **Display on site**: Edit the tabs and add the shortcode `[custom_tabs]` to any page or post where you want the tabs to appear.

## Process
Starting with the backend, I created a custom option page,
Chose not to use ACF since i dont have pro,
and connecting it to a field group would be complicated on the CMS side.
trying to build it as simple for the user as possible.

Front end is basic, just a simple tabs component, with a global logos section at the bottom.

the next step would be design implimenting.
And responsive design.

last would have been animating a logo slider on desktop, since it seems that was the intent.



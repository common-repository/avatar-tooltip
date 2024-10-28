=== Avatar Tooltip ===
Contributors: axenso
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FSEGV7H8YNVLQ
Tags: tooltip, avatar, gravatar
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 1.0.2
License: GPLv2 or later

To show a tooltip with info (member info and Gravatar profile) when you mouseover/click on an avatar.


== Description ==

You can enable a tooltip that shows info about user/author when you rollover/click on related avatar.

**Let's speak clearly: if you are loooking for a plugin that enables the standard Gravatar hovercards, this is not the plugin right for you. Please look for another one.**

When you rollover/click on an avatar (e.g. of a comment author), a tooltip popups and shows:

* some additional links if the author is a **registered user**: e.g. to user post archive, to user external website, to user blog (if multisite)
* some info from **Gravatar** if author is registered to that service: e.g. location, "about me" description, websites, other account pages (facebook, twitter....)

In plugin settings you can set up the javascript and css main options.

The plugin comes with some filter hooks, so you can customise the tooltip content at all.

The plugin uses [qTip plugin for jQuery](http://craigsworks.com/projects/qtip2/).


== Installation ==

= INSTALLATION =
1. Upload plugin directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the `Plugins` menu in WordPress

= SETUP =
1. Go to `Settings > Avatar Tooltip` to set up options
1. Edit `axe-avatar-tooltip.css` to customise tooltip styles


== Frequently Asked Questions ==

= Why does a tooltip show "No more info available"? =

Because the avatar owner is registered neither to the blog nor to Gravatar.

= I think some existing gravatars are missing or not shown... =

We suggest that first of all you read the [Gravatar FAQ](gravatar.com/site/faq/).

= I can see that the plugin files (css, javascript) are properly loaded in my theme, but tooltips are not shown yet... What's wrong? =

Probably the default jQuery selector (`#main ...`) is not good for your theme.
Look inside your theme html code and search for a class/id of your main content div: e.g. `#content` or something similar.
Then, go in `Settings > Avatar Tooltip` and set up the new selector.
This parent class is useful to avoid that the toolip is shown on undesired avatars (e.g. top Toolbar).

= Can I customise the tooltip aspect? =

In `Settings > Avatar Tooltip` you can set up main options (main css theme, position).
Then, you can edit `axe-avatar-tooltip.css` in plugin folder.
A tip: copy this file to your theme directory and edit it there. Useful to prevent the loss of styles when you upgrade the plugin.

= I don't care about Gravatar profile data. Can the tooltip show local user info? =

You can use the sample available at [this link](http://www.eventualo.net/blog/2013/01/avatar-tooltip-plugin-how-to-show-only-user-bio-and-social-links/): the tooltips will show the user bio (description), a link to user recent posts, the links to social network profiles (as filled in profile by user). Everything taken locallly from blog, nothing from Gravatar profile.

= Can I customise the tooltip content text? =

You have some available filter hooks to manage the tooltip text.

The following sample adds ugly debug info at the bottom of tooltip:
`
function my_avatar_tooltip_content_after( $text, $user, $md5email, $grav_name, $grav_profile ) {
	if ( is_object($user) ) {
		$text .= '<br />USERDATA:<br /><pre style="color:#000">'. print_r( $user, true ) .'</pre>';
	}
	if ( is_array($grav_profile) ) {
		$text .= '<br />GRAVATAR PROFILE:<br /><pre style="color:#000">'. print_r( $grav_profile, true . '</pre>');
	}	
	return $text;
}
add_filter('axe_avatar_tooltip_content_after', 'my_avatar_tooltip_content_after', 10, 5);
`

The following sample replaces the account list (facebook, twitter...) with a link to Gravatar full profile:
`
function my_avatar_tooltip_content_gravatar_accounts( $text, $md5email, $grav_profile ) {
	if ( isset( $grav_profile['profileUrl'] ) ) {
		return '<a href="'. esc_url($grav_profile['profileUrl']) .'" title="'. esc_attr( __('view complete profile on Gravatar', AXE_AT_PLUGIN_DIR) ) .'" target="_blank" rel="nofollow">' . __('view complete profile on Gravatar', AXE_AT_PLUGIN_DIR) .'</a>';
	} else {
		return $text;
	}
}
add_filter('axe_avatar_tooltip_content_gravatar_accounts', 'my_avatar_tooltip_content_gravatar_accounts', 10, 3);
`

You can look for more hooks inside plugin code.

If you are brave, the function that returns the tooltip text is pluggable and you can replace it at all:

1. create a php file in `mu-plugins` folder (if the folder does not exists, simply create it)
1. add a new function called `axe_at_tooltip_content` (look for the orginal function inside plugin code to see how it works)
1. from that moment, the plugin will load your custom function

== Screenshots ==

1. Tooltip of a user registered to blog and registered to Gravatar
2. Tooltip of a user not registered to blog but registered to Gravatar
3. Tooltip of a user registered to blog but not registered to Gravatar


== Changelog ==

= 1.0.2 =
* Updated: for a better integration with other plugins the get_avatar filter priority was increased to be sure that the [rel] attribute is added after other filters

= 1.0.1 =
* Fixed: bug about not showing tooltip content for not-logged user

= 1.0 =
* Initial release


== Upgrade Notice ==

= 1.0 =
Initial release

= 1.0.1 =
Fixed a bug about not showing tooltip content for not-logged user

= 1.0.2 =
Better integration with other plugins: the [rel] attribute is added after other filters

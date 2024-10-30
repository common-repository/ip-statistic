=== alx ip statistic ===
Contributors: alex27648
Plugin URI: https://inthome.ml/
Donate link: https://inthome.ml/?page_id=366
Tags: log, security, ip, shortcode, shortcut, inthome, ip statistic, statistics, ip, pass, passwords
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.7
Tested up to: 5.9.3
Stable tag: 2.3
Requires PHP: 5.6


Receiving ip addresses and browser identifiers of visitors on the login page and on any page of the site.

== Description ==

Receiving ip addresses , browser identifiers, error passwords (disable by default)  of visitors on the login page and if use "shortcode" at any page of the site.  Also work with guest users

Allows you to save data about visitors to the database for further viewing, and obtaining statistics.
In the settings you can specify where you want to receive this data. It is also possible to insert a shortcode on the page or record:

&#91;ip_statistics&#93;

When viewed on your login page or on other page (where you insert shortcode), the Data (ip adress and user agent) will be save in database. By default, only login error is logging

The plugin provides its own admin options page in the WordPress admin. Here you can define options where you whant to use this plugin and see statistics of clients. The plugin's admin page also provides some tips.



== Installation ==

1. Install via the built-in WordPress plugin installer. Or download and unzip ` alex-ip-statistic.zip` inside the plugins directory for your site (typically `wp-content/plugins/`)
2. Activate the plugin through the 'Plugins' admin menu in WordPress
3. Go to the `IP statistic` -> `Settings` admin options page to customize the settings.


== Frequently Asked Questions ==

= What happens when i deactivate and unistall plugin? =

When you deactivate it, will be remove all options, but database not be remove.
When you unistall, will be database of statistics.

== Screenshots ==

1. Admin interface of plugin
2. Settings interface of plugin

== Changelog ==

= 2.3 (2022-04-25) =
Highlights:

* Bug fixes

= 2.2 (2022-04-22) =
Highlights:

* Fix some conflicts with WP Security plugin.

= 2.1 (2022-04-15) =
Highlights:

* Fix some conflicts with WP Security plugin.
* Add new donations

=  2.0 (2021-03-09) =
Highlights:

* replace saving time by server on saving time by WP settings
* if you site not using CloudFlare proxy, ip of clients will show from REMOTE_ADDR not CF

= 1.9 (2020-05-02) =
Highlights:

* prepare for WP 5.4

= 1.8 (2019-03-23) =
Highlights:

* fix some bugs

= 1.7 (2019-03-15) =
Highlights:

* add buttons to clear data in base

= 1.6 (2019-03-14) =
Highlights:

* remove saving empty login and pass 

= 1.5 (2019-03-05) =
Highlights:

* New css

= 1.4 (2019-03-05) =
Highlights:

* Add function to save passwords at ERROR logins ONLY! with include or exclude existing users option.
* Settings. add new options
* Add new TABS with sort records


= 1.3 (2019-02-25) =
Highlights:

* Add new TABS with sort records


= 1.2 (2019-20-22) =
Highlights:

* Change to debug OFF
* Prepare to WP 5.1


= 1.1 (2019-01-04) =
Highlights:

* In statistics table column "Type" shows title of page/posts (in v1.0 shows id of page/post) .

= 1.0 (2018-12-14) =
Highlights:

* Develop end plugin.

== Upgrade Notice ==
Bug fixes
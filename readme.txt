=== I am Human ===
Contributors: rclick   
Donate link: http://programminglinuxblog.blogspot.com.au/2014/06/i-am-human.html
Tags: captcha, spam, human verification, fun
Requires at least: 3.9.1
Tested up to: 4.4
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A customisable human detection plugin, that isn't annoying. Seriously.


== Description ==

**Firstly** 

Sorry for not responding to forum questions; I wasn't setup to recieve emails when posts were created so completely missed them :( Thats fixed now, so hopefully I can get onto issues quicker from now on. 

**Secondly** 

To those who have donated money, WOW, you are good human beings. Thank you.

**What is I am Human?**

Sick of annoying CAPTCHA plugins, that give your users a blurry image that even you can't read? Why not try I Am Human? Its a fully customisable grid based human verification technique, that you can make as easy, or as hard as you want. Not only that, you can theme it so it looks like your site, and make it fun to use! This isn't so much of a plugin, as it is a revolution! 

**How does it work?**

When a user tries to submit a post comment, they will be presented a 'question' grid, like whats shown in the first picture within the screen-shots section. This is accompanied with a customisable description, which will explain what the user should do. The user then clicks on cells within the grid. When they do so, they change colour! Following the example within the screen-shots section, the user should click on the four cells within smilies left eye, turning them yellow. Because the 'answer' grid matches this, the test will pass and the comment will be posted! 

**How do I specify the grids? Is it hard?**

No! Within the Wordpress admin section, you can define your question and answer grids simply by drawing them yourself! The colours and text are fully customisable too.

See the screen-shots section for more details.

**All this for free?!**

Seems crazy I know, but yes, its totally free.

== Installation ==

Either install via the plugins section within Wordpress itself (Search for 'I am Human'), or click on the download button and manually install via the zip file using the following steps.

1. Upload the plugin zip file to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Navigate to Settings -> I am human options, and set it up!

== Frequently Asked Questions ==

= When I click post comment, no human check is displayed. Why? =

There are three conditions which will allow the post to be submitted without
prompting the user to confirm they are human.

1. The user is logged in. Its assumed that you trust the people who are signed up not to spam.
1. The __enable for post comments__ option is not checked in the settings.
1. There is no __colour_one__ selected. You must have selected at least one __color one__ cell on the answer image.

== Screenshots ==

1. An example of the grid you can setup in the admin section
2. After the user has clicked a couple of cells, then clicks a customisable error message is displayed.
3. The admin section, where you can define grid colours, dialog title and error message.
4. Also in the admin section, where you build your question and answer grids.

== Changelog ==

= 1.0 =

Initial release of I Am Human!

= 1.1 =

Bug fixes.

= 1.2 =

Bug fixes.

== Upgrade Notice ==

None.

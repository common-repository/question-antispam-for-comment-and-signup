=== Plugin Name ===
Contributors: qdinar
#Donate link: http://example.com/
Tags: multisite, antispam, captcha, spam, question, answer, signup, registration, comments
Requires at least: 4.4.1
Tested up to: 5.1.1
Stable tag: 0.1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

antispam question for signup and comment forms of wordpress, set by admin, supports Multisite mode


== Description ==

antispam question for signup and comment forms of wordpress, set by admin, supports Multisite mode


== Installation ==


1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

#== Frequently Asked Questions ==

#= A question that someone might have =

#= What about foo bar? =


#== Screenshots ==


== Changelog ==


2011 04 07
bug fixed, v 0.0.2

2011 09 03
add this antispam question to comment form

2011 09 04
move changelog into this file
move this file out from folder, delete folder
rename from "Signup Question Captcha" to "Wordpress Multisite Question Antispam"
rename this file from "signup-question-captcha.php" to "wp-ms-question-antispam.php"
replace all 'sqc' to 'wpmsqas' in this file.
change description from "Questions as CAPTCHA" to "Question and answer as antispam in signup and comment forms of Wordpress, set by admin, supports Multisite mode."
create plugin page http://qdb.wp.kukmara-rayon.ru/wp-ms-question-antispam/
change "Plugin URI" from http://qdb.wp.kukmara-rayon.ru/ to the plugin page.
change version from 0.0.2 to 0.0.3
direct "die" in "preprocess_comment" instead of setting "wp_delete_comment" and "die" in "comment_post", as in Peter's Custom Anti-Spam
add "I have used WordPress Hashcash code, also I have looked at buhsl-Captcha, Cookies for Comments, Peter's Custom Anti-Spam codes, to learn and use their codes" between version line and licence explanation
discover that antispam question and answer are same in all blogs, that is bad because blogs are in different languages
write a message in 3 languages
fix texts in admin page
have corrected it, same answers in all blogs, looking at Cookies for Comments code
change version to 0.0.4
seems when using "direct die" spam comments are saved, but not published, i change it back to old method
changing back to old method seems has not helped
have discovered that answer form is here even for logged in user
fix that with help of buhsl-captcha code

2011-09-14 15:56 utc+4:
code
`// admins can do what they like
if( is_admin() ){`
was not correct, is_admin do not mean user is admin, but that page is admin page. now i use is_user_logged_in() instead of it. i had copied the code, that now have appeared as incorrect, from wp-hashcash.

2011-10-19 8:54 utc+4 :
i want to prepare to set in wordpress plugins site. should move into folder. and make readme file.
i have renamed: from wp-ms-question-antispam to wp-simple-qa-antispam because signup is not only in multisite. qa is question-answer. i want to name this wp-signup-comment-simple-question-answer-antispam. i have changed my mind, i want to publish this in my site as single .php file before i make it prepared for wordpress plugins site. ah and "ms" is needed, because some plugins do not support ms mode, they are buggy in ms.
renamed to wp-ms-signup-comment-simple-question-answer-antispam.php
to do list: should make buddypress compatible. should make option for ms admin to change questions and answers in all blogs.

2011-11-7: once i have seen that old method to delete comments also leave some of them for moderation, for that, i am going to set it back to new "direct die" method. ... i have set it. now i going to set comparing answer with modifying to lowercase. ... i have set it now.

2013-11-03: i had not installed this in wp plugins site, i tried "wordpress-multisite-question-antispam" but "wordpress" was not alowed. now i try again , without that word. i rename: from Wordpress Multisite Question Antispam to Question Antispam. also wp-ms-question-antispam to question-antispam in plugin url in my blog ... that is private page yet
description:
antispam question for signup and comment forms of wordpress

2014-07-07:
going to make fixes for wordpress org plugins site
rename to Question Antispam for Comment and Signup, file and directory to question-antispam-for-comment-and-signup
version 0 0 5

2016-01-24
version 0.0.6
i have activated this for single wordpress signup page;
i have made all texts translatable, and changed some texts;
i have fixed position in comment form.

version 0.1.0
i have added translation possibility to some strings , which were fogotten;
i have added option to turn counting of spam requests on or off;
i have changed text of "delete" or "move to spam" option, made it more informative;
i have set "stop" ("delete") as default;
i have fixed 2 bugs of spam ratio widget.

version 0.1.1
using comment_form_after_fields

version 0.1.2, 0.1.3
just to write "Tested up to: 4.8"

2019-05-03
version 0.1.4
added forgotten mb_strtlower and trim

2019-05-18
version 0.1.5
start to take in account "Comment must be manually approved" and "Comment author must have a previously approved comment" settings.






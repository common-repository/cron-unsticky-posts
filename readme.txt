=== Cron Unsticky Posts ===
Contributors: musicmasteria
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=10960040
Tags: cron, sticky, unsticky, posts, front page
Requires at least: 2.8
Tested up to: 2.8
Stable tag: trunk

Unsticky old posts automatically using wordpress's cron functions.

== Description ==

<h2>Overview Of The Plugin</h2>

The Cron Unsticky Posts Plugin is very simple to use.
It is on whenever this plugin is active so you should disable it if
you no longer want old posts to be unstickied.

This plugin is the most for people who sticky a lot of posts but dont
want to have to go back a few days later and unsticky them one by one
just so that they dont fill up their home page.

If that sounds like you then this is the plugin for you!

<h3>Features</h3>

    * Unsticky posts automatically based on when they were posted
    * Set a global Unsticky Time (default: 3 days). After a posts is older than that Unsticky Time it will be unstickied automatically by this plugin
    * Set Unsticky Time on a per-post basis to allow certain posts more/less time on the front page
    * Uses WPs Cron, no need to setup complex cron jobs. Great if you dont even have access to Cron from your host!
    * Turn it on and forget about it. It will keep on running till you disable the plugin.
    * Its free!

<h3>Usage (in a nutshell)</h3>

Set All Posts to Unsticky After 5 days
 - Set "Unsticky Time" to 5 days in Settings->Unsticky Post

Set An Individual Post To Unsticky After 7 days
 - Add custom field "unstick_in" w/ value "7" to that post

See an example of usage in the "Usage" section

Please check out my website for more info about this plugin and others:
<a href="http://takeouttactics.com/">Take-Out-Tactics</a>

== Installation ==

Installation and running of this plugin is easy!
Just follow these steps and you can practicly forget about it after that.

1. Upload to your plugins folder, usually wp-content/plugins/ and unzip the file, it will create a wp-content/plugins/cron-unsticky-posts/ directory.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to "Settings" administration menu, select "Unsticky Posts". The plugin is already setup to run the cron jobs needed.
4. (optional) Change the "Unsticky Time" from 3 days to whatever you choose and then Click "Update Settings".

You're all set! The plugin will unsticky any 'old' post it finds every night at midnight.

== Usage ==

<h3>Basic Usage</h3>

Set All Posts to Unsticky After 5 days
 - Set "Unsticky Time" to 5 days in Settings->Unsticky Post

Set An Individual Post To Unsticky After 7 days
 - Add custom field "unstick_in" w/ value "7" to that post

<h3>Example of Usage</h3>

<h4>The Scenario</h4>
Today you want to post three different things:<br/>
* "Staying Alive"<br/>
* "Short"<br/>
* "Kill Me Quickly"<br/>
For both "Short" and "Kill Me Quickly", you don't want them to be around for
too long, only 2 days before they are unstickied. However, "Staying Alive"
is a really great post. You want it to be around for 5 days before it is unstickied.

<h4>What to do:</h4>
First, Set the "Unsticky Time" setting (above) to 2 days<br/>
Second, give post "Staying Alive" a custom field called "unstick_in" with
a value of "5".

<h4>So what happens? Here's the breakdown:</h4>
On day 1: You post "Staying Alive", "Short", and "Kill Me Quickly"<br/>
On day 2: Nothing is unstickied<br/>
On day 3: Both "Short" and "Kill Me Quickly" are unstickied<br/>
On day 4-6: Nothing is unstickied<br/>
On day 7: "Staying Alive" is finally unstickied<br/>

<h4>The "Monkey Wrench":</h4>
"What if I posted something on day 3 of the above scenario?"<br/>
- Because you set your "Unsticky Time" to 2 days it will be unstickied
on day 5 (2 days after it was posted)

== Frequently Asked Questions ==

<h2>Frequently Asked Questions</h2>

<h3>"But I want certain posts to be unstickied before/after the time I set above.
Is there a way for me to set the option above on an individual post basis?"</h3>

Yes, there is! With the use of custom fields you can make an individual post
ignore the default option set above and use its own.

Give the post a custom field called "unstick_in" (without the quotes)
and give that field the value of the number of days to wait before it should
be unstickied.

<h3>If you have other questions...</h3>
Please check the other documentation or ask in the comments here:
[Take-Out-Tactics:Cron Unsticky Posts](http://takeouttactics.com/projects/wordpress/plugins/cron-unsticky-posts/ "Cron Unsticky Posts Official Plugin Page | Take-Out-Tactics")

== Changelog ==

= 1.0 =
* Initial Release
* WP's Cron is used instead of server cron jobs
* Unsticky Time is an option on the settings page vs. it being hard coded in the plugin
* Custom fields can be used to give individual posts more/less time on the home page

== Upgrade Notice ==

= 1.0 =
It's the first release... You don't have much of a choice :P 
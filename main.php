<?php
/*
Plugin Name: Cron Unsticky Posts
Plugin URI: http://takeouttactics.com/projects/wordpress/plugins/cron-unsticky-posts/
Description: Unsticky old posts automatically using wordpress's cron functions.
Version: 1.0
Author: Musicmasteria
Author URI: http://takeouttactics.com/
*/

/* This plugin was made by Musicmasteria of Take-Out-Tactics
 * Updates and downloads can be found here:
 * http://takeouttactics.com/projects/wordpress/plugins/cron-unsticky-posts/
 * and/or here:
 * http://wordpress.org/extend/plugins/cron-unsticky-posts/
 *
 * This plugin is a FREE plugin and
 * it may not be altered and resold
 * by anyone.
 * 
 * For more wordpress plugins, php scripts, and other things by
 * Musicmasteria please check out Take-Out-Tactics at:
 * http://takeouttactics.com/
 * 
 * Thanks for using my plugins and I hope you enjoy my work.
 * 
 * --- Please support my work! ---
 * 
 * You can do so by telling your other blogging friends
 * about this plugin or any of my other works, by writing
 * me a nice thank you email - musicmasteria@yahoo.com,
 * or by buying me more energy drinks and cool gadgets.
 * All is welcome and greatly appriciated! Thank you!
 * 
 * Donation Link:
 * https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=10960040
 * 
 * And now, down to business...
*/

//Setup plugin

function setupUnstickyPosts(){
	//Get time for first event
	$current_unix = strtotime("tomarrow");
	//schedule event
	wp_schedule_event($current_unix, 'daily', 'unstickOldPosts');
	
	$oldoptions = get_option('unsticky_posts');
	if(empty($oldoptions)){
		$theoptions = array();
		$theoptions['days_ago'] = '3';
		$theoptions['last_run'] = false;
		$theoptions['save_options'] = true;
		update_option('unsticky_posts', $theoptions);
	}
}

register_activation_hook( __FILE__, 'setupUnstickyPosts' );

function unsetupUnstickyPosts(){
	//Get time for next event
	$next_scheduled_time = wp_next_scheduled( 'unstickOldPosts' );
	//unschedule event
	wp_unschedule_event($next_scheduled_time, 'unstickOldPosts' );
	
	$oldoptions = get_option('unsticky_posts');
	if(!$oldoptions['save_options']){
		delete_option('unsticky_posts');
	}
}

register_deactivation_hook( __FILE__, 'unsetupUnstickyPosts' );

add_action('admin_menu', 'cronUnstickyMenuSetup');

function cronUnstickyMenuSetup() {
  add_options_page('Cron Unsticky Posts Settings', 'Unsticky Posts', 'manage_options', 'unsticky-posts-options', 'unsticky_options_page');
}

// Function from http://www.laughing-buddha.net/jon/php/sec2hms/
function sec2hms ($sec, $padHours = false) 
  {

    // holds formatted string
    $hms = "";
    
    // there are 3600 seconds in an hour, so if we
    // divide total seconds by 3600 and throw away
    // the remainder, we've got the number of hours
    $hours = intval(intval($sec) / 3600); 

    // add to $hms, with a leading 0 if asked for
    $hms .= ($padHours) 
          ? str_pad($hours, 2, "0", STR_PAD_LEFT). ' Hours, '
          : $hours. ' Hours,';
     
    // dividing the total seconds by 60 will give us
    // the number of minutes, but we're interested in 
    // minutes past the hour: to get that, we need to 
    // divide by 60 again and keep the remainder
    $minutes = intval(($sec / 60) % 60); 

    // then add to $hms (with a leading 0 if needed)
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ' Minutes, and ';

    // seconds are simple - just divide the total
    // seconds by 60 and keep the remainder
    $seconds = intval($sec % 60); 

    // add to $hms, again with a leading 0 if needed
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

    // done!
    return $hms.' Seconds';
    
  }

//Now that we're done setting everything up, here's the meat of the plugin

// unstickOldPosts() - Gets Current Sticky Posts, Compairs their post date to unsticky date,
// and unsticks if applicable
function unstickOldPosts(){
	
	$options = get_option('unsticky_posts');
	
	if($options['days_ago']){
		$days_ago = $options['days_ago'];
	}else{
		$days_ago = 3; //Default of 3 days
	}
	
	//Get UNIX Time For The Beginning Of Today
	$current_unix = strtotime("today");
	
	//Query Sticky Posts If Any
	$sticky_posts = query_posts(array('post__in'=>get_option('sticky_posts')));
	
	$ID_to_be_unsticky = array(); //New Array
	$PostName_to_be_unsticky = array(); //New Array
	$ID_to_stay_stickied = array(); //New Array
	
	for($intI = 0; $intI < count($sticky_posts); $intI++){ 

	    $post_date = $sticky_posts[$intI]->post_date;
	    $post_date_unix = strtotime($post_date);
	    
	    //Check for custom field
	    $meta_value = get_post_meta($sticky_posts[$intI]->ID, 'unstick_in', true);
	    if( isset($meta_value{0}) ){
	    	//Validate Value - Is numeric
	    	if(is_numeric($meta_value)){
	    		$check_unix = $current_unix-($meta_value*86400);
	    	}else{
	    		//If value isn't a number, use default
	    		$check_unix = $current_unix-($days_ago*86400);
	    	}
	    }else{
	    	//Default Check Time
			$check_unix = $current_unix-($days_ago*86400);
	    }
		if($post_date_unix <= $check_unix){
		   $ID_to_be_unsticky[] = $sticky_posts[$intI]->ID;
		   $PostName_to_be_unsticky[] = $sticky_posts[$intI]->post_title;
		}else{
		$ID_to_stay_stickied[] = $sticky_posts[$intI]->ID;
		}
	}
	
	//Unsticky Posts Found To Be Too Old
	$count1 = count($ID_to_be_unsticky);
	$lastrun = array();
	
	if($count1!=0){
		$lastrun['date'] = $current_unix;
		for($int2 = 0; $int2 < $count1; $int2++){ 
			$lastrun[] = $PostName_to_be_unsticky[$int2].' [ID: '.$ID_to_be_unsticky[$int2]."]<br/>\n"; 
		}
		update_option('sticky_posts', $ID_to_stay_stickied);
	}else{
		$lastrun['date'] = $current_unix;
		$lastrun[] = 'None';
	}
	
	$options['last_run'] = $lastrun;
	update_option('unsticky_posts',$options);
}

function unsticky_options_page(){
	
$hidden_field_name = 'mt_submit_hidden2';

if( $_POST[ $hidden_field_name ] == 'Y' ) {
	//some function would go here to submit options
	$optionsArray = get_option('unsticky_posts');

	if($_POST['days_ago']){
		$optionsArray['days_ago'] = $_POST['days_ago'];
	}
	
	if($_POST['save_options']){
		if($_POST['save_options'] == 'on'){
			$optionsArray['save_options'] = true;
		}else{
			$optionsArray['save_options'] = false;
		}
	}else{
		$optionsArray['save_options'] = false;
	}

	update_option('unsticky_posts', $optionsArray);
?>

<div class="updated"><p><strong>Settings Updated.</strong></p></div>

<?php
}

if($_POST['reset_settings'] == 'true'){
	$theoptions = get_option('unsticky_posts');
	$theoptions['days_ago'] = '3';
	$theoptions['save_options'] = true;
	update_option('unsticky_posts', $theoptions);
?>

<div class="updated"><p><strong>Settings Reset To Defaults.</strong></p></div>

<?php
}
	
	$currentoptions = get_option('unsticky_posts');
	
	if($currentoptions['save_options']){
		$save_options_selected = ' checked';
	}
	
	$currentTime = date("F j, Y, g:i a");
		$next_scheduled_time = wp_next_scheduled( 'unstickOldPosts' );
	$nextRun = date("F j, Y, g:i a", $next_scheduled_time); 
	
		$timebetween = $next_scheduled_time-time();
	if($timebetween > 0){
		$timeUntilRun = sec2hms($timebetween);	
	}else{
		$timeUntilRun = '<i>Waiting For WP-Cron To Run. Check Back Soon!</i>';	
	}
	echo '
	<h2 style="text-align:center">Cron Unsticky Posts</h2>
	<p>
		<h3>Overview Of The Plugin</h3>
		The Cron Unsticky Posts Plugin is very simple to use.<br/>
		It is on whenever this plugin is active so you should disable it if<br/>
		you no longer want old posts to be unstickied.<br/>
		<br/>
		This plugin is the most for people who sticky a lot of posts but don\'t<br/>
		want to have to go back a few days later and unsticky them one by one<br/>
		just so that they don\'t fill up their home page.<br/>
		<br/>
		If that sounds like you then this is the plugin for you!<br/>
	</p>
	<h3>Plugin Status</h3>
	<p>
		This plugin is active and old posts are being unstickied accordingly!<br/>
		Current Time: '.$currentTime.'<br/>
		The Next Check Is Scheduled To Run At: '.$nextRun.'<br/>
		Time Until Next Run: '.$timeUntilRun.'<br/>
	</p>
	<h3>Settings</h3>
	<p>
	<form action="" method="post">
		<input type="hidden" name="'.$hidden_field_name.'" value="Y">
		<b>How Old Should A Post Be When It Is Unstickied? ("Unsticky Time")</b><br/>
			<select name="days_ago" id="days_ago">
';
	$s1 = 0;
	for($s = 1; $s<=100; $s++){
		if($s1 == 0)
			echo '				';
		echo '<option ';
		if($currentoptions['days_ago'] == $s)
			echo ' SELECTED ';
		echo 'value="'.$s.'">'.$s.'</option>';
		if($s1 == 10){
			echo "\n"; $s1 = -1;
		}
		$s1++;
	}
	echo '
			  </select> Days Old (Default: 3)<br/>
		<b>Should Settings Remain When The Plugin Is Deactivated?</b><br/>
		<input type="checkbox" name="save_options" id="save_options" value="on"'.$save_options_selected.' /> If checked, settings will not be deleted on plugin deactivation<br/>
		<br/>
		<input type="submit" value="Update Settings">
	</form>
	<form action="" method="post">
		<input type="hidden" name="reset_settings" id="reset_settings" value="true">
		or <b>Reset Plugin Settings</b><br/>
		<input type="submit" value="Reset Settings" onclick="return confirm(\'Are You Sure You Want To Reset The Settings?\');">
	</form>
	<br/>
	</p>
	<h3>Q & A</h3>
	<p>
		<b>Q:</b> "But I want certain posts to be unstickied before/after the time I set above.<br/>
		Is there a way for me to set the option above on an individual post basis?"<br/>
		<br/>
		<b>A:</b> Yes, there is! With the use of custom fields you can make an individual post<br/>
		ignore the default option set above and use its own.<br/>
		<br/>
		Give the post a custom field called "<b>unstick_in</b>" (without the quotes)<br/>
		and give that field the value of the number of days to wait before it should<br/>
		be unstickied.<br/>
	</p>
	<h3>Example of Usage</h3>
	<p>	
		<b>The Scenario</b><br/>
		    Today you want to post three different things:<br/>
		      - "Staying Alive"<br/>
		      - "Short"<br/>
		      - "Kill Me Quickly"<br/>
		    For both "Short" and "Kill Me Quickly", you don\'t want them to be around for<br/>
		    too long, only 2 days before they are unstickied. However, "Staying Alive"<br/>
		    is a really great post. You want it to be around for 5 days before it is unstickied.<br/>
		<br/>
		<b>What to do:</b><br/>
		    First, Set the "Unsticky Time" setting (above) to 2 days<br/>
		    Second, give post "Staying Alive" a custom field called "<b>unstick_in</b>" with<br/>
		    a value of "<b>5</b>".<br/>
			<br/>
		<b>So what happens? Here\'s the breakdown:</b><br/>
		    On day 1: You post "Staying Alive", "Short", and "Kill Me Quickly"<br/>
		    On day 2: Nothing is unstickied<br/>
		    On day 3: Both "Short" and "Kill Me Quickly" are unstickied<br/>
		    On day 4-6: Nothing is unstickied<br/>
		    On day 7: "Staying Alive" is finally unstickied<br/>
			<br/>
		<b>The "Monkey Wrench":</b><br/>
		    What if I posted something on day 3 of the above scenario?"<br/>
		        - Because you set your "Unsticky Time" to 2 days it will be unstickied<br/>
		          on day 5 (2 days after it was posted)<br/>
			  <br/>
	<br/>
	<b>"BUT I HAVE MORE QUESTIONS!!!"</b><br/>
		-The plugin isn\'t really that complicated but help will be available on my site<br/>
		<a href="http://takeouttactics.com">Take-Out-Tactics</a> if you need something or you happen to find something wrong with the plugin.<br/>
	</p>
	<p>	
	<b>And know the moment you\'ve all been waiting for,</b><br/>
	If you like this plugin, if it brings you comfort knowning that there is one less<br/>
	thing you have to do yourself to keep your site running smoothly, or if you\'d<br/>
	just like to make one young programmer happy, <b>please support my work<b>.<br/>
	<br/>
	You can do so by telling your other blogging friends about this plugin or any of<br/>
	my other works, writing me a nice thank you email - musicmasteria@yahoo.com, or<br/>
	buying me more energy drinks and cool gadgets.<br/>
	All is welcome and greatly appriciated! Thank you!<br/>
			<br/>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="10960040">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
	</p>
';
}
?>
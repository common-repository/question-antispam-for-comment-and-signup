<?php
/*
 Plugin Name: Question Antispam for Comment and Signup
 Plugin URI: http://qdb.wp.kukmara-rayon.ru/wp-ms-question-antispam/
 Description: Question and answer as antispam in signup and comment forms of Wordpress, set by admin, supports Multisite mode.
 Author: Dinar Qurbanov
 Author URI: http://qdb.wp.kukmara-rayon.ru/
 Version: 0.1.5

 I have used WordPress Hashcash code, also I have looked at buhsl-Captcha, Cookies for Comments, Peter's Custom Anti-Spam codes, to learn and use their codes, and also copied something from them

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

*/

function wpmsqas_option($save = false){
	if($save) {
		/*if( function_exists( 'update_site_option' ) ) {
			update_site_option('plugin_signup_question_captcha', $save);
		} else {
			update_option('plugin_signup_question_captcha', $save);
		}*/
		update_option('question_antispam_plugin', $save);

		return $save;
	} else {
		/*if( function_exists( 'get_site_option' ) ) {
			$options = get_site_option('plugin_signup_question_captcha');
		} else {
			$options = get_option('plugin_signup_question_captcha');
		}*/
		$options = get_option('question_antispam_plugin');

		if(!is_array($options))
			$options = array();

		return $options;
	}
}

/**
 * Install Question Antispam for Comment and Signup
 */

function wpmsqas_install () {
	// set our default options
	$options = wpmsqas_option();
	$options['comments-spam'] = $options['comments-spam'] || 0;
	$options['comments-ham'] = $options['comments-ham'] || 0;
	$options['signups-spam'] = $options['signups-spam'] || 0;
	$options['signups-ham'] = $options['signups-ham'] || 0;
	
	/*
	// akismet compat check
	if(function_exists('akismet_init')){
		$options['moderation'] = 'spam';
	} else {
		$options['moderation'] = 'delete';
	}
	*/
	$options['moderation'] = 'stop';
	
	// logging
	// $options['logging'] = true;
	// counting
	$options['count'] = $options['count'] || 'off';

	//question and answer
	$options['question'] = '10+10=?';
	$options['answer'] = '20';

	$options[ 'installed' ]=true;

	// update the key
	wpmsqas_option($options);
}

add_action('activate_signup_question_captcha', 'wpmsqas_install');

/**
 * Our plugin can also have a widget
 */

function wpmsqas_get_spam_ratio( $ham, $spam ) {
	if($spam + $ham == 0)
		$ratio = 0;
	else
		$ratio = round(100 * ($spam/($ham+$spam)),2);

	return $ratio;
}

function wpmsqas_widget_ratio($options){
	$signups_ham = (int)$options['signups-ham'];
	$signups_spam = (int)$options['signups-spam'];
	$ham = (int)$options['comments-ham'];
	$spam = (int)$options['comments-spam'];
	$ratio = wpmsqas_get_spam_ratio( $ham, $spam );
	$signups_ratio = wpmsqas_get_spam_ratio( $signups_ham, $signups_spam );

	$msg = '<li><span>'.sprintf(__('%1$d spam comments are blocked out, %2$d comments are allowed.  %3$d&#x25; of your comments are spam!').'</span></li>',$spam,$ham,$ratio);

	if( $signups_ham && $signups_spam )
		$msg .= '<li><span>'.sprintf(__('%1$d spam signups are blocked out, %2$d signups are allowed.  %3$d&#x25; of your signups are spam!').'</span></li>',$signups_spam,$signups_ham,$signups_ratio);

	return $msg;
}


/**
 * Admin Options
 */

add_action('admin_menu', 'wpmsqas_add_options_to_admin');

function wpmsqas_add_options_to_admin() {
/*	if( function_exists( 'is_site_admin' ) && !is_site_admin() )
		return;

	if (function_exists('add_options_page')) {
		if( function_exists( 'is_site_admin' ) ) {
			add_submenu_page('wpmu-admin.php', __('Signup Question Captcha'), __('Signup Question Captcha'), 'manage_options', 'wpmsqas_admin', 'wpmsqas_admin_options');
		} else {
			add_options_page('Signup Question Captchah', 'Signup Question Captcha', 8, basename(__FILE__), 'wpmsqas_admin_options');
		}
	}*/
	add_options_page(_x('Question Antispam','page title'), _x('Question Antispam','menu title'), 'manage_options', 'wpmsqas_config', 'wpmsqas_admin_options');
}

function wpmsqas_admin_options() {
	/*if( function_exists( 'is_site_admin' ) && !is_site_admin() )
		return;*/
	/*if ( isset($_POST['submit']) ) {
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			die(__('Cheatin&#8217; uh?'));

		check_admin_referer( 'cfc' );
		if( $_POST[ 'cfc_spam' ] == 'spam' || $_POST[ 'cfc_spam' ] == 'delete' ) {
			update_option( 'cfc_spam', $_POST[ 'cfc_spam' ] );
		}
		update_option( 'cfc_speed', (int)$_POST[ 'cfc_speed' ] );
		if ( $_POST[ 'cfc_delivery' ] == 'css' || $_POST[ 'cfc_delivery' ] == 'img' )
			update_option( 'cfc_delivery', $_POST[ 'cfc_delivery' ] );
	}*/

	$options = wpmsqas_option();

	if( !isset( $options[ 'installed' ] ) ) {
		wpmsqas_install(); // MU has no activation hook
		$options = wpmsqas_option();
	}

	// POST HANDLER
	if($_POST['wpmsqas-submit']){
		check_admin_referer( 'wpmsqas-options' );
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			die(__('Current user is not authorized to manage options'));

		$options['moderation'] = strip_tags(stripslashes($_POST['wpmsqas-moderation']));
		// $options['logging'] = strip_tags(stripslashes($_POST['wpmsqas-logging']));
		$options['question'] = strip_tags(stripslashes($_POST['wpmsqas-question']));
		$options['answer'] = trim(strip_tags(stripslashes($_POST['wpmsqas-answer'])));
		$options['count'] = strip_tags(stripslashes($_POST['wpmsqas-count']));
		wpmsqas_option($options);
	}
	
	// MAIN FORM
	echo '<style type="text/css">
		.wrap h3 { color: black; background-color: #e5f3ff; padding: 4px 8px; }

		.sidebar {
			border-right: 2px solid #e5f3ff;
			width: 200px;
			float: left;
			padding: 0px 20px 0px 10px;
			margin: 0px 20px 0px 0px;
		}

		.sidebar input {
			background-color: #FFF;
			border: none;
		}

		.main {
			float: left;
			width: 600px;
		}

		.clear { clear: both; }

		.input {width:100%;}
	</style>';

	echo '<div class="wrap">';

	echo '<div class="sidebar">';
	echo '<h3>'._x('About','question antispam admin page').'</h3>';
	echo '<ul>
	<li><a href="http://qdb.wp.kukmara-rayon.ru/wp-ms-question-antispam/">'._x('Plugin\'s Homepage','question antispam admin page').'</a></li>';
	/*if( function_exists( 'is_site_admin' ) && is_site_admin() ) {
		echo '<li><a href="http://mu.wordpress.org/forums/">WordPress MU Forums</a></li>';
	}*/
	//echo '<li><a href="http://wordpress.org/tags/wp-hashcash">Plugin Support Forum</a></li>';
	echo '</ul>';		
	echo '<h3>'._x('Statistics','question antispam admin page').'</h3>';
	echo '<p>'.wpmsqas_widget_ratio($options).'</p>';
	echo '</div>';

	echo '<div class="main">';
	echo '<h2>'.__('Settings').'</h2>';

	//echo '<h3>Standard Options</h3>';
	echo '<form method="POST" action="?page=' . $_GET[ 'page' ] . '&updated=true">';
	wp_nonce_field('wpmsqas-options');
	if( function_exists( 'is_site_admin' ) ) { // MU only
		//echo "<p>'Here was MU only block'</p>";
	}
	// moderation options
	$moderate = htmlspecialchars($options['moderation'], ENT_QUOTES);
	echo '<p><label for="wpmsqas-moderation">' . _x('What to do on wrong answer:', 'question antispam admin page') . '</label> ';
	echo '<select id="wpmsqas-moderation" name="wpmsqas-moderation">';
	//echo '<option value="moderate"'.($moderate=='moderate'?' selected':'').'>Moderate</option>';
	echo '<option value="spam"'.($moderate=='spam'?' selected':'').'>'.__('Save to spam without any message').'</option>';
	echo '<option value="stop"'.($moderate=='stop'?' selected':'').'>'.__('Stop and propose to go back').'</option>';
	echo '</select>';
	echo '</p>';
	// count or not
	echo '<p><label for="wpmsqas-count">' . __('Count spam and good requests') . '</label> ';
	echo '<input name="wpmsqas-count" type="checkbox" '.($options['count']=='on'?'checked':'').' value="on">';
	echo '</p>';
	//question and answer
	echo '<p><label for="wpmsqas-question">' . _x('Question:', 'question antispam admin page') . '</label>';
	echo '<input id="wpmsqas-question" name="wpmsqas-question" value="'.$options['question'].'" class="input" />';
	echo '<p><label for="wpmsqas-answer">' . _x('Answer:', 'question antispam admin page') . '</label>';
	echo '<input id="wpmsqas-answer" name="wpmsqas-answer" value="'.$options['answer'].'" class="input" />';

	/*
	// logging options
	echo '<h3>Logging:</h3>';

	$logging = htmlspecialchars($options['logging'], ENT_QUOTES);
	echo '<p><label for="wpmsqas-logging">Logging</label>
		<input name="wpmsqas-logging" type="checkbox" id="wpmsqas-logging"'.($logging?' checked':'').'/> 
		<br /><span style="color: grey; font-size: 90%;">Logs the reason why a given comment failed the spam
		check into the comment body.  Works only if moderation / akismet mode is enabled.</span></p>';
	*/
	echo '<input type="hidden" id="wpmsqas-submit" name="wpmsqas-submit" value="1" />';
	echo '<input type="submit" id="wpmsqas-submit-override" name="wpmsqas-submit-override" value="'.__('Save Question Antispam Settings').'"/>';
	echo '</form>';
	echo '</div>';

	echo '<div class="clear">';
	echo '<p style="text-align: center; font-size: .85em;">'.__('Author: Dinar Qurbanov, using free plugins\' codes').'</p>';
	echo '</div>';

	echo '</div>';
}

/**
 * Hook into the signups form
 */

function wpmuSignupForm( $errors ) {

	echo('<label for="wpmsqas_answer">'.__('Question against spammers:').'</label>');
	$error = $errors->get_error_message('captcha_wrong');
	if( isset($error) && $error != '') {
		echo '<p class="error">' . $error . '</p>';
	}
	$options = wpmsqas_option();
	echo('<label for="wpmsqas_answer">'.$options['question'].'</label><input type="text" name="wpmsqas_answer" />');	
}
add_action('signup_extra_fields', 'wpmuSignupForm');

/**
 * Validate our tag
 */

function wpmsqas_check_signup_question( $result ) {
	// get our options
	$options = wpmsqas_option();
	$spam = false;
	if( !strpos( $_SERVER[ 'PHP_SELF' ], 'wp-signup.php' ) || $_POST['stage'] == 'validate-blog-signup' || $_POST['stage'] == 'gimmeanotherblog' )
		return $result;

	// $spam = ($_POST['wpmsqas_answer']!=$options['answer']);
	$spam =
		(
			mb_strtolower(trim($_POST['wpmsqas_answer']))!=
			mb_strtolower($options['answer'])
		);	
	
	if($spam){
		if($options['count']=='on'){
			$options['signups-spam'] = ((int) $options['signups-spam']) + 1;
			wpmsqas_option($options);
		}
		$result['errors']->add( 'captcha_wrong', __('Incorrect answer to the antispam question.') );
	//echo '<p class="error">OK</p>';
	} else {
		if($options['count']=='on'){
			$options['signups-ham'] = ((int) $options['signups-ham']) + 1;
			wpmsqas_option($options);
		}
	}
	
	return $result;
}

add_filter( 'wpmu_validate_blog_signup', 'wpmsqas_check_signup_question' );
add_filter( 'wpmu_validate_user_signup', 'wpmsqas_check_signup_question' );



/**
 * Hook into the comment form
 */


function wpmsqas_add_commentform(){
	global $user_ID;
	if (isset($user_ID) && intval($user_ID) > 0 ) {
		// skip the CAPTCHA 
		return true;
	}
	$options = wpmsqas_option();
	echo('<p><label for="wpmsqas_answer">'.__('Question against spammers:').'</label>');
	echo('<label for="wpmsqas_answer">'.$options['question'].'</label><input type="text" name="wpmsqas_answer" /></p>');
}

add_action('comment_form_after_fields', 'wpmsqas_add_commentform');
#add_action('comment_form_before_fields', 'wpmsqas_add_commentform');
#add_action('comment_form', 'wpmsqas_add_commentform');

function wpmsqas_check_comment_antispam_answer( $comment ) {
	// admins can do what they like
	/*if( is_admin() ){
		return $comment;
	}else{
		echo 'OK';
		exit;
	}*/
	if(is_user_logged_in()){
		return $comment;
	}
	// get our options
	// get our options
	$type = $comment['comment_type'];
	$options = wpmsqas_option();
	$spam = false;
	if($type == "trackback" || $type == "pingback"){
	} else {
		// Check the wphc values against the last five keys
		$spam =
			(
				mb_strtolower(trim($_POST['wpmsqas_answer']))!=
				mb_strtolower($options['answer'])
			);
		//if($options['logging'] && $spam)
		//	$comment['comment_content'] .= "???";
	}

	if($spam){	
		if($options['count']=='on'){
			$options['comments-spam'] = ((int) $options['comments-spam']) + 1;
			wpmsqas_option($options);
		}
			
		switch($options['moderation']){
			case 'stop':
				//add_filter('comment_post', create_function('$id', 'wp_delete_comment($id); die(\'Антиспам сорауга җавап дөрес түгел. Кире кайтыгыз.<br>Ответ на антиспамный вопрос неправилен. Вернитесь на предыдущую страницу<br>Your answer to antispam question is not correct. Go back to the previous page\');'));
				header("Content-Type: text/html; charset=utf-8");
				die(__('Your answer to antispam question is not correct. Go back to the previous page'));
				break;
			case 'spam':
				add_filter('pre_comment_approved', create_function('', 'return \'spam\';'));
				break;
			/*case 'moderate':
			default:
				add_filter('pre_comment_approved', create_function('$a,$b', ' return 0;'));
				break;*/
		}
	} else {
		// add_filter('pre_comment_approved', create_function('', 'return 1;'));
		add_filter(
			'pre_comment_approved',
			function($approved,$commentdata){
				if($approved===0){
					if( 1==get_option( 'comment_moderation' ) || 1==get_option( 'comment_whitelist' ) ){
						return 0;
					}
					return 1;
				}
				return $approved;
			},
			199,
			2
		);
		if($options['count']=='on'){
			$options['comments-ham'] = ((int) $options['comments-ham']) + 1;
			wpmsqas_option($options);
		}
	}
	
	return $comment;


}

add_action('preprocess_comment', 'wpmsqas_check_comment_antispam_answer');


/**
 * Hook into the registration form
 */

// https://codex.wordpress.org/Customizing_the_Registration_Form

//1. Add a new form element...
add_action( 'register_form', 'wpmsqas_register_form' );
function wpmsqas_register_form() {
	$options = wpmsqas_option();
	$wpmsqas_answer = ( ! empty( $_POST['wpmsqas_answer'] ) ) ? trim( $_POST['wpmsqas_answer'] ) : '';
	
	?>
	<p>
		<label for="wpmsqas_answer"><?php _e('Question against spammers:'); ?></label>
		<label for="wpmsqas_answer"><?php echo( $options['question'] ); ?><br />
			<input type="text" name="wpmsqas_answer" id="wpmsqas_answer" class="input" value="<?php echo esc_attr( wp_unslash( $wpmsqas_answer ) ); ?>" size="25" /></label>
	</p>
	<?php
}

//2. Add validation. 
add_filter( 'registration_errors', 'wpmsqas_registration_errors', 10, 3 );
function wpmsqas_registration_errors( $errors ) {
	$options = wpmsqas_option();
	$spam =
		(
			mb_strtolower(trim($_POST['wpmsqas_answer']))!=
			mb_strtolower($options['answer'])
		);	
	if($spam){
		if($options['count']=='on'){
			$options['signups-spam'] = ((int) $options['signups-spam']) + 1;
			wpmsqas_option($options);
		}
		$errors->add( 'wpmsqas_answer_error', __( 'Incorrect answer to the antispam question.' ) );
	} else {
		if($options['count']=='on'){
			$options['signups-ham'] = ((int) $options['signups-ham']) + 1;
			wpmsqas_option($options);
		}
	}

	return $errors;
}





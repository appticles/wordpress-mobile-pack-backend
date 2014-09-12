<?php

define("WMP_VERSION", '2.1');
define('WMP_PLUGIN_NAME', 'WP Mobile Pack '.WMP_VERSION);
define('WMP_DOMAIN', 'wordpress-mobile-pack');

define('WMP_PLUGIN_PATH', WP_PLUGIN_DIR . '/'.WMP_DOMAIN.'/');

require_once( dirname( __FILE__ ) . '/config-uploads.php' );
if ( !defined( 'WMP_FILES_UPLOADS_DIR' ) && !defined( 'WMP_FILES_UPLOADS_URL' ) ) {
	wmp_set_uploads_dir();
}

define('WMP_FEEDBACK_EMAIL','feedback@appticles.com');
define('WMP_NEWS_UPDATES','http://cdn-wpmp.appticles.com/dashboard/news.json');
define('WMP_WHATSNEW_UPDATES','http://cdn-wpmp.appticles.com/dashboard/whats_new/content1.json');
define('WMP_MORE_UPDATES','http://cdn-wpmp.appticles.com/dashboard/more/more1.json');

define('WMP_WAITLIST_PATH','http://gateway-dev.appticles.com/waitlist/api/subscribe');

// define connect with appticles path
define('WMP_APPTICLES_CONNECT','http://dev.webcrumbz.co/content/wpconnect');
define('WMP_APPTICLES_DISCONNECT','http://dev.webcrumbz.co/content/wpdisconnect');

// define blog version
define('WMP_BLOG_VERSION',get_bloginfo('version'));

// define the string used for generating comments tokens (can be overwritten for increasing security)
define('WMP_CODE_KEY','asdc%/dfr_A!8792*');

?>
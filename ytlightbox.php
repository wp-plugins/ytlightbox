<?php
/**
 * Plugin Name: Youtube Lightbox
 * Description: A WordPress plugin to trigger a lightbox after a Youtube video ends, handled via the Youtube API.
 * Plugin URI: http://www.nic0las.com/projects/ytlightbox/
 * Author: Nicolas Erramuspe
 * Author URI: http://www.nic0las.com
 * Version: 0.2.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Load youtube API script
 */
add_action('wp_enqueue_scripts','ytlight_init');
function ytlight_init() {
    wp_enqueue_script('jquery');
    
    /* wp_enqueue_script(
    'custom-script',
    plugin_dir_url( __FILE__ ) . 'libs/fancybox/source/jquery.fancybox.js',
    array( 'jquery' )
    ); */

    wp_enqueue_style( 'style-name', plugin_dir_url( __FILE__ )  . 'css/ytlightbox.css' );
    
    wp_enqueue_script('theirscript', 'http://www.youtube.com/player_api');

    wp_enqueue_script(
        'custom-script-2',
        plugin_dir_url( __FILE__ ) . 'libs/ytlightbox.js',
        array( 'theirscript' )
    ); 
    
}


/**
 * Add shortcode functionality
 */
add_shortcode( 'ytlight', 'ytlight_shortcode' );

function ytlight_shortcode($attr){
    @extract( shortcode_atts( array(
        'video' => $attr['video'],
        'height' => $attr['height'],
        'width' => $attr['width']
    ), $attr ) );


    $DEFAULT_HEIGHT = "390px";
    $DEFAULT_WIDTH = "100\%";
    $DEFAULT_CONTENT = "<p style='text-align:center;'>Did you like this video?</p>";

    if (empty($attr['height'])) $attr['height'] = $DEFAULT_HEIGHT;
    if (empty($attr['width'])) $attr['width'] = $DEFAULT_WIDTH;

    if(stristr($attr['width'], 'px') == FALSE && stristr($attr['width'], '%') == FALSE) {
        $attr['width'] .= "px";
    }
    if(stristr($attr['height'], 'px') == FALSE && stristr($attr['height'], '%') == FALSE) {
        $attr['height'] .= "px";
    }
    
    $attr['id'] = "video_".date("YmdHis");
    $ytlight_content = get_option('ytlight_lightbox_content');
    
    if (empty($ytlight_content)) $ytlight_content = $DEFAULT_CONTENT;
    

    $share_buttons = "
        <center>
            <div id='ytlightbox-share-buttons'>
                <a href='http://www.facebook.com/sharer.php?u=".get_permalink(get_the_ID())."' target='_blank'>
                    <img src='".plugin_dir_url( __FILE__ )."/admin/facebook-btn.png' >
                </a>
                <a href='http://twitter.com/share?text=".get_permalink(get_the_title())."' target='_blank'>
                    <img src='".plugin_dir_url( __FILE__ )."/admin/twitter-btn.png' >
                </a>
            </div>
        </center>        
    ";        

    // MODAL
    $str_html = "<div id=".$attr['id']."></div>\n";
    // modal background
    $str_html .= "<div idX='".$attr['id']."_content' id='ytlightbox-modal-background'></div>";
    // modal content
    $str_html .= "<div id='ytlightbox-modal-content'>";
    // close button
    if (1==1) $str_html .= "<a href='javascript:;' id='ytlightbox-close' title='Close'></a>";
    // actual content
    $str_html .= "<div id='ytlightbox-innercontent'>".$ytlight_content."</div>";
    // share buttons
    if (1==1) $str_html .= "<br />".$share_buttons;
    $str_html .= "</div>";



    
    
    $str_html .= "<script type='text/javascript'>\n";
    $str_html .= "jQuery(document).ready(function(){ ";

    $str_html .= "jQuery('#ytlightbox-close').click(function(){ jQuery('#ytlightbox-modal-content, #ytlightbox-modal-background').toggleClass('active');  });";

    $str_html .= "setTimeout(function(){";
    // $str_html .= "function onPlayerStateChange(event) { if(event.data === 0) { jQuery.fancybox.open({ href : '#".$attr['id']."_content', type : 'inline', padding : 10 }); } }\n";
    $str_html .= "ytlightbox.insert({\n";
        $str_html .= "container: '".$attr['id']."',\n";
        $str_html .= "videoId: '".$attr['video']."',\n";

        $str_html .= "playerOptions:{\n";
            $str_html .= "height: '".$attr['height']."',\n";
            $str_html .= "width: '".$attr['width']."',\n";
            $str_html .= "onStateChange:function(event){ if(event.data === 0) {  jQuery('#ytlightbox-modal-content, #ytlightbox-modal-background').toggleClass('active');  } }\n";
        $str_html .= "},\n";
    
        $str_html .= "chapters: {}\n";
    $str_html .= "});\n";

    $str_html .= "}, 500);";


    
    $str_html .= "});\n";
    $str_html .= "</script>\n";
?>
     

    
<?php
    return $str_html;
}



/**
 * Main admin function
 */
function ytlight_admin_function(){
    include('admin/ytlight-admin.php');
}

/**
 * Settings
 */
/**
 * Plugin settings registration
 */
function ytlight_register_settings(){
    register_setting( 'cbw-settings-group', 'ytlight_lightbox_content' );
}
/**
 * Add to menu & register settings
 */
function ytlight_register_custom_menu(){
    // add item to the menu
    add_menu_page( 'YTLightbox', 'YTLightbox', 'administrator', 'ytlightbox', 'ytlight_admin_function', plugin_dir_url( __FILE__ ).'admin/youtube-logo.png');
    // include jquery
    wp_enqueue_script('jquery');
    // register settings
    add_action( 'admin_init', 'ytlight_register_settings' );
}

/**
 * Add a custom link for the plugin
 */
function ylight_custom_plugin_row_meta( $links, $file ) {
    if ( strpos( $file, 'ytlightbox.php' ) !== false ) {
	$new_links = array(
	    '<a href="http://on.nic0las.com/donate" target="_blank">Donate</a>'
	);	
	$links = array_merge( $links, $new_links );
    }
    return $links;
}


if (is_admin()) add_action( 'admin_menu', 'ytlight_register_custom_menu' );
// filter to add a donate button in the plugin list
add_filter( 'plugin_row_meta', 'ylight_custom_plugin_row_meta', 10, 2 );

?>

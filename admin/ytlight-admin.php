<?php
/**
 * Main admin panel script for YTLightbox
 */

// get the content from options or use default
$ytlight_content = get_option('ytlight_lightbox_content');

$editor_id = 'ytlight_lightbox_content';
$a_editor_settings = Array(
    'wpautop' => false,
    'extended_valid_elements' => 'style'
);

if (empty($ytlight_content)) $ytlight_content = "<p style='text-align:center;'>Did you like this video?</p>";
?>


<style type="text/css">
 #subscription-image{
     width:140px;
 }
 .button-primary{height:40px !important;}
 table#controls-footer td, table#controls-footer tr{ padding:0;margin:0; }
 .form-table td p{ margin-top:0 !important;  }
 p.submit{
     padding-top:0 !important;
 }

 #links {
     text-decoration: none;
     
 }
 #links:hover {
     text-decoration: underline;
     
 }
</style>
<div class="wrap">
    <h2>Youtube Lightbox</h2>

    <form method="post" action="options.php">
        <?php settings_fields( 'cbw-settings-group' ); ?>
        <?php do_settings_sections( 'cbw-settings-group' ); ?>
        <table border="0" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <table class="form-table" border="0" style="min-width:500px;">
                        <tr>
                            <td style="padding-top:0;margin-top:0;">
                                Content for the lightbox to show after videos
                                <hr />
                               
                                <?php
                                if (function_exists("wp_editor")){
                                    wp_editor( $ytlight_content, $editor_id, $a_editor_settings );
                                }else{
                                    echo '<textarea name="ytlight_lightbox_content">'.$ytlight_content.'</textarea>';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php submit_button(); ?>
                            </td>
                        </tr>

                    </table>
                </td>
                
            </tr>
        </table>
    </form>
    
</div>

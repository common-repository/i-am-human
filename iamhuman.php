<?php
/*
  Plugin Name: I am human
  Plugin URI:  http://programminglinuxblog.blogspot.com/2014/06/i-am-human.html
  Description: Human vs machine. Who will pass the test? Customisable machine filtering (otherwise known as captcha), that can be imbeded into forms.
  Author: rclick
  Version: 1.2
  Author URI: programminglinuxblog.blogspot.com
  License: http://www.gnu.org/licenses/gpl-2.0.html
 */

include_once 'iamhuman_constants.php';
include_once 'iamhuman_options.php';

function iamhuman_load_javascript_files() {

    wp_register_script('iamhuman-main', plugins_url('js/iamhuman.js', __FILE__)  //
            , array('jquery'), '1.0', true);

    wp_enqueue_script('iamhuman-main');
    
    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_style('wp-jquery-ui-dialog');

    wp_register_style('iamhuman-grid-style', plugins_url('css/grid_styles.css', __FILE__));
    wp_enqueue_style('iamhuman-grid-style');
}

add_action('wp_enqueue_scripts', 'iamhuman_load_javascript_files');
add_action('admin_init', 'iamhuman_load_javascript_files');

function iamhuman_html() {
    
    // disabled for logged in users, or if no colour one has
    // been specified

    $html = "
    <div id='iamhuman_container' style='display: none' title='" . get_option(IamHumanConstants::IAMHUMAN_OPTION_DESCRIPTION_TITLE) . "'>
        
        <div id=\"iamhuman_dialog_contents\">
        
            <div id=\"iamhuman_description\">
                " . get_option('description_text') . "
            </div>
       
            <div id=\"iamhuman_grid\" class='iamhuman_grid'></div>
            <div class='iamhuman_message'>" . iamhuman_get_incorrect_attempt_message() ."</div>
        </div>
    </div>
    
    <script>
        jQuery(document).ready(function($) {
        
            $('html > head').append($('<style> .colour_bg { background: " . iamhuman_get_background_colour() . "; }</style>'));
            $('html > head').append($('<style> .colour_one { background: " . iamhuman_get_colour_one() . "; }</style>'));
            $('html > head').append($('<style> .colour_two { background: " . iamhuman_get_colour_two() . "; }</style>'));
            $('html > head').append($('<style> .cell_mouse_over { background: " . get_option(IamHumanConstants::IAMHUMAN_OPTION_COLOUR_HOVER) . "; }</style>'));
                
            foreground = '" . get_option(IamHumanConstants::IAMHUMAN_OPTION_QUESTION_COLOUR_ONE_STRING) . "';" . 
            "guide = '" . get_option(IamHumanConstants::IAMHUMAN_OPTION_QUESTION_COLOUR_TWO_STRING) . "';" . 
            
            "create_grid(\"#iamhuman_grid\", " . get_option(IamHumanConstants::IAMHUMAN_OPTION_GRID_SIZE) .");
                
            highlight_list(\"#iamhuman_grid\", \"colour_one\", foreground);
            highlight_list(\"#iamhuman_grid\", \"colour_two\", guide);
            
            enable_handlers('#iamhuman_grid');
            
            //var form = jQuery(this).parents('form:first');
            var form = $(\"#commentform\");
            var answer_passed = false;

            form.append($('<div style=\"display:none\"><input id=\"alt_submit\" type=\"submit\" /></div>'));

            var handle_submit = function() {
                
                var button = jQuery(this);

                answer_field = form.children('input[name=\"iamhuman_answer\"]:first');
                                    
                if(answer_field.length == 0) {
                    form.append('<input name=\"iamhuman_answer\" type=\"hidden\" />');
                    answer_field = form.children('input[name=\"iamhuman_answer\"]:first');
                }
 
                jQuery(\"#iamhuman_container\").dialog({
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    my: 'center', 
                    at: 'center',
                    buttons: {
                            \"Check!\": {text: 'Check!', id: 'dialog_check', click: function(selected) {
                            	    
				    jQuery('#dialog_check').button('option', 'disabled', true );

                                    selection_string = get_selection_string(\"#iamhuman_grid\", \"colour_one\");
                                    
                                    var data = {
                                        'action': 'iamhuman_check_answer',
                                        'answer_string': selection_string
                                    };
                                    
                                    dialog = this;

                                    jQuery.post(ajaxurl, data, function(response) {

                                        result = $(response).find('success').first().text();

                                        if(result == 'true') {
                                            jQuery('.iamhuman_message').hide();
                                            answer_field.val(selection_string);
                                            answer_passed = true;
                                            jQuery('#alt_submit').click();
                                       } else {
                                            // display error message
                                            answer_passed = false;
                                            jQuery('.iamhuman_message').show();
                                        }
					jQuery('#dialog_check').button('option', 'disabled', false );
                                    });

                            }},
                            Cancel: function() {
				    // reset the grid
                                    clear_grid(\"#iamhuman_grid\");
                                    highlight_list(\"#iamhuman_grid\", \"colour_one\", foreground);
                                    highlight_list(\"#iamhuman_grid\", \"colour_two\", guide);
				    jQuery('.iamhuman_message').hide();

                                    jQuery(this).dialog(\"close\");
                            }
                    }
                })
                
                return false;
            };
            
            form.submit(function (event) {
            
               //answer_field = form.children('input[name=\"iamhuman_answer\"]:first').val();
               
               if(!answer_passed) {
                    event.preventDefault();
                    handle_submit();
                    return false;
               } else {
                    return true;
               }
           });
        });

    </script>";

    return $html;
}
/**
 * Remember this is only displayed for users that are not logged in.
 * NOTE: The answer field exists within the form because the validation occurs 
 * twice; once when the user clicks "check" via AJAX, and the second when the 
 * form is actually processed on the server.
 * 
 * @param array $fields
 * @return string
 */
function iamhuman_add_hidden_comment_field($fields) {
        
    if(iamhuman_is_enabled()) {
        $fields['iamhuman_answer'] = '<input id="iamhuman_answer" name="iamhuman_answer" type="hidden" value="" />';
        $html = iamhuman_html();
        echo $html;
    }
    

    return $fields;
}
add_filter('comment_form_default_fields', 'iamhuman_add_hidden_comment_field');

/**
 * Only for users that are not logged in.
 */

function iamhuman_answer_check_ajax_callback() {
        $results = "<result>";
        $answer_string = get_option(IamHumanConstants::IAMHUMAN_OPTION_ANSWER_COLOUR_ONE_STRING);
	$test_string = $_POST['answer_string'];
        $results .= "<success>" . (($answer_string === $test_string) ? "true" : "false")  . "</success>";
        $results .= "</result>";
        echo $results;

	die(); // this is required to return a proper result
}

add_action('wp_ajax_nopriv_iamhuman_check_answer', 'iamhuman_answer_check_ajax_callback');

/**
 * Ensure the AJAX url is set in the page.
 */function iamhuman_ajaxurl() {
?>
<script type="text/javascript">
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
<?php
}

add_action('wp_head','iamhuman_ajaxurl');


function iamhuman_validate_comment($comment_data) {

    if(iamhuman_is_enabled()) {
        
        $answer_string = get_option('answer_colour_one_string');
        
        if(!empty($answer_string)) {  // no answer setup yet
        
            if(empty($_POST['iamhuman_answer'])) {
               wp_die(iamhuman_get_incorrect_attempt_message());  
            } else {

                $test_answer = $_POST['iamhuman_answer'];

                if($test_answer !== $answer_string) {
                    wp_die(iamhuman_get_incorrect_attempt_message()); 
                }
            }
        }

	return $comment_data;

    } else {
	// plugin not enabled
	return $comment_data;
    }
}

add_filter('preprocess_comment', 'iamhuman_validate_comment');

/**
* Only enable for annonymous users, and if the required settings have been set.
*/
function iamhuman_is_enabled() {

    return !is_user_logged_in() && get_option('colour_one') !== false &&  get_option(IamHumanConstants::IAMHUMAN_OPTION_GRID_SIZE) !== false && 
            filter_var(get_option('enable_post_comments'), FILTER_VALIDATE_BOOLEAN);
}


/**
 * Add settings link on plugin page
 */
function iamhuman_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=i-am-human-options">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__);

add_filter("plugin_action_links_$plugin", 'iamhuman_settings_link' );

?>

<?php

function iamhuman_register_settings() {

    // colours to display on the page
    register_setting(IamHumanConstants::$IAMHUMAN_SETTINGS_ID, IamHumanConstants::IAMHUMAN_OPTION_COLOUR_ONE);
    register_setting(IamHumanConstants::$IAMHUMAN_SETTINGS_ID, IamHumanConstants::IAMHUMAN_OPTION_COLOUR_TWO);
    register_setting(IamHumanConstants::$IAMHUMAN_SETTINGS_ID, IamHumanConstants::IAMHUMAN_OPTION_COLOUR_HOVER);

    register_setting(IamHumanConstants::$IAMHUMAN_SETTINGS_ID, IamHumanConstants::IAMHUMAN_OPTION_GRID_SIZE);

    register_setting(IamHumanConstants::$IAMHUMAN_SETTINGS_ID, IamHumanConstants::IAMHUMAN_OPTION_BACKGROUND_COLOUR);

    register_setting(IamHumanConstants::$IAMHUMAN_SETTINGS_ID, IamHumanConstants::IAMHUMAN_OPTION_DESCRIPTION_TITLE);
    register_setting(IamHumanConstants::$IAMHUMAN_SETTINGS_ID, IamHumanConstants::IAMHUMAN_OPTION_DESCRIPTION_TEXT);
    register_setting(IamHumanConstants::$IAMHUMAN_SETTINGS_ID, IamHumanConstants::IAMHUMAN_OPTION_INCORRECT_ATTEMPT_MESSAGE);

    // question colour strings
    register_setting(IamHumanConstants::$IAMHUMAN_SETTINGS_ID, IamHumanConstants::IAMHUMAN_OPTION_QUESTION_COLOUR_ONE_STRING);
    register_setting(IamHumanConstants::$IAMHUMAN_SETTINGS_ID, IamHumanConstants::IAMHUMAN_OPTION_QUESTION_COLOUR_TWO_STRING);

    // answer colour strings
    register_setting(IamHumanConstants::$IAMHUMAN_SETTINGS_ID, IamHumanConstants::IAMHUMAN_OPTION_ANSWER_COLOUR_ONE_STRING);
    register_setting(IamHumanConstants::$IAMHUMAN_SETTINGS_ID, IamHumanConstants::IAMHUMAN_OPTION_ANSWER_COLOUR_TWO_STRING);

    register_setting(IamHumanConstants::$IAMHUMAN_SETTINGS_ID, IamHumanConstants::IAMHUMAN_OPTION_ENABLE_POST_COMMENTS);
}

function iamhuman_get_colour_one() {
    return get_option(IamHumanConstants::IAMHUMAN_OPTION_COLOUR_ONE, '#006495');
}

function iamhuman_get_colour_two() {
    return get_option(IamHumanConstants::IAMHUMAN_OPTION_COLOUR_TWO, '#E0A026');
}

function iamhuman_get_background_colour() {
    return get_option(IamHumanConstants::IAMHUMAN_OPTION_BACKGROUND_COLOUR, 'white');
}

function iamhuman_get_incorrect_attempt_message() {
    return get_option(IamHumanConstants::IAMHUMAN_OPTION_INCORRECT_ATTEMPT_MESSAGE, 'Incorrect Attempt');
}

/**
 * Gets the grid size, with the default option set.
 * 
 * @return int
 */
function iamhuman_get_grid_size_setting() {
    return get_option(IamHumanConstants::IAMHUMAN_OPTION_GRID_SIZE, 22);
}

add_action('admin_init', 'iamhuman_register_settings');

function iamhuman_enqueue_color_picker($hook_suffix) {

    if ("settings_page_i-am-human-options" === $hook_suffix) {
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');
    }
}

add_action('admin_enqueue_scripts', 'iamhuman_enqueue_color_picker');

function iamhuman_options_page() {


    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    echo "<h2>" . __("I am human", IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN) . "</h2>";
    ?>
    <div class="wrap">

        <form method="post" action="options.php" id="iamhuman_template_form">
            <input type="hidden" value="template_select" id="setting_type" name="setting_type"/>
            <?php
            // look for templates
            $template_files = iamhuman_get_templates();

            if (sizeof($template_files) > 0) {

                echo "<h3>" . __("Templates", IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN) . "</h3>";

                echo "<p>" . __("You have some templates installed! Choose one in the below dropdown box, or make your own grids "
                        . "in the section below.", IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN);

                echo "</p>";
                echo "<p><strong>";

                echo __("Note that choosing a template will overwrite any changes made manually.", IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN);

                echo "</strong></p>";

                echo "<select id='template_select'>";

                foreach ($template_files as $template_file) {
                    $contents = file_get_contents($template_file);
                    $json = json_decode($contents);
                    echo "<option value='" . $json->id . "'>" . $json->template_name . "</option>";
                }

                echo "</select>";
		?>

            	<p class="submit">
                	<input id="apply_template_submit" 
                       	class="button button-primary" 
                       	type="submit" 
                       	value="<?php echo __("Apply Template", IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN); ?>" />

	<?php
            }
	?>

        </form>

        <script>

            jQuery(document).ready(function($) {

                $("#apply_template_submit").click(function(event) {
                    event.preventDefault();

                    if (confirm('Are you sure you wish to apply this template and overwrite your changes?')) {

                        template_id = $("#template_select").val();

                        var data = {
                            'action': '<?php echo IamHumanConstants::$IAMHUMAN_AJAX_ACTION_SET_TEMPLATE; ?>',
                            'template_id': template_id
                        };

                        $.post(ajaxurl, data, function(response) {

                            if (response === '0') {
                                alert("Unfortunately the server had a problem with your request.")
                            } else {
                                alert(response);
                               // document.location.reload(true);
                            }
                        });
                    }
                });

            });
        </script>

        <form method="post" action="options.php" id="iamhuman_options_form">

            <?php settings_fields(IamHumanConstants::$IAMHUMAN_SETTINGS_ID); ?>
            <?php do_settings_sections(IamHumanConstants::$IAMHUMAN_SETTINGS_ID); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable for post comments</th>
                    <?php
                    $enabled = filter_var(filter_var(get_option(IamHumanConstants::IAMHUMAN_OPTION_ENABLE_POST_COMMENTS), FILTER_VALIDATE_BOOLEAN));
                    if ($enabled) {
                        $value = "checked='checked'";
                    } else {
                        $value = "";
                    }
                    ?>
                    <td><input type="checkbox" name="enable_post_comments" <?php echo $value; ?> value="true" /></td>
                </tr>
            </table>

            <?php echo "<h3>" . __("Dialog Box Options", IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN) . "</h3>"; ?>

            <p>
                <?php
                echo __("Select a title for the dialog box, as well as some descriptive text that will be displayed near the grid so the user"
                        . " knows what they should colour in.", IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN);
                ?>
            </p>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php echo __('Dialog title', IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN) ?></th>
                    <td><input name="description_title" value="<?php echo get_option(IamHumanConstants::IAMHUMAN_OPTION_DESCRIPTION_TITLE); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo __('Description Text', IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN) ?></th>
                    <td><textarea name="description_text"><?php echo get_option(IamHumanConstants::IAMHUMAN_OPTION_DESCRIPTION_TEXT); ?></textarea></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo __('Incorrect Attempt Message', IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN) ?></th>
                    <td><textarea name="incorrect_attempt_message"><?php echo iamhuman_get_incorrect_attempt_message(); ?></textarea></td>
                </tr>

                <tr valign="top">
                    <td  colspan="2">
                        <p>
                            <strong>
                                <?php echo __("WARNING: Changing the grid size will likely ruin your picture. Make sure to set it to the size you want
                            before you start!"); ?>
                            </strong>
                        </p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo __('Grid Size', IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN) ?></th>
                    <td><input name="grid_size" type="number" value="<?php echo iamhuman_get_grid_size_setting(); ?>" /></td>
                </tr> 
            </table>

            <?php echo "<h3>" . __("Colours", IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN) . "</h3>"; ?>

            <p>
                <?php
                echo __("Select the colours that will be used in the grid. Note that colour one is the only colour users will be able to select."
                        , IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN);
                ?>
            </p>
	 
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php echo __('Colour one', IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN) ?></th>
                    <td><input type="text" class='colour_field' id="colour_one" name="colour_one" value="<?php echo iamhuman_get_colour_one(); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo __('Colour two', IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN) ?></th>
                    <td><input type="text" class='colour_field' id="colour_two" name="colour_two" value="<?php echo iamhuman_get_colour_two(); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo __('Hover colour', IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN) ?></th>
                    <td><input type="text" class='colour_field' id="colour_hover" name="colour_hover" value="<?php echo get_option(IamHumanConstants::IAMHUMAN_OPTION_COLOUR_HOVER, '#F4D00C'); ?>" /></td>
                </tr> 
                <tr valign="top">
                    <th scope="row"><?php echo __('Background colour', IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN) ?></th>
                    <td><input type="text" class='colour_field' id="background_colour" name="background_colour" value="<?php echo iamhuman_get_background_colour(); ?>" /></td>
                </tr>
            </table>    

            <?php echo "<h3>" . __("Question Image", IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN) . "</h3>"; ?>

            <p>
                <?php
                echo __("Build the image you would like to be displayed initally below by clicking on the grid. Click once for the first colour,"
                        . "twice for the second colour, and a third time to reset to the background colour.", IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN);
                ?>
            </p>    

            <p>
                <?php
                echo __("Note that the user will only be able to specify colour one, not colour two, to complete the image. Nor will they be able to "
                        . "modify any cells that are set to colour two.", IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN);
                ?>
            </p>

            <div class="grid_container">
                <div id="iamhuman_grid_question" class="iamhuman_grid"></div>
            </div>
            <script>
                jQuery(function($) {
                    colour_one_string = "<?php echo get_option(IamHumanConstants::IAMHUMAN_OPTION_QUESTION_COLOUR_ONE_STRING) ?>";
                    colour_two_string = "<?php echo get_option(IamHumanConstants::IAMHUMAN_OPTION_QUESTION_COLOUR_TWO_STRING) ?>";

                    // setup the custom styles
                    $('html > head').append($('<style> .colour_bg { background: <?php echo iamhuman_get_background_colour(); ?>; }</style>'));
                    $('html > head').append($('<style> .colour_one { background: <?php echo iamhuman_get_colour_one(); ?>; }</style>'));
                    $('html > head').append($('<style> .colour_two { background: <?php echo iamhuman_get_colour_two(); ?>; }</style>'));
                    $(".grid_cell").css('background-color', '<?php echo iamhuman_get_background_colour(); ?>');

                    create_grid("#iamhuman_grid_question", <?php echo iamhuman_get_grid_size_setting(); ?>);

                    highlight_list("#iamhuman_grid_question", "colour_one", colour_one_string);
                    highlight_list("#iamhuman_grid_question", "colour_two", colour_two_string);

                    enable_handlers("#iamhuman_grid_question", true);

                    $("#iamhuman_options_form").submit(function() {
                        colour_one_string = get_selection_string("#iamhuman_grid_question", "colour_one");
                        $("#question_colour_one_string").val(colour_one_string);

                        colour_two_string = get_selection_string("#iamhuman_grid_question", "colour_two");
                        $("#question_colour_two_string").val(colour_two_string);
                    });

                    $('.colour_field').wpColorPicker();
                });


            </script>

            <table class="form-table" style="display: none;">
                <tr valign="top">
                    <th scope="row"><?php echo __('question_colour_one_string', IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN) ?></th>
                    <td><input type="text" id="question_colour_one_string" name="question_colour_one_string" value="<?php echo get_option(IamHumanConstants::IAMHUMAN_OPTION_QUESTION_COLOUR_ONE_STRING); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo __('question_colour_two_string', IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN) ?></th>
                    <td><input type="text" id="question_colour_two_string" name="question_colour_two_string" value="<?php echo get_option(IamHumanConstants::IAMHUMAN_OPTION_QUESTION_COLOUR_TWO_STRING); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo __('answer_colour_one_string', IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN) ?></th>
                    <td><input type="text" id="answer_colour_one_string" name="answer_colour_one_string" value="<?php echo get_option(IamHumanConstants::IAMHUMAN_OPTION_ANSWER_COLOUR_ONE_STRING); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo __('answer_colour_two_string', IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN) ?></th>
                    <td><input type="text" id="answer_colour_two_string" name="answer_colour_two_string" value="<?php echo get_option(IamHumanConstants::IAMHUMAN_OPTION_ANSWER_COLOUR_TWO_STRING); ?>" /></td>
                </tr>
            </table>

            <?php echo "<h3>" . __("Answer Image", IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN) . "</h3>"; ?>
            <p>
                <?php
                echo __("Build the answer image below, that the user must match in order to pass the test succesfully.", IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN);
                ?>
            </p>    

            <p>
                <?php echo __('Click the copy button to copy the question image to the answer image.', IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN); ?>
                <input type="button" id="copy_to_answer" value="Copy"/>
            </p>



            <script>
                jQuery(function($) {

                    $("#copy_to_answer").click(function() {
                        clear_grid('#iamhuman_grid_answer');

                        // copy
                        colour_one_string = get_selection_string("#iamhuman_grid_question", "colour_one");
                        colour_two_string = get_selection_string("#iamhuman_grid_question", "colour_two");
                        highlight_list("#iamhuman_grid_answer", "colour_one", colour_one_string);
                        highlight_list("#iamhuman_grid_answer", "colour_two", colour_two_string);
                    });

                });

            </script>



            <div class="grid_container">
                <div id="iamhuman_grid_answer" class="iamhuman_grid"></div>
            </div>
            <script>
                jQuery(function($) {

                    create_grid("#iamhuman_grid_answer", <?php echo iamhuman_get_grid_size_setting(); ?>);

                    answer_colour_one_string = "<?php echo get_option(IamHumanConstants::IAMHUMAN_OPTION_ANSWER_COLOUR_ONE_STRING);?>";
                    answer_colour_two_string = "<?php echo get_option(IamHumanConstants::IAMHUMAN_OPTION_ANSWER_COLOUR_TWO_STRING); ?>";

                    highlight_list("#iamhuman_grid_answer", "colour_one", answer_colour_one_string);
                    highlight_list("#iamhuman_grid_answer", "colour_two", answer_colour_two_string);

                    $("#iamhuman_options_form").submit(function() {
                        answer_colour_one_string = get_selection_string("#iamhuman_grid_answer", "colour_one");
                        answer_colour_two_string = get_selection_string("#iamhuman_grid_answer", "colour_two");

                        $("#answer_colour_one_string").val(answer_colour_one_string);
                        $("#answer_colour_two_string").val(answer_colour_two_string);
                    });

                    enable_handlers("#iamhuman_grid_answer", false);

                });

            </script> 

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

add_action('admin_menu', 'iamhuman_menu');

function iamhuman_menu() {
    add_options_page(
            __('I am human options', 'i-am-human'), __('I am human options', 'i-am-human'), 'manage_options', 'i-am-human-options', 'iamhuman_options_page');
}


/**
 * Applys the template ajax callback
 * 
 * @global type $wpdb
 */
function iamhuman_ajax_set_template_callback() {
//    global $wpdb; // this is how you get access to the database
//    // TODO: APPLY THE TEMPLATE!

    $template_id = $_POST['template_id'];

    $response = new stdClass();
    
    if ($template_id) {

        $template = iamhuman_get_template($template_id);

        if ($template) {
            iamhuman_apply_template($template);
            $response->success = true;
        } else {
            $response->success = false;
            $response->messsage = __("No template matched that id", IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN);
        }
    } else {
        $response->success = false;
        $response->messsage = __("No template matched was specified.", IamHumanConstants::$IAMHUMAN_TEXT_DOMAIN);
    }

    echo json_encode($response);

    die();
}

add_action('wp_ajax_' . IamHumanConstants::$IAMHUMAN_AJAX_ACTION_SET_TEMPLATE, 'iamhuman_ajax_set_template_callback');

/**
 * Returns all templates.
 * 
 * @return type 
 */
function iamhuman_get_templates() {
    return glob(plugin_dir_path(__FILE__) . IamHumanConstants::$IAMHUMAN_TEMPLATE_FOLDER . "*.tpl");
}

/**
 * Returns a template with the given id if it exists, otherwise returns null.
 * 
 * @param type $template_id
 * @return null
 */
function iamhuman_get_template($template_id) {
    $template_files = iamhuman_get_templates();

    if (sizeof($template_files) > 0) {
        foreach ($template_files as $template_file) {
            $contents = file_get_contents($template_file);
            $json = json_decode($contents);

            if ($json->id === $template_id) {
                return $json;
            }
        }
    }

    return null;
}

/**
 * Applies the given template
 * 
 * @param type $template
 */
function iamhuman_apply_template($template) {
    iamhuman_update_template_option($template, IamHumanConstants::IAMHUMAN_OPTION_COLOUR_ONE);
    iamhuman_update_template_option($template, IamHumanConstants::IAMHUMAN_OPTION_COLOUR_TWO);
    iamhuman_update_template_option($template, IamHumanConstants::IAMHUMAN_OPTION_COLOUR_HOVER);
    iamhuman_update_template_option($template, IamHumanConstants::IAMHUMAN_OPTION_GRID_SIZE);
    iamhuman_update_template_option($template, IamHumanConstants::IAMHUMAN_OPTION_BACKGROUND_COLOUR);
    iamhuman_update_template_option($template, IamHumanConstants::IAMHUMAN_OPTION_DESCRIPTION_TITLE);
    iamhuman_update_template_option($template, IamHumanConstants::IAMHUMAN_OPTION_DESCRIPTION_TEXT);
    iamhuman_update_template_option($template, IamHumanConstants::IAMHUMAN_OPTION_INCORRECT_ATTEMPT_MESSAGE);
    iamhuman_update_template_option($template, IamHumanConstants::IAMHUMAN_OPTION_QUESTION_COLOUR_ONE_STRING);
    iamhuman_update_template_option($template, IamHumanConstants::IAMHUMAN_OPTION_QUESTION_COLOUR_TWO_STRING);
    iamhuman_update_template_option($template, IamHumanConstants::IAMHUMAN_OPTION_ANSWER_COLOUR_ONE_STRING);
    iamhuman_update_template_option($template, IamHumanConstants::IAMHUMAN_OPTION_ANSWER_COLOUR_TWO_STRING);
}

function iamhuman_update_template_option($template, $option_id) {
    if($template->{$option_id}) {
        update_option($option_id, $template->{$option_id});
    }
}


?>

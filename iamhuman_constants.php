<?php

class IamHumanConstants {

    static $IAMHUMAN_SETTINGS_ID = "iamhuman-settings";
    static $IAMHUMAN_TEXT_DOMAIN = "i-am-human";
    static $IAMHUMAN_AJAX_ACTION_SET_TEMPLATE = "iamhuman_set_template";

    /**
     * template directory
     * 
     * @var string
     */
    static $IAMHUMAN_TEMPLATE_FOLDER = "templates/";

    /**
     * Options
     * 
     * When adding a new option, remember to add it to the all options
     * array below.
     * 
     * @var string
     */
    const IAMHUMAN_OPTION_COLOUR_ONE = "colour_one";
    const IAMHUMAN_OPTION_COLOUR_TWO = "colour_two";
    const IAMHUMAN_OPTION_COLOUR_HOVER = "colour_hover";
    const IAMHUMAN_OPTION_GRID_SIZE = "grid_size";
    const IAMHUMAN_OPTION_BACKGROUND_COLOUR = "background_colour";
    const IAMHUMAN_OPTION_DESCRIPTION_TITLE = "description_title";
    const IAMHUMAN_OPTION_DESCRIPTION_TEXT = "description_text";
    const IAMHUMAN_OPTION_INCORRECT_ATTEMPT_MESSAGE = "incorrect_attempt_message";
    const IAMHUMAN_OPTION_QUESTION_COLOUR_ONE_STRING = "question_colour_one_string";
    const IAMHUMAN_OPTION_QUESTION_COLOUR_TWO_STRING = "question_colour_two_string";
    const IAMHUMAN_OPTION_ANSWER_COLOUR_ONE_STRING = "answer_colour_one_string";
    const IAMHUMAN_OPTION_ANSWER_COLOUR_TWO_STRING = "answer_colour_two_string";
    const IAMHUMAN_OPTION_ENABLE_POST_COMMENTS = "enable_post_comments";

    /**
     * All options
     * 
     * @var array(string)
     */
    static $IAMHUMAN_OPTIONS = array(
        IAMHUMAN_OPTION_COLOUR_ONE,
        IAMHUMAN_OPTION_COLOUR_TWO,
        IAMHUMAN_OPTION_COLOUR_HOVER,
        IAMHUMAN_OPTION_GRID_SIZE,
        IAMHUMAN_OPTION_BACKGROUND_COLOUR,
        IAMHUMAN_OPTION_DESCRIPTION_TITLE,
        IAMHUMAN_OPTION_DESCRIPTION_TEXT,
        IAMHUMAN_OPTION_INCORRECT_ATTEMPT_MESSAGE,
        IAMHUMAN_OPTION_QUESTION_COLOUR_ONE_STRING,
        IAMHUMAN_OPTION_QUESTION_COLOUR_TWO_STRING,
        IAMHUMAN_OPTION_ANSWER_COLOUR_ONE_STRING,
        IAMHUMAN_OPTION_ANSWER_COLOUR_TWO_STRING,
        IAMHUMAN_OPTION_ENABLE_POST_COMMENTS,
    );

}

?>

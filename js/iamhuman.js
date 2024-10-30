
function enable_handlers(grid_selector, build_mode) {

    jQuery(grid_selector + ' .grid_cell').mouseover(function(event) {
        selected = event.target;
        jQuery(selected).addClass("cell_mouse_over");
    });

    jQuery(grid_selector + ' .grid_cell').mouseout(function(event) {
        selected = event.target;
        jQuery(selected).removeClass("cell_mouse_over");
    });

    jQuery(grid_selector + ' .grid_cell').click(function(event) {
        selected = event.target;

        if (!build_mode) {

            // when actually being used, the user can only use colour_one.
            // and can only specify a cell if the cell does not already have
            // colour_two selected.
            
            if (jQuery(selected).hasClass("colour_one")) {
                jQuery(selected).removeClass("colour_one");
            } else if(!jQuery(selected).hasClass("colour_two")) {
                jQuery(selected).addClass("colour_one");
            }

        } else {

            if (jQuery(selected).hasClass("colour_one")) {
                jQuery(selected).removeClass("colour_one");
                jQuery(selected).removeClass("colour_bg");
                jQuery(selected).addClass("colour_two");


            } else if (jQuery(selected).hasClass("colour_two")) {
                jQuery(selected).removeClass("colour_two");
                jQuery(selected).removeClass("colour_one");
                jQuery(selected).addClass("colour_bg");
            } else { // no colour
                jQuery(selected).removeClass("colour_bg");
                jQuery(selected).removeClass("colour_two");
                jQuery(selected).addClass("colour_one");
            }

        }

        selected_x = jQuery(selected).data("x");
        selected_y = jQuery(selected).data("y");

        //console.log("{" + selected_x + "," + selected_y + "}");
    });
}

function create_grid(grid_selector, grid_length, row_style, cell_style) {

    grid_element = jQuery(grid_selector);
     
    grid_element.css("display", "table");
    
    if (row_style) {
        row_style = 'style="' + row_style + '"';
    } else {
        row_style = '';
    }

    if (cell_style) {
        cell_style = 'style="' + cell_style + '"';
    } else {
        cell_style = '';
    }

    for (x = 0; x < grid_length; x++) {

        new_row = jQuery('<div class="grid_row" ' + row_style + '></div>');

        for (y = 0; y < grid_length; y++) {
            new_cell = jQuery('<span class="grid_cell colour_bg" ' + cell_style + ' data-x="' + x + '" data-y="' + y + '"></span>');
            new_row.append(new_cell);
        }

        grid_element.append(new_row);
    }
    
    grid_element.width(10 * grid_length);
    grid_element.height(10 * grid_length);
    
    // 22 ->  250px; (11)
    //height: 250px;  
 
    return grid_element;
}

function get_selection_string(grid_selector, selector_class) {
    selection_string = "";

    jQuery(grid_selector + " ." + selector_class).each(function() {
        selected_x = jQuery(this).data("x");
        selected_y = jQuery(this).data("y");
        selection_string += "{" + selected_x + "," + selected_y + "}";
    });

    return selection_string;
}

function to_coord_array(selection_string) {

    coords = selection_string.split("}");
    seperated = [];

    for (x = 0; x < coords.length; x++) {
        trimmed = coords[x].substring(1);
        seperated.push(trimmed);
    }
    return seperated;
}

/**
 * Removes all styles from all cells.
 * 
 * @param string grid_selector
 * @returns
 */
function clear_grid(grid_selector) {
    jQuery(grid_selector + " .grid_cell").each(function() {
        jQuery(this).removeClass("colour_one");
        jQuery(this).removeClass("colour_two");
        jQuery(this).addClass("colour_bg");
    });
}

function delete_grid(grid_selector) {
    jQuery(grid_selector).empty();
}

function highlight_list(grid_selector, highlight_class, selection_string) {

    if (selection_string != null) {
        coords = to_coord_array(selection_string);

        jQuery(grid_selector + " .grid_cell").each(function() {

            selected_x = jQuery(this).data("x");
            selected_y = jQuery(this).data("y");
            if (jQuery.inArray(selected_x + "," + selected_y, coords) !== -1) {
                jQuery(this).addClass(highlight_class);
            }

        });
    }
}

function array_compare(first_array, second_array) {

    if (first_array.length !== second_array.length) {
        return false;
    }

    for (var i = 0; i < second_array.length; i++) {

        if (first_array[i].compare) {
            if (!first_array[i].compare(second_array[i])) {
                return false;
            }
        }

        if (first_array[i] !== second_array[i]) {
            return false;
        }
    }
    return true;
}

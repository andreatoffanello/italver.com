<?php
/* Image with text over shortcode */
if (!function_exists('image_with_text_over')) {

    function image_with_text_over($atts, $content = null) {
        $args = array(
            "layout_width"			=> "",
            "image"					=> "",
            "image_shader_color"	=> "",
            "icon"					=> "",
            "icon_size"				=> "",
            "icon_color"			=> "",
            "title"					=> "",
            "title_color"			=> "",
            "title_size"			=> "",
            "title_tag"				=> "h3"
        );

        extract(shortcode_atts($args, $atts));

        $headings_array = array('h2', 'h3', 'h4', 'h5', 'h6');

        //get correct heading value. If provided heading isn't valid get the default one
        $title_tag = (in_array($title_tag, $headings_array)) ? $title_tag : $args['title_tag'];

        //init variables
        $html            = "";
        $title_styles    = "";
        $subtitle_styles = "";
        $line_styles     = "";
        $no_icon         = "";
        $icon_styles     = "";
        $shader_style    = "";

        //generate styles
        if($title_color != "") {
            $title_styles .= "color: ".$title_color.";";
        }

        if($title_size != "") {
            $valid_title_size = (strstr($title_size, 'px', true)) ? $title_size : $title_size.'px';
            $title_styles .= "font-size: ".$valid_title_size.";";
        }

        if($icon_color != "") {
            $bcolor = qode_hex2rgb($icon_color);
            $icon_styles .= "style='color: ".$icon_color."; border-color: rgba(".$bcolor[0].",".$bcolor[1].",".$bcolor[2].",0.6);'";
        }

        if (is_numeric($image)) {
            $image_src = wp_get_attachment_url($image);
        } else {
            $image_src = $image;
        }

        if ($image_shader_color !== '') {
            $shader_style = 'style = "background-color: ' . $image_shader_color . '"';
        }

        if($icon == ""){
            $no_icon = "no_icon";
        }

        //generate output
        $html .= '<div class="q_image_with_text_over '.$layout_width.'">';
        $html .= '<div class="shader" ' . $shader_style . '></div>';

        $html .= '<img src="' . $image_src . '" alt="' . $title . '" />';
        $html .= '<div class="text">';

        //title and subtitle table html
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<td>';
        if($icon != ""){
            $html .= '<i class="icon_holder fa '.$icon.' '.$icon_size.' " '.$icon_styles .'></i>';
        }
        $html .= '<'.$title_tag.' class="caption '.$no_icon.'" style="'.$title_styles.'">'.$title.'</'.$title_tag.'>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</table>';

        //image description table html which appears on mouse hover
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<td>';
        $html .= '<div class="desc">' . do_shortcode($content) . '</div>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '</div>'; //close text div
        $html .= '</div>'; //close image_with_text_over

        return $html;
    }
    add_shortcode('image_with_text_over', 'image_with_text_over');
}
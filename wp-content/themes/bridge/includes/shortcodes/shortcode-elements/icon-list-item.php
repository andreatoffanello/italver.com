<?php
/* Icon List Item shortcode */
if (!function_exists('icon_list_item')) {
    function icon_list_item($atts, $content = null) {
        $args = array(
            "icon"                                  => "",
            "icon_type"                             => "",
            "icon_color"                            => "",
            "icon_background_color"                 => "",
            "icon_border_color"                     => "",
            "title"                                 => "",
            "title_color"                           => "",
            "title_size"                            => ""
        );

        extract(shortcode_atts($args, $atts));
        $html           = '';
        $icon_style     = "";
        $icon_classes   = "";
        $title_style    = "";

        $icon_classes .= $icon_type." ";

        if($icon_color != "") {
            $icon_style .= "color:".$icon_color.";";
        }

        if($icon_background_color != "") {
            $icon_style .= "background-color: {$icon_background_color};";
        }

        if($icon_border_color != "") {
            $icon_style .= "border-color:".$icon_border_color.";";
        }

        if($title_color != "") {
            $title_style .= "color:".$title_color.";";
        }

        if($title_size != "") {
            $title_style .= "font-size: ".$title_size."px;";
        }

        $html .= '<div class="q_icon_list">';
        $html .= '<i class="fa '.$icon.' pull-left '.$icon_classes.'" style="'.$icon_style.'"></i>';
        $html .= '<p style="'.$title_style.'">'.$title.'</p>';
        $html .= '</div>';
        return $html;
    }
    add_shortcode('icon_list_item', 'icon_list_item');
}

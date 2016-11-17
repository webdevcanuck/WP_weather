<?php
/*
  Plugin Name: WP Plugin Base
  Plugin URI: http://pluginurl.com
  Description: Description of Plugin
  Version: 1.0.0
  Author: Author Name
  Author URI: http://autherurl.com

  Copyright 2013

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if (!defined('WPPB_VER')) {
    define('WPPB_VER', '1.0.0');
}

class My_Plugin
{
    private $var = 'asdf';

    public function __construct()
    {
        add_filter('get_my_plugin_instance', [$this, 'get_instance']);
    }

    public function get_instance()
    {
        return $this; // return the object
    }

    public function foo($atts = [])
    {
        $a = shortcode_atts(array(
            'bah' => 'My Title',
                ), $atts);

        return esc_attr($a['bah']); // never echo or print in a shortcode!
    }
}

add_shortcode('baztag', [new My_Plugin(), 'foo']);

class wp_my_plugin extends WP_Widget
{
    // constructor
    public function wp_my_plugin()
    {
        parent::WP_Widget(false, $name = __('My Weather', 'wp_widget_plugin'));
    }

    // widget form creation
    public function form($instance)
    {
        // Check values
        if ($instance) {
            $title = esc_attr($instance['title']);
        } else {
            $title = '';
        } ?>

				<p>
					Current: <?php echo $title; ?>
<br/>
					<select name="<?php echo $this->get_field_name('title'); ?>">
						<option>Toronto</option>
						<option>Mississauga</option>
					</select>
				</p>
        <?php

    }

    // widget update
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        // Fields
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['text'] = strip_tags($new_instance['text']);
        $instance['textarea'] = strip_tags($new_instance['textarea']);

        return $instance;
    }

    // widget display
    public function widget($args, $instance)
    {
        extract($args);
        // these are the widget options
        $title = apply_filters('widget_title', $instance['title']);
        $text = $instance['text'];
        $textarea = $instance['textarea'];
        echo $before_widget;
        // Display the widget
        echo '<div class="widget-text wp_widget_plugin_box">';

        // Check if title is set
        if ($title) {
            echo $before_title.$title.' Weather '.$after_title;
        }

        // Check if text is set
        if ($text) {
            echo '<p class="wp_widget_plugin_text">'.$text.' Weather</p>';
        }
        // Check if textarea is set
        if ($textarea) {
            echo '<p class="wp_widget_plugin_textarea">'.$textarea.'</p>';
        }
        echo '</div>';
        echo $after_widget;

        $json = file_get_contents('http://api.openweathermap.org/data/2.5/weather?q='.$title.'&appid=20c3b91de5fbe4adbd50fade26f79a44');

        $obj = json_decode($json);
        echo $obj->weather[0]->main."<br/><br/><img src='http://openweathermap.org/img/w/".$obj->weather[0]->icon.".png' style='width:25%'/><br/><br/>";

        print_r($obj);
    }
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("wp_my_plugin");'));
?>

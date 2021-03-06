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

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!defined('WPPB_VER')) {
    define('WPPB_VER', '1.0.0');
}

class My_Plugin {

    private $var = 'asdf';

    public function __construct() {
        add_filter('get_my_plugin_instance', [$this, 'get_instance']);
        add_action('admin_menu', [$this, 'my_plugin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'my_enqueue_styles']);     
    }

    public function get_instance() {
        return $this; // return the object
    }

    public function foo($atts = []) {
        $a = shortcode_atts(array(
            'bah' => 'My Title',
                ), $atts);

        return esc_attr($a['bah']); // never echo or print in a shortcode!
    }

    /** Step 1. */
    public function my_plugin_menu() {
        add_options_page('My Plugin Options', 'My Plugin', 'manage_options', 'my-unique-identifier', 'my_plugin_options');
    }

    /** Step 3. */
    public function my_plugin_options() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        echo '<div class="wrap">';
        echo '<p>Here is where the form would go if I actually had options.</p>';
        echo '</div>';
    }

    public function my_enqueue_styles() {
        wp_enqueue_script(
                'my_weather_plugin', plugin_dir_url(__FILE__) . 'js/main.js', array(), '1.0.0', true
        );
    }

}

add_shortcode('baztag', [new My_Plugin(), 'foo']);

class wp_my_plugin extends WP_Widget {

    // constructor
    public function wp_my_plugin() {
        parent::WP_Widget(false, $name = __('My Weather', 'wp_widget_plugin'));
    }

    // widget form creation
    public function form($instance) {
        // Check values
        if ($instance) {
            $title = esc_attr($instance['title']);
        } else {
            $title = '';
        }
        ?>

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
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        // Fields
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['text'] = strip_tags($new_instance['text']);
        $instance['textarea'] = strip_tags($new_instance['textarea']);

        return $instance;
    }

    // widget display
    public function widget($args, $instance) {
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
            echo $before_title . $title . ' Weather ' . $after_title;
        }

        // Check if text is set
        if ($text) {
            echo '<p class="wp_widget_plugin_text">' . $text . ' Weather</p>';
        }
        // Check if textarea is set
        if ($textarea) {
            echo '<p class="wp_widget_plugin_textarea">' . $textarea . '</p>';
        }
        echo '</div>';
        echo $after_widget;

        $getAPI = new my_getOptions();

        $json = file_get_contents('http://api.openweathermap.org/data/2.5/weather?q=' . $title . '&appid=' . $getAPI->outPut());

        $obj = json_decode($json);
        echo $obj->weather[0]->main . "<br/><br/><img src='http://openweathermap.org/img/w/" . $obj->weather[0]->icon . ".png' style='width:25%'/><br/><br/>";

        print_r($obj);
    }

}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("wp_my_plugin");'));

class my_adminMenu {

    public function __construct() {
        add_action('admin_menu', array($this, 'my_createMenu'));
    }

    public function my_createMenu() {
        add_menu_page('My Plugin Options', 'My Plugin', 'manage_options', 'my-unique-identifier', array($this, 'my_plugin_options'), plugins_url('wp-plugin-test/images/icon.png'), 6);
    }

    public function my_plugin_options() {
        $getAPI = new my_getOptions();
        $getCities = new my_getCities();

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'update') {
                update_option('my_weather_plugin', $_POST['extra_post_info']);
            }
        }
        ?>

        <h1>The Weather Plugin</h1>
        <hr/>
        <h2>Get API Key</h2>
        <a href="https://home.openweathermap.org/" target="_blank" class="button button-primary">Click To Get API Key</a>
        <hr/>
        <h2>Enter API Key</h2>
        <form method="post" action="">
        <?php settings_fields('extra-post-info-settings'); ?>
        <?php do_settings_sections('extra-post-info-settings'); ?>
           <table class="form-table">
              <tr valign="top">
                 <th scope="row">API Key:</th>
                 <td><input type="text" name="extra_post_info" value="<?php echo $getAPI->outPut(); ?>"/></td>
              </tr>
           </table>
           <hr/>
           <h2>Your Cities</h2>
           <table class="form-table">
              <tr>
                 <th class="row">City List</th><th class="row">Selected Cities</th>
              </tr>
              <tr valign="top">
                 <td><?php echo $getCities->citiesOut(); ?><div id="my_addToSelectedCities" class="button button-primary">Add to Selected Cities</div></td>
                 <td><select id="selectedcities" name="my_selectedCities" multiple></select><div id="my_removeSelectedCities" class="button button-primary">Remove Selected</div></td>
              </tr>
           </table>
           <hr/>
        <?php submit_button(); ?>
        </form>

        <?php
    }

}

new my_adminMenu();

class my_getOptions {

    private $thisOption;

    public function __construct() {
        $this->thisOption = get_option('my_weather_plugin');
    }

    public function outPut() {
        return $this->thisOption;
    }

}

/* - - - - */

class my_getCities {

    private $cityOptions;

    public function __construct() {
        
    }

    public function citiesOut() {
        $json = file_get_contents('http://localhost:8888/wp/wp-content/plugins/wp-plugin-test/city.list.json');
        $obj = json_decode($json);

        $this->cityOptions = '<select name="my_cityOptions" multiple>';
        foreach ($obj->cities as $mydata) {
            $this->cityOptions .= '<option>' . $mydata->name . '</option>';
        }

        return $this->cityOptions . '</select>';
    }

}
?>

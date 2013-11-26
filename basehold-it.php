<?php
/*
Plugin Name: Basehold.it for Wordpress
Plugin URI: http://web.peterhartree.co.uk
Description: Grants Wordpress theme developers (even more) convenient access to Dan
  Eden's Basehold.it baseline grid overlay.
Version: 1.0
Author: Peter Hartree
Author URI: http://web.peterhartree.co.uk
License: GPL2
*/

/** ==========================================================================
  *! Adds Basehold.it stylesheet according to default settings or the params
  * supplied in query string.
  ========================================================================== */

function basehold_it() {
  if(isset($_GET['bl'])):
    $baseline = $_GET['bl'];
  endif;

  /* correct invalid user input */
  $baseline = str_replace('px', '', $baseline);

  if(!is_numeric($baseline)):
    $baseline = get_option('basehold_it_default_height');
  endif;

  if(isset($_GET['col'])):
    $baseline_color = $_GET['col'];

    /* correct invalid user input */
    if($baseline_color == 'white'): $baseline_color = 'FFFFFF'; endif;
    if(strlen($baseline_color) == 3): $baseline_color = $baseline_color . $baseline_color; endif;

    $baseline_color = str_replace('#', '', $baseline_color);

    if(!ctype_xdigit($baseline_color)):
      $baseline_color = get_option('basehold_it_default_color');
    endif;

  else:
      $baseline_color = get_option('basehold_it_default_color');
  endif;

  wp_enqueue_style( 'basehold-it', 'http://basehold.it/'.$baseline.'/'.$baseline_color);
}

if(isset($_GET['bl']) || get_option('basehold_it_permanent')):
  add_action( 'wp_enqueue_scripts', 'basehold_it' );
endif;

/** ==========================================================================
  *! Adds options panel to Wordpress Tools menu
  ========================================================================== */
function basehold_it_options() {
?>
  <div class="wrap">
  <h2>Basehold.it</h2>
  <hr>
  <h3>Instructions</h3>
  <p>The simplest way to apply the grid overlay is to add a <strong>?bl</strong> querystring to the end of any URL on your site. So:</p>
  <blockquote><em>http://yoursite.com/</em> would become <em>http://yoursite.com/<strong>?bl</strong></em></blockquote>
  <p>Load a URL in this form and you'll see the grid applied, with the height and color settings specified below.</p>
  <hr>
  <h3>Settings</h3>
  <form method="post" action="options.php">
    <?php settings_fields( 'basehold_it_settings' ); ?>
    <?php do_settings_sections( 'basehold_it_settings' ); ?>

    <table class="form-table">
      <tr valign="top">
        <th scope="row">Default baseline height</th>
        <td>
          <input type="text" name="basehold_it_default_height" value="<?php echo get_option('basehold_it_default_height'); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">Default baseline color</th>
        <td>
          <input type="text" name="basehold_it_default_color" value="<?php echo get_option('basehold_it_default_color'); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">Show permanently</th>
          <td>
            <input type="checkbox" name="basehold_it_permanent" value="<?php echo get_option('basehold_it_permanent'); ?>" />
            <p>If this field is checked, the grid will <strong>always</strong> show.</p>
          </td>
      </tr>
    </table>
    <p><strong>N.B.</strong> If you like, you can specify a baseline height and color on the fly. Just append <strong>?bl=NUMBER&col=HEX</strong> to any page URL.</p>

    <?php submit_button(); ?>
  </form>
  </div>
<?php
}

function basehold_it_menu() {
  add_management_page('Basehold.it Settings', 'Basehold.it', 'manage_options', 'basehold-it', 'basehold_it_options');
}

add_action('admin_menu', 'basehold_it_menu');


/** ==========================================================================
  *!Register settings and default options on plugin activation
  ========================================================================== */
function basehold_it_activate() {
  register_setting( 'basehold_it_settings', 'basehold_it_permanent' );
  register_setting( 'basehold_it_settings', 'basehold_it_default_height' );
  register_setting( 'basehold_it_settings', 'basehold_it_default_color' );

  add_option( 'basehold_it_permanent', false, '', 'yes' );
  add_option( 'basehold_it_default_height', '24', '', 'yes' );
  add_option( 'basehold_it_default_color', 'FFFFFF', '', 'yes' );

}

register_activation_hook( __FILE__, 'basehold_it_activate' );

/** ==========================================================================
  *!Unregister settings on plugin de-activation
  ========================================================================== */
function basehold_it_deactivate() {
  unregister_setting( 'basehold_it_settings', 'basehold_it_permanent' );
  unregister_setting( 'basehold_it_settings', 'basehold_it_default_height' );
  unregister_setting( 'basehold_it_settings', 'basehold_it_default_color' );
}

register_deactivation_hook( __FILE__, 'myplugin_deactivate' );


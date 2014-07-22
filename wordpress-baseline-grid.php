<?php
/*
Plugin Name: WordPress Baseline Grid
Plugin URI: http://web.peterhartree.co.uk
Description: Gives theme developers convenient access to Dan Eden's
  baseline grid overlay service (Basehold.it). Append <strong>?bl</strong>
  to a url to get a baseline with default color and height. Use
  <strong>bl=INT</strong> and <strong>col=HEX</strong> parameters to adjust
  grid options on the fly, or tailor the default as appropriate via the
  <a href="tools.php?page=basehold-it">plugin settings</a> page.
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

  /* correct input if px unit supplied unnecessarily */
  $baseline = str_replace('px', '', $baseline);

  /* ignore invalid input */
  if(!is_numeric($baseline)):
    $baseline = get_option('basehold_it_default_height');
  endif;

  if(isset($_GET['col'])):
    $baseline_color = $_GET['col'];

    /* correct invalid input */
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

/* apply overlay */
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

  <p>To apply the grid overlay, add <strong>?bl</strong> to the end of any URL on your site. So:</p>
  <blockquote><em><?php echo get_site_url(); ?></em> would become <em><a href="<?php echo get_site_url(); ?>?bl" target="_blank"><?php echo get_site_url(); ?><strong>?bl</strong></a></em></blockquote>
  <p><strong>The default grid colour is white</strong>, so if you've got a mostly white site and don't see much, try changing the color setting, below.</p>
  <p>&nbsp;</p>
  <form method="post" action="options.php">
    <legend style="padding: 0; margin: 0;">
      <h3>Settings</h3>
    </legend>
    <p>Configure the default baseline using the fields below. This is the baseline that shows when you append <strong>?bl</strong> to a URL. </p>
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
        <th scope="row">Always on</th>
          <td>
            <input type="checkbox" name="basehold_it_permanent" value="<?php echo get_option('basehold_it_permanent'); ?>" />
          </td>
      </tr>
    </table>

    <p>If you prefer, you can specify baseline height and color on the fly. Just append <strong>?bl=NUMBER&col=HEX</strong> to a URL.</p>

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

register_deactivation_hook( __FILE__, 'basehold_it_deactivate' );
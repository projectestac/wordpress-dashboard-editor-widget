<?php
/**
 * @package Nodes_Editor_Widget
 * @version 1.0.0
 */
/**
 * Plugin Name: Nodes: Gutenberg Widget
 * Plugin URI: https://agora.xtec.cat/nodes/
 * Description: Generates a widget in the admin dashboard to easily enable and disable the Gutenberg editor. Uses the
 *              functionality provided by the plugin Advanced Editor Tools (previously TinyMCE Advanced).
 * Author: Toni Ginard
 * Version: 1.0.0
 * License: GPLv3
 */

const OPTIONS_KEY = 'tadv_admin_settings';
const TINYMCE_KEY = 'replace_block_editor';

/**
 * If there is the specific GET parameter, activate or deactivate the editor.
 */
$url_param = $_GET['dashboard-editor-widget'] ?? '';

if (!empty($url_param)) {
    if ($url_param === 'activate') {
        editor_widget_activate();
    } elseif ($url_param === 'deactivate') {
        editor_widget_deactivate();
    }
}

/**
 * Add the widget to the dashboard.
 */
add_action('wp_dashboard_setup', function () {
    if (current_user_can('activate_plugins')) {
        wp_add_dashboard_widget(
            'dashboard_widget_editor',
            'Edició per blocs',
            'dashboard_widget_editor'
        );
    }
});

/**
 * Add the widget content.
 */
function dashboard_widget_editor() {
    $tadv_admin_settings = get_option(OPTIONS_KEY);

    echo '<div style="display:flex; align-items:flex-start;">
              <div style="flex:1; margin-bottom:10px;">
                  <p>
                      L\'edició per blocs permet tenir un major control de com es mostra el contingut (maquetació), creant
                      estructures més riques i atractives. S\'incorporen blocs extra com ara carrusels de notícies, línies
                      de temps, comptadors enrere, etc.
                  </p>
                  <p>
                      <a href="https://projectes.xtec.cat/digital/serveis-digitals/nodes/editor-gutenberg/#compat-carrusels-pestanyes"
                         target="_blank">Compatibilitat amb l\'editor clàssic, els Carrusels i les Pestanyes</a>
                  </p>
              </div>
              <div style="width:100px; margin-left:10px;">
                  <img src="' . plugin_dir_url( __FILE__ ) . '/gutenberg-logo.svg" width="100" height="100"
                      alt="Gutenberg Logo"/>
              </div>
          </div>';

    // Check if the option is set.
    if (strpos($tadv_admin_settings['options'], TINYMCE_KEY) !== false) {
        echo '<a class="button button-primary" href=' . admin_url() . '?dashboard-editor-widget=activate>Activa</a>';
    } else {
        echo '<a class="button button-primary" href=' . admin_url() . '?dashboard-editor-widget=deactivate>Desactiva</a>';
    }

    echo '&nbsp;&nbsp;&nbsp;';
    echo '<a class="button button-secondary" href="https://projectes.xtec.cat/digital/serveis-digitals/nodes/editor-gutenberg/"
            target="_blank">Més informació</a>';
}

/**
 * Activate the editor.
 */
function editor_widget_activate() {
    $tadv_admin_settings = get_option(OPTIONS_KEY);

    // Remove the keyword that disables the editor
    $tadv_admin_settings['options'] = str_replace(TINYMCE_KEY, '', $tadv_admin_settings['options']);

    // Remove eventually resulting double commas
    $tadv_admin_settings['options'] = str_replace(',,', ',', $tadv_admin_settings['options']);

    // Remove eventually resulting trailing commas
    $tadv_admin_settings['options'] = trim($tadv_admin_settings['options'], ',');

    // Save to database
    update_option(OPTIONS_KEY, $tadv_admin_settings);
}

/**
 * Deactivate the editor.
 */
function editor_widget_deactivate() {
    $tadv_admin_settings = get_option(OPTIONS_KEY);

    // Add the keyword only or a comma and the keyword depending on if the string is empty or not
    if (empty($tadv_admin_settings['options'])) {
        $tadv_admin_settings['options'] = TINYMCE_KEY;
    } else {
        $tadv_admin_settings['options'] .= ',' . TINYMCE_KEY;
    }

    // Save to database
    update_option(OPTIONS_KEY, $tadv_admin_settings);
}

<?php

class W4G_Challenge_Admin {

    public function __construct() {
        // Hook into the admin menu
        add_action( 'admin_menu', array( $this, 'create_plugin_settings_page' ) );

        // Add Settings and Fields
        add_action( 'admin_init', array( $this, 'setup_sections' ) );
        add_action( 'admin_init', array( $this, 'setup_fields' ) );
    }

    public function create_plugin_settings_page() {
        // Add the menu item and page
        $page_title = 'Challenge Settings Page';
        $menu_title = 'Challenge';
        $capability = 'manage_options';
        $slug = 'w4g_challenge_fields';
        $callback = array( $this, 'plugin_settings_page_content' );
        $icon = 'dashicons-admin-plugins';
        $position = 100;

        add_options_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );
    }

    public function plugin_settings_page_content() {?>
        <div class="wrap">
            <h2>Challenge Settings</h2><?php
            if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ){
                  // $this->admin_notice();
            } ?>
            <form method="POST" action="options.php">
                <?php
                    settings_fields( 'w4g_challenge_fields' );
                    do_settings_sections( 'w4g_challenge_fields' );
                    submit_button();
                ?>
            </form>
        </div> <?php
    }
    
    public function admin_notice() { ?>
        <div class="notice notice-success is-dismissible">
            <p>Your settings have been updated!</p>
        </div><?php
    }

    public function setup_sections() {
        add_settings_section( 'our_second_section', 'Challenge Status', array( $this, 'section_callback' ), 'w4g_challenge_fields' );
        add_settings_section( 'our_first_section', 'Application Settings', array( $this, 'section_callback' ), 'w4g_challenge_fields' );
        add_settings_section( 'our_third_section', 'Application Page', array( $this, 'section_callback' ), 'w4g_challenge_fields' );
    }

    public function section_callback( $arguments ) {
        switch( $arguments['id'] ){
            case 'our_first_section':
                echo 'These settings are required before running your challenge.';
                break;
            case 'our_second_section':
                echo 'Control whether the challenge application is open or closed.';
                break;
            case 'our_third_section':
                echo 'Messaging for the Application Page.';
                break;
        }
    }

    public function setup_fields() {
        $fields = array(
            array(
                'uid' => 'challenge_status',
                'label' => 'Challenge Status',
                'section' => 'our_second_section',
                'type' => 'radio',
                'options' => array(
                    'inactive' => 'Pre-Challenge: <em>application is closed</em>',
                    'active' => 'Active: <em>application is open</em>',
                    'ended' => 'Post-Challenge: <em>application is closed</em>',
                ),
                'default' => array('inactive')
            ),
            array(
                'uid' => 'custom_fields_id',
                'label' => 'ACF Field Group ID',
                'section' => 'our_first_section',
                'type' => 'text',
                'placeholder' => 'ID',
                'helper' => '<a href="#">How to add fields to the Application</a>',
                'supplimental' => 'Add additional fields to the application.',
            ),
            array(
                'uid' => 'challenge_edit',
                'label' => 'Editing Capabilities',
                'section' => 'our_first_section',
                'type' => 'radio',
                'options' => array(
                    'allow' => 'Allow applicants to edit their own applications after submitting and while challenge is active',
                    'deny' => "Don't allow applicants to edit their own applications after submitting",
                ),
                'default' => array('allow')
            ),
            array(
                'uid' => 'challenge_information',
                'label' => 'Pre-Challenge Messaging',
                'section' => 'our_third_section',
                'type' => 'textarea',
                'placeholder' => '',
                'helper' => '',
                'supplimental' => 'Appears above the application while challenge status is Pre-Challenge',
            ),
            array(
                'uid' => 'challenge_instructions',
                'label' => 'Active Challenge Messaging - Logged In',
                'section' => 'our_third_section',
                'placeholder' => '',
                'type' => 'textarea',
                'supplimental' => 'Appears above the application to users who are logged in while challenge status is Active',
            ),
            array(
                'uid' => 'challenge_logged_out_message',
                'label' => 'Active Challenge Messaging - Logged Out',
                'section' => 'our_third_section',
                'placeholder' => '',
                'type' => 'textarea',
                'supplimental' => 'Appears above the application to users who are not logged in while challenge status is Active'
            ),
            array(
                'uid' => 'challenge_ended_message',
                'label' => 'Post-Challenge Messaging',
                'section' => 'our_third_section',
                'placeholder' => '',
                'type' => 'textarea',
                'supplimental' => 'Appears above the application while challenge status is Pre-Challenge',
            ),
        );
        foreach( $fields as $field ){

            add_settings_field( $field['uid'], $field['label'], array( $this, 'field_callback' ), 'w4g_challenge_fields', $field['section'], $field );
            register_setting( 'w4g_challenge_fields', $field['uid'] );
        }
    }

    public function field_callback( $arguments ) {

        $default = isset( $arguments['default'] ) ? $arguments['default'] : '';
        $value = get_option( $arguments['uid'], $default );

        switch( $arguments['type'] ){
            case 'text':
            case 'password':
            case 'number':
                printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
                break;
            case 'textarea':
                printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>', $arguments['uid'], $arguments['placeholder'], $value );
                break;
            case 'select':
            case 'multiselect':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ) {

                    $attributes = '';
                    $options_markup = '';

                    if( !is_array( $value ) ) {
                        $value = array( $value );
                    }
                    
                    foreach( $arguments['options'] as $key => $label ) {

                        $selected = '';
                        if( in_array( $key, $value ) ) {
                            $selected = "selected='selected'";
                        }

                        $options_markup .= sprintf( '<option value="%s" %s>%s</option>', $key, $selected, $label );
                    }

                    if( $arguments['type'] === 'multiselect' ){
                        $attributes = ' multiple="multiple" ';
                    }
                    printf( '<select name="%1$s[]" id="%1$s" %2$s>%3$s</select>', $arguments['uid'], $attributes, $options_markup );
                }
                break;
            case 'radio':
            case 'checkbox':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ) {

                    $options_markup = '';
                    $iterator = 0;

                    if( !is_array( $value ) ) {
                        $value = array( $value );
                    }
                    
                    foreach( $arguments['options'] as $key => $label ) {

                        $checked = '';
                        if( in_array( $key, $value ) ) {
                            $checked = "checked='checked'";
                        }

                        $iterator++;
                        $options_markup .= sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>', $arguments['uid'], $arguments['type'], $key, $checked, $label, $iterator );
                    }
                    printf( '<fieldset>%s</fieldset>', $options_markup );
                }
                break;
        }

        if( isset( $arguments['helper'] ) && !empty( $arguments['helper'] ) ) {
            printf( '<span class="helper"> %s</span>', $arguments['helper'] );
        }

        if( isset( $arguments['supplimental'] ) && !empty( $arguments['supplimental'] ) ){
            printf( '<p class="description">%s</p>',  $arguments['supplimental'] );
        }
    }

}
new W4G_Challenge_Admin();
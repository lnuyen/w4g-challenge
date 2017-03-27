<?php 
/**
 * Load ACF Group data that's required for the setting page and the 'challenge_application' Custom Post Type.
 */

if( function_exists('register_field_group') ):

	register_field_group(array (
		'id' => 'acf_application',
		'title' => 'Application',
		'fields' => array (
			array (
				'key' => 'field_58d15fcc4a4f0',
				'label' => 'Project Name',
				'name' => 'project_name',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'challenge_app',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));

endif;

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_1',
	'title' => 'My Group',
	'fields' => array (),
	'location' => array (
		array (
			array (
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'post',
			),
		),
	),
));

acf_add_local_field(array(
	'key' => 'field_1',
	'label' => 'Sub Title',
	'name' => 'sub_title',
	'type' => 'text',
	'parent' => 'group_1'
));

endif;
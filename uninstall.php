<?php

if( !defined( 'WP_UNINSTALL_PLUGIN' ) ){

   exit (); } 

do_action('cw_verifi_uninstall');

delete_option('cw_verifi_options' );


?>
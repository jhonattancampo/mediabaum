<?php
if ( !defined( 'ABSPATH' ) ) { $IXAP413 = 'wp-load.php'; $IXAP414 = 100; while ( !file_exists( $IXAP413 ) && ( $IXAP414 > 0 ) ) { $IXAP413 = '../' . $IXAP413; $IXAP414--; } if ( file_exists( $IXAP413 ) ) { require_once $IXAP413; } } if ( defined( 'ABSPATH' ) ) { if ( !current_user_can( AFFILIATES_ACCESS_AFFILIATES ) ) { wp_die( __( 'Access denied.', AFFILIATES_PRO_PLUGIN_DOMAIN ) ); } else { if ( isset ( $_GET['action'] ) ) { global $wpdb, $affiliates_db; switch( $_GET['action'] ) { case 'generate_mass_payment_file' : if ( isset( $_GET['service'] ) ) { $IXAP30 = array( 'tables' => array( 'referrals' => $affiliates_db->get_tablename( 'referrals' ), 'affiliates' => $affiliates_db->get_tablename( 'affiliates' ), 'affiliates_users' => $affiliates_db->get_tablename( 'affiliates_users' ), 'users' => $wpdb->users, ) ); $IXAP30 = array_merge( $_GET, $IXAP30 ); Affiliates_Totals::get_mass_payment_file( $_GET['service'], $IXAP30, get_option( 'blog_charset' ) ); die; } break; } } } }
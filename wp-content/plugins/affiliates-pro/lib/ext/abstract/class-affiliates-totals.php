<?php
	
/**
 * Copyright 2011 "kento" Karim Rahimpur - www.itthinx.com
 * 
 * This code is provided subject to the license granted.
 *
 * UNAUTHORIZED USE AND DISTRIBUTION IS PROHIBITED.
 *
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * 
 * This header and all notices must be kept intact.
 */

	
abstract class Affiliates_Totals implements I_Affiliates_Totals { public static function get_mass_payment_file( $IXAP29 = 'paypal', $IXAP30 = null, $IXAP7 = null ) { global $affiliates_db; $IXAP31 = isset( $IXAP30['from_date'] ) ? $IXAP30['from_date'] : null; $IXAP32 = $IXAP31 ? DateHelper::u2s( $IXAP31 ) : null; $IXAP33 = isset( $IXAP30['thru_date'] ) ? $IXAP30['thru_date'] : null; $IXAP34 = $IXAP33 ? DateHelper::u2s( $IXAP33, 24*3600 ) : null; $IXAP35 = isset( $IXAP30['minimum_total'] ) ? bcadd( "0", $IXAP30['minimum_total'], AFFILIATES_REFERRAL_AMOUNT_DECIMALS ) : null; $IXAP36 = isset( $IXAP30['referral_status'] ) ? Affiliates_Utility::verify_referral_status_transition( $IXAP30['referral_status'], $IXAP30['referral_status'] ) : null; $IXAP37 = isset( $IXAP30['currency_id'] ) ? Affiliates_Utility::verify_currency_id( $IXAP30['currency_id'] ) : null; $IXAP38 = isset( $IXAP30['affiliate_id'] ) ? affiliates_check_affiliate_id( $IXAP30['affiliate_id'] ) : null; $IXAP39 = isset( $IXAP30['affiliate_name'] ) ? $IXAP30['affiliate_name'] : null; $IXAP40 = isset( $IXAP30['affiliate_user_login'] ) ? $IXAP30['affiliate_user_login'] : null; $IXAP41 = isset( $IXAP30['orderby'] ) ? $IXAP30['orderby'] : null; $IXAP42 = isset( $IXAP30['order'] ) ? $IXAP30['order'] : null; switch ( $IXAP41 ) { case 'affiliate_id' : case 'name' : case 'user_login' : case 'email' : case 'total' : case 'currency_id' : break; default: $IXAP41 = 'name'; } switch ( $IXAP42 ) { case 'asc' : case 'ASC' : case 'desc' : case 'DESC' : break; default: $IXAP42 = 'ASC'; } if ( isset( $IXAP30['tables'] ) ) { $IXAP43 = $IXAP30['tables']['affiliates']; $IXAP44 = $IXAP30['tables']['affiliates_users']; $IXAP45 = $IXAP30['tables']['referrals']; $IXAP46 = $IXAP30['tables']['users']; $IXAP47 = array( " 1=%d " ); $IXAP48 = array( 1 ); if ( $IXAP38 ) { $IXAP47[] = " a.affiliate_id = %d "; $IXAP48[] = $IXAP38; } if ( $IXAP39 ) { $IXAP47[] = " a.name LIKE '%%%s%%' "; $IXAP48[] = $IXAP39; } if ( $IXAP40 ) { $IXAP47[] = " u.user_login LIKE '%%%s%%' "; $IXAP48[] = $IXAP40; } if ( $IXAP32 && $IXAP34 ) { $IXAP47[] = " r.datetime >= %s AND r.datetime < %s "; $IXAP48[] = $IXAP32; $IXAP48[] = $IXAP34; } else if ( $IXAP32 ) { $IXAP47[] = " r.datetime >= %s "; $IXAP48[] = $IXAP32; } else if ( $IXAP34 ) { $IXAP47[] = " r.datetime < %s "; $IXAP48[] = $IXAP34; } if ( $IXAP36 ) { $IXAP47[] = " r.status = %s "; $IXAP48[] = $IXAP36; } if ( $IXAP37 ) { $IXAP47[] = " r.currency_id = %s "; $IXAP48[] = $IXAP37; } if ( !empty( $IXAP47 ) ) { $IXAP47 = " WHERE " . implode( " AND ", $IXAP47 ); } else { $IXAP47 = ''; } $IXAP49 = ''; if ( $IXAP35 ) { $IXAP49 .= " HAVING SUM(r.amount) >= %s "; $IXAP48[] = $IXAP35; } $IXAP50 = ''; if ( $IXAP41 && $IXAP42 ) { $IXAP50 .= " ORDER BY $IXAP41 $IXAP42 "; } $IXAP51 = $affiliates_db->get_objects( "
				SELECT a.*, u.user_login, SUM(r.amount) as total, r.currency_id
				FROM $IXAP45 r
				LEFT JOIN $IXAP43 a ON r.affiliate_id = a.affiliate_id
				LEFT JOIN $IXAP44 au ON a.affiliate_id = au.affiliate_id
				LEFT JOIN $IXAP46 u on au.user_id = u.ID
				$IXAP47
				GROUP BY r.affiliate_id, r.currency_id
				$IXAP49
				$IXAP50
				", $IXAP48 ); $IXAP29 = strtolower( $IXAP29 ); if ( !headers_sent() ) { switch ( $IXAP29 ) { case 'paypal' : $IXAP52 = date( 'Y-m-d-H-i-s', time() ); header( 'Content-Description: File Transfer' ); if ( !empty( $IXAP7 ) ) { header( 'Content-Type: text/plain; charset=' . $IXAP7 ); } else { header( 'Content-Type: text/plain' ); } header( "Content-Disposition: attachment; filename=\"affiliates-mass-payment-$IXAP52.txt\"" ); foreach( $IXAP51 as $IXAP15 ) { $IXAP53 = Affiliates_Affiliate::get_attribute( $IXAP15->affiliate_id, Affiliates_Attributes::IXAP54 ); $IXAP27 = !empty( $IXAP53 ) ? $IXAP53 : $IXAP15->email; $IXAP23 = $IXAP15->total; $IXAP37 = $IXAP15->currency_id; $IXAP38 = $IXAP15->affiliate_id; $IXAP55 = "Affiliate payment"; if ( !empty( $IXAP27 ) && !empty( $IXAP23 ) && !empty( $IXAP37 ) ) { echo "$IXAP27\t$IXAP23\t$IXAP37\t$IXAP38\t$IXAP55\n"; } } echo "\n"; break; case 'export' : $IXAP52 = date( 'Y-m-d-H-i-s', time() ); header( 'Content-Description: File Transfer' ); if ( !empty( $IXAP7 ) ) { header( 'Content-Type: text/plain; charset=' . $IXAP7 ); } else { header( 'Content-Type: text/plain' ); } header( "Content-Disposition: attachment; filename=\"affiliates-totals-export-$IXAP52.txt\"" ); echo __( 'Id', AFFILIATES_PRO_PLUGIN_DOMAIN ); echo "\t"; echo __( 'Affiliate', AFFILIATES_PRO_PLUGIN_DOMAIN ); echo "\t"; echo __( 'Email', AFFILIATES_PRO_PLUGIN_DOMAIN ); echo "\t"; echo __( 'Username', AFFILIATES_PRO_PLUGIN_DOMAIN ); echo "\t"; echo __( 'Total', AFFILIATES_PRO_PLUGIN_DOMAIN ); echo "\t"; echo __( 'Currency', AFFILIATES_PRO_PLUGIN_DOMAIN ); echo "\n"; foreach( $IXAP51 as $IXAP15 ) { $IXAP38 = $IXAP15->affiliate_id; $name = stripslashes( $IXAP15->name ); $IXAP27 = $IXAP15->email; $IXAP56 = stripslashes( $IXAP15->user_login ); $IXAP23 = $IXAP15->total; $IXAP37 = $IXAP15->currency_id; echo "$IXAP38\t$name\t$IXAP27\t$IXAP56\t$IXAP23\t$IXAP37\n"; } echo "\n"; } } else { wp_die( 'ERROR: headers already sent' ); } } } public static function update_status( $IXAP57, $IXAP30 = null ) { global $affiliates_db; $IXAP58 = ""; $IXAP31 = isset( $IXAP30['from_date'] ) ? $IXAP30['from_date'] : null; $IXAP32 = $IXAP31 ? DateHelper::u2s( $IXAP31 ) : null; $IXAP33 = isset( $IXAP30['thru_date'] ) ? $IXAP30['thru_date'] : null; $IXAP34 = $IXAP33 ? DateHelper::u2s( $IXAP33, 24*3600 ) : null; $IXAP35 = isset( $IXAP30['minimum_total'] ) ? bcadd( "0", $IXAP30['minimum_total'], AFFILIATES_REFERRAL_AMOUNT_DECIMALS ) : null; $IXAP36 = isset( $IXAP30['referral_status'] ) ? Affiliates_Utility::verify_referral_status_transition( $IXAP30['referral_status'], $IXAP30['referral_status'] ) : null; $IXAP37 = isset( $IXAP30['currency_id'] ) ? Affiliates_Utility::verify_currency_id( $IXAP30['currency_id'] ) : null; $IXAP38 = isset( $IXAP30['affiliate_id'] ) ? affiliates_check_affiliate_id( $IXAP30['affiliate_id'] ) : null; $IXAP39 = isset( $IXAP30['affiliate_name'] ) ? $IXAP30['affiliate_name'] : null; $IXAP40 = isset( $IXAP30['affiliate_user_login'] ) ? $IXAP30['affiliate_user_login'] : null; $IXAP41 = isset( $IXAP30['orderby'] ) ? $IXAP30['orderby'] : null; $IXAP42 = isset( $IXAP30['order'] ) ? $IXAP30['order'] : null; switch ( $IXAP41 ) { case 'affiliate_id' : case 'name' : case 'email' : $IXAP41 = 'a.' . $IXAP41; break; case 'user_login' : $IXAP41 = 'au.' . $IXAP41; break; case 'currency_id' : $IXAP41 = 'r.' . $IXAP41; break; default: $IXAP41 = 'a.name'; } switch ( $IXAP42 ) { case 'asc' : case 'ASC' : case 'desc' : case 'DESC' : break; default: $IXAP42 = 'ASC'; } if ( isset( $IXAP30['tables'] ) ) { $IXAP58 .= "<h1>" . __( "Closing referrals", AFFILIATES_PRO_PLUGIN_DOMAIN ) . "</h1>"; $IXAP58 .= "<div class='closing-referrals-overview'>"; $IXAP43 = $IXAP30['tables']['affiliates']; $IXAP44 = $IXAP30['tables']['affiliates_users']; $IXAP45 = $IXAP30['tables']['referrals']; $IXAP46 = $IXAP30['tables']['users']; $IXAP47 = array( " 1=%d " ); $IXAP48 = array( 1 ); if ( $IXAP38 ) { $IXAP47[] = " a.affiliate_id = %d "; $IXAP48[] = $IXAP38; } if ( $IXAP39 ) { $IXAP47[] = " a.name LIKE '%%%s%%' "; $IXAP48[] = $IXAP39; } if ( $IXAP40 ) { $IXAP47[] = " u.user_login LIKE '%%%s%%' "; $IXAP48[] = $IXAP40; } if ( $IXAP32 && $IXAP34 ) { $IXAP47[] = " r.datetime >= %s AND r.datetime < %s "; $IXAP48[] = $IXAP32; $IXAP48[] = $IXAP34; } else if ( $IXAP32 ) { $IXAP47[] = " r.datetime >= %s "; $IXAP48[] = $IXAP32; } else if ( $IXAP34 ) { $IXAP47[] = " r.datetime < %s "; $IXAP48[] = $IXAP34; } if ( $IXAP36 ) { $IXAP47[] = " r.status = %s "; $IXAP48[] = $IXAP36; } if ( $IXAP37 ) { $IXAP47[] = " r.currency_id = %s "; $IXAP48[] = $IXAP37; } if ( $IXAP35 ) { $IXAP59 = $IXAP47; if ( !empty( $IXAP59 ) ) { $IXAP59 = " WHERE " . implode( " AND ", $IXAP47 ); } else { $IXAP59 = ''; } $IXAP60 = $IXAP48; $IXAP61 = " HAVING SUM(r.amount) >= %s "; $IXAP60[] = $IXAP35; $IXAP47[] = " (a.affiliate_id, r.currency_id) IN
					(
					SELECT r.affiliate_id, r.currency_id
					FROM $IXAP45 r
					LEFT JOIN $IXAP43 a ON r.affiliate_id = a.affiliate_id
					LEFT JOIN $IXAP44 au ON a.affiliate_id = au.affiliate_id
					LEFT JOIN $IXAP46 u on au.user_id = u.ID
					$IXAP59
					GROUP BY r.affiliate_id, r.currency_id
					$IXAP61
					)
					"; foreach( $IXAP60 as $IXAP62 ) { array_push( $IXAP48, $IXAP62 ); } $IXAP47[] = " r.amount IS NOT NULL "; } if ( !empty( $IXAP47 ) ) { $IXAP47 = " WHERE " . implode( " AND ", $IXAP47 ); } else { $IXAP47 = ''; } $IXAP50 = ''; if ( $IXAP41 && $IXAP42 ) { $IXAP50 .= " ORDER BY $IXAP41 $IXAP42 "; } $IXAP63 = isset( $IXAP30['step'] ) ? intval( $IXAP30['step'] ) : 1; switch ( $IXAP63 ) { case 1 : $IXAP51 = $affiliates_db->get_objects( "
						SELECT a.*, r.*, u.user_login
						FROM $IXAP45 r
						LEFT JOIN $IXAP43 a ON r.affiliate_id = a.affiliate_id
						LEFT JOIN $IXAP44 au ON a.affiliate_id = au.affiliate_id
						LEFT JOIN $IXAP46 u on au.user_id = u.ID
						$IXAP47
						$IXAP50
						", $IXAP48 ); $IXAP58 .= "<div class='manage'>"; $IXAP58 .= "<div class='warning'>"; $IXAP58 .= "<p>"; $IXAP58 .= "<strong>"; $IXAP58 .= __( "Please review the list of referrals that will be <em>closed</em>.", AFFILIATES_PRO_PLUGIN_DOMAIN ); $IXAP58 .= "</strong>"; $IXAP58 .= "</p>"; $IXAP58 .= "</div>"; $IXAP58 .= "<p>"; $IXAP58 .= __( "Usually only referrals that are <em>accepted</em> and have been paid out should be <em>closed</em>. If there are unwanted or too many referrals shown, restrict your filter settings.", AFFILIATES_PRO_PLUGIN_DOMAIN ); $IXAP58 .= "</p>"; $IXAP58 .= "<p>"; $IXAP58 .= __( "If these referrals can be closed, click the confirmation button below.", AFFILIATES_PRO_PLUGIN_DOMAIN ); $IXAP58 .= "</p>"; $IXAP58 .= "</div>"; $IXAP58 .= '<div id="referrals-overview" class="referrals-overview">'; $IXAP58 .= self::render_results( $IXAP51 ); $IXAP58 .= '</div>'; if ( count( $IXAP51 > 0 ) ) { $IXAP64 = ""; if ( !empty( $IXAP31 ) ) { $IXAP64 .= "&from_date=" . urlencode( $IXAP31 ); } if ( !empty( $IXAP33 ) ) { $IXAP64 .= "&thru_date=" . urlencode( $IXAP33 ); } if ( !empty( $IXAP35 ) ) { $IXAP64 .= "&minimum_total=" . urlencode( $IXAP35 ); } if ( !empty( $IXAP36 ) ) { $IXAP64 .= "&referral_status=" . urlencode( $IXAP36 ); } if ( !empty( $IXAP37 ) ) { $IXAP64 .= "&currency_id=" . urlencode( $IXAP37 ); } if ( !empty( $IXAP38 ) ) { $IXAP64 .= "&affiliate_id=" . urlencode( $IXAP38 ); } if ( !empty( $IXAP39 ) ) { $IXAP64 .= "&affiliate_name=" . urlencode( $IXAP39 ); } if ( !empty( $IXAP40 ) ) { $IXAP64 .= "&affiliate_user_login=" . urlencode( $IXAP40 ); } if ( !empty( $IXAP41 ) ) { $IXAP64 .= "&orderby=" . urlencode( $IXAP41 ); } if ( !empty( $IXAP42 ) ) { $IXAP64 .= "&order=" . urlencode( $IXAP42 ); } $IXAP65 = esc_url( AFFILIATES_PRO_PLUGIN_URL . 'lib/ext/includes/generate-mass-payment-file.php' ); $IXAP58 .= '<div class="manage confirm">'; $IXAP66 = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; $IXAP66 = remove_query_arg( 'paged', $IXAP66 ); $IXAP66 = remove_query_arg( 'action', $IXAP66 ); $IXAP66 = remove_query_arg( 'affiliate_id', $IXAP66 ); $IXAP58 .= "<p>"; $IXAP58 .= __( "Close these referrals by clicking:", AFFILIATES_PRO_PLUGIN_DOMAIN ); $IXAP58 .= "</p>"; $IXAP58 .= "<a title='" . __( 'Click to close these referrals', AFFILIATES_PRO_PLUGIN_DOMAIN ) . "' " . "class='close-referrals button' " . "href='" . esc_url( $IXAP66 ) . "&action=close_referrals&step=2" . $IXAP64 . "'>" . "<img class='icon' alt='" . __( 'Close referrals', AFFILIATES_PRO_PLUGIN_DOMAIN) . "' src='". AFFILIATES_PRO_PLUGIN_URL ."images/closed.png'/>" . "<span class='label'>" . __( 'Close Referrals', AFFILIATES_PRO_PLUGIN_DOMAIN) . "</span>" . "</a>"; $IXAP58 .= "<div class='warning'>"; $IXAP58 .= "<p>"; $IXAP58 .= "<strong>"; $IXAP58 .= __( "This action can not be undone*.", AFFILIATES_PRO_PLUGIN_DOMAIN ); $IXAP58 .= "</strong>"; $IXAP58 .= "</p>"; $IXAP58 .= "<p>"; $IXAP58 .= "<span style='font-size:0.8em;'>"; $IXAP58 .= __( "*To undo, each referral would have to be set to the desired status individually.", AFFILIATES_PRO_PLUGIN_DOMAIN ); $IXAP58 .= "</span>"; $IXAP58 .= "</p>"; $IXAP58 .= "</div>"; $IXAP58 .= '</div>'; } break; case 2 : $IXAP51 = $affiliates_db->get_objects( "
						SELECT a.*, r.*, u.user_login
						FROM $IXAP45 r
						LEFT JOIN $IXAP43 a ON r.affiliate_id = a.affiliate_id
						LEFT JOIN $IXAP44 au ON a.affiliate_id = au.affiliate_id
						LEFT JOIN $IXAP46 u on au.user_id = u.ID
						$IXAP47
						$IXAP50
						", $IXAP48 ); $IXAP67 = array(); $IXAP68 = array(); $IXAP69 = array(); foreach ( $IXAP51 as $IXAP15 ) { if ( $IXAP70 = Affiliates_Utility::verify_referral_status_transition( $IXAP15->status, $IXAP57 ) ) { if ( $affiliates_db->query( "UPDATE $IXAP45 SET status = %s WHERE affiliate_id = %d AND post_id = %d AND datetime = %s ", $IXAP70, $IXAP15->affiliate_id, $IXAP15->post_id, $IXAP15->datetime ) ) { $IXAP15->status = $IXAP70; $IXAP67[] = $IXAP15; } else { $IXAP69[] = $IXAP15; } } else { $IXAP68[] = $IXAP15; } } $IXAP71 = array( AFFILIATES_REFERRAL_STATUS_ACCEPTED => __( 'Accepted', AFFILIATES_PLUGIN_DOMAIN ), AFFILIATES_REFERRAL_STATUS_CLOSED => __( 'Closed', AFFILIATES_PLUGIN_DOMAIN ), AFFILIATES_REFERRAL_STATUS_PENDING => __( 'Pending', AFFILIATES_PLUGIN_DOMAIN ), AFFILIATES_REFERRAL_STATUS_REJECTED => __( 'Rejected', AFFILIATES_PLUGIN_DOMAIN ), ); $IXAP58 .= "<h2>" . __( "Updated", AFFILIATES_PRO_PLUGIN_DOMAIN ) . "</h2>"; $IXAP58 .= "<p>"; $IXAP58 .= sprintf( __( "These referrals have been updated to <em>%s</em>.", AFFILIATES_PRO_PLUGIN_DOMAIN ), ( isset( $IXAP71[$IXAP57] ) ? $IXAP71[$IXAP57] : $IXAP57 ) ); $IXAP58 .= "</p>"; $IXAP58 .= self::render_results( $IXAP67 ); if ( count( $IXAP68 ) > 0 ) { $IXAP58 .= "<h2>" . __( "Omitted", AFFILIATES_PRO_PLUGIN_DOMAIN ) . "</h2>"; $IXAP58 .= "<p>"; $IXAP58 .= sprintf( __( "These referrals have been omitted because their status must not be changed to <em>%s</em>.", AFFILIATES_PRO_PLUGIN_DOMAIN ), ( isset( $IXAP71[$IXAP57] ) ? $IXAP71[$IXAP57] : $IXAP57 ) ); $IXAP58 .= "</p>"; $IXAP58 .= self::render_results( $IXAP68 ); } if ( count( $IXAP69 ) > 0 ) { $IXAP58 .= "<h2>" . __( "Failed", AFFILIATES_PRO_PLUGIN_DOMAIN ) . "</h2>"; $IXAP58 .= "<p>"; $IXAP58 .= sprintf( __( "These referrals could not be updated to <em>%s</em>.", AFFILIATES_PRO_PLUGIN_DOMAIN ), ( isset( $IXAP71[$IXAP57] ) ? $IXAP71[$IXAP57] : $IXAP57 ) ); $IXAP58 .= "</p>"; $IXAP58 .= self::render_results( $IXAP69 ); } break; } $IXAP58 .= "</div>"; } return $IXAP58; } public static function render_results( $IXAP51 ) { $IXAP58 = ""; $IXAP72 = array( 'datetime' => __( 'Date', AFFILIATES_PLUGIN_DOMAIN ), 'post_title' => __( 'Post', AFFILIATES_PLUGIN_DOMAIN ), 'name' => __( 'Affiliate', AFFILIATES_PLUGIN_DOMAIN ), 'amount' => __( 'Amount', AFFILIATES_PLUGIN_DOMAIN ), 'currency_id' => __( 'Currency', AFFILIATES_PLUGIN_DOMAIN ), 'status' => __( 'Status', AFFILIATES_PLUGIN_DOMAIN ) ); $IXAP71 = array( AFFILIATES_REFERRAL_STATUS_ACCEPTED => __( 'Accepted', AFFILIATES_PLUGIN_DOMAIN ), AFFILIATES_REFERRAL_STATUS_CLOSED => __( 'Closed', AFFILIATES_PLUGIN_DOMAIN ), AFFILIATES_REFERRAL_STATUS_PENDING => __( 'Pending', AFFILIATES_PLUGIN_DOMAIN ), AFFILIATES_REFERRAL_STATUS_REJECTED => __( 'Rejected', AFFILIATES_PLUGIN_DOMAIN ), ); $IXAP73 = array( AFFILIATES_REFERRAL_STATUS_ACCEPTED => "<img class='icon' alt='" . __( 'Accepted', AFFILIATES_PRO_PLUGIN_DOMAIN) . "' src='" . AFFILIATES_PRO_PLUGIN_URL . "images/accepted.png'/>", AFFILIATES_REFERRAL_STATUS_CLOSED => "<img class='icon' alt='" . __( 'Closed', AFFILIATES_PRO_PLUGIN_DOMAIN) . "' src='" . AFFILIATES_PRO_PLUGIN_URL . "images/closed.png'/>", AFFILIATES_REFERRAL_STATUS_PENDING => "<img class='icon' alt='" . __( 'Pending', AFFILIATES_PRO_PLUGIN_DOMAIN) . "' src='" . AFFILIATES_PRO_PLUGIN_URL . "images/pending.png'/>", AFFILIATES_REFERRAL_STATUS_REJECTED => "<img class='icon' alt='" . __( 'Rejected', AFFILIATES_PRO_PLUGIN_DOMAIN) . "' src='" . AFFILIATES_PRO_PLUGIN_URL . "images/rejected.png'/>", ); $IXAP58 .= '<table id="referrals" class="referrals wp-list-table widefat fixed" cellspacing="0">'; $IXAP58 .= "<thead>"; $IXAP58 .= "<tr>"; foreach ( $IXAP72 as $IXAP74 => $IXAP75 ) { $IXAP58 .= "<th scope='col'>$IXAP75</th>"; } $IXAP58 .= "</tr>"; $IXAP58 .= "</thead>"; $IXAP58 .= "<tbody>"; if ( count( $IXAP51 ) > 0 ) { for ( $IXAP76 = 0; $IXAP76 < count( $IXAP51 ); $IXAP76++ ) { $IXAP15 = $IXAP51[$IXAP76]; $IXAP58 .= '<tr class="details-referrals ' . ( $IXAP76 % 2 == 0 ? 'even' : 'odd' ) . '">'; $IXAP58 .= '<td class="datetime">' . DateHelper::s2u( $IXAP15->datetime ) . '</td>'; $IXAP77 = get_the_title( $IXAP15->post_id ); $IXAP58 .= '<td class="post_title">' . wp_filter_nohtml_kses( $IXAP77 ) . '</td>'; $IXAP58 .= "<td class='name'>" . stripslashes( wp_filter_nohtml_kses( $IXAP15->name ) ) . "</td>"; $IXAP58 .= "<td class='amount'>" . stripslashes( wp_filter_nohtml_kses( $IXAP15->amount ) ) . "</td>"; $IXAP58 .= "<td class='currency_id'>" . stripslashes( wp_filter_nohtml_kses( $IXAP15->currency_id ) ) . "</td>"; $IXAP58 .= "<td class='status'>"; $IXAP58 .= isset( $IXAP73[$IXAP15->status] ) ? $IXAP73[$IXAP15->status] : ''; $IXAP58 .= isset( $IXAP71[$IXAP15->status] ) ? $IXAP71[$IXAP15->status] : ''; $IXAP58 .= "</td>"; $IXAP58 .= '</tr>'; } } else { $IXAP58 .= '<tr><td colspan="' . count( $IXAP72 ) . '">' . __('There are no results.', AFFILIATES_PLUGIN_DOMAIN ) . '</td></tr>'; } $IXAP58 .= '</tbody>'; $IXAP58 .= '</table>'; return $IXAP58; } }
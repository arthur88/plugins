<?php
/*
Plugin Name: Log and check users ip
PLugin URI: http://www.webai.lt
Description: It`s a plugin that checks users ip and dissalow them if user is loggen rom ore then 2 different ip addresses
Version: 0.0.1
Author: Artūras Z.
Author URI: http://www.webai.lt
 */

defined( 'ABSPATH' ) or die( 'No direct access, please' );



function create_table(){
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $wpdb->prefix . 'users_logs';

	if($wpdb->get_var("show tables like '$table_name'") != $table_name)
	{
		$sql = "CREATE TABLE " . $table_name . " (
			`id` mediumint(11) NOT NULL AUTO_INCREMENT,
			`user_id` int(11) NOT NULL,
			`timed` int(11) NOT NULL,
			`user_ip` varchar(16) NOT NULL,
			UNIQUE KEY id (id)
			) $charset_collate;";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);
}
}

add_filter('wp_authenticate', 'check_login',10,2);


function check_login ($username, $password) {
	global $wpdb;
	$username = sanitize_user($username);
	$password = trim($password);

	$table = $wpdb->prefix . 'users_logs';
	$table_users = $wpdb->prefix . 'users';
	$uIP = esc_textarea(auto_reverse_proxy_user_ip());

	$user_id = $wpdb->get_results("SELECT ID FROM ".$table_users." WHERE user_login = '$username'");

	/**
	 * [$ch_u_log count different ussers diffs ip`s]
	 * @var [int]
	 */
	$ch_u_log= $wpdb->get_results("SELECT COUNT(user_id) as usr_log_qty FROM $table WHERE user_id = '".$user_id[0]->ID."' AND user_ip = '".$uIP."' ");
	$u_qty_times = $ch_u_log[0]->usr_log_qty;

	if($u_qty_times > 0){ //if user with same ip ADDRESS WAS logged before


		$wpdb->update($table,
			array('timed' => strtotime(date("Y-m-d H:i:s"))),
				array( 'id' => $user_id[0]->ID ),
					array('%s'),
						array( '%d' ));



	} else { // if user with the ip adress was not logged before
		$uqty2= $wpdb->get_results("SELECT COUNT(user_id) as uID FROM $table WHERE user_id = '".$user_id[0]->ID."' ");
		$uID = $uqty2[0]->uID;

		if(($uID < 3) && (!current_user_can('manage_options'))) { //if users total logged ip adresses was less or equal 3 and user is not admin

			if(($user_id[0]->ID) ){
			$wpdb->insert($table, array(
				'user_id' => $user_id[0]->ID,
				'timed' => strtotime(date("Y-m-d H:i:s")),
				'user_ip' => $uIP
			), array('%s', '%d', '%s'));
			}
			else {}

		}
		else { //if user with different ip adress is more

			//Create an error to return to user
         		$user = new WP_Error( 'denied', __('error'));
         		remove_action('authenticate', 'wp_authenticate_username_password', 20);
         		custom_authenticate($user, $username, $password);
				add_filter('authenticate','custom_authenticate', 31, 3);

		}
	}




}

/**
 * [auto_reverse_proxy_user_ip get user ip by checking proxy]
 * @return [type] [description]
 */
function auto_reverse_proxy_user_ip(){
	$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
	if (!empty($_SERVER['X_FORWARDED_FOR'])) {
		$X_FORWARDED_FOR = explode(',', $_SERVER['X_FORWARDED_FOR']);
		if (!empty($X_FORWARDED_FOR)) {
			$REMOTE_ADDR = trim($X_FORWARDED_FOR[0]);
		}
	}

	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$HTTP_X_FORWARDED_FOR= explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
		if (!empty($HTTP_X_FORWARDED_FOR)) {
			$REMOTE_ADDR = trim($HTTP_X_FORWARDED_FOR[0]);
		}
	}
	return preg_replace('/[^0-9a-f:\., ]/si', '', $REMOTE_ADDR);
}


register_activation_hook(__FILE__,'create_table');
register_deactivation_hook(__FILE__,'drop_table');

/** drop table on deativation  */
function drop_table(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'users_logs';
	$wpdb->query("DROP TABLE IF EXISTS $table_name");
}

/**
 * [custom_authenticate return error on fail]
 * @param  [arr] $user     [wp]
 * @param  [arr] $username [wp]
 * @param  [arr] $password [wp]
 * @return [error]           [wp]
 */
function custom_authenticate($user, $username, $password) {
	return new WP_Error( 'denied', __('<strong>ERROR</strong>: You cant login more then 2 differents IP addresses.') );
}

add_action('admin_menu', 'userr_ip_list');

function userr_ip_list(){
    add_menu_page( 'Users IP', 'Users IP', 'manage_options', 'users_ip', 'show_users' );
}

function show_users(){
	global $wpdb;
	$lg = langs();
	$table = $wpdb->prefix . 'users_logs';
	$table_users = $wpdb->prefix . 'users';
	echo '<div class="wrap">';
	echo "<table class='widefat fixed striped' cellspacing='0'>";
	echo "<form action='".esc_url($_SERVER['REQUEST_URI'])."' method='POST' >";
	echo "<thead><tr>";
	echo "<th class='manage-column column-columnname' scope='col'>".$lg['s_by_u']."</th>";
	echo "<th class='manage-column column-columnname' scope='col'>".$lg['s_by_ip']."</th></tr>
	</thead><tbody><tr >";
	echo "<td class='column-columnname'><input type='text' name='s_by_u' value='".(!empty($_POST['s_by_u']) ? sanitize_text_field($_POST['s_by_u']) : '')."' placeholder='".$lg['enter_fixed_u']."'></td>";
	echo "<td class='column-columnname'><input type='text' name='s_by_ip' value='".(!empty($_POST['s_by_ip']) ? sanitize_text_field($_POST['s_by_ip']) : '')."' placeholder='".$lg['enter_some_ip']."' ></td>";
	echo "<td><input type='submit' value=".$lg['search']." name='search_by' class='button-primary' /></td>";
	echo "</tr></tbody></form>";
	echo "</table><br/><br/>";

	echo "<table class='widefat fixed striped striped' cellspacing='0'>";

	echo "<thead>";
		echo "<tr>
			 <th class='manage-column column-columnname' scope='col' width='40px'>".$lg['no']."</th>
			 <th class='manage-column column-columnname' scope='col'>".$lg['usr']."</th>";
		echo "<th class='manage-column column-columnname' scope='col'>".$lg['timed']."</th>";
		echo "<th class='manage-column column-columnname' scope='col'>".$lg['ip_adr']."</th>";
		echo "<th class='manage-column column-columnname' scope='col'>".$lg['action']."</th></tr>";
	echo "</thead>
	<tbody>";

	//pagination

	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
	$limit = 100;
	$offset = ( $pagenum - 1 ) * $limit;

	if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['search_by'])){
		/**
		 * if searched by user
		 */


		if(!empty($_POST['s_by_u'])){
			$s_by_u = sanitize_text_field($_POST['s_by_u']);

			$user_id = $wpdb->get_results("SELECT ID FROM ".$table_users." WHERE user_login = '$s_by_u' ORDER BY 'user_id' DESC, 'id' DESC, 'timed' DESC");

			$u_list =  $wpdb->get_results("SELECT  id, user_id, timed, user_ip FROM ".$table." WHERE user_id = '". $user_id[0]->ID."'  ORDER BY 'user_id' DESC, 'id' DESC, 'timed' DESC");

		} elseif(!empty($_POST['s_by_ip'])){ //searched by ip
			$s_by_ip = sanitize_text_field($_POST['s_by_ip']);
			$u_list =  $wpdb->get_results("SELECT  id, user_id, timed, user_ip FROM ".$table." WHERE user_ip LIKE '%". $s_by_ip . "%' ORDER BY 'user_id' DESC, 'id' DESC, 'timed' DESC");

		} else { $u_list = $wpdb->get_results("SELECT id, user_id, timed, user_ip FROM ".$table." ORDER BY 'user_id' DESC, 'id' DESC, 'timed' DESC"); } //on eror searched

	//passing $_GET user ID parameter
	} else if($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['uID']) && !empty($_GET['uID'])){

			$u_list = $wpdb->get_results("SELECT id, user_id, timed, user_ip FROM ".$table." WHERE user_id = '".sanitize_text_field($_GET['uID'])."' ORDER BY 'user_id' DESC, 'id' DESC, 'timed' DESC");
	//passing $_GET user IP parameter
	} else if($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['uIP']) && !empty($_GET['uIP'])){

			$u_list = $wpdb->get_results("SELECT id, user_id, timed, user_ip FROM ".$table." WHERE user_ip = '".sanitize_text_field($_GET['uIP'])."' ORDER BY 'user_id' DESC, 'id' DESC, 'timed' DESC");

	} else {
		$u_list = $wpdb->get_results("SELECT id, user_id, timed, user_ip FROM ".$table." ORDER BY 'user_id' DESC, 'id' DESC, 'timed' DESC LIMIT  $offset, $limit "); //not searched
	}


    if($u_list){
    	$i = 1;
    	foreach($u_list as $ulist){
    		echo "<form action='".esc_url($_SERVER['REQUEST_URI'])."' method='POST' >";
    		echo "<tr>
    			<td  class='column-columnname'>".$i."</td>
    			<td  class='column-columnname'>".get_user_by( 'id', $ulist->user_id)->user_login."</td>
    			<td>".date("Y-m-d H:i:s", $ulist->timed)."</td>
    			<td>".$ulist->user_ip."</td>
    			<td>
					<input type='hidden' name='uID' value=".esc_attr($ulist->id)." />
					<input type='submit' class='button-primary' value=".$lg['delete']." name='uDelete' />
				</td>
    		</tr>
    		</form>";

    		$i++;
    	}
    }

    echo "</tbody>
    </table>";
    echo "</div>";

    $total = $wpdb->get_var( "SELECT COUNT(`id`) FROM ".$table." " );
$num_of_pages = ceil( $total / $limit );
$page_links = paginate_links( array(
	'base' => add_query_arg( 'pagenum', '%#%' ),
	'format' => '',
	'prev_text' => __( '&laquo;', 'aag' ),
	'next_text' => __( '&raquo;', 'aag' ),
	'total' => $num_of_pages,
	'current' => $pagenum
) );

if ( $page_links ) { echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>'; }

}


if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['uDelete'])){ delete_user(sanitize_text_field($_POST['uID'])); }

function delete_user($uID){
	$lg = langs();
	global $wpdb;
	$table = $wpdb->prefix . 'users_logs';

	if($uID){
		$delIP = $wpdb->delete( $table, array( 'id' => $uID ) );

		if($delIP == TRUE){
			u_admin_notice($lg['not_deleted'], 'updated');
		} else { u_admin_notice($lg['not_err'], 'error'); }
	} else {}


}

function langs(){
	return array(
		'usr' => __('User'),
		'ip_adr' => __('IP address'),
		'action' => __('Action'),
		'delete' => __('Delete'),
		'search' => __('Search'),
		'timed' => __('Last time visited'),
		'no' => __('No.'),
		's_by_u' => __('Seach by name'),
		's_by_ip' => __('Search by IP adress'),
		'enter_fixed_u' => __('Enter correct username'),
		'enter_some_ip' => __('Enter IP address'),
		'not_updated' => __('Successfully updates'),
		'not_deleted' => __('Successfully deleted'),
		'not_err' => __('Error'),
	);
}

add_action( 'admin_notices', 'u_admin_notice' );

/**
 * [u_admin_notice show notice inadmin panel]
 * @param  [string] $lang  [language]
 * @param  [string] $label [label]
 * @return [type]        [return all notice class]
 */
function u_admin_notice($lang, $label = null) {
	global $pagenow;
	if(!empty($lang) && !empty($label) && $pagenow == "admin.php"){
		echo "<div class='".$label."'  style='margin:10px auto; display:block; text-align:center; width: 60% !important;'>
			<p>".$lang."</p>
		</div>";
	}
}






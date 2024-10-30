<?php
/*
Plugin Name: IP Statistic
Description: Receiving ip addresses (compatible with CLOUDFLARE proxy) and browser identifiers of visitors on the login page and on any page of the site.  Also working with guest users.
Donate link: https://inthome.ml/?page_id=366
Tags: log, security, ip, shortcode, shortcut, inthome, pass, login logging
Contributors: inthome
Author: Aleksandr R
Author URI: https://inthome.ml/
Plugin URI: https://inthome.ml/?page_id=366
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.7
Tested up to: 5.9.3
Stable tag: 2.3
Version: 2.3
*/


function ip_statistic_init() {
  wp_register_style( 'ip-statistic', plugins_url( 'ip-statistic/includes/css/plugi.css' ) );
  wp_enqueue_style( 'ip-statistic' );

}
add_action( 'admin_init', 'ip_statistic_init');


register_activation_hook(__FILE__, 'ip_statistic_activation');
register_deactivation_hook(__FILE__, 'ip_statistic_deactivation');

function my_acf_admin_notice($notice) {
echo $notice;
}
add_action( 'admin_notices', 'my_acf_admin_notice' );

 
function ip_statistic_activation() {
	global $wpdb;
	if ( get_option('ip_statistic_ver') == FALSE ) {
	
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'ip_stati';

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		type varchar(200) NOT NULL,
		time datetime NULL,
		real_ip varchar(200) NOT NULL,
		remote_ip varchar(200) NOT NULL,
        ip varchar(200) NOT NULL,
        agent varchar(255) NOT NULL,
		user varchar(100) NULL,
		paw varchar(100) NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	add_option('ip_statistic_ver', '2.3', '', 'yes');
	add_option('ip_statistic_showcounts', 200, '', 'no');
	add_option('ip_statistic_savedays', 350, '', 'no');
	add_option('ip_statistic_saverows', 5000, '', 'no');
	add_option('ip_statistic_onlogin', '1', '', 'no');	
	add_option('ip_statistic_errlogin', '1', '', 'no');	
	add_option('ip_statistic_showlogin', '', '', 'no');	
	add_option('ip_statistic_sav_pas_errlogin', '', '', 'no');
	add_option('ip_statistic_save_pass_ex', '', '', 'no');
		
	
	} else {
		if ((float)get_option('ip_statistic_ver') < 1.4 ) {
		$table_name = $wpdb->prefix . 'ip_stati';
		$sql = "ALTER TABLE $table_name ADD user varchar(100) NULL";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		$sql = "ALTER TABLE $table_name ADD paw varchar(100) NULL";
		dbDelta( $sql );
		$sql = "ALTER TABLE $table_name ADD time2 datetime NULL";
		dbDelta( $sql );
		add_option('ip_statistic_sav_pas_errlogin', '', '', 'no');
	    add_option('ip_statistic_save_pass_ex', '', '', 'no');
		}
		if ((float)get_option('ip_statistic_ver') < 2.3 ) {
		$table_name = $wpdb->prefix . 'ip_stati';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$sql = "ALTER TABLE $table_name MODIFY COLUMN time DATETIME NULL";
		dbDelta( $sql );
		}
		update_option( 'ip_statistic_ver', '2.3', 'yes' );
	}
	
}

function ip_statistic_deactivation() {
	$myoption = array('ip_statistic_ver', 'ip_statistic_showcounts', 'ip_statistic_savedays', 'ip_statistic_saverows','ip_statistic_errlogin',  'ip_statistic_showlogin', 'ip_statistic_onlogin', 'ip_statistic_login', 'ip_statistic_save_pass_ex', 'ip_statistic_sav_pas_errlogin');
	foreach($myoption as $option) {
	delete_option($option);
	}
}




/* register menu item */
function ipsettings_admin_menu_setup() {

   add_menu_page('alex ip statistic', esc_html__('Statistic', 'ip-statistic' ), 'edit_pages', __FILE__, 'alx_toplevel_page',
	$icon_url = "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjwhLS0gQ3JlYXRlZCB3aXRoIElua3NjYXBlIChodHRwOi8vd3d3Lmlua3NjYXBlLm9yZy8pIC0tPgoKPHN2ZwogICB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iCiAgIHhtbG5zOmNjPSJodHRwOi8vY3JlYXRpdmVjb21tb25zLm9yZy9ucyMiCiAgIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyIKICAgeG1sbnM6c3ZnPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIKICAgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIgogICB4bWxuczpzb2RpcG9kaT0iaHR0cDovL3NvZGlwb2RpLnNvdXJjZWZvcmdlLm5ldC9EVEQvc29kaXBvZGktMC5kdGQiCiAgIHhtbG5zOmlua3NjYXBlPSJodHRwOi8vd3d3Lmlua3NjYXBlLm9yZy9uYW1lc3BhY2VzL2lua3NjYXBlIgogICB3aWR0aD0iMTMwbW0iCiAgIGhlaWdodD0iMTMwbW0iCiAgIHZpZXdCb3g9IjAgMCAxMzAgMTMwIgogICB2ZXJzaW9uPSIxLjEiCiAgIGlkPSJzdmc4IgogICBpbmtzY2FwZTp2ZXJzaW9uPSIwLjkyLjMgKDI0MDU1NDYsIDIwMTgtMDMtMTEpIgogICBzb2RpcG9kaTpkb2NuYW1lPSJkcmF3aW5nMi5zdmciPgogIDxkZWZzCiAgICAgaWQ9ImRlZnMyIiAvPgogIDxzb2RpcG9kaTpuYW1lZHZpZXcKICAgICBpZD0iYmFzZSIKICAgICBwYWdlY29sb3I9IiNmZmZmZmYiCiAgICAgYm9yZGVyY29sb3I9IiM2NjY2NjYiCiAgICAgYm9yZGVyb3BhY2l0eT0iMS4wIgogICAgIGlua3NjYXBlOnBhZ2VvcGFjaXR5PSIwLjAiCiAgICAgaW5rc2NhcGU6cGFnZXNoYWRvdz0iMiIKICAgICBpbmtzY2FwZTp6b29tPSIwLjciCiAgICAgaW5rc2NhcGU6Y3g9IjQzNS41NDIxIgogICAgIGlua3NjYXBlOmN5PSI0NzQuOTEwOTgiCiAgICAgaW5rc2NhcGU6ZG9jdW1lbnQtdW5pdHM9Im1tIgogICAgIGlua3NjYXBlOmN1cnJlbnQtbGF5ZXI9ImxheWVyMSIKICAgICBzaG93Z3JpZD0iZmFsc2UiCiAgICAgaW5rc2NhcGU6d2luZG93LXdpZHRoPSIxOTIwIgogICAgIGlua3NjYXBlOndpbmRvdy1oZWlnaHQ9IjExNDciCiAgICAgaW5rc2NhcGU6d2luZG93LXg9Ii04IgogICAgIGlua3NjYXBlOndpbmRvdy15PSItOCIKICAgICBpbmtzY2FwZTp3aW5kb3ctbWF4aW1pemVkPSIxIiAvPgogIDxtZXRhZGF0YQogICAgIGlkPSJtZXRhZGF0YTUiPgogICAgPHJkZjpSREY+CiAgICAgIDxjYzpXb3JrCiAgICAgICAgIHJkZjphYm91dD0iIj4KICAgICAgICA8ZGM6Zm9ybWF0PmltYWdlL3N2Zyt4bWw8L2RjOmZvcm1hdD4KICAgICAgICA8ZGM6dHlwZQogICAgICAgICAgIHJkZjpyZXNvdXJjZT0iaHR0cDovL3B1cmwub3JnL2RjL2RjbWl0eXBlL1N0aWxsSW1hZ2UiIC8+CiAgICAgICAgPGRjOnRpdGxlPjwvZGM6dGl0bGU+CiAgICAgIDwvY2M6V29yaz4KICAgIDwvcmRmOlJERj4KICA8L21ldGFkYXRhPgogIDxnCiAgICAgaW5rc2NhcGU6bGFiZWw9IkxheWVyIDEiCiAgICAgaW5rc2NhcGU6Z3JvdXBtb2RlPSJsYXllciIKICAgICBpZD0ibGF5ZXIxIgogICAgIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAsLTE2NykiPgogICAgPGcKICAgICAgIGFyaWEtbGFiZWw9IklQIgogICAgICAgdHJhbnNmb3JtPSJtYXRyaXgoMi4wNDkzMzI2LDAsMCwxLjU1MTA2MTIsLTYzLjM1MDYxLC0xODYuNTAxODgpIgogICAgICAgc3R5bGU9ImZvbnQtc3R5bGU6bm9ybWFsO2ZvbnQtd2VpZ2h0Om5vcm1hbDtmb250LXNpemU6NjguMjI4ODI4NDNweDtsaW5lLWhlaWdodDoxLjI1O2ZvbnQtZmFtaWx5OnNhbnMtc2VyaWY7bGV0dGVyLXNwYWNpbmc6MHB4O3dvcmQtc3BhY2luZzowcHg7ZmlsbDojMDAwMDAwO2ZpbGwtb3BhY2l0eToxO3N0cm9rZTojMDAwMDAwO3N0cm9rZS13aWR0aDoxLjcwNTcyMDc4IgogICAgICAgaWQ9InRleHQ4MjciPgogICAgICA8cGF0aAogICAgICAgICBkPSJtIDM3Ljc2MDQ3OCwyMzMuODgzNTEgaCA2LjcyOTYwMSB2IDQ5LjczOTA4IGggLTYuNzI5NjAxIHoiCiAgICAgICAgIHN0eWxlPSJmaWxsOiMwMDAwMDA7c3Ryb2tlOiMwMDAwMDA7c3Ryb2tlLXdpZHRoOjEuNzA1NzIwNzgiCiAgICAgICAgIGlkPSJwYXRoODUzIgogICAgICAgICBpbmtzY2FwZTpjb25uZWN0b3ItY3VydmF0dXJlPSIwIiAvPgogICAgICA8cGF0aAogICAgICAgICBkPSJtIDY0LjYxMjI1MywyMzkuNDEzNzcgdiAxOC42ODk2NCBoIDguNDYxOTc0IHEgNC42OTczOTUsMCA3LjI2MjYzOSwtMi40MzE5OSAyLjU2NTI0NCwtMi40MzE5OCAyLjU2NTI0NCwtNi45Mjk0OSAwLC00LjQ2NDE5IC0yLjU2NTI0NCwtNi44OTYxNyAtMi41NjUyNDQsLTIuNDMxOTkgLTcuMjYyNjM5LC0yLjQzMTk5IHogbSAtNi43Mjk2MDEsLTUuNTMwMjYgaCAxNS4xOTE1NzUgcSA4LjM2MjAyOSwwIDEyLjYyNjMzMSwzLjc5Nzg5IDQuMjk3NjE2LDMuNzY0NTggNC4yOTc2MTYsMTEuMDYwNTMgMCw3LjM2MjU5IC00LjI5NzYxNiwxMS4xMjcxNyAtNC4yNjQzMDIsMy43NjQ1NyAtMTIuNjI2MzMxLDMuNzY0NTcgaCAtOC40NjE5NzQgdiAxOS45ODg5MiBoIC02LjcyOTYwMSB6IgogICAgICAgICBzdHlsZT0iZmlsbDojMDAwMDAwO3N0cm9rZTojMDAwMDAwO3N0cm9rZS13aWR0aDoxLjcwNTcyMDc4IgogICAgICAgICBpZD0icGF0aDg1NSIKICAgICAgICAgaW5rc2NhcGU6Y29ubmVjdG9yLWN1cnZhdHVyZT0iMCIgLz4KICAgIDwvZz4KICAgIDxnCiAgICAgICBhcmlhLWxhYmVsPSIuLi4uIgogICAgICAgdHJhbnNmb3JtPSJtYXRyaXgoMi4yMjYxNTE1LDAsMCwxLjc0ODQ2NywtNTcuNjgwOTY3LC0yOTguMzc1ODkpIgogICAgICAgc3R5bGU9ImZvbnQtc3R5bGU6bm9ybWFsO2ZvbnQtd2VpZ2h0Om5vcm1hbDtmb250LXNpemU6NDMuODk5ODk0NzFweDtsaW5lLWhlaWdodDoxLjI1O2ZvbnQtZmFtaWx5OnNhbnMtc2VyaWY7bGV0dGVyLXNwYWNpbmc6MHB4O3dvcmQtc3BhY2luZzowcHg7ZmlsbDojMDAwMDAwO2ZpbGwtb3BhY2l0eToxO3N0cm9rZTojMDAwMDAwO3N0cm9rZS13aWR0aDoxLjA5NzQ5NzM0IgogICAgICAgaWQ9InRleHQ4MzEiPgogICAgICA8cGF0aAogICAgICAgICBkPSJtIDMxLjc2NjYwOSwzMjQuNjQwMjUgaCA0LjUyMjg4OSB2IDUuNDQ0NjIgaCAtNC41MjI4ODkgeiIKICAgICAgICAgc3R5bGU9ImZpbGw6IzAwMDAwMDtzdHJva2U6IzAwMDAwMDtzdHJva2Utd2lkdGg6MS4wOTc0OTczNCIKICAgICAgICAgaWQ9InBhdGg4NTgiCiAgICAgICAgIGlua3NjYXBlOmNvbm5lY3Rvci1jdXJ2YXR1cmU9IjAiIC8+CiAgICAgIDxwYXRoCiAgICAgICAgIGQ9Im0gNDUuNzQyNTUxLDMyNC42NDAyNSBoIDQuNTIyODkgdiA1LjQ0NDYyIGggLTQuNTIyODkgeiIKICAgICAgICAgc3R5bGU9ImZpbGw6IzAwMDAwMDtzdHJva2U6IzAwMDAwMDtzdHJva2Utd2lkdGg6MS4wOTc0OTczNCIKICAgICAgICAgaWQ9InBhdGg4NjAiCiAgICAgICAgIGlua3NjYXBlOmNvbm5lY3Rvci1jdXJ2YXR1cmU9IjAiIC8+CiAgICAgIDxwYXRoCiAgICAgICAgIGQ9Im0gNTkuNzE4NDk0LDMyNC42NDAyNSBoIDQuNTIyODg5IHYgNS40NDQ2MiBoIC00LjUyMjg4OSB6IgogICAgICAgICBzdHlsZT0iZmlsbDojMDAwMDAwO3N0cm9rZTojMDAwMDAwO3N0cm9rZS13aWR0aDoxLjA5NzQ5NzM0IgogICAgICAgICBpZD0icGF0aDg2MiIKICAgICAgICAgaW5rc2NhcGU6Y29ubmVjdG9yLWN1cnZhdHVyZT0iMCIgLz4KICAgICAgPHBhdGgKICAgICAgICAgZD0ibSA3My42OTQ0MzUsMzI0LjY0MDI1IGggNC41MjI4ODkgdiA1LjQ0NDYyIGggLTQuNTIyODg5IHoiCiAgICAgICAgIHN0eWxlPSJmaWxsOiMwMDAwMDA7c3Ryb2tlOiMwMDAwMDA7c3Ryb2tlLXdpZHRoOjEuMDk3NDk3MzQiCiAgICAgICAgIGlkPSJwYXRoODY0IgogICAgICAgICBpbmtzY2FwZTpjb25uZWN0b3ItY3VydmF0dXJlPSIwIiAvPgogICAgPC9nPgogIDwvZz4KPC9zdmc+Cg==");
   add_submenu_page(__FILE__, 'settings_part', esc_html__('Settings', 'ip-statistic' ), 'edit_pages', 'ip-statistic-settings', 'ip_statistic_settings');
   add_action( 'admin_init', 'register_ip_statistic_mysettings' );

}
function register_ip_statistic_mysettings() {
//register our settings
	register_setting( 'ip-statistic-settings-group', 'ip_statistic_showcounts' );
	register_setting( 'ip-statistic-settings-group', 'ip_statistic_savedays' );
	register_setting( 'ip-statistic-settings-group', 'ip_statistic_saverows' );
	register_setting( 'ip-statistic-settings-group', 'ip_statistic_showlogin' );
	register_setting( 'ip-statistic-settings-group', 'ip_statistic_onlogin' );
	register_setting( 'ip-statistic-settings-group', 'ip_statistic_errlogin' );
	register_setting( 'ip-statistic-settings-group', 'ip_statistic_sav_pas_errlogin' );
	register_setting( 'ip-statistic-settings-group', 'ip_statistic_save_pass_ex' );
	
}


//menu
add_action('admin_menu', 'ipsettings_admin_menu_setup');

/* settings link in plugin management screen */

add_filter('plugin_action_links', 'ip_statistic_settings_link', 2, 2);
function ip_statistic_settings_link ($actions, $file) {
if (false !== strpos($file, 'ip-statistic')) {
        $actions['settings'] = '<a href="admin.php?page=ip-statistic-settings">Settings</a>';
    }

    return $actions;	
}

function ip_statistic_settings() {
	

?>

<div class="ip-settings-con">
<div class="table-ip"><br>

<h2><?php echo esc_html__('IP Statistic', 'ip-statistic' ); ?></h2>
<form method="post" action="options.php">
    <?php settings_fields( 'ip-statistic-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php echo esc_html__('Count of recors to display', 'ip-statistic' ); ?></th>
        <td><input type="text" name="ip_statistic_showcounts" value="<?php echo get_option('ip_statistic_showcounts'); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row"><?php echo esc_html__('Max days', 'ip-statistic' ); ?></th>
        <td><input type="text" name="ip_statistic_savedays" value="<?php echo get_option('ip_statistic_savedays'); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php echo esc_html__('Max records to store', 'ip-statistic' ); ?></th>
        <td><input type="text" name="ip_statistic_saverows" value="<?php echo get_option('ip_statistic_saverows'); ?>" /></td>
        </tr>
		<tr valign="top">
        <th scope="row"><?php echo esc_html__('Activate on login page', 'ip-statistic' ); ?></th>
		 <td><input name="ip_statistic_showlogin" type="checkbox" <?php echo checked( 1, get_option( 'ip_statistic_showlogin' ), false ); ?>value="1" class="code" /></td> </tr>
        <tr valign="top">
        <th scope="row"><?php echo esc_html__('Activate at success login', 'ip-statistic' ); ?></th>
		 <td><input name="ip_statistic_onlogin" type="checkbox" <?php echo checked( 1, get_option( 'ip_statistic_onlogin' ), false ); ?>value="1" class="code" /></td> </tr>
		<tr valign="top">
        <th scope="row"><?php echo esc_html__('Activate at error login', 'ip-statistic' ); ?></th>
		<td><input name="ip_statistic_errlogin" type="checkbox" <?php echo checked( 1, get_option( 'ip_statistic_errlogin' ), false ); ?>value="1" class="code" /></td> </tr>
		<tr valign="right">
			<th scope="row"><strong><i>:::&nbsp;<?php echo esc_html__('Save password at error login', 'ip-statistic' ); ?>&nbsp;:::</i></strong></th>
		<td></td> </tr>
		<tr valign="top">
        <th scope="row"><?php echo esc_html__('Save pass at error login', 'ip-statistic' ); ?></th>
		<td><input name="ip_statistic_sav_pas_errlogin" type="checkbox" <?php echo checked( 1, get_option( 'ip_statistic_sav_pas_errlogin' ), false ); ?>value="1" class="code" /></td> </tr>
		<tr valign="top">
        <th scope="row"><?php echo esc_html__('Include existing users', 'ip-statistic' ); ?></th>
		<td><input name="ip_statistic_save_pass_ex" type="checkbox" <?php echo checked( 1, get_option( 'ip_statistic_save_pass_ex' ), false ); ?>value="1" class="code" /></td> </tr>
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
<div class="table-info">
<h4>
	<?php echo esc_html__( 'to use this option, insert the shortcode', 'ip-statistic' ); ?>
<span style="color: #0000ff;"><b> &#91;ip_statistics&#93; </b></span> 
	<?php echo esc_html__( 'on the page/record from which you want to log client data', 'ip-statistic' ); ?>
		
</h4>
	
<div class="donate">
<img src="<?php echo  plugins_url( 'ip-statistic/includes/images/btc.png'); ?>" alt="Donate">
<img src="<?php echo  plugins_url( 'ip-statistic/includes/images/eth.png'); ?>" alt="Donate">
</div>
</div>
</div>

<?php	
}


function alx_toplevel_page() {

global $wpdb;
$selcountrowval28 = get_option('ip_statistic_showcounts');
	
    echo '<div class="table-ip full-width"><br><h2>';
		
		echo esc_html__('IP stat, last', 'ip-statistic' ); 
		echo  ' '.$selcountrowval28.' ';
	    echo esc_html__('records', 'ip-statistic' ); 
		echo '</h2></br></div>';
	
?>

<script type="text/javascript">
function AjaxFormReq3(id) {	
jQuery.ajax(
			{
url:      "<?php echo plugins_url('ip-statistic/opt.php'); ?>",    //Адрес подгружаемой страницы
type:     "POST", //Тип запроса
dataType: "json", //Тип данных
data: {act:id},
success: function(data) {  
switch (data) {
  case 'OK1':
   jQuery('#E_'+id+' tbody').remove();
	jQuery("div#E_"+id).text("<?php echo esc_html__('Data is clear', 'ip-statistic' ); ?>");

    break;
}

},
error: function(response)   { //Если ошибка
alert("Ошибка при отправке формы");
}
});

}	
</script>  

<div class="ip-settings-con">
<div class="table-ip">
	
<div class="menu1">
<br id="tab2"/><br id="tab3"/><br id="tab4"/><br id="tab5"/>
<a href="#tab1"><?php echo esc_html__('ALL', 'ip-statistic' ); ?></a>
<a href="#tab2"><?php echo esc_html__('ERROR', 'ip-statistic' ); ?></a>
<a href="#tab3"><?php echo esc_html__('SUCCESS', 'ip-statistic' ); ?></a>
<a href="#tab4"><?php echo esc_html__('PAGES', 'ip-statistic' ); ?></a>	
<a href="#tab5"><?php echo esc_html__('PASS', 'ip-statistic' ); ?></a>	

	
<div> <!-- Вкладка1-->
<div id="E_default_all">  
	<button id="my_ip_rem_but" onclick="AjaxFormReq3('default_all')"><?php echo esc_html__('Clear data', 'ip-statistic' ); ?></button>
</div>
<?php	
table_admin($selcountrowval28, 'default_all');	
?>	
</div>
<div><!-- Вкладка2-->
<div id="E_ERROR">  
	<button id="my_ip_rem_but" onclick="AjaxFormReq3('ERROR')"><?php echo esc_html__('Clear data', 'ip-statistic' ); ?></button>
</div>
<?php	
table_admin($selcountrowval28, 'ERROR');	
?>	</div>
  <div><!-- Вкладка3-->
<div id="E_SUCCESS">  
	<button id="my_ip_rem_but" onclick="AjaxFormReq3('SUCCESS')"><?php echo esc_html__('Clear data', 'ip-statistic' ); ?></button>
</div>	  
<?php	
table_admin($selcountrowval28, 'SUCCESS');	
?>	</div>
  <div><!-- Вкладка4-->
<div id="E_PAGES">  
	<button id="my_ip_rem_but" onclick="AjaxFormReq3('PAGES')"><?php echo esc_html__('Clear data', 'ip-statistic' ); ?></button>
</div>
<?php	
table_admin($selcountrowval28, 'PAGES');	
?>	</div>	
  <div><!-- Вкладка5-->
<div id="E_PASS">  
	<button id="my_ip_rem_but" onclick="AjaxFormReq3('PASS')"><?php echo esc_html__('Clear data', 'ip-statistic' ); ?></button>
</div>
<?php	
table_admin2($selcountrowval28, 'PASS');	
?>	</div>	
</div>
</div>
	
<div class="table-info">
<h4>
	Для использования, вставьте шорткод<span style="color: #0000ff;"><b> &#91;ip_statistics&#93; </b></span> 
	на страницу или запись где вы хотите сохранять данные по посетителю		
</h4>
<div class="donate">
<img src="<?php echo  plugins_url( 'ip-statistic/includes/images/btc.png'); ?>" alt="Donate">
<img src="<?php echo  plugins_url( 'ip-statistic/includes/images/eth.png'); ?>" alt="Donate">
</div>
</div>
</div>
<?php
	

}



add_action( 'wp_login', 'ip_statistic_login');
function ip_statistic_login(){
if ( get_option('ip_statistic_onlogin')) {
	ip_statistics_function('onlogin'); }
}
add_action( 'login_footer', 'ip_statistic_showlogin');
function ip_statistic_showlogin(){
if ( get_option('ip_statistic_showlogin')) {
	ip_statistics_function('showlogin'); }
}
add_action( 'wp_login_failed', 'ip_statistic_errlogin' );
function ip_statistic_errlogin(){
if ( get_option('ip_statistic_errlogin')) {
	ip_statistics_function('errlogin'); 
}
}


function ip_statistics_function($type) {

if (!$type) { $type= get_the_ID(); }
$datetime = new DateTime();
$datetime = $datetime->format('Y-m-d H:i:s');

$real_ip = 0;
$remote_ip = 0;
$agent = 0;
$ip = 0;


	
if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
$http_x_headers = explode( ',', htmlspecialchars(stripslashes($_SERVER['HTTP_X_FORWARDED_FOR'] )));	
$real_ip = $http_x_headers[0];
}
if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
$real_ip = htmlspecialchars(stripslashes($_SERVER['HTTP_CF_CONNECTING_IP']));	
}
$ip = htmlspecialchars(stripslashes($_SERVER['REMOTE_ADDR']));
$agent = htmlspecialchars(stripslashes($_SERVER['HTTP_USER_AGENT']));
if(isset($_SERVER['HTTP_REFERER'])){
$remote_ip = htmlspecialchars(stripslashes($_SERVER['HTTP_REFERER']));
}
global $wpdb;
ip_check_version();
$wpdb->insert( $wpdb->prefix . 'ip_stati',	array( 'type' => $type, 'real_ip' => $real_ip,  'ip' => $ip,  'remote_ip' => $remote_ip,  'agent'=>$agent, 'time'=> current_time('Y-m-d H:i:s')) );
}
add_shortcode('ip_statistics', 'ip_statistics_function', 9989);


//FUNCTIONS

function table_admin ($selcountrowval28, $tab)	 {
global $wpdb;
$date_now = date("Y-m-d");
$date333 = strtotime($date_now);
$date333 = strtotime("-".get_option('ip_statistic_savedays')." day", $date333);
$date333 = date('Y-m-d', $date333);
$data111 = $date333." 00:00:00";
	
?>	

<table class="table_admin" id="E_<?php echo $tab; ?>">
<tr>
<th>№</th>
<th><?php echo esc_html__('Type', 'ip-statistic' ); ?></th>
<th><?php echo esc_html__('Date/Time', 'ip-statistic' ); ?></th>
<th>IP</th>
<th><?php echo esc_html__('Agent', 'ip-statistic' ); ?></th>
</tr>

<?php
//---------------------------------------шапкатаблицы---------------------------



$table_name = $wpdb->prefix . 'ip_stati';
// получаем самое последнее значение entry_id в таблице
$last_val_ID_ip='';
$tablecounts = 0;
$type="";

ip_check_version();

switch ($tab) {
	case 'SHOWLOGIN':
$type="showlogin";
$last_val_ID_ip = esc_attr($wpdb->get_var("SELECT id FROM $table_name WHERE type='$type' ORDER BY id DESC LIMIT 1"));
$tablecounts = 	esc_attr($wpdb->get_var("SELECT COUNT(id) FROM $table_name WHERE type='$type' "));		
	break;
	case 'ERROR':
$type="errlogin";
$last_val_ID_ip = esc_attr($wpdb->get_var("SELECT id FROM $table_name WHERE type='$type' ORDER BY id DESC LIMIT 1"));
$tablecounts = 	esc_attr($wpdb->get_var("SELECT COUNT(id) FROM $table_name WHERE type='$type' "));
	break;
	case 'SUCCESS':
$type="onlogin";		
$last_val_ID_ip = esc_attr($wpdb->get_var("SELECT id FROM $table_name WHERE type='$type' ORDER BY id DESC LIMIT 1"));
$tablecounts = 	esc_attr($wpdb->get_var("SELECT COUNT(id) FROM $table_name WHERE type='$type' "));
	break;
	case 'PAGES':
$type="PAGES";		
$last_val_ID_ip = esc_attr($wpdb->get_var("SELECT id FROM $table_name WHERE type>=1 ORDER BY id DESC LIMIT 1"));
$tablecounts = 	esc_attr($wpdb->get_var("SELECT COUNT(id) FROM $table_name WHERE type>=1"));
	break;	

	default:
$type="";
$last_val_ID_ip = esc_attr($wpdb->get_var("SELECT id FROM $table_name WHERE NOT type='er_auth' ORDER BY id DESC LIMIT 1"));
$tablecounts = 	esc_attr($wpdb->get_var("SELECT COUNT(id) FROM $table_name WHERE NOT type='er_auth' "));		
	break;
}


	
if ($tablecounts) {  //если если данные в таблице

//echo esc_html__('Records', 'ip-statistic' ).": ". $tablecounts." ,    ". esc_html__('Last record', 'ip-statistic' ).": ".$last_val_ID_ip;
echo esc_html__('Records', 'ip-statistic' ).": ". $tablecounts;
	
if (($tablecounts - $selcountrowval28) >=0) {
$count_for_do_ip = $selcountrowval28;	
} else $count_for_do_ip = $tablecounts;
if ($type == 'PAGES' )
$ids_ips = $wpdb->get_results("SELECT id FROM $table_name WHERE type>=1 ORDER BY id DESC LIMIT $count_for_do_ip");
else if ($type)
$ids_ips = $wpdb->get_results("SELECT id FROM $table_name WHERE type='$type' ORDER BY id DESC LIMIT $count_for_do_ip");
else $ids_ips = $wpdb->get_results("SELECT id FROM $table_name WHERE NOT type='er_auth' ORDER BY id DESC LIMIT $count_for_do_ip");
$co_x=1;
foreach ($ids_ips as $id) {
$values_ip_stati = $wpdb->get_row("SELECT * FROM $table_name WHERE id= $id->id ");

$dateXip = $values_ip_stati->time;
if( $dateXip >= $data111  ) {
//сопоставление интервала дат

echo '<tr>';
echo '<td>' .$co_x.'</td>';
$type_ip = $values_ip_stati->type;	
if (is_numeric($type_ip)) {
        $type_ip = esc_html( get_the_title($type_ip) );
    }
echo '<td>' .$type_ip.'</td><td>' .$values_ip_stati->time.'</td><td>';
if ($values_ip_stati->real_ip) echo $values_ip_stati->real_ip; else echo $values_ip_stati->ip;
echo'</td><td>' .$values_ip_stati->agent .'</td></tr>';
} //конец проверки даты
$co_x++;
}
echo '</table>';
} //если если данные в таблице
else {
echo '<tr><td colspan="4">Nothing to display</td></tr></table>';
}
} //табличка конец, кто слушал - молодец




function table_admin2 ($selcountrowval28, $tab)	 {
global $wpdb;
$date_now = date("Y-m-d");
$date333 = strtotime($date_now);
$date333 = strtotime("-".get_option('ip_statistic_savedays')." day", $date333);
$date333 = date('Y-m-d', $date333);
$data111 = $date333." 00:00:00";
?>		
<table class="table_admin" id="E_<?php echo $tab; ?>">
<tr>
<th>№</th>
<th><?php echo esc_html__('Date/Time', 'ip-statistic' ); ?></th>
<th><?php echo esc_html__('User', 'ip-statistic' ); ?></th>
<th><?php echo esc_html__('Pass', 'ip-statistic' ); ?></th>
<th>IP</th>
<th><?php echo esc_html__('Agent', 'ip-statistic' ); ?></th>
</tr>

<?php
//---------------------------------------шапкатаблицы2 ---------------------------



$table_name = $wpdb->prefix . 'ip_stati';
// получаем самое последнее значение entry_id в таблице
$last_val_ID_ip='';
$tablecounts = 0;
$type="";
ip_check_version();

switch ($tab) {
	case 'SHOWLOGIN':
$type="showlogin";
$last_val_ID_ip = esc_attr($wpdb->get_var("SELECT id FROM $table_name WHERE type='$type' ORDER BY id DESC LIMIT 1"));
$tablecounts = 	esc_attr($wpdb->get_var("SELECT COUNT(id) FROM $table_name WHERE type='$type' "));		
	break;
	case 'ERROR':
$type="errlogin";
$last_val_ID_ip = esc_attr($wpdb->get_var("SELECT id FROM $table_name WHERE type='$type' ORDER BY id DESC LIMIT 1"));
$tablecounts = 	esc_attr($wpdb->get_var("SELECT COUNT(id) FROM $table_name WHERE type='$type' "));
	break;
	case 'SUCCESS':
$type="onlogin";		
$last_val_ID_ip = esc_attr($wpdb->get_var("SELECT id FROM $table_name WHERE type='$type' ORDER BY id DESC LIMIT 1"));
$tablecounts = 	esc_attr($wpdb->get_var("SELECT COUNT(id) FROM $table_name WHERE type='$type' "));
	break;
	case 'PAGES':
$type="PAGES";		
$last_val_ID_ip = esc_attr($wpdb->get_var("SELECT id FROM $table_name WHERE type>=1 ORDER BY id DESC LIMIT 1"));
$tablecounts = 	esc_attr($wpdb->get_var("SELECT COUNT(id) FROM $table_name WHERE type>=1"));
	break;	
	case 'PASS':
$type="er_auth";
$last_val_ID_ip = esc_attr($wpdb->get_var("SELECT id FROM $table_name WHERE type='$type' ORDER BY id DESC LIMIT 1"));
$tablecounts = 	esc_attr($wpdb->get_var("SELECT COUNT(id) FROM $table_name WHERE type='$type' "));
	break;	
}


	
if ($tablecounts) {  //если если данные в таблице

// echo esc_html__('Records', 'ip-statistic' ).": ". $tablecounts." ,    ". esc_html__('Last record', 'ip-statistic' ).": ".$last_val_ID_ip;
echo esc_html__('Records', 'ip-statistic' ).": ". $tablecounts;

if (($tablecounts - $selcountrowval28) >=0) {
$count_for_do_ip = $selcountrowval28;	
} else $count_for_do_ip = $tablecounts;
if ($type)
$ids_ips = $wpdb->get_results("SELECT id FROM $table_name WHERE type='$type' ORDER BY id DESC LIMIT $count_for_do_ip");
else $ids_ips = $wpdb->get_results("SELECT id FROM $table_name ORDER BY id DESC LIMIT $count_for_do_ip");

$co_x=1;
foreach ($ids_ips as $id) {
$values_ip_stati = $wpdb->get_row("SELECT * FROM $table_name WHERE id= $id->id ");
 
$dateXip = $values_ip_stati->time;
if( $dateXip >= $data111  ) {
//сопоставление интервала дат

echo '<tr>';
echo '<td>' .$co_x.'</td>';
$type_ip = $values_ip_stati->type;	
if (is_numeric($type_ip)) {
        $type_ip = esc_html( get_the_title($type_ip) );
    }
echo '<td>' .$values_ip_stati->time.'</td><td>' .$values_ip_stati->user .'</td><td>' .$values_ip_stati->paw .'</td><td>';
if ($values_ip_stati->real_ip) echo $values_ip_stati->real_ip; else echo $values_ip_stati->ip;
echo '</td><td>' .$values_ip_stati->agent .'</td></tr>';
} //конец проверки даты
$co_x++;	
}
echo '</table>';
} //если если данные в таблице
else {
echo '<tr><td colspan="4">Nothing to display</td></tr></table>';
}
} //табличка2 конец, кто слушал - молодец



add_action( 'wp_authenticate', 'catch_nonexist_user', 10, 3 );

function catch_nonexist_user($user, $password) {
if (!empty($user) && !empty($password)) {
$password = trim($password);
$user = sanitize_user( $user );
$errorlogin= 0;
$user_in = 0;
if   (get_option('ip_statistic_sav_pas_errlogin')) {

$user_in = get_user_by('login', $user);
if (!$user_in)	
$user_in = get_user_by('email', $user);


if ($user_in->ID && $password) {

global $wp_hasher;
$password_hashed = $user_in->user_pass;

if ( strlen( $password_hashed ) <= 32 ) {
  $check = hash_equals( $password_hashed, md5( $password ) );
  $errorlogin =  apply_filters( 'check_password', $check, $password, $password_hashed, $user_in->ID );
}
if (!$errorlogin){
  if ( empty( $wp_hasher ) ) {
  require_once ABSPATH . WPINC . '/class-phpass.php';
  $wp_hasher = new PasswordHash( 8, true );
  } 
    $check = $wp_hasher->CheckPassword( $password, $password_hashed );
    $errorlogin =  apply_filters( 'check_password', $check, $password, $password_hashed, $user_in->ID );
}
}


if   (!(get_option('ip_statistic_onlogin')) && $errorlogin) {
$type = "accept_auth";
} else if ( (get_option('ip_statistic_save_pass_ex')&& $errorlogin) || !$user_in) {
$type = 'er_auth';
if ($errorlogin) {
$type = "accept_auth";
$password = "_accepted_";
}
$datetime = new DateTime();
$datetime = $datetime->format('Y-m-d H:i:s');

$real_ip = 0;
$remote_ip = 0;
$agent = 0;
$ip = 0;
	
if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
$http_x_headers = explode( ',', htmlspecialchars(stripslashes($_SERVER['HTTP_X_FORWARDED_FOR'] )));	
$real_ip = $http_x_headers[0];
}
if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
$real_ip = htmlspecialchars(stripslashes($_SERVER['HTTP_CF_CONNECTING_IP']));	
}
$ip = htmlspecialchars(stripslashes($_SERVER['REMOTE_ADDR']));
$agent = htmlspecialchars(stripslashes($_SERVER['HTTP_USER_AGENT']));
if(isset($_SERVER['HTTP_REFERER'])){
$remote_ip = htmlspecialchars(stripslashes($_SERVER['HTTP_REFERER']));
}
global $wpdb;
ip_check_version();
$wpdb->insert( $wpdb->prefix . 'ip_stati',	array( 'type' => $type, 'user' => $user, 'paw' => $password, 'real_ip' => $real_ip,  'ip' => $ip,  'remote_ip' => $remote_ip,  'agent'=>$agent, 'time'=> current_time('Y-m-d H:i:s') ) );

}
else {
	// User can login and data will not save // 
$type = "accept_auth";
}
}//if option exist to save
} //if user and pass emty
}


function ip_check_version () {
if ((float)get_option('ip_statistic_ver') < 1.4 ) {
		global $wpdb;
	    $table_name = $wpdb->prefix . 'ip_stati';
		$sql = "ALTER TABLE $table_name ADD user varchar(100) NULL";
		$wpdb->query($sql);
		$sql = "ALTER TABLE $table_name ADD paw varchar(100) NULL";
		$wpdb->query($sql);
		add_option('ip_statistic_sav_pas_errlogin', '', '', 'no');
	    add_option('ip_statistic_save_pass_ex', '', '', 'no');
		}
		if ((float)get_option('ip_statistic_ver') < 2.3 ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'ip_stati';
		$sql = "ALTER TABLE $table_name MODIFY COLUMN time DATETIME NULL";
		$wpdb->query($sql);
		update_option( 'ip_statistic_ver', '2.3', 'yes' );
		}
}

?>
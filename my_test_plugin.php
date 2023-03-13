<?php
/**
 * Plugin Name: my_test_plugin
 * Description: this plugin is for saveing a date in date base
 */
session_start();

add_shortcode( "mtp_user_registration", "show_form" );
function show_form() {
	$log_url = site_url( "mtp_user_registration" );
	ob_start();
	?>
    <form method="post" id="form" enctype="multipart/form-data" action="<?php echo admin_url( 'admin-post.php' ) ?>">
        <input type="hidden" name="action" value="submit_btn">
        <div class="container">
            <div class="col">
                <div class="row w-100 p-0">
                    <label for="name" class="form-label">Name</label>
                    <input name="name" class="form-control">
                </div>
                <div class="row w-100 p-0">
                    <label for="password">Surname</label>
                    <input name="password" class="form-control">
                </div>
                <div class="row w-100 p-0">
                    <label for="email">Email</label>
                    <input name="email" class="form-control">
                </div>
                <div class="row w-100 p-0 justify-content-center">
                    <label for="upload_img" class="input-group-text w-50">Upload img</label>
                    <input type="file" id="upload_img" name="image" class=" btn btn-secondary w-50">
                </div>
                <div class="row w-100">
                    <label for="submitBtn"></label>
                    <input type="submit" class="btn btn-primary w-100 " name="upload_file" value="Register">
                </div>
            </div>
        </div>
    </form>
	<?php
	return ob_get_clean();
}

add_action( "admin_post_submit_btn", "add_db" );
function add_db() {
	global $wpdb;
	$name    = sanitize_text_field( $_POST['name'] );
	$password = sanitize_text_field( $_POST['password'] );
	$email   = sanitize_email( $_POST['email'] );
//	$db_name = 'wp_users';
	if ( strlen( $name ) > 3 && strlen( $password ) > 5 && strlen( $email ) > 5 ) {
		$last_id = wp_create_user($name, $password, $email);
	} else {
		$last_id = null;
		echo "name should be more than 3 symbol, password 5, email 5";
	}
    file_upload($last_id);
}

// this function is adding img in (wp media) and adding img_src in db
function file_upload( $id ) {
	$upload = wp_upload_bits( $_FILES['image']['name'], null, file_get_contents( $_FILES['image']['tmp_name'] ) );
	if ( ! $upload['error'] ) {
		$filename      = $upload['file'];
		$wp_filetype   = wp_check_filetype( $filename );
		$attachment    = array(
			'post_type'    => $wp_filetype['type'],
			'post_name'    => sanitize_file_name( $filename ),
			'post_content' => '',
			'post_status'  => 'inherit'
		);
		$attachment_id = wp_insert_attachment( $attachment, $filename, '111' );
		if ( !is_wp_error( $attachment_id ) ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
		}
		echo "file uploaded ";
	} else {
		echo $upload['error'];
	}

    $get_updated =  wp_update_user([
            'ID' => $id,
            'user_url' => $upload['url']
    ]);

	if ( is_wp_error( $get_updated ) ) {
		echo "Error is here";
	} else {
		echo "User Email confirm";
	}
}

add_shortcode( "mtp_show_users", "show_table" );
function show_table(): bool|string {
	global $wpdb;

    if(isset($_SESSION['success'])){
        ?><script>alert("life id good ");</script><?php
    }elseif (isset($_SESSION['error'])){
	    ?><script>alert("<?php echo $_SESSION['error']?>");</script><?php
    }
	session_destroy();

    print_r($_SESSION);
	if ( isset( $_GET['last_id'] ) ) {
		$last_id          = $_GET['last_id'];
		$show_users_query = $wpdb->prepare( "SELECT * FROM `wp_users` WHERE ID <= %d", $last_id );
		$arr_from_db      = $wpdb->get_results( $show_users_query );
		$arr_from_db      = array_slice( $arr_from_db, count( $arr_from_db ) - 5, count( $arr_from_db ) );
	} elseif ( isset( $_GET['first_id'] ) ) {
		$first_id         = $_GET['first_id'];
		$show_users_query = $wpdb->prepare( "SELECT * FROM `wp_users` WHERE ID > %d", $first_id );
		$arr_from_db      = $wpdb->get_results( $show_users_query );
	} else {
		$show_users_query = $wpdb->prepare( "SELECT * FROM `wp_users`" );
		$arr_from_db      = $wpdb->get_results( $show_users_query );
		$last_id          = 0;
	}
	ob_start();
	?>
    <div class="container m-0 p-0">
        <div class="row w-100">
            <div >
                <form class="p-0 m-0" method="get" action="<?php echo site_url( 'edit' ) ?>">
                    <input type="hidden" name="action" value="edit_func">
                    <table class="table table-striped table-dark table-hover">
                        <?php
                        for ( $i = 0; $i < 5; $i ++ ) {
                            ?>
                            <tr>
                                <td><?php echo esc_attr( $arr_from_db[ $i ]->ID ) ?></td>
                                <td><?php echo esc_attr( $arr_from_db[ $i ]->display_name ) ?></td>
                                <td><?php echo esc_attr( $arr_from_db[ $i ]->user_email ) ?></td>
                                <td>
                                    <input class="btn btn-hover btn-danger" type="submit" name="edit" value="<?php echo esc_attr( $arr_from_db[ $i ]->ID ) ?>">
                                </td>
                                <td>
                                    <img src="<?php echo ( strlen( $arr_from_db[ $i ]->user_url ) > 0 ) ? esc_attr( $arr_from_db[ $i ]->user_url ) : 'http://localhost/wordpress/wp-content/uploads/2023/03/tomcat.jpg' ?>"
                                         width="50"
                                         height="50" sizes="" srcset="">
                                </td>
                            </tr>
                            <?php
                            $last_id = esc_attr( $arr_from_db[ $i ]->ID );
                        }
                        ?>
                    </table>
                </form>
            </div>
            <div class="row w-100  padding-left-10">
                <form class="w-50 p-0" action="<?php echo site_url( '/users/' ) ?>" method="get">
                    <input type="hidden" name="action" value="prev_pg">
                    <input type="hidden" name="first_id" value="<?php echo $last_id - 10 ?>">
                    <input type="submit" value="prev" class="btn btn-dark w-100">
                </form>
                <form class="w-50 p-0" action="<?php echo site_url( '/users/' ) ?>" method="get">
                    <input type="hidden" name="action" value="next_pg">
                    <input type="hidden" name="last_id" value="<?php echo $last_id + 5 ?>">
                    <input type="submit" value="next" class="btn btn-secondary w-100">
                </form>
            </div>
        </div>
   </div>
	<?php
	return ob_get_clean();
}//show_table func bracket

add_shortcode( "mtp_edit_users", "edit_short" );
function edit_short() {

	$id_from_get = 0;
	if ( isset( $_GET['edit'] ) ) {
		$id_from_get = $_GET['edit'];
	}
	ob_start(); ?>
    <form method="get" enctype="multipart/form-data" action="<?php echo admin_url( 'admin-post.php' ) ?>">
        <input type="hidden" name="action" value="edit">
        <div class="container">
            <div class="col">
                <div class="row w-100 p-0">
                    <label for="name">new name</label>
                    <input class="form-control" type="text" name="name"  id="name">
                </div>
                <div class="row w-100 p-0">
                    <label for="password">new password</label>
                    <input class="form-control" type="text" name="password" id="password">
                </div>
                <div class="row w-100 p-0">
                    <label for="email">new email</label>
                    <input class="form-control" type="text" name="email" id="email">
                </div>
                <div class="row w-100 p-0">
                    <label for="id">current ID</label>
                    <input class="form-control" type="number" name="id" id="id" value="<?php echo $id_from_get ?>" readonly>
                </div>
                <div class="row w-100 p-0">
                    <input type="submit" class="btn btn-primary ">
                </div>
            </div>
        </div>
    </form>
	<?php
	return ob_get_clean();
}

add_action( "admin_post_edit", "edit_func" );
function edit_func() {

	global $wpdb;
	$name    = sanitize_text_field( $_GET['name'] );
	$password = sanitize_text_field( $_GET['password'] );
	$email   = sanitize_email( $_GET['email'] );
	$id      = sanitize_text_field( $_GET['id'] );

	if ( strlen( $name ) >= 3 && strlen( $password ) >= 5 && strlen( $email ) >= 5 ) {
		get_userdata( $id );
		$updated_user_data = wp_update_user([
                'ID' => $id,
                'user_email' => $email,
                'display_name' => $name
        ]);
		if ( is_wp_error( $updated_user_data ) ) {
            $_SESSION['error'] = 'Error' . $updated_user_data->get_error_message();
			$_SESSION['success'] = null;
		} else {
			$_SESSION['success'] = 'updated successfully';
			$_SESSION['error'] = null;
		}
        wp_safe_redirect(site_url('users'));
	} else {
		echo "name shold be more than 3 sibol , surname 5, email 5";
	}
}

// JS part --------------------------------------------------------------------
add_action( 'wp_enqueue_scripts', 'js_script' );
function js_script() {
	wp_enqueue_script( 'custom_script', plugin_dir_url( __FILE__ ) . '/my_test_plugin.js', [ 'jquery' ] );
	wp_localize_script( 'custom_script', 'MYSCRIPT', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
	) );
	wp_enqueue_style( 'style', plugin_dir_url( __FILE__ ) . 'my_test_plugin.css' );
	wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css' );
}

add_shortcode( 'mtp_js_user_registration', 'mtp_js_user_registration' );
function mtp_js_user_registration() {
	$log_url = site_url( "mtp_user_registration" );
	ob_start();
	?>
    <form id="form" enctype="multipart/form-data">
        <div class="container">
            <div class="col">
                <div class="row w-100 p-0">
                    <label for="name">Name</label>
                    <input name="name" id="name" class="form-control">
                </div>
                <div class="row w-100 p-0">
                    <label for="password">password</label>
                    <input name="password" id="password" class="form-control">
                </div>
                <div class="row w-100 p-0">
                    <label for="email">Email</label>
                    <input name="email" id="email" class="form-control">
                </div>
                <div class="row w-100 p-0">
                    <button name="upload_file" id="js_submit_btn" class="btn btn-primary w-100">Register</button>
                </div>
            </div>
        </div>
    </form>
	<?php
	return ob_get_clean();
}

add_action( 'wp_ajax_my_ajax_request', 'adding_with_js' );
function adding_with_js() {
	add_db();
}

?>
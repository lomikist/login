<?php
/**
 * Plugin Name: my_test_plugin
 * Description: this plugin is for saveing a date in date base
 */
/**
 * this func is creat a new sesion if it is not exist
 *
 * @return void
 */
function start_session() {
	if ( ! session_id() ) {
		session_start();
	}
}

add_action( 'init', 'start_session' );
/**
 * this function for showing users login form
 *
 * @return false|string
 */
function show_form() {
	if ( isset( $_SESSION['armplugin']['error'] ) ) {
		?>
        <script> alert("<?php echo esc_attr( $_SESSION['armplugin']['error'] ) ?>") </script><?php
	} elseif ( isset( $_SESSION['armplugin']['success'] ) ) {
		?>
        <script> alert("<?php echo esc_attr( $_SESSION['armplugin']['success'] ) ?>") </script><?php
	}
	session_destroy();
	$login_nonce = esc_attr( wp_create_nonce( 'login_nonce' ) );
	ob_start();
	?>
    <form method="post" id="form" enctype="multipart/form-data" action="<?php echo admin_url( 'admin-post.php' ) ?>">
        <input type="hidden" name="action" value="submit_btn">
		<?php wp_referer_field(); ?>
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
                    <input type="hidden" name="login_nonce" value="<?php echo $login_nonce ?>">
                </div>
            </div>
        </div>
    </form>
	<?php
	return ob_get_clean();
}

add_shortcode( "mtp_user_registration", "show_form" );
/**
 * this function is taking user data and adding it to db , after that call file_upload
 *
 * @return void
 */
function add_db() {

	$refere              = wp_get_referer();
	$name                = sanitize_text_field( $_POST['name'] ) ?? '';
	$password            = sanitize_text_field( $_POST['password'] ) ?? '';
	$email               = sanitize_email( $_POST['email'] ) ?? '';
	$be_checked_nonce    = sanitize_text_field( $_POST['login_nonce'] ?? null );
	$js_be_checked_nonce = sanitize_text_field( $_POST['js_login_nonce'] ?? null );
	if ( isset( $_POST['login_nonce'] ) && ! wp_verify_nonce( $be_checked_nonce, 'login_nonce' ) && ! check_admin_referer( $referer ) ) {
		die( '' );
	}
	if ( isset( $_POST['js_login_nonce'] ) && ! wp_verify_nonce( $js_be_checked_nonce, 'js_login_nonce' ) && ! check_admin_referer( $referer ) ) {
		die( '' );
	}
	if ( strlen( $name ) > 3 && strlen( $password ) > 5 && strlen( $email ) > 5 ) {
		$last_id                          = wp_create_user( $name, $password, $email );
		$_SESSION['armplugin']['success'] = "you are registered";
		$_SESSION['armplugin']['error']   = null;
	} else {
		$last_id                          = null;
		$_SESSION['armplugin']['error']   = "name should be more than 3 symbol, password 5, email 5";
		$_SESSION['armplugin']['success'] = null;
	}
	file_upload( $last_id, $refere );
}

add_action( "admin_post_submit_btn", "add_db" );
/**
 *  this func is taking a last added user id and link url where is come requests,
 *  taking a file data and upload it in wp media ,
 *  after that changing user url to img url
 *
 * @param   int     $id
 * @param   string  $referer
 *
 * @return void
 */
function file_upload( int $id, string $referer ) {

	$parsed_referer = wp_parse_url( $referer );
	$upload         = wp_upload_bits( $_FILES['image']['name'], null, file_get_contents( $_FILES['image']['tmp_name'] ) );
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
		if ( ! is_wp_error( $attachment_id ) ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
		}
	} else {
		$_SESSION['armplugin']['success'] = null;
		$_SESSION['armplugin']['error']   = $upload['error'];
	}
	$get_updated = wp_update_user( [
		'ID'       => $id,
		'user_url' => $upload['url']
	] );
	if ( is_wp_error( $get_updated ) ) {
		$_SESSION['armplugin']['success'] = null;
		$_SESSION['armplugin']['error']   = $get_updated->get_error_message();
	} else {
		$_SESSION['armplugin']['success'] = "updated";
		$_SESSION['armplugin']['error']   = null;
	}
	wp_safe_redirect( site_url( $parsed_referer['path'] ) );
}

/**
 * this function for drawing a users table ,
 * get data from db and after that generating a table
 *
 * @return false|string
 */
function show_table() {
	if ( isset( $_SESSION['armplugin']['error'] ) ) {
		?>
        <script> alert("<?php echo esc_attr( $_SESSION['armplugin']['error'] ) ?>") </script><?php
	} elseif ( isset( $_SESSION['armplugin']['success'] ) ) {
		?>
        <script> alert("<?php echo esc_attr( $_SESSION['armplugin']['success'] ) ?>") </script><?php
	}
	session_destroy();
	$current_page   = ! empty( $_GET['current'] );
	$row_count      = count_users()['total_users'];
	$row_per_page   = 3;
	$number_of_page = ceil( $row_count / $row_per_page );
	$initial_page   = $current_page * $row_per_page;
	echo $number_of_page;
	$arr_from_db = json_decode( json_encode( get_users( array(
		'offset'  => $initial_page,
		'number'  => $row_per_page,
		'orderby' => 'ID',
	) ) ), true );
	ob_start();
	?>
    <div class="container m-0 p-0">
        <div class="row w-100">
            <div>
                <form class="p-0 m-0" method="get" action="<?php echo site_url( 'edit' ) ?>">
                    <input type="hidden" name="action" value="edit_func">
                    <table class="table table-striped table-dark table-hover">
						<?php
						foreach ( $arr_from_db as $key ) {
							?>
                            <tr>
                                <td><?php echo esc_attr( $key['data']['ID'] ?? '' ) ?></td>
                                <td><?php echo esc_attr( $key['data']['display_name'] ?? '' ) ?></td>
                                <td><?php echo esc_attr( $key['data']['user_email'] ?? '' ) ?></td>
                                <td>
                                    <input class="btn btn-hover btn-danger" type="submit" name="edit" value="<?php echo esc_attr( $key['data']['ID'] ?? '' ) ?>">
                                </td>
                                <td>
                                    <img src="<?php echo ( strlen( esc_attr( $key['data']['user_url'] ) ) > 0 ) ? esc_attr( $key['data']['user_url'] ) : 'http://localhost/wordpress/wp-content/uploads/2023/03/tomcat
                                    .jpg' ?>" width="50" height="50" sizes="" srcset="">
                                </td>
                            </tr>
							<?php
						}
						?>
                    </table>
                </form>
            </div>
            <div class="row w-100  padding-left-10">
                <form class="w-50 p-0" action="" method="get">

                    <input type="submit" class="btn-success" name="current" value="<?php echo $current_page == 0 ? 0 : ( $current_page == ( $number_of_page - 1 ) ? ( $number_of_page - 2 ) : $current_page - 1 ) ?>">
					<?php
					for ( $i = 0; $i < $number_of_page; ++ $i ) {
						?><input type="submit" class="btn-primary" name="current" value='<?php echo $i ?>'><?php
					}
					?>
                    <input type="submit" class="btn-danger" name="current" value="<?php echo $current_page == 0 ? 1 : ( $current_page == ( $number_of_page - 1 ) ? ( $number_of_page - 1 ) : $current_page + 1 ) ?>">
                </form>
            </div>
        </div>
    </div>
	<?php
	return ob_get_clean();
}

add_shortcode( "mtp_show_users", "show_table" );
/**
 * this func take an edit id and generate a form basing an id,
 * and sed a request admin-post
 *
 * @return false|string
 */
function edit_short() {

	$id_from_get = esc_attr( $_GET['edit'] ?? 0 );
	ob_start(); ?>
    <form method="get" enctype="multipart/form-data" action="<?php echo admin_url( 'admin-post.php' ) ?>">
        <input type="hidden" name="action" value="edit">
        <div class="container">
            <div class="col">
                <div class="row w-100 p-0">
                    <label for="name">new name</label>
                    <input class="form-control" type="text" name="name" id="name">
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
                    <input class="form-control" type="number" name="id" id="id" value="<?php echo esc_attr( $id_from_get ) ?>" readonly>
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

add_shortcode( "mtp_edit_users", "edit_short" );
/**
 * taking a changed user data and changing that
 *
 * @return void
 */
function edit_func() {
	$name     = sanitize_text_field( $_GET['name'] ) ?? '';
	$password = sanitize_text_field( $_GET['password'] ) ?? '';
	$email    = sanitize_email( $_GET['email'] ) ?? '';
	$id       = sanitize_text_field( $_GET['id'] ) ?? '';
	if ( strlen( $name ) >= 3 && strlen( $password ) >= 5 && strlen( $email ) >= 5 ) {
		get_userdata( $id );
		$updated_user_data = wp_update_user( [
			'ID'           => $id,
			'user_email'   => $email,
			'display_name' => $name
		] );
		if ( is_wp_error( $updated_user_data ) ) {
			$_SESSION['armplugin']['error']   = 'Error' . $updated_user_data->get_error_message();
			$_SESSION['armplugin']['success'] = null;
		} else {
			$_SESSION['armplugin']['success'] = 'updated successfully';
			$_SESSION['armplugin']['error']   = null;
		}
		wp_safe_redirect( site_url( 'users' ) );
	} else {
		$_SESSION['armplugin']['error']   = 'name should be more that .....';
		$_SESSION['armplugin']['success'] = null;
	}
}

add_action( "admin_post_edit", "edit_func" );
/**
 * this func for assets a script files and style files
 *
 * @return void
 */
function js_script() {

	wp_enqueue_script( 'custom_script', plugin_dir_url( __FILE__ ) . '/my_test_plugin.js', [ 'jquery' ] );
	wp_localize_script( 'custom_script', 'MYSCRIPT', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => esc_attr( wp_create_nonce( 'js_login_nonce' ) )
	) );
	wp_enqueue_style( 'style', plugin_dir_url( __FILE__ ) . 'my_test_plugin.css' );
	wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css' );
}

add_action( 'wp_enqueue_scripts', 'js_script' );
/**
 * for generating a registration form
 *
 * @return false|string
 */
function mtp_js_user_registration() {
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

add_shortcode( 'mtp_js_user_registration', 'mtp_js_user_registration' );
/**
 * This func call a add_db func witch adding a users in db ,
 * after then when request are send
 *
 * @return void
 */
function adding_with_js() {
	add_db();
}

add_action( 'wp_ajax_my_ajax_request', 'adding_with_js' );
?>
<?php
/**
 * Plugin Name: my_test_plugin
 * Description: this plugin is for saveing a date in date base
 */
add_shortcode( "mtp_user_registration", "show_form" );
function show_form() {
	$log_url = site_url( "mtp_user_registration" );
	ob_start();
	?>
    <form method="post" id="form" enctype="multipart/form-data" action="<?php echo admin_url( 'admin-post.php' ) ?>">
        <label for="name">Name</label>
        <input type="hidden" name="action" value="submit_btn">
        <input name="name">
        <br/>
        <label for="surname">Surmane</label>
        <input name="surname">
        <br/>
        <label for="email">Email</label>
        <input name="email">
        <br/>
        <label for="upload_img">upload img</label>
        <input type="file" name="image">

        <br/>
        <label for="submitBtn"></label>
        <input type="submit" name="upload_file" value="submit">
    </form>
	<?php
	return ob_get_clean();
}

function add_db() {
	global $wpdb;
	$name    = sanitize_text_field( $_POST['name'] );
	$surname = sanitize_text_field( $_POST['surname'] );
	$email   = sanitize_email( $_POST['email'] );
	$db_name = $wpdb->prefix . 'info';
	if ( strlen( $name ) > 3 && strlen( $surname ) > 5 && strlen( $email ) > 5 ) {
		$data = array(
			'name1'   => $name,
			'surname' => $surname,
			'email'   => $email
		);
		if ( $wpdb->insert( $db_name, $data ) ) {
			echo "data is added";
		} else {
			echo $wpdb->last_error;
		}
	} else {
		echo "name shousld be more than 3 sibol , surname 5, email 5";
	}
    if (count($_FILES) > 0) {
	    $lastid = $wpdb->insert_id; //above mentioned, user id
	    file_upload( $lastid );
    };
}

add_action( "admin_post_submit_btn", "add_db" );


// this function is adding img in (wp media) and adding img_src in db
function file_upload( $id ) {
    print_r(1234);
	global $wpdb;
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
		if ( ! is_wp_error( $attachment_id ) ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
		}
		echo "file uploaded ";
	} else {
		echo $upload['error'];
	}
	// here were gonna chaneg user img_url with uploaded current file url
	$arr       = array(
		'img_src' => $upload['url'],
	);
	$arr_which = array(
		'id' => $id,
	);
	if ( $wpdb->update( 'wp_info', $arr, $arr_which ) ) {
		echo 'image also was uploaded';
	} else {
		print_r( $wpdb->last_error );
	}
	//----------------
}

function show_table() {
	global $wpdb;
	if ( isset($_GET['last_id'])) {
		$last_id = $_GET['last_id'];
		$show_users_query = $wpdb->prepare( "SELECT * FROM `wp_info` WHERE id <= %d", $last_id );
		$arr_from_db      = $wpdb->get_results( $show_users_query );
        $arr_from_db      = array_slice($arr_from_db, count($arr_from_db) - 5, count($arr_from_db));
	}else if ( isset($_GET['first_id'])) {
		$first_id = $_GET['first_id'];
		$show_users_query = $wpdb->prepare( "SELECT * FROM `wp_info` WHERE id >= %d", $first_id );
		$arr_from_db      = $wpdb->get_results( $show_users_query );
	}else {
		$show_users_query = $wpdb->prepare( "SELECT * FROM `wp_info`");
		$arr_from_db      = $wpdb->get_results( $show_users_query );
		$last_id = 0;
	}
    ob_start();
	?>
    <form method="get" action="<?php echo admin_url( 'admin-post.php' ) ?>">
        <input type="hidden" name="action" value="edit_short">
        <table class="table table-striped table-dark" >
			<?php
				for ( $i = 0; $i < 5; $i ++ ) {
					?>
                    <tr>
                        <td ><?php echo esc_attr( $arr_from_db[ $i ]->id ) ?></td>
                        <td ><?php echo esc_attr( $arr_from_db[ $i ]->name1 ) ?></td>
                        <td ><?php echo esc_attr( $arr_from_db[ $i ]->surname ) ?></td>
                        <td ><?php echo esc_attr( $arr_from_db[ $i ]->email ) ?></td>
                        <td >
                            <input type="submit" name="edit" value="<?php echo esc_attr( $arr_from_db[ $i ]->id ) ?>">
                        </td>
                        <td>
                            <img src="<?php echo esc_attr( $arr_from_db[ $i ]->img_src) ?>" alt="" width="50" height="50" sizes="" srcset="">
                        </td>
                    </tr>
					<?php
					$last_id = esc_attr( $arr_from_db[ $i ]->id );
			    }
            ?>
            <input type="hidden" name="last_id" value="<?php echo $last_id ?>">
        </table>
    </form>

    <form action="<?php echo site_url('/users/')?>" method="get">
        <input type="hidden" name="action" value="next_pg">
        <input type="hidden" name="last_id" value="<?php echo $last_id + 5?>">
        <input type="submit" value="next">
    </form>
    <form action="<?php echo site_url('/users/')?>" method="get">
        <input type="hidden" name="action" value="prev_pg">
        <input type="hidden" name="first_id" value="<?php echo $last_id - 10?>">
        <input type="submit" value="prev">
    </form>
	<?php
	return ob_get_clean();
}//show_table func bracket
add_shortcode( "mtp_show_users", "show_table" );

function show_next() {
	echo "show_next";
	global $wpdb;
	$last_id = 0;
	// print_r($_GET);
	$show_users_query = $wpdb->prepare( "SELECT * FROM `wp_info` WHERE id > %d", $_GET['last_id'] );
	if ( $_GET['last_id'] == '' ) {
		$first_id    = (string) ( (int) $_GET['last_id'] + 1 );//cuz last id is the previus previus page last_id
		$arr_from_db = $wpdb->get_results( $show_users_query );
	} else {
		$first_id    = $_GET['last_id'];
		$arr_from_db = $wpdb->get_results( $show_users_query );
	}
}
add_action( "admin_post_next_pg", 'show_next' );

//function show_prev() {
//	echo "show_prev";
//	global $wpdb;
//	$last_id          = (int) $_GET['first_id'];
//	$show_users_query = $wpdb->prepare( "SELECT * FROM wp_info WHERE id <= %d", $last_id );
//	$arr_from_db      = $wpdb->get_results( $show_users_query );
//	$arr_for_show     = array_slice( $arr_from_db, count( $arr_from_db ) - 5, count( $arr_from_db ) );
//}// show_prev func brackets
//add_action( 'admin_post_prev_pg', 'show_prev' );

function edit_short() {
	$id_from_get = 0;
	$id_from_get = $_GET['edit'];
	ob_start(); ?>
    <form method="get" action="<?php echo admin_url( 'admin-post.php' ) ?>">
        <input type="hidden" name="action" value="edit_users">

        <label for="name">new name </label>
        <input type="text" name="name" id="name"><br>

        <label for="surname">new surname</label>
        <input type="text" name="surname" id="surname"><br>

        <label for="email">new email</label>
        <input type="text" name="email" id="email"><br>

        <label for="id">current ID</label>
        <input type="number" name="id" id="id" value="<?php echo $id_from_get ?>" readonly><br>

        <input type="submit">
    </form>
	<?php
	echo ob_get_clean();
}
add_action( 'admin_post_edit_short', 'edit_short' );

function edit_users() {
	global $wpdb;
	$name    = sanitize_text_field( $_GET['name'] );
	$surname = sanitize_text_field( $_GET['surname'] );
	$email   = sanitize_email( $_GET['email'] );
	$id      = sanitize_text_field( $_GET['id'] );
	if ( strlen( $name ) >= 3 && strlen( $surname ) >= 5 && strlen( $email ) >= 5 ) {
		$arr       = array(
			'name1'   => $name,
			'surname' => $surname,
			'email'   => $email
		);
		$arr_which = array(
			'id' => $id,
		);
		if ( $wpdb->update( $wpdb->prefix . 'info', $arr, $arr_which ) ) {
			echo 'data was sucsefully changed';
		} else {
			print_r( $wpdb->last_error );
		}
	} else {
		echo "name shold be more than 3 sibol , surname 5, email 5";
	}
	?>
    <form action="<?php echo site_url( "users" ) ?>" method="get">
        <input type="submit" value='users page' class="btn btn-outline-secondary">
    </form>
	<?php
}
add_action( "admin_post_edit_users", "edit_users" );

// JS part --------------------------------------------------------------------
add_action( 'wp_enqueue_scripts', 'js_script' );

function js_script() {
	wp_enqueue_script( 'custom_script', plugin_dir_url( __FILE__ ) . '/my_test_plugin.js', [ 'jquery' ] );
	wp_localize_script( 'custom_script', 'MYSCRIPT', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
	) );
    wp_enqueue_style('style', plugin_dir_url( __FILE__ ).'my_test_plugin.css');
	wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css' );

}

add_shortcode( 'mtp_js_user_registration', 'mtp_js_user_registration' );
function mtp_js_user_registration() {
	$log_url = site_url( "mtp_user_registration" );
	ob_start();
	?>
    <form id="form" enctype="multipart/form-data">
        <label for="name">Name</label>
        <input name="name" id="name"  class="form-control">
        <label for="surname">Surmane</label>
        <input name="surname" id="surname"  class="form-control">
        <label for="email">Email</label>
        <input name="email" id="email"  class="form-control" >
<!--        <label for="upload_img">upload img</label>-->
<!--        <input type="file" id="file">-->
<!--        <br/>-->
        <label for="submitBtn"></label>
        <button name="upload_file" id="js_submit_btn" class="btn btn-primary"> register</button>
    </form>
	<?php
	return ob_get_clean();
}

add_action( 'wp_ajax_my_ajax_request', 'adding_with_js' );
add_action( 'wp_ajax_nopriv_my_ajax_request', 'adding_with_js' );

function adding_with_js() {
	add_db();
}
?>
<?php
/**
 * Plugin Name: my_test_plugin
 * Description: this plugin is for saveing a date in date base
 */
global $sum;

add_shortcode( "log_form_short", "show_form" );

function show_form() {
	$log_url = site_url( "log_form_short" );
	ob_start();
	?>
    <form method='post' id='form' enctype="multipart/form-data" action='<?php echo admin_url( 'admin-post.php' ) ?>'>
        <label for='name'>Name</label>
        <input type='hidden' name='action' value='submit_btn'>
        <input name='name'>
        <br/>
        <label for='surname'>Surmane</label>
        <input name='surname'>
        <br/>
        <label for='email'>Email</label>
        <input name='email'>
        <br/>
        <label for='upload_img'>upload img</label>
        <input type="file" name="image">

        <br/>
        <label for='submitBtn'></label>
        <input type='submit' name='upload_file' value="submit">
    </form>
	<?php
	return ob_get_clean();
}

function add_db() {
	global $wpdb;
	$name    = sanitize_text_field( $_POST['name'] );
	$surname = sanitize_text_field( $_POST['surname'] );
	$email   = sanitize_email( $_POST['email'] );

	// echo $img_url;
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
	$lastid = $wpdb->insert_id; //above mentioned, user id
	do_action( 'file_upload', $lastid);
}

add_action( "admin_post_submit_btn", "add_db" );
add_action( 'file_upload', 'file_upload' );

function file_upload($id) {
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
	$arr = array(
		'img_src' => $upload['url'],
	);
	$arr_which = array(
		'id' => $id,
	);
	if ( $wpdb->update( $wpdb->prefix.'info', $arr, $arr_which ) ) {
		echo 'image also was uploaded';
	} else {
		print_r($wpdb->last_error);
	}
	//----------------
}

add_shortcode( "show_table_short", "show_table" );
function show_table() {
	global $wpdb;
	$last_id     = 0;
	$change_url  = site_url( "cahnge_short" );
	$arr_from_db = $wpdb->get_results( "SELECT * FROM wp_info WHERE id < 100" );
	ob_start();
	?>
    <form method='get' action='<?php echo admin_url( 'admin-post.php' )?>'>
	<input type='hidden' name='action' value='edit_short'>
        <table style=border: 1px solid black;>
			<?php
			if ( count( $arr_from_db ) >= 5 ) {
				for ( $i = 0; $i < 5; $i ++ ) {
					$real_id      = esc_attr( $arr_from_db[ $i ]->id );
					$real_name1   = esc_attr( $arr_from_db[ $i ]->name1 );
					$real_surname = esc_attr( $arr_from_db[ $i ]->surname );
					$real_email   = esc_attr( $arr_from_db[ $i ]->email );
					$img_url 	  = $arr_from_db[ $i ]->img_src;

					echo 1;
					?>
                    <tr>
                        <td><?php echo $real_id ?></td>
                        <td><?php echo $real_name1 ?></td>
                        <td><?php echo $real_surname ?></td>
                        <td><?php echo $real_email ?></td>
                        <td>
                            <input type='submit' name='edit' value='<?php echo $real_id ?>'>
                        </td>
                    </tr>
					<?php
					$last_id = $real_id;
				}
			} else {
				for ( $i = 0; $i < count( $arr_from_db ); $i ++ ) {
					$real_id      = esc_attr( $arr_from_db[ $i ]->id );
					$real_name1   = esc_attr( $arr_from_db[ $i ]->name1 );
					$real_surname = esc_attr( $arr_from_db[ $i ]->surname );
					$real_email   = esc_attr( $arr_from_db[ $i ]->email );
					$img_url 	  = $arr_from_db[ $i ]->img_src;
					?>
                    <tr>
                        <td><?php echo $real_id ?></td>
                        <td><?php echo $real_name1 ?></td>
                        <td><?php echo $real_surname ?></td>
                        <td><?php echo $real_email ?></td>
                        <td>
                            <input type='submit' name='edit' value='<?php echo $real_id ?>'>
                        </td>
						<td><img src="<?php echo $img_url?>" alt="" width='100' height='100' sizes="" srcset=""></td>
                    </tr>
				<?php }?><!--for loop bracket-->
			<?php }?><!--else bracket-->

            <input type='hidden' name='last_id' value='<?php echo $last_id ?>'>
		</table>
    </form>
    <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method='get'>
        <input type='hidden' name='action' value='next_pg'>
        <input type='hidden' name='last_id' value='<?php echo $last_id ?>'>
        <input type='submit' value='next'>
        <!--        <input type='submit' value='prev' >-->
    </form>
	<?php
	return ob_get_clean();
}//show_table func bracket 

function show_next() {
	echo "show_next";
	global $wpdb;
	$last_id = 0;
	// print_r($_GET);
	echo( $_GET['last_id'] == '' );
	if ( $_GET['last_id'] == '' ) {
		$first_id    = (string) ( (int) $_GET['last_id'] + 1 );//cuz last id is the previus previus page last_id
		$arr_from_db = $wpdb->get_results( "SELECT * FROM wp_info WHERE id > '" . $_GET['last_id'] . "'" );
	} else {
		$first_id    = $_GET['last_id'];
		$arr_from_db = $wpdb->get_results( "SELECT * FROM wp_info WHERE id > '" . $_GET['last_id'] . "'" );
	}
	?>
    <form method='get' action='<?php echo admin_url( 'admin-post.php' )?>'>
	<input type='hidden' name='action' value='edit_short'>
        <table style=border: 1px solid black;>
			<tbody>
			<?php
			for ( $i = 0; $i < 5; $i ++ ) {
			//cuz if arr_from_db dosent exist its , anyway he would print something
			if ( isset( $arr_from_db[ $i ] ) ) {
				$real_id      = esc_attr( $arr_from_db[ $i ]->id );
				$real_name1   = esc_attr( $arr_from_db[ $i ]->name1 );
				$real_surname = esc_attr( $arr_from_db[ $i ]->surname );
				$real_email   = esc_attr( $arr_from_db[ $i ]->email );
				$img_url 	  = $arr_from_db[ $i ]->img_src;
				$last_id      = $real_id;
			} else {
				$real_id      = 'no member';
				$real_name1   = '';
				$real_surname = '';
				$real_email   = '';
				$last_id      = 0;
			}
			?>
            <tr>
                <td><?php echo $real_id ?></td>
                <td><?php echo $real_name1 ?></td>
                <td><?php echo $real_surname ?></td>
                <td><?php echo $real_email ?></td>
                <td>
                    <input type='submit' readonly name='edit' value='<?php echo $real_id ?>'>
                </td>
				<td><img src="<?php echo $img_url?>" alt="" width='100' height='100' sizes="" srcset=""></td>
            </tr><br>
			<?php
		} ?> <!--for loop bracket-->
        </table>
    </form> <!--always use after bracket -->
    <form action='<?php echo admin_url( 'admin-post.php' ) ?>' method='get'>
        <input type='hidden' name='action' value='next_pg'>
        <input type='hidden' name='last_id' value='<?php echo $last_id ?>'>
        <input type='submit' value='next'>
    </form>
    <form action='<?php echo admin_url( 'admin-post.php' ) ?>' method='get'>
        <input type='hidden' name='action' value='prev_pg'>
        <input type='hidden' name='first_id' value='<?php echo $first_id ?>'>
        <input type='submit' value='prev'>
    </form>
	<?php
}// show_next function bracket

add_action( "admin_post_next_pg", 'show_next' );

function show_prev() {
	echo "show_prev";
	global $wpdb;
	$last_id = (int) $_GET['first_id'];
	$arr_from_db = $wpdb->get_results( "SELECT * FROM wp_info WHERE id <= $last_id" );
	$arr_from_db = array_reverse( $arr_from_db );
	?>
    <form method='get' action='<?php echo admin_url( 'admin-post.php' )?>'>
	<input type='hidden' name='action' value='edit_short'>
        <table style=border: 1px solid black;>
			<?php
			for ( $i = 0; $i < 5; $i ++ ){
			if ( isset( $arr_from_db[ $i ] ) ) {
				$real_id      = esc_attr( $arr_from_db[ $i ]->id );
				$real_name1   = esc_attr( $arr_from_db[ $i ]->name1 );
				$real_surname = esc_attr( $arr_from_db[ $i ]->surname );
				$real_email   = esc_attr( $arr_from_db[ $i ]->email );
				$img_url 	  = $arr_from_db[ $i ]->img_src;
				$first_id     = $real_id - 1;
			} else {
				$real_id      = 'no member';
				$real_name1   = '';
				$real_surname = '';
				$real_email   = '';
				$last_id      = 0;
				$first_id     = 0;
			}
			?>
            <tr>
                <td><?php echo $real_id ?></td>
                <td><?php echo $real_name1 ?></td>
                <td><?php echo $real_surname ?></td>
                <td><?php echo $real_email ?></td>
                <td>
                    <input type='submit' readonly name='edit' value='<?php echo $real_id ?>'>
                </td>
				<td><img src="<?php echo $img_url?>" alt="" width='100' height='100'></td>
            </tr>
            <br>
			<?php
		} ?> <!--for loop bracket-->
		</table>

    </form> <!--always use after bracket -->
    <form action='<?php echo admin_url( 'admin-post.php' ) ?>' method='get'>
        <input type='hidden' name='action' value='next_pg'>
        <input type='hidden' name='last_id' value='<?php echo $last_id ?>'>
        <input type='submit' value='next'>
    </form>
    <form action='<?php echo admin_url( 'admin-post.php' ) ?>' method='get'>
        <input type='hidden' name='action' value='prev_pg'>
        <input type='hidden' name='first_id' value='<?php echo $first_id ?>'>
        <input type='submit' value='prev'>
    </form>
	<?php
}// show_prev func brackets

add_action( "admin_post_prev_pg", 'show_prev' );

add_action("admin_post_edit_short", "edit_short");

function edit_short() {
	$id_from_get = 0;
	$id_from_get = $_GET['edit'];
	global $wpdb;

	$row_id = $wpdb->get_results( "SELECT * FROM wp_info WHERE id = '$id_from_get'" );

	foreach ( $row_id as $key => $value ) {
		print_r( "Your current id is - " . $value->id . " write it in input " );
		$row_id = $value->id;
	}

	ob_start(); ?>
    <form method='get' action='<?php echo admin_url( 'admin-post.php' )?>'>
	<input type='hidden' name='action' value='edit_users'>

        <label for='name'>new name </label>
        <input type='text' name='name' id='name'><br>

        <label for='surname'>new surname</label>
        <input type='text' name='surname' id='surname'><br>

        <label for='email'>new email</label>
        <input type='text' name='email' id='email'><br>

        <label for='id'>current ID</label>
        <input type='number' name='id' id='id' value="<?php echo $id_from_get ?>" readonly><br>

        <input type='submit'>
    </form>
	<?php

	echo ob_get_clean();
}

add_action("admin_post_edit_users", "edit_users");

function edit_users(){
	global $wpdb;
	$name    = sanitize_text_field( $_GET['name'] );
	$surname = sanitize_text_field( $_GET['surname'] );
	$email   = sanitize_email( $_GET['email'] );
	$id 	 = sanitize_text_field( $_GET['id'] );

	if ( strlen( $name ) >= 3 && strlen( $surname ) >= 5 && strlen( $email ) >= 5 ) {
		$arr       = array(
			'name1'   => $name,
			'surname' => $surname,
			'email'   => $email
		);
		$arr_which = array(
			'id' => $id,
		);
		if ( $wpdb->update( $wpdb->prefix.'info', $arr, $arr_which ) ) {
			echo 'data was sucsefully changed';
		} else {
			print_r($wpdb->last_error);
		}
	} else {
		echo "name shold be more than 3 sibol , surname 5, email 5";
	}
	?>
	<form action="<?php echo site_url("users")?>" method="get">
		<input type="submit" value = 'users page'>
	</form>
	<?php
}
?>
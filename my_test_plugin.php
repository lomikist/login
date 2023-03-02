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
	?><form method = 'post' id = 'form' action='<?php echo admin_url( 'admin-post.php' )?>'>
        <label for='name'>Name</label>   
        <input type='hidden' name = 'action' value = 'submit_btn'>
        <input name = 'name' >
        <br/>    
        <label for='surname'>Surmane</label>
        <input name = 'surname' >  
        <br/>
        <label for='email'>Email</label> 
        <input name = 'email' >
        <br/>
        <label for='upload_img'>upload img</label>
        <input type="file" name="upload_img" id="upload_img">
        <br/>
        <label for='submitBtn'></label>
        <input type = 'submit'>
        </form>
<?php
	return ob_get_clean();
}

function add_db() {
	global $wpdb;
	$name    = sanitize_text_field( $_POST['name'] );
	$surname = sanitize_text_field( $_POST['surname'] );
	$email   = sanitize_email( $_POST['email'] );
	$db_name = $wpdb->prefix;
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
}

add_action( "admin_post_submit_btn", "add_db" );
add_shortcode( "show_table_short", "show_table" );
function show_table() {
	global $wpdb;
	$last_id     = 0;
	$change_url  = site_url( "cahnge_short" );
	$arr_from_db = $wpdb->get_results( "SELECT * FROM wp_info WHERE id < 100" );
	ob_start();
	?>
    <form action='<?php echo $change_url ?>' method='get'>
        <table style=border: 1px solid black;>
			<?php
			if ( count( $arr_from_db ) >= 10 ) {
				for ( $i = 0; $i < 10; $i ++ ) {
					$real_id      = esc_attr( $arr_from_db[ $i ]->id );
					$real_name1   = esc_attr( $arr_from_db[ $i ]->name1 );
					$real_surname = esc_attr( $arr_from_db[ $i ]->surname );
					$real_email   = esc_attr( $arr_from_db[ $i ]->email );
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
				<?php }
			} ?>
            <input type='hidden' name='last_id' value='<?php echo $last_id ?>'>
        </table>
    </form>
    <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method='get'>
        <input type='hidden' name='action' value='next_pg'>
        <input type='submit' value='next'>
        <input type='submit' value='prev'>
    </form>
	<?php
	return ob_get_clean();
}

function show_next() {
	echo "show_next";
	global $wpdb;
	$last_id  = 0;
	$first_id = (string) ( (int) $_GET['last_id'] + 1 );//cuz last id is the previus previus page last_id
	$arr_from_db = $wpdb->get_results( "SELECT * FROM wp_info WHERE id > '" . $_GET['last_id'] . "'" );
	?>
    <form action="<?php echo site_url( "cahnge_short" ) ?>" method='get'>
        <table style=border: 1px solid black;>
			<?php
			for ( $i = 0;
			$i < 10;
			$i ++ ) {
			$real_id      = esc_attr( $arr_from_db[ $i ]->id );
			$real_name1   = esc_attr( $arr_from_db[ $i ]->name1 );
			$real_surname = esc_attr( $arr_from_db[ $i ]->surname );
			$real_email   = esc_attr( $arr_from_db[ $i ]->email );
			$last_id      = $real_id;
			?>
            <tr>
                <td><?php echo $real_id ?></td>
                <td><?php echo $real_name1 ?></td>
                <td><?php echo $real_surname ?></td>
                <td><?php echo $real_email ?></td>
                <td>
                    <input type='submit' readonly name='edit' value='<?php echo $real_id ?>'>
                </td>

            </tr>
            <br>
        </table>
		<?php
		} ?> <!--for loop bracket-->

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
	$first_id    = (int) $_GET['first_id'];
	$arr_from_db = $wpdb->get_results( "SELECT * FROM wp_info WHERE id < '" . $_GET['first_id'] . "'" );
	$arr_from_db = array_reverse( $arr_from_db, true );
	?>
    <form action="<?php echo site_url( "cahnge_short" ) ?>" method='get'>
        <table style=border: 1px solid black;>
			<?php
			$id_minusten = $first_id - 10;
			for ( $i = $first_id;
			$i > $id_minusten;
			$i -- ){
			$real_id      = esc_attr( $arr_from_db[ $i - 2 ]->id );
			$real_name1   = esc_attr( $arr_from_db[ $i - 2 ]->name1 );
			$real_surname = esc_attr( $arr_from_db[ $i - 2 ]->surname );
			$real_email   = esc_attr( $arr_from_db[ $i - 2 ]->email );
			$first_id     = $real_id;
			?>
            <tr>
                <td><?php echo $real_id ?></td>
                <td><?php echo $real_name1 ?></td>
                <td><?php echo $real_surname ?></td>
                <td><?php echo $real_email ?></td>
                <td>
                    <input type='submit' readonly name='edit' value='<?php echo $real_id ?>'>
                </td>
            </tr>
            <br>
        </table>
		<?php
		} ?> <!--for loop bracket-->

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
}

add_action( "admin_post_prev_pg", 'show_prev' );
?>
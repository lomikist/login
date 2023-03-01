<!--<style>-->
<!--table, th, td {-->
<!--    border:1px solid black;-->
<!--}-->
<!--</style>-->

<?php
/**
 * Plugin Name: my_test_plugin
 * Description: this plugin is for saveing a date in date base
 */

global $sum;

add_shortcode( "log_form_short", "show_form" );
function show_form() {
	$log_url = site_url( "log_form_short" );
	ob_start( );
	echo "<form method = 'post' id = 'form' action='".admin_url( 'admin-post.php' )."'>
        <label for='name'>Name</label>   
        <input type='hidden' name = 'action' value = 'submit_btn'>
        <input name = 'name' id = 'name'>
        <br/>    
        <label for='surname'>Surmane</label>
        <input name = 'surname' id = 'surname'>  
        <br/>
        <label for='email'>Email</label> 
        <input name = 'email' id = 'email'>
        <br/>    
        <label for='submitBtn'></label>
        <input type = 'submit' id = 'submit_btn'>
        </form>";

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
	$last_id = 0;

	$change_url  = site_url( "cahnge_short" );

	$arr_from_db = $wpdb->get_results( "SELECT * FROM wp_info WHERE id < 100" );

	ob_start();

	echo "
            <form action='$change_url' method = 'get'>
            <table style = border: 1px solid black;>";

	if ( count( $arr_from_db ) >= 10 ) {
		for ( $i = 0; $i < 10; $i ++ ) {
            $real_id      = esc_attr( $arr_from_db[ $i ]->id );
			$real_name1   = esc_attr( $arr_from_db[ $i ]->name1 );
			$real_surname = esc_attr( $arr_from_db[ $i ]->surname );
			$real_email   = esc_attr( $arr_from_db[ $i ]->email );
			echo "<tr>
                    <td>$real_id</td>
                    <td>$real_name1</td>
                    <td>$real_surname</td>
                    <td>$real_email</td>
                    <td>
                        <input type='submit' value = 'edit' name = '$real_id'>
                    </td>
                </tr>";
            $last_id = $real_id;
		}
	} else {
		for ( $i = 0; $i < count( $arr_from_db ); $i ++ ) {
			$real_id      = esc_attr( $arr_from_db[ $i ]->id );
			$real_name1   = esc_attr( $arr_from_db[ $i ]->name1 );
			$real_surname = esc_attr( $arr_from_db[ $i ]->surname );
			$real_email   = esc_attr( $arr_from_db[ $i ]->email );
			echo "<tr>
                    <td>$real_id</td>
                    <td>$real_name1</td>
                    <td>$real_surname</td>
                    <td>$real_email</td>
                    <td>
                        <input type='submit' value = 'edit' name = '$real_id'>
                    </td>
                </tr>";
		}
	}
	echo "<input type='hidden' name = 'last_id' value = '$last_id'>
        </table>
    </form>
     <form action=".admin_url( 'admin-post.php' )." method = 'get' >
        <input type='hidden' name = 'action' value = 'next_pg'>
        <input type='submit' value='next'>
      </form>";
	return ob_get_clean();
}


function show_next() {
	global $wpdb;
    $last_id = 0;
	$arr_from_db = $wpdb->get_results( "SELECT * FROM wp_info WHERE id > '".$_GET['last_id']."'" );

	echo "<form action='".site_url( "cahnge_short" )."' method = 'get'>
            <table style = border: 1px solid black;>";

        for ( $i = 0; $i < 10; $i ++ ) {
            $real_id      = esc_attr( $arr_from_db[ $i ]->id );
            $real_name1   = esc_attr( $arr_from_db[ $i ]->name1 );
            $real_surname = esc_attr( $arr_from_db[ $i ]->surname );
            $real_email   = esc_attr( $arr_from_db[ $i ]->email );
            $last_id = $real_id;
            echo "<tr>
                        <td>$real_id</td>
                        <td>$real_name1</td>
                        <td>$real_surname</td>
                        <td>$real_email</td>
                        <td>
                            <input type='submit' value = 'edit' name = '$real_id'>
                        </td>
                    </tr>";
        }

	echo    "
        </table>
    </form>
    <form action=".admin_url( 'admin-post.php' )." method = 'get' >
        <input type='hidden' name = 'action' value = 'next_pg'>
        <input type='hidden' name = 'last_id' value = '$last_id'>
        <input type='submit' value = 'next'>
    </form>";
}

add_action( "admin_post_next_pg", 'show_next' );

?>
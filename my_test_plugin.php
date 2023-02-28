<style>
table, th, td {
    border:1px solid black;
}
</style>

<?php
/**
 * Plugin Name: my_test_plugin
 * Description: this plugin i for saveing a date in date base 
 */

add_shortcode("log_form_short", "show_form");

function show_form()
{
	$log_url = site_url( "log_form_short" );

    ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE ^ PHP_OUTPUT_HANDLER_REMOVABLE);

	echo "<form method = 'post' id = 'forma' action='$log_url'>
        <label for='name'>Name</label>   
        <input name = 'name' id = 'name'>
        <br/>    
        <label for='surname'>Surmane</label>
        <input name = 'surname' id = 'surname'>  
        <br/>
        <label for='email'>Email</label> 
        <input name = 'email' id = 'email'>
        <br/>    
        <label for='submitBtn'></label>
        <input type = 'submit' name = 'submitBtn' id = 'submitBtn'>
        </form>";

	return ob_get_clean();
}

function add_db()
{
    global $wpdb;
    if (isset($_POST['submitBtn'])) {

        $name = sanitize_text_field($_POST['name']);
        $surname = sanitize_text_field($_POST['surname']);
        $email = sanitize_text_field($_POST['email']);

        $db_name = $wpdb->prefix;

        if (strlen($name) > 3 && strlen($surname) > 5 && strlen($email) > 5) {
            $data = array(
                'name1' =>  $name,
                'surname' => $surname,
                'email' => $email
            );
            if($wpdb->insert($db_name, $data))
            {
                echo "data is added";
            }else echo $wpdb->last_error;
        }else {
            echo "name shousld be more than 3 sibol , surname 5, email 5";
        }
    }
}


add_action("admin_post", "add_db");

do_action("admin_post");

add_shortcode("show_table_short" , "show_table");

function show_table()
{
    global $wpdb;
    $change_url = site_url( "cahnge_short" );
    $arr_from_db = $wpdb->get_results("SELECT * FROM wp_info WHERE id < 100");

	ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE ^ PHP_OUTPUT_HANDLER_REMOVABLE);

    echo"<form action='$change_url' method = 'get'>
            <table style = border: 1px solid black;>";
    foreach ($arr_from_db as $key) {
        $real_id = esc_attr($key->id);
        $real_name1 = esc_attr($key->name1);
        $real_surname = esc_attr($key->surname);
        $real_email = esc_attr($key->email);

        echo "<tr>
                <td>$real_id</td>
                <td>$real_name1</td>
                <td>$real_surname</td>
                <td>$real_email</td>
                <td>
                    <input type='submit' value = '$real_id' name = '$real_id'>
                </td>
            </tr>";
    }
    echo "</table>
    </form>";

	return ob_get_clean();
}?>

<?php
/**
 * Plugin Name: change_plugin
 * Description: this plugin i for saveing a date in date base 
 */
$id_from_get = 0;
$id_from_db = 0;


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id_from_get;
    foreach ($_GET as $key => $value) {
        $id_from_get = $key;
    }

    $row_id = $wpdb->get_results("SELECT * FROM wp_info WHERE id = $id_from_get");
    foreach ($row_id as $key => $value) {
        print_r("Your current id is - ".$value->id." write it in input ");
        $row_id = $value->id;
    }
};

if(isset($_POST['changeBtn']))
{
    global $wpdb;

    $name = strip_tags($_POST['name']);
    $surname = strip_tags($_POST['surname']);
    $email = strip_tags($_POST['email']);
    if (strlen($name) >= 3 && strlen($surname) >= 5 && strlen($email) >= 5) {
        $arr = array(
            'name1'=> $name,
            'surname'=>$surname,
            'email'=>$email
        );
        $arr_which = array(
            'id'=>$_POST['id'],
        );

        if($wpdb->update('wp_info',$arr,$arr_which)){
            echo "data updated";
        }
        else echo "error";
    }else {
        echo "name shold be more than 3 sibol , surname 5, email 5";
    }
};

add_shortcode( 'edit_short', 'changing');

function changing(){    
    global $id_from_get;

    ob_get_clean();
    ob_start();
    
    echo "<form method = 'post' action='change.php'>
            <label for='name'>new name </label>
            <input type='text' name = 'name' id='name'><br>
            
            <label for='surname'>new surname</label>
            <input type='text' name = 'surname' id='surname'><br>
            
            <label for='email'>new email</label>
            <input type='text' name = 'email' id='email'><br>

            <label for='id'>current ID</label>
            <input type='number' name = 'id' id='id' value='$id_from_get' readonly='true'><br>
            
            <input type='submit' name = 'changeBtn'>
        </form>";
        
}

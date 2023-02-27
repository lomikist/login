
<?php
/**
 * Plugin Name: change_plugin
 * Description: this plugin i for saveing a date in date base 
 */
$idFromGet = 0;
$idFromDb = 0;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $idFromGet;
    foreach ($_GET as $key => $value) {
        $idFromGet = $key;
    }
    // echo $id;
    $a = $wpdb->get_results("SELECT * FROM wp_info WHERE id = $idFromGet");
    foreach ($a as $key => $value) {
        print_r("Your current id is - ".$value->id." write it in input ");
        $idFromDb = $value->id;
    }
};

if(isset($_POST['changeBtn']))
{
    global $wpdb;

    $arr = array(
        'name1'=>$_POST['name'],
        'surname'=>$_POST['surname'],
        'email'=>$_POST['email']
    );
    $arrWhich = array(
        'id'=>$_POST['id'],
    );

    print_r($arr);
    print_r($arrWhich);
    // $wpdb->update('wp_info',
    //             $arr,
    //             $arrWhich);
    if($wpdb->update('wp_info',$arr,$arrWhich)){
        echo "data updated";
    }
    else echo "error";
};

add_shortcode( 'change', 'changing');
function changing(){    
    global $idFromDb;

    return "
    <form method = 'post' action='change.php'>
        <label for='name'>new name </label>
        <input type='text' name = 'name' id='name'><br>
        
        <label for='surname'>new surname</label>
        <input type='text' name = 'surname' id='surname'><br>
        
        <label for='email'>new email</label>
        <input type='text' name = 'email' id='email'><br>

        <label for='id'>current ID</label>
        <input type='number' name = 'id' id='id' value='$idFromDb' readonly='true'><br>
        
        <input type='submit' name = 'changeBtn'>
    </form>";
}

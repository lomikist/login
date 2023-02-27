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
$shortName = "myShort";
add_shortcode($shortName , "showForm");
//second
function showForm()
{
    return '
        <form method = "post" id = "forma" action="http://localhost/wordpress6/wordpress/myshort/">
            <label for="name">Name</label>
            <input name = "name" id = "name">
            <br/>
            <label for="surname">Surmane</label>
            <input name = "surname" id = "surname">
            <br/>
            <label for="email">Email</label>
            <input name = "email" id = "email">
            <br/>
            <label for="submitBtn"></label>
            <input type = "submit" name = "submitBtn" id = "submitBtn">
        </form>
    ';
}

function addDb()
{
    global $wpdb;
    if (isset($_POST['submitBtn'])) {
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $email = $_POST['email'];

        $tableName = $wpdb->prefix . 'info';
        $data = array(
            'name1' =>  $name,
            'surname' => $surname,
            'email' => $email
        );
        if($wpdb->insert($tableName, $data))
        {
            echo "data is added";
        }
    }
}
add_action("acf/save_pot", "addDb");

do_action("acf/save_pot");


add_shortcode("showTable" , "showTable");

function showTable()
{
    global $wpdb;

    $tableName = $wpdb->prefix . 'info';
    
    $a = $wpdb->get_results("SELECT * FROM wp_info WHERE id < 100");

    $tables = "<form action='http://localhost/wordpress6/wordpress/change/' method = 'get'>";
    $tables .= "<table style = border: 1px solid black; >";
    foreach ($a as $key) {
        $tables .= "
            <tr>
                <td>$key->id</td>
                <td>$key->name1</td>
                <td>$key->surname</td>
                <td>$key->email</td>
                <td>
                    <input type='submit' value = 'edit' name = '$key->id'>
                </td>
            </tr>
        ";
    }
    $tables .= "</table>";
    $tables .= "</form>";

    return $tables;
}?>
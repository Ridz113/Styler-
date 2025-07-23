<?php 

session_start(); // Start session
error_reporting(E_ERROR | E_PARSE);
include_once '/home/aky/public_html/styler/init.php';

## kill script if user not logged in
if (!isset($_SESSION['user_id'])) {
	die;
}

## Gets all necessary information for INSERT
$category_id = intval($_POST['category_id']);
$item_id = intval($_POST['item_id']);
$user_id = intval($_SESSION['user_id']);

## insert product into wardrobe_items
sql("INSERT INTO wardrobe_items SET category_id='{$category_id}',item_id='{$item_id}',user_id='{$user_id}'");
$insert_id = insert_id();
if($insert_id > 0){
	echo "success";
}
?>
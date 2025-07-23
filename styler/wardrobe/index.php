<?php
session_start();
include_once '/home/aky/public_html/styler/init.php';
$tpl = file_get_contents('wardrobe_tpl.html');  // the contents of wardrobe page gets stored in tpl


// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    $_SESSION['wardrobe_error'] = 'Please Sign In to use the wardrobe feature';
    header("Location: /styler/sign-in/");
}
else{
    $_SESSION['wardrobe_error'] = '';
}


if(isset($_GET['remove_id'])){
    $wardrobe_products_id = intval($_GET['remove_id']);
    sql("DELETE FROM wardrobe_items WHERE wardrobe_products_id='{$wardrobe_products_id}'");
}




// page headline
$headline = "This is your wardrobe, " . ucwords($_SESSION['username']);
$tpl = str_replace("{headline}",$headline,$tpl);


// specify category id's for each section
$categories = [
    'hats' => 6,
    'tops' => [1,4],
    'bottoms' => [2, 5],
    'shoes' => 3
];


// loop through each category and run SQL query to grab the items corresponding to the category id
foreach ($categories as $key => $category_id) {
    // use IN for categories that have more than 1 ID otherwise use exact match "="
    $category_condition = is_array($category_id) ? "IN (" . implode(",", $category_id) . ")" : "= $category_id";
    
    $items_sql = sql("SELECT ci.*,wi.wardrobe_products_id FROM wardrobe_items wi 
                      LEFT JOIN clothing_items ci ON wi.item_id = ci.item_id 
                      WHERE wi.user_id = '" . intval($_SESSION['user_id']) . "' 
                      AND wi.category_id $category_condition");

    $items = []; // Reset array for each category

    // build array items to be used for JSON
    foreach ($items_sql as $item) {
        $items[] = [
            'wardrobe_products_id'=>$item['wardrobe_products_id'],
            'item_url'=>$item['product_url'],
            'image_url' => $item['image_url'],
            'name' => $item['name'],
            'price' => $item['price']
        ];
    }

    // replace json in template
    $tpl = str_replace("{" . $key . "_json}", addslashes(json_encode($items)), $tpl);
}



tpl_output($tpl); // outputs the new tpl (template)
?>

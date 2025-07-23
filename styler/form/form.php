<?php
error_reporting(E_ERROR | E_PARSE);

include_once '/home/aky/public_html/styler/init.php'; // gets and embeds the code from init.php into here


if(isset($_POST['name'])){

  # Image upload and path
  $image_url = '';
  if (isset($_FILES['file'])) {
      $targetDir = "/styler/images/";
      $targetFile = $targetDir . basename($_FILES["file"]["name"]);
      if (move_uploaded_file($_FILES["file"]["tmp_name"],'/home/aky/public_html'. $targetFile)) {
          $image_url = $targetFile;
      }
  }

  # Get form data and sanitise data to prevent injection
  $name           = addslashes($_POST['name']);
  $category_id    = intval($_POST['category_id']);
  $subcategory_id = intval($_POST['subcategory_id']);
  $gender         = addslashes($_POST['gender']);
  $brand_id       = intval($_POST['brand_id']);
  $colour_id      = intval($_POST['colour_id']);
  $price          = floatval($_POST['price']);
  $image_url      = addslashes($image_url);
  $product_url    = addslashes($_POST['product_url']);
  $store_id       = intval($_POST['store_id']);
  $item_added     = time();

  // Insert data into the database
  $sql = "INSERT INTO clothing_items (name, category_id, subcategory_id, gender, brand_id, colour_id, price, image_url, product_url, store_id, item_added)
  VALUES ('$name', $category_id, '$subcategory_id', '$gender', '$brand_id', '$colour_id', $price, '$image_url', '$product_url', '$store_id', $item_added)";
  
  sql($sql);
}


$tpl = file_get_contents('form.html');

// get cats
$cats = sql("SELECT * FROM categories");
foreach($cats as $cat){
  $cats_options .= '<option value="'.$cat['category_id'].'">'.ucwords($cat['category_name']).'</option>';
}
$tpl = str_replace('{cats_options}', $cats_options, $tpl);


// get subcats
$subcats = sql("SELECT * FROM subcategories");
foreach($subcats as $subcat){
  $subcat_options .= '<option value="'.$subcat['subcategory_id'].'">'.ucwords($subcat['subcategory_name']).'</option>';
}
$tpl = str_replace('{subcat_options}', $subcat_options, $tpl);


// get brands
$brands = sql("SELECT * FROM brands");
foreach($brands as $brand){
  $brand_options .= '<option value="'.$brand['brand_id'].'">'.ucwords($brand['brand_name']).'</option>';
}
$tpl = str_replace('{brand_options}', $brand_options, $tpl);

// get colours
$colours = sql("SELECT * FROM colours");
foreach($colours as $colour){
  $colour_options .= '<option value="'.$colour['colour_id'].'">'.ucwords($colour['colour_name']).'</option>';
}
$tpl = str_replace('{colour_options}', $colour_options, $tpl);


// get stores
$stores = sql("SELECT * FROM stores");
foreach($stores as $store){
  $store_options .= '<option value="'.$store['store_id'].'">'.$store['store_name'].'</option>';
}
$tpl = str_replace('{store_options}', $store_options, $tpl);






echo $tpl;


?>

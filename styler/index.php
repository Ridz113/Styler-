<?php 
session_start(); // Start session
error_reporting(E_ERROR | E_PARSE);
include_once '/home/aky/public_html/styler/init.php';

$tpl = file_get_contents('home_tpl.html');

# get all item ids of user wardrobe into an array to check in loop later
$user_item_ids=[];
if($_SESSION['user_id']){
  $item_ids_sql = sql("SELECT * FROM wardrobe_items WHERE user_id='".intval($_SESSION['user_id'])."'");
  foreach($item_ids_sql as $items_ids){
    $user_item_ids[] = $items_ids['item_id'];
  }


  // remove homepage hero when user is logged in - by adding "hide" class
  $tpl = str_replace('class="hero-section"', 'class="hero-section hide"', $tpl);
}


  // Array to store conditions
  $conditions = [];
  $params = [];
  $types = "";

  // Check for filters
  if(empty($_GET)){
    $tpl = str_replace('class="clear-filters"','class="clear-filters hide"', $tpl);
  }

  // Sets up the SQL query/condition to use on the database for the search
   $search = $_GET['search'];
  if (!empty($_GET['search'])) {
    $conditions[] = "ci.name LIKE '%".addslashes($search)."%'";
  }

  // Gathers all applied category filters and queries them into an SQL statement
  $category_ids = $_GET['category'];
   if(is_array($category_ids)){
     $conditions[] = 'ci.category_id IN ('.implode(',', $category_ids).')';
   }

// Gathers all applied gender filters and queries them into an SQL statement
   $genders = ["male", "female", "unisex"];
   $gender = $_GET['gender'];

   if($gender){
       foreach($gender as $g){
        $g= strtolower($g);
    if(in_array($g,$genders)){
       
     $genders_conditions[] = strtolower($g);
    }
   }
   $conditions[] = "ci.gender IN ('".implode("','", $genders_conditions)."')";
   }

  // Gathers all applied subcategory filters and queries them into an SQL statement
   $subcategory_ids = $_GET['subcategory'];
  if (!empty($_GET['subcategory'])) {
    $conditions[] = 'ci.subcategory_id IN ('.implode(',', $subcategory_ids).')';
  }

// Gathers all applied colour filters and queries them into an SQL statement
  $colour_ids = $_GET['colour'];
  if (!empty($_GET['colour'])) {
    $conditions[] = 'ci.colour_id IN ('.implode(',', $colour_ids).')';
  }

// Gathers all applied store filters and queries them into an SQL statement
  $store_ids = $_GET['store'];
  if (!empty($_GET['store'])) {
    $conditions[] = 'ci.store_id IN ('.implode(',', $store_ids).')';
  }

// Gathers all applied brand filters and queries them into an SQL statement
  $brand_ids = $_GET['brand'];
  if (!empty($_GET['brand'])) {
    $conditions[] = 'ci.brand_id IN ('.implode(',', $brand_ids).')';
  }

// Gathers all applied sort by prices filters and queries them into an SQL statement
  $price_range = $_GET['price-range'];
  if(!empty($price_range)){
    list($price_min, $price_max) = explode('-',$price_range,2);
    $conditions[] = "ci.price >= '".intval($price_min)."'";
    $conditions[] = "ci.price <= '".intval($price_max)."'";
   }

  // Append WHERE clause only if there are conditions
  if (!empty($conditions)) {
      $where = " WHERE " . implode(" AND ", $conditions);
  }

// when no sort is specified - then automatically apply low-to-high sort
  if(empty($_GET['sort'])){
    $_GET['sort']="low-to-high";
  }

  // manage sorting for SQL
  if(!empty($_GET['sort'])){
   switch($_GET['sort']){
    case 'high-to-low':
        $order_by = "ORDER BY ci.price DESC";
        break;
    default:
        $order_by = "ORDER BY ci.price ASC";
   }
  }

  // SQL query to retrieve data from database
  $query = "SELECT 
      ci.*, 
      str.store_name, 
      br.brand_name, 
      cat.category_name, 
      sub.subcategory_name, 
      col.colour_name
  FROM 
      clothing_items ci
  LEFT JOIN 
      stores str ON ci.store_id = str.store_id
  LEFT JOIN 
      brands br ON ci.brand_id = br.brand_id
  LEFT JOIN 
      categories cat ON ci.category_id = cat.category_id
  LEFT JOIN 
      subcategories sub ON ci.subcategory_id = sub.subcategory_id
  LEFT JOIN 
      colours col ON ci.colour_id = col.colour_id
   ".$where."

    {$order_by}
   "; 
  

  // Prepare statement
 $sql = sql($query);

  // Fetch data
  while ($row = fetch($sql)) {

          $product_img = '<div style="display:flex;justify-content:center;"><img src="'.$row['image_url'].'"/></div>';
          $product_store = '<div style="display:flex;"><div class="product_store">'. ucwords($row['store_name']).'</div></div>';
          $product_brand = '<div class="product_brand">'. ucwords($row['brand_name']).'</div>';
          $product_name = '<div class="product_name">'. ucwords($row['name']).'</div>';
          $product_price = '<div class="product_price">'."£".$row['price'].'</div>';

          ## check if item exists in users wardrobe
          if(in_array($row['item_id'],$user_item_ids)){
            $wardrobe_button = '<button type="button">Added</button>';
          }else{
            $wardrobe_button = '<button type="button" class="wardrobe-add-item" data-item-id="'.$row['item_id'].'" data-category-id="'.$row['category_id'].'">Add to Wardrobe</button>'; 
          }
          if (!isset($_SESSION['user_id'])) {
              $wardrobe_button = '<a href="/styler/sign-in/" style="color:#000;text-decoration:none;" class="wardrobe-add-item">Add to Wardrobe</a>';
          }
          $products .= '<div class="product">
                            '.$product_img.$product_store.$product_brand.$product_name.$product_price.$wardrobe_button.'</div>';
  }

// Error is displayed when no products are shown
  if(!$products) {
      $products = "
      <div>
      No results found
      <div style=\"color:#666;font-size:90%;\">Please try adjusting your filters</div>
      </div>";
      $tpl = str_replace('class="products-container"','class="products-container no-results"', $tpl);
  }


  // replaces products in homepage with the products in the database
  $tpl = str_replace('{products}', $products, $tpl);


  // get cats
  $cats = sql("SELECT * FROM categories");
  foreach($cats as $cat){
    $cat_checked = isset($_GET['category']) && in_array($cat['category_id'],$_GET['category']) ? 'checked':''; 
    $cats_options .= '<div class="_option-item _item"> <input id="category_'.$cat['category_name'].'" type="checkbox" onchange="update_results(this);" name="category[]" value="'.$cat['category_id'].
    '" '.$cat_checked.'><label id="{filter_encoded}_'.$cat['category_name'].'"for="category_'.$cat['category_name'].'">'.'<div>'.ucwords($cat['category_name']).'</div></label></div>';
  }
  $tpl = str_replace('{cats_options}', $cats_options, $tpl);
  if($_GET['category']){
    $tpl = str_replace('class="filter-name">Categories','class="filter-name -active">Categories', $tpl);
  }

  // get subcats
  $subcats = sql("SELECT * FROM subcategories");
  foreach($subcats as $subcat){
    $sub_checked = is_array($_GET['subcategory']) && in_array($subcat['subcategory_id'],$_GET['subcategory']) ? 'checked':''; 
    $subcats_options .= '<div class="_option-item _item"> <input id="subcategory_'.$subcat['subcategory_name'].'" type="checkbox" onchange="update_results(this);" name="subcategory[]" value="'.$subcat['subcategory_id'].
    '" '.$sub_checked.'><label id="{filter_encoded}_'.$subcat['subcategory_name'].'"for="subcategory_'.$subcat['subcategory_name'].'">'.'<div>'.ucwords($subcat['subcategory_name']).'</div></label></div>';
  }
  $tpl = str_replace('{subcats_options}', $subcats_options, $tpl);
    if($_GET['subcategory']){
    $tpl = str_replace('class="filter-name">Subcategories','class="filter-name -active">Subcategories', $tpl);
  }

  // get brands
  $brands = sql("SELECT * FROM brands");
  foreach($brands as $brand){
    $brand_checked = is_array($_GET['brand']) && in_array($brand['brand_id'],$_GET['brand']) ? 'checked':'';
    $brand_options .= '<div class="_option-item _item"> <input id="brand_'.$brand['brand_name'].'" type="checkbox" onchange="update_results(this);" name="brand[]" value="'.$brand['brand_id'].
    '" '.$brand_checked.'><label id="{filter_encoded}_'.$brand['brand_name'].'"for="brand_'.$brand['brand_name'].'">'.'<div>'.ucwords($brand['brand_name']).'</div></label></div>';
  }
  $tpl = str_replace('{brand_options}', $brand_options, $tpl);
      if($_GET['brand']){
    $tpl = str_replace('class="filter-name">Brand','class="filter-name -active">Brand', $tpl);
  }

  // get colours
  $colours = sql("SELECT * FROM colours");
  foreach($colours as $colour){
    $color_checked = is_array($_GET['colour']) && in_array($colour['colour_id'],$_GET['colour']) ? 'checked':'';
    $colour_options .= '<div class="_option-item _item"> <input id="colour_'.$colour['colour_name'].'" type="checkbox" onchange="update_results(this);" name="colour[]" value="'.$colour['colour_id'].
    '" '.$color_checked.'><label id="{filter_encoded}_'.$colour['colour_name'].'"for="colour_'.$colour['colour_name'].'">'.'<div>'.ucwords($colour['colour_name']).'</div></label></div>';
  }
  $tpl = str_replace('{colour_options}', $colour_options, $tpl);
  if($_GET['colour']){
    $tpl = str_replace('class="filter-name">Colour','class="filter-name -active">Colour', $tpl);
  }

  // get stores
  $stores = sql("SELECT * FROM stores");
  foreach($stores as $store){
    $store_checked = is_array($_GET['store']) && in_array($store['store_id'],$_GET['store']) ? 'checked':'';
    $store_options .= '<div class="_option-item _item"> <input id="store_'.$store['store_name'].'"type="checkbox" onchange="update_results(this);" name="store[]" value="'.$store['store_id'].
    '" '.$store_checked.'><label id="{filter_encoded}_'.$store['store_name'].'"for="store_'.$store['store_name'].'">'.'<div>'.ucwords($store['store_name']).'</div></label></div>';
  }
  $tpl = str_replace('{store_options}', $store_options, $tpl);
    if($_GET['store']){
    $tpl = str_replace('class="filter-name">Store','class="filter-name -active">Store', $tpl);
  }

  // gender
  $genders = ["Male", "Female", "Unisex"];
  foreach($genders as $gender){
    $gender_checked = is_array($_GET['gender']) && in_array($gender,$_GET['gender']) ? 'checked':'';
    $gender_options .= '<div class="_option-item _item"> <input id="gender_'.$gender.'"type="checkbox" onchange="update_results(this);" name="gender[]" value="'.$gender.
    '" '.$gender_checked.'><label id="{filter_encoded}_'.$gender.'"for="gender_'.$gender.'">'.'<div>'.ucwords($gender).'</div></label></div>';
  }
  $tpl = str_replace('{gender_options}', $gender_options, $tpl);
    if($_GET['gender']){
    $tpl = str_replace('class="filter-name">Gender','class="filter-name -active">Gender', $tpl);
  }


  // sort
  $sortings[] = ['sort_text'=>'Price: Low to High','sort_name'=>'low-to-high'];
  $sortings[] = ['sort_text'=>'Price: High to Low','sort_name'=>'high-to-low'];
  foreach($sortings as $sorting){
    $sorting_checked = isset($_GET['sort']) && $sorting['sort_name']==$_GET['sort'] ? 'checked':'';
    $sorting_options .= '<div class="_option-item _item"> <input id="sort_'.$sorting['sort_name'].'"type="checkbox" onchange="query_string(\'sort\',\''.$sorting['sort_name'].'\');" name="sort[]" value="'.$sorting['sort_name'].
    '" '.$sorting_checked.'><label id="{filter_encoded}_'.$sorting['sort_name'].'"for="sort_'.$sorting['sort_name'].'">'.'<div>'.ucwords($sorting['sort_text']).'</div></label></div>';
  }
  $tpl = str_replace('{sorting_options}', $sorting_options, $tpl);
  if($_GET['sort']){
    $tpl = str_replace('class="filter-name">Sort','class="filter-name -active">Sort', $tpl);
  }


  // price range
  if($_GET['price-range']){
    list($min_range, $max_range) = explode("-",$_GET['price-range'],2);
  }
  $tpl = str_replace('{min_range}', $min_range ? $min_range : 5, $tpl);
  $tpl = str_replace('{max_range}', $max_range ? $max_range : 200, $tpl);
  if($_GET['price-range']){
    $tpl = str_replace('class="filter-name">Budget','class="filter-name -active">Budget', $tpl);
  }


tpl_output($tpl); 





?>






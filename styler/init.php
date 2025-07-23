<?php
// this file runs intially on the website, resposible for connecting to database and setting up main template layout

// Database connection settings
$conn = mysqli_connect("127.0.0.1","aky","U6ZL}DtD==DA","aky_clothes_database");
$sign_in_out = [];

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}

// function to make mysql queries easier, prevent repeating $conn
function sql($query,$options=[]){
  global $conn;
  $sql = mysqli_query($conn,$query);
  return $sql;
}

// function retrieve data - if it is a string, call sql()
function fetch($query){
  if(is_string($query)){
    $query = sql($query);
  }
  return mysqli_fetch_assoc($query);
}

// function to get the recent insert id
function insert_id($query=""){
  global $conn;
  return mysqli_insert_id($conn);
}

// function to get number of rows
function num_rows($query){
  return mysqli_num_rows($query);
}

// outputs the main app layout template
function tpl_output($tpl){ // tcp_output takes in the template and replaces the specific section with code
    $main_tpl = file_get_contents('/home/aky/public_html/styler/main_layout_tpl.html'); // gets the contents of main app template
    $main_tpl = str_replace('{page_content}',$tpl,$main_tpl); // in the main app template find 'page_content' and replace it with the argument 'tpl'
    
    // Displays sign in/out 
    if (!isset($_SESSION['username'])) {
        $sign_in_out = 'Sign In';
    }else{
        $sign_in_out = 'Sign Out';
    }
    $main_tpl = str_replace('{sign in/out}', $sign_in_out,$main_tpl);
    
    $main_tpl = str_replace('{time}',time(), $main_tpl);

    $search = $_GET['search'] ?: '';
    $main_tpl = str_replace("{search}", $search, $main_tpl);

    echo $main_tpl; // echos out the template
}

?>
----Personal Information----

Final Year Project made by Muhammed Ridwan Uddin
Student ID: 2446198
Submitted to the University of Birmingham
Supervisor: Jian Liu
Inspector: Mubashir Ali

----Instructions----

To use the project please visit: https://aky.me/styler/

- You will be redirected to the homepage where you will see the website's hero to give you an overview of the website

- The homepage allows you to seach and filter your way to find the clothes you want

- To use the wardrobe you must log in to create an account

- Use the wardrobe to mix and match all the products you have saved and feel free to remove any products you do not want anymore

- After finding your outffit, press buy and you will be redirected to the products original e-commerce store.

----Additional note----

The form.html and form.php files were required to submit products into the database
These are not meant to be accessed by users and are seperate from the website 

----Styler Directory----

    - contains home_tpl.html, home.js, index.php, init.php, main_layout_tpl.html and style.css
    - Contains the default template of the website which includes its header allowing users to navigate between pages
    - The main content is swapped between each page and the homepage is always initialised first with init.php

    - Creates the homepage and its functionality allowing it to extract products from the database and display them with their key information
    - Also provides a filtering system and a search function to navigate through these products
    
    ----Directories Within Styler Directory----
   
    - form directory
        - Contains the form.html and form.php files and were needed to submit products manually into the database through a HTML/PHP form

    - images directory
        - holds all images of all the products in the homepage and the wardrobe page

    - sign-in directory
        - contains index.php and sign-in.html 
        - creates the sign-in page and its functionality allowing users to login 

    - sign-out directory
        - contains index.php and sign-out html
        - Provides the functionalty of the user logging out of a session

    - sign-up directory
        - contains index.php and sign-up html
        - creates the sign-up page and its functionality allowing users to register

    - wardrobe directory
        - contains ajax_add_item.php, index.php and wardrobe_tpl.html
        - creates the wardrobe page and its functionality allowing users to add products to the page and display them to buy




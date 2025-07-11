<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Havok Restaurant | Menu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./styles/large-screens.styles.css">
    <link rel="stylesheet" href="./styles/medium-screens.styles.css">
    <link rel="stylesheet" href="./styles/small-screens.styles.css">
    <link rel="stylesheet" href="./notifier/style.css"></link>
    <script src="./notifier/index.var.js" defer></script>
    <script src="./scripts/menu_handler.js" defer></script>
</head>
<body>
    <div class="sidebar-category-menu" id="sidebar-category-menu">
        <div class="sidebar-displayer-menu" id="sidebar-displayer-menu">
            <i class="fa-solid fa-bars"></i>
        </div>
        <div class="sidebar-content">
            <img src="../image/logo-massimo-white.png" alt="" class="sidebar-logo">

            <div id="category-links"></div>
        </div>
    </div>

    <!-- client order placeholder -->
     <div class="client-cart-icon">
        <div class="cart-item-counter">0</div>
        <i class="fa-solid fa-utensils"></i>
     </div>

     <div class="food-cart-holder">
        <h5 class="food-cart-heading">Food Cart</h5>
        <input type="text" placeholder="Table ID:" class="table-id-holder" id="table-id-holder">
     </div>

    <div class="menu-container">
        <div class="upper-part">
            <div class="overlay-filter"></div>
            <img src="../image/dish-1.png" alt="" class="dish-img di-one">
            <img src="../image/dish-2.png" alt="" class="dish-img di-two">
            <img src="../image/dish-3.png" alt="" class="dish-img di-three">
            <img src="../image/dish-3.png" alt="" class="dish-img di-four">
            <img src="../image/logo-massimo-white.png" alt="" class="company-logo">
            <!-- banner head -->
            <div class="banner-tagline">best taste in town</div>
            <svg xmlns="http://www.w3.org/2000/svg" class="wave" viewBox="0 0 1440 320"><path fill="#ffe79f" fill-opacity="1" d="M0,128L26.7,138.7C53.3,149,107,171,160,149.3C213.3,128,267,64,320,69.3C373.3,75,427,149,480,192C533.3,235,587,245,640,229.3C693.3,213,747,171,800,144C853.3,117,907,107,960,122.7C1013.3,139,1067,181,1120,186.7C1173.3,192,1227,160,1280,160C1333.3,160,1387,192,1413,208L1440,224L1440,320L1413.3,320C1386.7,320,1333,320,1280,320C1226.7,320,1173,320,1120,320C1066.7,320,1013,320,960,320C906.7,320,853,320,800,320C746.7,320,693,320,640,320C586.7,320,533,320,480,320C426.7,320,373,320,320,320C266.7,320,213,320,160,320C106.7,320,53,320,27,320L0,320Z"></path></svg>
        </div>
        <!-- menu content -->
        <div class="lower-part" id="lower-part">
        </div>
    </div>
</body>
</html>
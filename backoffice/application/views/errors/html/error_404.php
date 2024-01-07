<!DOCTYPE html> <html class="no-js" lang="en"> 
<head>

   <!--- basic page needs
   ================================================== -->
   <meta charset="utf-8">
   <title><?php echo ERROR_PAGE_TITLE; ?> | Page Not Found</title>
   <meta name="description" content="">  
   <meta name="author" content="">

   <!-- mobile specific metas
   ================================================== -->
   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- CSS
    ================================================== -->
    <link rel="stylesheet" href="<?php echo ERROR_PAGE_RETURN_URL; ?>/public_html/error/css/base.css">  
    <link rel="stylesheet" href="<?php echo ERROR_PAGE_RETURN_URL; ?>/public_html/error/css/main.css">
    <link rel="stylesheet" href="<?php echo ERROR_PAGE_RETURN_URL; ?>/public_html/error/css/vendor.css">     

   <!-- script
   ================================================== -->
   <script src="<?php echo ERROR_PAGE_RETURN_URL; ?>/public_html/error/js/modernizr.js"></script> 
   <!-- favicons
   ================================================== -->
   <link rel="icon" type="image/png" href="<?php echo ERROR_PAGE_RETURN_URL; ?>/public_html/images/logos/fav_6193385_thumb.png"> 
</head>

<body>
<style type="text/css">
  #notfound {
    position: relative;
    height: 100vh;
  }
  #notfound .notfound {
    position: absolute;
    left: 50%;
    top: 50%;
    -webkit-transform: translate(-50%, -50%);
    -ms-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
  }
  .notfound {
    max-width: 920px;
    width: 100%;
    line-height: 1.4;
    text-align: center;
    padding-left: 15px;
    padding-right: 15px;
}
.notfound .notfound-404 {
    position: absolute;
    height: 100px;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    z-index: -1;
}
.notfound .notfound-404 h1 {
    color: #7266ba29;
    font-weight: 900;
    font-size: 276px;
    margin: 0px;
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);

}
.notfound h2 {
    font-family: 'Maven Pro', sans-serif;
    font-size: 46px;
    color: #428bca;
    font-weight: 900;
    text-transform: uppercase;
    margin: 0px;
    font-size: 26px;
}
.notfound p {
    color: #000;
    font-weight: 400;
    text-transform: uppercase;
    margin-top: 15px;
    font-size: 15px;
}
.notfound h2 span
{
  color: red;
  font-size:35px; 
}
body {
  font-family: 'Maven Pro', sans-serif;
  background: #fff;
}
.notfound .btn-addon{
    text-decoration: none;
    text-transform: uppercase;
    background: #7266ba;
    display: inline-block;
    padding: 16px 38px;
    border: 2px solid transparent;
    border-radius: 40px;
    color: #fff;
    font-weight: 400;
    transition: 0.2s all;
}

</style>


    <!-- main content
    ================================================== -->
    

       <div id="notfound">
         <div class="notfound">
            <div class="notfound-404">
              <h1>404</h1>
            </div>
            <h2>We are sorry, Page not found <span>!</span></h2>
            <p>The page you are looking for might have been removed or is temporarily unavailable.</p>
           <a href="<?php echo ERROR_PAGE_RETURN_URL?>" class="btn-addon"><i class="fa fa-backward">  </i>  Back To Dashboard</a>
         </div>
       </div> 



     <!-- /main-404-content -->

   

   <!-- Java Script
   ================================================== --> 
   <script src="<?php echo ERROR_PAGE_RETURN_URL; ?>/public_html/error/js/jquery-2.1.3.min.js"></script>
   <script src="<?php echo ERROR_PAGE_RETURN_URL; ?>/public_html/error/js/plugins.js"></script>
   <script src="<?php echo ERROR_PAGE_RETURN_URL; ?>/public_html/error/js/main.js"></script>

</body>

</html>

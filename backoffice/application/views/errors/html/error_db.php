<!DOCTYPE html> <html class="no-js" lang="en"> 
<head>

   <!--- basic page needs
   ================================================== -->
   <meta charset="utf-8">
   <title><?php echo ERROR_PAGE_TITLE; ?> | Database Error found.</title>
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
.notfound .notfound-500 {
    position: absolute;
    height: 100px;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    z-index: -1;
}
.notfound .notfound-500 h1 {
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
    font-size: 14px;
}
.notfound h2 span
{
  color: red;
  font-size:35px; 
}

.notfound .error h5
{
  font-family: "roboto-regular", sans-serif;
  color: red;
  text-transform:capitalize;
  text-align: left;
  font-weight: normal;
  letter-spacing: .0rem;
  font-size: 20px;
}
.notfound .error h5 .expand_btn
{
  float: right;
  color: #f5f6f5;
  background-color: #7266baa6;
  display: inline-block;
  padding: 0px 6px;
  border: 2px solid transparent;
  border-radius: 40px;
  font-weight: revert;
  font-size: 12px;
}
.notfound .error p
{
  text-align: left;
  font-size:12px;
  margin-bottom: 5px;
  margin-top: 5px;
  text-transform: none; 
}
.notfound .error
{
  padding: 15px 15px;
  background-color: #e8e6f4;
  margin-top: 4rem;
  
}
.notfound .error p
{
  font-size: 15px;
}
button:focus {outline:0;}
.collaps
{
  height: 110px;
  overflow: hidden;
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
.expand
{
  height: 200px;
  overflow-y: scroll;
  scrollbar-width: thin; 
  scrollbar-color: red yellow;
}

.expand::-webkit-scrollbar {
  width: 20px;
   --scrollbarBG: #CFD8DC;
  --thumbBG: #fff;
}
</style>
    <!-- main content
    ================================================== -->
   <div id="notfound">
         <div class="notfound">
            <div class="notfound-500">
              <h1>500</h1>
            </div>
            <h2>SORRY , INTERNAL SERVER ERROR<span>!</span></h2>
            <p>It looks as though we've broken something on our system Please try to reload this page in a little while</p>
           <a href="<?php echo ERROR_PAGE_RETURN_URL?>" class="btn-addon"><i class="fa fa-backward">  </i>  Back To Dashboard</a>
           <div class="error collaps"><h5><?php echo $heading; ?><button id="expand" class="expand_btn"><span id="btn_content">Expand</span></button></h5> 
            <p><?php echo $message; ?></p>
           </div> 
         </div>
          
       </div>  
        <!-- /main-404-content -->

    

   <!-- Java Script
   ================================================== --> 
   <script src="<?php echo ERROR_PAGE_RETURN_URL; ?>/public_html/error/js/jquery-2.1.3.min.js"></script>
   <script src="<?php echo ERROR_PAGE_RETURN_URL; ?>/public_html/error/js/plugins.js"></script>
   <script src="<?php echo ERROR_PAGE_RETURN_URL; ?>/public_html/error/js/main.js"></script>
<script type="text/javascript">
  $("#expand").click(function(){   
    if ( $(this).hasClass( "collaps_btn" ))
    { 
      $(".notfound .error").addClass('collaps');
      $(".expand_btn").removeClass('collaps_btn');
      $(".notfound .error").removeClass('expand');
      $("#btn_content").html('expand');
    }
    else
    {
      $(".notfound .error").addClass('expand');
      $(".expand_btn").addClass('collaps_btn');
      $(".notfound .error").removeClass('collaps');
      $("#btn_content").html('collapse');
    }



  });
</script>
</body>

</html>

<html>
  <head>
    <script type="text/javascript" src="class/jquery/jquery-1.10.2.min.js"></script>
    <script type="text/javascript">
      // Executes the function when DOM will be loaded fully
      $(document).ready(function () {	
      // hover property will help us set the events for mouse enter and mouse leave
      $('.navigation li').hover(
      // When mouse enters the .navigation element
      function () {
      //Fade in the navigation submenu
      $('ul', this).fadeIn(); 	// fadeIn will show the sub cat menu
      }, 
      // When mouse leaves the .navigation element
      function () {
      //Fade out the navigation submenu
      $('ul', this).fadeOut();	 // fadeOut will hide the sub cat menu		
      }
      );
      });
    </script>
    
    <style type="text/css">
      
      /* Giving a font-family and Size to give good look */
      body{
      font-family: Verdana, Tahoma, Arial, sans-serif, Helvetica, 微软雅黑;
      font-size:12px;
      }
      
      /* Adjusting the margins, paddings and no list styles */
      .navigation  {
      margin:0; 
      padding:0; 
      list-style:none;
      }	
      
      /* Little tricking with positions */
      .navigation  li {
      float:left;			/* Show list items inline */
      width:100px; 
      position:relative; 
      }
      
      /* Playing with Main Categories */
      .navigation  li a {
      background:#262626; 
      color:#fff;
      display:block;  	/* Making sure a element covers whole li area */
      padding:8px 7px 8px 7px; 
      text-decoration:none; /* No underline */
      border-top:1px solid #F2861D;
      text-align:center; 
      text-transform:uppercase;
      margin:0; 
      }

      .navigation  li a:hover {
      color:#F2861D;
      }
      
      /* Sub Cat Menu stuff*/
      .navigation  ul {
      position:absolute; 
      left:0; 
      display:none; /* Hide it by default */
      margin:0 0 0 -1px; 
      padding:0; 
      list-style:none;
      border-bottom:3px solid #F2861D;
      }
      
      .navigation  ul li {
      width:100px; 
      float:left; 
      border-top:none;
      }
      
      /* Sub Cat menu link properties */
      .navigation  ul a {
      display:block;    	/* Making sure a element covers whole li area */
      height:15px;
      padding:8px 7px 8px 7px; 
      color:#fff;
      text-decoration:none;	
      border-top:none;
      border-bottom:1px dashed #6B6B6B;
      margin:-3; 
      }
      
      .navigation  ul a:hover {
      color:#F2861D;
      }
    </style>
  </head>
  <body>
    <div style="width:650px; margin:0 auto">
      <ul class="navigation">
	<li><a href="#">电机状态</a>
	  <ul>
	    <li><a href="main.php">基本信息</a></li>
	  </ul>
	  <div class="clear"></div>
	</li>
	<li><a href="#">日常操作</a>
	  <ul>
	    <li><a href="weektask.php">本周任务</a></li>
	    <li><a href="monthtask.php">本月任务</a></li>
	    <li><a href="issuetask.php">分发任务</a></li>
	  </ul>
	  <div class="clear"></div>
	</li>
	<li><a href="#">系统设置</a>
	  <ul>
	    <li><a href="usermgr.php">用户设置</a></li>
	    <li><a href="sysconf.php">系统参数</a></li>
	  </ul>			
	  <div class="clear"></div>
	</li>
	<li><a href="#">个人设置</a>
	  <ul>
	    <li><a href="#">个人信息</a></li>
	    <li><a href="changepass.php">更改密码</a></li>
	  </ul>			
	  <div class="clear"></div>
	</li>

      </ul>

      <div class="clear"></div>

    </div>
  </body>
</html>

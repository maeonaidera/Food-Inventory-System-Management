<?php 
$user = current_user(); 
?>
<!DOCTYPE html>   
<html lang="en">     
<head>     
    <meta charset="UTF-8">     
    <title>
        <?php 
            if (!empty($page_title)) 
                echo remove_junk($page_title);             
            elseif (!empty($user)) 
                echo ucfirst($user['name']);             
            else 
                echo "Inventory Management System";
        ?>     
    </title>     
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>     
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />     
    <link rel="stylesheet" href="libs/css/main.css" />
    <style>
        /* Custom styles for admin and special users */
        <?php if ($user['user_level'] === '1' || $user['user_level'] === '2'): ?>
            .page {
                display: flex;
                flex-direction: column;
                height: auto; /* Standard layout */
            }

            .container-fluid {
                flex-grow: 1;
            }

        /* Custom styles for normal users (user level 3) */
        <?php elseif ($user['user_level'] === '3'): ?>
            .page {
                height: 100vh; /* Full height of the viewport */
                display: flex;
                flex-direction: column;
            }

            .container-fluid {
                flex-grow: 1; /* Ensure container takes up remaining height */
            }
        <?php endif; ?>
    </style>
</head>   
<body>   
<?php if ($session->isUserLoggedIn(true)): ?>     
    <header id="header">        
    <div class="logo pull-left"> Inventory System</div>
        
        <div class="header-content">       
            <div class="header-date pull-left">         
                <strong><span id="realTimeClock"></span></strong>       
            </div>       
            <div class="pull-right clearfix">         
                <ul class="info-menu list-inline list-unstyled">           
                    <li class="profile">             
                        <a href="#" data-toggle="dropdown" class="toggle" aria-expanded="false">               
                            <img src="uploads/users/<?php echo $user['image'];?>" alt="user-image" class="img-circle img-inline">               
                            <span><?php echo remove_junk(ucfirst($user['name'])); ?> <i class="caret"></i></span>             
                        </a>             
                        <ul class="dropdown-menu">               
                            <li>                   
                                <a href="profile.php?id=<?php echo (int)$user['id'];?>">                       
                                    <i class="glyphicon glyphicon-user"></i> Profile                   
                                </a>               
                            </li>              
                            <li>                  
                                <a href="edit_account.php" title="edit account">                      
                                    <i class="glyphicon glyphicon-cog"></i> Settings                  
                                </a>              
                            </li>              
                            <li class="last">                  
                                <a href="logout.php">                      
                                    <i class="glyphicon glyphicon-off"></i> Logout                  
                                </a>              
                            </li>            
                        </ul>           
                    </li>         
                </ul>       
            </div>      
        </div>     
    </header>     

    <?php if ($user['user_level'] === '1' || $user['user_level'] === '2'): ?>     
        <div class="sidebar">       
            <?php if ($user['user_level'] === '1'): ?>         
                <!-- Admin menu -->       
                <?php include_once('admin_menu.php');?>        
            <?php elseif ($user['user_level'] === '2'): ?>         
                <!-- Special user menu -->       
                <?php include_once('special_menu.php');?>        
            <?php endif;?>     
        </div> 
    <?php endif; ?>
<?php endif;?>  

<!-- Page content for all users -->

<?php if ($user === null || $user['user_level'] === '1' || $user['user_level'] === '2'): ?>
        <div class="page">
            <div class="container-fluid">
<?php elseif ($user['user_level'] === '3'): ?>
    <div style="margin: 100px">
    <div class="container-fluid">
<?php endif; ?>





<script>
function updateClock() {
    var now = new Date();
    var options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric'
    };
    var timeOptions = {
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit' 
    };
    document.getElementById('realTimeClock').innerText = now.toLocaleDateString('en-US', options) + " " + now.toLocaleTimeString('en-US', timeOptions);
}

// Update every second
setInterval(updateClock, 1000);

// Initialize clock immediately
updateClock();
</script>

</body>   
</html>

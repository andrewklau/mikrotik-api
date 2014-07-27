<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Router Status</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>

	<section id="home" name="home"></section>
	<div id="headerwrap">
	    <div class="container">
	    	<div class="row centered">
	    		<div class="col-lg-12">
					<h1>Router Control</h1>
                    <p>You are currently connected via <?php echo $_SERVER['REMOTE_ADDR']; ?></p>
	    		</div>

	    	</div>
	    </div> <!--/ .container -->
	</div><!--/ #headerwrap -->


	<section id="desc" name="desc"></section>


		<div class="container">
			<div class="row centered">

        <div class="col-lg-12">
            <h3>GW1</h3>
            <p>Ping to Google from ISP1: <span id="pingtest1"></span> </p>
        </div>

        <table class="table table-striped table-bordered table-condensed">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>IP Address</th>
                    <th>Current Upload / Download</th>
                    <th>Limit Upload / Download</th>
                </tr>
            </thead>
            <tbody id="queue_GW1">
            </tbody>
        </table>
    
        <div class="col-lg-12">
            <h3>GW2</h3>
            <p>Ping to Google from ISP2: <span id="pingtest2"></span> </p>
        </div>

        <table class="table table-striped table-bordered table-condensed">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>IP Address</th>
                    <th>Current Upload / Download</th>
                    <th>Limit Upload / Download</th>
                </tr>
            </thead>
            <tbody id="queue_GW2">
            </tbody>
        </table>
    


			</div>
			<br>
			<hr>
	    </div> <!--/ .container -->
	</div><!--/ #introwrap -->
	  

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">

    function updatePage() {
            $.get('example.php?action=ping&address=8.8.8.8&rm=GW1', function(data) {
                $('#pingtest1').html(data);
            });
            
            $.get('example.php?action=ping&address=8.8.8.8&rm=GW2', function(data) {
                $('#pingtest2').html(data);
            });

            $.get('example.php?action=queue&rm=GW1', function(data) {
                $('#queue_GW1').html(data);
            });
            
            $.get('example.php?action=queue&rm=GW2', function(data) {
                $('#queue_GW2').html(data);
            });
            
    }
    
    $(document).ready(function () {
        
        updatePage();
        setInterval(function() {
            updatePage();
        }, 10000);
    });
       
    </script>
  </body>
</html>

<!--
Mikrotik Configuration
/ip firewall mangle

# All users need a marker here
add chain=prerouting action=mark-connection new-connection-mark=GW2 src-address=192.168.1.3 place-before="Catch All"
add 

add action=mark-routing chain=prerouting comment="RM for GW1" connection-mark=GW1 in-interface=bridge-local new-routing-mark=GW1
add action=mark-routing chain=prerouting comment="RM for GW2" connection-mark=GW2 in-interface=bridge-local new-routing-mark=GW2

/queue simple add max-limit=512k/1M target=192.168.1.0/24 name="Everyone Else"
/queue simple add max-limit=512k/1M target=192.168.1.3 name="Andrew" place-before="Everyone Else"


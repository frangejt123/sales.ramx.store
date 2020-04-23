<html>
<head>
    <style type="text/css">
        body{
            font-size: 15px;
            font-family: tahoma, sans-serif;
        }
        ul li{
            height: 50px;
        }
    </style>
</head>
<body>

<div>

<div>
    <b>TABLE NUMBER : #<?php echo $table_number; ?></b>
</div>

<div>
    DATETIME : <?php echo date("m/d/Y H:i:s", strtotime($transaction_time)); ?>
</div>
<br />
<ul style="list-style: none">
<?php
   foreach($details as $ind => $row){
        echo "<li>".$row["description"]." &nbsp &nbsp &nbsp &nbsp ".$row["quantity"]."</li>";
   }
?>
</ul>

Transaction #: <?php echo $id; ?>
</div>

<script type="text/javascript">
        function PrintWindow()
        {                    
           window.print();            
           CheckWindowState();
        }
       
        function CheckWindowState()
        {           
            if(document.readyState=="complete")
            {
                window.close(); 
            }
            else
            {           
                setTimeout("CheckWindowState()", 2000)
            }
        }    
        
       PrintWindow();
</script>
</body>
</html>
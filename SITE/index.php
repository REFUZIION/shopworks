<?php
session_start();
require_once("assets/src/dbcontroller.php");
$db_handle = new DBController();
if(!empty($_GET["action"])) {
switch($_GET["action"]) {
	case "add":
		if(!empty($_POST["quantity"])) {
			$productByCode = $db_handle->runQuery("SELECT * FROM tbl_products WHERE code='" . $_GET["code"] . "'");
			$itemArray = array($productByCode[0]["code"]=>array('name'=>$productByCode[0]["name"], 'code'=>$productByCode[0]["code"], 'quantity'=>$_POST["quantity"], 'price'=>$productByCode[0]["price"], 'image'=>$productByCode[0]["image"]));
			
			if(!empty($_SESSION["cart_item"])) {
				if(in_array($productByCode[0]["code"],array_keys($_SESSION["cart_item"]))) {
					foreach($_SESSION["cart_item"] as $k => $v) {
							if($productByCode[0]["code"] == $k) {
								if(empty($_SESSION["cart_item"][$k]["quantity"])) {
									$_SESSION["cart_item"][$k]["quantity"] = 0;
								}
								$_SESSION["cart_item"][$k]["quantity"] += $_POST["quantity"];
							}
					}
				} else {
					$_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
				}
			} else {
				$_SESSION["cart_item"] = $itemArray;
			}
		}
	break;
	case "remove":
		if(!empty($_SESSION["cart_item"])) {
			foreach($_SESSION["cart_item"] as $k => $v) {
					if($_GET["code"] == $k)
						unset($_SESSION["cart_item"][$k]);				
					if(empty($_SESSION["cart_item"]))
						unset($_SESSION["cart_item"]);
			}
		}
	break;
	case "empty":
		unset($_SESSION["cart_item"]);
	break;	
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/cartstyle.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Bootstrap Boilerplate</title>
</head>
<div class="container-fluid">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a href="" class="navbar-brand"><img src="assets/img/refuzion.jpg" width="75px" height="75px"></a>
        <div class="collapse navbar-collapse justify-content-between" id="navbar">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="" class="nav-link">home</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick=toggleHidden()>Shopping cart</a>
                </li>
                <li class="nav-item dropdown">
                    <a href="" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Maak uw keuze</a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a href="" class="dropdown-item">optie 1</a>
                        <a href="" class="dropdown-item">optie 2</a>
                        <a href="" class="dropdown-item">optie 3</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</div>
<div id="shopping-cart">
<div class="txt-heading">Shopping Cart</div>

    <a id="btnEmpty" href="index.php?action=empty">Empty Cart</a>
    <?php
    if(isset($_SESSION["cart_item"])){
        $total_quantity = 0;
        $total_price = 0;
    ?>	
    <table class="tbl-cart" cellpadding="10" cellspacing="1">
    <tbody>
    <tr>
    <th style="text-align:left;">Name</th>
    <th style="text-align:left;">Code</th>
    <th style="text-align:right;" width="5%">Quantity</th>
    <th style="text-align:right;" width="10%">Unit Price</th>
    <th style="text-align:right;" width="10%">Price</th>
    <th style="text-align:center;" width="5%">Remove</th>
    </tr>	
    <?php		
        foreach ($_SESSION["cart_item"] as $item){
            $item_price = $item["quantity"]*$item["price"];
            ?>
                <tr>
                <td><img src="<?php echo $item["image"]; ?>" class="cart-item-image" /><?php echo $item["name"]; ?></td>
                <td><?php echo $item["code"]; ?></td>
                <td style="text-align:right;"><?php echo $item["quantity"]; ?></td>
                <td  style="text-align:right;"><?php echo "$ ".$item["price"]; ?></td>
                <td  style="text-align:right;"><?php echo "$ ". number_format($item_price,2); ?></td>
                <td style="text-align:center;"><a href="index.php?action=remove&code=<?php echo $item["code"]; ?>" class="btnRemoveAction"><img src="icon-delete.png" alt="Remove Item" /></a></td>
                </tr>
                <?php
                $total_quantity += $item["quantity"];
                $total_price += ($item["price"]*$item["quantity"]);
            }
            ?>

    <tr>
    <td colspan="2" align="right">Total:</td>
    <td align="right"><?php echo $total_quantity; ?></td>
    <td align="right" colspan="2"><strong><?php echo "$ ".number_format($total_price, 2); ?></strong></td>
    <td></td>
    </tr>
    </tbody>
    </table>		
    <?php
    } else {
    ?>
    <div class="no-records">Your Cart is Empty</div>
    <?php 
    }
    ?>
</div>

<div class="container">
<!-- Slider hier -->
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script type="text/javascript">
    function toggleHidden()
    {
        document.getElementById('shopping-cart').classList.toggle('hidden');
       
    }
</script>
<script src="assets/js/bootstrap.min.js"></script>
</html>
<?php
interface CheckoutInterface
{
    public function getAllCart();
    public function scan($item);
    public function total();
}

class Checkout implements CheckoutInterface
{
    private $cart = [];
    private $modifiedCart = [];
    private $total = 0;
    private $totalQty = 0;
    public function __construct()
    {
    }

    public function scan($item)
    {
        $this->cart[] = $item;
    }

    public function getAllCart()
    {
        return $this->modifiedCart;
    }

    public function total()
    {
        $cart = $this->cart;
         $cart = $this->aggregateCart($cart);
        $cart = $this->deal3for2($cart);
        $cart = $this->bulkdiscount($cart);
        $cart = $this->freevga($cart);
        $this->modifiedCart = $cart;

        $this->total = 0;
        foreach ($this->modifiedCart as $c) {
            $this->total += $c['total_price'];
            $this->totalQty += $c['qty'];
        }
    }

    private function aggregateCart($cart)
    {
        $tempCart = [];
        foreach ($cart as $c) {
            if (!isset($tempCart[$c['sku']])) {
                $tempCart[$c['sku']] = $c;
                $tempCart[$c['sku']]['total_price'] = $c['price'];
                $tempCart[$c['sku']]['qty'] = 0;
                $tempCart[$c['sku']]['notes']=array();
                
            }
            $tempCart[$c['sku']]['qty']++;
            $tempCart[$c['sku']]['total_price'] = $tempCart[$c['sku']]['qty'] * $tempCart[$c['sku']]['price'];
        }
        return $tempCart;
    }
/**
 * deal3for2
 * 3 for 2 deal on Apple TVs
 * @param [type] $cart
 * @return void
 */
    private function deal3for2($cart)
    {
        $allowSku = ['atv'];
        foreach ($cart as $index => $c) {
            if (in_array($cart[$index]['sku'], $allowSku)) {
                $newQty = 0;
                $tempQty = $cart[$index]['qty'];
                while ($tempQty > 3) {
                    $newQty += 2;
                    $tempQty -= 3;
                }
                $cart[$index]['total_price'] = $cart[$index]['price'] * ($newQty + $tempQty);
                $cart[$index]['notes'][] = 'deal3for2';
            }
        }
        return $cart;
    }
/**
 * $499.99 each, if someone buys more than 4 units.
 * @param [type] $cart
 * @return void
 */
    private function bulkdiscount($cart)
    {
       
        $allowSku = ['ipd'];
       print_r($cart);
              foreach ($cart as $index => $c) {
            if (in_array($cart[$index]['sku'], $allowSku)) {
                $newPrice = 0;
               $tempQty = $cart[$index]['qty'];
                if ($tempQty > 4) {            
                  $newPrice=499.99;
                }
                $cart[$index]['total_price'] = $newPrice * ( $tempQty);
                $cart[$index]['notes'][] = 'bulkdiscount';
            }
        }
        return $cart;
    }
/**
 * We will bundle in a VGA adapter free of charge with every MacBook Pro sold.
 * @param  $cart
 * @return void
 */
    private function freevga($cart)
    {
       
        $allowSku = ['mp'];
        $my_array2=array();
            foreach ($cart as $index => $c) {
            if (in_array($cart[$index]['sku'], $allowSku)) {
                $newPrice = 0;
                         $my_array2[0]['sku'] = 'vga';
                         $my_array2[0]['total_price'] = 0;
                         $my_array2[0]['qty'] =  $cart[$index]['qty'];
                         $my_array2[0]['price'] =30;
                         $my_array2[0]['notes'][] ='freeVga';
                }                    
               $cart = array_merge($cart, $my_array2);
        }
        return $cart;
    }
}

$product = [
    [
        'sku' => 'ipd',
        'price' => 549.99
    ], [
        'sku' => 'mp',
        'price' => 1399
    ], [
        'sku' => 'atv',
        'price' => 100
    ]
];

$checkout = new Checkout();
$checkout->scan($product[2]);
$checkout->scan($product[2]);
$checkout->scan($product[2]);
$checkout->scan($product[2]);
$checkout->scan($product[2]);
$checkout->scan($product[2]);
$checkout->scan($product[0]);
$checkout->scan($product[0]);
$checkout->scan($product[0]);
$checkout->scan($product[0]);
$checkout->scan($product[0]);
$checkout->scan($product[1]);
$checkout->scan($product[1]);
$checkout->scan($product[1]);
$checkout->scan($product[1]);
$checkout->scan($product[2]);
$checkout->total();
?>

<html>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Qty</th>
            <th>Notes</th>
            <th>Price</th>
            <th>Total price</th>
        </tr />
    </thead>
    <tbody>
        <?php $i = 1;
        foreach ($checkout->getAllCart() as $cart) {
        ?>
            <tr>
                <td><?= $i++  ?></td>
                <td><?= $cart['sku'] ?></td>
                <td><?= $cart['qty'] ?></td>
                <td><?= implode(', ',$cart['notes']);?></td>
                <td><?= $cart['price'] ?></td>
                <td><?= $cart['total_price'] ?></td>
            </tr>
        <?php } ?>

    </tbody>
</table>


</html>

<?php
interface electricityBillInterface
 {

    public function code( $item );

    public function amountOfElectricity( $item );

    public function peakhours( $item );

    public function amountDue();
}

class electricityBill implements electricityBillInterface
 {
    private $param = [];
    private $total = 0;
    private $totalQty = 0;
    private $type = 0;
    private $peakhours = 0;
    private $amountOfElectricity = 0;

    public function __construct()
 {
    }

    public function code( $item )
 {
        $this->type = $item;
    }

    public function peakhours( $item )
 {
        $this->peakhours = $item;
    }

    public function amountOfElectricity( $item )
 {
        $this->amountOfElectricity = $item;
    }

    public  function negativeZero( $var ) {
        return ( $var < 0 ? 0 : $var );
    }
/**
*  I: Rate varies depending on time of usage:
*  Peak hours: RM76.00 for first 1000 kwh and RM0.065 for each additional kwh
*  Off peak hours: RM40.00 for first 1000 kwh and RM0.028 for each additional kwh. 
 * @param [type] $var
 * @return void
 */
    public  function CheckRate( $var ) {

        if ( $var == 1 )
 {
            $rate['slabrate'] = 0.065;
            $rate['basic'] = 76;

        }
        if ( $var == 0 )
 {
            $rate['slabrate'] = 0.028;
            $rate['basic'] = 40;

        }
        return $rate;
    }

    public function amountDue()
 {
        $param = $this->param;
        $param['type'] = $this->type;
        $param['amountOfElectricity'] = $this->amountOfElectricity;
        $param['peakhours'] = $this->peakhours;
        $total = $this->calculate( $param );
        return $total;
    }
    /**
    *R: RM6.00 plus RM0.052 per kwh used
    *C: RM60.00 for the first 1000 kwh and RM0.045 for each additional kwh    
    * @param [type] $param
    * @return void
    */

    private function calculate( $param )
 {
        $total = $this->total;

        if ( $param['type'] == 'R' ) {

            $consumption = 6+( $param['amountOfElectricity']*0.052 );

            $total = $consumption;

        }
        if ( $param['type'] == 'C' ) {

            $consumption = 60+( ( $this->negativeZero( $param['amountOfElectricity']-1000 ) )*0.045 );

            $total = $consumption;

        }
        if ( $param['type'] == 'I' ) {

            $rate = $this->CheckRate( $param['peakhours'] );

            $consumption = $rate['basic']+( ( $this->negativeZero( $param['amountOfElectricity']-1000 ) )*$rate['slabrate'] );

            $total = $consumption;

        }

        return number_format( ( float )$total, 2, '.', '' );

    }

}

$product = [
    [
        'code' => 'R',
        'amountOfElectricity' => 50
    ], [
        'code' => 'C',
        'amountOfElectricity' => 800
    ], [
        'code' => 'I',
        'amountOfElectricity' => 100,
        'peakhours' => 1// 1 means peak hours 0 means off peak hours
    ]
];

$i = 2;
$bill  = new electricityBill();
$bill ->code( $product[$i]['code'] );
$bill ->amountOfElectricity( $product[$i]['amountOfElectricity'] );
if ( $product[$i]['code'] == 'I' ) {
    $bill ->peakhours( $product[$i]['peakhours'] );
}
echo  $bill ->amountDue();
?>


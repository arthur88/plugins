add_action( 'woocommerce_single_product_summary', 'mokilizingas_calculation', 11 );
 
/**
 * Mokilizingas calculation class
 * Author: info@webai.lt / www.webai.lt
 * @global type $product
 */
function mokilizingas_calculation() { 
    global $product;
    
    echo "<iframe src='https://secure.mokilizingas.lt/online/calc/ml-001/{MOKILIZINGO-ID}/".$product->get_price()."/12/0/0/' scrolling='no' frameborder='0' allowtransparency='true' style='border: none; width: 100%; height: 100%;' title='Moki lizingas'></iframe>";
 
 }
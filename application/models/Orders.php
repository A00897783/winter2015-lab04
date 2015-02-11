<?php

/**
 * Data access wrapper for "orders" table.
 *
 * @author jim
 */
class Orders extends MY_Model {

    // constructor
    function __construct() {
        parent::__construct('orders', 'num');
    }

    // add an item to an order
    function add_item($num, $code) {
        $sameItem = $this->orderitems->get($num, $code);
        if($sameItem != null){ // if the item is already added
            $sameItem->quantity++;
            $this->orderitems->update($sameItem);// update quantity
        }else{
            // if the item is the new item to be added
            $orderitem = $this->orderitems->create();
            $orderitem->order = $num;
            $orderitem->item = $code;
            $orderitem->quantity = 1;
            $this->orderitems->add($orderitem);// add new item
        }
        
    }
    // calculate the total for an order
    function total($order) {
        //FIXME
        
        $myorder = $this->orders->get($order);
        $orderitems = $this->orderitems->group($order);
        $sum = 0.0;
        foreach ($orderitems as $orderitem){
            $thismenu= $this->menu->get($orderitem->item); // get the menu by item
            $sum += $thismenu->price * $orderitem->quantity;//add to sum
        }
        $sum = money_format('%i',$sum);
        $myorder->total = $sum;
        $this->orders->update($myorder);// update sum
        
        return $sum;
    }

    // retrieve the details for an order
    function details($num) {
        
    }

    // cancel an order
    function flush($num) {
    }

    // validate an order
    // it must have at least one item from each category
    function validate($num) {
        $CI = & get_instance();
        $items = $CI->orderitems->group($num);//get all orderd items for this order
        $gotem = array();
        if(count($items)>0){
            foreach($items as $item){
                $menu = $CI->menu->get($item->item); //get all menus for the item
                $gotem[$menu->category] = 1;// set some value to the category
            }
        }
        return isset($gotem['m'])&&isset($gotem['d'])&&isset($gotem['s']);
    }

}

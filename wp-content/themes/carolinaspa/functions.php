<?php
function carolinaspa_new_products($args){
    $args ['limit']   = 6;
    $args ['columns']   = 3;
    $args ['title']   = 'Just Released';

return $args;

}
add_filter('storefront_recent_products_args','carolinaspa_new_products');

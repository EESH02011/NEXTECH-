<?php
declare(strict_types=1);
// The compatibility action API remains available; the browser uses these REST resources.
function rest_routes(): array {
    return [
      ['GET','/bootstrap','bootstrap'],['GET','/products','products'],['GET','/products/{id}','product'],
      ['POST','/auth/register','register'],['POST','/auth/login','login'],['DELETE','/auth/session','logout'],
      ['POST','/auth/forgot','forgot'],['POST','/auth/reset','reset'],['PATCH','/profile','profile'],['PUT','/profile/password','password'],
      ['GET','/cart','cart'],['PUT','/cart/items/{id}','cart'],['DELETE','/cart/items/{id}','cart-remove'],
      ['GET','/favorites','favorites'],['POST','/favorites','favorite'],['GET','/addresses','addresses'],
      ['POST','/addresses','address'],['PUT','/addresses/{id}','address'],['DELETE','/addresses/{id}','address-delete'],
      ['GET','/orders','orders'],['POST','/orders','checkout'],['GET','/orders/{id}','order'],
      ['PATCH','/orders/{id}/cancellation','cancel-order'],['POST','/orders/{id}/payment','retry-payment'],
      ['GET','/admin/dashboard','admin-dashboard'],['GET','/admin/products','admin-products'],
      ['POST','/admin/products','admin-product'],['PUT','/admin/products/{id}','admin-product'],['DELETE','/admin/products/{id}','admin-product-delete'],
      ['GET','/admin/orders','admin-orders'],['PATCH','/admin/orders/{id}','admin-order'],
      ['GET','/admin/customers','admin-customers'],['GET','/admin/audit','admin-audit'],['GET','/admin/export','admin-export'],
      ['PATCH','/admin/settings','admin-settings'],['POST','/admin/uploads','upload'],
      ['GET','/admin/categories','admin-categories'],['GET','/admin/categories/{id}','admin-category'],['POST','/admin/categories','admin-category-save'],['PUT','/admin/categories/{id}','admin-category-save'],['DELETE','/admin/categories/{id}','admin-category-delete'],
      ['GET','/admin/suppliers','admin-suppliers'],['GET','/admin/suppliers/{id}','admin-supplier'],['POST','/admin/suppliers','admin-supplier-save'],['PUT','/admin/suppliers/{id}','admin-supplier-save'],['DELETE','/admin/suppliers/{id}','admin-supplier-delete'],
      ['GET','/admin/users','admin-users'],['GET','/admin/users/{id}','admin-user'],['POST','/admin/users','admin-user-save'],['PUT','/admin/users/{id}','admin-user-save'],['DELETE','/admin/users/{id}','admin-user-delete'],
      ['GET','/admin/roles','admin-roles'],['GET','/admin/movements','admin-movements'],
      ['GET','/admin/reports','admin-reports'],['POST','/webhooks/stripe','webhook-stripe'],['POST','/webhooks/mercadopago','webhook-mercadopago']
    ];
}
function resolve_rest(string $path,string $method): array {
    $path=rtrim($path,'/');$matched=false;
    foreach(rest_routes() as [$verb,$pattern,$action]){
        $regex='~^'.str_replace('\{id\}','([^/]+)',preg_quote($pattern,'~')).'$~';
        if(!preg_match($regex,$path,$m))continue;$matched=true;
        if($verb!==$method)continue;
        return [$action,isset($m[1])?rawurldecode($m[1]):null];
    }
    fail($matched?'Método no permitido.':'Ruta no encontrada.',$matched?405:404);
}

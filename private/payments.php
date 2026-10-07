<?php
declare(strict_types=1);
function provider_request(string $provider,string $path,string $method='GET',array $data=[],string $key=''): array {
    $c=config();$base=$provider==='stripe'?'https://api.stripe.com':'https://api.mercadopago.com';
    $token=$provider==='stripe'?$c['stripe']['secret_key']:$c['mercadopago']['access_token'];
    $h=['Authorization: Bearer '.$token,'Accept: application/json'];$curl=curl_init($base.$path);
    if($method==='POST'){$h[]='Content-Type: '.($provider==='stripe'?'application/x-www-form-urlencoded':'application/json');curl_setopt($curl,CURLOPT_POSTFIELDS,$provider==='stripe'?http_build_query($data):json_encode($data));}
    if($key)$h[]=($provider==='stripe'?'Idempotency-Key':'X-Idempotency-Key').': '.$key;
    curl_setopt_array($curl,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_CUSTOMREQUEST=>$method,CURLOPT_HTTPHEADER=>$h,CURLOPT_TIMEOUT=>20,CURLOPT_CONNECTTIMEOUT=>8,CURLOPT_PROTOCOLS=>CURLPROTO_HTTPS]);
    $body=curl_exec($curl);$code=curl_getinfo($curl,CURLINFO_RESPONSE_CODE);$error=curl_error($curl);curl_close($curl);
    if($body===false||$code<200||$code>=300)throw new RuntimeException('Error de comunicación con la pasarela: '.$code);
    $response=json_decode($body,true);if(!is_array($response))throw new RuntimeException('Respuesta inválida de pasarela.');return $response;
}
function create_payment(array $o,array $u): void {
    $base=rtrim(config()['base_url'],'/');if(!str_starts_with($base,'https://'))fail('Los pagos requieren una URL HTTPS configurada.',503);
    $items=query('SELECT * FROM order_items WHERE order_id=?',[$o['id']])->fetchAll();$provider=$o['provider'];
    if($provider==='stripe') {
        $line=[];foreach($items as $p)$line[]=['price_data'=>['currency'=>'mxn','product_data'=>['name'=>$p['name']],'unit_amount'=>(int)$p['price']],'quantity'=>(int)$p['qty']];
        if($o['shipping'])$line[]=['price_data'=>['currency'=>'mxn','product_data'=>['name'=>'Envío'],'unit_amount'=>(int)$o['shipping']],'quantity'=>1];
        $r=provider_request('stripe','/v1/checkout/sessions','POST',['mode'=>'payment','customer_email'=>$u['email'],'line_items'=>$line,'client_reference_id'=>$o['id'],'metadata'=>['order_id'=>$o['id']],'success_url'=>$base.'/#pedido?id='.$o['id'].'&pago=retorno','cancel_url'=>$base.'/#pedido?id='.$o['id']],$o['id']);$url=$r['url']??'';
    } else {
        $line=[];foreach($items as $p)$line[]=['id'=>$p['sku'],'title'=>$p['name'],'quantity'=>(int)$p['qty'],'unit_price'=>$p['price']/100,'currency_id'=>'MXN'];if($o['shipping'])$line[]=['id'=>'ENVIO','title'=>'Envío','quantity'=>1,'unit_price'=>$o['shipping']/100,'currency_id'=>'MXN'];
        $r=provider_request('mercadopago','/checkout/preferences','POST',['items'=>$line,'payer'=>['email'=>$u['email']],'external_reference'=>$o['id'],'back_urls'=>['success'=>$base.'/#pedido?id='.$o['id'],'pending'=>$base.'/#pedido?id='.$o['id'],'failure'=>$base.'/#pedido?id='.$o['id']],'notification_url'=>$base.'/api.php?action=webhook-mercadopago','auto_return'=>'approved'],$o['id']);$url=$r['init_point']??'';
    }
    if(!filter_var($url,FILTER_VALIDATE_URL)||!str_starts_with($url,'https://'))throw new RuntimeException('URL de pago inválida.');query('UPDATE orders SET checkout_url=?,updated_at=? WHERE id=?',[$url,now(),$o['id']]);
}
function confirm_payment(string $orderId,string $provider,string $paymentId,int $amount,string $currency,string $eventId): void {
    db()->beginTransaction();$lock=db()->getAttribute(PDO::ATTR_DRIVER_NAME)==='mysql'?' FOR UPDATE':'';$o=row('SELECT * FROM orders WHERE id=?'.$lock,[$orderId]);
    if(!$o||$o['provider']!==$provider||$o['total']!=$amount||strtoupper($currency)!=='MXN')fail('El pago no coincide con el pedido.',409);
    if(row('SELECT event_id FROM payment_events WHERE event_id=?',[$eventId])){db()->commit();return;}
    if($o['payment_status']==='paid'){
        if($o['payment_id']!==$paymentId)order_event($orderId,'Se recibió otro pago para el pedido. Soporte debe revisar el duplicado.');
        query('INSERT INTO payment_events(event_id,created_at) VALUES(?,?)',[$eventId,now()]);db()->commit();return;
    }
    $status=$o['status']==='pending'?'paid':'review';query('UPDATE orders SET payment_status=?,status=?,payment_id=?,updated_at=? WHERE id=?',['paid',$status,$paymentId,now(),$orderId]);query('INSERT INTO payment_events(event_id,created_at) VALUES(?,?)',[$eventId,now()]);order_event($orderId,$status==='paid'?'Pago confirmado por la pasarela.':'Pago recibido. Soporte está revisando el pedido.');db()->commit();
    $u=row('SELECT email FROM users WHERE id=?',[$o['user_id']]);send_email($u['email'],'Pago recibido: '.$orderId,'Tu pago fue confirmado. Consulta tu pedido: '.config()['base_url'].'/#pedido?id='.$orderId);
}
function handle_webhook(string $provider): never {
    if((int)($_SERVER['CONTENT_LENGTH']??0)>1048576)fail('Solicitud demasiado grande.',413);
    $raw=file_get_contents('php://input',false,null,0,1048577);if(strlen($raw)>1048576)fail('Solicitud demasiado grande.',413);$payload=json_decode($raw,true);if(!is_array($payload))fail('Datos inválidos.');$c=config();
    if($provider==='stripe') {
        $secret=$c['stripe']['webhook_secret']??'';if(!$secret)fail('Pasarela no configurada.',503);$parts=[];foreach(explode(',',$_SERVER['HTTP_STRIPE_SIGNATURE']??'') as $p){$v=explode('=',$p,2);if(count($v)===2)$parts[$v[0]][]=$v[1];}$t=$parts['t'][0]??'0';$valid=false;
        foreach($parts['v1']??[] as $sig)if(hash_equals(hash_hmac('sha256',$t.'.'.$raw,$secret),$sig))$valid=true;
        if(!$valid||abs(time()-(int)$t)>300)fail('Firma no válida.',401);
        if(in_array($payload['type']??'',['checkout.session.completed','checkout.session.async_payment_succeeded'],true)) {
            $sessionId=$payload['data']['object']['id']??'';if(!preg_match('/^cs_[a-zA-Z0-9_]+$/',$sessionId))fail('Sesión de pago inválida.');$s=provider_request('stripe','/v1/checkout/sessions/'.rawurlencode($sessionId));
            if(($s['payment_status']??'')==='paid')confirm_payment($s['client_reference_id']??'','stripe',$s['payment_intent']??$s['id'],(int)$s['amount_total'],$s['currency'],$payload['id']);
        }
    } elseif($provider==='mercadopago') {
        $secret=$c['mercadopago']['webhook_secret']??'';if(!$secret)fail('Pasarela no configurada.',503);$parts=[];foreach(explode(',',$_SERVER['HTTP_X_SIGNATURE']??'') as $p){$v=explode('=',trim($p),2);if(count($v)===2)$parts[$v[0]]=$v[1];}
        $id=(string)($_GET['data_id']??$payload['data']['id']??'');$requestId=$_SERVER['HTTP_X_REQUEST_ID']??'';$ts=$parts['ts']??'0';
        $manifest='id:'.strtolower($id).';request-id:'.$requestId.';ts:'.$ts.';';
        if(!$id||!$requestId||!hash_equals(hash_hmac('sha256',$manifest,$secret),$parts['v1']??''))fail('Firma no válida.',401);
        if(($payload['type']??'')==='payment'){$p=provider_request('mercadopago','/v1/payments/'.rawurlencode($id));if(($p['status']??'')==='approved')confirm_payment($p['external_reference']??'','mercadopago',(string)$p['id'],(int)round((float)$p['transaction_amount']*100),$p['currency_id'],'mp-payment-'.$p['id']);}
    } else fail('Pasarela desconocida.',404);
    json_out(['received'=>true]);
}

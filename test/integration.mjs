import assert from 'node:assert/strict';
import {spawn,spawnSync} from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';
import os from 'node:os';
import crypto from 'node:crypto';
import {fileURLToPath} from 'node:url';
const root=path.resolve(path.dirname(fileURLToPath(import.meta.url)),'..');
const php=process.env.PHP_BINARY||'php';
const tmp=fs.mkdtempSync(path.join(os.tmpdir(),'nextech-test-'));
const port=Number(process.env.TEST_PORT||8089),base='http://127.0.0.1:'+port;
const config={environment:'development',base_url:base,db:{driver:'sqlite',path:tmp+'/test.db'},mail_log:tmp+'/mail.log',smtp:{username:''},mercadopago:{access_token:'',webhook_secret:''},stripe:{secret_key:'',webhook_secret:''}};
const conf=tmp+'/config.php';fs.writeFileSync(conf,`<?php return json_decode(base64_decode('${Buffer.from(JSON.stringify(config)).toString('base64')}'),true);`);
const env={...process.env,NEXTECH_CONFIG:conf};
const adminPass=crypto.randomBytes(20).toString('hex');
const fixture=`require '${root.replaceAll('\\','/')}/private/lib.php';require '${root.replaceAll('\\','/')}/private/schema.php';require '${root.replaceAll('\\','/')}/private/seed.php';migrate(db());seed_catalog(db());query('UPDATE products SET stock=5');query("INSERT INTO users(name,email,password,role,created_at) VALUES(?,?,?,'admin',?)",['Test Admin','admin@example.test',password_hash('${adminPass}',PASSWORD_DEFAULT),now()]);`;
const setup=spawnSync(php,['-r',fixture],{env,encoding:'utf8'});assert.equal(setup.status,0,setup.stderr);
const server=spawn(php,['-d','session.save_path="'+tmp.replaceAll('\\','/')+'"','-S','127.0.0.1:'+port,'-t',root+'/public_html'],{env,stdio:['ignore','pipe','pipe']});let logs='';server.stderr.on('data',b=>logs+=b);
const results=[];
class Client {
 cookie='';csrf='';
 async call(action,data=null,params={},expected=200){const opts={headers:{Cookie:this.cookie}};if(data!==null){opts.method='POST';opts.headers['Content-Type']='application/json';opts.headers['X-CSRF-Token']=this.csrf;opts.body=JSON.stringify(data)}const r=await fetch(base+'/api.php?'+new URLSearchParams({action,...params}),opts);const c=r.headers.getSetCookie();if(c.length)this.cookie=c.at(-1).split(';')[0];const j=await r.json();assert.equal(r.status,expected,action+': '+JSON.stringify(j));if(j.csrf)this.csrf=j.csrf;return j}
}
async function test(name,fn){await fn();results.push(name);console.log('PASS '+name)}
try{
 let ready=false;for(let i=0;i<50;i++){try{await fetch(base);ready=true;break}catch{await new Promise(r=>setTimeout(r,100))}}assert.ok(ready,'Servidor no disponible');
 const a=new Client,b=new Client,admin=new Client;await a.call('bootstrap');await b.call('bootstrap');await admin.call('bootstrap');
 await test('300 productos y 20 por cada categoría',async()=>{const cats=(await a.call('bootstrap')).categories;assert.equal(cats.length,15);assert.equal((await a.call('products')).total,300);for(const c of cats)assert.equal((await a.call('products',null,{category:c.slug})).total,20)});
 await test('Búsqueda, filtros y orden de precios',async()=>{assert.ok((await a.call('products',null,{q:'laptop'})).total>0);assert.equal((await a.call('products',null,{q:"' OR 1=1 --"})).total,0);const items=(await a.call('products',null,{sort:'price-asc',limit:100})).items;assert.ok(items.every((p,i)=>!i||p.price>=items[i-1].price))});
 await test('Sesiones, CSRF y registro seguro',async()=>{await a.call('cart',null,{},401);const previous=a.csrf;a.csrf='invalid';await a.call('register',{name:'Ana Prueba',email:'ana@example.test',password:'A-testing-password-123',accept:true},{},403);a.csrf=previous;await a.call('register',{name:'Ana Prueba',email:'ana@example.test',password:'A-testing-password-123',accept:true},{},201);await b.call('register',{name:'Beto Prueba',email:'beto@example.test',password:'B-testing-password-123',accept:true},{},201);await a.call('admin-products',null,{},403);await admin.call('login',{email:'admin@example.test',password:adminPass});});
 const address={recipient:'Ana Prueba',phone:'5500000000',street:'Calle de prueba 123',colony:'Centro',city:'Ciudad de México',state:'Ciudad de México',postal_code:'06000',notes:'Datos ficticios para pruebas'};
 await test('Direcciones y aislamiento entre clientes',async()=>{await a.call('address',{...address,label:'Casa'});const ad=(await a.call('addresses'))[0];await b.call('address',{...address,id:ad.id,label:'Ajena'},{},404);assert.equal((await b.call('addresses')).length,0)});
 await test('Perfil y favoritos persistentes',async()=>{const r=await a.call('profile',{name:'Ana Actualizada',phone:'5511111111',role:'admin'});assert.equal(r.user.role,'customer');await a.call('favorite',{id:1});assert.equal((await a.call('favorites')).length,1);await a.call('favorite',{id:1});assert.equal((await a.call('favorites')).length,0)});
 let order;const key=crypto.randomUUID();const p=await a.call('product',null,{id:1});
 await test('Stock insuficiente y validación de cantidades',async()=>{await a.call('cart',{product_id:1,qty:6},{},409);await a.call('cart',{product_id:1,qty:-1},{},400);await a.call('cart',{product_id:1,qty:2})});
 await test('Checkout calcula total, reserva stock e impide doble pedido',async()=>{order=await a.call('checkout',{key,provider:'demo',address,accept:true,total:1},{},201);assert.equal(order.subtotal,p.price*2);assert.equal((await a.call('product',null,{id:1})).stock,3);const same=await a.call('checkout',{key,provider:'demo',address,accept:true});assert.equal(same.id,order.id);assert.equal((await a.call('product',null,{id:1})).stock,3);assert.equal((await a.call('cart')).length,0)});
 await test('Pedidos privados y cancelación devuelve stock una sola vez',async()=>{await b.call('order',null,{id:order.id},404);await b.call('cancel-order',{id:order.id},{},400);await a.call('cancel-order',{id:order.id});assert.equal((await a.call('product',null,{id:1})).stock,5);await a.call('cancel-order',{id:order.id},{},409);assert.equal((await a.call('product',null,{id:1})).stock,5)});
 await test('Alta, edición y ocultación de producto con auditoría',async()=>{const d={sku:'TEST-1',category:'componentes',name:'Producto de prueba',brand:'Pruebas',description:'Solo para pruebas automatizadas.',specs:{Tipo:'Prueba'},image:'',source_url:'',price:10000,compare_price:0,stock:2,active:true,featured:false,verified:true};const r=await admin.call('admin-product',d);await admin.call('admin-product',{...d,id:r.id,stock:3,active:false});await a.call('product',null,{id:r.id},404);assert.ok((await admin.call('admin-audit')).some(x=>x.action==='update-product'))});
 await test('Flujo del pedido: pago, preparación, guía y entrega',async()=>{await a.call('cart',{product_id:2,qty:1});const o=await a.call('checkout',{key:crypto.randomUUID(),provider:'demo',address,accept:true},{},201);await admin.call('admin-order',{id:o.id,status:'shipped',tracking:'Paquetería Prueba 123'},{},400);for(const s of ['paid','processing','shipped','delivered'])await admin.call('admin-order',{id:o.id,status:s,tracking:'Paquetería Prueba 123'});assert.equal((await a.call('order',null,{id:o.id})).status,'delivered');assert.equal((await admin.call('admin-dashboard')).revenue,0)});
 await test('Ventas reales bloqueadas sin configuración comercial',async()=>{await admin.call('admin-settings',{demo_mode:false},{},400);await a.call('checkout',{key:crypto.randomUUID(),provider:'stripe',address,accept:true},{},400)});
 await test('Recuperación de contraseña con token único e invalidación de sesión',async()=>{await a.call('forgot',{email:'ana@example.test'});const mail=fs.readFileSync(tmp+'/mail.log','utf8').trim().split('\n').map(JSON.parse).findLast(m=>m.subject.includes('contraseña'));const token=mail.body.match(/token=([a-f0-9]+)/)[1];await b.call('reset',{token,password:'New-testing-password-123'});assert.equal((await a.call('bootstrap')).user,null);await b.call('reset',{token,password:'Other-testing-password-123'},{},400);await a.call('login',{email:'ana@example.test',password:'New-testing-password-123'})});
 await test('Webhooks sin configurar no aceptan pagos',async()=>{await a.call('webhook-stripe',{type:'checkout.session.completed'},{},503);await a.call('webhook-mercadopago',{type:'payment'},{},503)});
 await test('Logout revoca acceso a la cuenta',async()=>{await a.call('logout',{});await a.call('orders',null,{},401)});
 console.log(JSON.stringify({passed:results.length,checks:results},null,2));
}catch(e){console.error(e);console.error(logs.slice(-4000));process.exitCode=1}finally{server.kill();await new Promise(r=>server.once('exit',r));const resolved=fs.realpathSync(tmp),tempRoot=fs.realpathSync(os.tmpdir());if(path.dirname(resolved)!==tempRoot||!path.basename(resolved).startsWith('nextech-test-'))throw Error('Ruta de limpieza inesperada');fs.rmSync(resolved,{recursive:true,force:true})}

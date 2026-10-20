<?php require_once __DIR__.'/../config/auth.php'; require_login(); $role=current_role();
$nav=[
 ['url'=>url(''),'label'=>'Dashboard','icon'=>'⌂','roles'=>['admin','doctor','nurse','pharmacist','cashier','patient','registration','medical_record','laboratory','radiology','management','auditor']],
 ['url'=>url('registration.php'),'label'=>'Pendaftaran & Antrean','icon'=>'▣','roles'=>['registration','nurse','doctor']],
 ['url'=>url('patients.php'),'label'=>'Master Pasien','icon'=>'♙','roles'=>['doctor','nurse','registration','medical_record','laboratory','radiology']],
 ['url'=>url('appointments.php'),'label'=>'Jadwal & Appointment','icon'=>'◷','roles'=>['doctor','nurse','registration','patient']],
 ['url'=>url('encounters.php'),'label'=>'SOAP / EMR','icon'=>'✚','roles'=>['doctor','nurse','medical_record']],
 ['url'=>url('nursing.php'),'label'=>'Catatan Keperawatan','icon'=>'♥','roles'=>['nurse','doctor','medical_record']],
 ['url'=>url('vitals.php'),'label'=>'Vital Signs','icon'=>'⌁','roles'=>['doctor','nurse','patient','medical_record']],
 ['url'=>url('cpoe.php'),'label'=>'CPOE / Order','icon'=>'⇢','roles'=>['doctor','nurse','laboratory','radiology']],
 ['url'=>url('lab.php'),'label'=>'Laboratorium','icon'=>'⚗','roles'=>['doctor','nurse','laboratory','medical_record','patient']],
 ['url'=>url('radiology.php'),'label'=>'Radiologi','icon'=>'◉','roles'=>['doctor','nurse','radiology','medical_record','patient']],
 ['url'=>url('prescriptions.php'),'label'=>'Resep & Farmasi','icon'=>'▤','roles'=>['doctor','pharmacist','patient']],
 ['url'=>url('medicines.php'),'label'=>'Stok Obat','icon'=>'▥','roles'=>['pharmacist']],
 ['url'=>url('billing.php'),'label'=>'Billing / Kasir','icon'=>'◈','roles'=>['cashier','patient']],
 ['url'=>url('reports.php'),'label'=>'Laporan & KPI','icon'=>'▦','roles'=>['doctor','nurse','pharmacist','cashier','management','medical_record','laboratory','radiology','registration']],
 ['url'=>url('audit.php'),'label'=>'Audit Trail','icon'=>'⌘','roles'=>['auditor']],
 ['url'=>url('user_management.php'),'label'=>'User & Access','icon'=>'♙','roles'=>['admin']],
 ['url'=>url('system_health.php'),'label'=>'System Health','icon'=>'✓','roles'=>['admin']],
]; $flash=flash();
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=h($page_title??'CareFlow Hospital')?></title><link rel="stylesheet" href="<?=h(url('assets/css/app.css'))?>"></head><body><div class="layout"><aside class="sidebar"><div class="brand">✚ <span>CareFlow</span></div><div class="hospital">HOSPITAL INFORMATION SYSTEM<br><small>Integrated Clinical Platform</small></div><div class="role-box"><span class="role-dot"></span><div><small>Login sebagai</small><strong><?=h(role_label($role))?></strong></div></div><nav><?php foreach($nav as $item): if(in_array($role,$item['roles'],true)):?><a class="<?=($page_title??'')===$item['label']?'active':''?>" href="<?=h($item['url'])?>"><span class="nav-icon"><?=$item['icon']?></span><?=$item['label']?></a><?php endif; endforeach;?></nav><div class="side-bottom"><a href="<?=h(url('auth/logout.php'))?>">↪ Keluar Sistem</a></div></aside><main class="main"><header class="topbar"><div><span class="eyebrow">CARE MANAGEMENT • <?=h(strtoupper(role_label($role)))?></span><h2><?=h($page_title??'Dashboard')?></h2><p class="page-subtitle"><?=h($page_subtitle??'Integrated Hospital Management System')?></p></div><div class="user-pill"><span class="avatar"><?=h(strtoupper(substr($_SESSION['user']['name'],0,1)))?></span><div><strong><?=h($_SESSION['user']['name'])?></strong><small><?=h(role_label($role))?> · <?=h($_SESSION['user']['employee_no']??'Patient')?></small></div></div></header><?php if($flash):?><div class="alert success">✓ <?=h($flash)?></div><?php endif;?>
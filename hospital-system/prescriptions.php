<?php
require_once __DIR__.'/config/database.php';
require_once __DIR__.'/config/auth.php';
require_page('prescriptions.php');
$page_title='Resep & Farmasi';
$page_subtitle=is_role('doctor')?'E-prescribing dari encounter dokter. Apoteker melakukan verifikasi dan dispensing.':'Medication workflow sesuai role: verifikasi apotek atau riwayat resep pasien.';
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 check_csrf(); $action=$_POST['action']??'';
 if($action==='create' && is_role('doctor')){
  $did=doctor_id($pdo); $eid=(int)($_POST['encounter_id']??0);
  $s=$pdo->prepare('SELECT id,patient_id FROM encounters WHERE id=? AND doctor_id=? LIMIT 1'); $s->execute([$eid,$did]); $e=$s->fetch();
  $medId=(int)($_POST['medicine_id']??0); $qty=(int)($_POST['quantity']??0);
  $m=$pdo->prepare('SELECT id,stock,name FROM medicines WHERE id=? AND is_active=1 LIMIT 1'); $m->execute([$medId]); $medicine=$m->fetch();
  if(!$e || !$medicine || $qty<1 || trim($_POST['dosage']??'')===''){$msg='Encounter, obat, jumlah, dan aturan pakai harus valid.';}
  else{
   $pdo->beginTransaction();
   try{
    $q=$pdo->prepare("INSERT INTO prescriptions(patient_id,doctor_id,encounter_id,status,notes) VALUES(?,?,?,'pending',?)");
    $q->execute([$e['patient_id'],$did,$eid,trim($_POST['notes']??'')]); $rid=(int)$pdo->lastInsertId();
    $q=$pdo->prepare('INSERT INTO prescription_items(prescription_id,medicine_id,quantity,dosage,route,duration) VALUES(?,?,?,?,?,?)');
    $q->execute([$rid,$medId,$qty,trim($_POST['dosage']),$_POST['route']??'oral',trim($_POST['duration']??'')]);
    $pdo->commit(); audit($pdo,'CREATE','PRESCRIPTION',$rid,'Resep elektronik dibuat dokter.'); $msg='Resep berhasil dibuat dan masuk antrean verifikasi apoteker.';
   }catch(Throwable $e2){if($pdo->inTransaction())$pdo->rollBack();$msg='Resep gagal disimpan.';}
  }
 }
 elseif($action==='status' && is_role('pharmacist')){
  $rid=(int)($_POST['id']??0); $status=$_POST['status']??'';
  if(!in_array($status,['verified','dispensed','rejected'],true)){$msg='Status resep tidak valid.';}
  else{
   $pdo->beginTransaction();
   try{
    $s=$pdo->prepare('SELECT pr.id,pr.status,pi.medicine_id,pi.quantity,m.stock,m.name FROM prescriptions pr JOIN prescription_items pi ON pi.prescription_id=pr.id JOIN medicines m ON m.id=pi.medicine_id WHERE pr.id=? FOR UPDATE');
    $s->execute([$rid]); $items=$s->fetchAll(); if(!$items) throw new RuntimeException('Prescription tidak ditemukan.');
    $current=$items[0]['status'];
    if($status==='verified'){
      if($current!=='pending') throw new RuntimeException('Hanya resep pending yang dapat diverifikasi.');
      $pdo->prepare("UPDATE prescriptions SET status='verified' WHERE id=?")->execute([$rid]);
    } elseif($status==='dispensed'){
      if($current!=='verified') throw new RuntimeException('Resep harus berstatus verified sebelum dispensing.');
      foreach($items as $it){if((int)$it['stock']<(int)$it['quantity']) throw new RuntimeException('Stok '.$it['name'].' tidak mencukupi.');}
      $u=$pdo->prepare('UPDATE medicines SET stock=stock-? WHERE id=?'); foreach($items as $it)$u->execute([(int)$it['quantity'],(int)$it['medicine_id']]);
      $pdo->prepare("UPDATE prescriptions SET status='dispensed',dispensed_at=NOW() WHERE id=?")->execute([$rid]);
    } else {
      if($current==='dispensed') throw new RuntimeException('Resep yang sudah dispensed tidak dapat direject.');
      $pdo->prepare("UPDATE prescriptions SET status='rejected' WHERE id=?")->execute([$rid]);
    }
    $pdo->commit(); audit($pdo,'UPDATE','PRESCRIPTION',$rid,'Status resep menjadi '.$status.' oleh apoteker.'); $msg='Workflow resep diperbarui.';
   }catch(Throwable $e2){if($pdo->inTransaction())$pdo->rollBack();$msg='Gagal memproses resep: '.$e2->getMessage();}
  }
 }
}
$meds=[];$enc=[];
if(is_role('doctor')){
 $meds=$pdo->query("SELECT id,code,name,stock,unit FROM medicines WHERE is_active=1 ORDER BY name")->fetchAll();
 $s=$pdo->prepare("SELECT e.id,e.patient_id,p.name,p.mrn FROM encounters e JOIN patients p ON p.id=e.patient_id WHERE e.doctor_id=? AND e.clinical_status IN ('draft','signed','amended') ORDER BY e.encounter_date DESC"); $s->execute([doctor_id($pdo)]); $enc=$s->fetchAll();
}
$where='';$params=[];
if(is_role('patient')){$where='WHERE pr.patient_id=?';$params=[current_patient_id($pdo)??0];}
elseif(is_role('doctor')){$where='WHERE pr.doctor_id=?';$params=[doctor_id($pdo)];}
elseif(is_role('pharmacist')){$where="WHERE pr.status IN ('pending','verified')";}
$sql="SELECT pr.*,p.name patient,p.mrn,u.name doctor FROM prescriptions pr JOIN patients p ON p.id=pr.patient_id JOIN doctors d ON d.id=pr.doctor_id JOIN users u ON u.id=d.user_id $where ORDER BY pr.created_at DESC";
$s=$pdo->prepare($sql);$s->execute($params);$rows=$s->fetchAll();
include __DIR__.'/includes/header.php';
?>
<?php if($msg):?><div class="alert <?=str_contains($msg,'berhasil')||str_contains($msg,'diperbarui')?'success':'danger'?>"><?=h($msg)?></div><?php endif;?>
<?php if(is_role('doctor')):?>
<div class="card"><div class="card-header"><div><h3 class="section-title">E-Prescribing</h3><p class="section-subtitle">Dokter memilih encounter yang menjadi tanggung jawabnya. Status dispensing hanya diubah apoteker.</p></div><span class="tag">Physician order</span></div>
<form method="post" class="form-grid"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="action" value="create">
<div style="grid-column:1/-1"><label>Encounter</label><select name="encounter_id" required><option value="">Pilih encounter</option><?php foreach($enc as $e):?><option value="<?=$e['id']?>">#<?=$e['id']?> — <?=h($e['mrn'].' '.$e['name'])?></option><?php endforeach;?></select></div>
<div><label>Obat</label><select name="medicine_id" required><option value="">Pilih obat</option><?php foreach($meds as $m):?><option value="<?=$m['id']?>"><?=h($m['code'].' — '.$m['name'].' (stok '.$m['stock'].' '.$m['unit'].')')?></option><?php endforeach;?></select></div>
<div><label>Jumlah</label><input type="number" name="quantity" min="1" required></div><div><label>Dosis / aturan pakai</label><input name="dosage" required placeholder="1 tablet 3x sehari"></div><div><label>Route</label><select name="route"><option value="oral">Oral</option><option value="iv">IV</option><option value="im">IM</option><option value="sc">SC</option><option value="topical">Topical</option></select></div><div><label>Durasi</label><input name="duration" placeholder="5 hari"></div>
<div style="grid-column:1/-1"><label>Catatan resep</label><textarea name="notes"></textarea></div><div><button class="btn primary">+ Terbitkan Resep</button></div></form></div><div style="height:18px"></div>
<?php endif;?>
<div class="card"><div class="card-header"><div><h3 class="section-title"><?=is_role('pharmacist')?'Pharmacy Queue':(is_role('patient')?'Resep Saya':'Resep Dokter Saya')?></h3><p class="section-subtitle">Workflow: pending → verified → dispensed / rejected.</p></div></div>
<div class="table-wrap"><table><tr><th>Pasien</th><th>Dokter</th><th>Waktu</th><th>Item</th><th>Status</th><th>Workflow</th></tr>
<?php foreach($rows as $r):$is=$pdo->prepare("SELECT pi.*,m.name,m.code FROM prescription_items pi JOIN medicines m ON m.id=pi.medicine_id WHERE pi.prescription_id=?");$is->execute([$r['id']]);$items=$is->fetchAll();?>
<tr><td><?=h($r['patient'])?><br><span class="mini"><?=h($r['mrn'])?></span></td><td><?=h($r['doctor'])?></td><td><?=h(date('d/m/Y H:i',strtotime($r['created_at'])))?></td><td><?php foreach($items as $it):?><b><?=h($it['name'])?></b> × <?=h((string)$it['quantity'])?><br><span class="mini"><?=h($it['dosage'])?> · <?=h($it['route'])?> · <?=h($it['duration'])?></span><br><?php endforeach;?></td><td><span class="badge <?=$r['status']==='pending'?'warn':($r['status']==='rejected'?'danger':'ok')?>"><?=h(strtoupper($r['status']))?></span></td><td><?php if(is_role('pharmacist')):?><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?=$r['id']?>"><select name="status"><option value="verified">Verified</option><option value="dispensed">Dispensed</option><option value="rejected">Rejected</option></select><button class="btn secondary" style="margin-top:6px">Proses</button></form><?php else:?><span class="mini">Read-only</span><?php endif;?></td></tr>
<?php endforeach;?>
<?php if(!$rows):?><tr><td colspan="6"><div class="empty"><strong>Belum ada resep</strong><p>Resep akan muncul sesuai scope akun.</p></div></td></tr><?php endif;?></table></div></div>
<?php include __DIR__.'/includes/footer.php';?>

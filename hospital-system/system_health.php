<?php
require_once __DIR__.'/config/database.php';
require_once __DIR__.'/config/auth.php';
require_role(['admin']);
$page_title='System Health';
$page_subtitle='Pemeriksaan integritas koneksi, schema, tabel, dan data inti CareFlow.';
$required = [
 'users'=>['id','name','email','password_hash','role','is_active'],
 'patients'=>['id','user_id','mrn','name','gender','birth_date','insurance'],
 'doctors'=>['id','user_id','department','specialty','license_no'],
 'appointments'=>['id','patient_id','doctor_id','appointment_date','status','queue_no'],
 'registrations'=>['id','patient_id','registered_by','registration_date','visit_type','guarantor','complaint','status'],
 'encounters'=>['id','patient_id','doctor_id','subjective','objective','assessment','plan','diagnosis_code','clinical_status'],
 'encounter_addenda'=>['id','encounter_id','author_user_id','note','created_at'],
 'nursing_notes'=>['id','patient_id','encounter_id','nurse_id','assessment','intervention','evaluation'],
 'vital_signs'=>['id','patient_id','recorded_by','temperature','systolic','diastolic','pulse','spo2','status'],
 'medicines'=>['id','code','name','stock','min_stock','expiry_date','price'],
 'prescriptions'=>['id','patient_id','doctor_id','encounter_id','status'],
 'prescription_items'=>['id','prescription_id','medicine_id','quantity','dosage'],
 'cpoe_orders'=>['id','encounter_id','patient_id','ordered_by','order_type','priority','status'],
 'lab_results'=>['id','order_id','test_name','result_value','flag','verified_by'],
 'radiology_results'=>['id','order_id','modality','findings','impression','reported_by'],
 'invoices'=>['id','patient_id','invoice_no','total','status'],
 'invoice_items'=>['id','invoice_id','description','qty','price'],
 'payments'=>['id','invoice_id','amount','method','received_by'],
 'audit_logs'=>['id','user_id','action','module','record_id','description','created_at'],
];
$checks=[];$tableCount=0;
foreach($required as $table=>$cols){
 $exists=false;$actual=[];$error='';
 try{$s=$pdo->query("SHOW COLUMNS FROM `{$table}`");foreach($s->fetchAll() as $c)$actual[]=$c['Field'];$exists=true;$tableCount++;}
 catch(Throwable $e){$error=$e->getMessage();}
 $missing=array_values(array_diff($cols,$actual));
 $checks[]=['table'=>$table,'exists'=>$exists,'missing'=>$missing,'count'=>$exists?(int)$pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn():null,'error'=>$error];
}
$users=(int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$patients=(int)$pdo->query('SELECT COUNT(*) FROM patients')->fetchColumn();
$encounters=(int)$pdo->query('SELECT COUNT(*) FROM encounters')->fetchColumn();
$registrations=(int)$pdo->query('SELECT COUNT(*) FROM registrations')->fetchColumn();
$issues=0;foreach($checks as $c){$issues += !$c['exists'] ? 1 : count($c['missing']);}
include __DIR__.'/includes/header.php';
?>
<div class="grid cards">
 <div class="card stat"><div class="icon">✓</div><div class="num"><?=h((string)$tableCount)?> / <?=count($required)?></div><div class="label">Tabel tersedia</div><div class="hint">Schema inti SIMRS</div></div>
 <div class="card stat"><div class="icon">◫</div><div class="num"><?=h((string)$users)?></div><div class="label">Akun pengguna</div><div class="hint">RBAC aktif</div></div>
 <div class="card stat"><div class="icon">♙</div><div class="num"><?=h((string)$patients)?></div><div class="label">Pasien</div><div class="hint">Master patient index</div></div>
 <div class="card stat"><div class="icon">⚕</div><div class="num"><?=h((string)$encounters)?></div><div class="label">Encounter</div><div class="hint">Clinical documentation</div></div>
</div>
<div class="card" style="margin-top:18px"><div class="card-header"><div><h3 class="section-title">Schema Integrity</h3><p class="section-subtitle">Tabel yang diperiksa: <?=count($required)?> · Registrations: <?=h((string)$registrations)?> episode.</p></div><span class="badge <?=$issues===0?'ok':'danger'?>"><?=$issues===0?'HEALTHY':'NEEDS REVIEW'?></span></div>
<div class="table-wrap"><table><tr><th>Table</th><th>Status</th><th>Rows</th><th>Missing columns</th></tr>
<?php foreach($checks as $c):?><tr><td><b><?=h($c['table'])?></b></td><td><span class="badge <?=$c['exists']&&count($c['missing'])===0?'ok':'danger'?>"><?=$c['exists']?(count($c['missing'])===0?'READY':'INCOMPLETE'):'MISSING'?></span></td><td><?=h((string)($c['count']??'—'))?></td><td><?=h($c['missing']?implode(', ',$c['missing']):'—')?></td></tr><?php endforeach;?></table></div></div>
<div class="grid two" style="margin-top:18px"><div class="card"><h3 class="section-title">Environment</h3><div class="info-list"><div><span>Database</span><strong>hospital_system</strong></div><div><span>Host</span><strong>127.0.0.1:3307</strong></div><div><span>PHP</span><strong><?=h(PHP_VERSION)?></strong></div><div><span>Server time</span><strong><?=h(date('d/m/Y H:i:s'))?></strong></div></div></div><div class="card"><h3 class="section-title">Expected workflow</h3><div class="timeline"><div class="timeline-item"><strong>Registration</strong><div class="mini">MRN → episode → queue → service</div></div><div class="timeline-item"><strong>Clinical</strong><div class="mini">Triage → vital → SOAP → diagnosis → CPOE</div></div><div class="timeline-item"><strong>Support</strong><div class="mini">LIS/RIS → prescription → pharmacy → billing → audit</div></div></div></div></div>
<?php include __DIR__.'/includes/footer.php';?>

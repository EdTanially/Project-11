<?php
require __DIR__ . '/config/config.php';

$pdo = db();
$page = $_GET['page'] ?? (user() ? 'dashboard' : 'login');

if ($page === 'logout') { session_destroy(); redirect('?page=login'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'login') {
            $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? AND active = 1 LIMIT 1');
            $stmt->execute([trim($_POST['username'] ?? '')]);
            $u = $stmt->fetch();
            if ($u && hash_equals($u['password'], hash('sha256', $_POST['password'] ?? ''))) {
                $_SESSION['user'] = $u; audit('LOGIN','session',null,'Login berhasil'); redirect('?page=dashboard');
            }
            flash('error','Username atau password tidak sesuai.'); redirect('?page=login');
        }
        verify_csrf(); require_login();
        if ($action === 'create_order') {
            require_role(['Dokter']);
            $patientId=(int)$_POST['patient_id']; $type=$_POST['type']; $item=trim($_POST['item_name']);
            $map=['Laboratorium'=>'Laboratorium','Radiologi'=>'Radiologi','Farmasi'=>'Farmasi','Tindakan Medis'=>'Rawat Inap','Konsultasi'=>'Unit Konsulen','Diet'=>'Gizi'];
            $destination=$map[$type] ?? 'Rawat Inap';
            $p=$pdo->prepare('SELECT * FROM patients WHERE id=?'); $p->execute([$patientId]); $patient=$p->fetch();
            if (!$patient || !$item) throw new Exception('Data pasien/order wajib lengkap.');
            $warnings=[];
            if ($type==='Farmasi' && stripos((string)$patient['allergies'], 'Penisilin') !== false && stripos($item,'penisilin') !== false) $warnings[]='Potensi alergi obat: pasien tercatat alergi Penisilin.';
            $dup=$pdo->prepare('SELECT COUNT(*) FROM orders WHERE patient_id=? AND type=? AND item_name=? AND status NOT IN (\'Selesai\',\'Dibatalkan\')');
            $dup->execute([$patientId,$type,$item]); if ((int)$dup->fetchColumn()>0) $warnings[]='Terdeteksi kemungkinan duplikasi order aktif.';
            $status = $warnings ? 'Menunggu Validasi' : 'Menunggu Validasi';
            $stmt=$pdo->prepare('INSERT INTO orders(patient_id,doctor_id,type,item_name,priority,clinical_note,dosage,route,schedule_at,status,destination_unit) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
            $stmt->execute([$patientId,user()['id'],$type,$item,$_POST['priority'],trim($_POST['clinical_note']),trim($_POST['dosage'] ?? ''),trim($_POST['route'] ?? ''),$_POST['schedule_at']?:null,$status,$destination]);
            $id=(int)$pdo->lastInsertId(); audit('CREATE_ORDER','order',$id,'Order '.$type.' dibuat'.($warnings?' | ALERT: '.implode(' | ',$warnings):''));
            $_SESSION['order_alerts']=$warnings; redirect('?page=orders&created='.$id);
        }
        if ($action === 'validate_order') {
            require_role(['Dokter','Administrator SIMRS']); $id=(int)$_POST['id'];
            $pdo->prepare('UPDATE orders SET status=\'Dikirim\', validated_at=NOW() WHERE id=? AND status=\'Menunggu Validasi\'')->execute([$id]); audit('VALIDATE_ORDER','order',$id,'Order divalidasi dan didistribusikan'); flash('success','Order divalidasi dan dikirim ke unit tujuan.'); redirect('?page=orders');
        }
        if ($action === 'change_order') {
            require_role(['Dokter']); $id=(int)$_POST['id']; $reason=trim($_POST['reason']); if(!$reason) throw new Exception('Alasan perubahan wajib diisi.');
            $stmt=$pdo->prepare('UPDATE orders SET item_name=?, clinical_note=?, priority=?, status=\'Perlu Revisi\', changed_reason=? WHERE id=? AND status IN (\'Menunggu Validasi\',\'Dikirim\')');
            $stmt->execute([trim($_POST['item_name']),trim($_POST['clinical_note']),$_POST['priority'],$reason,$id]); audit('CHANGE_ORDER','order',$id,'Alasan: '.$reason); flash('success','Order berhasil diubah dan dicatat ke audit trail.'); redirect('?page=orders');
        }
        if ($action === 'cancel_order') {
            require_role(['Dokter']); $id=(int)$_POST['id']; $reason=trim($_POST['reason']); if(!$reason) throw new Exception('Alasan pembatalan wajib diisi.');
            $stmt=$pdo->prepare('UPDATE orders SET status=\'Dibatalkan\', cancellation_reason=? WHERE id=? AND status NOT IN (\'Selesai\',\'Dibatalkan\')');
            $stmt->execute([$reason,$id]); audit('CANCEL_ORDER','order',$id,'Alasan: '.$reason); flash('success','Order dibatalkan dan alasan disimpan.'); redirect('?page=orders');
        }
        if ($action === 'execute_order') {
            $id=(int)$_POST['id']; $role=user()['role']; $allowed=['Petugas Laboratorium','Petugas Radiologi','Petugas Farmasi','Perawat/Bidan','Petugas Gizi','Dokter','Administrator SIMRS']; require_role($allowed);
            $result=trim($_POST['result_text'] ?? ''); $status=in_array($role,['Petugas Radiologi'])?'Dijadwalkan':'Diproses';
            $finish = $role==='Petugas Laboratorium' || $role==='Petugas Radiologi' || $role==='Petugas Farmasi' || $role==='Petugas Gizi' || $role==='Perawat/Bidan';
            if ($finish && $result!=='') $status='Selesai';
            $stmt=$pdo->prepare('UPDATE orders SET status=?, result_text=?, executed_at=COALESCE(executed_at,NOW()), completed_at=? WHERE id=? AND status IN (\'Dikirim\',\'Diproses\',\'Dijadwalkan\')');
            $stmt->execute([$status,$result,$status==='Selesai'?date('Y-m-d H:i:s'):null,$id]); audit('EXECUTE_ORDER','order',$id,'Pelaksanaan/hasil: '.$result); flash('success','Progress order diperbarui.'); redirect('?page=workqueue');
        }
    } catch (Throwable $e) { flash('error',$e->getMessage()); redirect($_SERVER['HTTP_REFERER'] ?? '?page=dashboard'); }
}

if ($page === 'login') {
    $f=flash();
    ?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>CPOE SIMRS — Login</title><link rel="stylesheet" href="assets/style.css"></head><body><div class="login"><div class="login-card"><div class="login-brand"><div class="brand-mark">+</div><div><b>SIMRS CPOE</b><div class="helper">Order Management</div></div></div><?php if($f): ?><div class="alert <?=e($f[0])?>"><?=e($f[1])?></div><?php endif; ?><form method="post"><input type="hidden" name="action" value="login"><div class="field"><label>Username</label><input name="username" placeholder="contoh: dokter" required></div><div class="field" style="margin-top:12px"><label>Password</label><input type="password" name="password" placeholder="••••••••" required></div><button class="btn btn-primary" style="width:100%;margin-top:18px">Masuk ke CPOE</button></form><div class="helper" style="margin-top:15px">Demo akun: dokter/dokter123 · lab/lab123 · farmasi/farmasi123 · admin/admin123</div></div></div></body></html><?php exit; }

require_login();
$u=user();
$pathMap=['dashboard'=>'Dashboard','orders'=>'Order Management','new_order'=>'Buat Order','workqueue'=>'Work Queue Unit','patients'=>'Pasien','audit'=>'Audit Trail'];
$title=$pathMap[$page] ?? 'CPOE SIMRS';
$flashMsg=flash();

$stats=$pdo->query("SELECT
 SUM(status='Menunggu Validasi') pending,
 SUM(status='Dikirim') sent,
 SUM(status='Diproses' OR status='Dijadwalkan') processing,
 SUM(status='Selesai') done,
 SUM(status='Dibatalkan') cancelled FROM orders")->fetch();

function render_header(string $title): void { global $u; ?><div class="app"><aside class="sidebar"><div class="brand"><div class="brand-mark">+</div><span><b>CPOE</b><small>SIMRS • Order Mgmt</small></span></div><div class="nav-label">Workspace</div><nav class="nav"><a class="<?=($GLOBALS['page']=='dashboard'?'active':'')?>" href="?page=dashboard"><span class="nav-icon">⌂</span><span>Dashboard</span></a><a class="<?=($GLOBALS['page']=='orders'?'active':'')?>" href="?page=orders"><span class="nav-icon">☷</span><span>Order Management</span></a><?php if($u['role']==='Dokter'): ?><a class="<?=($GLOBALS['page']=='new_order'?'active':'')?>" href="?page=new_order"><span class="nav-icon">＋</span><span>Buat Order</span></a><?php endif; ?><a class="<?=($GLOBALS['page']=='workqueue'?'active':'')?>" href="?page=workqueue"><span class="nav-icon">◈</span><span>Work Queue</span></a><a class="<?=($GLOBALS['page']=='patients'?'active':'')?>" href="?page=patients"><span class="nav-icon">◉</span><span>Pasien</span></a><?php if(in_array($u['role'],['Administrator SIMRS','Dokter'],true)): ?><a class="<?=($GLOBALS['page']=='audit'?'active':'')?>" href="?page=audit"><span class="nav-icon">⌁</span><span>Audit Trail</span></a><?php endif; ?></nav><div class="nav-label">Session</div><nav class="nav"><a href="?page=logout"><span class="nav-icon">↪</span><span>Keluar</span></a></nav><div class="sidebar-foot"><b><?=e($u['role'])?></b><br><span><?=e($u['unit_name'] ?? 'SIMRS')?></span></div></aside><main class="main"><header class="topbar"><div class="crumb"><?=$title?></div><div class="top-actions"><div><b><?=e($u['name'])?></b><div class="helper" style="text-align:right"><?=e($u['unit_name'] ?? '')?></div></div><div class="avatar"><?=e(strtoupper(substr($u['name'],0,1)))?></div></div></header><div class="content"><?php }
function render_footer(): void { ?></div></main></div><?php }

render_header($title);
if($flashMsg): ?><div class="alert <?=e($flashMsg[0])?>"><?=e($flashMsg[1])?></div><?php endif;

if ($page==='dashboard') {
    $recent=$pdo->query("SELECT o.*,p.name patient_name,p.medical_record_no, u.name doctor_name FROM orders o JOIN patients p ON p.id=o.patient_id JOIN users u ON u.id=o.doctor_id ORDER BY o.created_at DESC LIMIT 8")->fetchAll();
    ?><div class="hero"><div><div class="eyebrow">CPOE • ORDER MANAGEMENT</div><div class="title">Command center pelayanan order medis.</div><div class="subtitle">Alur terintegrasi dari pembuatan order dokter, validasi sistem, distribusi otomatis ke unit, pelaksanaan, dokumentasi hasil, hingga penutupan order.</div></div><?php if($u['role']==='Dokter'): ?><a class="btn btn-primary" href="?page=new_order">＋ Buat Order Baru</a><?php endif; ?></div>
    <div class="grid stats"><div class="card stat"><div class="label">Menunggu Validasi</div><div class="n"><?=e($stats['pending']??0)?></div><div class="note">Perlu review dokter/admin</div></div><div class="card stat"><div class="label">Dikirim ke Unit</div><div class="n"><?=e($stats['sent']??0)?></div><div class="note">Siap diproses unit tujuan</div></div><div class="card stat"><div class="label">Sedang Diproses</div><div class="n"><?=e($stats['processing']??0)?></div><div class="note">Diproses / dijadwalkan</div></div><div class="card stat"><div class="label">Selesai</div><div class="n"><?=e($stats['done']??0)?></div><div class="note">Hasil tervalidasi / tindakan terdokumentasi</div></div></div>
    <div class="grid two" style="margin-top:18px"><div class="card"><div class="card-head"><b>Order Terbaru</b><a class="btn btn-ghost btn-sm" href="?page=orders">Lihat semua</a></div><div class="table-wrap"><table class="table"><tr><th>Pasien</th><th>Jenis</th><th>Order</th><th>Status</th></tr><?php foreach($recent as $o): ?><tr><td><b><?=e($o['patient_name'])?></b><div class="helper"><?=e($o['medical_record_no'])?></div></td><td><?=e($o['type'])?></td><td><?=e($o['item_name'])?></td><td><?=status_badge($o['status'])?></td></tr><?php endforeach; ?></table></div></div><div class="card"><div class="card-head"><b>Alur SOP</b></div><div class="card-body"><div class="timeline"><?php foreach(['A. Persiapan akses & role','B. Pembuatan order','C. Validasi + alert','D. Distribusi ke unit','E–G. Pelaksanaan unit','H. Perubahan / pembatalan','J. Penutupan order'] as $i=>$s): ?><div class="timeline-item"><div class="dot"></div><div><b><?=e($s)?></b><div class="helper">Tahap <?=($i+1)?> dalam proses CPOE</div></div></div><?php endforeach; ?></div></div></div></div><?php
}

if ($page==='orders') {
    $q=trim($_GET['q']??''); $status=$_GET['status']??''; $sql="SELECT o.*,p.name patient_name,p.medical_record_no,u.name doctor_name FROM orders o JOIN patients p ON p.id=o.patient_id JOIN users u ON u.id=o.doctor_id WHERE 1=1"; $params=[];
    if($q!==''){ $sql.=' AND (p.name LIKE ? OR p.medical_record_no LIKE ? OR o.item_name LIKE ?)'; array_push($params,"%$q%","%$q%","%$q%"); }
    if($status!==''){ $sql.=' AND o.status=?'; $params[]=$status; } $sql.=' ORDER BY o.created_at DESC'; $st=$pdo->prepare($sql);$st->execute($params);$orders=$st->fetchAll();
    ?><div class="hero"><div><div class="eyebrow">ORDER MANAGEMENT</div><div class="title">Semua order terintegrasi.</div><div class="subtitle">Pantau status, unit tujuan, hasil dan riwayat order secara end-to-end.</div></div></div><div class="card"><div class="card-body"><form class="filters"><input class="field" style="max-width:320px;padding:11px;border:1px solid #d9e5e3;border-radius:11px" name="q" placeholder="Cari pasien / RM / order..." value="<?=e($q)?>"><select name="status" style="padding:11px;border:1px solid #d9e5e3;border-radius:11px"><option value="">Semua status</option><?php foreach(['Menunggu Validasi','Dikirim','Diproses','Dijadwalkan','Selesai','Perlu Revisi','Dibatalkan'] as $s): ?><option <?=$status===$s?'selected':''?>><?=e($s)?></option><?php endforeach; ?></select><button class="btn btn-primary">Filter</button></form></div><div class="table-wrap"><table class="table"><tr><th>Waktu</th><th>Pasien</th><th>Jenis</th><th>Order</th><th>Unit</th><th>Prioritas</th><th>Status</th><th>Aksi</th></tr><?php if(!$orders): ?><tr><td colspan="8"><div class="empty">Belum ada order.</div></td></tr><?php endif; foreach($orders as $o): ?><tr><td><?=e(date('d M H:i',strtotime($o['created_at'])))?></td><td><b><?=e($o['patient_name'])?></b><div class="helper"><?=e($o['medical_record_no'])?></div></td><td><?=e($o['type'])?></td><td><b><?=e($o['item_name'])?></b><div class="helper"><?=e($o['clinical_note']??'')?></div></td><td><?=e($o['destination_unit'])?></td><td><?=e($o['priority'])?></td><td><?=status_badge($o['status'])?></td><td><a class="btn btn-ghost btn-sm" href="?page=order&id=<?=e((string)$o['id'])?>">Detail</a> <?php if($u['role']==='Dokter' && in_array($o['status'],['Menunggu Validasi','Perlu Revisi'],true)): ?><form method="post" style="display:inline"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="action" value="validate_order"><input type="hidden" name="id" value="<?=$o['id']?>"><button class="btn btn-primary btn-sm">Validasi</button></form><?php endif; ?></td></tr><?php endforeach; ?></table></div></div><?php
}

if ($page==='new_order') {
    require_role(['Dokter']); $patients=$pdo->query('SELECT * FROM patients ORDER BY name')->fetchAll();
    ?><div class="hero"><div><div class="eyebrow">DOKTER</div><div class="title">Buat order medis</div><div class="subtitle">Isi parameter order, tentukan prioritas, lalu lakukan validasi dan tanda tangan elektronik melalui proses CPOE.</div></div></div><div class="card"><div class="card-body"><form method="post"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="action" value="create_order"><div class="form-grid"><div class="field"><label>Pasien *</label><select name="patient_id" required><option value="">Pilih pasien</option><?php foreach($patients as $p): ?><option value="<?=$p['id']?>"><?=e($p['name'])?> — <?=e($p['medical_record_no'])?> (<?=e($p['room'])?>)</option><?php endforeach; ?></select></div><div class="field"><label>Jenis Order *</label><select name="type" required><?php foreach(['Laboratorium','Radiologi','Farmasi','Tindakan Medis','Konsultasi','Diet'] as $t): ?><option><?=e($t)?></option><?php endforeach; ?></select></div><div class="field"><label>Nama / item pemeriksaan *</label><input name="item_name" required placeholder="Contoh: CBC, Foto Thorax, Ceftriaxone 1 g"></div><div class="field"><label>Prioritas *</label><select name="priority"><option>Routine</option><option>Urgent</option><option>Stat</option></select></div><div class="field"><label>Dosis (opsional)</label><input name="dosage" placeholder="Contoh: 1 x 1 g"></div><div class="field"><label>Rute (opsional)</label><input name="route" placeholder="Contoh: IV / PO / IM"></div><div class="field"><label>Jadwal (opsional)</label><input type="datetime-local" name="schedule_at"></div><div class="field full"><label>Catatan klinis / instruksi</label><textarea name="clinical_note" placeholder="Indikasi, instruksi khusus, persiapan, dll."></textarea></div></div><div style="margin-top:18px;padding:15px;border-radius:14px;background:#f1faf8"><b>Validasi otomatis aktif</b><div class="helper">Sistem akan mengecek field wajib dan, bila data tersedia, memberikan alert alergi serta kemungkinan duplikasi order sebelum disimpan/ditandatangani.</div></div><div style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px"><a class="btn btn-ghost" href="?page=orders">Batal</a><button class="btn btn-primary">Simpan & Proses Validasi</button></div></form></div></div><?php
}

if ($page==='order') {
    $id=(int)($_GET['id']??0);
    $st=$pdo->prepare('SELECT o.*,p.name patient_name,p.medical_record_no,p.allergies,p.room,u.name doctor_name FROM orders o JOIN patients p ON p.id=o.patient_id JOIN users u ON u.id=o.doctor_id WHERE o.id=?');
    $st->execute([$id]); $o=$st->fetch();
    if(!$o){ echo '<div class="empty">Order tidak ditemukan.</div>'; render_footer(); exit; }
    ?>
    <div class="hero"><div><div class="eyebrow">ORDER DETAIL #<?=e((string)$o['id'])?></div><div class="title"><?=e($o['type'])?> · <?=e($o['item_name'])?></div><div class="subtitle">Detail order, validasi, pelaksanaan, serta kontrol perubahan/pembatalan.</div></div><a class="btn btn-ghost" href="?page=orders">← Kembali</a></div>
    <div class="grid two"><div class="card"><div class="card-head"><b>Ringkasan Order</b><?=status_badge($o['status'])?></div><div class="card-body"><div class="patient-banner"><div class="patient-main"><div class="patient-icon"><?=e(strtoupper(substr($o['patient_name'],0,1)))?></div><div><b><?=e($o['patient_name'])?></b><div class="helper"><?=e($o['medical_record_no'])?> · <?=e($o['room'])?></div></div></div><div><div class="helper">Dokter</div><b><?=e($o['doctor_name'])?></b></div></div><div class="grid three" style="margin-top:18px"><div><div class="helper">Prioritas</div><b><?=e($o['priority'])?></b></div><div><div class="helper">Unit</div><b><?=e($o['destination_unit'])?></b></div><div><div class="helper">Dibuat</div><b><?=e(date('d M Y H:i',strtotime($o['created_at'])))?></b></div></div><div style="margin-top:18px"><div class="helper">Catatan klinis</div><div><?=nl2br(e($o['clinical_note']?:'-'))?></div></div><?php if($o['result_text']): ?><div style="margin-top:18px"><div class="helper">Hasil / dokumentasi</div><div><?=nl2br(e($o['result_text']))?></div></div><?php endif; ?></div></div>
    <div class="card"><div class="card-head"><b>Kontrol Order</b></div><div class="card-body"><?php if($u['role']==='Dokter' && in_array($o['status'],['Menunggu Validasi','Dikirim','Perlu Revisi'],true)): ?><form method="post"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="action" value="change_order"><input type="hidden" name="id" value="<?=$o['id']?>"><div class="field"><label>Item / pemeriksaan</label><input name="item_name" value="<?=e($o['item_name'])?>" required></div><div class="field" style="margin-top:12px"><label>Prioritas</label><select name="priority"><option <?=$o['priority']==='Routine'?'selected':''?>>Routine</option><option <?=$o['priority']==='Urgent'?'selected':''?>>Urgent</option><option <?=$o['priority']==='Stat'?'selected':''?>>Stat</option></select></div><div class="field" style="margin-top:12px"><label>Catatan klinis</label><textarea name="clinical_note"><?=e($o['clinical_note'])?></textarea></div><div class="field" style="margin-top:12px"><label>Alasan perubahan *</label><textarea name="reason" required placeholder="Wajib dicatat untuk audit trail"></textarea></div><button class="btn btn-primary" style="width:100%">Simpan Perubahan</button></form><form method="post" style="margin-top:12px"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="action" value="cancel_order"><input type="hidden" name="id" value="<?=$o['id']?>"><div class="field"><label>Alasan pembatalan *</label><textarea name="reason" required placeholder="Wajib dicatat untuk audit trail"></textarea></div><button class="btn btn-danger" style="width:100%">Batalkan Order</button></form><?php else: ?><div class="empty">Kontrol perubahan/pembatalan tersedia untuk dokter sebelum order selesai.</div><?php endif; ?></div></div></div><?php
}

if ($page==='workqueue') {
    $unit=$u['unit_name']; $where="o.status IN ('Dikirim','Diproses','Dijadwalkan')"; $params=[];
    if(!in_array($u['role'],['Administrator SIMRS','Dokter'],true)){$where.=' AND o.destination_unit=?';$params[]=$unit;}
    $st=$pdo->prepare("SELECT o.*,p.name patient_name,p.medical_record_no,p.allergies FROM orders o JOIN patients p ON p.id=o.patient_id WHERE $where ORDER BY CASE o.priority WHEN 'Stat' THEN 1 WHEN 'Urgent' THEN 2 ELSE 3 END,o.created_at ASC");$st->execute($params);$items=$st->fetchAll();
    ?><div class="hero"><div><div class="eyebrow">UNIT WORK QUEUE</div><div class="title">Antrian pelaksanaan.</div><div class="subtitle">Tampilkan order yang sudah didistribusikan ke unit. Input hasil/tindakan dan status akhir akan tercatat pada SIMRS.</div></div></div><div class="grid three"><?php foreach(['Laboratorium','Radiologi','Farmasi','Rawat Inap','Gizi','Unit Konsulen'] as $unitName): $cnt=$pdo->prepare("SELECT COUNT(*) FROM orders WHERE destination_unit=? AND status IN ('Dikirim','Diproses','Dijadwalkan')");$cnt->execute([$unitName]); ?><div class="card stat"><div class="label"><?=e($unitName)?></div><div class="n"><?=$cnt->fetchColumn()?></div><div class="note">order aktif</div></div><?php endforeach; ?></div><div class="card" style="margin-top:18px"><div class="card-head"><b>Order dalam antrian</b></div><div class="table-wrap"><table class="table"><tr><th>Pasien</th><th>Order</th><th>Unit</th><th>Prioritas</th><th>Status</th><th>Pelaksanaan</th></tr><?php if(!$items): ?><tr><td colspan="6"><div class="empty">Tidak ada order pada antrian kamu.</div></td></tr><?php endif; foreach($items as $o): ?><tr><td><b><?=e($o['patient_name'])?></b><div class="helper"><?=e($o['medical_record_no'])?> · Alergi: <?=e($o['allergies']?:'tidak tercatat')?></div></td><td><?=e($o['type'])?> — <b><?=e($o['item_name'])?></b></td><td><?=e($o['destination_unit'])?></td><td><?=e($o['priority'])?></td><td><?=status_badge($o['status'])?></td><td><form method="post" style="min-width:260px"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="action" value="execute_order"><input type="hidden" name="id" value="<?=$o['id']?>"><div style="display:flex;gap:7px"><input name="result_text" placeholder="Hasil / dokumentasi" style="flex:1;padding:9px;border:1px solid #d9e5e3;border-radius:9px"><button class="btn btn-primary btn-sm">Simpan</button></div></form></td></tr><?php endforeach; ?></table></div></div><?php
}

if ($page==='patients') {
    $patients=$pdo->query('SELECT * FROM patients ORDER BY created_at DESC')->fetchAll();
    ?><div class="hero"><div><div class="eyebrow">PATIENT MASTER</div><div class="title">Pasien & ringkasan klinis.</div><div class="subtitle">Data pasien digunakan sebagai konteks utama saat dokter membuat order dan sistem melakukan validasi.</div></div></div><div class="grid three"><?php foreach($patients as $p): ?><div class="card"><div class="card-body"><div class="patient-main"><div class="patient-icon"><?=e(strtoupper(substr($p['name'],0,1)))?></div><div><b><?=e($p['name'])?></b><div class="helper"><?=e($p['medical_record_no'])?></div></div></div><div style="margin-top:15px"><div class="helper">Ruangan</div><b><?=e($p['room']?:'-')?></b></div><div style="margin-top:10px"><div class="helper">Diagnosis</div><div><?=e($p['diagnosis']?:'-')?></div></div><div style="margin-top:10px"><div class="helper">Alergi</div><div><?=e($p['allergies']?:'Tidak ada / belum tercatat')?></div></div></div></div><?php endforeach; ?></div><?php
}

if ($page==='audit') {
    require_role(['Administrator SIMRS','Dokter']); $logs=$pdo->query('SELECT a.*,u.name user_name,u.role FROM audit_logs a LEFT JOIN users u ON u.id=a.user_id ORDER BY a.created_at DESC LIMIT 120')->fetchAll();
    ?><div class="hero"><div><div class="eyebrow">AUDIT TRAIL</div><div class="title">Jejak aktivitas pengguna.</div><div class="subtitle">Perubahan dan pembatalan order menyimpan alasan, user, entitas, dan waktu untuk keterlacakan transaksi.</div></div></div><div class="card"><div class="table-wrap"><table class="table"><tr><th>Waktu</th><th>Pengguna</th><th>Role</th><th>Aksi</th><th>Entitas</th><th>Detail</th></tr><?php foreach($logs as $l): ?><tr><td><?=e(date('d M Y H:i:s',strtotime($l['created_at'])))?></td><td><?=e($l['user_name']??'System')?></td><td><?=e($l['role']??'-')?></td><td><b><?=e($l['action'])?></b></td><td><?=e($l['entity'])?> #<?=e((string)$l['entity_id'])?></td><td><?=e($l['detail'])?></td></tr><?php endforeach; ?></table></div></div><?php
}
render_footer();

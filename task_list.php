<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<?php if ($_SESSION['login_type'] != 3) : ?>
			<div class="card-header">
				<div class="card-tools">
					<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_project"><i class="fa fa-plus"></i> Thêm dự án mới</a>
				</div>
			</div>
		<?php endif; ?>
		<div class="card-body">
			<table class="table tabe-hover table-condensed" id="list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="20%">
					<col width="15%">
					<col width="15%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">STT</th>
						<th>Tên dự án</th>
						<th>Tên công việc</th>
						<th>Ngày bắt đầu dự án</th>
						<th>Ngày kết thúc dự án</th>
						<th>Trạng thái dự án</th>
						<th>Trạng thái công việc</th>
						<th>Tùy chọn</th>
					</tr>
				</thead>
				<tbody style="font-weight: lighter !important;">
					<?php
					$i = 1;
					$where = "";
					if ($_SESSION['login_type'] == 3) {
						$where = " where concat('[',REPLACE(users_id,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
					}

					$stat = array("Chưa thực hiện", "Đã bắt đầu", "Đang thực hiện", "Tạm dừng", "Quá hạn", "Đã hoàn thành", "Hoàn thành quá hạn");
					$qry = $conn->query("SELECT t.*,p.name as pname,p.start_date,p.status as pstatus, p.end_date,p.id as pid FROM task_list t inner join project_list p on p.id = t.project_id $where AND users_id IN (SELECT id FROM users $where) order by p.name asc");
					while ($row = $qry->fetch_assoc()) :
						$trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
						unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
						$desc = strtr(html_entity_decode($row['description']), $trans);
						$desc = str_replace(array("<li>", "</li>"), array("", ", "), $desc);
						$tprog = $conn->query("SELECT * FROM task_list where project_id = {$row['pid']}")->num_rows;
						$cprog = $conn->query("SELECT * FROM task_list where project_id = {$row['pid']} and status = 3")->num_rows;
						$prog = $tprog > 0 ? ($cprog / $tprog) * 100 : 0;
						$prog = $prog > 0 ?  number_format($prog, 2) : $prog;
						$prod = $conn->query("SELECT * FROM user_productivity where project_id = {$row['pid']}")->num_rows;
						if ($row['pstatus'] == 0 && strtotime(date('d-m-Y')) > strtotime($row['end_date']) &&  $cprog > 0) :
							$row['pstatus'] = 6;
						elseif ($row['pstatus'] == 0 && strtotime(date('d-m-Y')) > strtotime($row['end_date'])) :
								$row['pstatus'] = 4;
						elseif ($row['pstatus'] == 0 && strtotime(date('d-m-Y')) >= strtotime($row['start_date'])) :
							if ($prod  > 0  || $cprog > 0)
								$row['pstatus'] = 2;
							else
								$row['pstatus'] = 1;
						endif;

					?>
						<tr>
							<td style="font-weight: normal !important;" class="text-center"><?php echo $i++ ?></td>
							<td>
								<p><b><?php echo ucwords($row['pname']) ?></b></p>
							</td>
							<td>
								<p><b><?php echo ucwords($row['task']) ?></b></p>
								<p class="truncate"><?php echo strip_tags($desc) ?></p>
							</td>
							<td><b><?php echo date("d-m-Y", strtotime($row['start_date'])) ?></b></td>
							<td><b><?php echo date("d-m-Y", strtotime($row['end_date'])) ?></b></td>
							<td class="text-center">
								<?php
								if ($stat[$row['pstatus']] == 'Chưa thực hiện') {
									echo "<span class='badge badge-secondary'>{$stat[$row['pstatus']]}</span>";
								} elseif ($stat[$row['pstatus']] == 'Đã bắt đầu') {
									echo "<span class='badge badge-primary'>{$stat[$row['pstatus']]}</span>";
								} elseif ($stat[$row['pstatus']] == 'Đang thực hiện') {
									echo "<span class='badge badge-info'>{$stat[$row['pstatus']]}</span>";
								} elseif ($stat[$row['pstatus']] == 'Tạm dừng') {
									echo "<span class='badge badge-warning'>{$stat[$row['pstatus']]}</span>";
								} elseif ($stat[$row['pstatus']] == 'Quá hạn') {
									echo "<span class='badge badge-danger'>{$stat[$row['pstatus']]}</span>";
								} elseif ($stat[$row['pstatus']] == 'Đã hoàn thành') {
									echo "<span class='badge badge-success'>{$stat[$row['pstatus']]}</span>";
								}elseif ($stat[$row['pstatus']] == 'Hoàn thành quá hạn') {
									echo "<span class='badge badge-danger'>{$stat[$row['pstatus']]}</span>";
								}
								?>
							</td>
							<td class="text-center">
								<?php
								if ($row['status'] == 1) {
									echo "<span class='badge badge-secondary'>Chưa thực hiện</span>";
								} elseif ($row['status'] == 2) {
									echo "<span class='badge badge-primary'>Đang thực hiện</span>";
								} elseif ($row['status'] == 3) {
									echo "<span class='badge badge-success'>Đã hoàn thành</span>";
								}elseif ($row['status'] == 4) {
									echo "<span class='badge badge-danger'>Hoàn thành quá hạn</span>";
								}
								?>
							</td>
							<td class="text-center">
								<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
									Tùy chọn
								</button>
								<div class="dropdown-menu">
									<a class="dropdown-item new_productivity" data-pid='<?php echo $row['pid'] ?>' data-tid='<?php echo $row['id'] ?>' data-task='<?php echo ucwords($row['task']) ?>' href="javascript:void(0)">Thêm hoạt động</a>
								</div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<style>
	table p {
		margin: unset !important;
	}

	table td {
		vertical-align: middle !important
	}
</style>
<script>
	$(document).ready(function() {
		$('#list').dataTable()
		$('.new_productivity').click(function() {
			uni_modal("<i class='fa fa-plus'></i> Thêm tiến độ cho: " + $(this).attr('data-task'), "manage_progress.php?pid=" + $(this).attr('data-pid') + "&tid=" + $(this).attr('data-tid'), 'large')
		})
	})

	function delete_project($id) {
		start_load()
		$.ajax({
			url: 'ajax.php?action=delete_project',
			method: 'POST',
			data: {
				id: $id
			},
			success: function(resp) {
				if (resp == 1) {
					alert_toast("Đã lưu dữ liệu thành công!", 'success')
					setTimeout(function() {
						location.reload()
					}, 1500)

				}
			}
		})
	}
</script>
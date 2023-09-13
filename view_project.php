<?php
include 'db_connect.php';
$stat = array("Chưa thực hiện", "Đã bắt đầu", "Đang thực hiện", "Tạm dừng", "Quá hạn", "Đã hoàn thành", "Hoàn thành quá hạn");
$qry = $conn->query("SELECT * FROM project_list where id = " . $_GET['id'])->fetch_array();
foreach ($qry as $k => $v) {
	$$k = $v;
}
$tprog = $conn->query("SELECT * FROM task_list where project_id = {$id}")->num_rows;
$cprog = $conn->query("SELECT * FROM task_list where project_id = {$id} and status = 3")->num_rows;
$cprog1 = $conn->query("SELECT * FROM task_list where project_id = {$id} and status = 4")->num_rows;
$prog = $tprog > 0 ? ($cprog / $tprog) * 100 : 0;
$prog = $prog > 0 ?  number_format($prog, 2) : $prog;
$prod = $conn->query("SELECT * FROM user_productivity where project_id = {$id}")->num_rows;
// ...
if ($status == 0 || $status == 1 || $status == 2 || $status == 3 || $status == 4 || $status == 5 || $status == 6) {
    if (strtotime(date('d-m-Y')) > strtotime($end_date) && $cprog > 0 || $cprog1 > 0) {
        $status = 6;
    } elseif (strtotime(date('d-m-Y')) > strtotime($end_date)) {
        $status = 4;
    } elseif ($tprog >= 1 && $cprog == $tprog ) {
        $status = 5;
    } elseif (strtotime(date('d-m-Y')) >= strtotime($start_date)) {
        if ($prod > 0 || $cprog > 0) {
            $status = 2;
        } else {
            $status = 1;
        }
    }

    // Cập nhật trạng thái trong CSDL
    $update_query = "UPDATE project_list SET status = $status WHERE id = $id";
    $conn->query($update_query);
}

// if($status == 0|1|2|4 && strtotime(date('d-m-Y')) > strtotime($end_date) && $cprog > 0){
// 	$status = 6;
// 	// Câu truy vấn để cập nhật trạng thái trong CSDL
// 	$update_query = "UPDATE project_list SET status = 6 WHERE id = $id";
// 	$conn->query($update_query);
// } elseif($status == 0|1|2 && strtotime(date('d-m-Y')) > strtotime($end_date)){
// 	$status = 4;
// 	// Câu truy vấn để cập nhật trạng thái trong CSDL
// 	$update_query = "UPDATE project_list SET status = 4 WHERE id = $id";
// 	$conn->query($update_query);
// }
// elseif($status == 0|1|2 && $cprog ==$tprog && $tprog >=1){
// 	$status = 5;
// 	// Câu truy vấn để cập nhật trạng thái trong CSDL
// 	$update_query = "UPDATE project_list SET status = 5 WHERE id = $id";
// 	$conn->query($update_query);
	
// } elseif ($status == 0|1|5 && strtotime(date('d-m-Y')) >= strtotime($start_date)) {
// 	if ($prod > 0 || $cprog > 0){
// 		$status = 2;
// 		// Câu truy vấn để cập nhật trạng thái trong CSDL
// 		$update_query = "UPDATE project_list SET status = 2 WHERE id = $id";
// 		$conn->query($update_query);
// 	} else {
// 		$status = 1;
// 		// Câu truy vấn để cập nhật trạng thái trong CSDL
// 		$update_query = "UPDATE project_list SET status = 1 WHERE id = $id";
// 		$conn->query($update_query);
// 	}
// }
// ...

$manager = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where id = $manager_id");
$manager = $manager->num_rows > 0 ? $manager->fetch_array() : array();
?>
<div class="col-lg-12">
	<div class="row">
		<div class="col-md-12">
			<div class="callout callout-info">
				<div class="col-md-12">
					<div class="row">
						<div class="col-sm-6">
							<dl>
								<dt><b class="border-bottom border-primary">Tên dự án:</b></dt>
								<dd><?php echo ucwords($name) ?></dd>
								<dt><b class="border-bottom border-primary">Mô tả:</b></dt>
								<dd><?php echo html_entity_decode($description) ?></dd>
								<dd>
								<b class="border-bottom border-primary">File:</b><br>
									<?php
									if (isset($file_name) && !empty($file_name)) {
										$file_names = explode(',', $file_name);
										foreach ($file_names as $file) {
											echo '<a href="assets/uploads/project-documents/' . $file . '" target="_blank">' . $file . '</a><br>';
										}
									} else {
										echo 'Không có tệp đính kèm.';
									}
									?>
								</dd>
							</dl>
						</div>
						<div class="col-md-6">
							<dl>
								<dt><b class="border-bottom border-primary">Ngày bắt đầu:</b></dt>
								<dd><?php echo date("d-m-Y", strtotime($start_date)) ?></dd>
							</dl>
							<dl>
								<dt><b class="border-bottom border-primary">Ngày kết thúc:</b></dt>
								<dd><?php echo date("d-m-Y", strtotime($end_date)) ?></dd>
							</dl>
							<dl>
								<dt><b class="border-bottom border-primary">Trạng thái:</b></dt>
								<dd>
									<?php
									if ($stat[$status] == 'Chưa thực hiện') {
										echo "<span class='badge badge-secondary'>{$stat[$status]}</span>";
									} elseif ($stat[$status] == 'Đã bắt đầu') {
										echo "<span class='badge badge-primary'>{$stat[$status]}</span>";
									} elseif ($stat[$status] == 'Đang thực hiện') {
										echo "<span class='badge badge-info'>{$stat[$status]}</span>";
									} elseif ($stat[$status] == 'Tạm dừng') {
										echo "<span class='badge badge-warning'>{$stat[$status]}</span>";
									} elseif ($stat[$status] == 'Quá hạn') {
										echo "<span class='badge badge-danger'>{$stat[$status]}</span>";
									} elseif ($stat[$status] == 'Đã hoàn thành') {
										echo "<span class='badge badge-success'>{$stat[$status]}</span>";
									}elseif ($stat[$status] == 'Hoàn thành quá hạn') {
										echo "<span class='badge badge-danger'>{$stat[$status]}</span>";
									}
									?>
								</dd>
							</dl>
							<dl>
								<dt><b class="border-bottom border-primary">Quản lý:</b></dt>
								<dd>
									<?php if (isset($manager['id'])) : ?>
										<div class="d-flex align-items-center mt-1">
											<img class="img-circle img-thumbnail p-0 shadow-sm border-info img-sm mr-3" src="assets/uploads/<?php echo $manager['avatar'] ?>" alt="Avatar">
											<b><?php echo ucwords($manager['name']) ?></b>
										</div>
									<?php else : ?>
										<small><i>Người quản lý đã bị xóa khỏi cơ sở dữ liệu</i></small>
									<?php endif; ?>
								</dd>
							</dl>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<div class="card card-outline card-primary">
				<div class="card-header">
					<span><b>Thành viên của nhóm:</b></span>
					<div class="card-tools">
						<!-- <button class="btn btn-primary bg-gradient-primary btn-sm" type="button" id="manage_team">Manage</button> -->
					</div>
				</div>
				<div class="card-body">
					<ul class="users-list clearfix">
						<?php
						if (!empty($user_ids)) :
							$members = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where id in ($user_ids) order by concat(firstname,' ',lastname) asc");
							while ($row = $members->fetch_assoc()) :
						?>
								<li>
									<img class="img-circle img-bordered-sm" src="assets/uploads/<?php echo $row['avatar'] ?>" alt="User Image">
									<a class="users-list-name" href="javascript:void(0)"><?php echo ucwords($row['name']) ?></a>
									<!-- <span class="users-list-date">Today</span> -->
								</li>
						<?php
							endwhile;
						endif;
						?>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="card card-outline card-primary">
				<div class="card-header">
					<span><b>Danh sách công việc:</b></span>
					<?php if ($_SESSION['login_type'] != 3) : ?>
						<div class="card-tools">
							<button class="btn btn-primary bg-gradient-primary btn-sm" type="button" id="new_task"><i class="fa fa-plus"></i> Thêm công việc</button>
						</div>
					<?php endif; ?>
				</div>
				<div class="card-body p-0">
					<div class="table-responsive">
						<table class="table table-condensed m-0 table-hover">
							<colgroup>
								<col width="5%">
								<col width="25%">
								<col width="30%">
								<col width="15%">
								<col width="15%">
							</colgroup>
							<thead>
								<th>STT</th>
								<th>Tên công việc</th>
								<th>Mô tả</th>
								<th>Trạng thái</th>
								<th>Tùy chọn</th>
							</thead>
							<tbody>
								<?php
								$i = 1;
								$where = "";
								if($_SESSION['login_type'] == 3){
									$where = " where concat('[',REPLACE(users_id,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
								}
								$tasks = $conn->query("SELECT * FROM task_list where project_id = {$id} AND users_id IN (SELECT id FROM users $where) order by task asc");
								//$tasks = $conn->query("SELECT * FROM task_list where project_id = {$id} order by task asc");
								while ($row = $tasks->fetch_assoc()) :
									$trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
									unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
									$desc = strtr(html_entity_decode($row['description']), $trans);
									$desc = str_replace(array("<li>", "</li>"), array("", ", "), $desc);
								?>
									<tr>
										<td class="text-center"><?php echo $i++ ?></td>
										<td class=""><b><?php echo ucwords($row['task']) ?></b></td>
										<td class="">
											<p class="truncate"><?php echo strip_tags($desc) ?></p>
										</td>
										<td>
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
												<a class="dropdown-item view_task" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-task="<?php echo $row['task'] ?>">Xem</a>
												<div class="dropdown-divider"></div>
											
													<a class="dropdown-item edit_task" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-task="<?php echo $row['task'] ?>">Sửa</a>
													<div class="dropdown-divider"></div>
													<?php if ($_SESSION['login_type'] != 3) : ?>
													<a class="dropdown-item delete_task" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Xóa</a>
												<?php endif; ?>
											</div>
										</td>
									</tr>
								<?php
								endwhile;
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<b>Tiến độ/ Hoạt động của thành viên</b>
					<div class="card-tools">
						<button class="btn btn-primary bg-gradient-primary btn-sm" type="button" id="new_productivity"><i class="fa fa-plus"></i> Thêm hoạt động</button>
					</div>
				</div>
				<div class="card-body">
					<?php
					$where_condition = "p.user_id = {$_SESSION['login_id']} OR u.type IN (1, 2)";
					if ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 2) {
						$where_condition = "u.type IN (1, 2, 3)";
					}
					$progress = $conn->query("SELECT p.*, concat(u.firstname,' ',u.lastname) as uname, u.avatar, t.task 
											FROM user_productivity p 
											INNER JOIN users u ON u.id = p.user_id 
											INNER JOIN task_list t ON t.id = p.task_id 
											WHERE p.project_id = $id AND ($where_condition)
											ORDER BY unix_timestamp(p.date_created) DESC");
					while ($row = $progress->fetch_assoc()) :
					?>
						<div class="post">

							<div class="user-block">
								<?php if ($_SESSION['login_id'] == $row['user_id']) : ?>
									<span class="btn-group dropleft float-right">
										<span class="btndropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor: pointer;">
											<i class="fa fa-ellipsis-v"></i>
										</span>
										<div class="dropdown-menu">
											<a class="dropdown-item manage_progress" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-task="<?php echo $row['task'] ?>">Sửa</a>
											<div class="dropdown-divider"></div>
											<a class="dropdown-item delete_progress" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Xóa</a>
										</div>
									</span>
								<?php endif; ?>
								<img class="img-circle img-bordered-sm" src="assets/uploads/<?php echo $row['avatar'] ?>" alt="user image">
								<span class="username">
									<a href="#"><?php echo ucwords($row['uname']) ?> &nbsp;[ <?php echo ucwords($row['task']) ?> ]</a>
								</span>
								<span class="description">
									<span class="fa fa-calendar-day"></span>
									<span><b><?php echo date('m-d-Y', strtotime($row['date'])) ?></b></span>
									<span> &nbsp;| &nbsp;</span>
									<span class="fa fa-user-clock"></span>
									<span>Start: <b><?php echo date('h:i A', strtotime($row['date'] . ' ' . $row['start_time'])) ?></b></span>
									<span> &nbsp;| &nbsp;</span>
									<span>End: <b><?php echo date('h:i A', strtotime($row['date'] . ' ' . $row['end_time'])) ?></b></span>
								</span>



							</div>
							<!-- /.user-block -->
							<div >
								<?php
								if (!empty($row['subject'])) {
								?>
									<span style="font-weight: 700;" class="border-bottom border-primary">Tiêu đề:</span>
								<?php
									echo " " . html_entity_decode($row['subject']);
								} else {
								?>
									<span style="font-weight: 700;" class="border-bottom border-primary">Tiêu đề:</span>
									<span style="font-weight: 500;color: #767676; font-style: italic; font-size: 0.9rem;"> &nbsp;Không có...</span>
								<?php
								}
								?>

							</div>
							<div>
								<!-- Danh sách tệp đã tải lên -->
								<?php
								if (!empty($row['file_name'])) {
									$files = explode(',', $row['file_name']);
									echo '<div style="font-weight: 700;"><span class="border-bottom border-primary">Tệp đính kèm:</span></div>';
									foreach ($files as $file) {
										echo '<div><a href="uploads1/progress/' . $file . '">' . $file . '</a></div>';
									}
								}
								else {
										echo '<span style="font-weight: 700;" class="border-bottom border-primary">Files:</span>';
										echo '<span style="font-weight: 500;color: #767676; font-style: italic; font-size: 0.9rem;"> &nbsp;Không có...</span>';
									}
								?>
							</div>
							<div>
								<?php
								if (!empty($row['comment'])) {
									echo '<span style="font-weight: 700;" class="border-bottom border-primary">Nội dung:</span><br>';
									echo html_entity_decode($row['comment']);
								} else {
									echo '<span style="font-weight: 700;" class="border-bottom border-primary">Nội dung:</span>';
									echo '<span style="font-weight: 500;color: #767676; font-style: italic; font-size: 0.9rem;"> &nbsp;Không có nội dung...</span>';
								
								}
								?>
							</div>

							<p>
								<!-- <a href="#" class="link-black text-sm"><i class="fas fa-link mr-1"></i> Demo File 1 v2</a> -->
							</p>
						</div>
						<div class="post clearfix"></div>
					<?php endwhile; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
	.users-list>li img {
		border-radius: 50%;
		height: 67px;
		width: 67px;
		object-fit: cover;
	}

	.users-list>li {
		width: 33.33% !important
	}

	.truncate {
		-webkit-line-clamp: 1 !important;
	}

	.callout a{
		text-decoration: none !important;
	}

	.callout a:hover{
		color: #007bff;
	}
</style>
<script>
	$('#new_task').click(function() {
		uni_modal("Tạo công việc mới cho <?php echo ucwords($name) ?>", "manage_task.php?pid=<?php echo $id ?>", "mid-large")
	})
	$('.edit_task').click(function() {
		uni_modal("Chỉnh sửa công việc: " + $(this).attr('data-task'), "manage_task.php?pid=<?php echo $id ?>&id=" + $(this).attr('data-id'), "mid-large")
	})
	$('.view_task').click(function() {
		uni_modal("Chi tiết công việc", "view_task.php?id=" + $(this).attr('data-id'), "mid-large")
	})
	$('#new_productivity').click(function() {
		uni_modal("<i class='fa fa-plus'></i> Thêm tiến độ", "manage_progress.php?pid=<?php echo $id ?>", 'large')
	})
	$('.manage_progress').click(function() {
		uni_modal("<i class='fa fa-edit'></i> Cập nhật tiến độ", "manage_progress.php?pid=<?php echo $id ?>&id=" + $(this).attr('data-id'), 'large')
	})
	$('.delete_progress').click(function() {
		_conf("Bạn có chắc chắn xóa tiến độ này?", "delete_progress", [$(this).attr('data-id')])
	})
	$('.delete_task').click(function() {
		_conf("Bạn có chắc chắn xóa công việc này?", "delete_task", [$(this).attr('data-id')])
	})

	function delete_progress($id) {
		start_load()
		$.ajax({
			url: 'ajax.php?action=delete_progress',
			method: 'POST',
			data: {
				id: $id
			},
			success: function(resp) {
				if (resp == 1) {
					alert_toast("Đã xóa dữ liệu thành công", 'success')
					setTimeout(function() {
						location.reload()
					}, 1500)

				}
			}
		})
	};

	function delete_task($id) {
		start_load()
		$.ajax({
			url: 'ajax.php?action=delete_task',
			method: 'POST',
			data: {
				id: $id
			},
			success: function(resp) {
				if (resp == 1) {
					alert_toast("Đã xóa dữ liệu thành công", 'success')
					setTimeout(function() {
						location.reload()
					}, 1500)

				}
			}
		})
	}
</script>
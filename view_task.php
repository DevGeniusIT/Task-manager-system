<?php
include 'db_connect.php';
if (isset($_GET['id'])) {
	$qry = $conn->query("SELECT * FROM task_list where id = " . $_GET['id'])->fetch_array();
	foreach ($qry as $k => $v) {
		$$k = $v;
	}
}
?>
<div class="container-fluid">
	<dl>
		<dt><b class="border-bottom border-primary">Tên công việc</b></dt>
		<dd><?php echo ucwords($task) ?></dd>
	</dl>
	<dl>
		<dt><b class="border-bottom border-primary">Trạng thái</b></dt>
		<dd>
			<?php
			if ($status == 1) {
				echo "<span class='badge badge-secondary'>Chưa thực hiện</span>";
			} elseif ($status == 2) {
				echo "<span class='badge badge-primary'>Đang thực hiện</span>";
			} elseif ($status == 3) {
				echo "<span class='badge badge-success'>Đã hoàn thành</span>";
			}elseif ($status == 4) {
				echo "<span class='badge badge-danger'>Hoàn thành quá hạn</span>";
			}
			?>
		</dd>
	</dl>
	<dl style="margin-bottom: 0px;">
		<span><b class="border-bottom border-primary">Thành viên:</b></span>
		<ul class="users-list clearfix" style="margin-bottom: 0px !important;">
			<?php
			if (!empty($users_id)) :
				$members = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where id in ($users_id) order by concat(firstname,' ',lastname) asc");
				while ($row = $members->fetch_assoc()) :
			?>
					<li style="width: 25% !important;">
						<div class="text-center"><img class="img-circle img-bordered-sm" style="width: 2rem; height: 2rem;" src="assets/uploads/<?php echo $row['avatar'] ?>" alt="User Image"></div>
						<a class="users-list-name" href="javascript:void(0)"><?php echo ucwords($row['name']) ?></a>
						<!-- <span class="users-list-date">Today</span> -->
					</li>
			<?php
				endwhile;
			endif;
			?>
		</ul>
	</dl>
	<dl>
		<dt><b class="border-bottom border-primary">Mô tả</b></dt>
		<dd><?php echo html_entity_decode($description) ?></dd>
	</dl>
	<dl><b class="border-bottom border-primary">File:</b></dl>
	<dl>
		<?php
		if (isset($file_name) && !empty($file_name)) {
			$file_names = explode(',', $file_name);
			foreach ($file_names as $file) {
				echo '<a href="assets/uploads/task_documents/' . $file . '" target="_blank">' . $file . '</a><br>';
			}
		} else {
			echo 'Không có tệp đính kèm.';
		}
		?>
	</dl>
</div>
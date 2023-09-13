<?php if (!isset($conn)) {
	include 'db_connect.php';
} ?>

<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-body">
			<form action="" id="manage-project">

				<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="" class="control-label">Tên dự án</label>
							<input type="text" class="form-control form-control-sm" name="name" value="<?php echo isset($name) ? $name : '' ?>">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Trạng thái</label>
							<select name="status" id="status" class="custom-select custom-select-sm">
								<option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Chưa thực hiện</option>
								<option value="3" <?php echo isset($status) && $status == 3 ? 'selected' : '' ?>>Tạm dừng</option>
								<option value="5" <?php echo isset($status) && $status == 5 ? 'selected' : '' ?>>Đã hoàn thành</option>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="" class="control-label">Ngày bắt đầu</label>
							<input type="date" class="form-control form-control-sm" autocomplete="off" name="start_date" value="<?php echo isset($start_date) ? date("Y-m-d", strtotime($start_date)) : '' ?>">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="" class="control-label">Ngày kết thúc</label>
							<input type="date" class="form-control form-control-sm" autocomplete="off" name="end_date" value="<?php echo isset($end_date) ? date("Y-m-d", strtotime($end_date)) : '' ?>">
						</div>
					</div>
				</div>
				<div class="row">
					<?php if ($_SESSION['login_type'] == 1) : ?>
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="control-label">Quản lý</label>
								<select class="form-control form-control-sm select2" name="manager_id">
									<option></option>
									<?php
									$managers = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where type = 2 order by concat(firstname,' ',lastname) asc ");
									while ($row = $managers->fetch_assoc()) :
									?>
										<option value="<?php echo $row['id'] ?>" <?php echo isset($manager_id) && $manager_id == $row['id'] ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
									<?php endwhile; ?>
								</select>
							</div>
						</div>
					<?php else : ?>
						<input type="hidden" name="manager_id" value="<?php echo $_SESSION['login_id'] ?>">
					<?php endif; ?>
					<div class="col-md-6">
						<div class="form-group">
							<label for="" class="control-label">Nhân viên</label>
							<select class="form-control form-control-sm select2" multiple="multiple" name="user_ids[]">
								<option></option>
								<?php
								$employees = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where type = 3 order by concat(firstname,' ',lastname) asc ");
								while ($row = $employees->fetch_assoc()) :
								?>
									<option value="<?php echo $row['id'] ?>" <?php echo isset($user_ids) && in_array($row['id'], explode(',', $user_ids)) ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
								<?php endwhile; ?>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-10">
						<div class="form-group">
							<label for="" class="control-label">Mô tả</label>
							<textarea name="description" id="" cols="30" rows="10" class="summernote form-control">
								<?php echo isset($description) ? $description : '' ?>
							</textarea>
						</div>
						<div>
							<form enctype="multipart/form-data" id="file-upload-form">
								<div class="form-group">
									<label for="exampleFormControlFile1">Choose File</label>
									<input type="file" class="form-control-file" id="exampleFormControlFile1" name="file_name[]" multiple>
									<small id="fileHelp" class="form-text text-muted">Kích thước tệp tối đa: 30 MB. Các định dạng được phép: JPG, PNG, JPEG, PDF, DOCX, XLSX, XLS, PPTX.</small>
								</div>
								<div>
									<?php
									if (isset($file_name) && !empty($file_name)) {
										$file_names = explode(',', $file_name);
										foreach ($file_names as $file) {
											echo $file . ' <a href="delete_file.php?file=' . $file . '"><i class="fa fa-trash"></i></a><br>';

											
										}
									}
									?>
									<div id="file-error-container" class="text-danger mt-2"></div>

								</div>
							</form>

						</div>
					</div>

				</div>
			</form>
		</div>
		<div class="card-footer border-top border-info">
			<div class="d-flex w-100 justify-content-center align-items-center">
				<button id="save-button" class="btn btn-flat  bg-gradient-primary mx-2" form="manage-project">Lưu</button>
				<button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href='index.php?page=project_list'">Thoát</button>
			</div>
		</div>
	</div>
</div>
<script>
	$('#manage-project').submit(function(e) {
		e.preventDefault()
		start_load()
		$.ajax({
			url: 'ajax.php?action=save_project',
			data: new FormData($(this)[0]),
			cache: false,
			contentType: false,
			processData: false,
			method: 'POST',
			type: 'POST',
			success: function(resp) {
				if (resp == 1) {
					alert_toast('Đã lưu dữ liệu thành công!', "success");
					setTimeout(function() {
						location.href = 'index.php?page=project_list'
					}, 2000)
				}
			}
		})
	});

	document.addEventListener("DOMContentLoaded", function() {
		const fileInput = document.querySelector('#exampleFormControlFile1');
		const errorContainer = document.querySelector('#file-error-container');

		fileInput.addEventListener('change', function() {
			errorContainer.innerHTML = ''; // Xóa thông báo lỗi trước đó

			const allowedFormats = ['pptx', 'jpeg','docx', 'pdf', 'xls', 'xlsx', 'jpg', 'png'];
			const maxFileSize = 30 * 1024 * 1024; // 5MB
			const maxFileSizeDisplay = (maxFileSize / (1024 * 1024)).toFixed(2) + 'MB';

			for (const file of fileInput.files) {
				const fileName = file.name;
				const fileExtension = fileName.split('.').pop().toLowerCase();
				const fileSize = file.size;
				
				if ((!allowedFormats.includes(fileExtension)) && fileSize > maxFileSize) {
					errorContainer.innerHTML += `<p class="text-danger"> Định dạng không hợp lệ và dung lượng vượt quá giới hạn.</p>`;
					fileInput.value = null; // Xóa tệp tải lên không hợp lệ
				} else if (fileSize > maxFileSize) {
					errorContainer.innerHTML += `<p class="text-danger">Dung lượng vượt quá giới hạn.</p>`;
					fileInput.value = null; // Xóa tệp tải lên không hợp lệ
				} else if (!allowedFormats.includes(fileExtension)) {
					errorContainer.innerHTML += `<p class="text-danger">Định dạng không hợp lệ.</p>`;
					fileInput.value = null; // Xóa tệp tải lên không hợp lệ
				}
			}
		});
	});


</script>
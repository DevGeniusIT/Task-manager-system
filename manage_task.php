<head>
	<link href="path/to/select2.min.css" rel="stylesheet">

</head>
<?php
session_start();

include 'db_connect.php';
if (isset($_GET['id'])) {
	$qry = $conn->query("SELECT * FROM task_list where id = " . $_GET['id'])->fetch_array();
	foreach ($qry as $k => $v) {
		$$k = $v;
	}
}
?>
<div class="container-fluid">
	<form action="" id="manage-task">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<input type="hidden" name="project_id" value="<?php echo isset($_GET['pid']) ? $_GET['pid'] : '' ?>">
		<?php if ($_SESSION['login_type'] != 3) : ?>
		<div class="form-group">
			<label for="">Công việc</label>
			<input type="text" class="form-control form-control-sm" name="task" value="<?php echo isset($task) ? $task : '' ?>" required>
		</div>
		<?php endif; ?>


		<?php
		$project_id = isset($_GET['pid']) ? $_GET['pid'] : '';
		$users_id_in_project = '';

		if (!empty($project_id)) {
			$project = $conn->query("SELECT * FROM project_list WHERE id = '$project_id'");
			if ($project->num_rows > 0) {
				$project_data = $project->fetch_assoc();
				$users_id_in_project = explode(',', $project_data['user_ids']);
			}
		}
		?>

		<div class="form-group">
		<?php if ($_SESSION['login_type'] != 3) : ?>
			<label for="" class="control-label">Nhân viên</label>
			<select class="form-control form-control-sm select2" multiple="multiple" name="users_id[]">
				<?php
				$employees = $conn->query("SELECT *, CONCAT(firstname,' ',lastname) as name FROM users WHERE type = 3 ORDER BY CONCAT(firstname,' ',lastname) ASC ");
				while ($row = $employees->fetch_assoc()) :
					if (in_array($row['id'], $users_id_in_project)) :
				?>
						<option value="<?php echo $row['id'] ?>" <?php echo isset($users_id) && in_array($row['id'], explode(',', $users_id)) ? "selected" : ''; ?>><?php echo ucwords($row['name']) ?></option>
				<?php
					endif;
				endwhile;
				?>
			</select>
			<?php endif; ?>

		</div>

		<div class="form-group">
			<label for="">Trạng thái</label>
			<select name="status" id="status" class="custom-select custom-select-sm">
				<option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : ''; ?>>Chưa thực hiện</option>
				<option value="2" <?php echo isset($status) && $status == 2 ? 'selected' : ''; ?>>Đang thực hiện</option>
				<option value="3" <?php echo isset($status) && $status == 3 ? 'selected' : ''; ?>>Đã hoàn thành</option>
				<option value="4" <?php echo isset($status) && $status == 4 ? 'selected' : ''; ?>>Hoàn thành quá hạn</option>
			</select>
		</div>
		<?php if ($_SESSION['login_type'] != 3) : ?>
		<div class="form-group">
			<label for="">Mô tả</label>
			<textarea name="description" id="" cols="30" rows="10" class="summernote form-control">
				<?php echo isset($description) ? $description : ''; ?>
			</textarea>
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
								echo $file . ' <a href="delete_file_task.php?file=' . $file . '"><i class="fa fa-trash"></i></a><br>';
							}
						}
						?>
						<div id="file-error" style="color: red;"></div>

					</div>
				</form>

			</div>
		</div>
		<?php endif; ?>

	</form>
</div>

<script src="path/to/jquery.min.js"></script>

<!-- Thư viện Select2 JS -->
<script src="path/to/select2.min.js"></script>
<script>
	// Đợi tài liệu được tải hoàn tất
	$(document).ready(function() {
		// Khởi tạo Select2 cho trường chọn có class 'select2'
		$('.select2').select2({
	    placeholder:"Vui lòng chọn ở đây",
	    width: "100%"
	  });
	});
</script>
<script>
	$(document).ready(function() {


		$('.summernote').summernote({
			height: 200,
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
				['fontname', ['fontname']],
				['fontsize', ['fontsize']],
				['color', ['color']],
				['para', ['ol', 'ul', 'paragraph', 'height']],
				['table', ['table']],
				['view', ['undo', 'redo', 'fullscreen', 'codeview', 'help']]
			]
		});
		
        $('input[type="file"]').on('change', function() {
            var fileInput = this;
            var invalidFiles = [];
            var allowedFormats = ['pptx', 'jpeg','docx', 'pdf', 'xls', 'xlsx', 'jpg', 'png'];
            var errorMessage = '';
			const maxFileSize = 30 * 1024 * 1024; // 30MB


            for (var i = 0; i < fileInput.files.length; i++) {
                var file = fileInput.files[i];
                var fileSize = file.size;

                if (fileSize > maxFileSize) {
                    invalidFiles.push(file.name);
                } else {
                    var fileExtension = file.name.split('.').pop().toLowerCase();
                    if (allowedFormats.indexOf(fileExtension) === -1) {
                        invalidFiles.push(file.name);
                    }
                }
            }

            if (invalidFiles.length > 0) {
                for (var i = 0; i < invalidFiles.length; i++) {
                    var fileName = invalidFiles[i];
                    var fileExtension = fileName.split('.').pop().toLowerCase();
					if (allowedFormats.indexOf(fileExtension) === -1 && fileSize > maxFileSize) {
                        errorMessage += 'Định dạng không hợp lệ và dung lượng vượt quá giới hạn.<br>';
						fileInput.value = null; // Xóa tệp tải lên không hợp lệ
                    }
                   else if (allowedFormats.indexOf(fileExtension) === -1) {
                        errorMessage += 'Định dạng không hợp lệ.<br>';
						fileInput.value = null; // Xóa tệp tải lên không hợp lệ
                    }else if (fileSize > maxFileSize) {
                        errorMessage += 'Dung lượng vượt quá giới hạn.<br>';
						fileInput.value = null; // Xóa tệp tải lên không hợp lệ
                    } 
                }
                $('#file-error').html(errorMessage);
            }
			 else {
                $('#file-error').empty();
            }
        });

	$('#manage-task').submit(function(e) {
		e.preventDefault()
		start_load()
		var formData = new FormData($(this)[0]);

		$.ajax({
			url: 'ajax.php?action=save_task',
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
						location.reload()
					}, 1500)
				}
			}
		});
	});
	});
	
	
</script>
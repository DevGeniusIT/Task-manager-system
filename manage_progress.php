<?php 
session_start(); 
include 'db_connect.php';
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM user_productivity where id = ".$_GET['id'])->fetch_array();
	foreach($qry as $k => $v){
		$$k = $v;
	}
}

$task_where = ''; // Điều kiện WHERE mặc định ban đầu

if (isset($_SESSION['login_type']) && $_SESSION['login_type'] == 3) {
    // Nếu là tài khoản loại 3, chỉ lấy các công việc được giao cho tài khoản đang đăng nhập
    $task_where = " AND FIND_IN_SET({$_SESSION['login_id']}, users_id)";
}

$tasks = $conn->query("SELECT * FROM task_list WHERE project_id = {$_GET['pid']} $task_where ORDER BY task ASC");
?>

<div class="container-fluid">
	<form action="" id="manage-progress">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<input type="hidden" name="project_id" value="<?php echo isset($_GET['pid']) ? $_GET['pid'] : '' ?>">
		<div class="col-lg-12">
			<div class="row">
				<div class="col-md-5">
					<?php if(!isset($_GET['tid'])): ?>
					 <div class="form-group">
		              <label for="" class="control-label">Tên công việc</label>
		              <select class="form-control form-control-sm select2" name="task_id">
		              	<option></option>
		              	<?php 
		              	while($row= $tasks->fetch_assoc()):
		              	?>
		              	<option value="<?php echo $row['id'] ?>" <?php echo isset($task_id) && $task_id == $row['id'] ? "selected" : '' ?>><?php echo ucwords($row['task']) ?></option>
		              	<?php endwhile; ?>
		              </select>
		            </div>
		            <?php else: ?>
					<input type="hidden" name="task_id" value="<?php echo isset($_GET['tid']) ? $_GET['tid'] : '' ?>">
		            <?php endif; ?>
					<div class="form-group">
						<label for="">Tiêu đề</label>
						<input type="text" class="form-control form-control-sm" name="subject" value="<?php echo isset($subject) ? $subject : '' ?>" required>
					</div>
					<div class="form-group">
						<label for="">Ngày</label>
						<input type="date" class="form-control form-control-sm" name="date" value="<?php echo isset($date) ? date("Y-m-d",strtotime($date)) : '' ?>" required>
					</div>
					<div class="form-group">
						<label for="">Thời gian bắt đầu</label>
						<input type="time" class="form-control form-control-sm" name="start_time" value="<?php echo isset($start_time) ? date("H:i",strtotime("01-01-2023 ".$start_time)) : '' ?>" required>
					</div>
					<div class="form-group">
						<label for="">Thời gian kết thúc</label>
						<input type="time" class="form-control form-control-sm" name="end_time" value="<?php echo isset($end_time) ? date("H:i",strtotime("01-01-2323 ".$end_time)) : '' ?>" required>
					</div>
				</div>
				<div class="col-md-7">
					<div class="form-group">
						<label for="">Mô tả/ Báo cáo tiến độ</label>
						<textarea name="comment" id="" cols="30" rows="10" class="summernote form-control" required="">
							<?php echo isset($comment) ? $comment : '' ?>
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
											echo $file . ' <a href="delete_file_progress.php?file=' . $file . '"><i class="fa fa-trash"></i></a><br>';
										}
									}
									?>
									<div id="file-error" style="color: red;"></div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
	$(document).ready(function(){
	$('.summernote').summernote({
        height: 200,
        toolbar: [
            [ 'style', [ 'style' ] ],
            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
            [ 'fontname', [ 'fontname' ] ],
            [ 'fontsize', [ 'fontsize' ] ],
            [ 'color', [ 'color' ] ],
            [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
            [ 'table', [ 'table' ] ],
            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
        ]
    })
     $('.select2').select2({
	    placeholder:"Vui lòng chọn ở đây",
	    width: "100%"
	  });
     });
	 $('input[type="file"]').on('change', function() {
            var fileInput = this;
            var invalidFiles = [];
            var allowedFormats = ['jpeg','docx', 'pdf', 'xls', 'xlsx', 'jpg', 'png'];
            var errorMessage = '';
			const maxFileSize = 1 * 1024 * 1024; // 30MB

            for (var i = 0; i < fileInput.files.length; i++) {
                var file = fileInput.files[i];
                var fileSize = file.size;
                var fileName = file.name;
                var fileExtension = fileName.split('.').pop().toLowerCase();
				if (fileSize > maxFileSize && allowedFormats.indexOf(fileExtension) === -1) {
                    errorMessage += 'Tập tin "' + fileName + '" không đúng định dạng và vượt quá kích thước cho phép.<br>';
                    invalidFiles.push(file.name);
					fileInput.value = null; // Xóa tệp tải lên không hợp lệ
				}else if (fileSize > maxFileSize) {
                    errorMessage += 'Tập tin "' + fileName + '" vượt quá kích thước cho phép.<br>';
                    invalidFiles.push(file.name);
					fileInput.value = null; // Xóa tệp tải lên không hợp lệ
                } else if (allowedFormats.indexOf(fileExtension) === -1) {
                    errorMessage += 'Tập tin "' + fileName + '" không đúng định dạng.<br>';
                    invalidFiles.push(file.name);
					fileInput.value = null; // Xóa tệp tải lên không hợp lệ
                }
            }

            if (invalidFiles.length > 0) {
                $('#file-error').html(errorMessage);

                
            } else {
                $('#file-error').empty();
            }
        });
    $('#manage-progress').submit(function(e){
    	e.preventDefault()
    	start_load()
    	$.ajax({
    		url:'ajax.php?action=save_progress',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp == 1){
					alert_toast('Đã lưu dữ liệu thành công.',"success");
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
    	})
    })
</script>

<?php

$this->load->view('Backend/Parts/header');
$this->load->view('Backend/Parts/navbar_main');
$this->load->view('Backend/Parts/sidebar');

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Berkas</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item"><a href="#">Master</a></li>
						<li class="breadcrumb-item active">Berkas</li>
					</ol>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
	<!-- /.content-header -->

	<!-- Main content -->
	<div class="content">
		<div class="container-fluid">
			<div class="row">

				<div class="col-lg-12">

					<div class="card card-primary card-outline">
						<div class="card-header">
							<h5 class="card-title">List Berkas</h5>
						</div>
						<div class="card-body">

							<div class="table-responsive">
								<table class="table" id="tbl_berkas" style="width: 100%;">
									<thead>
										<tr>
											<th style="text-align: center;">No.</th>
											<th style="text-align: center;">No Registrasi</th>
											<th style="text-align: center;">NISN Siswa</th>
											<th style="text-align: center;">Nama Siswa</th>
											<th style="text-align: center;">Status Verifikasi</th>
											<th style="text-align: center;">Aksi</th>
										</tr>
									</thead>
								</table>
							</div>

						</div>
					</div>

				</div>

			</div>
			<!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
	<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<div class="modal fade" id="mdl_box" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Konfirmasi</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p>Ubah Status Verifikasi</p>
				<form id="frm_status">
					<input type="hidden" name="id" id="input_id">

					<div class="form-group">
						<label for="input_noreg">No Registrasi</label>
						<input type="text" id="input_noreg" class="form-control" readonly>
					</div>

					<div class="form-group">
						<label for="input_status">Status Verifikasi</label>
						<select name="status" id="input_status" class="form-control">
							<option value="2">Approve</option>
							<option value="3">Reject</option>
						</select>
					</div>
				</form>
			</div>
			<div class="modal-footer justify-content-between">
				<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
				<button type="button" class="btn btn-primary" id="btn_save">OK</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<?php $this->load->view('Backend/Parts/footer'); ?>

<script>
	var Toast = Swal.mixin({
		toast: true,
		position: 'top-end',
		showConfirmButton: false,
		timer: 3000
	});

	var tbl_berkas = $('#tbl_berkas').DataTable({
		initComplete: function() {
			var api = this.api();
			$('#tbl_berkas input')
				.off('.DT')
				.on('input.DT', function() {
					api.search(this.value).draw();
				});
		},
		oLanguage: {
			sProcessing: "loading..."
		},
		"processing": true,
		"serverSide": true,
		"destroy": true,
		ajax: {
			url: base_url + 'backend/berkas/getList',
			type: "POST",
			data: function(data) {
				//   data.table = tableData;
			}
		},
		"columns": [{
				"data": null,
				"width": "10%",
				"sortable": false,
				render: function(data, type, row, meta) {
					return meta.row + meta.settings._iDisplayStart + 1;
				}
			},
			{
				data: "no_registrasi"
			},
			{
				data: "nisn_siswa"
			},
			{
				data: "nama_siswa"
			},
			{
				data: "status_verifikasi"
			},
			{
				data: "act"
			}
		],
		columnDefs: [{
			"className": "dt-center",
			"targets": [0, 4, 5]
		}, ]
	});

	function set_status(data) {
		if (data) {
			var link_ = base_url + 'backend/berkas/berkas_update';
			var NewData = new FormData();
			NewData.append('id', data.id);
			NewData.append('status', data.status);

			$.ajax({
				url: link_,
				type: "POST",
				data: NewData,
				dataType: "JSON",
				contentType: false,
				cache: false,
				processData: false
			}).done(function(response) {

				if (response.success) {
					Toast.fire({
						icon: 'success',
						title: response.msg
					});

					tbl_berkas.ajax.reload();

				} else {
					Toast.fire({
						icon: 'error',
						title: response.msg
					});
				}
			});
		}
		return false;
	}


	function unduh_berkas(id) {
		window.location.href = base_url + "backend/berkas/unduh/" + id;
		return false;
	}

	function popUpMdl(data) {
		var mdl = $('#mdl_box');

		var modal_title = mdl.find('.modal-title');

		var input_id = $('#input_id');
		var input_noreg = $('#input_noreg');
		var input_status = $('#input_status');

		if (data.id) {
			input_id.val(data.id);
			input_noreg.val(data.no_registrasi);
			input_status.val(data.status_verifikasi).change();

			mdl.modal('show');
		}
	}


	$('#mdl_box').on('show.bs.modal', function(e) {
		// var input_id = $('#input_id');
		// var input_noreg = $('#input_noreg');
		// var id_val = input_id.val();
	});

	$('#mdl_box').on('hide.bs.modal', function(e) {
		var input_id = $('#input_id');
		var input_noreg = $('#input_noreg');
		var id_val = input_id.val();

		input_id.val('');
		input_noreg.val('');


	});

	$('#btn_save').on('click', function(e) {

		var link_ = base_url + 'backend/berkas/berkas_update';
		var mdl = $('#mdl_box');
		var form = $('#frm_status');
		var data = form.serializeArray();
		var formHtml = form[0];
		var NewData = new FormData(formHtml);

		$.ajax({
			url: link_,
			type: "POST",
			data: NewData,
			dataType: "JSON",
			contentType: false,
			cache: false,
			processData: false
		}).done(function(response) {

			if (response.success) {
				Toast.fire({
					icon: 'success',
					title: response.msg
				});

				tbl_berkas.ajax.reload();
				mdl.modal('hide');
			} else {
				Toast.fire({
					icon: 'error',
					title: response.msg
				});
			}
		});
	});
</script>

@section('page-title', __tr("Manage Pages"))
@section('head-title', __tr("Manage Pages"))
@section('keywordName', strip_tags(__tr("Manage Pages")))
@section('keyword', strip_tags(__tr("Manage Pages")))
@section('description', strip_tags(__tr("Manage Pages")))
@section('keywordDescription', strip_tags(__tr("Manage Pages")))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-200"><?= __tr('Manage Pages') ?></h1>
	<a class="btn btn-primary btn-sm" href="<?= route('manage.page.add.view') ?>" title="Add New Page"><?= __tr('Add New Page') ?></a>
</div>
<!-- Start of Page Wrapper -->
<div class="row">
	<div class="col-xl-12 mb-4">
		<div class="card mb-4">
			<div class="card-body">
				<table class="table table-hover" id="lwManagePagesTable">
					<thead>
						<tr>
							<th><?= __tr('Title') ?></th>
							<th><?= __tr('Created') ?></th>
							<th><?= __tr('Updated') ?></th>
							<th><?= __tr('Status') ?></th>
							<th><?= __tr('Action') ?></th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<!-- End of Page Wrapper -->

<!-- User Soft delete Container -->
<div id="lwPageDeleteContainer" style="display: none;">
	<h3><?= __tr('Are You Sure!') ?></h3>
	<strong><?= __tr('You want to delete this page.') ?></strong>
</div>
<!-- User Soft delete Container -->

<!-- Pages Action Column -->
<script type="text/_template" id="pagesActionColumnTemplate">
	<div class="btn-group">
		<button type="button" class="btn btn-black dropdown-toggle lw-datatable-action-dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<i class="fas fa-ellipsis-v"></i>
		</button>
		<div class="dropdown-menu dropdown-menu-right">
		    <!-- Page Edit Button -->
		    <a class="dropdown-item" href="<%= __Utils.apiURL("<?= route('manage.page.edit.view', ['pageUId' => 'pageUId']) ?>", {'pageUId': __tData._uid}) %>"><i class="far fa-edit"></i> <?= __tr('Edit') ?></a>
		    <!-- /Page Edit Button -->

		    <!-- Preview URL -->
		    <!-- ## CUSTOM-CHANGE ## <a class="dropdown-item" target="_blank" href="<%= __tData.preview_url %>"><i class="fas fa-external-link-alt"></i> <?= __tr('Preview Page') ?></a> -->
		    <!-- /Preview URL -->

		    <!-- Page Delete Button -->
		    <a data-callback="onSuccessAction" data-method="post" class="dropdown-item lw-ajax-link-action-via-confirm" data-confirm="#lwPageDeleteContainer" href data-action="<%= __Utils.apiURL("<?= route('manage.page.write.delete', ['pageUId' => 'pageUId']) ?>", {'pageUId': __tData._uid}) %>"><i class="fas fa-trash-alt"></i> <?= __tr('Delete') ?></a>
		    <!-- /Page Delete Button -->

		</div>
	</div>
</script>
<!-- Pages Action Column -->

@push('appScripts')
<script>
	var dtColumnsData = [{
				"name": "title",
				"orderable": true,
			},
			{
				"name": "created_at",
				"orderable": true,
			},
			{
				"name": "updated_at",
				"orderable": true,
			},
			{
				"name": 'status'
			},
			{
				"name": 'action',
				"template": '#pagesActionColumnTemplate'
			}
		],
		dataTableInstance;

	dataTableInstance = dataTable('#lwManagePagesTable', {
		url: "<?= route('manage.page.list') ?>",
		dtOptions: {
			"searching": true,
			"order": [
				[0, 'asc']
			],
			"pageLength": 25
		},
		columnsData: dtColumnsData,
		scope: this
	});

	// Perform actions after delete / restore / block
	onSuccessAction = function(response) {
		reloadDT(dataTableInstance);
	}
</script>
@endpush
@section('kink-title', __tr("Manage Kinks"))
@section('head-title', __tr("Manage Kinks"))
@section('keywordName', strip_tags(__tr("Manage Kinks")))
@section('keyword', strip_tags(__tr("Manage Kinks")))
@section('description', strip_tags(__tr("Manage Kinks")))
@section('keywordDescription', strip_tags(__tr("Manage Kinks")))
@section('kink-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('kink-url', url()->current())

<!-- Kink Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-200"><?= __tr('Manage Kinks') ?></h1>
	<a class="btn btn-primary btn-sm" href="<?= route('manage.kink.add.view') ?>" title="Add New Kink"><?= __tr('Add New Kink') ?></a>
</div>
<!-- Start of Kink Wrapper -->
<div class="row">
	<div class="col-xl-12 mb-4">
		<div class="card mb-4">
			<div class="card-body">
				<table class="table table-hover" id="lwManageKinksTable">
					<thead>
						<tr>
							<th><?= __tr('Title') ?></th>
							<th><?= __tr('Created') ?></th>
							<th><?= __tr('Updated') ?></th>
							<th><?= __tr('Action') ?></th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<!-- End of Kink Wrapper -->

<!-- User Soft delete Container -->
<div id="lwKinkDeleteContainer" style="display: none;">
	<h3><?= __tr('Are You Sure!') ?></h3>
	<strong><?= __tr('You want to delete this kink.') ?></strong>
</div>
<!-- User Soft delete Container -->

<!-- Kinks Action Column -->
<script type="text/_template" id="kinksActionColumnTemplate">
	<div class="btn-group">
		<button type="button" class="btn btn-black dropdown-toggle lw-datatable-action-dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<i class="fas fa-ellipsis-v"></i>
		</button>
		<div class="dropdown-menu dropdown-menu-right">
		    <!-- Kink Edit Button -->
		    <a class="dropdown-item" href="<%= __Utils.apiURL("<?= route('manage.kink.edit.view', ['kinkUId' => 'kinkUId']) ?>", {'kinkUId': __tData._uid}) %>"><i class="far fa-edit"></i> <?= __tr('Edit') ?></a>
		    <!-- /Kink Edit Button -->

		    <!-- Kink Delete Button -->
		    <a data-callback="onSuccessAction" data-method="post" class="dropdown-item lw-ajax-link-action-via-confirm" data-confirm="#lwKinkDeleteContainer" href data-action="<%= __Utils.apiURL("<?= route('manage.kink.write.delete', ['kinkUId' => 'kinkUId']) ?>", {'kinkUId': __tData._uid}) %>"><i class="fas fa-trash-alt"></i> <?= __tr('Delete') ?></a>
		    <!-- /Kink Delete Button -->

		</div>
	</div>
</script>
<!-- Kinks Action Column -->

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
				"name": 'action',
				"template": '#kinksActionColumnTemplate'
			}
		],
		dataTableInstance;

	dataTableInstance = dataTable('#lwManageKinksTable', {
		url: "<?= route('manage.kink.list') ?>",
		dtOptions: {
			"searching": true,
			"order": [
				[0, 'asc']
			],
			"kinkLength": 25
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
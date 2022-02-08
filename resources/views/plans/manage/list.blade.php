@section('plan-title', __tr("Manage Plans"))
@section('head-title', __tr("Manage Plans"))
@section('keywordName', strip_tags(__tr("Manage Plans")))
@section('keyword', strip_tags(__tr("Manage Plans")))
@section('description', strip_tags(__tr("Manage Plans")))
@section('keywordDescription', strip_tags(__tr("Manage Plans")))
@section('plan-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('plan-url', url()->current())

<!-- Plan Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-200"><?= __tr('Manage Plans') ?></h1>
	<a class="btn btn-primary btn-sm" href="<?= route('manage.plan.add.view') ?>" title="Add New Plan"><?= __tr('Add New Plan') ?></a>
</div>
<!-- Start of Plan Wrapper -->
<div class="row">
	<div class="col-xl-12 mb-4">
		<div class="card mb-4">
			<div class="card-body">
				<table class="table table-hover" id="lwManagePlansTable">
					<thead>
						<tr>
							<th><?= __tr('Title') ?></th>
							<th><?= __tr('Price') ?></th>
							<th><?= __tr('Type') ?></th>
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
<!-- End of Plan Wrapper -->

<!-- User Soft delete Container -->
<div id="lwPlanDeleteContainer" style="display: none;">
	<h3><?= __tr('Are You Sure!') ?></h3>
	<strong><?= __tr('You want to delete this plan.') ?></strong>
</div>
<!-- User Soft delete Container -->

<!-- Plans Action Column -->
<script type="text/_template" id="plansActionColumnTemplate">
	<div class="btn-group">
		<button type="button" class="btn btn-black dropdown-toggle lw-datatable-action-dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<i class="fas fa-ellipsis-v"></i>
		</button>
		<div class="dropdown-menu dropdown-menu-right">
		    <!-- Plan Edit Button -->
		    <a class="dropdown-item" href="<%= __Utils.apiURL("<?= route('manage.plan.edit.view', ['planUId' => 'planUId']) ?>", {'planUId': __tData._uid}) %>"><i class="far fa-edit"></i> <?= __tr('Edit') ?></a>
		    <!-- /Plan Edit Button -->

		    <!-- Plan Delete Button -->
		    <a data-callback="onSuccessAction" data-method="post" class="dropdown-item lw-ajax-link-action-via-confirm" data-confirm="#lwPlanDeleteContainer" href data-action="<%= __Utils.apiURL("<?= route('manage.plan.write.delete', ['planUId' => 'planUId']) ?>", {'planUId': __tData._uid}) %>"><i class="fas fa-trash-alt"></i> <?= __tr('Delete') ?></a>
		    <!-- /Plan Delete Button -->

		</div>
	</div>
</script>
<!-- Plans Action Column -->

@push('appScripts')
<script>
	var dtColumnsData = [{
				"name": "title",
				"orderable": true,
			},
			{
				"name": "price",
				"orderable": true,
			},
			{
				"name": "formattedPlanType",
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
				"template": '#plansActionColumnTemplate'
			}
		],
		dataTableInstance;

	dataTableInstance = dataTable('#lwManagePlansTable', {
		url: "<?= route('manage.plan.list') ?>",
		dtOptions: {
			"searching": true,
			"order": [
				[0, 'asc']
			],
			"planLength": 25
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
@section('blockCountry-title', __tr("Manage Blocked Countries"))
@section('head-title', __tr("Manage Blocked Countries"))
@section('keywordName', strip_tags(__tr("Manage Blocked Countries")))
@section('keyword', strip_tags(__tr("Manage Blocked Countries")))
@section('description', strip_tags(__tr("Manage Blocked Countries")))
@section('keywordDescription', strip_tags(__tr("Manage Blocked Countries")))
@section('blockCountry-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('blockCountry-url', url()->current())

<!-- BlockCountry Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-200"><?= __tr('Manage Blocked Countries') ?></h1>
	<a class="btn btn-primary btn-sm" href="<?= route('manage.blockCountry.add.view') ?>" title="Add New Block Country"><?= __tr('Add New Block Country') ?></a>
</div>
<!-- Start of Block Country Wrapper -->
<div class="row">
	<div class="col-xl-12 mb-4">
		<div class="card mb-4">
			<div class="card-body">
				<table class="table table-hover" id="lwManageBlockCountriesTable">
					<thead>
						<tr>
							<th><?= __tr('Name') ?></th>
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
<!-- End of Block Country Wrapper -->

<!-- User Soft delete Container -->
<div id="lwBlockCountryDeleteContainer" style="display: none;">
	<h3><?= __tr('Are You Sure!') ?></h3>
	<strong><?= __tr('You want to delete this Blocked Country.') ?></strong>
</div>
<!-- User Soft delete Container -->

<!-- Block Countries Action Column -->
<script type="text/_template" id="blockCountriesActionColumnTemplate">
	<div class="btn-group">
		<button type="button" class="btn btn-black dropdown-toggle lw-datatable-action-dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<i class="fas fa-ellipsis-v"></i>
		</button>
		<div class="dropdown-menu dropdown-menu-right">
		    <!-- Block Country Edit Button -->
		    <a class="dropdown-item" href="<%= __Utils.apiURL("<?= route('manage.blockCountry.edit.view', ['blockCountryUId' => 'blockCountryUId']) ?>", {'blockCountryUId': __tData._uid}) %>"><i class="far fa-edit"></i> <?= __tr('Edit') ?></a>
		    <!-- /Block Country Edit Button -->

		    <!-- Block Country Delete Button -->
		    <a data-callback="onSuccessAction" data-method="post" class="dropdown-item lw-ajax-link-action-via-confirm" data-confirm="#lwBlockCountryDeleteContainer" href data-action="<%= __Utils.apiURL("<?= route('manage.blockCountry.write.delete', ['blockCountryUId' => 'blockCountryUId']) ?>", {'blockCountryUId': __tData._uid}) %>"><i class="fas fa-trash-alt"></i> <?= __tr('Delete') ?></a>
		    <!-- /Block Country Delete Button -->

		</div>
	</div>
</script>
<!-- Block Countries Action Column -->

@push('appScripts')
<script>
	var dtColumnsData = [{
				"name": "name",
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
				"template": '#blockCountriesActionColumnTemplate'
			}
		],
		dataTableInstance;

	dataTableInstance = dataTable('#lwManageBlockCountriesTable', {
		url: "<?= route('manage.blockCountry.list') ?>",
		dtOptions: {
			"searching": true,
			"order": [
				[0, 'asc']
			],
			"blockCountryLength": 25
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
@section('forum-category-title', __tr("Manage Forum Categories"))
@section('head-title', __tr("Manage Forum Categories"))
@section('keywordName', strip_tags(__tr("Manage Forum Categories")))
@section('keyword', strip_tags(__tr("Manage Forum Categories")))
@section('description', strip_tags(__tr("Manage Forum Categories")))
@section('keywordDescription', strip_tags(__tr("Manage Forum Categories")))
@section('forum-category-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('forum-category-url', url()->current())

<!-- Forum Category Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-200"><?= __tr('Manage Forum Categories') ?></h1>
	<a class="btn btn-primary btn-sm" href="<?= route('manage.forum-category.add.view') ?>" title="Add New Forum Category"><?= __tr('Add New Forum Category') ?></a>
</div>
<!-- Start of Forum Category Wrapper -->
<div class="row">
	<div class="col-xl-12 mb-4">
		<div class="card mb-4">
			<div class="card-body">
				<table class="table table-hover" id="lwManageForumCategoriesTable">
					<thead>
						<tr>
							<th><?= __tr('Title') ?></th>
							<th><?= __tr('Parent Category') ?></th>
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
<!-- End of Forum Category Wrapper -->

<!-- User Soft delete Container -->
<div id="lwForumCategoryDeleteContainer" style="display: none;">
	<h3><?= __tr('Are You Sure!') ?></h3>
	<strong><?= __tr('You want to delete this forum-category.') ?></strong>
</div>
<!-- User Soft delete Container -->

<!-- Forum Categories Action Column -->
<script type="text/_template" id="forumCategoriesActionColumnTemplate">
	<div class="btn-group">
		<button type="button" class="btn btn-black dropdown-toggle lw-datatable-action-dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<i class="fas fa-ellipsis-v"></i>
		</button>
		<div class="dropdown-menu dropdown-menu-right">
		    <!-- Forum Category Edit Button -->
		    <a class="dropdown-item" href="<%= __Utils.apiURL("<?= route('manage.forum-category.edit.view', ['forumCategoryUId' => 'forumCategoryUId']) ?>", {'forumCategoryUId': __tData._uid}) %>"><i class="far fa-edit"></i> <?= __tr('Edit') ?></a>
		    <!-- /Forum Category Edit Button -->

		    <!-- Forum Category Delete Button -->
		    <a data-callback="onSuccessAction" data-method="post" class="dropdown-item lw-ajax-link-action-via-confirm" data-confirm="#lwForumCategoryDeleteContainer" href data-action="<%= __Utils.apiURL("<?= route('manage.forum-category.write.delete', ['forumCategoryUId' => 'forumCategoryUId']) ?>", {'forumCategoryUId': __tData._uid}) %>"><i class="fas fa-trash-alt"></i> <?= __tr('Delete') ?></a>
		    <!-- /Forum Category Delete Button -->

		</div>
	</div>
</script>
<!-- Forum Categories Action Column -->

@push('appScripts')
<script>
	var dtColumnsData = [{
				"name": "title",
				"orderable": true,
			},
			{
				"name": "parent",
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
				"template": '#forumCategoriesActionColumnTemplate'
			}
		],
		dataTableInstance;

	dataTableInstance = dataTable('#lwManageForumCategoriesTable', {
		url: "<?= route('manage.forum-category.list') ?>",
		dtOptions: {
			"searching": true,
			"order": [
				[0, 'asc']
			],
			"forumCategoryLength": 25
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
@section('forum-topic-title', __tr("Manage Forum Topics"))
@section('head-title', __tr("Manage Forum Topics"))
@section('keywordName', strip_tags(__tr("Manage Forum Topics")))
@section('keyword', strip_tags(__tr("Manage Forum Topics")))
@section('description', strip_tags(__tr("Manage Forum Topics")))
@section('keywordDescription', strip_tags(__tr("Manage Forum Topics")))
@section('forum-topic-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('forum-topic-url', url()->current())



<!-- Forum Topic Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-200"><?= __tr('Manage Forum Topics') ?></h1>
	<a class="btn btn-primary btn-sm" href="<?= route('manage.forum-topic.add.view') ?>" title="Add New Forum Topic"><?= __tr('Add New Forum Topic') ?></a>
</div>
<!-- Start of Forum Topic Wrapper -->
<div class="row">
	<div class="col-xl-12 mb-4">
		<div class="card mb-4">
			<div class="card-body">
				<table class="table table-hover" id="lwManageForumTopicsTable">
					<thead>
						<tr>
							<th><?= __tr('Title') ?></th>
							<th><?= __tr('Forum Category') ?></th>
							<th><?= __tr('Views') ?></th>
							<th><?= __tr('Replies') ?></th>
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
<!-- End of Forum Topic Wrapper -->
<?php $userId = Auth::user()->_id; 
?>
<!-- User Soft delete Container -->
<div id="lwForumTopicDeleteContainer" style="display: none;">
	<h3><?= __tr('Are You Sure!') ?></h3>
	<strong><?= __tr('You want to delete this forum topic.') ?></strong>
</div>
<!-- User Soft delete Container -->

<!-- Forum Topics Action Column -->
<script type="text/_template" id="forumTopicsActionColumnTemplate">
	<div class="btn-group">
		<button type="button" class="btn btn-black dropdown-toggle lw-datatable-action-dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<i class="fas fa-ellipsis-v"></i>
		</button>
		<div class="dropdown-menu dropdown-menu-right">
			


		    <!-- Forum Topic Edit Button -->
		    <% if(__tData.users__id == 1) { %>
		    <a class="dropdown-item" href="<%= __Utils.apiURL("<?= route('manage.forum-topic.edit.view', ['forumTopicUId' => 'forumTopicUId']) ?>", {'forumTopicUId': __tData._uid}) %>"><i class="far fa-edit"></i> <?= __tr('Edit') ?></a>
		    <% } %>
		    <!-- /Forum Topic Edit Button -->

		    <!-- Forum Topic Delete Button -->
		    <a data-callback="onSuccessAction" data-method="post" class="dropdown-item lw-ajax-link-action-via-confirm" data-confirm="#lwForumTopicDeleteContainer" href data-action="<%= __Utils.apiURL("<?= route('manage.forum-topic.write.delete', ['forumTopicUId' => 'forumTopicUId']) ?>", {'forumTopicUId': __tData._uid}) %>"><i class="fas fa-trash-alt"></i> <?= __tr('Delete') ?></a>
		    <!-- /Forum Topic Delete Button -->

		    <!-- Forum Topic Reply Button -->
		    <% if(__tData.reply_count != 0) { %>
		    <a class="dropdown-item" href="<%= __Utils.apiURL("<?= route('manage.forum-topic.reply.view', ['forumTopicUId' => 'forumTopicUId']) ?>", {'forumTopicUId': __tData._uid}) %>"><i class="fa fa-reply"></i> <?= __tr('Reply') ?></a>
		    <% } %>
		    <!-- /Forum Topic Reply Button -->


		</div>
	</div>
</script>
<!-- Forum Topics Action Column -->

@push('appScripts')
<script>
	var dtColumnsData = [{
				"name": "title",
				"orderable": true,
			},
			{
				"name": "forum_category",
				"orderable": true,
			},
			{
				"name": "view_count",
				"orderable": true,
			},
			{
				"name": "reply_count",
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
				"template": '#forumTopicsActionColumnTemplate'
			}
		],
		dataTableInstance;
	dataTableInstance = dataTable('#lwManageForumTopicsTable', {
		url: "<?= route('manage.forum-topic.list') ?>",
		dtOptions: {
			"searching": true,
			"order": [
				[0, 'asc']
			],
			"forumTopicLength": 25
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
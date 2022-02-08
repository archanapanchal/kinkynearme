@section('forum-topic-title', __tr("Manage Forum Topic Replies"))
@section('head-title', __tr("Manage Forum Topic Replies"))
@section('keywordName', strip_tags(__tr("Manage Forum Topic Replies")))
@section('keyword', strip_tags(__tr("Manage Forum Topic Replies")))
@section('description', strip_tags(__tr("Manage Forum Topic Replies")))
@section('keywordDescription', strip_tags(__tr("Manage Forum Topic Replies")))
@section('forum-topic-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('forum-topic-url', url()->current())


<!-- Forum Topic Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-200"><?= __tr('Manage Forum Topic Replies - '.$title) ?></h1>
	<a class="btn btn-primary btn-sm" href="{{url('/admin/forum-topics')}}" title="Back to forum topic list"><?= __tr('Back to forum topic list') ?></a>
</div>
<!-- Start of Forum Topic Wrapper -->
<div class="row">
	<div class="col-xl-12 mb-4">
		<div class="card mb-4">
			<div class="card-body">
				<table class="table table-hover" id="lwManageForumTopicRepliesTable">
					<thead>
						<tr>
							<th><?= __tr('User') ?></th>
							<th><?= __tr('Reply') ?></th>
							<th><?= __tr('Status') ?></th>
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

<!-- User Soft delete Container -->
<div id="lwForumReplyDeleteContainer" style="display: none;">
	<h3><?= __tr('Are You Sure!') ?></h3>
	<strong><?= __tr('You want to delete this forum topic reply.') ?></strong>

<!-- Forum Topics Action Column -->
<script type="text/_template" id="forumTopicRepliesActionColumnTemplate">
	<div class="btn-group">
		<button type="button" class="btn btn-black dropdown-toggle lw-datatable-action-dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<i class="fas fa-ellipsis-v"></i>
		</button>
		<div class="dropdown-menu dropdown-menu-right">

			<a data-callback="onSuccessAction" data-method="post" class="dropdown-item lw-ajax-link-action-via-confirm" data-confirm="#lwForumReplyDeleteContainer" href data-action="<%= __Utils.apiURL("<?= route('manage.forum-topic.reply.write.delete', ['forumTopicReplyUId' => 'forumTopicReplyUId']) ?>", {'forumTopicReplyUId': __tData._uid}) %>"><i class="fas fa-trash-alt"></i> <?= __tr('Delete') ?></a>

			<% if(__tData.status != 1) { %>
				<!-- Verify Reply -->
				<a class="dropdown-item lw-ajax-link-action" data-callback="onSuccessAction" href="<%= __Utils.apiURL("<?= route('manage.forum-topic.reply.write.change-status', ['forumTopicReplyUId' => 'forumTopicReplyUId']) ?>", {'forumTopicReplyUId': __tData._uid}) %>" data-method="post"><i class="fa fa-unlock"></i> <?= __tr('Active') ?></a>
				<!-- /Verify Reply -->
			<% } else { %>
				<!-- Unverify Reply -->
				<a class="dropdown-item lw-ajax-link-action" data-callback="onSuccessAction" href="<%= __Utils.apiURL("<?= route('manage.forum-topic.reply.write.change-status', ['forumTopicReplyUId' => 'forumTopicReplyUId']) ?>", {'forumTopicReplyUId': __tData._uid}) %>" data-method="post"><i class="fa fa-lock"></i> <?= __tr('In-Active') ?></a>
				<!-- /Verify Reply -->
			<% } %>
		</div>
	</div>
</script>
<!-- Forum Topics Action Column -->

@push('appScripts')
<script>
	var dtColumnsData = [
			{
				"name": "username",
				"orderable": true,
			},
			{
				"name": "reply",
				"orderable": true,
			},
			{
				"name": "status",
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
				"template": '#forumTopicRepliesActionColumnTemplate'
			}
		],
		dataTableInstance;

		// console.log(dataTableInstance);

	dataTableInstance = dataTable('#lwManageForumTopicRepliesTable', {
		url: "{{url('/admin/forum-topics/').'/'.$forumTopicUId.'/reply-list'}}",
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
		console.log(response);
		reloadDT(dataTableInstance);
	}
</script>
@endpush
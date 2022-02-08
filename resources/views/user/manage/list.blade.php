@section('page-title', __tr("Manage Users"))
@section('head-title', __tr("Manage Users"))
@section('keywordName', strip_tags(__tr("Manage Users")))
@section('keyword', strip_tags(__tr("Manage Users")))
@section('description', strip_tags(__tr("Manage Users")))
@section('keywordDescription', strip_tags(__tr("Manage Users")))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())

<?php $userStatus = request()->status; ?>
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-200"><?= __tr('Manage Users') ?></h1>
	<a class="<?= $userStatus == 2 ? 'active' : '' ?>" href="<?= route('manage.user.export', ['status' => $userStatus]) ?>">
		<i class="fas fa-file-export"></i> <?= __tr('Export Users') ?>
	</a>
</div>
<div class="row">
	<div class="col-xl-12">
		<!-- card -->
		<div class="card mb-4">
			<!-- card body -->
			<div class="card-body">
				<ul class="nav nav-tabs">
					<!-- Active Tab -->
					<li class="nav-item">
						<a class="nav-link <?= $userStatus == 1 ? 'active' : '' ?>" href="<?= route('manage.user.view_list', ['status' => 1]) ?>">
							<?= __tr('Active') ?>
						</a>
					</li>
					<!-- /Active Tab -->

					<!-- Inactive Tab -->
					<li class="nav-item">
						<a class="nav-link <?= $userStatus == 2 ? 'active' : '' ?>" href="<?= route('manage.user.view_list', ['status' => 2]) ?>">
							<?= __tr('Inactive') ?>
						</a>
					</li>
					<!-- /Inactive Tab -->

					<!-- Deleted Tab -->
					<li class="nav-item">
						<a class="nav-link <?= $userStatus == 5 ? 'active' : '' ?>" href="<?= route('manage.user.view_list', ['status' => 5]) ?>">
							<?= __tr('Deleted') ?>
						</a>
					</li>
					<!-- /Deleted Tab -->

					<!-- Never Activated Tab -->
					<li class="nav-item">
						<a class="nav-link <?= $userStatus == 4 ? 'active' : '' ?>" href="<?= route('manage.user.view_list', ['status' => 4]) ?>">
							<?= __tr('Never Activated') ?>
						</a>
					</li>
					<!-- /Never Activated Tab -->

					<!-- Blocked Tab -->
					<li class="nav-item">
						<a class="nav-link <?= $userStatus == 3 ? 'active' : '' ?>" href="<?= route('manage.user.view_list', ['status' => 3]) ?>">
							<?= __tr('Blocked') ?>
						</a>
					</li>
				</ul>
				<!-- table start -->
				<div class="lw-nav-content">
					<table class="table table-hover" id="lwManageUsersTable">
						<!-- table headings -->
						<thead>
							<tr>
								<th class="lw-dt-nosort"><?= __tr('Profile Picture') ?></th>
								<th><?= __tr('Full Name') ?></th>
								<th><?= __tr('Username') ?></th>
								<th><?= __tr('Email') ?></th>
								<th><?= __tr('DOB') ?></th>
								<th><?= __tr('Gender') ?></th>
								<th><?= __tr('Location') ?></th>
								<th><?= __tr('Created On') ?></th>
								<th><?= __tr('Action') ?></th>
							</tr>
						</thead>
						<!-- /table headings -->
						<tbody class="lw-datatable-photoswipe-gallery"></tbody>
					</table>
					<div>
						<!-- table end -->
					</div>
					<!-- /card body -->
				</div>
				<!-- /card -->
			</div>
		</div>
		<!-- User Soft delete Container -->
		<div id="lwUserSoftDeleteContainer" style="display: none;">
			<h3><?= __tr('Are You Sure!') ?></h3>
			<strong><?= __tr('You want to delete this user, it will be placed in deleted tab.') ?></strong>
		</div>
		<!-- User Soft delete Container -->

		<!-- User Permanent delete Container -->
		<div id="lwUserPermanentDeleteContainer" style="display: none;">
			<h3><?= __tr('Are You Sure!') ?></h3>
			<strong><?= __tr('You want to permanent delete this user.') ?></strong>
		</div>
		<!-- User Permanent delete Container -->

		<script type="text/_template" id="usersProfilePictureTemplate">
			<img class="lw-datatable-profile-picture lw-dt-thumbnail lw-photoswipe-gallery-img lw-lazy-img" data-src="<%= __tData.profile_picture %>">
</script>

		<script type="text/_template" id="verifiedUserTemplate">
			<div>
		<%= __tData.full_name %>
		<% if(__tData.is_verified == 1) { %> &nbsp;&nbsp;&nbsp;<i class="fas fa-user-check text-info" title="Verified User"></i> <% } %> <% if(__tData.is_fake == 1) { %><i class="fas fa-user-secret" title="Fake User"></i> <% } %>
		<!-- show premium badge if user is premium -->
		<% if(__tData.is_premium_user) { %>
			<span class="lw-small-premium-badge" title="<?= __tr('Premium User') ?>"></span> 
		<% } %>
		<!-- /show premium badge if user is premium -->
	</div>
</script>

		<!-- User Action gender column template -->
		<script type="text/_template" id="usersGenderActionColumnTemplate">
			<span><%= __tData.formattedGender %></span>
</script>
		<!-- User Action gender column template -->

		<!-- Pages Action Column -->
		<script type="text/_template" id="usersActionColumnTemplate">
			<% if(__tData.user_roles__id != 1) { %>
		<!-- dropdown -->
		<div class="btn-group">
			<button type="button" class="btn btn-black dropdown-toggle lw-datatable-action-dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="fas fa-ellipsis-v"></i>
			</button>
			<div class="dropdown-menu dropdown-menu-right">
				
				<% if(__tData.status != 3) { %>
					<!-- Edit Button -->
					<a class="dropdown-item" href="<%= __Utils.apiURL("<?= route('manage.user.edit', ['userUid' => 'userUid']) ?>", {'userUid': __tData._uid}) %>"><i class="far fa-edit"></i> <?= __tr('Edit') ?></a>
					<!-- /Edit Button -->

					<!-- Transaction Detail Button -->
					<a href class="dropdown-item" data-toggle="modal" data-user-uid='<%= __tData._uid %>' data-target="#userTransactionDialog" data-user-name="<%= __tData.full_name %>"><i class="far fa-edit"  id="lwTransactionDetailBtn"></i> <?= __tr('Transactions') ?></a>

					<!--10-01-2022-->
					<a class="dropdown-item" href="<%= __Utils.apiURL("<?= route('user.photos', ['username' => 'userUid']) ?>", {'userUid': __tData._uid}) %>"><i class="far fa-edit"></i> <?= __tr('Upload Portfolio') ?></a>


					<!-- /Transaction Detail Button -->

					<% if(__tData.is_verified != 1) { %>
						<!-- Verify User -->
						<a class="dropdown-item lw-ajax-link-action" data-callback="onSuccessAction" href="<%= __Utils.apiURL("<?= route('manage.user.write.verify', ['userUid' => 'userUid']) ?>", {'userUid': __tData._uid}) %>" data-method="post"><i class="fas fa-user-check"></i> <?= __tr('Verify') ?></a>
						<!-- /Verify User -->
					<% } else { %>
						<!-- Unverify User -->
						<a class="dropdown-item lw-ajax-link-action" data-callback="onSuccessAction" href="<%= __Utils.apiURL("<?= route('manage.user.write.unverify', ['userUid' => 'userUid']) ?>", {'userUid': __tData._uid}) %>" data-method="post"><i class="fas fa-user"></i> <?= __tr('Unverify') ?></a>
						<!-- /Verify User -->
					<% } %>
				<% } %>
				<% if(__tData.status == 5) { %>
					<!-- Permanent delete button -->
					<a class="dropdown-item lw-ajax-link-action-via-confirm" data-confirm="#lwUserPermanentDeleteContainer"  data-method="post" data-action="<%= __Utils.apiURL("<?= route('manage.user.write.permanent_delete', ['userUid' => 'userUid']) ?>", {'userUid': __tData._uid}) %>" data-callback="onSuccessAction" href data-method="post"><i class="fas fa-trash-alt"></i> <?= __tr('Delete') ?></a>
					<!-- /Permanent delete button -->

					<!-- Restore button -->
					<a class="dropdown-item lw-ajax-link-action" data-callback="onSuccessAction" href="<%= __Utils.apiURL("<?= route('manage.user.write.restore_user', ['userUid' => 'userUid']) ?>", {'userUid': __tData._uid}) %>" data-method="post"><i class="fas fa-trash-restore-alt"></i> <?= __tr('Restore') ?></a>
					<!-- /Restore button -->

					<!-- Block button -->
					<a class="dropdown-item lw-ajax-link-action" data-callback="onSuccessAction" href="<%= __Utils.apiURL("<?= route('manage.user.write.block_user', ['userUid' => 'userUid']) ?>", {'userUid': __tData._uid}) %>" data-method="post"><i class="fas fa-ban"></i> <?= __tr('Block') ?></a>
					<!-- /Block button -->
				<% } %>

				<!-- If status is other than delete -->
				<% if(__tData.status != 5 && __tData.status != 3) { %>

					<!-- Soft delete button -->
					<a class="dropdown-item lw-ajax-link-action-via-confirm" data-callback="onSuccessAction" data-confirm="#lwUserSoftDeleteContainer"  data-method="post" data-action="<%= __Utils.apiURL("<?= route('manage.user.write.soft_delete', ['userUid' => 'userUid']) ?>", {'userUid': __tData._uid}) %>" href data-method="post"><i class="fas fa-trash-alt"></i> <?= __tr('Soft Delete') ?></a>
					<!-- /Soft delete button -->

					<!-- Block button -->
					<a class="dropdown-item lw-ajax-link-action" data-callback="onSuccessAction" href="<%= __Utils.apiURL("<?= route('manage.user.write.block_user', ['userUid' => 'userUid']) ?>", {'userUid': __tData._uid}) %>" data-method="post"><i class="fas fa-ban"></i> <?= __tr('Block') ?></a>
					<!-- /Block button -->

				<% } %>

				<!-- If status is blocked -->
				<% if(__tData.status == 3) { %>
					<!-- Unblock button -->
					<a class="dropdown-item lw-ajax-link-action" data-callback="onSuccessAction" href="<%= __Utils.apiURL("<?= route('manage.user.write.unblock_user', ['userUid' => 'userUid']) ?>", {'userUid': __tData._uid}) %>" data-method="post"><i class="fas fa-ban"></i> <?= __tr('Unblock') ?></a>
					<!-- /Unblock button -->
				<% } %>
			</div>
		</div>
	<% } %>
</script>
		<!-- Pages Action Column -->

		<!-- user transaction Modal-->
		<div class="modal fade" id="userTransactionDialog" tabindex="-1" role="dialog" aria-labelledby="userTransactionModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div id="lwUserTransactionContent"></div>
					<script type="text/_template" id="lwUserTransactionTemplate" data-replace-target="#lwUserTransactionContent" data-modal-id="#userTransactionDialog">
						<div class="modal-header">
						<h5 class="modal-title" id="userTransactionModalLabel"><?= __tr('User Transactions') ?> (<%= __tData.userName %>)</h5>
						<button class="close" type="button" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<!-- user financial transaction table -->
						<table class="table table-hover" id="lwUserTransactionTable">
							<thead>
								<tr>
									<th><?= __tr('Plan Name') ?></th>
									<th><?= __tr('Transaction_id') ?></th>
									<th><?= __tr('Amount') ?></th>
									<th><?= __tr('Currency') ?></th>
									<th><?= __tr('Payment status') ?></th>
									<th><?= __tr('Active Plan status') ?></th>
									<th><?= __tr('Plan Type') ?></th>
									<th><?= __tr('Created') ?></th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
						<!-- user financial transaction table -->
					</div>
			</script>
				</div>
			</div>
		</div>
		<!-- / user transaction Modal-->
	</div>
</div>
@push('appScripts')
<script>
	//user transaction dialog details
	__Utils.modalTemplatize('#lwUserTransactionTemplate', function(e, data) {
		return {
			'transactionData': fetchUserTransactions(data), //fetch user transaction list data
			'userName': data['userName']
		};
	}, function(e, myData) {});

	//fetch user transaction list data
	function fetchUserTransactions(data) {

		//transaction list data table columns data
		/*var userTransactionDtColumnsData = [{
				"name": "created_at",
				"orderable": true
			},
			{
				"name": 'formattedIsTestMode',
				"orderable": false
			},
			{
				"name": "formattedStatus",
				"orderable": false
			},
			{
				"name": "formattedAmount",
				"orderable": false
			},
			{
				"name": 'formattedCreditType',
				"orderable": false
			},
			{
				"name": 'method',
				"orderable": false
			},
			{
				"name": 'packageName',
				"orderable": false
			}
		];*/
		var userTransactionDtColumnsData = [
			{
				"name": 'paln_title_detail',
				"orderable": false
			},
			{
				"name": 'transaction_id',
				"orderable": false
			},
			{
				"name": "amount",
				"orderable": false
			},
			{
				"name": "currency",
				"orderable": false
			},
			{
				"name": 'payment_status',
				"orderable": false
			},			
			{
				"name": 'plan_active_status',
				"orderable": false
			},
			{
				"name": 'formattedPlanType',
				
			},
			{
				"name": "created_at",
				"orderable": true
			}
			
		];

		_.defer(function() {
			dataTable('#lwUserTransactionTable', {
				url: __Utils.apiURL("<?= route('manage.user.read.transaction_list', ['userUid' => 'userUid']) ?>", {
					'userUid': data['userUid']
				}),
				dtOptions: {
					"searching": false,
					"order": [
						[0, 'desc']
					],
					"pageLength": 10
				},
				columnsData: userTransactionDtColumnsData,
				scope: this
			});
		})
	}

	var dtColumnsData = [{
				"name": "_id",
				"template": '#usersProfilePictureTemplate'
			},
			{
				"name": "full_name",
				"orderable": true,
				"template": '#verifiedUserTemplate'
			},
			{
				"name": "username",
				"orderable": true
			},
			{
				"name": "email",
				"orderable": true
			},
			{
				"name": 'dob',
				"orderable": true
			},
			{
				"name": 'gender',
				"orderable": true,
				"template": '#usersGenderActionColumnTemplate'
			},
			{
				"name": 'location',
				"orderable": true,
			},
			{
				"name": "created_at",
				"orderable": true
			},
			{
				"name": 'action',
				"template": '#usersActionColumnTemplate'
			}
		],
		dataTableInstance;

	// Perform actions after delete / restore / block
	onSuccessAction = function(response) {
		reloadDT(dataTableInstance);
	};

	//for users list
	fetchUsers = function() {
		dataTableInstance = dataTable('#lwManageUsersTable', {
			url: "<?= route('manage.user.read.list', ['status' => $userStatus]) ?>",
			dtOptions: {
				"searching": true,
				"order": [
					[0, 'desc']
				],
				"pageLength": 10,
				"drawCallback": function() {
					applyLazyImages();
				}
			},
			columnsData: dtColumnsData,
			scope: this
		});
	};

	fetchUsers();
</script>
@endpush
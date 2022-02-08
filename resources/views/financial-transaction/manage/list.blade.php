@section('page-title', __tr("Financial Transactions"))
@section('head-title', __tr("Financial Transactions"))
@section('keywordName', strip_tags(__tr("Financial Transactions")))
@section('keyword', strip_tags(__tr("Financial Transactions")))
@section('description', strip_tags(__tr("Financial Transactions")))
@section('keywordDescription', strip_tags(__tr("Financial Transactions")))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-200"><?= __tr('Financial Transactions') ?></h1>
</div>
<!-- Start of Page Wrapper -->
<?php $transactionType = request()->transactionType; ?>
<div class="row">
	<div class="col-xl-12 mb-4">
		<div class="card mb-4">
			<div class="card-body">
				<ul class="nav nav-tabs mb-3">
					<!-- Live Transaction -->
					<li class="nav-item">
						<a class="nav-link <?= $transactionType == 'live' ? 'active' : '' ?>" href="<?= route('manage.financial_transaction.read.view_list', ['transactionType' => 'live']) ?>">
							<?= __tr('Live Transaction') ?>
						</a>
					</li>
					<!-- /Live Transaction -->

					<!-- Test Transaction -->
					<li class="nav-item">
						<a class="nav-link <?= $transactionType == 'test' ? 'active' : '' ?>" href="<?= route('manage.financial_transaction.read.view_list', ['transactionType' => 'test']) ?>">
							<?= __tr('Test Transaction') ?>
						</a>
					</li>
					<!-- /Test Transaction -->
				</ul>
				<!-- delete all transaction button -->
				@if($transactionType == 'test')
				<a class="btn btn-danger float-right btn-sm lw-ajax-link-action-via-confirm" data-confirm="#lwDeleteAllTestTransactions" data-method="post" data-action="<?= route('manage.financial_transaction.write.delete.all_transaction') ?>" data-callback="deleteAllTransactionCallback"><?= __tr('Delete All') ?></a>
				<br><br>
				@endif
				<!-- delete all transaction button -->

				<!-- transaction table -->
				<table class="table table-hover" id="lwTransactionTable">
					<thead>
						<tr>
							<th><?= __tr('User') ?></th>
							<th><?= __tr('Created On') ?></th>
							<th><?= __tr('Amount') ?></th>
							<th><?= __tr('Payment Method') ?></th>
							<th><?= __tr('Package') ?></th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
				<!-- /transaction table -->
			</div>
		</div>
	</div>
</div>
<!-- End of Page Wrapper -->

<!-- User Permanent delete Container -->
<div id="lwDeleteAllTestTransactions" style="display: none;">
	<h3><?= __tr('Are You Sure!') ?></h3>
	<strong><?= __tr('You want to delete all test transactions.') ?></strong>
</div>
<!-- User Permanent delete Container -->

@push('appScripts')
<script>
	//transaction list data table columns data
	var dtColumnsData = [{
				"name": "userFullName",
				"orderable": true
			},
			{
				"name": "created_at",
				"orderable": true
			},
			{
				"name": "formatAmount",
				"orderable": false
			},
			{
				"name": "method",
				"orderable": true
			},
			{
				"name": "packageName",
				"orderable": false
			}
		],
		transactionListDataTable;

	//for transactions list
	fetchTransactions = function() {
		transactionListDataTable = dataTable('#lwTransactionTable', {
			url: "<?= route('manage.financial_transaction.read.list', ['transactionType' => $transactionType]) ?>",
			dtOptions: {
				"searching": true,
				"order": [
					[0, 'desc']
				],
				"pageLength": 10
			},
			columnsData: dtColumnsData,
			scope: this
		});
	};
	//fetch transaction data
	fetchTransactions();

	//delete all test transaction callback
	function deleteAllTransactionCallback(responseData) {
		if (responseData.reaction == 1) {
			reloadDT(transactionListDataTable);
		}
	};
</script>
@endpush
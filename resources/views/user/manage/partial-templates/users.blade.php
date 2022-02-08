<div class="modal fade" id="<?= $modalId ?>" tabindex="-1" role="dialog" aria-labelledby="<?= $modalId ?>Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="boosterModalLabel"><?= $modalHeading ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button> 
            </div>
            <div class="modal-body">
            	@if($totalUsers == 0)
            		<div class="container-fluid">
						<!-- info message -->
						<div class="alert alert-info"> There are no users. </div>
						<!-- / info message -->
					</div>
            	@else
                	<div class="row">
		                @foreach($usersList as $user)
							<div class="col-4 col-lg-3">
								<img data-src="<?= imageOrNoImageAvailable($user['userImageUrl']) ?>" class="lw-user-thumbnail lw-lazy-img w-100" />
							</div>
							<div class="col-8 col-lg-9 d-flex align-items-center">
								<div class="col-12">
									<h5>
										<?= $user['userFullName'] ?>
									</h5>
									<?= $user['detailString'] ?> <br>
									@if(isset($user['looking_for']) && $user['looking_for'])
										<label><strong>Looking for</strong>: </label> <?= $user['looking_for'] ?> <br>
									@endif
									@if(isset($user['kinks']) && $user['kinks'])
										<label><strong>Interests/Kinks</strong>: </label> <?= $user['kinks'] ?> <br>
									@endif
								</div>
							</div>
						@endforeach
					</div>
				@endif
            </div>
            <div class="modal-footer"> <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Close</button> </div>
        </div>
    </div>
</div>
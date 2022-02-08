<table cellspacing="0" cellpadding="0" width="100%" class="w320" style="border-collapse: collapse !important; font-family: Helvetica, Arial, sans-serif;">
	<tbody>
		<tr style="font-family: Helvetica, Arial, sans-serif;">
			<td class="header-lg" style="border-collapse: collapse; color: #4d4d4d; font-family: Helvetica, Arial, sans-serif; font-size: 24px; font-weight: 700; line-height: normal; padding: 10px 60px 0px; text-align: left;">
				Hello <?= e($ToUserName) ?>,
			</td>
		</tr>
		<tr style="font-family: Helvetica, Arial, sans-serif;">
			<td class="free-text" style="border-collapse: collapse; color: #777777; font-family: Helvetica, Arial, sans-serif; font-size: 14px; line-height: 21px; padding: 10px 60px 0px; text-align: left; width: 100% !important;">
					<?= e($FromUserName) ?> has marked your profile as favorite.
			</td>
		</tr>
		<tr style="font-family: Helvetica, Arial, sans-serif;">
			<td class="free-text" style="border-collapse: collapse; color: #777777; font-family: Helvetica, Arial, sans-serif; font-size: 14px; line-height: 21px; padding: 10px 60px 0px; text-align: left; width: 100% !important;">
					Please visit <a href="<?= e($PageURL) ?>" target="_blank"> Favorite Page</a> to check the profile.
			</td>
		</tr>
	</tbody>
</table>
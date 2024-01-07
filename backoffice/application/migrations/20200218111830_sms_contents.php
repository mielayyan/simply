<?php

class Migration_sms_contents extends CI_Migration
{

	public function up()
	{
		$dbPrefix = $this->db->dbprefix;
		if ((($dbPrefix == 'inf_') || $this->db->where("sms_status", "yes")->count_all_results("module_status") > 0)) {
			$query = [];

			$query[] = "DROP TABLE IF EXISTS `{$dbPrefix}sms_types`";

			$query[] = "DROP TABLE IF EXISTS `{$dbPrefix}sms_contents`";

			$query[] = "CREATE TABLE `{$dbPrefix}sms_types` (
						`id` int(11) NOT NULL,
						`sms_type` varchar(30) NOT NULL,
						`variables` text NOT NULL,
						`status` int(1) NOT NULL DEFAULT '1',
						`datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
						) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
			$query[] = "ALTER TABLE `{$dbPrefix}sms_types` ADD PRIMARY KEY (`id`);";
			$query[] = "ALTER TABLE `{$dbPrefix}sms_types` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";

			$query[] = "CREATE TABLE `{$dbPrefix}sms_contents` (
						`id` int(11) NOT NULL,
						`sms_type_id` int(11) NOT NULL,
						`sms_content` text CHARACTER SET utf8 NOT NULL,
						`lang_id` int(11) NOT NULL,
						`datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
						) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
			$query[] = "ALTER TABLE `{$dbPrefix}sms_contents` ADD PRIMARY KEY (`id`);";
			$query[] = "ALTER TABLE `{$dbPrefix}sms_contents` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
			$query[] = "INSERT INTO `{$dbPrefix}sms_types` (`sms_type`, `variables`) VALUES
						('registration', 'fullname,company_name,link'),
						('payout_release', 'fullname,company_name,amount'),
						('change_password', 'fullname,company_name,new_password'),
						('change_transaction_password', 'fullname,company_name,password'),
						('payout_request', 'fullname,company_name,admin_user_name,username,payout_amount');";
			$query[] = "INSERT INTO `{$dbPrefix}sms_contents` (`sms_type_id`, `sms_content`, `lang_id`) VALUES
						(1, 'You have been registered successfully in {company_name}!', 1),
						(1, '¡Te has registrado correctamente en {company_name}!', 2),
						(1, '您已成功在 {company_name} 中注册！', 3),
						(1, 'Sie wurden erfolgreich in {company_name} registriert!', 4),
						(1, 'Você foi registrado com sucesso em {company_name}!', 5),
						(1, 'Vous avez été enregistré avec succès dans {company_name}!', 6),
						(1, 'Sei stato registrato con successo in {company_name}!', 7),
						(1, '{company_name} sitesine başarıyla kaydoldunuz!', 8),
						(1, 'Zostałeś pomyślnie zarejestrowany w {company_name}!', 9),
						(1, 'لقد تم تسجيلك بنجاح في {company_name}!', 10),
						(1, 'Вы были успешно зарегистрированы в {company_name}!', 11),
						(2, 'Dear {fullname}, Your payout has been released successfully', 1),
						(2, 'Estimado {fullname}, Su pago ha sido liberado con éxito', 2),
						(2, '尊敬的 {fullname}，您的付款已成功释放', 3),
						(2, 'Lieber {fullname}, Ihre Auszahlung wurde erfolgreich freigegeben', 4),
						(2, 'Prezado {fullname}, seu pagamento foi liberado com sucesso', 5),
						(2, 'Cher {fullname}, Votre paiement a été validé', 6),
						(2, 'Gentile {fullname}, il tuo pagamento è stato rilasciato correttamente', 7),
						(2, 'Sayın {fullname}, Ödemeniz başarıyla onaylandı', 8),
						(2, 'Drogi {fullname}, Twoja wypłata została pomyślnie zwolniona', 9),
						(2, 'عزيزي {fullname} ، لقد تم إصدار دفعتك بنجاح', 10),
						(2, 'Уважаемый {fullname}, ваша выплата была успешно выпущена', 11),
						(3, 'Dear {fullname}, Your password has been successfully changed, Your new password is {new_password}', 1),
						(3, 'Estimado {fullname}, su contraseña se ha cambiado correctamente, su nueva contraseña es {new_password}', 2),
						(3, '尊敬的 {fullname}，您的密码已成功更改，您的新密码为 {new_password}', 3),
						(3, 'Lieber {fullname}, Ihr Passwort wurde erfolgreich geändert. Ihr neues Passwort lautet {new_password}', 4),
						(3, 'Prezado {fullname}, sua senha foi alterada com sucesso, sua nova senha é {new_password}', 5),
						(3, 'Cher {fullname}, Votre mot de passe a été modifié avec succès, Votre nouveau mot de passe est {new_password}', 6),
						(3, 'Gentile {fullname}, la tua password è stata cambiata correttamente, la tua nuova password è {new_password}', 7),
						(3, 'Sayın {fullname}, Şifreniz başarıyla değiştirildi, Yeni şifreniz {new_password}', 8),
						(3, 'Drogi {fullname}, Twoje hasło zostało pomyślnie zmienione, Twoje nowe hasło to {new_password}', 9),
						(3, 'عزيزي {fullname} ، تم تغيير كلمة مرورك بنجاح ، كلمة مرورك الجديدة {new_password}', 10),
						(3, 'Уважаемый {fullname}, Ваш пароль был успешно изменен, Ваш новый пароль {new_password}', 11),
						(4, 'Dear {fullname}, Your new Transaction Password is {password}', 1),
						(4, 'Estimado {fullname}, su nueva contraseña de transacción es {password}', 2),
						(4, '尊敬的 {fullname}，您的新交易密码为 {password}', 3),
						(4, 'Lieber {fullname}, Ihr neues Transaktionskennwort lautet {password}', 4),
						(4, 'Prezado {fullname}, sua nova senha de transação é {password}', 5),
						(4, 'Cher {fullname}, votre nouveau mot de passe de transaction est {password}', 6),
						(4, 'Gentile {fullname}, la tua nuova password di transazione è {password}', 7),
						(4, 'Sayın {fullname}, Yeni İşlem Parolanız {password}', 8),
						(4, 'Drogi {fullname}, nowe hasło do transakcji to {password}', 9),
						(4, 'عزيزي {fullname} ، كلمة المرور الجديدة للمعاملات الخاصة بك هي {password}', 10),
						(4, 'Уважаемый {fullname}, Ваш новый пароль для транзакции - {password}', 11),
						(5, 'Dear {admin_user_name}, {username} requested payout of {payout_amount}', 1),
						(5, 'Estimado {admin_user_name}, {username} solicitó el pago de {payout_amount}', 2),
						(5, '尊敬的 {admin_user_name}，{username} 请求支付 {payout_amount}', 3),
						(5, 'Sehr geehrter {admin_user_name}, {username} hat die Auszahlung von {payout_amount} angefordert', 4),
						(5, 'Prezado {admin_user_name}, {username} solicitou pagamento de {payout_amount}', 5),
						(5, 'Cher {admin_user_name}, {username} a demandé le paiement de {payout_amount}', 6),
						(5, 'Gentile {admin_user_name}, {username} ha richiesto il pagamento di {payout_amount}', 7),
						(5, 'Sayın {admin_user_name}, {username}, {payout_amount} tutarında ödeme yapılmasını istedi', 8),
						(5, 'Drogi {admin_user_name}, {username} zażądał wypłaty w wysokości {payout_amount}', 9),
						(5, 'عزيزي {admin_user_name} ، {username} طلب دفع تعويضات {payout_amount}', 10),
						(5, 'Уважаемый {admin_user_name}, {username} запросил выплату {payout_amount}', 11);";

			$query[] = "DELETE FROM `{$dbPrefix}infinite_urls` WHERE `id` = '289';";

			$query[] = "DELETE FROM `{$dbPrefix}infinite_urls` WHERE `id` = '290';";

			$query[] = "DELETE FROM `{$dbPrefix}infinite_mlm_sub_menu` WHERE `sub_id` = '221';";

			$query[] = "INSERT INTO `{$dbPrefix}infinite_urls` (`id`, `link`, `status`, `target`, `sub_menu_ref_id`) VALUES (289, 'sms/edit_sms_content', 'yes', 'none', '');";
			$query[] = "INSERT INTO `{$dbPrefix}infinite_urls` (`id`, `link`, `status`, `target`, `sub_menu_ref_id`) VALUES (290, 'sms/sms_content', 'yes', 'none', '');";
			$query[] = "INSERT INTO `{$dbPrefix}infinite_mlm_sub_menu` (`sub_id`, `sub_link_ref_id`, `icon`, `sub_status`, `sub_refid`, `perm_admin`, `perm_dist`, `perm_emp`, `sub_order_id`) VALUES ('221', '290', 'clip-bubbles-3', 'yes', '10', '1', '0', '1', '5');";

			// dd($query);die;
			
			foreach ($query as $qry) {
				$this->db->query($qry);
			}
		}
	}

	public function down()
	{
		$dbPrefix = $this->db->dbprefix;
		if ((($dbPrefix == 'inf_') || $this->db->where("sms_status", "yes")->count_all_results("module_status") > 0)) {
			$this->db->query("DROP TABLE IF EXISTS `{$dbPrefix}sms_contents`;");
			$this->db->query("DROP TABLE IF EXISTS `{$dbPrefix}sms_types`;");

			$this->db->where("sub_id", 221)->delete("infinite_mlm_sub_menu");
			$this->db->query("ALTER TABLE `{$dbPrefix}infinite_mlm_sub_menu` auto_increment = 221;");

			$this->db->where("id", 289)->delete("infinite_urls");
			$this->db->where("id", 290)->delete("infinite_urls");
			$this->db->query("ALTER TABLE `{$dbPrefix}infinite_urls` auto_increment = 289;");
		}
	}
}

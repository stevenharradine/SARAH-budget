<?php
	require_once '../../views/_secureHead.php';
	require_once $relative_base_path . 'models/edit.php';

	if( isset ($sessionManager) && $sessionManager->isAuthorized () ) {
		$id = request_isset ('id');
		
		$record = BudgetManager::getItemRecord ($id);

		$page_title = 'Edit | Reciept | Budget';

		// build edit view
		$editModel = new EditModel ('Edit', 'update_item_by_id', $id, 'receipt.php', 'item' );

		$editModel->addRow ('item_name', 'Name', $record['item_name'] );
		$editModel->addRow ('amount', 'Price', $record['amount'] );
		$editModel->addRow ('qty', 'Qty', $record['qty'] );
		$editModel->addRow ('category', 'Category', $record['category'] );
		$editModel->addRow ('brand', 'Brand', $record['brand'] );
		$editModel->addRow ('size', 'Size', $record['size'] );
		$editModel->addRow ('size_unit', 'units', $record['size_unit'] );
		$editModel->addRow ('tax', 'Tax', $record['tax'] );
		$editModel->addRow ('sale', 'sale', $record['sale'] );

		$views_to_load = array();
		$views_to_load[] = ' ' . EditView2::render($editModel);

		include $relative_base_path . 'views/_generic.php';
	}

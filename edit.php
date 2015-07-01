<?php
	require_once '../../views/_secureHead.php';
	require_once $relative_base_path . 'models/edit.php';

	if( isset ($sessionManager) && $sessionManager->isAuthorized () ) {
		$id = request_isset ('id');

		$budgetManager = new BudgetManager ();
		
		$record = $budgetManager->getRecord ($id);

		$page_title = 'Edit | Bookmarks';

		$record_date_split = explode (' ', $record['date']);
		$date = $record_date_split[0];
		$time = $record_date_split[1];

		// build edit view
		$editModel = new EditModel ('Edit', 'update_by_id', $id);
//		$editModel->addRow ('amount', 'Amount', $record['amount'] );
//		$editModel->addRow ('category', 'Category', $record['category'] );
		$editModel->addRow ('store', 'Store', $record['store'] );
//		$editModel->addRow ('items', 'Items', $record['items'] );
		$editModel->addRow ('dateOption', 'dateOption', 'dateOption-selectTime', 'readonly=readonly' );
		$editModel->addRow ('selectTime-date', 'Date', $date );
		$editModel->addRow ('selectTime-time', 'Time', $time );

		$views_to_load = array();
		$views_to_load[] = ' ' . EditView2::render($editModel);

		include $relative_base_path . 'views/_generic.php';
	}

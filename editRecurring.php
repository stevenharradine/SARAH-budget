<?php
	require_once '../../views/_secureHead.php';
	require_once $relative_base_path . 'models/edit.php';

	if( isset ($sessionManager) && $sessionManager->isAuthorized () ) {
		$id = request_isset ('id');
		
		$record = BudgetManager::getRecurringRecord ($id);

		$page_title = 'Edit | Bookmarks';

		// build edit view
		$editModel = new EditModel ('Edit', 'update_by_id', $id, 'recurring.php');
		$editModel->addRow ('amount', 'Amount', $record['amount'] );
		$editModel->addRow ('category', 'Category', $record['category'] );
		$editModel->addRow ('store', 'Store', $record['store'] );
		$editModel->addRow ('items', 'Items', $record['items'] );
		$editModel->addRow ('startdate', 'Start date', $record['startDate'] );
		$editModel->addRow ('enddate', 'End date', $record['endDate'] );

		$views_to_load = array();
		$views_to_load[] = ' ' . EditView2::render($editModel);

		include $relative_base_path . 'views/_generic.php';
	}

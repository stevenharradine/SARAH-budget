<?php
	require_once '../../views/_secureHead.php';

	require_once $relative_base_path . 'models/add.php';
	require_once $relative_base_path . 'models/table.php';
	require_once $relative_base_path . 'models/button.php';

	if( isset ($sessionManager) && $sessionManager->isAuthorized () ) {
		$id = request_isset ('id');
		$amount = request_isset ('amount');
		$category = request_isset ('category');
		$store = request_isset ('store');
		$items = request_isset ('items');
		$startdate = request_isset ('startdate');
		$enddate = request_isset ('enddate');
		
		switch ($page_action) {
			case ('update_by_id') :
				$db_update_success = BudgetManager::updateRecurringRecord ($id, $amount, $category, $store, $items, $startdate, $enddate);
				break;
			case ('add_budget_item') :
				$db_add_success = BudgetManager::addRecurringRecord ($amount, $category, $store, $items, $startdate, $enddate);
				break;
			case ('delete_by_id') :
				$db_delete_success = BudgetManager::deleteRecurringRecord ($id);
				break;
		}

		$page_title = 'Recurring | Budget';

		$alt_menu = getAddButton() . getBackButton();

		$addModel = new AddModel ('Add', 'add_budget_item');
		$addModel->addRow ('amount', 'Amount');
		$addModel->addRow ('category', 'Category');
		$addModel->addRow ('store', 'Store');
		$addModel->addRow ('items', 'Items');
		$addModel->addRow ('startdate', 'Start Date', 'CURRENT_TIMESTAMP');
		$addModel->addRow ('enddate', 'End Date', '2037-12-31 23:59:59');

		// build recurring table model
		$recurringModel = new TableModel ('Recurring items', 'recurring');
		$recurringModel->addRow ( array (
			TableView2::createCell ('amount', 'Amount', 'th' ),
			TableView2::createCell ('category', 'Category', 'th' ),
			TableView2::createCell ('store', 'Store', 'th' ),
			TableView2::createCell ('items', 'Items', 'th' ),
			TableView2::createCell ()
		));

		$recurring_items_data = BudgetManager::getAllRecurring ();

		while (($recurring_items_row = mysql_fetch_array( $recurring_items_data ) ) != null) {
			$amount = $recurring_items_row['amount'];
			$category = $recurring_items_row['category'];

			$recurringModel->addRow ( array (
				TableView2::createCell ('amount',  format_currency ($amount) ),
				TableView2::createCell ('category',  $category),
				TableView2::createCell ('store', $recurring_items_row['store'] ),
				TableView2::createCell ('items', $recurring_items_row['items'] ),
				TableView2::createEdit ($recurring_items_row['RECURRING_ID'], 'editRecurring.php')
			));

			$total_spent += $amount;

			$category_normalized = strtolower ($category);
			
			$totals[$category_normalized] = isset ($totals[$category_normalized]) ? $totals[$category_normalized] + $amount : $amount;
		}

		$views_to_load = array();
		$views_to_load[] = ' ' . AddView2::render($addModel);
		$views_to_load[] = ' ' . TableView2::render($recurringModel);
		
		include $relative_base_path . 'views/_generic.php';
	}
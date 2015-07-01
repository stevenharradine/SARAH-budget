<?php
	require_once '../../views/_secureHead.php';

	require_once $relative_base_path . 'models/add.php';
	require_once $relative_base_path . 'models/table.php';
	require_once $relative_base_path . 'models/button.php';

	if( isset ($sessionManager) && $sessionManager->isAuthorized () ) {
		$id = request_isset ('id');

		$store = request_isset ('store');
		$dateOption = request_isset ('dateOption');
		$selectTimeDate = request_isset ('selectTime-date');
		$selectTimeTime = request_isset ('selectTime-time');

		$budget_id = request_isset ('budget_id');
		$item_id = request_isset ('item_id');
		$item_name = request_isset ('item_name');
		$amount = request_isset ('amount');
		$qty = request_isset ('qty');
		$category = request_isset ('category');
		$brand = request_isset ('brand');
		$size = request_isset ('size');
		$size_unit = request_isset ('size_unit');
		$tax = request_isset ('tax');
		$sale = request_isset ('sale');

		// figure out what $date should be
		if ($dateOption == 'dateOption-current') {
			$date = 'CURRENT_TIMESTAMP';
		} else if ($dateOption == 'dateOption-selectTime') {
			$date = "$selectTimeDate $selectTimeTime";
		}

		switch ($page_action) {
			case ('update_item_by_id') :
				$id = BudgetManager::getBudgetIdFromItemId ($item_id);
				$db_update_success = BudgetManager::updateItemRecord ($item_id, $item_name, $amount, $qty, $category, $brand, $size, $size_unit, $tax, $sale);
				break;
			case ('add_receipt_item') :
				$id = $budget_id;
				$db_add_success = BudgetManager::addItemRecord ($budget_id, $item_name, $amount, $qty, $category, $brand, $size, $size_unit, $tax, $sale);
				break;
			case ('delete_item_by_id') :
				$id = BudgetManager::getBudgetIdFromItemId ($item_id);
				$db_delete_success = BudgetManager::deleteItemRecord ($item_id);
				break;
			case ('add_budget_item') :
				$id = BudgetManager::addRecord ($store, $date);
				break;
		}

		$spending_history_data = BudgetManager::getRecord($id);
		$items = BudgetManager::getItems($id);
		$receipt_total = 0;

		$page_title = 'Receipt | Budget';
		$alt_menu = getAddButton() . getBackButton();

		// add Item form
		$addModel = new AddModel ('Add', 'add_receipt_item');
		$addModel->addRow ('item_name', 'Item');
		$addModel->addRow ('amount', 'Amount');
		$addModel->addRow ('qty', 'Qty', 1);
		$addModel->addRow ('category', 'Category');
		$addModel->addRow ('brand', 'Brand');
		$addModel->addRow ('size', 'Size');
		$addModel->addRow ('size_unit', 'Units');
		$addModel->addRow ('sale', 'Sale');
		$addModel->addOptionBox ('tax', 'Tax', [
			'13%',
			'0%'
		]);
		$addModel->addRow ('budget_id', 'budget_id', $id);

		// build items table model
		$itemsModel = new TableModel ('Items', 'budget');
		$itemsModel->addRow ( array (
			TableView2::createCell ('total', 'total', 'th' ),
			TableView2::createCell ('qty', 'qty', 'th' ),
			TableView2::createCell ('tax', 'tax', 'th' ),
			TableView2::createCell ('amount', 'Amount', 'th' ),
			TableView2::createCell ('item_name', 'Item', 'th' ),
			TableView2::createCell ('category', 'Category', 'th' ),
			TableView2::createCell ('brand', 'Brand', 'th' ),
			TableView2::createCell ('size', 'Size', 'th' ),
			TableView2::createCell ('units', 'units', 'th' ),
			TableView2::createCell ('sale', 'sale', 'th' ),
			TableView2::createCell ()
		));

		while (($items_row = mysql_fetch_array( $items ) ) != null) {
			$item_total = $items_row['amount'] * $items_row['qty'] * ( 1 + $items_row['tax'] );

			$itemsModel->addRow ( array (
				TableView2::createCell ('total', format_currency ( $item_total ) ),
				TableView2::createCell ('qty', $items_row['qty'] ),
				TableView2::createCell ('tax', format_percent ($items_row['tax']) ),
				TableView2::createCell ('amount', format_currency ( $items_row['amount'] ) ),
				TableView2::createCell ('item_name', $items_row['item_name'] ),
				TableView2::createCell ('category', $items_row['category'] ),
				TableView2::createCell ('brand', $items_row['brand'] ),
				TableView2::createCell ('size', $items_row['size'] ),
				TableView2::createCell ('units', $items_row['size_unit'] ),
				TableView2::createCell ('sale', $items_row['sale'] ),
				TableView2::createEdit ($items_row['BUDGET_ITEM_ID'], 'editItem.php')
			));

			$receipt_total += $item_total;
		}

		// build budget table model
		$budgetModel = new TableModel ('', 'budget');
		$budgetModel->addRow ( array (
			TableView2::createCell ('total', 'Total', 'th' ),
			TableView2::createCell ('store', 'Store', 'th' ),
			TableView2::createCell ('date', 'Date', 'th' )
		));

		$budgetModel->addRow ( array (
			TableView2::createCell ('amount', format_currency ( $receipt_total ) ),
			TableView2::createCell ('store', $spending_history_data['store'] ),
			TableView2::createCell ('date', $spending_history_data['date'] )
		));

		$views_to_load[] = ' ' . AddView2::render($addModel);
		$views_to_load[] = ' ' . TableView2::render($budgetModel);
		$views_to_load[] = ' ' . TableView2::render($itemsModel);
		
		include $relative_base_path . 'views/_generic.php';
	}
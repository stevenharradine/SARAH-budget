<?php
	require_once '../../views/_secureHead.php';

	require_once $relative_base_path . 'models/add.php';
	require_once $relative_base_path . 'models/table.php';
	require_once $relative_base_path . 'models/button.php';

	if( isset ($sessionManager) && $sessionManager->isAuthorized () ) {
		$id = request_isset ('id');
		$year = request_isset ('year');
		$month = request_isset ('month');
		$store = request_isset ('store');
		$dateOption = request_isset ('dateOption');
		$selectTimeDate = request_isset ('selectTime-date');
		$selectTimeTime = request_isset ('selectTime-time');

		// figure out what $date should be
		if ($dateOption == 'dateOption-current') {
			$date = 'CURRENT_TIMESTAMP';
		} else if ($dateOption == 'dateOption-selectTime') {
			$date = "$selectTimeDate $selectTimeTime";
		}

		$hasTitle = $year != null && $month != null;
		
		$total_spent = 0;
		$totals = array ();
		$index = 0;
		$budget_line_items = '';

		if ($hasTitle) {
			$todayDate = new DateTime ("$year-$month-01 00:00:01");
			$todayDateFormated = date ('F Y', $todayDate->getTimestamp());
		}
		
		// pad month with leading 0 for database
		if (strlen ($month) == 1) {
			$month = '0' . $month;
		}

		switch ($page_action) {
			case ('update_by_id') :
				$db_update_success = BudgetManager::updateRecord ($id, $store, $date);
				break;
			case ('delete_by_id') :
				$db_delete_success = BudgetManager::deleteRecord ($id);
				break;
		}
		
		$spending_history_data = BudgetManager::getAllRecords($year, $month);
		
		$filterDOM = '';
		if ($year != null) {
			$filterDOM .= "<input type='hidden' name='year' value='$year' />";
		}
		if ($month != null) {
			$filterDOM .= "<input type='hidden' name='month' value='$month' />";
		}


		$page_title = 'Budget';

		if (! (isset ($_REQUEST['year']) && isset ($_REQUEST['month']))) {
			$meta = "<meta http-equiv='refresh' content='0;url=index.php?year=" . date("Y") . "&month=" . date("m") . "' />";
		}
		$alt_menu = getAddButton() . ButtonView::render (new ButtonModel(IconView::render( new IconModel ('arrows-ccw', 'Recurring')), 'recurring.php', 'recurring'));

		$addModel = new AddModel ('Add', 'add_budget_item', 'receipt.php');
		$addModel->addRow ('store', 'Store');
		$addModel->addRadioOption ('dateOption-current', 'CURRENT_TIMESTAMP', 'dateOption', 'checked="checked"');
		$addModel->addRadioOption ('dateOption-selectTime', 'Date/Time', 'dateOption');
		$addModel->addRow ('selectTime-date', 'Date', '', 'YYYY-MM-DD');
		$addModel->addRow ('selectTime-time', 'Time', '', 'HH:MM:SS');

		// build recurring table model
		$recurringModel = new TableModel ('Recurring items', 'recurring');
		$recurringModel->addRow ( array (
			TableView2::createCell ('amount', 'Amount', 'th' ),
			TableView2::createCell ('category', 'Category', 'th' ),
			TableView2::createCell ('store', 'Store', 'th' ),
			TableView2::createCell ('items', 'Items', 'th' )
		));

		$recurring_items_data = BudgetManager::getAllRecurringByMonth ( $year, $month );

		while (($recurring_items_row = mysql_fetch_array( $recurring_items_data ) ) != null) {
			$amount = $recurring_items_row['amount'];
			$category = $recurring_items_row['category'];

			$recurringModel->addRow ( array (
				TableView2::createCell ('amount',  format_currency ($amount) ),
				TableView2::createCell ('category', $category),
				TableView2::createCell ('store', $recurring_items_row['store'] ),
				TableView2::createCell ('items', $recurring_items_row['items'] )
			));

			$total_spent += $amount;

			$category_normalized = strtolower ($category);
			
			$totals[$category_normalized] = isset ($totals[$category_normalized]) ? $totals[$category_normalized] + $amount : $amount;
		}

		// build budget table model
		$budgetModel = new TableModel ('Reciepts', 'budget');
		$budgetModel->addRow ( array (
			TableView2::createCell ('amount', 'Amount', 'th' ),
			TableView2::createCell ('store', 'Store', 'th' ),
			TableView2::createCell ('date', 'Date', 'th' ),
			TableView2::createCell (),
			TableView2::createCell ()
		));

		while (($spending_history_row = mysql_fetch_array( $spending_history_data ) ) != null) {
			$this_id = $spending_history_row['BUDGET_ID'];
			$store = $spending_history_row['store'];
			$date = $spending_history_row['date'];
			$total = BudgetManager::getRecieptTotalById($this_id);

			$reciept_items_data = BudgetManager::getItems($this_id);

			while (($reciept_items_row = mysql_fetch_array( $reciept_items_data ) ) != null) {
				$amount = $reciept_items_row['amount'] * $reciept_items_row['qty'] * ( 1 + $reciept_items_row['tax']);

				$total_spent += $amount;
				$category_normalized = strtolower ($reciept_items_row['category']);
				$totals[$category_normalized] = isset ($totals[$category_normalized]) ? $totals[$category_normalized] + $amount : $amount;
			}

			$budgetModel->addRow ( array (
				TableView2::createCell ('amount', format_currency ($total) ),
				TableView2::createCell ('store', $spending_history_row['store'] ),
				TableView2::createCell ('date', $spending_history_row['date'] ),
				TableView2::createCell ('view', "<a href='receipt.php?id=$this_id'>View</a>"),
				TableView2::createEdit ($this_id)
			));
		}

		$categoryModel = new TableModel ('Category totals', 'categoryTotals');

		foreach ($totals as $this_category => $this_amount) {
			$categoryModel->addRow ( array (
				TableView2::createCell ('category', $this_category, 'th'),
				TableView2::createCell ('amount', format_currency ( $this_amount ) )
			));
		}
		$categoryModel->addRow ( array (
			TableView2::createCell ('', 'Total', 'th' ),
			TableView2::createCell ('total', format_currency ( $total_spent ) )
		));

		$categoryModel->setTitleWeight ('h3');
		$recurringModel->setTitleWeight ('h3');
		$budgetModel->setTitleWeight ('h3');

		$views_to_load = array();
		if ($hasTitle) {
			$views_to_load[] = " <h2>$todayDateFormated</h2>";
		}
		$views_to_load[] = ' ' . AddView2::render($addModel);
		$views_to_load[] = '_paging.php';
		$views_to_load[] = ' ' . TableView2::render($categoryModel);
		$views_to_load[] = ' ' . TableView2::render($recurringModel);
		$views_to_load[] = ' ' . TableView2::render($budgetModel);
		$views_to_load[] = '_paging.php';
		
		include $relative_base_path . 'views/_generic.php';
	}
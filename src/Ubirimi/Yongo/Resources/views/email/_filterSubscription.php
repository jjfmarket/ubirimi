<?php

use Ubirimi\SystemProduct;

$urlIssuePrefix = '/yongo/issue/';
$selectedProductId = SystemProduct::SYS_PRODUCT_YONGO;
$columns = $columns;
$issues = $issues;
$issuesCount = $issues->num_rows;
$clientSettings = $clientSettings;
$clientId = $clientId;
$cliMode = $cliMode;
$getSearchParameters = $searchParameters;

require __DIR__ . '/../issue/search/_listResult.php';
<?php
// Include all algorithm functions
require_once 'function.php';

// =================================================================================================
// 1. PHP INITIALIZATION AND INPUT PERSISTENCE
// =================================================================================================

// Initialize result variables to hold the HTML output for each simulation block
$sortOutput = "";
$searchOutput = "";
$memoryOutput = "";
$pagingOutput = "";

// Capture input from POST requests and fallback to default values for initial page load.
// This ensures user input persists across submissions.
$sortInput = $_POST['sortInput'] ?? "5,3,8,4,2";
$searchInput = $_POST['searchInput'] ?? "1,3,5,7,9";
$searchTarget = $_POST['searchTarget'] ?? "7";
$blockInput = $_POST['blockInput'] ?? "100,500,200,300,600";
$processInput = $_POST['processInput'] ?? "212,417,112,426";
$refInput = $_POST['refInput'] ?? "7,0,1,2,0,3,0,4";
$frameSize = $_POST['frameSize'] ?? "3";
$activeTab = $_POST['activeTab'] ?? 'sort'; // Track the active tab

// Ensure $frameSize is an integer
$frameSize = (int)$frameSize > 0 ? (int)$frameSize : 3;


// =================================================================================================
// 2. REQUEST ROUTING AND EXECUTION
// =================================================================================================

// Check if a POST request was made and an action is specified
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    try {
        // Execute the relevant algorithm based on the 'action' parameter
        switch ($action) {
            case 'bubbleSort':
            case 'selectionSort':
            case 'mergeSort':
                $activeTab = 'sort';
                if ($action == 'bubbleSort') {
                    $sortOutput = bubbleSort(getArrayFromInput($sortInput));
                } elseif ($action == 'selectionSort') {
                    $sortOutput = selectionSort(getArrayFromInput($sortInput));
                } else {
                    $sortOutput = mergeSort(getArrayFromInput($sortInput));
                }
                break;
            case 'linearSearch':
            case 'binarySearch':
            case 'recursiveBinarySearch':
                $activeTab = 'search';
                if ($action == 'linearSearch') {
                    $searchOutput = linearSearch(getArrayFromInput($searchInput), (int) $searchTarget);
                } elseif ($action == 'binarySearch') {
                    $searchOutput = binarySearch(getArrayFromInput($searchInput), (int) $searchTarget);
                } else {
                    $searchOutput = jumpSearch(getArrayFromInput($searchInput), (int) $searchTarget);
                }
                break;
            case 'firstFit':
                $activeTab = 'memory';
                $blocks = getArrayFromInput($blockInput);
                $processes = getArrayFromInput($processInput);
                $memoryOutput = firstFit($blocks, $processes);
                break;
            case 'fifoPaging':
            case 'lruPaging':
                $activeTab = 'paging';
                if ($action == 'fifoPaging') {
                    $pagingOutput = fifoPaging(getArrayFromInput($refInput), $frameSize);
                } else {
                    $pagingOutput = lruPaging(getArrayFromInput($refInput), $frameSize);
                }
                break;
            default:
                // If action is unknown but a tab was set, preserve it.
                break;
        }
    } catch (Exception $e) {
        // Generic error handling (kept simplified for this example)
        $error = "<p class='text-red-500'>Error: Invalid input or calculation issue. Please check your comma-separated values.</p>";
        
        if (in_array($action, ['bubbleSort', 'selectionSort', 'mergeSort'])) {
            $sortOutput = $error;
            $activeTab = 'sort';
        } elseif (in_array($action, ['linearSearch', 'binarySearch', 'recursiveBinarySearch'])) {
            $searchOutput = $error;
            $activeTab = 'search';
        } elseif ($action == 'firstFit') {
            $memoryOutput = $error;
            $activeTab = 'memory';
        } else {
            $pagingOutput = $error;
            $activeTab = 'paging';
        }
    }
}
?>
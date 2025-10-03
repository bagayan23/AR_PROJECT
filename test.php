<?php
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

// Ensure $frameSize is an integer
$frameSize = (int)$frameSize > 0 ? (int)$frameSize : 3;


// =================================================================================================
// 2. HELPER FUNCTIONS
// =================================================================================================

/**
 * Parses a comma-separated string into an array of integers, ignoring non-numeric parts.
 */
function getArrayFromInput(string $input): array {
    $parts = explode(',', $input);
    $array = [];
    foreach ($parts as $part) {
        $trimmed = trim($part);
        if (is_numeric($trimmed)) {
            $array[] = (int) $trimmed;
        }
    }
    return $array;
}


// =================================================================================================
// 3. DATA ALGORITHMS (Sorting and Searching)
// =================================================================================================

/**
 * Simulates and generates step-by-step output for the Bubble Sort algorithm.
 */
function bubbleSort(array $arr): string {
    $n = count($arr);
    $output = "<h4>Bubble Sort Result</h4><p>Initial: [" . implode(", ", $arr) . "]</p>";
    for ($i = 0; $i < $n - 1; $i++) {
        for ($j = 0; $j < $n - $i - 1; $j++) {
            if ($arr[$j] > $arr[$j+1]) {
                // Swap elements using PHP array destructuring
                [$arr[$j], $arr[$j+1]] = [$arr[$j+1], $arr[$j]];
            }
            $output .= "<p>Pass " . ($i+1) . ", Step " . ($j+1) . ": [" . implode(", ", $arr) . "]</p>";
        }
    }
    return $output;
}

/**
 * Simulates and generates step-by-step output for the Selection Sort algorithm.
 * Note: Updated to include more granular step output.
 */
function selectionSort(array $arr): string {
    $n = count($arr);
    $output = "<h4>Selection Sort Result</h4><p>Initial: [" . implode(", ", $arr) . "]</p>";
    for ($i = 0; $i < $n - 1; $i++) {
        $minIndex = $i;
        $output .= "<p>Pass " . ($i+1) . " (Unsorted starts at index $i):</p>";
        
        // Find the minimum element in the remaining unsorted array
        for ($j = $i + 1; $j < $n; $j++) {
            if ($arr[$j] < $arr[$minIndex]) {
                $minIndex = $j;
            }
        }
        
        $output .= "<p class='ml-4'>&rarr; Minimum element found at index $minIndex (value: {$arr[$minIndex]}).</p>";

        // Swap the found minimum element with the current element
        if ($minIndex !== $i) {
            [$arr[$i], $arr[$minIndex]] = [$arr[$minIndex], $arr[$i]];
            // REMOVED ASTERISKS from swap description
            $output .= "<p class='ml-4'>&rarr; SWAP {$arr[$minIndex]} and {$arr[$i]}. Array: [" . implode(", ", $arr) . "]</p>";
        } else {
            $output .= "<p class='ml-4'>&rarr; No swap. Array: [" . implode(", ", $arr) . "]</p>";
        }
    }
    return $output;
}

/**
 * Helper function to merge two sorted subarrays and log the step.
 */
function merge(array $left, array $right, &$output): array {
    $result = [];
    $i = 0; // index for $left
    $j = 0; // index for $right

    // Merge until one array is exhausted
    while ($i < count($left) && $j < count($right)) {
        if ($left[$i] <= $right[$j]) {
            $result[] = $left[$i];
            $i++;
        } else {
            $result[] = $right[$j];
            $j++;
        }
    }

    // Append remaining elements (if any)
    while ($i < count($left)) {
        $result[] = $left[$i];
        $i++;
    }
    while ($j < count($right)) {
        $result[] = $right[$j];
        $j++;
    }

    $output .= "<p class='ml-4'>&rarr; MERGE: [" . implode(", ", $left) . "] and [" . implode(", ", $right) . "] &rarr; [" . implode(", ", $result) . "]</p>";
    return $result;
}

/**
 * The core recursive function for Merge Sort.
 */
function mergeSortCore(array $arr, &$output): array {
    $n = count($arr);
    if ($n <= 1) {
        return $arr; // Base case: array with 0 or 1 element is sorted
    }

    $mid = (int) floor($n / 2);
    $left = array_slice($arr, 0, $mid);
    $right = array_slice($arr, $mid);

    $output .= "<p>Divide: [" . implode(", ", $arr) . "] into Left: [" . implode(", ", $left) . "] and Right: [" . implode(", ", $right) . "]</p>";

    $left = mergeSortCore($left, $output);
    $right = mergeSortCore($right, $output);

    // After recursive calls return, merge the two sorted halves
    return merge($left, $right, $output);
}

/**
 * Simulates and generates step-by-step output for the Merge Sort algorithm.
 */
function mergeSort(array $arr): string {
    $output = "<h4>Merge Sort Result</h4><p>Initial: [" . implode(", ", $arr) . "]</p>";
    
    // Check for empty array to prevent issues
    if (empty($arr)) {
        $output .= "<p>Array is empty. Nothing to sort.</p>";
        return $output;
    }

    $sortedArr = mergeSortCore($arr, $output);
    
    $output .= "<p class='font-bold mt-2'>Final Sorted Array: [" . implode(", ", $sortedArr) . "]</p>";
    return $output;
}


/**
 * Simulates and generates step-by-step output for the Linear Search algorithm.
 */
function linearSearch(array $arr, int $target): string {
    $output = "<h4>Linear Search Result</h4>";
    $found = false;
    foreach ($arr as $i => $value) {
        $output .= "<p>Check index $i: value $value</p>";
        if ($value === $target) {
            $output .= "<p class='text-green-600 font-bold'>Found $target at index $i</p>";
            $found = true;
            break;
        }
    }
    if (!$found) {
        $output .= "<p class='text-red-600 font-bold'>Target $target not found.</p>";
    }
    return $output;
}

/**
 * Simulates and generates step-by-step output for the Jump Search algorithm.
 * Replaces the previous recursiveBinarySearch.
 */
function jumpSearch(array $arr, int $target): string {
    // Jump search requires a sorted array
    sort($arr);
    $n = count($arr);
    $output = "<h4>Jump Search Result</h4><p>Searching in Sorted Array: [" . implode(", ", $arr) . "]</p>";

    if ($n === 0) {
        $output .= "<p class='text-red-600 font-bold'>Target $target not found (Array is empty).</p>";
        return $output;
    }

    // Determine optimal block size (square root of array size)
    $blockSize = (int) floor(sqrt($n));
    $prev = 0;
    $step = $blockSize;

    // 1. Jumping/Block Search
    $output .= "<p>Block size (m): $blockSize</p>";
    
    while ($prev < $n && $arr[min($step, $n) - 1] < $target) {
        $output .= "<p>Jump from index $prev (Value: {$arr[$prev]}) to index " . (min($step, $n) - 1) . " (Value: {$arr[min($step, $n) - 1]}). Value is too small.</p>";
        $prev = $step;
        $step += $blockSize;
        
        if ($prev >= $n) {
             // We jumped past the end and the target is not in the last block
             $output .= "<p>Jumped past the end of the array.</p>";
             $output .= "<p class='text-red-600 font-bold'>Target $target not found.</p>";
             return $output;
        }
    }
    
    // Check if the jump landed exactly on the target
    if ($prev < $n && $arr[min($step, $n) - 1] === $target) {
         $output .= "<p>Jump landed exactly on target $target at index " . (min($step, $n) - 1) . ".</p>";
         $output .= "<p class='text-green-600 font-bold'>Found $target at index " . (min($step, $n) - 1) . "</p>";
         return $output;
    }

    // 2. Linear Search within the determined block
    $linearStart = $prev;
    $linearEnd = min($step, $n);
    
    $output .= "<p>Target not found at block end. Beginning linear search from index $linearStart up to $linearEnd.</p>";
    
    for ($i = $linearStart; $i < $linearEnd; $i++) {
        $output .= "<p class='ml-4'>Linear check index $i: value {$arr[$i]}</p>";
        if ($arr[$i] === $target) {
            $output .= "<p class='text-green-600 font-bold'>Found $target at index $i</p>";
            return $output;
        }
        // If the current element exceeds the target, we can stop the linear search
        if ($arr[$i] > $target) {
            $output .= "<p class='ml-4'>Value {$arr[$i]} > Target $target. Stopping linear search.</p>";
            break;
        }
    }

    $output .= "<p class='text-red-600 font-bold'>Target $target not found.</p>";
    return $output;
}

/**
 * Simulates and generates step-by-step output for the Iterative Binary Search algorithm.
 * RENAMED from iterativeBinarySearch to just binarySearch.
 */
function binarySearch(array $arr, int $target): string {
    // Ensure the array is sorted as required for binary search
    sort($arr);
    $output = "<h4>Binary Search (Iterative) Result</h4><p>Searching in Sorted Array: [" . implode(", ", $arr) . "]</p>";

    $low = 0;
    $high = count($arr) - 1;
    $found = false;

    while ($low <= $high) {
        $mid = (int) floor(($low + $high) / 2);
        $midValue = $arr[$mid] ?? null;

        if ($midValue === null) break;

        $output .= "<p>Searching range indices [$low, $high]. Check mid $mid: value $midValue</p>";

        if ($midValue === $target) {
            $output .= "<p class='text-green-600 font-bold'>Found $target at index $mid</p>";
            $found = true;
            break;
        } elseif ($midValue < $target) {
            $low = $mid + 1; // Search right half
        } else {
            $high = $mid - 1; // Search left half
        }
    }

    if (!$found) {
        $output .= "<p class='text-red-600 font-bold'>Target $target not found.</p>";
    }
    return $output;
}


// =================================================================================================
// 4. MEMORY ALGORITHMS (Allocation and Paging)
// =================================================================================================

/**
 * Simulates the First Fit memory allocation algorithm.
 */
function firstFit(array $blocks, array $processes): string {
    $originalBlocks = $blocks;
    $n = count($processes);
    $m = count($blocks);
    $allocation = array_fill(0, $n, -1);
    $output = "<h4>First Fit Result</h4>";

    foreach ($processes as $i => $p) {
        // Find the first block that can accommodate the process
        for ($j = 0; $j < $m; $j++) {
            if ($blocks[$j] >= $p) {
                $allocation[$i] = $j;
                $blocks[$j] -= $p; // Allocate and reduce block size
                break;
            }
        }
        $blockIndex = $allocation[$i];
        $allocationText = $blockIndex !== -1 ? "Block " . ($blockIndex + 1) . " (Initial Size: {$originalBlocks[$blockIndex]}KB)" : "Not Allocated";
        $output .= "<p>Process {$p}KB &rarr; {$allocationText}</p>";
        $output .= "<p>Remaining free blocks: [" . implode(", ", $blocks) . "]</p>";
    }
    return $output;
}

/**
 * Simulates the First-In, First-Out (FIFO) Page Replacement algorithm.
 */
function fifoPaging(array $refString, int $frameSize): string {
    $frames = [];
    $pageFaults = 0;
    $output = "<h4>FIFO Paging Result (Frames: $frameSize)</h4>";

    foreach ($refString as $step => $page) {
        $isHit = in_array($page, $frames);
        $replaced = "";

        if (!$isHit) {
            $pageFaults++;
            if (count($frames) < $frameSize) {
                $frames[] = $page; // Add to free frame
            } else {
                $replaced = array_shift($frames); // FIFO: remove the oldest page (from the start)
                $frames[] = $page; // Add the new page (to the end)
            }
        }

        $status = $isHit ? "HIT" : "FAULT";
        $statusClass = $isHit ? 'text-green-600' : 'text-red-600';
        $replacedInfo = $replaced ? "(Replaced $replaced)" : "";

        $output .= "<p>Step " . ($step+1) . ": Request {$page} <span class='font-bold $statusClass'>&rarr; {$status}</span> {$replacedInfo}</p>";
        $output .= "<div class='flex flex-wrap'>";
        foreach ($frames as $f) {
            $output .= "<div class='frame used'>$f</div>"; // Display current frames
        }
        // Fill remaining frames with empty placeholders
        for ($k = count($frames); $k < $frameSize; $k++) {
            $output .= "<div class='frame free'>-</div>";
        }
        $output .= "</div>";
    }

    $output .= "<p class='font-bold mt-2'>Total Page Faults: $pageFaults</p>";
    return $output;
}

/**
 * Simulates the Least Recently Used (LRU) Page Replacement algorithm.
 */
function lruPaging(array $refString, int $frameSize): string {
    $frames = [];
    $recent = []; // Key: page, Value: last access step index
    $pageFaults = 0;
    $output = "<h4>LRU Paging Result (Frames: $frameSize)</h4>";

    foreach ($refString as $step => $page) {
        $isHit = in_array($page, $frames);
        $replaced = "";

        if (!$isHit) {
            $pageFaults++;
            if (count($frames) < $frameSize) {
                $frames[] = $page; // Add to free frame
            } else {
                // Find LRU page: the one with the minimum 'recent' time stamp
                $lruPage = null;
                $minTime = PHP_INT_MAX;

                foreach ($frames as $f) {
                    // Check last access time
                    if (!isset($recent[$f]) || $recent[$f] < $minTime) {
                        $minTime = $recent[$f] ?? -1;
                        $lruPage = $f;
                    }
                }

                // Replace the LRU page in the frames array
                $index = array_search($lruPage, $frames);
                if ($index !== false) {
                    $replaced = $lruPage;
                    $frames[$index] = $page;
                }
            }
        }

        $recent[$page] = $step; // Update last used time for the current page

        $status = $isHit ? "HIT" : "FAULT";
        $statusClass = $isHit ? 'text-green-600' : 'text-red-600';
        $replacedInfo = $replaced ? "(Replaced $replaced)" : "";

        $output .= "<p>Step " . ($step+1) . ": Request {$page} <span class='font-bold $statusClass'>&rarr; {$status}</span> {$replacedInfo}</p>";
        $output .= "<div class='flex flex-wrap'>";
        foreach ($frames as $f) {
            $output .= "<div class='frame used'>$f</div>";
        }
        // Fill remaining frames with empty placeholders
        for ($k = count($frames); $k < $frameSize; $k++) {
            $output .= "<div class='frame free'>-</div>";
        }
        $output .= "</div>";
    }

    $output .= "<p class='font-bold mt-2'>Total Page Faults: $pageFaults</p>";
    return $output;
}


// =================================================================================================
// 5. REQUEST ROUTING AND EXECUTION
// =================================================================================================

// Check if a POST request was made and an action is specified
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    try {
        // Execute the relevant algorithm based on the 'action' parameter
        switch ($action) {
            case 'bubbleSort':
                $sortOutput = bubbleSort(getArrayFromInput($sortInput));
                break;
            case 'selectionSort':
                $sortOutput = selectionSort(getArrayFromInput($sortInput));
                break;
            case 'mergeSort': 
                $sortOutput = mergeSort(getArrayFromInput($sortInput));
                break;
            case 'linearSearch':
                $searchOutput = linearSearch(getArrayFromInput($searchInput), (int) $searchTarget);
                break;
            case 'binarySearch':
                $searchOutput = binarySearch(getArrayFromInput($searchInput), (int) $searchTarget);
                break;
            case 'recursiveBinarySearch': // This action now calls the Jump Search function
                $searchOutput = jumpSearch(getArrayFromInput($searchInput), (int) $searchTarget);
                break;
            case 'firstFit':
                $blocks = getArrayFromInput($blockInput);
                $processes = getArrayFromInput($processInput);
                $memoryOutput = firstFit($blocks, $processes);
                break;
            case 'fifoPaging':
                $pagingOutput = fifoPaging(getArrayFromInput($refInput), $frameSize);
                break;
            case 'lruPaging':
                $pagingOutput = lruPaging(getArrayFromInput($refInput), $frameSize);
                break;
            default:
                // Handle unknown action if necessary
                break;
        }
    } catch (Exception $e) {
        // Generic error message if something goes wrong during execution
        $error = "<p class='text-red-500'>Error: Invalid input or calculation issue. Please check your comma-separated values.</p>";
        // Assign error output to the relevant section based on the action
        if (in_array($action, ['bubbleSort', 'selectionSort', 'mergeSort'])) {
            $sortOutput = $error;
        } elseif (in_array($action, ['linearSearch', 'binarySearch', 'recursiveBinarySearch'])) {
            $searchOutput = $error;
        } elseif ($action == 'firstFit') {
            $memoryOutput = $error;
        } else {
            $pagingOutput = $error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OS and Data Algorithm Simulator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom Styles for Algorithm Blocks (Frames/Blocks) */
        .frame, .block {
            display: inline-flex;
            width: 40px;
            height: 40px;
            margin: 2px;
            border: 2px solid #333;
            text-align: center;
            justify-content: center;
            align-items: center;
            line-height: 1;
            font-weight: bold;
            border-radius: 6px;
            box-shadow: 1px 1px 3px rgba(0,0,0,0.1);
        }
        .used { background-color: #a7f3d0; color: #065f46; border-color: #34d399; } /* lightgreen for used frames */
        .free { background-color: #e5e7eb; color: #4b5563; border-color: #9ca3af; } /* lightgray for free/empty frames */
    </style>
</head>
<body class="bg-gray-50 min-h-screen p-4 md:p-8 font-['Inter']">

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow-2xl">
        <h1 class="text-4xl font-extrabold text-blue-700 mb-8 border-b pb-2">OS and Data Algorithm Simulator</h1>

        <div class="section mb-10 p-4 border rounded-lg bg-blue-50">
            <h2 class="text-2xl font-semibold text-blue-800 mb-4">Data Algorithms (Sorting and Searching)</h2>

            <h3 class="text-xl font-medium text-blue-600 border-b mb-3">Sorting Algorithms</h3>
            
            <form method="post">
                <div class="flex flex-col md:flex-row gap-4 mb-4 items-center">
                    <label for="sortInput" class="font-medium whitespace-nowrap">Enter Array (comma separated): </label>
                    <input type="text" id="sortInput" name="sortInput" value="<?= htmlspecialchars($sortInput) ?>" 
                            class="p-2 border rounded-lg flex-grow shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="flex gap-3 mb-4 flex-wrap">
                    <button type="submit" name="action" value="bubbleSort" 
                            class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition duration-150 shadow-md">Run Bubble Sort</button>
                    <button type="submit" name="action" value="selectionSort" 
                            class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition duration-150 shadow-md">Run Selection Sort</button>
                    <button type="submit" name="action" value="mergeSort" 
                            class="px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition duration-150 shadow-md">Run Merge Sort</button>
                </div>
            </form>
            
            <div id="sortOutput" class="mt-4 p-3 bg-white rounded-md border border-gray-200">
                <?= $sortOutput ?>
            </div>

            <h3 class="text-xl font-medium text-blue-600 border-b mt-6 mb-3">Searching Algorithms</h3>

            <form method="post">
                <div class="flex flex-col md:flex-row gap-4 mb-4 items-center">
                    <label for="searchInput" class="font-medium whitespace-nowrap">Enter Array (comma separated): </label>
                    <input type="text" id="searchInput" name="searchInput" value="<?= htmlspecialchars($searchInput) ?>" 
                            class="p-2 border rounded-lg flex-grow shadow-sm focus:ring-sky-500 focus:border-sky-500">
                    <label for="searchTarget" class="font-medium whitespace-nowrap">Target: </label>
                    <input type="number" id="searchTarget" name="searchTarget" value="<?= htmlspecialchars($searchTarget) ?>" 
                            class="p-2 border rounded-lg w-20 shadow-sm focus:ring-sky-500 focus:border-sky-500">
                </div>
                
                <div class="flex flex-wrap gap-3 mb-4">
                    <button type="submit" name="action" value="linearSearch" 
                            class="px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition duration-150 shadow-md">Linear Search</button>
                    <button type="submit" name="action" value="binarySearch" 
                            class="px-4 py-2 bg-cyan-500 text-white rounded-lg hover:bg-cyan-600 transition duration-150 shadow-md">Binary Search (Iterative)</button>
                    <button type="submit" name="action" value="recursiveBinarySearch" 
                            class="px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition duration-150 shadow-md">Jump Search</button>
                </div>
            </form>

            <div id="searchOutput" class="mt-4 p-3 bg-white rounded-md border border-gray-200">
                <?= $searchOutput ?>
            </div>
        </div>

        <div class="section p-4 border rounded-lg bg-green-50">
            <h2 class="text-2xl font-semibold text-green-800 mb-4">OS Algorithms (Memory Allocation and Paging)</h2>

            <h3 class="text-xl font-medium text-green-600 border-b mb-3">Memory Allocation (First Fit)</h3>
            
            <form method="post">
                <div class="flex flex-col md:flex-row gap-4 mb-4 items-center">
                    <label for="blockInput" class="font-medium whitespace-nowrap">Blocks (Sizes, KB): </label>
                    <input type="text" id="blockInput" name="blockInput" value="<?= htmlspecialchars($blockInput) ?>" 
                            class="p-2 border rounded-lg flex-grow shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="flex flex-col md:flex-row gap-4 mb-4 items-center">
                    <label for="processInput" class="font-medium whitespace-nowrap">Processes (Sizes, KB): </label>
                    <input type="text" id="processInput" name="processInput" value="<?= htmlspecialchars($processInput) ?>" 
                            class="p-2 border rounded-lg flex-grow shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <button type="submit" name="action" value="firstFit" 
                            class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition duration-150 shadow-md mb-4">Run First Fit</button>
            </form>

            <div id="memoryOutput" class="mt-4 p-3 bg-white rounded-md border border-gray-200">
                <?= $memoryOutput ?>
            </div>

            <h3 class="text-xl font-medium text-green-600 border-b mt-6 mb-3">Page Replacement (FIFO & LRU)</h3>
            
            <form method="post">
                <div class="flex flex-col md:flex-row gap-4 mb-4 items-center">
                    <label for="refInput" class="font-medium whitespace-nowrap">Reference String (comma separated): </label>
                    <input type="text" id="refInput" name="refInput" value="<?= htmlspecialchars($refInput) ?>" 
                            class="p-2 border rounded-lg flex-grow shadow-sm focus:ring-orange-500 focus:border-orange-500">
                    <label for="frameSize" class="font-medium whitespace-nowrap">Frames:</label>
                    <input type="number" id="frameSize" name="frameSize" value="<?= htmlspecialchars($frameSize) ?>" min="1" max="10" 
                            class="p-2 border rounded-lg w-20 shadow-sm focus:ring-orange-500 focus:border-orange-500">
                </div>
                
                <div class="flex gap-3 mb-4">
                    <button type="submit" name="action" value="fifoPaging" 
                            class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition duration-150 shadow-md">Run FIFO</button>
                    <button type="submit" name="action" value="lruPaging" 
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-150 shadow-md">Run LRU</button>
                </div>
            </form>

            <div id="pagingOutput" class="mt-4 p-3 bg-white rounded-md border border-gray-200">
                <?= $pagingOutput ?>
            </div>
        </div>
    </div>
</body>
</html>
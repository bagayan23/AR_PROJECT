<?php

// =================================================================================================
// 1. HELPER FUNCTIONS
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
// 2. DATA ALGORITHMS (Sorting and Searching)
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
    
    $output .= "<p>Target not found at block end. Beginning linear search from index $linearStart up to " . ($linearEnd - 1) . ".</p>";
    
    for ($i = $linearStart; $i < $linearEnd; $i++) {
        $output .= "<p class='ml-4'>Linear check index $i: value {$arr[$i]}</p>";
        if ($arr[$i] === $target) {
            $output .= "<p class='text-green-600 font-bold'>Found $target at index $i</p>";
            return $output;
        }
        // If the current element exceeds the target, we can stop the linear search (since array is sorted)
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
// 3. MEMORY ALGORITHMS (Allocation and Paging)
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
                    // Check last access time. Use -1 if not set for initial pages.
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
?>
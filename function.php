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

/**
 * Generates a unique ID for animation elements
 */
function generateAnimationId(): string {
    return 'anim_' . bin2hex(random_bytes(4));
}

/**
 * Creates input value cards display with color indicators
 * @param array $values - The values to display
 * @param string $cardType - The type of card (bubble, selection, merge, linear, binary, jump, block, process, fifo, lru)
 * @param string $title - The title for the cards section
 * @param array $extras - Extra information cards (e.g., target, frame count)
 * @param array $legendItems - Legend items with color indicators
 */
function createInputCards(array $values, string $cardType, string $title, array $extras = [], array $legendItems = []): string {
    $output = "<div class='input-cards-container'>";
    $output .= "<div class='input-cards-title' style='color: " . getCardTitleColor($cardType) . ";'>{$title}</div>";
    $output .= "<div class='input-cards'>";
    
    // Render main value cards
    foreach ($values as $i => $value) {
        $output .= "<div class='input-card {$cardType}-card'>";
        $output .= "<span class='card-value'>{$value}</span>";
        $output .= "<span class='card-index'>[{$i}]</span>";
        $output .= "</div>";
    }
    
    // Render extra cards (target, frames, etc.)
    foreach ($extras as $extra) {
        $extraType = $extra['type'] ?? 'target';
        $label = $extra['label'] ?? '';
        $value = $extra['value'] ?? '';
        $output .= "<div class='input-card {$extraType}-card'>";
        if ($label) {
            $output .= "<span class='card-label'>{$label}</span>";
        }
        $output .= "<span class='card-value'>{$value}</span>";
        $output .= "</div>";
    }
    
    $output .= "</div>";
    
    // Render legend if provided
    if (!empty($legendItems)) {
        $output .= "<div class='input-legend'>";
        foreach ($legendItems as $item) {
            $output .= "<div class='input-legend-item'>";
            $output .= "<span class='input-legend-color' style='background: {$item['color']};'></span>";
            $output .= "<span>{$item['label']}</span>";
            $output .= "</div>";
        }
        $output .= "</div>";
    }
    
    $output .= "</div>";
    return $output;
}

/**
 * Creates input cards for memory allocation (blocks and processes)
 */
function createMemoryInputCards(array $blocks, array $processes): string {
    $output = "<div class='input-cards-container'>";
    
    // Block Cards
    $output .= "<div class='input-cards-title' style='color: #065f46;'>Memory Blocks (KB)</div>";
    $output .= "<div class='input-cards'>";
    foreach ($blocks as $i => $size) {
        $output .= "<div class='input-card block-card'>";
        $output .= "<span class='card-label'>Block " . ($i + 1) . "</span>";
        $output .= "<span class='card-value'>{$size}</span>";
        $output .= "</div>";
    }
    $output .= "</div>";
    
    // Process Cards
    $output .= "<div class='input-cards-title mt-3' style='color: #1e40af;'>Process Sizes (KB)</div>";
    $output .= "<div class='input-cards'>";
    foreach ($processes as $i => $size) {
        $output .= "<div class='input-card process-card'>";
        $output .= "<span class='card-label'>P" . ($i + 1) . "</span>";
        $output .= "<span class='card-value'>{$size}</span>";
        $output .= "</div>";
    }
    $output .= "</div>";
    
    // Legend
    $output .= "<div class='input-legend'>";
    $output .= "<div class='input-legend-item'><span class='input-legend-color' style='background: linear-gradient(135deg, #10b981, #34d399);'></span><span>Memory Blocks</span></div>";
    $output .= "<div class='input-legend-item'><span class='input-legend-color' style='background: linear-gradient(135deg, #3b82f6, #60a5fa);'></span><span>Processes</span></div>";
    $output .= "</div>";
    
    $output .= "</div>";
    return $output;
}

/**
 * Creates input cards for paging algorithms
 */
function createPagingInputCards(array $refString, int $frameCount, string $algorithm): string {
    $cardType = strtolower($algorithm) === 'lru' ? 'lru' : 'fifo';
    $algorithmLabel = strtoupper($algorithm);
    
    $output = "<div class='input-cards-container'>";
    
    // Reference String Cards
    $output .= "<div class='input-cards-title' style='color: " . ($cardType === 'lru' ? '#991b1b' : '#9a3412') . ";'>Reference String ({$algorithmLabel})</div>";
    $output .= "<div class='input-cards'>";
    foreach ($refString as $i => $page) {
        $output .= "<div class='input-card {$cardType}-card'>";
        $output .= "<span class='card-value'>{$page}</span>";
        $output .= "<span class='card-index'>[{$i}]</span>";
        $output .= "</div>";
    }
    // Frame Count Card
    $output .= "<div class='input-card frame-card'>";
    $output .= "<span class='card-label'>Frames</span>";
    $output .= "<span class='card-value'>{$frameCount}</span>";
    $output .= "</div>";
    $output .= "</div>";
    
    // Legend
    $output .= "<div class='input-legend'>";
    $output .= "<div class='input-legend-item'><span class='input-legend-color' style='background: linear-gradient(135deg, " . ($cardType === 'lru' ? '#ef4444, #dc2626' : '#f97316, #fb923c') . ");'></span><span>Page References</span></div>";
    $output .= "<div class='input-legend-item'><span class='input-legend-color' style='background: linear-gradient(135deg, #f59e0b, #d97706);'></span><span>Frame Count</span></div>";
    $output .= "</div>";
    
    $output .= "</div>";
    return $output;
}

/**
 * Gets the title color based on card type
 */
function getCardTitleColor(string $cardType): string {
    $colors = [
        'bubble' => '#3730a3',
        'selection' => '#6b21a8',
        'merge' => '#9d174d',
        'linear' => '#0369a1',
        'binary' => '#0e7490',
        'jump' => '#115e59',
        'sort' => '#1e40af',
        'search' => '#0369a1',
        'block' => '#065f46',
        'process' => '#1e40af',
        'fifo' => '#9a3412',
        'lru' => '#991b1b',
    ];
    return $colors[$cardType] ?? '#374151';
}

/**
 * Creates a visual array bar representation with animation support
 */
function createVisualArrayBars(array $arr, array $highlights = [], string $animId = ''): string {
    $maxVal = max($arr) ?: 1;
    $output = "<div class='visual-array-container' id='{$animId}'>";
    $output .= "<div class='flex items-end justify-center gap-1 h-40 p-4 bg-gray-100 rounded-lg'>";
    
    foreach ($arr as $i => $value) {
        $height = ($value / $maxVal) * 100;
        $bgClass = 'bg-blue-500';
        $extraClass = '';
        
        if (in_array($i, $highlights)) {
            $bgClass = 'bg-yellow-400';
            $extraClass = 'animate-pulse';
        }
        
        $output .= "<div class='flex flex-col items-center'>";
        $output .= "<div class='array-bar {$bgClass} {$extraClass} rounded-t transition-all duration-300' 
                         style='width: 30px; height: {$height}%;'
                         data-value='{$value}' data-index='{$i}'></div>";
        $output .= "<span class='text-xs mt-1 font-mono'>{$value}</span>";
        $output .= "</div>";
    }
    
    $output .= "</div></div>";
    return $output;
}

/**
 * Creates step-by-step animation data for sorting algorithms
 */
function createSortingSteps(array $steps): string {
    $stepsJson = json_encode($steps);
    $animId = generateAnimationId();
    
    $output = "<div class='sorting-animation-container' id='{$animId}'>";
    $output .= "<div class='controls mb-4 flex gap-2 items-center'>";
    $output .= "<button onclick='playSortAnimation(\"{$animId}\")' class='px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path d='M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z'/></svg> Play</button>";
    $output .= "<button onclick='pauseSortAnimation(\"{$animId}\")' class='px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path d='M5.75 3a.75.75 0 00-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 00.75-.75V3.75A.75.75 0 007.25 3h-1.5zM12.75 3a.75.75 0 00-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 00.75-.75V3.75a.75.75 0 00-.75-.75h-1.5z'/></svg> Pause</button>";
    $output .= "<button onclick='resetSortAnimation(\"{$animId}\")' class='px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z' clip-rule='evenodd'/></svg> Reset</button>";
    $output .= "<div class='ml-4 flex items-center gap-2'><span class='text-xs text-gray-500'>Slow</span><input type='range' id='{$animId}_speed' min='100' max='2000' value='700' class='w-24 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-500' onchange='updateSpeed(\"{$animId}\", this.value)'><span class='text-xs text-gray-500'>Fast</span></div>";
    $output .= "<span class='ml-auto text-sm text-gray-500'>Step: <span id='{$animId}_step'>0</span>/<span id='{$animId}_total'>" . count($steps) . "</span></span>";
    $output .= "</div>";
    $output .= "<div class='visual-display'></div>";
    $output .= "<div class='step-description mt-2 p-2 bg-gray-50 rounded text-sm'></div>";
    $output .= "<script>(function(){ var fn = function(){ if(typeof initSortAnimation === 'function'){ initSortAnimation('{$animId}', {$stepsJson}); } else { setTimeout(fn, 50); } }; if(document.readyState === 'complete'){ fn(); } else { window.addEventListener('load', fn); } })();</script>";
    $output .= "</div>";
    
    return $output;
}

/**
 * Creates a process history section showing step-by-step value changes
 */
function createProcessHistory(array $steps, string $type = 'sort'): string {
    $historyId = 'history_' . bin2hex(random_bytes(4));
    
    $output = "<div class='process-history mt-6'>";
    $output .= "<div class='history-header flex items-center justify-between p-3 bg-gradient-to-r from-slate-100 to-slate-50 rounded-t-xl border border-slate-200 cursor-pointer hover:bg-slate-100 transition' onclick='toggleHistory(\"{$historyId}\")'>";
    $output .= "<h5 class='text-md font-bold flex items-center gap-2 text-slate-700'>";
    $output .= "<svg class='w-5 h-5 text-indigo-500' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'/></svg>";
    $output .= "Process History <span class='text-xs font-normal text-slate-400'>(" . count($steps) . " steps)</span></h5>";
    $output .= "<button id='{$historyId}_btn' class='flex items-center gap-1 text-sm px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-full transition font-medium'>";
    $output .= "<svg id='{$historyId}_icon' class='w-4 h-4 transition-transform duration-300' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/></svg>";
    $output .= "<span id='{$historyId}_text'>Show</span></button>";
    $output .= "</div>";
    $output .= "<div id='{$historyId}' class='history-content hidden bg-gradient-to-br from-slate-50 to-slate-100 rounded-b-xl border border-t-0 border-slate-200 shadow-sm max-h-96 overflow-y-auto'>";
    $output .= "<table class='w-full text-sm'>";
    $output .= "<thead class='bg-slate-200 sticky top-0'>";
    $output .= "<tr><th class='px-3 py-2 text-left font-semibold text-slate-600'>Step</th><th class='px-3 py-2 text-left font-semibold text-slate-600'>Array State</th><th class='px-3 py-2 text-left font-semibold text-slate-600'>Action</th></tr>";
    $output .= "</thead><tbody>";
    
    foreach ($steps as $i => $step) {
        $rowClass = $i % 2 === 0 ? 'bg-white' : 'bg-slate-50';
        $output .= "<tr class='{$rowClass} hover:bg-indigo-50 transition-colors'>";
        $output .= "<td class='px-3 py-2 font-mono text-indigo-600 font-bold'>" . ($i + 1) . "</td>";
        
        // Array state display
        $output .= "<td class='px-3 py-2'><div class='flex flex-wrap gap-1'>";
        if (isset($step['array'])) {
            foreach ($step['array'] as $idx => $val) {
                $cardClass = 'bg-slate-200 text-slate-700';
                if (isset($step['comparing']) && in_array($idx, $step['comparing'])) {
                    $cardClass = 'bg-amber-400 text-amber-900';
                } elseif (isset($step['swapped']) && in_array($idx, $step['swapped'])) {
                    $cardClass = 'bg-red-400 text-white';
                } elseif (isset($step['sorted']) && in_array($idx, $step['sorted'])) {
                    $cardClass = 'bg-emerald-400 text-white';
                } elseif (isset($step['currentIndex']) && $step['currentIndex'] === $idx) {
                    $cardClass = 'bg-amber-400 text-amber-900';
                } elseif (isset($step['found']) && $step['found'] && isset($step['currentIndex']) && $step['currentIndex'] === $idx) {
                    $cardClass = 'bg-emerald-400 text-white';
                } elseif (isset($step['checked']) && in_array($idx, $step['checked'])) {
                    $cardClass = 'bg-gray-400 text-white';
                } elseif (isset($step['mid']) && $step['mid'] === $idx) {
                    $cardClass = 'bg-amber-400 text-amber-900';
                } elseif (isset($step['eliminated']) && in_array($idx, $step['eliminated'])) {
                    $cardClass = 'bg-gray-300 text-gray-500';
                }
                $output .= "<span class='inline-flex items-center justify-center w-8 h-8 rounded {$cardClass} text-xs font-bold'>{$val}</span>";
            }
        } elseif (isset($step['frames'])) {
            // For paging algorithms
            $output .= "<span class='text-slate-600'>Frames: [" . implode(', ', array_map(fn($f) => $f ?? '-', $step['frames'])) . "]</span>";
        } elseif (isset($step['blocks'])) {
            // For memory algorithms
            $output .= "<span class='text-slate-600'>Blocks: [" . implode(', ', $step['blocks']) . "]</span>";
        }
        $output .= "</div></td>";
        
        // Description/Action
        $desc = $step['description'] ?? '';
        $statusIcon = '';
        if (strpos($desc, '✓') !== false || strpos($desc, 'Found') !== false || strpos($desc, 'Sorted') !== false) {
            $statusIcon = "<span class='inline-block w-2 h-2 bg-emerald-500 rounded-full mr-1'></span>";
        } elseif (strpos($desc, '✗') !== false || strpos($desc, 'not found') !== false) {
            $statusIcon = "<span class='inline-block w-2 h-2 bg-red-500 rounded-full mr-1'></span>";
        } elseif (strpos($desc, 'Swap') !== false) {
            $statusIcon = "<span class='inline-block w-2 h-2 bg-red-400 rounded-full mr-1'></span>";
        } elseif (strpos($desc, 'Compar') !== false || strpos($desc, 'Check') !== false) {
            $statusIcon = "<span class='inline-block w-2 h-2 bg-amber-400 rounded-full mr-1'></span>";
        }
        $output .= "<td class='px-3 py-2 text-slate-600'>{$statusIcon}{$desc}</td>";
        $output .= "</tr>";
    }
    
    $output .= "</tbody></table></div></div>";
    return $output;
}


// =================================================================================================
// 2. DATA ALGORITHMS (Sorting and Searching)
// =================================================================================================

/**
 * Simulates and generates step-by-step output for the Bubble Sort algorithm with visual animation.
 */
function bubbleSort(array $arr): string {
    $n = count($arr);
    $animId = generateAnimationId();
    $steps = [];
    
    // Initial state
    $steps[] = [
        'array' => $arr,
        'comparing' => [],
        'swapped' => [],
        'sorted' => [],
        'description' => 'Initial array: [' . implode(', ', $arr) . ']'
    ];
    
    $sortedIndices = [];
    
    for ($i = 0; $i < $n - 1; $i++) {
        for ($j = 0; $j < $n - $i - 1; $j++) {
            // Comparing step
            $steps[] = [
                'array' => $arr,
                'comparing' => [$j, $j + 1],
                'swapped' => [],
                'sorted' => $sortedIndices,
                'description' => "Comparing {$arr[$j]} and {$arr[$j+1]}"
            ];
            
            if ($arr[$j] > $arr[$j+1]) {
                // Swap
                [$arr[$j], $arr[$j+1]] = [$arr[$j+1], $arr[$j]];
                
                $steps[] = [
                    'array' => $arr,
                    'comparing' => [],
                    'swapped' => [$j, $j + 1],
                    'sorted' => $sortedIndices,
                    'description' => "Swapped! {$arr[$j+1]} > {$arr[$j]}"
                ];
            }
        }
        $sortedIndices[] = $n - $i - 1;
    }
    $sortedIndices[] = 0;
    
    // Final sorted state
    $steps[] = [
        'array' => $arr,
        'comparing' => [],
        'swapped' => [],
        'sorted' => range(0, $n - 1),
        'description' => '✓ Sorted! Final array: [' . implode(', ', $arr) . ']'
    ];
    
    $output = "<h4 class='text-lg font-bold mb-3 flex items-center gap-2'>";
    $output .= "<span class='w-3 h-3 bg-indigo-500 rounded-full'></span> Bubble Sort Visualization</h4>";
    
    $output .= createSortingSteps($steps);
    $output .= createProcessHistory($steps, 'sort');
    
    return $output;
}

/**
 * Simulates and generates step-by-step output for the Selection Sort algorithm with visual animation.
 */
function selectionSort(array $arr): string {
    $n = count($arr);
    $steps = [];
    
    // Initial state
    $steps[] = [
        'array' => $arr,
        'comparing' => [],
        'swapped' => [],
        'sorted' => [],
        'minIndex' => -1,
        'description' => 'Initial array: [' . implode(', ', $arr) . ']'
    ];
    
    $sortedIndices = [];
    
    for ($i = 0; $i < $n - 1; $i++) {
        $minIndex = $i;
        
        $steps[] = [
            'array' => $arr,
            'comparing' => [$i],
            'swapped' => [],
            'sorted' => $sortedIndices,
            'minIndex' => $minIndex,
            'description' => "Pass " . ($i + 1) . ": Starting from index $i, current minimum is {$arr[$minIndex]}"
        ];
        
        for ($j = $i + 1; $j < $n; $j++) {
            $steps[] = [
                'array' => $arr,
                'comparing' => [$j, $minIndex],
                'swapped' => [],
                'sorted' => $sortedIndices,
                'minIndex' => $minIndex,
                'description' => "Comparing {$arr[$j]} with current minimum {$arr[$minIndex]}"
            ];
            
            if ($arr[$j] < $arr[$minIndex]) {
                $minIndex = $j;
                $steps[] = [
                    'array' => $arr,
                    'comparing' => [],
                    'swapped' => [],
                    'sorted' => $sortedIndices,
                    'minIndex' => $minIndex,
                    'description' => "New minimum found: {$arr[$minIndex]} at index $minIndex"
                ];
            }
        }
        
        if ($minIndex !== $i) {
            [$arr[$i], $arr[$minIndex]] = [$arr[$minIndex], $arr[$i]];
            $steps[] = [
                'array' => $arr,
                'comparing' => [],
                'swapped' => [$i, $minIndex],
                'sorted' => $sortedIndices,
                'minIndex' => -1,
                'description' => "Swapped {$arr[$minIndex]} and {$arr[$i]}"
            ];
        }
        
        $sortedIndices[] = $i;
    }
    $sortedIndices[] = $n - 1;
    
    // Final sorted state
    $steps[] = [
        'array' => $arr,
        'comparing' => [],
        'swapped' => [],
        'sorted' => range(0, $n - 1),
        'minIndex' => -1,
        'description' => '✓ Sorted! Final array: [' . implode(', ', $arr) . ']'
    ];
    
    $output = "<h4 class='text-lg font-bold mb-3 flex items-center gap-2'>";
    $output .= "<span class='w-3 h-3 bg-purple-500 rounded-full'></span> Selection Sort Visualization</h4>";
    
    $output .= createSortingSteps($steps);
    $output .= createProcessHistory($steps, 'sort');
    
    return $output;
}

/**
 * Helper function to merge two sorted subarrays and log the step.
 */
function merge(array $left, array $right, &$steps, $depth = 0): array {
    $result = [];
    $i = 0;
    $j = 0;

    while ($i < count($left) && $j < count($right)) {
        if ($left[$i] <= $right[$j]) {
            $result[] = $left[$i];
            $i++;
        } else {
            $result[] = $right[$j];
            $j++;
        }
    }

    while ($i < count($left)) {
        $result[] = $left[$i];
        $i++;
    }
    while ($j < count($right)) {
        $result[] = $right[$j];
        $j++;
    }

    $steps[] = [
        'type' => 'merge',
        'left' => $left,
        'right' => $right,
        'result' => $result,
        'depth' => $depth,
        'description' => "Merge [" . implode(", ", $left) . "] + [" . implode(", ", $right) . "] → [" . implode(", ", $result) . "]"
    ];
    
    return $result;
}

/**
 * The core recursive function for Merge Sort with visual steps.
 */
function mergeSortCore(array $arr, &$steps, $depth = 0): array {
    $n = count($arr);
    if ($n <= 1) {
        return $arr;
    }

    $mid = (int) floor($n / 2);
    $left = array_slice($arr, 0, $mid);
    $right = array_slice($arr, $mid);

    $steps[] = [
        'type' => 'divide',
        'array' => $arr,
        'left' => $left,
        'right' => $right,
        'depth' => $depth,
        'description' => "Divide [" . implode(", ", $arr) . "] → Left: [" . implode(", ", $left) . "], Right: [" . implode(", ", $right) . "]"
    ];

    $left = mergeSortCore($left, $steps, $depth + 1);
    $right = mergeSortCore($right, $steps, $depth + 1);

    return merge($left, $right, $steps, $depth);
}

/**
 * Simulates and generates step-by-step output for the Merge Sort algorithm with visual animation.
 */
function mergeSort(array $arr): string {
    $steps = [];
    $animId = generateAnimationId();
    
    if (empty($arr)) {
        return "<p>Array is empty. Nothing to sort.</p>";
    }

    $steps[] = [
        'type' => 'initial',
        'array' => $arr,
        'description' => 'Initial array: [' . implode(', ', $arr) . ']'
    ];
    
    $sortedArr = mergeSortCore($arr, $steps);
    
    $steps[] = [
        'type' => 'final',
        'array' => $sortedArr,
        'description' => '✓ Sorted! Final array: [' . implode(', ', $sortedArr) . ']'
    ];
    
    $stepsJson = json_encode($steps);
    
    $output = "<h4 class='text-lg font-bold mb-3 flex items-center gap-2'>";
    $output .= "<span class='w-3 h-3 bg-pink-500 rounded-full'></span> Merge Sort Visualization</h4>";
    
    $output .= "<div class='merge-sort-container' id='{$animId}'>";
    $output .= "<div class='controls mb-4 flex gap-2 items-center'>";
    $output .= "<button onclick='playMergeAnimation(\"{$animId}\")' class='px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path d='M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z'/></svg> Play</button>";
    $output .= "<button onclick='pauseMergeAnimation(\"{$animId}\")' class='px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path d='M5.75 3a.75.75 0 00-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 00.75-.75V3.75A.75.75 0 007.25 3h-1.5zM12.75 3a.75.75 0 00-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 00.75-.75V3.75a.75.75 0 00-.75-.75h-1.5z'/></svg> Pause</button>";
    $output .= "<button onclick='resetMergeAnimation(\"{$animId}\")' class='px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z' clip-rule='evenodd'/></svg> Reset</button>";
    $output .= "<div class='ml-4 flex items-center gap-2'><span class='text-xs text-gray-500'>Slow</span><input type='range' id='{$animId}_speed' min='100' max='2000' value='700' class='w-24 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-pink-500' onchange='updateMergeSpeed(\"{$animId}\", this.value)'><span class='text-xs text-gray-500'>Fast</span></div>";
    $output .= "</div>";
    $output .= "<div class='merge-visual-display p-4 bg-gray-50 rounded-lg min-h-[200px]'></div>";
    $output .= "<div class='step-description mt-2 p-2 bg-white border rounded text-sm font-mono'></div>";
    $output .= "<script>(function(){ var fn = function(){ if(typeof initMergeAnimation === 'function'){ initMergeAnimation('{$animId}', {$stepsJson}); } else { setTimeout(fn, 50); } }; if(document.readyState === 'complete'){ fn(); } else { window.addEventListener('load', fn); } })();</script>";
    $output .= "</div>";
    
    $output .= createProcessHistory($steps, 'merge');
    
    return $output;
}


/**
 * Simulates and generates step-by-step output for the Linear Search algorithm with visual animation.
 */
function linearSearch(array $arr, int $target): string {
    $animId = generateAnimationId();
    $steps = [];
    
    $steps[] = [
        'array' => $arr,
        'currentIndex' => -1,
        'found' => false,
        'checked' => [],
        'description' => "Searching for target: {$target} in array [" . implode(", ", $arr) . "]"
    ];
    
    $found = false;
    $foundIndex = -1;
    $checked = [];
    
    foreach ($arr as $i => $value) {
        $checked[] = $i;
        
        if ($value === $target) {
            $found = true;
            $foundIndex = $i;
            $steps[] = [
                'array' => $arr,
                'currentIndex' => $i,
                'found' => true,
                'checked' => $checked,
                'description' => "✓ Found {$target} at index {$i}!"
            ];
            break;
        } else {
            $steps[] = [
                'array' => $arr,
                'currentIndex' => $i,
                'found' => false,
                'checked' => $checked,
                'description' => "Checking index {$i}: {$value} ≠ {$target}"
            ];
        }
    }
    
    if (!$found) {
        $steps[] = [
            'array' => $arr,
            'currentIndex' => -1,
            'found' => false,
            'checked' => $checked,
            'description' => "✗ Target {$target} not found in the array"
        ];
    }
    
    $stepsJson = json_encode($steps);
    
    $output = "<h4 class='text-lg font-bold mb-3 flex items-center gap-2'>";
    $output .= "<span class='w-3 h-3 bg-sky-500 rounded-full'></span> Linear Search Visualization</h4>";
    
    $output .= "<div class='search-animation-container' id='{$animId}'>";
    $output .= "<div class='controls mb-4 flex gap-2 items-center'>";
    $output .= "<button onclick='playSearchAnimation(\"{$animId}\")' class='px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path d='M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z'/></svg> Play</button>";
    $output .= "<button onclick='pauseSearchAnimation(\"{$animId}\")' class='px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path d='M5.75 3a.75.75 0 00-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 00.75-.75V3.75A.75.75 0 007.25 3h-1.5zM12.75 3a.75.75 0 00-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 00.75-.75V3.75a.75.75 0 00-.75-.75h-1.5z'/></svg> Pause</button>";
    $output .= "<button onclick='resetSearchAnimation(\"{$animId}\")' class='px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z' clip-rule='evenodd'/></svg> Reset</button>";
    $output .= "<div class='ml-4 flex items-center gap-2'><span class='text-xs text-gray-500'>Slow</span><input type='range' id='{$animId}_speed' min='100' max='2000' value='700' class='w-24 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-sky-500' onchange='updateSearchSpeed(\"{$animId}\", this.value)'><span class='text-xs text-gray-500'>Fast</span></div>";
    $output .= "<span class='ml-4 px-2 py-1 bg-red-100 text-red-800 rounded'>Target: <strong>{$target}</strong></span>";
    $output .= "</div>";
    $output .= "<div class='search-visual-display p-4 bg-gray-50 rounded-lg'></div>";
    $output .= "<div class='step-description mt-2 p-2 bg-white border rounded text-sm font-mono'></div>";
    $output .= "<script>(function(){ var fn = function(){ if(typeof initSearchAnimation === 'function'){ initSearchAnimation('{$animId}', {$stepsJson}, 'linear'); } else { setTimeout(fn, 50); } }; if(document.readyState === 'complete'){ fn(); } else { window.addEventListener('load', fn); } })();</script>";
    $output .= "</div>";
    
    $output .= createProcessHistory($steps, 'search');
    
    return $output;
}

/**
 * Simulates and generates step-by-step output for the Jump Search algorithm with visual animation.
 */
function jumpSearch(array $arr, int $target): string {
    sort($arr);
    $n = count($arr);
    $animId = generateAnimationId();
    $steps = [];
    
    if ($n === 0) {
        return "<p class='text-red-600 font-bold'>Target $target not found (Array is empty).</p>";
    }

    $blockSize = (int) floor(sqrt($n));
    
    $steps[] = [
        'array' => $arr,
        'blockSize' => $blockSize,
        'currentBlock' => [],
        'linearSearch' => [],
        'found' => false,
        'description' => "Sorted array: [" . implode(", ", $arr) . "]. Block size: √{$n} = {$blockSize}"
    ];
    
    $prev = 0;
    $step = $blockSize;
    
    // Jump phase
    while ($prev < $n && $arr[min($step, $n) - 1] < $target) {
        $blockEnd = min($step, $n) - 1;
        $steps[] = [
            'array' => $arr,
            'blockSize' => $blockSize,
            'currentBlock' => range($prev, $blockEnd),
            'linearSearch' => [],
            'found' => false,
            'description' => "Jump: Block [{$prev}..{$blockEnd}], end value {$arr[$blockEnd]} < {$target}. Jump ahead."
        ];
        
        $prev = $step;
        $step += $blockSize;
        
        if ($prev >= $n) {
            $steps[] = [
                'array' => $arr,
                'blockSize' => $blockSize,
                'currentBlock' => [],
                'linearSearch' => [],
                'found' => false,
                'description' => "✗ Jumped past array end. Target {$target} not found."
            ];
            break;
        }
    }
    
    if ($prev < $n) {
        // Linear search phase
        $linearEnd = min($step, $n);
        $linearChecked = [];
        
        for ($i = $prev; $i < $linearEnd; $i++) {
            $linearChecked[] = $i;
            
            if ($arr[$i] === $target) {
                $steps[] = [
                    'array' => $arr,
                    'blockSize' => $blockSize,
                    'currentBlock' => range($prev, $linearEnd - 1),
                    'linearSearch' => $linearChecked,
                    'found' => true,
                    'foundIndex' => $i,
                    'description' => "✓ Found {$target} at index {$i}!"
                ];
                break;
            } else {
                $steps[] = [
                    'array' => $arr,
                    'blockSize' => $blockSize,
                    'currentBlock' => range($prev, $linearEnd - 1),
                    'linearSearch' => $linearChecked,
                    'found' => false,
                    'description' => "Linear search: index {$i}, value {$arr[$i]} ≠ {$target}"
                ];
                
                if ($arr[$i] > $target) {
                    $steps[] = [
                        'array' => $arr,
                        'blockSize' => $blockSize,
                        'currentBlock' => [],
                        'linearSearch' => $linearChecked,
                        'found' => false,
                        'description' => "✗ Value {$arr[$i]} > {$target}. Target not found."
                    ];
                    break;
                }
            }
        }
    }
    
    $stepsJson = json_encode($steps);
    
    $output = "<h4 class='text-lg font-bold mb-3 flex items-center gap-2'>";
    $output .= "<span class='w-3 h-3 bg-teal-500 rounded-full'></span> Jump Search Visualization</h4>";
    
    $output .= "<div class='search-animation-container' id='{$animId}'>";
    $output .= "<div class='controls mb-4 flex gap-2 items-center'>";
    $output .= "<button onclick='playSearchAnimation(\"{$animId}\")' class='px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path d='M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z'/></svg> Play</button>";
    $output .= "<button onclick='pauseSearchAnimation(\"{$animId}\")' class='px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path d='M5.75 3a.75.75 0 00-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 00.75-.75V3.75A.75.75 0 007.25 3h-1.5zM12.75 3a.75.75 0 00-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 00.75-.75V3.75a.75.75 0 00-.75-.75h-1.5z'/></svg> Pause</button>";
    $output .= "<button onclick='resetSearchAnimation(\"{$animId}\")' class='px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z' clip-rule='evenodd'/></svg> Reset</button>";
    $output .= "<div class='ml-4 flex items-center gap-2'><span class='text-xs text-gray-500'>Slow</span><input type='range' id='{$animId}_speed' min='100' max='2000' value='700' class='w-24 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-teal-500' onchange='updateSearchSpeed(\"{$animId}\", this.value)'><span class='text-xs text-gray-500'>Fast</span></div>";
    $output .= "<span class='ml-4 px-2 py-1 bg-red-100 text-red-800 rounded'>Target: <strong>{$target}</strong></span>";
    $output .= "</div>";
    $output .= "<div class='search-visual-display p-4 bg-gray-50 rounded-lg'></div>";
    $output .= "<div class='step-description mt-2 p-2 bg-white border rounded text-sm font-mono'></div>";
    $output .= "<script>(function(){ var fn = function(){ if(typeof initSearchAnimation === 'function'){ initSearchAnimation('{$animId}', {$stepsJson}, 'jump'); } else { setTimeout(fn, 50); } }; if(document.readyState === 'complete'){ fn(); } else { window.addEventListener('load', fn); } })();</script>";
    $output .= "</div>";
    
    $output .= createProcessHistory($steps, 'search');
    
    return $output;
}

/**
 * Simulates and generates step-by-step output for the Iterative Binary Search algorithm with visual animation.
 */
function binarySearch(array $arr, int $target): string {
    sort($arr);
    $animId = generateAnimationId();
    $steps = [];
    
    $steps[] = [
        'array' => $arr,
        'low' => 0,
        'high' => count($arr) - 1,
        'mid' => -1,
        'found' => false,
        'eliminated' => [],
        'description' => "Sorted array: [" . implode(", ", $arr) . "]. Searching for: {$target}"
    ];

    $low = 0;
    $high = count($arr) - 1;
    $found = false;
    $eliminated = [];

    while ($low <= $high) {
        $mid = (int) floor(($low + $high) / 2);
        $midValue = $arr[$mid] ?? null;

        if ($midValue === null) break;

        $steps[] = [
            'array' => $arr,
            'low' => $low,
            'high' => $high,
            'mid' => $mid,
            'found' => false,
            'eliminated' => $eliminated,
            'description' => "Range [{$low}..{$high}], mid = {$mid}, value = {$midValue}"
        ];

        if ($midValue === $target) {
            $steps[] = [
                'array' => $arr,
                'low' => $low,
                'high' => $high,
                'mid' => $mid,
                'found' => true,
                'eliminated' => $eliminated,
                'description' => "✓ Found {$target} at index {$mid}!"
            ];
            $found = true;
            break;
        } elseif ($midValue < $target) {
            // Eliminate left half
            for ($i = $low; $i <= $mid; $i++) {
                $eliminated[] = $i;
            }
            $low = $mid + 1;
            $steps[] = [
                'array' => $arr,
                'low' => $low,
                'high' => $high,
                'mid' => -1,
                'found' => false,
                'eliminated' => $eliminated,
                'description' => "{$midValue} < {$target}, search right half [{$low}..{$high}]"
            ];
        } else {
            // Eliminate right half
            for ($i = $mid; $i <= $high; $i++) {
                $eliminated[] = $i;
            }
            $high = $mid - 1;
            $steps[] = [
                'array' => $arr,
                'low' => $low,
                'high' => $high,
                'mid' => -1,
                'found' => false,
                'eliminated' => $eliminated,
                'description' => "{$midValue} > {$target}, search left half [{$low}..{$high}]"
            ];
        }
    }

    if (!$found) {
        $steps[] = [
            'array' => $arr,
            'low' => $low,
            'high' => $high,
            'mid' => -1,
            'found' => false,
            'eliminated' => $eliminated,
            'description' => "✗ Target {$target} not found in the array"
        ];
    }
    
    $stepsJson = json_encode($steps);
    
    $output = "<h4 class='text-lg font-bold mb-3 flex items-center gap-2'>";
    $output .= "<span class='w-3 h-3 bg-cyan-500 rounded-full'></span> Binary Search Visualization</h4>";
    
    $output .= "<div class='search-animation-container' id='{$animId}'>";
    $output .= "<div class='controls mb-4 flex gap-2 items-center'>";
    $output .= "<button onclick='playSearchAnimation(\"{$animId}\")' class='px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path d='M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z'/></svg> Play</button>";
    $output .= "<button onclick='pauseSearchAnimation(\"{$animId}\")' class='px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path d='M5.75 3a.75.75 0 00-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 00.75-.75V3.75A.75.75 0 007.25 3h-1.5zM12.75 3a.75.75 0 00-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 00.75-.75V3.75a.75.75 0 00-.75-.75h-1.5z'/></svg> Pause</button>";
    $output .= "<button onclick='resetSearchAnimation(\"{$animId}\")' class='px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z' clip-rule='evenodd'/></svg> Reset</button>";
    $output .= "<div class='ml-4 flex items-center gap-2'><span class='text-xs text-gray-500'>Slow</span><input type='range' id='{$animId}_speed' min='100' max='2000' value='700' class='w-24 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-cyan-500' onchange='updateSearchSpeed(\"{$animId}\", this.value)'><span class='text-xs text-gray-500'>Fast</span></div>";
    $output .= "<span class='ml-4 px-2 py-1 bg-red-100 text-red-800 rounded'>Target: <strong>{$target}</strong></span>";
    $output .= "</div>";
    $output .= "<div class='search-visual-display p-4 bg-gray-50 rounded-lg'></div>";
    $output .= "<div class='step-description mt-2 p-2 bg-white border rounded text-sm font-mono'></div>";
    $output .= "<script>(function(){ var fn = function(){ if(typeof initSearchAnimation === 'function'){ initSearchAnimation('{$animId}', {$stepsJson}, 'binary'); } else { setTimeout(fn, 50); } }; if(document.readyState === 'complete'){ fn(); } else { window.addEventListener('load', fn); } })();</script>";
    $output .= "</div>";
    
    $output .= createProcessHistory($steps, 'search');
    
    return $output;
}


// =================================================================================================
// 3. MEMORY ALGORITHMS (Allocation and Paging)
// =================================================================================================

/**
 * Simulates the First Fit memory allocation algorithm with visual representation.
 */
function firstFit(array $blocks, array $processes): string {
    $originalBlocks = $blocks;
    $n = count($processes);
    $m = count($blocks);
    $allocation = array_fill(0, $n, -1);
    $animId = generateAnimationId();
    $steps = [];
    
    // Initial state
    $steps[] = [
        'blocks' => $blocks,
        'originalBlocks' => $originalBlocks,
        'processes' => $processes,
        'allocation' => $allocation,
        'currentProcess' => -1,
        'currentBlock' => -1,
        'description' => "Memory Blocks: [" . implode("KB, ", $originalBlocks) . "KB]. Processes: [" . implode("KB, ", $processes) . "KB]"
    ];

    foreach ($processes as $i => $p) {
        $allocated = false;
        
        for ($j = 0; $j < $m; $j++) {
            $steps[] = [
                'blocks' => $blocks,
                'originalBlocks' => $originalBlocks,
                'processes' => $processes,
                'allocation' => $allocation,
                'currentProcess' => $i,
                'currentBlock' => $j,
                'description' => "Trying to fit Process {$p}KB into Block " . ($j + 1) . " ({$blocks[$j]}KB remaining)"
            ];
            
            if ($blocks[$j] >= $p) {
                $allocation[$i] = $j;
                $blocks[$j] -= $p;
                $allocated = true;
                
                $steps[] = [
                    'blocks' => $blocks,
                    'originalBlocks' => $originalBlocks,
                    'processes' => $processes,
                    'allocation' => $allocation,
                    'currentProcess' => $i,
                    'currentBlock' => $j,
                    'allocated' => true,
                    'description' => "✓ Process {$p}KB allocated to Block " . ($j + 1) . ". Remaining: {$blocks[$j]}KB"
                ];
                break;
            }
        }
        
        if (!$allocated) {
            $steps[] = [
                'blocks' => $blocks,
                'originalBlocks' => $originalBlocks,
                'processes' => $processes,
                'allocation' => $allocation,
                'currentProcess' => $i,
                'currentBlock' => -1,
                'allocated' => false,
                'description' => "✗ Process {$p}KB could not be allocated (no suitable block)"
            ];
        }
    }
    
    $stepsJson = json_encode($steps);
    
    $output = "<h4 class='text-lg font-bold mb-3 flex items-center gap-2'>";
    $output .= "<span class='w-3 h-3 bg-emerald-500 rounded-full'></span> First Fit Memory Allocation</h4>";
    
    $output .= "<div class='memory-animation-container' id='{$animId}'>";
    $output .= "<div class='controls mb-4 flex gap-2 items-center'>";
    $output .= "<button onclick='playMemoryAnimation(\"{$animId}\")' class='px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path d='M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z'/></svg> Play</button>";
    $output .= "<button onclick='pauseMemoryAnimation(\"{$animId}\")' class='px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path d='M5.75 3a.75.75 0 00-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 00.75-.75V3.75A.75.75 0 007.25 3h-1.5zM12.75 3a.75.75 0 00-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 00.75-.75V3.75a.75.75 0 00-.75-.75h-1.5z'/></svg> Pause</button>";
    $output .= "<button onclick='resetMemoryAnimation(\"{$animId}\")' class='px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z' clip-rule='evenodd'/></svg> Reset</button>";
    $output .= "<div class='ml-4 flex items-center gap-2'><span class='text-xs text-gray-500'>Slow</span><input type='range' id='{$animId}_speed' min='100' max='2000' value='700' class='w-24 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-emerald-500' onchange='updateMemorySpeed(\"{$animId}\", this.value)'><span class='text-xs text-gray-500'>Fast</span></div>";
    $output .= "</div>";
    $output .= "<div class='memory-visual-display p-4 bg-gray-50 rounded-lg'></div>";
    $output .= "<div class='step-description mt-2 p-2 bg-white border rounded text-sm font-mono'></div>";
    $output .= "<script>(function(){ var fn = function(){ if(typeof initMemoryAnimation === 'function'){ initMemoryAnimation('{$animId}', {$stepsJson}); } else { setTimeout(fn, 50); } }; if(document.readyState === 'complete'){ fn(); } else { window.addEventListener('load', fn); } })();</script>";
    $output .= "</div>";
    
    $output .= createProcessHistory($steps, 'memory');
    
    return $output;
}

/**
 * Simulates the First-In, First-Out (FIFO) Page Replacement algorithm with visual animation.
 */
function fifoPaging(array $refString, int $frameSize): string {
    $frames = [];
    $pageFaults = 0;
    $animId = generateAnimationId();
    $steps = [];
    
    $steps[] = [
        'frames' => $frames,
        'frameSize' => $frameSize,
        'currentPage' => -1,
        'isHit' => false,
        'replaced' => null,
        'pageFaults' => 0,
        'refString' => $refString,
        'currentStep' => -1,
        'description' => "FIFO Paging with {$frameSize} frames. Reference string: [" . implode(", ", $refString) . "]"
    ];

    foreach ($refString as $step => $page) {
        $isHit = in_array($page, $frames);
        $replaced = null;

        if (!$isHit) {
            $pageFaults++;
            if (count($frames) < $frameSize) {
                $frames[] = $page;
            } else {
                $replaced = array_shift($frames);
                $frames[] = $page;
            }
        }

        $steps[] = [
            'frames' => $frames,
            'frameSize' => $frameSize,
            'currentPage' => $page,
            'isHit' => $isHit,
            'replaced' => $replaced,
            'pageFaults' => $pageFaults,
            'refString' => $refString,
            'currentStep' => $step,
            'description' => $isHit 
                ? "Page {$page}: HIT! Page already in memory." 
                : ($replaced !== null 
                    ? "Page {$page}: FAULT! Replaced page {$replaced} (FIFO oldest)." 
                    : "Page {$page}: FAULT! Added to empty frame.")
        ];
    }

    $stepsJson = json_encode($steps);
    
    $output = "<h4 class='text-lg font-bold mb-3 flex items-center gap-2'>";
    $output .= "<span class='w-3 h-3 bg-orange-500 rounded-full'></span> FIFO Page Replacement</h4>";
    
    $output .= "<div class='paging-animation-container' id='{$animId}'>";
    $output .= "<div class='controls mb-4 flex gap-2 items-center'>";
    $output .= "<button onclick='playPagingAnimation(\"{$animId}\")' class='px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path d='M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z'/></svg> Play</button>";
    $output .= "<button onclick='pausePagingAnimation(\"{$animId}\")' class='px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path d='M5.75 3a.75.75 0 00-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 00.75-.75V3.75A.75.75 0 007.25 3h-1.5zM12.75 3a.75.75 0 00-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 00.75-.75V3.75a.75.75 0 00-.75-.75h-1.5z'/></svg> Pause</button>";
    $output .= "<button onclick='resetPagingAnimation(\"{$animId}\")' class='px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z' clip-rule='evenodd'/></svg> Reset</button>";
    $output .= "<div class='ml-4 flex items-center gap-2'><span class='text-xs text-gray-500'>Slow</span><input type='range' id='{$animId}_speed' min='100' max='2000' value='700' class='w-24 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-orange-500' onchange='updatePagingSpeed(\"{$animId}\", this.value)'><span class='text-xs text-gray-500'>Fast</span></div>";
    $output .= "</div>";
    $output .= "<div class='paging-visual-display p-4 bg-gray-50 rounded-lg'></div>";
    $output .= "<div class='step-description mt-2 p-2 bg-white border rounded text-sm font-mono'></div>";
    $output .= "<script>(function(){ var fn = function(){ if(typeof initPagingAnimation === 'function'){ initPagingAnimation('{$animId}', {$stepsJson}, 'fifo'); } else { setTimeout(fn, 50); } }; if(document.readyState === 'complete'){ fn(); } else { window.addEventListener('load', fn); } })();</script>";
    $output .= "</div>";
    
    $output .= createProcessHistory($steps, 'paging');
    
    return $output;
}

/**
 * Simulates the Least Recently Used (LRU) Page Replacement algorithm with visual animation.
 */
function lruPaging(array $refString, int $frameSize): string {
    $frames = [];
    $recent = [];
    $pageFaults = 0;
    $animId = generateAnimationId();
    $steps = [];
    
    $steps[] = [
        'frames' => $frames,
        'frameSize' => $frameSize,
        'currentPage' => -1,
        'isHit' => false,
        'replaced' => null,
        'pageFaults' => 0,
        'refString' => $refString,
        'currentStep' => -1,
        'recent' => [],
        'description' => "LRU Paging with {$frameSize} frames. Reference string: [" . implode(", ", $refString) . "]"
    ];

    foreach ($refString as $step => $page) {
        $isHit = in_array($page, $frames);
        $replaced = null;

        if (!$isHit) {
            $pageFaults++;
            if (count($frames) < $frameSize) {
                $frames[] = $page;
            } else {
                $lruPage = null;
                $minTime = PHP_INT_MAX;

                foreach ($frames as $f) {
                    if (!isset($recent[$f]) || $recent[$f] < $minTime) {
                        $minTime = $recent[$f] ?? -1;
                        $lruPage = $f;
                    }
                }

                $index = array_search($lruPage, $frames);
                if ($index !== false) {
                    $replaced = $lruPage;
                    $frames[$index] = $page;
                }
            }
        }

        $recent[$page] = $step;

        $steps[] = [
            'frames' => array_values($frames),
            'frameSize' => $frameSize,
            'currentPage' => $page,
            'isHit' => $isHit,
            'replaced' => $replaced,
            'pageFaults' => $pageFaults,
            'refString' => $refString,
            'currentStep' => $step,
            'recent' => $recent,
            'description' => $isHit 
                ? "Page {$page}: HIT! Page already in memory (updated access time)." 
                : ($replaced !== null 
                    ? "Page {$page}: FAULT! Replaced page {$replaced} (Least Recently Used)." 
                    : "Page {$page}: FAULT! Added to empty frame.")
        ];
    }

    $stepsJson = json_encode($steps);
    
    $output = "<h4 class='text-lg font-bold mb-3 flex items-center gap-2'>";
    $output .= "<span class='w-3 h-3 bg-red-500 rounded-full'></span> LRU Page Replacement</h4>";
    
    $output .= "<div class='paging-animation-container' id='{$animId}'>";
    $output .= "<div class='controls mb-4 flex gap-2 items-center'>";
    $output .= "<button onclick='playPagingAnimation(\"{$animId}\")' class='px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path d='M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z'/></svg> Play</button>";
    $output .= "<button onclick='pausePagingAnimation(\"{$animId}\")' class='px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path d='M5.75 3a.75.75 0 00-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 00.75-.75V3.75A.75.75 0 007.25 3h-1.5zM12.75 3a.75.75 0 00-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 00.75-.75V3.75a.75.75 0 00-.75-.75h-1.5z'/></svg> Pause</button>";
    $output .= "<button onclick='resetPagingAnimation(\"{$animId}\")' class='px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition'><svg class='w-4 h-4 inline' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z' clip-rule='evenodd'/></svg> Reset</button>";
    $output .= "<div class='ml-4 flex items-center gap-2'><span class='text-xs text-gray-500'>Slow</span><input type='range' id='{$animId}_speed' min='100' max='2000' value='700' class='w-24 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-red-500' onchange='updatePagingSpeed(\"{$animId}\", this.value)'><span class='text-xs text-gray-500'>Fast</span></div>";
    $output .= "</div>";
    $output .= "<div class='paging-visual-display p-4 bg-gray-50 rounded-lg'></div>";
    $output .= "<div class='step-description mt-2 p-2 bg-white border rounded text-sm font-mono'></div>";
    $output .= "<script>(function(){ var fn = function(){ if(typeof initPagingAnimation === 'function'){ initPagingAnimation('{$animId}', {$stepsJson}, 'lru'); } else { setTimeout(fn, 50); } }; if(document.readyState === 'complete'){ fn(); } else { window.addEventListener('load', fn); } })();</script>";
    $output .= "</div>";
    
    $output .= createProcessHistory($steps, 'paging');
    
    return $output;
}
?>
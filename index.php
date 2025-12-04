<?php
// Include the core logic file which handles initialization, input persistence, and execution
require_once 'logic.php';
// Note: $sortOutput, $searchOutput, $memoryOutput, $pagingOutput, 
// and the input variables are now available here via logic.php

// Determine which tab was active or default to 'sort'
$activeTab = $_POST['activeTab'] ?? 'sort';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OS and Data Algorithm Simulator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/animations.js"></script>
</head>
<body class="min-h-screen p-4 md:p-8 font-['Inter']">

    <div class="max-w-5xl mx-auto">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <h1 class="text-4xl md:text-5xl font-extrabold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">OS and Data Algorithm Simulator</h1>
            <p class="text-gray-500 text-lg">Visualize sorting, searching, memory allocation & paging algorithms</p>
        </div>

        <div class="backdrop-blur-sm bg-white/80 p-6 md:p-8 rounded-2xl shadow-xl border border-white/20">

        <nav class="mb-8">
            <ul class="flex flex-wrap gap-2 p-1 bg-gray-100/80 rounded-xl">
                <li>
                    <button type="button" data-target="sort-section" class="tab-button px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 hover:bg-white/60">Sorting</button>
                </li>
                <li>
                    <button type="button" data-target="search-section" class="tab-button px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 hover:bg-white/60">Searching</button>
                </li>
                <li>
                    <button type="button" data-target="memory-section" class="tab-button px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 hover:bg-white/60">Memory Allocation</button>
                </li>
                <li>
                    <button type="button" data-target="paging-section" class="tab-button px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 hover:bg-white/60">Paging</button>
                </li>
            </ul>
        </nav>

        <input type="hidden" name="activeTab" id="activeTab" value="<?= htmlspecialchars($activeTab) ?>">

        <div id="sort-section" class="section-content section mb-8">
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-6 rounded-xl border border-indigo-100 shadow-sm">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-5 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
                    Sorting Algorithms
                </h2>
            
            <form method="post">
                <input type="hidden" name="activeTab" value="sort">
                <div class="flex flex-col md:flex-row gap-4 mb-5 items-center">
                    <label for="sortInput" class="font-medium text-gray-700 whitespace-nowrap">Enter Array: </label>
                    <input type="text" id="sortInput" name="sortInput" value="<?= htmlspecialchars($sortInput) ?>" 
                            placeholder="e.g., 64, 34, 25, 12, 22, 11, 90"
                            class="p-3 border-0 rounded-xl flex-grow shadow-sm bg-white/70 backdrop-blur focus:ring-2 focus:ring-indigo-400 focus:outline-none transition-all">
                </div>

                <div class="flex gap-3 mb-5 flex-wrap">
                    <button type="submit" name="action" value="bubbleSort" 
                            class="px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl hover:from-indigo-600 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg font-medium">Bubble Sort</button>
                    <button type="submit" name="action" value="selectionSort" 
                            class="px-5 py-2.5 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl hover:from-purple-600 hover:to-purple-700 transition-all duration-200 shadow-md hover:shadow-lg font-medium">Selection Sort</button>
                    <button type="submit" name="action" value="mergeSort" 
                            class="px-5 py-2.5 bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-xl hover:from-pink-600 hover:to-pink-700 transition-all duration-200 shadow-md hover:shadow-lg font-medium">Merge Sort</button>
                </div>
            </form>
            
            <div id="sortOutput" class="mt-4 p-4 bg-white/60 backdrop-blur rounded-xl border border-gray-100">
                <?= $sortOutput ?>
            </div>
            </div>
        </div>

        <div id="search-section" class="section-content section mb-8">
            <div class="bg-gradient-to-br from-sky-50 to-cyan-50 p-6 rounded-xl border border-sky-100 shadow-sm">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-sky-600 to-cyan-600 bg-clip-text text-transparent mb-5 flex items-center gap-2">
                    <svg class="w-6 h-6 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Searching Algorithms
                </h2>

            <form method="post">
                <input type="hidden" name="activeTab" value="search">
                <div class="flex flex-col md:flex-row gap-4 mb-5 items-center">
                    <label for="searchInput" class="font-medium text-gray-700 whitespace-nowrap">Enter Array: </label>
                    <input type="text" id="searchInput" name="searchInput" value="<?= htmlspecialchars($searchInput) ?>" 
                            placeholder="e.g., 2, 3, 4, 10, 40"
                            class="p-3 border-0 rounded-xl flex-grow shadow-sm bg-white/70 backdrop-blur focus:ring-2 focus:ring-sky-400 focus:outline-none transition-all">
                    <label for="searchTarget" class="font-medium text-gray-700 whitespace-nowrap">Target: </label>
                    <input type="number" id="searchTarget" name="searchTarget" value="<?= htmlspecialchars($searchTarget) ?>" 
                            class="p-3 border-0 rounded-xl w-24 shadow-sm bg-white/70 backdrop-blur focus:ring-2 focus:ring-sky-400 focus:outline-none transition-all">
                </div>
                
                <div class="flex flex-wrap gap-3 mb-5">
                    <button type="submit" name="action" value="linearSearch" 
                            class="px-5 py-2.5 bg-gradient-to-r from-sky-500 to-sky-600 text-white rounded-xl hover:from-sky-600 hover:to-sky-700 transition-all duration-200 shadow-md hover:shadow-lg font-medium">Linear Search</button>
                    <button type="submit" name="action" value="binarySearch" 
                            class="px-5 py-2.5 bg-gradient-to-r from-cyan-500 to-cyan-600 text-white rounded-xl hover:from-cyan-600 hover:to-cyan-700 transition-all duration-200 shadow-md hover:shadow-lg font-medium">Binary Search</button>
                    <button type="submit" name="action" value="recursiveBinarySearch" 
                            class="px-5 py-2.5 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-xl hover:from-teal-600 hover:to-teal-700 transition-all duration-200 shadow-md hover:shadow-lg font-medium">Jump Search</button>
                </div>
            </form>

            <div id="searchOutput" class="mt-4 p-4 bg-white/60 backdrop-blur rounded-xl border border-gray-100">
                <?= $searchOutput ?>
            </div>
            </div>
        </div>

        <div id="memory-section" class="section-content section mb-8">
            <div class="bg-gradient-to-br from-emerald-50 to-green-50 p-6 rounded-xl border border-emerald-100 shadow-sm">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-green-600 bg-clip-text text-transparent mb-5 flex items-center gap-2">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Memory Allocation (First Fit)
                </h2>
            
            <form method="post">
                <input type="hidden" name="activeTab" value="memory">
                <div class="flex flex-col md:flex-row gap-4 mb-4 items-center">
                    <label for="blockInput" class="font-medium text-gray-700 whitespace-nowrap">Blocks (KB): </label>
                    <input type="text" id="blockInput" name="blockInput" value="<?= htmlspecialchars($blockInput) ?>" 
                            placeholder="e.g., 100, 500, 200, 300, 600"
                            class="p-3 border-0 rounded-xl flex-grow shadow-sm bg-white/70 backdrop-blur focus:ring-2 focus:ring-emerald-400 focus:outline-none transition-all">
                </div>
                <div class="flex flex-col md:flex-row gap-4 mb-5 items-center">
                    <label for="processInput" class="font-medium text-gray-700 whitespace-nowrap">Processes (KB): </label>
                    <input type="text" id="processInput" name="processInput" value="<?= htmlspecialchars($processInput) ?>" 
                            placeholder="e.g., 212, 417, 112, 426"
                            class="p-3 border-0 rounded-xl flex-grow shadow-sm bg-white/70 backdrop-blur focus:ring-2 focus:ring-emerald-400 focus:outline-none transition-all">
                </div>

                <button type="submit" name="action" value="firstFit" 
                            class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl hover:from-emerald-600 hover:to-green-700 transition-all duration-200 shadow-md hover:shadow-lg font-medium mb-4">Run First Fit</button>
            </form>

            <div id="memoryOutput" class="mt-4 p-4 bg-white/60 backdrop-blur rounded-xl border border-gray-100">
                <?= $memoryOutput ?>
            </div>
            </div>
        </div>

        <div id="paging-section" class="section-content section mb-8">
            <div class="bg-gradient-to-br from-orange-50 to-amber-50 p-6 rounded-xl border border-orange-100 shadow-sm">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent mb-5 flex items-center gap-2">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                    Page Replacement (FIFO & LRU)
                </h2>
            
            <form method="post">
                <input type="hidden" name="activeTab" value="paging">
                <div class="flex flex-col md:flex-row gap-4 mb-5 items-center">
                    <label for="refInput" class="font-medium text-gray-700 whitespace-nowrap">Reference String: </label>
                    <input type="text" id="refInput" name="refInput" value="<?= htmlspecialchars($refInput) ?>" 
                            placeholder="e.g., 7, 0, 1, 2, 0, 3, 0, 4"
                            class="p-3 border-0 rounded-xl flex-grow shadow-sm bg-white/70 backdrop-blur focus:ring-2 focus:ring-orange-400 focus:outline-none transition-all">
                    <label for="frameSize" class="font-medium text-gray-700 whitespace-nowrap">Frames:</label>
                    <input type="number" id="frameSize" name="frameSize" value="<?= htmlspecialchars($frameSize) ?>" min="1" max="10" 
                            class="p-3 border-0 rounded-xl w-24 shadow-sm bg-white/70 backdrop-blur focus:ring-2 focus:ring-orange-400 focus:outline-none transition-all">
                </div>
                
                <div class="flex gap-3 mb-5">
                    <button type="submit" name="action" value="fifoPaging" 
                            class="px-5 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-200 shadow-md hover:shadow-lg font-medium">Run FIFO</button>
                    <button type="submit" name="action" value="lruPaging" 
                            class="px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-md hover:shadow-lg font-medium">Run LRU</button>
                </div>
            </form>

            <div id="pagingOutput" class="mt-4 p-4 bg-white/60 backdrop-blur rounded-xl border border-gray-100">
                <?= $pagingOutput ?>
            </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        // Initialize tabs when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            initTabs('<?= htmlspecialchars($_POST['action'] ?? $activeTab) ?>');
        });
    </script>
</body>
</html>
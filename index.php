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
        .tab-button.active {
            border-bottom: 4px solid #3b82f6; /* Blue 500 */
            color: #1d4ed8; /* Blue 700 */
            font-weight: 600;
        }
        .section-content {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen p-4 md:p-8 font-['Inter']">

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow-2xl">
        <h1 class="text-4xl font-extrabold text-blue-700 mb-6 border-b pb-2">OS and Data Algorithm Simulator</h1>

        <nav class="mb-6 border-b border-gray-200">
            <ul class="flex -mb-px text-lg font-medium text-center">
                <li class="mr-2">
                    <button type="button" data-target="sort-section" class="tab-button inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300">Sorting</button>
                </li>
                <li class="mr-2">
                    <button type="button" data-target="search-section" class="tab-button inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300">Searching</button>
                </li>
                <li class="mr-2">
                    <button type="button" data-target="memory-section" class="tab-button inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300">Memory Allocation</button>
                </li>
                <li class="mr-2">
                    <button type="button" data-target="paging-section" class="tab-button inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300">Paging</button>
                </li>
            </ul>
        </nav>

        <input type="hidden" name="activeTab" id="activeTab" value="<?= htmlspecialchars($activeTab) ?>">

        <div id="sort-section" class="section-content section mb-10 p-4 border rounded-lg bg-blue-50">
            <h2 class="text-2xl font-semibold text-blue-800 mb-4">Sorting Algorithms</h2>
            
            <form method="post">
                <input type="hidden" name="activeTab" value="sort">
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
        </div>

        <div id="search-section" class="section-content section mb-10 p-4 border rounded-lg bg-blue-50">
            <h2 class="text-2xl font-semibold text-blue-800 mb-4">Searching Algorithms</h2>

            <form method="post">
                <input type="hidden" name="activeTab" value="search">
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

        <div id="memory-section" class="section-content section mb-10 p-4 border rounded-lg bg-green-50">
            <h2 class="text-2xl font-semibold text-green-800 mb-4">Memory Allocation (First Fit)</h2>
            
            <form method="post">
                <input type="hidden" name="activeTab" value="memory">
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
        </div>

        <div id="paging-section" class="section-content section p-4 border rounded-lg bg-green-50">
            <h2 class="text-2xl font-semibold text-green-800 mb-4">Page Replacement (FIFO & LRU)</h2>
            
            <form method="post">
                <input type="hidden" name="activeTab" value="paging">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab-button');
            const sections = document.querySelectorAll('.section-content');
            const activeTabInput = document.getElementById('activeTab');
            
            // Get the active tab from PHP or default to 'sort'
            let activeTabId = activeTabInput.value || 'sort';

            function showSection(targetId) {
                // Remove active class from all buttons and hide all sections
                tabs.forEach(tab => tab.classList.remove('active'));
                sections.forEach(section => section.style.display = 'none');

                // Add active class to the selected button
                const activeBtn = document.querySelector(`.tab-button[data-target="${targetId}-section"]`);
                if (activeBtn) {
                    activeBtn.classList.add('active');
                }

                // Show the target section
                const targetSection = document.getElementById(targetId + '-section');
                if (targetSection) {
                    targetSection.style.display = 'block';
                }

                // Update the hidden input field for PHP persistence
                activeTabInput.value = targetId;
            }

            // Set up click listeners for the navigation buttons
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target').replace('-section', '');
                    showSection(targetId);
                });
            });

            // Re-map PHP's simple activeTab value to the full section ID prefix
            const phpActiveMap = {
                'bubbleSort': 'sort',
                'selectionSort': 'sort',
                'mergeSort': 'sort',
                'linearSearch': 'search',
                'binarySearch': 'search',
                'recursiveBinarySearch': 'search',
                'firstFit': 'memory',
                'fifoPaging': 'paging',
                'lruPaging': 'paging',
                // Fallback to the value from the hidden input itself
                'sort': 'sort',
                'search': 'search',
                'memory': 'memory',
                'paging': 'paging'
            };

            // Use the PHP-determined active tab on page load
            const initialTab = phpActiveMap['<?= htmlspecialchars($_POST['action'] ?? $activeTab) ?>'] || 'sort';
            showSection(initialTab);
        });
    </script>
</body>
</html>
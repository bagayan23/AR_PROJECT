/* =====================================================
   OS and Data Algorithm Simulator - JavaScript
   Animation Engine for Algorithm Visualization
   ===================================================== */

// =====================================================
// Animation State Management
// =====================================================
const animationStates = {};
const pendingInits = [];

function getState(animId) {
    if (!animationStates[animId]) {
        animationStates[animId] = {
            playing: false,
            currentStep: 0,
            speed: 500,
            interval: null,
            steps: [],
            type: null
        };
    }
    return animationStates[animId];
}

// =====================================================
// Sorting Animation Functions
// =====================================================
function initSortAnimation(animId, steps) {
    const state = getState(animId);
    state.steps = steps;
    state.currentStep = 0;
    state.type = 'sort';
    renderSortStep(animId, 0);
}

function renderSortStep(animId, stepIndex) {
    const state = getState(animId);
    const step = state.steps[stepIndex];
    if (!step) return;
    
    const container = document.querySelector(`#${animId} .visual-display`);
    const descEl = document.querySelector(`#${animId} .step-description`);
    const stepEl = document.getElementById(`${animId}_step`);
    
    if (!container) return;
    
    const arr = step.array;
    
    let html = '<div class="visual-cards-container">';
    html += '<div class="visual-cards">';
    
    arr.forEach((value, i) => {
        let cardClasses = 'visual-card';
        
        if (step.comparing && step.comparing.includes(i)) {
            cardClasses += ' comparing';
        } else if (step.swapped && step.swapped.includes(i)) {
            cardClasses += ' swapped';
        } else if (step.sorted && step.sorted.includes(i)) {
            cardClasses += ' sorted';
        } else if (step.minIndex === i) {
            cardClasses += ' minimum';
        } else {
            cardClasses += ' default';
        }
        
        html += `<div class="${cardClasses}">
            <span class="card-value">${value}</span>
            <span class="card-index">[${i}]</span>
        </div>`;
    });
    
    html += '</div>';
    
    // Add legend
    html += '<div class="visual-legend">';
    html += '<span class="legend-item"><span class="legend-color comparing"></span> Comparing</span>';
    html += '<span class="legend-item"><span class="legend-color swapped"></span> Swapping</span>';
    html += '<span class="legend-item"><span class="legend-color sorted"></span> Sorted</span>';
    if (step.minIndex !== undefined && step.minIndex >= 0) {
        html += '<span class="legend-item"><span class="legend-color minimum"></span> Minimum</span>';
    }
    html += '</div>';
    html += '</div>';
    
    container.innerHTML = html;
    
    if (descEl) descEl.innerHTML = step.description;
    if (stepEl) stepEl.textContent = stepIndex;
}

function playSortAnimation(animId) {
    const state = getState(animId);
    if (state.playing) return;
    state.playing = true;
    
    state.interval = setInterval(() => {
        if (state.currentStep >= state.steps.length - 1) {
            pauseSortAnimation(animId);
            return;
        }
        state.currentStep++;
        renderSortStep(animId, state.currentStep);
    }, state.speed);
}

function pauseSortAnimation(animId) {
    const state = getState(animId);
    state.playing = false;
    if (state.interval) {
        clearInterval(state.interval);
        state.interval = null;
    }
}

function resetSortAnimation(animId) {
    pauseSortAnimation(animId);
    const state = getState(animId);
    state.currentStep = 0;
    renderSortStep(animId, 0);
}

function updateSpeed(animId, value) {
    const state = getState(animId);
    state.speed = 2100 - parseInt(value);
    if (state.playing) {
        pauseSortAnimation(animId);
        playSortAnimation(animId);
    }
}

// =====================================================
// Merge Sort Animation Functions
// =====================================================
function initMergeAnimation(animId, steps) {
    const state = getState(animId);
    state.steps = steps;
    state.currentStep = 0;
    state.type = 'merge';
    renderMergeStep(animId, 0);
}

function renderMergeStep(animId, stepIndex) {
    const state = getState(animId);
    const step = state.steps[stepIndex];
    if (!step) return;
    
    const container = document.querySelector(`#${animId} .merge-visual-display`);
    const descEl = document.querySelector(`#${animId} .step-description`);
    
    if (!container) return;
    
    let html = '<div class="visual-cards-container">';
    html += '<div class="merge-visual-content">';
    
    if (step.type === 'initial' || step.type === 'final') {
        html += `<div class="merge-cards-row ${step.type === 'final' ? 'final' : ''}">`;
        step.array.forEach((val, i) => {
            const cardClass = step.type === 'final' ? 'sorted' : 'default';
            html += `<div class="visual-card merge-card ${cardClass}">
                <span class="card-value">${val}</span>
                <span class="card-index">[${i}]</span>
            </div>`;
        });
        html += '</div>';
    } else if (step.type === 'divide') {
        html += '<div class="merge-step-label">Dividing</div>';
        html += '<div class="merge-cards-row dividing">';
        step.array.forEach((val, i) => {
            html += `<div class="visual-card merge-card comparing">
                <span class="card-value">${val}</span>
            </div>`;
        });
        html += '</div>';
        html += '<div class="merge-arrow">↓</div>';
        html += '<div class="merge-split-row">';
        html += '<div class="merge-cards-group left">';
        step.left.forEach(val => {
            html += `<div class="visual-card merge-card left-half">
                <span class="card-value">${val}</span>
            </div>`;
        });
        html += '</div>';
        html += '<div class="merge-cards-group right">';
        step.right.forEach(val => {
            html += `<div class="visual-card merge-card right-half">
                <span class="card-value">${val}</span>
            </div>`;
        });
        html += '</div>';
        html += '</div>';
    } else if (step.type === 'merge') {
        html += '<div class="merge-step-label">Merging</div>';
        html += '<div class="merge-combine-row">';
        html += '<div class="merge-cards-group left">';
        step.left.forEach(val => {
            html += `<div class="visual-card merge-card left-half">
                <span class="card-value">${val}</span>
            </div>`;
        });
        html += '</div>';
        html += '<span class="merge-plus">+</span>';
        html += '<div class="merge-cards-group right">';
        step.right.forEach(val => {
            html += `<div class="visual-card merge-card right-half">
                <span class="card-value">${val}</span>
            </div>`;
        });
        html += '</div>';
        html += '</div>';
        html += '<div class="merge-arrow">↓</div>';
        html += '<div class="merge-cards-row merged">';
        step.result.forEach(val => {
            html += `<div class="visual-card merge-card sorted">
                <span class="card-value">${val}</span>
            </div>`;
        });
        html += '</div>';
    }
    
    html += '</div>';
    
    // Legend
    html += '<div class="visual-legend">';
    html += '<span class="legend-item"><span class="legend-color comparing"></span> Dividing</span>';
    html += '<span class="legend-item"><span class="legend-color left-half"></span> Left Half</span>';
    html += '<span class="legend-item"><span class="legend-color right-half"></span> Right Half</span>';
    html += '<span class="legend-item"><span class="legend-color sorted"></span> Merged/Sorted</span>';
    html += '</div>';
    
    html += '</div>';
    container.innerHTML = html;
    
    if (descEl) descEl.innerHTML = step.description;
}

function playMergeAnimation(animId) {
    const state = getState(animId);
    if (state.playing) return;
    state.playing = true;
    
    state.interval = setInterval(() => {
        if (state.currentStep >= state.steps.length - 1) {
            pauseMergeAnimation(animId);
            return;
        }
        state.currentStep++;
        renderMergeStep(animId, state.currentStep);
    }, state.speed);
}

function pauseMergeAnimation(animId) {
    const state = getState(animId);
    state.playing = false;
    if (state.interval) {
        clearInterval(state.interval);
        state.interval = null;
    }
}

function resetMergeAnimation(animId) {
    pauseMergeAnimation(animId);
    const state = getState(animId);
    state.currentStep = 0;
    renderMergeStep(animId, 0);
}

function updateMergeSpeed(animId, value) {
    const state = getState(animId);
    state.speed = 2100 - parseInt(value);
    if (state.playing) {
        pauseMergeAnimation(animId);
        playMergeAnimation(animId);
    }
}

// =====================================================
// Search Animation Functions
// =====================================================
function initSearchAnimation(animId, steps, type) {
    const state = getState(animId);
    state.steps = steps;
    state.searchType = type;
    state.currentStep = 0;
    state.type = 'search';
    renderSearchStep(animId, 0);
}

function renderSearchStep(animId, stepIndex) {
    const state = getState(animId);
    const step = state.steps[stepIndex];
    if (!step) return;
    
    const container = document.querySelector(`#${animId} .search-visual-display`);
    const descEl = document.querySelector(`#${animId} .step-description`);
    
    if (!container) return;
    
    const arr = step.array;
    
    let html = '<div class="visual-cards-container">';
    html += '<div class="visual-cards">';
    
    arr.forEach((value, i) => {
        let cardClasses = 'visual-card search-card';
        
        if (step.found && step.foundIndex === i) {
            cardClasses += ' found';
        } else if (step.currentIndex === i) {
            cardClasses += ' checking';
        } else if (step.eliminated && step.eliminated.includes(i)) {
            cardClasses += ' eliminated';
        } else if (step.checked && step.checked.includes(i)) {
            cardClasses += ' checked';
        } else if (step.mid === i) {
            cardClasses += ' mid';
        } else if (step.low !== undefined && step.high !== undefined && i >= step.low && i <= step.high) {
            cardClasses += ' in-range';
        } else if (step.currentBlock && step.currentBlock.includes(i)) {
            cardClasses += ' block-highlight';
        } else if (step.linearSearch && step.linearSearch.includes(i)) {
            cardClasses += ' linear-check';
        } else {
            cardClasses += ' default';
        }
        
        html += `<div class="${cardClasses}">
            <span class="card-value">${value}</span>
            <span class="card-index">[${i}]</span>
        </div>`;
    });
    
    html += '</div>';
    
    // Add legend
    html += '<div class="visual-legend">';
    html += '<span class="legend-item"><span class="legend-color checking"></span> Checking</span>';
    html += '<span class="legend-item"><span class="legend-color found"></span> Found</span>';
    html += '<span class="legend-item"><span class="legend-color eliminated"></span> Eliminated</span>';
    if (state.searchType === 'binary') {
        html += '<span class="legend-item"><span class="legend-color mid"></span> Mid</span>';
        html += '<span class="legend-item"><span class="legend-color in-range"></span> In Range</span>';
    }
    if (state.searchType === 'jump') {
        html += '<span class="legend-item"><span class="legend-color block-highlight"></span> Current Block</span>';
    }
    html += '</div>';
    html += '</div>';
    
    container.innerHTML = html;
    
    if (descEl) descEl.innerHTML = step.description;
}

function playSearchAnimation(animId) {
    const state = getState(animId);
    if (state.playing) return;
    state.playing = true;
    
    state.interval = setInterval(() => {
        if (state.currentStep >= state.steps.length - 1) {
            pauseSearchAnimation(animId);
            return;
        }
        state.currentStep++;
        renderSearchStep(animId, state.currentStep);
    }, state.speed);
}

function pauseSearchAnimation(animId) {
    const state = getState(animId);
    state.playing = false;
    if (state.interval) {
        clearInterval(state.interval);
        state.interval = null;
    }
}

function resetSearchAnimation(animId) {
    pauseSearchAnimation(animId);
    const state = getState(animId);
    state.currentStep = 0;
    renderSearchStep(animId, 0);
}

// =====================================================
// Memory Allocation Animation Functions
// =====================================================
function initMemoryAnimation(animId, steps) {
    const state = getState(animId);
    state.steps = steps;
    state.currentStep = 0;
    state.type = 'memory';
    renderMemoryStep(animId, 0);
}

function renderMemoryStep(animId, stepIndex) {
    const state = getState(animId);
    const step = state.steps[stepIndex];
    if (!step) return;
    
    const container = document.querySelector(`#${animId} .memory-visual-display`);
    const descEl = document.querySelector(`#${animId} .step-description`);
    
    if (!container) return;
    
    let html = '<div class="space-y-4">';
    
    // Memory Blocks
    html += '<div class="mb-4"><span class="font-semibold text-gray-700">Memory Blocks:</span></div>';
    html += '<div class="flex flex-wrap gap-2 justify-center">';
    
    step.blocks.forEach((size, i) => {
        const origSize = step.originalBlocks[i];
        const usedPercent = ((origSize - size) / origSize) * 100;
        let blockClasses = 'memory-block border-2 relative overflow-hidden';
        
        if (step.currentBlock === i) {
            blockClasses += ' checking border-yellow-500';
        } else if (step.allocation && step.allocation.includes(i)) {
            blockClasses += ' border-green-500';
        } else {
            blockClasses += ' border-gray-300 bg-gray-100';
        }
        
        html += `<div class="${blockClasses}" style="min-width: 100px;">
            <div class="absolute bottom-0 left-0 right-0 bg-green-400 transition-all duration-300" style="height: ${usedPercent}%;"></div>
            <div class="relative z-10">
                <div class="font-bold">Block ${i + 1}</div>
                <div class="text-sm">${size}KB free</div>
                <div class="text-xs text-gray-500">(${origSize}KB total)</div>
            </div>
        </div>`;
    });
    
    html += '</div>';
    
    // Processes
    html += '<div class="mt-6 mb-2"><span class="font-semibold text-gray-700">Processes:</span></div>';
    html += '<div class="flex flex-wrap gap-2 justify-center">';
    
    step.processes.forEach((size, i) => {
        let procClasses = 'px-4 py-2 rounded-lg font-bold transition-all duration-300';
        
        if (step.currentProcess === i) {
            procClasses += ' bg-yellow-400 text-gray-800 scale-110';
        } else if (step.allocation && step.allocation[i] !== -1) {
            procClasses += ' bg-green-500 text-white';
        } else if (step.allocated === false && step.currentProcess === i) {
            procClasses += ' bg-red-500 text-white';
        } else {
            procClasses += ' bg-blue-500 text-white';
        }
        
        const allocatedTo = step.allocation && step.allocation[i] !== -1 ? ` → B${step.allocation[i] + 1}` : '';
        
        html += `<div class="${procClasses}">P${i + 1}: ${size}KB${allocatedTo}</div>`;
    });
    
    html += '</div>';
    html += '</div>';
    
    container.innerHTML = html;
    
    if (descEl) descEl.innerHTML = step.description;
}

function playMemoryAnimation(animId) {
    const state = getState(animId);
    if (state.playing) return;
    state.playing = true;
    
    state.interval = setInterval(() => {
        if (state.currentStep >= state.steps.length - 1) {
            pauseMemoryAnimation(animId);
            return;
        }
        state.currentStep++;
        renderMemoryStep(animId, state.currentStep);
    }, state.speed);
}

function pauseMemoryAnimation(animId) {
    const state = getState(animId);
    state.playing = false;
    if (state.interval) {
        clearInterval(state.interval);
        state.interval = null;
    }
}

function resetMemoryAnimation(animId) {
    pauseMemoryAnimation(animId);
    const state = getState(animId);
    state.currentStep = 0;
    renderMemoryStep(animId, 0);
}

// =====================================================
// Paging Animation Functions
// =====================================================
function initPagingAnimation(animId, steps, type) {
    const state = getState(animId);
    state.steps = steps;
    state.pagingType = type;
    state.currentStep = 0;
    state.type = 'paging';
    renderPagingStep(animId, 0);
}

function renderPagingStep(animId, stepIndex) {
    const state = getState(animId);
    const step = state.steps[stepIndex];
    if (!step) return;
    
    const container = document.querySelector(`#${animId} .paging-visual-display`);
    const descEl = document.querySelector(`#${animId} .step-description`);
    
    if (!container) return;
    
    let html = '<div class="space-y-4">';
    
    // Reference String with current indicator and index below
    html += '<div class="mb-4"><span class="font-semibold text-gray-700">Reference String:</span></div>';
    html += '<div class="flex flex-wrap gap-2 justify-center mb-4">';
    
    step.refString.forEach((page, i) => {
        let refClasses = 'ref-item flex flex-col items-center transition-all duration-300';
        let valueClasses = 'ref-value w-10 h-10 flex items-center justify-center rounded-lg font-bold text-sm shadow-sm';
        let indexClasses = 'ref-index text-xs mt-1 font-medium';
        
        if (i === step.currentStep) {
            valueClasses += ' bg-gradient-to-br from-amber-400 to-orange-500 text-white ring-2 ring-orange-300 ring-offset-1 scale-110';
            indexClasses += ' text-orange-600 font-bold';
        } else if (i < step.currentStep) {
            valueClasses += ' bg-gradient-to-br from-gray-400 to-gray-500 text-white';
            indexClasses += ' text-gray-500';
        } else {
            valueClasses += ' bg-gradient-to-br from-slate-100 to-slate-200 text-gray-700 border border-gray-300';
            indexClasses += ' text-gray-400';
        }
        
        html += `<div class="${refClasses}">`;
        html += `<div class="${valueClasses}">${page}</div>`;
        html += `<div class="${indexClasses}">[${i}]</div>`;
        html += '</div>';
    });
    
    html += '</div>';
    
    // Page Frames
    html += '<div class="mb-2"><span class="font-semibold text-gray-700">Page Frames:</span></div>';
    html += '<div class="flex gap-2 justify-center mb-4">';
    
    for (let i = 0; i < step.frameSize; i++) {
        const page = step.frames[i];
        let frameClasses = 'page-frame border-2 transition-all duration-300';
        
        if (page !== undefined) {
            if (page === step.currentPage && step.isHit) {
                frameClasses += ' hit';
            } else if (page === step.currentPage && !step.isHit) {
                frameClasses += ' fault new bg-red-400 text-white border-red-500';
            } else {
                frameClasses += ' bg-blue-500 text-white border-blue-600';
            }
        } else {
            frameClasses += ' bg-gray-200 border-gray-300 text-gray-400';
        }
        
        html += `<div class="${frameClasses}">${page !== undefined ? page : '-'}</div>`;
    }
    
    html += '</div>';
    
    // Status
    if (step.currentStep >= 0) {
        html += '<div class="flex justify-center gap-4 items-center">';
        if (step.isHit) {
            html += '<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full font-bold">HIT ✓</span>';
        } else if (step.currentPage !== -1) {
            html += '<span class="px-3 py-1 bg-red-100 text-red-800 rounded-full font-bold">FAULT ✗</span>';
        }
        html += `<span class="text-gray-600">Page Faults: <strong>${step.pageFaults}</strong></span>`;
        html += '</div>';
        
        if (step.replaced !== null && step.replaced !== undefined) {
            html += `<div class="text-center mt-2 text-sm text-orange-600">Replaced page: <strong>${step.replaced}</strong></div>`;
        }
    }
    
    html += '</div>';
    
    container.innerHTML = html;
    
    if (descEl) descEl.innerHTML = step.description;
}

function playPagingAnimation(animId) {
    const state = getState(animId);
    if (state.playing) return;
    state.playing = true;
    
    state.interval = setInterval(() => {
        if (state.currentStep >= state.steps.length - 1) {
            pausePagingAnimation(animId);
            return;
        }
        state.currentStep++;
        renderPagingStep(animId, state.currentStep);
    }, state.speed);
}

function pausePagingAnimation(animId) {
    const state = getState(animId);
    state.playing = false;
    if (state.interval) {
        clearInterval(state.interval);
        state.interval = null;
    }
}

function resetPagingAnimation(animId) {
    pausePagingAnimation(animId);
    const state = getState(animId);
    state.currentStep = 0;
    renderPagingStep(animId, 0);
}

// =====================================================
// Tab Navigation
// =====================================================
function initTabs(activeTab) {
    const tabs = document.querySelectorAll('.tab-button');
    const sections = document.querySelectorAll('.section-content');
    const activeTabInput = document.getElementById('activeTab');

    function showSection(targetId) {
        tabs.forEach(tab => tab.classList.remove('active'));
        sections.forEach(section => section.style.display = 'none');

        const activeBtn = document.querySelector(`.tab-button[data-target="${targetId}-section"]`);
        if (activeBtn) {
            activeBtn.classList.add('active');
        }

        const targetSection = document.getElementById(targetId + '-section');
        if (targetSection) {
            targetSection.style.display = 'block';
        }

        if (activeTabInput) {
            activeTabInput.value = targetId;
        }
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target').replace('-section', '');
            showSection(targetId);
        });
    });

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
        'sort': 'sort',
        'search': 'search',
        'memory': 'memory',
        'paging': 'paging'
    };

    const initialTab = phpActiveMap[activeTab] || 'sort';
    showSection(initialTab);
}

// =====================================================
// Initialization on DOM Ready
// =====================================================
document.addEventListener('DOMContentLoaded', function() {
    // Process any pending initializations
    while (pendingInits.length > 0) {
        const init = pendingInits.shift();
        init();
    }
});

// Make functions globally available
window.initSortAnimation = initSortAnimation;
window.playSortAnimation = playSortAnimation;
window.pauseSortAnimation = pauseSortAnimation;
window.resetSortAnimation = resetSortAnimation;
window.updateSpeed = updateSpeed;

window.initMergeAnimation = initMergeAnimation;
window.playMergeAnimation = playMergeAnimation;
window.pauseMergeAnimation = pauseMergeAnimation;
window.resetMergeAnimation = resetMergeAnimation;
window.updateMergeSpeed = updateMergeSpeed;

window.initSearchAnimation = initSearchAnimation;
window.playSearchAnimation = playSearchAnimation;
window.pauseSearchAnimation = pauseSearchAnimation;
window.resetSearchAnimation = resetSearchAnimation;

window.initMemoryAnimation = initMemoryAnimation;
window.playMemoryAnimation = playMemoryAnimation;
window.pauseMemoryAnimation = pauseMemoryAnimation;
window.resetMemoryAnimation = resetMemoryAnimation;

window.initPagingAnimation = initPagingAnimation;
window.playPagingAnimation = playPagingAnimation;
window.pausePagingAnimation = pausePagingAnimation;
window.resetPagingAnimation = resetPagingAnimation;

window.initTabs = initTabs;

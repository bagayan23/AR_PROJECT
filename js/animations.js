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
// Process History Toggle
// =====================================================
function toggleHistory(historyId) {
    const content = document.getElementById(historyId);
    const icon = document.getElementById(historyId + '_icon');
    const text = document.getElementById(historyId + '_text');
    
    if (content && icon && text) {
        content.classList.toggle('hidden');
        const isHidden = content.classList.contains('hidden');
        text.textContent = isHidden ? 'Show' : 'Hide';
        icon.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(180deg)';
    }
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

function updateSearchSpeed(animId, value) {
    const state = getState(animId);
    // Invert the value: lower slider = slower (higher delay), higher slider = faster (lower delay)
    state.speed = 2100 - parseInt(value);
    
    // If currently playing, restart with new speed
    if (state.playing) {
        pauseSearchAnimation(animId);
        playSearchAnimation(animId);
    }
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
    
    let html = '<div class="memory-visualization">';
    
    // Memory Blocks Section
    html += '<div class="memory-section">';
    html += '<div class="section-header"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg> Memory Blocks</div>';
    html += '<div class="memory-blocks-grid">';
    
    step.blocks.forEach((size, i) => {
        const origSize = step.originalBlocks[i];
        const usedSize = origSize - size;
        const usedPercent = (usedSize / origSize) * 100;
        const freePercent = 100 - usedPercent;
        
        let blockStatus = 'available';
        let statusIcon = '';
        let borderColor = 'border-slate-300';
        let glowClass = '';
        
        if (step.currentBlock === i) {
            blockStatus = 'checking';
            borderColor = 'border-amber-400';
            glowClass = 'ring-2 ring-amber-300 ring-offset-2';
            statusIcon = '<div class="status-badge checking"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></div>';
        } else if (step.allocated && step.currentBlock === i) {
            blockStatus = 'allocated';
            borderColor = 'border-emerald-500';
            glowClass = 'ring-2 ring-emerald-300 ring-offset-2';
            statusIcon = '<div class="status-badge allocated"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>';
        } else if (usedPercent > 0) {
            blockStatus = 'partial';
            borderColor = 'border-emerald-400';
        }
        
        html += `<div class="memory-block-card ${glowClass}">`;
        html += statusIcon;
        html += `<div class="block-header">Block ${i + 1}</div>`;
        html += '<div class="block-visual">';
        html += `<div class="block-bar">`;
        html += `<div class="block-used" style="height: ${usedPercent}%"></div>`;
        html += `<div class="block-free" style="height: ${freePercent}%"></div>`;
        html += '</div>';
        html += '</div>';
        html += '<div class="block-info">';
        html += `<div class="block-free-size">${size}KB <span class="text-slate-400">free</span></div>`;
        if (usedSize > 0) {
            html += `<div class="block-used-size">${usedSize}KB <span class="text-emerald-600">used</span></div>`;
        }
        html += `<div class="block-total">${origSize}KB total</div>`;
        html += '</div>';
        html += '</div>';
    });
    
    html += '</div></div>';
    
    // Allocation Arrow
    if (step.currentProcess !== undefined && step.currentProcess >= 0) {
        html += '<div class="allocation-indicator">';
        if (step.allocated) {
            html += '<div class="arrow-down success"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg></div>';
        } else if (step.allocated === false) {
            html += '<div class="arrow-down failed"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></div>';
        } else {
            html += '<div class="arrow-down searching"><svg class="w-8 h-8 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg></div>';
        }
        html += '</div>';
    }
    
    // Processes Section
    html += '<div class="process-section">';
    html += '<div class="section-header"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6"/></svg> Processes</div>';
    html += '<div class="processes-grid">';
    
    step.processes.forEach((size, i) => {
        let processStatus = 'waiting';
        let processClasses = 'process-card';
        let statusText = '';
        let allocInfo = '';
        
        if (step.currentProcess === i) {
            if (step.allocated === true) {
                processStatus = 'allocated';
                processClasses += ' allocated';
                statusText = '<span class="status-text success">✓ Allocated</span>';
            } else if (step.allocated === false) {
                processStatus = 'failed';
                processClasses += ' failed';
                statusText = '<span class="status-text failed">✗ Failed</span>';
            } else {
                processStatus = 'active';
                processClasses += ' active';
                statusText = '<span class="status-text active">Processing...</span>';
            }
        } else if (step.allocation && step.allocation[i] !== -1) {
            processStatus = 'completed';
            processClasses += ' completed';
            allocInfo = `<div class="alloc-info">→ Block ${step.allocation[i] + 1}</div>`;
        }
        
        html += `<div class="${processClasses}">`;
        html += `<div class="process-header">P${i + 1}</div>`;
        html += `<div class="process-size">${size}KB</div>`;
        html += allocInfo;
        html += statusText;
        html += '</div>';
    });
    
    html += '</div></div>';
    
    // Legend
    html += '<div class="memory-legend">';
    html += '<div class="legend-item"><span class="legend-dot bg-slate-300"></span> Available</div>';
    html += '<div class="legend-item"><span class="legend-dot bg-amber-400"></span> Checking</div>';
    html += '<div class="legend-item"><span class="legend-dot bg-emerald-500"></span> Allocated</div>';
    html += '<div class="legend-item"><span class="legend-dot bg-red-500"></span> Failed</div>';
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

function updateMemorySpeed(animId, value) {
    const state = getState(animId);
    // Invert the value: lower slider = slower (higher delay), higher slider = faster (lower delay)
    state.speed = 2100 - parseInt(value);
    
    // If currently playing, restart with new speed
    if (state.playing) {
        pauseMemoryAnimation(animId);
        playMemoryAnimation(animId);
    }
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

function updatePagingSpeed(animId, value) {
    const state = getState(animId);
    // Invert the value: lower slider = slower (higher delay), higher slider = faster (lower delay)
    state.speed = 2100 - parseInt(value);
    
    // If currently playing, restart with new speed
    if (state.playing) {
        pausePagingAnimation(animId);
        playPagingAnimation(animId);
    }
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
window.updateSearchSpeed = updateSearchSpeed;

window.initMemoryAnimation = initMemoryAnimation;
window.playMemoryAnimation = playMemoryAnimation;
window.pauseMemoryAnimation = pauseMemoryAnimation;
window.resetMemoryAnimation = resetMemoryAnimation;
window.updateMemorySpeed = updateMemorySpeed;

window.initPagingAnimation = initPagingAnimation;
window.playPagingAnimation = playPagingAnimation;
window.pausePagingAnimation = pausePagingAnimation;
window.resetPagingAnimation = resetPagingAnimation;
window.updatePagingSpeed = updatePagingSpeed;

window.initTabs = initTabs;

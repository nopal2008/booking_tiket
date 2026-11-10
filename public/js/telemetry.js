/**
 * Tumpak Sewu Real-Time Frontend Performance & Health Telemetry Monitor
 * Tracks Core Web Vitals (LCP, CLS, INP/FID, FCP, TTFB), Long Tasks, Error Telemetry, and Memory.
 */
(function () {
    'use strict';

    const Telemetry = {
        metrics: {
            fcp: null,
            lcp: null,
            cls: 0,
            fid: null,
            inp: null,
            ttfb: null,
            domTime: null,
            loadTime: null,
            longTasks: 0,
            jsHeap: null,
            fps: 60,
        },
        errors: [],
        logs: [],
        isOpen: false,
        fpsFrames: 0,
        lastFpsUpdate: performance.now(),

        init() {
            this.measureNavigationTiming();
            this.observePaint();
            this.observeLCP();
            this.observeCLS();
            this.observeFIDandINP();
            this.observeLongTasks();
            this.captureErrors();
            this.startFpsLoop();
            this.injectHUD();
            console.info('%c[Telemetry Monitor]%c Frontend performance & health monitoring initialized.', 'color:#059669;font-weight:bold;', 'color:inherit;');
        },

        measureNavigationTiming() {
            window.addEventListener('load', () => {
                setTimeout(() => {
                    const nav = performance.getEntriesByType('navigation')[0];
                    if (nav) {
                        this.metrics.ttfb = Math.round(nav.responseStart - nav.requestStart);
                        this.metrics.domTime = Math.round(nav.domContentLoadedEventEnd - nav.startTime);
                        this.metrics.loadTime = Math.round(nav.loadEventEnd - nav.startTime);
                    }
                    if (performance.memory) {
                        this.metrics.jsHeap = Math.round(performance.memory.usedJSHeapSize / (1024 * 1024));
                    }
                    this.updateUI();
                }, 0);
            });
        },

        observePaint() {
            try {
                const observer = new PerformanceObserver((list) => {
                    for (const entry of list.getEntries()) {
                        if (entry.name === 'first-contentful-paint') {
                            this.metrics.fcp = Math.round(entry.startTime);
                            this.updateUI();
                        }
                    }
                });
                observer.observe({ type: 'paint', buffered: true });
            } catch (e) {}
        },

        observeLCP() {
            try {
                const observer = new PerformanceObserver((list) => {
                    const entries = list.getEntries();
                    const lastEntry = entries[entries.length - 1];
                    if (lastEntry) {
                        this.metrics.lcp = Math.round(lastEntry.startTime);
                        this.updateUI();
                    }
                });
                observer.observe({ type: 'largest-contentful-paint', buffered: true });
            } catch (e) {}
        },

        observeCLS() {
            try {
                const observer = new PerformanceObserver((list) => {
                    for (const entry of list.getEntries()) {
                        if (!entry.hadRecentInput) {
                            this.metrics.cls += entry.value;
                            this.updateUI();
                        }
                    }
                });
                observer.observe({ type: 'layout-shift', buffered: true });
            } catch (e) {}
        },

        observeFIDandINP() {
            try {
                const observer = new PerformanceObserver((list) => {
                    for (const entry of list.getEntries()) {
                        const duration = Math.round(entry.duration);
                        if (entry.entryType === 'first-input') {
                            this.metrics.fid = duration;
                        }
                        if (entry.entryType === 'event') {
                            if (!this.metrics.inp || duration > this.metrics.inp) {
                                this.metrics.inp = duration;
                            }
                        }
                        this.updateUI();
                    }
                });
                observer.observe({ type: 'first-input', buffered: true });
            } catch (e) {}
        },

        observeLongTasks() {
            try {
                const observer = new PerformanceObserver((list) => {
                    for (const entry of list.getEntries()) {
                        if (entry.duration > 50) {
                            this.metrics.longTasks++;
                            this.logs.push({
                                type: 'warn',
                                time: new Date().toLocaleTimeString(),
                                message: `Long task detected: ${Math.round(entry.duration)}ms`
                            });
                            this.updateUI();
                        }
                    }
                });
                observer.observe({ type: 'longtask', buffered: true });
            } catch (e) {}
        },

        captureErrors() {
            window.addEventListener('error', (event) => {
                this.errors.push({
                    type: 'error',
                    time: new Date().toLocaleTimeString(),
                    message: event.message,
                    source: event.filename ? `${event.filename.split('/').pop()}:${event.lineno}` : 'inline'
                });
                this.updateUI();
            });

            window.addEventListener('unhandledrejection', (event) => {
                this.errors.push({
                    type: 'unhandled-promise',
                    time: new Date().toLocaleTimeString(),
                    message: event.reason?.message || String(event.reason),
                    source: 'Promise Rejection'
                });
                this.updateUI();
            });
        },

        startFpsLoop() {
            const frame = (time) => {
                this.fpsFrames++;
                if (time - this.lastFpsUpdate >= 1000) {
                    this.metrics.fps = Math.round((this.fpsFrames * 1000) / (time - this.lastFpsUpdate));
                    this.fpsFrames = 0;
                    this.lastFpsUpdate = time;
                    if (performance.memory) {
                        this.metrics.jsHeap = Math.round(performance.memory.usedJSHeapSize / (1024 * 1024));
                    }
                    this.updateUI();
                }
                requestAnimationFrame(frame);
            };
            requestAnimationFrame(frame);
        },

        getRating(metric, value) {
            if (value === null || value === undefined) return { label: 'Waiting', color: 'text-slate-400' };
            switch (metric) {
                case 'lcp':
                    return value <= 2500 ? { label: 'Good', color: 'text-emerald-500' } :
                           value <= 4000 ? { label: 'Needs Improvement', color: 'text-amber-500' } :
                                           { label: 'Poor', color: 'text-rose-500' };
                case 'cls':
                    return value <= 0.1 ? { label: 'Good', color: 'text-emerald-500' } :
                           value <= 0.25 ? { label: 'Needs Improvement', color: 'text-amber-500' } :
                                           { label: 'Poor', color: 'text-rose-500' };
                case 'fcp':
                    return value <= 1800 ? { label: 'Good', color: 'text-emerald-500' } :
                           value <= 3000 ? { label: 'Needs Improvement', color: 'text-amber-500' } :
                                           { label: 'Poor', color: 'text-rose-500' };
                case 'ttfb':
                    return value <= 800 ? { label: 'Good', color: 'text-emerald-500' } :
                           value <= 1800 ? { label: 'Needs Improvement', color: 'text-amber-500' } :
                                           { label: 'Poor', color: 'text-rose-500' };
                default:
                    return { label: 'Normal', color: 'text-emerald-500' };
            }
        },

        injectHUD() {
            const wrapper = document.createElement('div');
            wrapper.id = 'telemetry-hud';
            wrapper.className = 'fixed bottom-4 right-4 z-50 font-sans text-xs selection:bg-emerald-500 selection:text-white pointer-events-auto';

            wrapper.innerHTML = `
                <!-- Pill Toggle Button -->
                <button id="telemetry-pill" type="button" class="group flex items-center gap-2.5 px-3 py-2 rounded-full bg-slate-900/90 hover:bg-slate-900 text-white backdrop-blur-md border border-slate-700/60 shadow-xl transition-all duration-300 hover:scale-105 active:scale-95">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="font-mono font-medium tracking-tight text-slate-200">
                        <span id="pill-fps">60</span> FPS
                        <span class="text-slate-600 mx-1">•</span>
                        LCP <span id="pill-lcp" class="text-emerald-400">-</span>
                    </span>
                    <span id="pill-badge" class="hidden px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-rose-500/30 text-rose-300 border border-rose-500/50">0 err</span>
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-emerald-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </button>

                <!-- Full Telemetry Modal Drawer -->
                <div id="telemetry-drawer" class="hidden mt-2 w-80 sm:w-96 rounded-2xl bg-slate-900/95 text-slate-200 backdrop-blur-xl border border-slate-700/80 shadow-2xl p-4 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                            <h3 class="font-semibold text-white text-sm">System Health & Telemetry</h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] text-slate-400 font-mono bg-slate-800 px-2 py-0.5 rounded">Ctrl+Shift+P</span>
                            <button id="telemetry-close" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800">&times;</button>
                        </div>
                    </div>

                    <!-- Core Web Vitals Grid -->
                    <div class="grid grid-cols-2 gap-2 mt-3 text-slate-300 font-mono">
                        <div class="bg-slate-800/60 p-2.5 rounded-xl border border-slate-700/50">
                            <div class="text-[10px] text-slate-400 flex justify-between"><span>LCP</span><span id="badge-lcp" class="text-[9px]"></span></div>
                            <div class="text-base font-bold text-white mt-0.5" id="val-lcp">-</div>
                            <div class="text-[9px] text-slate-400">Largest Contentful Paint</div>
                        </div>
                        <div class="bg-slate-800/60 p-2.5 rounded-xl border border-slate-700/50">
                            <div class="text-[10px] text-slate-400 flex justify-between"><span>CLS</span><span id="badge-cls" class="text-[9px]"></span></div>
                            <div class="text-base font-bold text-white mt-0.5" id="val-cls">0.000</div>
                            <div class="text-[9px] text-slate-400">Cumulative Layout Shift</div>
                        </div>
                        <div class="bg-slate-800/60 p-2.5 rounded-xl border border-slate-700/50">
                            <div class="text-[10px] text-slate-400 flex justify-between"><span>FCP</span><span id="badge-fcp" class="text-[9px]"></span></div>
                            <div class="text-base font-bold text-white mt-0.5" id="val-fcp">-</div>
                            <div class="text-[9px] text-slate-400">First Contentful Paint</div>
                        </div>
                        <div class="bg-slate-800/60 p-2.5 rounded-xl border border-slate-700/50">
                            <div class="text-[10px] text-slate-400 flex justify-between"><span>TTFB</span><span id="badge-ttfb" class="text-[9px]"></span></div>
                            <div class="text-base font-bold text-white mt-0.5" id="val-ttfb">-</div>
                            <div class="text-[9px] text-slate-400">Time to First Byte</div>
                        </div>
                    </div>

                    <!-- Diagnostics Bar -->
                    <div class="mt-3 bg-slate-800/40 p-2.5 rounded-xl border border-slate-700/40 space-y-1.5 font-mono text-[11px]">
                        <div class="flex justify-between text-slate-400">
                            <span>FPS Target</span>
                            <span class="text-emerald-400 font-semibold" id="val-fps">60 FPS</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>DOM Ready</span>
                            <span class="text-slate-200" id="val-dom">-</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>Page Load</span>
                            <span class="text-slate-200" id="val-load">-</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>JS Heap Memory</span>
                            <span class="text-slate-200" id="val-heap">-</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>Long Tasks (>50ms)</span>
                            <span class="text-amber-400" id="val-longtasks">0</span>
                        </div>
                    </div>

                    <!-- Error Console Log -->
                    <div class="mt-3">
                        <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1 flex justify-between">
                            <span>Runtime Logs</span>
                            <span id="error-count" class="text-slate-400">0 issue</span>
                        </div>
                        <div id="telemetry-logs" class="max-h-24 overflow-y-auto font-mono text-[10px] space-y-1 bg-slate-950/60 p-2 rounded-lg border border-slate-800 text-slate-400">
                            <div class="text-emerald-400/80">✓ All scripts and visual components healthy.</div>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(wrapper);

            const pill = document.getElementById('telemetry-pill');
            const drawer = document.getElementById('telemetry-drawer');
            const closeBtn = document.getElementById('telemetry-close');

            const toggle = () => {
                this.isOpen = !this.isOpen;
                drawer.classList.toggle('hidden', !this.isOpen);
            };

            pill.addEventListener('click', toggle);
            closeBtn.addEventListener('click', () => {
                this.isOpen = false;
                drawer.classList.add('hidden');
            });

            window.addEventListener('keydown', (e) => {
                if (e.ctrlKey && e.shiftKey && (e.key === 'P' || e.key === 'p')) {
                    e.preventDefault();
                    toggle();
                }
            });
        },

        updateUI() {
            const pillFps = document.getElementById('pill-fps');
            const pillLcp = document.getElementById('pill-lcp');
            const pillBadge = document.getElementById('pill-badge');

            if (pillFps) pillFps.textContent = this.metrics.fps;
            if (pillLcp) pillLcp.textContent = this.metrics.lcp ? `${(this.metrics.lcp / 1000).toFixed(2)}s` : '-';

            const totalIssues = this.errors.length + this.metrics.longTasks;
            if (pillBadge) {
                if (totalIssues > 0) {
                    pillBadge.textContent = `${totalIssues} alert`;
                    pillBadge.classList.remove('hidden');
                } else {
                    pillBadge.classList.add('hidden');
                }
            }

            const valLcp = document.getElementById('val-lcp');
            const badgeLcp = document.getElementById('badge-lcp');
            if (valLcp && this.metrics.lcp) {
                valLcp.textContent = `${(this.metrics.lcp / 1000).toFixed(2)}s`;
                const r = this.getRating('lcp', this.metrics.lcp);
                badgeLcp.textContent = r.label;
                badgeLcp.className = `text-[9px] font-semibold ${r.color}`;
            }

            const valCls = document.getElementById('val-cls');
            const badgeCls = document.getElementById('badge-cls');
            if (valCls) {
                valCls.textContent = this.metrics.cls.toFixed(3);
                const r = this.getRating('cls', this.metrics.cls);
                badgeCls.textContent = r.label;
                badgeCls.className = `text-[9px] font-semibold ${r.color}`;
            }

            const valFcp = document.getElementById('val-fcp');
            const badgeFcp = document.getElementById('badge-fcp');
            if (valFcp && this.metrics.fcp) {
                valFcp.textContent = `${(this.metrics.fcp / 1000).toFixed(2)}s`;
                const r = this.getRating('fcp', this.metrics.fcp);
                badgeFcp.textContent = r.label;
                badgeFcp.className = `text-[9px] font-semibold ${r.color}`;
            }

            const valTtfb = document.getElementById('val-ttfb');
            const badgeTtfb = document.getElementById('badge-ttfb');
            if (valTtfb && this.metrics.ttfb) {
                valTtfb.textContent = `${this.metrics.ttfb}ms`;
                const r = this.getRating('ttfb', this.metrics.ttfb);
                badgeTtfb.textContent = r.label;
                badgeTtfb.className = `text-[9px] font-semibold ${r.color}`;
            }

            const valFps = document.getElementById('val-fps');
            if (valFps) valFps.textContent = `${this.metrics.fps} FPS`;

            const valDom = document.getElementById('val-dom');
            if (valDom && this.metrics.domTime) valDom.textContent = `${this.metrics.domTime}ms`;

            const valLoad = document.getElementById('val-load');
            if (valLoad && this.metrics.loadTime) valLoad.textContent = `${this.metrics.loadTime}ms`;

            const valHeap = document.getElementById('val-heap');
            if (valHeap && this.metrics.jsHeap) valHeap.textContent = `${this.metrics.jsHeap} MB`;

            const valLong = document.getElementById('val-longtasks');
            if (valLong) valLong.textContent = this.metrics.longTasks;

            const logsBox = document.getElementById('telemetry-logs');
            const errorCount = document.getElementById('error-count');
            if (logsBox && (this.errors.length > 0 || this.logs.length > 0)) {
                if (errorCount) errorCount.textContent = `${this.errors.length} err, ${this.metrics.longTasks} warn`;
                logsBox.innerHTML = '';
                [...this.errors, ...this.logs].forEach((item) => {
                    const row = document.createElement('div');
                    row.className = item.type === 'error' ? 'text-rose-400' : 'text-amber-400';
                    row.textContent = `[${item.time}] ${item.message}`;
                    logsBox.appendChild(row);
                });
            }
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => Telemetry.init());
    } else {
        Telemetry.init();
    }

    window.AppTelemetry = Telemetry;
})();


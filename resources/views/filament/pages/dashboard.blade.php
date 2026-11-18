<x-filament-panels::page class="fi-dashboard-page">
    <script>
        (function() {
            window.filamentChartJsPlugins = window.filamentChartJsPlugins || [];

            // Livewire re-evaluates DOM scripts on navigation; replace existing plugin entry to prevent duplicate registration.
            const pluginId = 'sipetronBarBadge';
            const pluginIdx = window.filamentChartJsPlugins.findIndex(p => p && p.id === pluginId);

            const barBadgePlugin = {
                id: pluginId,
                afterDatasetsDraw(chart) {
                    if (!chart.config.options?.plugins?.sipetronBarBadge?.display) {
                        return;
                    }

                    try {
                        const ctx = chart.ctx;
                        ctx.save();

                        chart.data.datasets.forEach((dataset, datasetIndex) => {
                            const meta = chart.getDatasetMeta(datasetIndex);
                            if (!meta || meta.hidden) {
                                return;
                            }

                            meta.data.forEach((element, index) => {
                                const val = dataset.data[index];
                                if (val === undefined || val === null) {
                                    return;
                                }

                                const text = String(val);
                                const badgeW = 20;
                                const badgeH = 17;
                                const badgeX = (element?.x ?? 0) + 8;
                                const badgeY = (element?.y ?? 0) - (badgeH / 2);

                                ctx.beginPath();
                                if (typeof ctx.roundRect === 'function') {
                                    ctx.roundRect(badgeX, badgeY, badgeW, badgeH, 6);
                                } else {
                                    ctx.rect(badgeX, badgeY, badgeW, badgeH);
                                }
                                ctx.fillStyle = '#E8F3EC';
                                ctx.fill();
                                ctx.lineWidth = 1;
                                ctx.strokeStyle = '#C4DED0';
                                ctx.stroke();

                                ctx.fillStyle = '#0F3D2E';
                                ctx.font = 'bold 10px ui-sans-serif, system-ui, -apple-system, sans-serif';
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'middle';
                                ctx.fillText(text, badgeX + (badgeW / 2), element?.y ?? 0);
                            });
                        });

                        ctx.restore();
                    } catch (err) {
                        console.warn('[E-VITRON] Failed to render chart bar badge:', err);
                    }
                }
            };

            if (pluginIdx >= 0) {
                window.filamentChartJsPlugins[pluginIdx] = barBadgePlugin;
            } else {
                window.filamentChartJsPlugins.push(barBadgePlugin);
            }

            function initCountUp() {
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    return;
                }

                const counters = document.querySelectorAll('.sipetron-stat-value[data-count]');
                counters.forEach(counter => {
                    if (counter.dataset.animated === 'true') {
                        return;
                    }
                    counter.dataset.animated = 'true';

                    const target = parseInt(counter.getAttribute('data-count'), 10);
                    if (isNaN(target) || target <= 0) {
                        return;
                    }

                    const duration = 600;
                    const startTime = performance.now();

                    function update(now) {
                        const elapsed = now - startTime;
                        const progress = Math.min(elapsed / duration, 1);
                        const ease = 1 - (1 - progress) * (1 - progress);
                        counter.textContent = String(Math.round(ease * target));

                        if (progress < 1) {
                            requestAnimationFrame(update);
                        } else {
                            counter.textContent = String(target);
                        }
                    }

                    counter.textContent = '0';
                    requestAnimationFrame(update);
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initCountUp, { once: true });
            } else {
                initCountUp();
            }

            if (!window._sipetronDashboardNavListenerBound) {
                window._sipetronDashboardNavListenerBound = true;
                document.addEventListener('livewire:navigated', initCountUp);
            }
        })();
    </script>

    @include('filament.pages.partials.dashboard.styles')
    @include('filament.pages.partials.dashboard.kpi-and-widgets')
</x-filament-panels::page>

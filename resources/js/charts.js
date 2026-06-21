import Chart from 'chart.js/auto';

/**
 * Render a chart into a canvas element.
 *
 * @param {string} canvasId  - ID of the <canvas> element
 * @param {string} type      - 'bar' | 'line' | 'doughnut' | 'pie'
 * @param {object} data      - { labels: [...], datasets: [...] }
 * @param {object} [opts]    - optional Chart.js config overrides
 * @returns {Chart|null}
 */
export function createChart(canvasId, type, data, opts = {}) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return null;

    // Destroy existing chart on this canvas if any
    const existing = Chart.getChart(canvas);
    if (existing) existing.destroy();

    return new Chart(canvas, {
        type,
        data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'bottom' },
            },
            ...opts,
        },
    });
}

/**
 * Build datasets with DaisyUI-compatible colors.
 */
export const colors = {
    primary: '#4f46e5',
    secondary: '#a855f7',
    accent: '#06b6d4',
    success: '#22c55e',
    warning: '#f59e0b',
    error: '#ef4444',
    info: '#3b82f6',
    palette: [
        '#4f46e5', '#a855f7', '#06b6d4', '#22c55e',
        '#f59e0b', '#ef4444', '#ec4899', '#14b8a6',
        '#f97316', '#8b5cf6', '#0ea5e9', '#84cc16',
    ],
};

/**
 * Auto-render all charts from embedded JSON data in the page.
 * Usage: <canvas id="chart-xxx" data-chart='{...}'></canvas>
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
        try {
            const spec = JSON.parse(canvas.dataset.chart);
            createChart(canvas.id, spec.type, spec.data, spec.options);
        } catch (e) {
            console.warn('Chart parse error on', canvas.id, e);
        }
    });
});

(function () {
    const canvas = document.getElementById('bg-balls');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    let balls = [];
    let animId;

    function resize() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    window.addEventListener('resize', resize);
    resize();

    const COUNT = 30;
    const MIN_R = 6;
    const MAX_R = 60;
    const SPEED = 1.2;

    function getThemeColor() {
        const theme = document.documentElement.getAttribute('data-theme') || 'garden';
        return theme === 'dim'
            ? 'oklch(86.133% 0.141 139.549)'
            : 'oklch(62.45% 0.278 3.836)';
            // Referenced fropm DaisyUI's "garden" and "dim" theme colors, converted to oklch for better color interpolation.
    }

    function initBalls() {
        balls = [];
        const baseColor = getThemeColor();
        for (let i = 0; i < COUNT; i++) {
            const r = MIN_R + Math.random() * (MAX_R - MIN_R);
            const alpha = 0.15 + Math.random() * 0.25;
            balls.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                r,
                vx: (Math.random() - 0.5) * SPEED * 2,
                vy: (Math.random() - 0.5) * SPEED * 2,
                color: baseColor,
                alpha,
            });
        }
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        const color = getThemeColor();
        ctx.filter = 'blur(8px)';

        for (const b of balls) {
            b.x += b.vx;
            b.y += b.vy;

            if (b.x - b.r < 0 || b.x + b.r > canvas.width) b.vx *= -1;
            if (b.y - b.r < 0 || b.y + b.r > canvas.height) b.vy *= -1;

            b.x = Math.max(b.r, Math.min(canvas.width - b.r, b.x));
            b.y = Math.max(b.r, Math.min(canvas.height - b.r, b.y));

            ctx.beginPath();
            ctx.arc(b.x, b.y, b.r, 0, Math.PI * 2);
            ctx.fillStyle = color;
            ctx.globalAlpha = b.alpha;
            ctx.fill();
            ctx.globalAlpha = 1;
        }

        animId = requestAnimationFrame(draw);
    }

    initBalls();
    draw();
})();

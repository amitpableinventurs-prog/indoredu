<div id="mm-game">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex gap-2" id="mm-level-select">
            <button type="button" data-level="easy" class="mm-level-btn px-3 py-1.5 text-sm font-medium rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200">Level 1</button>
            <button type="button" data-level="medium" class="mm-level-btn px-3 py-1.5 text-sm font-medium rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 ring-2 ring-indigo-500">Level 2</button>
            <button type="button" data-level="hard" class="mm-level-btn px-3 py-1.5 text-sm font-medium rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200">Level 3</button>
        </div>
        <div class="flex items-center gap-4 text-sm font-medium text-gray-700 dark:text-gray-200">
            <span>Moves: <span id="mm-moves">0</span></span>
            <span>Matches: <span id="mm-matches">0</span>/<span id="mm-total">8</span></span>
            <span>Time: <span id="mm-time">0</span>s</span>
        </div>
    </div>

    <div id="mm-start-screen" class="text-center py-10">
        <p class="text-gray-600 dark:text-gray-300 mb-5">Pick a level above, then flip two cards at a time and match each question to its answer. Fewer moves and less time means a higher score.</p>
        <x-primary-button type="button" id="mm-start-btn">Start Game</x-primary-button>
    </div>

    <div id="mm-board" class="hidden grid grid-cols-4 gap-2 sm:gap-3"></div>

    <div id="mm-end-screen" class="hidden text-center py-10">
        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">All matched! 🎉</p>
        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 my-2"><span id="mm-final-score">0</span> points</p>
        <p id="mm-new-best" class="text-amber-600 dark:text-amber-400 font-medium hidden">🎉 New personal best!</p>
        <x-primary-button type="button" id="mm-restart-btn" class="mt-4">Play Again</x-primary-button>
    </div>
</div>

<script>
(function () {
    const PAIR_SETS = {
        easy: [
            { a: '4 × 5', b: '20' },
            { a: '3 × 3', b: '9' },
            { a: 'H₂O', b: 'Water' },
            { a: 'Capital of India', b: 'New Delhi' },
            { a: 'Synonym of "Happy"', b: 'Joyful' },
            { a: 'Antonym of "Hot"', b: 'Cold' },
        ],
        medium: [
            { a: '7 × 8', b: '56' },
            { a: '9 × 6', b: '54' },
            { a: 'H₂O', b: 'Water' },
            { a: 'CO₂', b: 'Carbon Dioxide' },
            { a: 'Capital of India', b: 'New Delhi' },
            { a: 'Capital of Japan', b: 'Tokyo' },
            { a: 'Synonym of "Happy"', b: 'Joyful' },
            { a: 'Antonym of "Hot"', b: 'Cold' },
        ],
        hard: [
            { a: '7 × 8', b: '56' },
            { a: '9 × 6', b: '54' },
            { a: '12 × 11', b: '132' },
            { a: 'H₂O', b: 'Water' },
            { a: 'CO₂', b: 'Carbon Dioxide' },
            { a: 'NaCl', b: 'Salt' },
            { a: 'Capital of India', b: 'New Delhi' },
            { a: 'Capital of Japan', b: 'Tokyo' },
            { a: 'Capital of France', b: 'Paris' },
            { a: 'Synonym of "Happy"', b: 'Joyful' },
            { a: 'Antonym of "Hot"', b: 'Cold' },
            { a: 'Antonym of "Ancient"', b: 'Modern' },
        ],
    };

    const startScreen = document.getElementById('mm-start-screen');
    const board = document.getElementById('mm-board');
    const endScreen = document.getElementById('mm-end-screen');
    const movesEl = document.getElementById('mm-moves');
    const matchesEl = document.getElementById('mm-matches');
    const totalEl = document.getElementById('mm-total');
    const timeEl = document.getElementById('mm-time');
    const finalScoreEl = document.getElementById('mm-final-score');
    const newBestEl = document.getElementById('mm-new-best');
    const levelButtons = document.querySelectorAll('.mm-level-btn');

    let level = 'medium';
    let PAIRS = PAIR_SETS[level];
    let moves = 0;
    let matches = 0;
    let elapsed = 0;
    let timer = null;
    let lock = false;
    let flipped = [];

    levelButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            level = btn.dataset.level;
            levelButtons.forEach((b) => b.classList.remove('ring-2', 'ring-indigo-500'));
            btn.classList.add('ring-2', 'ring-indigo-500');
        });
    });

    function shuffle(arr) {
        const a = arr.slice();
        for (let i = a.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [a[i], a[j]] = [a[j], a[i]];
        }
        return a;
    }

    function buildCards() {
        const cards = [];
        PAIRS.forEach((pair, i) => {
            cards.push({ pairId: i, text: pair.a });
            cards.push({ pairId: i, text: pair.b });
        });
        return shuffle(cards);
    }

    function renderBoard() {
        board.innerHTML = '';
        buildCards().forEach((card, index) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.dataset.pairId = String(card.pairId);
            btn.dataset.text = card.text;
            btn.dataset.index = String(index);
            btn.className = 'mm-card aspect-square flex items-center justify-center text-center p-1.5 text-[11px] sm:text-xs font-semibold rounded-md border-2 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 select-none transition hover:border-indigo-400';
            btn.textContent = '?';
            btn.addEventListener('click', () => flipCard(btn));
            board.appendChild(btn);
        });
    }

    function flipCard(btn) {
        if (lock) return;
        if (btn.classList.contains('mm-matched') || btn.classList.contains('mm-flipped')) return;

        btn.classList.add('mm-flipped');
        btn.textContent = btn.dataset.text;
        btn.classList.add('border-indigo-400', 'text-gray-900', 'dark:text-gray-100');
        flipped.push(btn);

        if (flipped.length === 2) {
            moves++;
            movesEl.textContent = String(moves);
            lock = true;

            if (flipped[0].dataset.pairId === flipped[1].dataset.pairId) {
                flipped.forEach((c) => {
                    c.classList.add('mm-matched', 'border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-500/10', 'text-emerald-700', 'dark:text-emerald-300');
                });
                matches++;
                matchesEl.textContent = String(matches);
                flipped = [];
                lock = false;
                if (matches === PAIRS.length) endGame();
            } else {
                setTimeout(() => {
                    flipped.forEach((c) => {
                        c.classList.remove('mm-flipped', 'border-indigo-400', 'text-gray-900', 'dark:text-gray-100');
                        c.textContent = '?';
                    });
                    flipped = [];
                    lock = false;
                }, 700);
            }
        }
    }

    function startGame() {
        PAIRS = PAIR_SETS[level];
        moves = 0;
        matches = 0;
        elapsed = 0;
        flipped = [];
        lock = false;
        movesEl.textContent = '0';
        matchesEl.textContent = '0';
        totalEl.textContent = String(PAIRS.length);
        timeEl.textContent = '0';

        startScreen.classList.add('hidden');
        endScreen.classList.add('hidden');
        board.classList.remove('hidden');
        renderBoard();

        clearInterval(timer);
        timer = setInterval(() => {
            elapsed++;
            timeEl.textContent = String(elapsed);
        }, 1000);
    }

    async function endGame() {
        clearInterval(timer);
        board.classList.add('hidden');
        endScreen.classList.remove('hidden');
        newBestEl.classList.add('hidden');

        const extraMoves = Math.max(0, moves - PAIRS.length);
        const score = Math.max(50, Math.min(1000, 1000 - extraMoves * 40 - elapsed * 3));
        finalScoreEl.textContent = String(score);

        const prevBest = parseInt(document.getElementById('game-best-score').textContent, 10) || 0;
        const result = await saveGameScore(score);
        if (result && score > prevBest) {
            newBestEl.classList.remove('hidden');
        }
    }

    document.getElementById('mm-start-btn').addEventListener('click', startGame);
    document.getElementById('mm-restart-btn').addEventListener('click', startGame);
})();
</script>

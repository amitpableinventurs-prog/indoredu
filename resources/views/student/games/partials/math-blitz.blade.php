<div id="mb-game">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex gap-2" id="mb-difficulty">
            <button type="button" data-diff="easy" class="mb-diff-btn px-3 py-1.5 text-sm font-medium rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200">Easy</button>
            <button type="button" data-diff="medium" class="mb-diff-btn px-3 py-1.5 text-sm font-medium rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 ring-2 ring-indigo-500">Medium</button>
            <button type="button" data-diff="hard" class="mb-diff-btn px-3 py-1.5 text-sm font-medium rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200">Hard</button>
        </div>
        <div class="flex items-center gap-4 text-sm font-medium text-gray-700 dark:text-gray-200">
            <span>Level: <span id="mb-level">1</span></span>
            <span>Score: <span id="mb-score">0</span></span>
            <span>Time left: <span id="mb-time">60</span>s</span>
        </div>
    </div>

    <div id="mb-start-screen" class="text-center py-10">
        <p class="text-gray-600 dark:text-gray-300 mb-5">Pick a difficulty above, then solve as many problems as you can in 60 seconds.</p>
        <x-primary-button type="button" id="mb-start-btn">Start Game</x-primary-button>
    </div>

    <div id="mb-play-screen" class="hidden text-center py-8">
        <p id="mb-question" class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-6">?</p>
        <input id="mb-answer" type="number" inputmode="numeric" autocomplete="off"
               class="w-40 text-center text-2xl rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
        <p id="mb-feedback" class="mt-3 text-sm h-5"></p>
    </div>

    <div id="mb-end-screen" class="hidden text-center py-10">
        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">Time's up!</p>
        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 my-2"><span id="mb-final-score">0</span> correct</p>
        <p id="mb-new-best" class="text-amber-600 dark:text-amber-400 font-medium hidden">🎉 New personal best!</p>
        <x-primary-button type="button" id="mb-restart-btn" class="mt-4">Play Again</x-primary-button>
    </div>
</div>

<script>
(function () {
    const startScreen = document.getElementById('mb-start-screen');
    const playScreen = document.getElementById('mb-play-screen');
    const endScreen = document.getElementById('mb-end-screen');
    const scoreEl = document.getElementById('mb-score');
    const levelEl = document.getElementById('mb-level');
    const timeEl = document.getElementById('mb-time');
    const questionEl = document.getElementById('mb-question');
    const answerInput = document.getElementById('mb-answer');
    const feedback = document.getElementById('mb-feedback');
    const finalScoreEl = document.getElementById('mb-final-score');
    const newBestEl = document.getElementById('mb-new-best');
    const diffButtons = document.querySelectorAll('.mb-diff-btn');

    const MAX_LEVEL = 5;
    const LEVEL_UP_EVERY = 5;

    let difficulty = 'medium';
    let score = 0;
    let level = 1;
    let timeLeft = 60;
    let timer = null;
    let currentAnswer = 0;

    diffButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            difficulty = btn.dataset.diff;
            diffButtons.forEach((b) => b.classList.remove('ring-2', 'ring-indigo-500'));
            btn.classList.add('ring-2', 'ring-indigo-500');
        });
    });

    function randInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    function updateLevel() {
        const next = Math.min(MAX_LEVEL, 1 + Math.floor(score / LEVEL_UP_EVERY));
        if (next !== level) {
            level = next;
            levelEl.textContent = String(level);
        }
    }

    function nextQuestion() {
        updateLevel();

        const ranges = { easy: 10, medium: 30, hard: 100 };
        const mulCaps = { easy: 6, medium: 10, hard: 12 };
        const boost = level - 1;
        const max = ranges[difficulty] + boost * Math.round(ranges[difficulty] * 0.3);
        const op = ['+', '−', '×'][randInt(0, 2)];
        let a, b;

        if (op === '×') {
            const mulCap = mulCaps[difficulty] + boost * 2;
            a = randInt(2, mulCap);
            b = randInt(2, mulCap);
        } else {
            a = randInt(1, max);
            b = randInt(1, max);
            if (op === '−' && b > a) { [a, b] = [b, a]; }
        }

        currentAnswer = op === '+' ? a + b : op === '−' ? a - b : a * b;
        questionEl.textContent = `${a} ${op} ${b}`;
    }

    function checkAnswer() {
        if (answerInput.value.trim() === '') return;
        const val = parseInt(answerInput.value, 10);
        if (Number.isNaN(val)) return;

        if (val === currentAnswer) {
            score++;
            scoreEl.textContent = score;
            feedback.textContent = '✓ Correct';
            feedback.className = 'mt-3 text-sm h-5 text-emerald-600 dark:text-emerald-400';
        } else {
            feedback.textContent = `✗ It was ${currentAnswer}`;
            feedback.className = 'mt-3 text-sm h-5 text-rose-600 dark:text-rose-400';
        }

        answerInput.value = '';
        nextQuestion();
    }

    answerInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') checkAnswer();
    });

    function startGame() {
        score = 0;
        level = 1;
        timeLeft = 60;
        scoreEl.textContent = '0';
        levelEl.textContent = '1';
        timeEl.textContent = '60';
        feedback.textContent = '';
        startScreen.classList.add('hidden');
        endScreen.classList.add('hidden');
        playScreen.classList.remove('hidden');
        nextQuestion();
        answerInput.value = '';
        answerInput.focus();

        clearInterval(timer);
        timer = setInterval(() => {
            timeLeft--;
            timeEl.textContent = String(timeLeft);
            if (timeLeft <= 0) endGame();
        }, 1000);
    }

    async function endGame() {
        clearInterval(timer);
        playScreen.classList.add('hidden');
        endScreen.classList.remove('hidden');
        newBestEl.classList.add('hidden');
        finalScoreEl.textContent = String(score);

        const prevBest = parseInt(document.getElementById('game-best-score').textContent, 10) || 0;
        const result = await saveGameScore(score);
        if (result && score > prevBest) {
            newBestEl.classList.remove('hidden');
        }
    }

    document.getElementById('mb-start-btn').addEventListener('click', startGame);
    document.getElementById('mb-restart-btn').addEventListener('click', startGame);
})();
</script>

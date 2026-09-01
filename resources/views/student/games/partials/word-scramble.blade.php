<div id="ws-game">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6 text-sm font-medium text-gray-700 dark:text-gray-200">
        <span>Level: <span id="ws-level">1</span></span>
        <span>Score: <span id="ws-score">0</span></span>
        <span>Time left: <span id="ws-time">90</span>s</span>
    </div>

    <div id="ws-start-screen" class="text-center py-10">
        <p class="text-gray-600 dark:text-gray-300 mb-5">Unscramble as many school-subject words as you can in 90 seconds. Stuck? Use a hint.</p>
        <x-primary-button type="button" id="ws-start-btn">Start Game</x-primary-button>
    </div>

    <div id="ws-play-screen" class="hidden text-center py-8">
        <p class="text-xs uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-2" id="ws-category">Subject</p>
        <p id="ws-scrambled" class="text-3xl sm:text-4xl font-bold tracking-widest text-gray-900 dark:text-gray-100 mb-6">?</p>
        <input id="ws-answer" type="text" autocomplete="off" autocapitalize="off" spellcheck="false"
               class="w-64 text-center text-xl rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 uppercase">
        <p id="ws-feedback" class="mt-3 text-sm h-5"></p>
        <div class="mt-3 flex items-center justify-center gap-3">
            <button type="button" id="ws-hint-btn" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">Show hint</button>
            <button type="button" id="ws-skip-btn" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:underline">Skip word</button>
        </div>
    </div>

    <div id="ws-end-screen" class="hidden text-center py-10">
        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">Time's up!</p>
        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 my-2"><span id="ws-final-score">0</span> points</p>
        <p id="ws-new-best" class="text-amber-600 dark:text-amber-400 font-medium hidden">🎉 New personal best!</p>
        <x-primary-button type="button" id="ws-restart-btn" class="mt-4">Play Again</x-primary-button>
    </div>
</div>

<script>
(function () {
    const WORDS = [
        { word: 'FRACTION', hint: 'A part of a whole, like 1/2', category: 'Math' },
        { word: 'TRIANGLE', hint: 'A shape with three sides', category: 'Math' },
        { word: 'EQUATION', hint: 'A math statement with an equals sign', category: 'Math' },
        { word: 'PERIMETER', hint: 'The distance around a shape', category: 'Math' },
        { word: 'MULTIPLY', hint: 'To repeatedly add a number', category: 'Math' },
        { word: 'PHOTOSYNTHESIS', hint: 'How plants make food from sunlight', category: 'Science' },
        { word: 'GRAVITY', hint: 'The force that pulls objects down', category: 'Science' },
        { word: 'MOLECULE', hint: 'Two or more atoms bonded together', category: 'Science' },
        { word: 'ECOSYSTEM', hint: 'Living things and their environment together', category: 'Science' },
        { word: 'PLANET', hint: 'Earth is one of these', category: 'Science' },
        { word: 'CONTINENT', hint: 'A large landmass, like Asia or Africa', category: 'Geography' },
        { word: 'EQUATOR', hint: 'The imaginary line around the middle of Earth', category: 'Geography' },
        { word: 'CAPITAL', hint: 'The main city of a country', category: 'Geography' },
        { word: 'GLACIER', hint: 'A slow-moving river of ice', category: 'Geography' },
        { word: 'METAPHOR', hint: 'A comparison without using "like" or "as"', category: 'English' },
        { word: 'SYNONYM', hint: 'A word that means the same as another', category: 'English' },
        { word: 'GRAMMAR', hint: 'The rules for how a language is put together', category: 'English' },
        { word: 'PARAGRAPH', hint: 'A group of related sentences', category: 'English' },
        { word: 'DEMOCRACY', hint: 'A government elected by the people', category: 'Social Studies' },
        { word: 'CONSTITUTION', hint: 'A country\'s written set of founding rules', category: 'Social Studies' },
    ];

    const SHORT_WORDS = WORDS.filter((w) => w.word.length <= 8);
    const MEDIUM_WORDS = WORDS.filter((w) => w.word.length > 8 && w.word.length <= 10);
    const LONG_WORDS = WORDS.filter((w) => w.word.length > 10);
    const MAX_LEVEL = 3;
    const LEVEL_UP_EVERY = 3;

    const startScreen = document.getElementById('ws-start-screen');
    const playScreen = document.getElementById('ws-play-screen');
    const endScreen = document.getElementById('ws-end-screen');
    const scoreEl = document.getElementById('ws-score');
    const levelEl = document.getElementById('ws-level');
    const timeEl = document.getElementById('ws-time');
    const scrambledEl = document.getElementById('ws-scrambled');
    const categoryEl = document.getElementById('ws-category');
    const answerInput = document.getElementById('ws-answer');
    const feedback = document.getElementById('ws-feedback');
    const finalScoreEl = document.getElementById('ws-final-score');
    const newBestEl = document.getElementById('ws-new-best');
    const hintBtn = document.getElementById('ws-hint-btn');
    const skipBtn = document.getElementById('ws-skip-btn');

    let score = 0;
    let wordsCorrect = 0;
    let level = 1;
    let timeLeft = 90;
    let timer = null;
    let current = null;

    function shuffle(arr) {
        const a = arr.slice();
        for (let i = a.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [a[i], a[j]] = [a[j], a[i]];
        }
        return a;
    }

    function scramble(word) {
        let letters;
        do {
            letters = shuffle(word.split(''));
        } while (letters.join('') === word && word.length > 1);
        return letters.join('');
    }

    function updateLevel() {
        const next = Math.min(MAX_LEVEL, 1 + Math.floor(wordsCorrect / LEVEL_UP_EVERY));
        if (next !== level) {
            level = next;
            levelEl.textContent = String(level);
        }
    }

    function poolForLevel() {
        let pool = SHORT_WORDS;
        if (level >= 2) pool = pool.concat(MEDIUM_WORDS);
        if (level >= 3) pool = pool.concat(LONG_WORDS);
        return pool;
    }

    function nextWord() {
        updateLevel();
        const pool = poolForLevel();
        let pick;
        do {
            pick = pool[Math.floor(Math.random() * pool.length)];
        } while (pool.length > 1 && current && pick.word === current.word);

        current = pick;
        categoryEl.textContent = current.category;
        scrambledEl.textContent = scramble(current.word);
        hintBtn.disabled = false;
        hintBtn.textContent = 'Show hint';
        feedback.textContent = '';
        answerInput.value = '';
    }

    function checkAnswer() {
        if (!current || answerInput.value.trim() === '') return;
        const guess = answerInput.value.trim().toUpperCase();

        if (guess === current.word) {
            score += 10 * level;
            wordsCorrect++;
            scoreEl.textContent = String(score);
            feedback.textContent = '✓ Correct!';
            feedback.className = 'mt-3 text-sm h-5 text-emerald-600 dark:text-emerald-400';
            nextWord();
        } else {
            feedback.textContent = 'Not quite — keep trying';
            feedback.className = 'mt-3 text-sm h-5 text-rose-600 dark:text-rose-400';
        }
    }

    answerInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') checkAnswer();
    });

    hintBtn.addEventListener('click', () => {
        if (!current) return;
        feedback.textContent = `Hint: ${current.hint}`;
        feedback.className = 'mt-3 text-sm h-5 text-gray-500 dark:text-gray-400';
    });

    skipBtn.addEventListener('click', () => {
        feedback.textContent = current ? `Skipped — it was ${current.word}` : '';
        feedback.className = 'mt-3 text-sm h-5 text-gray-500 dark:text-gray-400';
        nextWord();
    });

    function startGame() {
        score = 0;
        wordsCorrect = 0;
        level = 1;
        current = null;
        timeLeft = 90;
        scoreEl.textContent = '0';
        levelEl.textContent = '1';
        timeEl.textContent = '90';
        startScreen.classList.add('hidden');
        endScreen.classList.add('hidden');
        playScreen.classList.remove('hidden');
        nextWord();
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

    document.getElementById('ws-start-btn').addEventListener('click', startGame);
    document.getElementById('ws-restart-btn').addEventListener('click', startGame);
})();
</script>

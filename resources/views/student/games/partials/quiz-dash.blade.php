<div id="qd-game">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6 text-sm font-medium text-gray-700 dark:text-gray-200">
        <span>Question: <span id="qd-progress">0</span>/10</span>
        <span>Score: <span id="qd-score">0</span></span>
        <span>Time left: <span id="qd-time">15</span>s</span>
    </div>

    <div id="qd-start-screen" class="text-center py-10">
        <p class="text-gray-600 dark:text-gray-300 mb-5">Answer 10 quick-fire questions across math, science and general knowledge. The faster you answer, the more points you earn.</p>
        <x-primary-button type="button" id="qd-start-btn">Start Game</x-primary-button>
    </div>

    <div id="qd-play-screen" class="hidden">
        <p class="text-xs uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-2 text-center" id="qd-subject">Subject</p>
        <p id="qd-question" class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6 text-center">Question?</p>
        <div id="qd-options" class="grid sm:grid-cols-2 gap-3"></div>
    </div>

    <div id="qd-end-screen" class="hidden text-center py-10">
        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">Round complete!</p>
        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 my-2"><span id="qd-final-score">0</span> points</p>
        <p class="text-sm text-gray-500 dark:text-gray-400"><span id="qd-correct-count">0</span>/10 correct</p>
        <p id="qd-new-best" class="text-amber-600 dark:text-amber-400 font-medium mt-1 hidden">🎉 New personal best!</p>
        <x-primary-button type="button" id="qd-restart-btn" class="mt-4">Play Again</x-primary-button>
    </div>
</div>

<script>
(function () {
    const QUESTIONS = [
        { q: 'What is 12 × 8?', options: ['96', '86', '108', '92'], correct: 0, subject: 'Math' },
        { q: 'What is the square root of 81?', options: ['7', '8', '9', '11'], correct: 2, subject: 'Math' },
        { q: 'How many sides does a hexagon have?', options: ['5', '6', '7', '8'], correct: 1, subject: 'Math' },
        { q: 'What is 15% of 200?', options: ['20', '25', '30', '35'], correct: 2, subject: 'Math' },
        { q: 'Which planet is known as the Red Planet?', options: ['Venus', 'Mars', 'Jupiter', 'Saturn'], correct: 1, subject: 'Science' },
        { q: 'What gas do plants absorb from the air?', options: ['Oxygen', 'Nitrogen', 'Carbon Dioxide', 'Hydrogen'], correct: 2, subject: 'Science' },
        { q: 'What is the chemical symbol for gold?', options: ['Ag', 'Au', 'Gd', 'Go'], correct: 1, subject: 'Science' },
        { q: 'How many bones are in the adult human body?', options: ['186', '206', '226', '246'], correct: 1, subject: 'Science' },
        { q: 'What is the powerhouse of the cell?', options: ['Nucleus', 'Ribosome', 'Mitochondria', 'Golgi body'], correct: 2, subject: 'Science' },
        { q: 'Which is the longest river in the world?', options: ['Amazon', 'Nile', 'Yangtze', 'Mississippi'], correct: 1, subject: 'Geography' },
        { q: 'Which is the largest desert in the world?', options: ['Sahara', 'Gobi', 'Antarctic', 'Kalahari'], correct: 2, subject: 'Geography' },
        { q: 'What is the capital of Australia?', options: ['Sydney', 'Melbourne', 'Canberra', 'Perth'], correct: 2, subject: 'Geography' },
        { q: 'Which country has the largest population?', options: ['USA', 'India', 'China', 'Indonesia'], correct: 1, subject: 'Geography' },
        { q: 'Who wrote the play "Romeo and Juliet"?', options: ['Charles Dickens', 'William Shakespeare', 'Mark Twain', 'Leo Tolstoy'], correct: 1, subject: 'English' },
        { q: 'What is a synonym for "enormous"?', options: ['Tiny', 'Huge', 'Quiet', 'Fast'], correct: 1, subject: 'English' },
        { q: 'Which word is a noun in this list?', options: ['Quickly', 'Beautiful', 'Freedom', 'Run'], correct: 2, subject: 'English' },
        { q: 'In which year did India gain independence?', options: ['1945', '1947', '1950', '1952'], correct: 1, subject: 'Social Studies' },
        { q: 'Who is known as the Father of the Nation in India?', options: ['Jawaharlal Nehru', 'Subhas Chandra Bose', 'Mahatma Gandhi', 'B. R. Ambedkar'], correct: 2, subject: 'Social Studies' },
        { q: 'How many continents are there on Earth?', options: ['5', '6', '7', '8'], correct: 2, subject: 'Geography' },
        { q: 'What is the freezing point of water in Celsius?', options: ['-1°C', '0°C', '1°C', '32°C'], correct: 1, subject: 'Science' },
    ];

    const startScreen = document.getElementById('qd-start-screen');
    const playScreen = document.getElementById('qd-play-screen');
    const endScreen = document.getElementById('qd-end-screen');
    const progressEl = document.getElementById('qd-progress');
    const scoreEl = document.getElementById('qd-score');
    const timeEl = document.getElementById('qd-time');
    const subjectEl = document.getElementById('qd-subject');
    const questionEl = document.getElementById('qd-question');
    const optionsEl = document.getElementById('qd-options');
    const finalScoreEl = document.getElementById('qd-final-score');
    const correctCountEl = document.getElementById('qd-correct-count');
    const newBestEl = document.getElementById('qd-new-best');

    const ROUND_SIZE = 10;
    const QUESTION_SECONDS = 15;

    let round = [];
    let index = 0;
    let score = 0;
    let correctCount = 0;
    let timeLeft = QUESTION_SECONDS;
    let timer = null;
    let answered = false;

    function shuffle(arr) {
        const a = arr.slice();
        for (let i = a.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [a[i], a[j]] = [a[j], a[i]];
        }
        return a;
    }

    function showQuestion() {
        answered = false;
        timeLeft = QUESTION_SECONDS;
        timeEl.textContent = String(timeLeft);
        progressEl.textContent = String(index + 1);

        const item = round[index];
        subjectEl.textContent = item.subject;
        questionEl.textContent = item.q;
        optionsEl.innerHTML = '';

        item.options.forEach((opt, i) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = opt;
            btn.className = 'qd-option px-4 py-3 text-sm font-medium text-left rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition';
            btn.addEventListener('click', () => selectAnswer(i));
            optionsEl.appendChild(btn);
        });

        clearInterval(timer);
        timer = setInterval(() => {
            timeLeft--;
            timeEl.textContent = String(timeLeft);
            if (timeLeft <= 0) selectAnswer(-1);
        }, 1000);
    }

    function selectAnswer(choice) {
        if (answered) return;
        answered = true;
        clearInterval(timer);

        const item = round[index];
        const buttons = optionsEl.querySelectorAll('.qd-option');
        buttons.forEach((btn, i) => {
            btn.disabled = true;
            if (i === item.correct) {
                btn.classList.add('border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-500/10', 'text-emerald-700', 'dark:text-emerald-300');
            } else if (i === choice) {
                btn.classList.add('border-rose-500', 'bg-rose-50', 'dark:bg-rose-500/10', 'text-rose-700', 'dark:text-rose-300');
            }
        });

        if (choice === item.correct) {
            correctCount++;
            score += 10 + timeLeft;
            scoreEl.textContent = String(score);
        }

        setTimeout(() => {
            index++;
            if (index >= round.length) {
                endGame();
            } else {
                showQuestion();
            }
        }, 900);
    }

    function startGame() {
        round = shuffle(QUESTIONS).slice(0, ROUND_SIZE);
        index = 0;
        score = 0;
        correctCount = 0;
        scoreEl.textContent = '0';

        startScreen.classList.add('hidden');
        endScreen.classList.add('hidden');
        playScreen.classList.remove('hidden');
        showQuestion();
    }

    async function endGame() {
        clearInterval(timer);
        playScreen.classList.add('hidden');
        endScreen.classList.remove('hidden');
        newBestEl.classList.add('hidden');
        finalScoreEl.textContent = String(score);
        correctCountEl.textContent = String(correctCount);

        const prevBest = parseInt(document.getElementById('game-best-score').textContent, 10) || 0;
        const result = await saveGameScore(score);
        if (result && score > prevBest) {
            newBestEl.classList.remove('hidden');
        }
    }

    document.getElementById('qd-start-btn').addEventListener('click', startGame);
    document.getElementById('qd-restart-btn').addEventListener('click', startGame);
})();
</script>

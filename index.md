<html lang="fr">
<head>
    <meta charset="UTF-8">

        .container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 600px;
        }

        .screen {
            display: none;
        }

        .screen.active {
            display: block;
        }

        h1 {
            color: #6366f1;
            margin-bottom: 2rem;
            font-size: 1.5rem;
            text-align: center;
        }

        .player-inputs {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .input-field {
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 1rem;
            width: 100%;
            box-sizing: border-box;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }

        .add-player-btn {
            background: none;
            border: none;
            color: #6366f1;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
        }

        .add-player-btn::before {
            content: "+";
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }

        .btn {
            background-color: #818cf8;
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.2s;
        }

        .btn:hover {
            background-color: #6366f1;
        }

        .question-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .points {
            color: #6366f1;
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .options {
            display: grid;
            gap: 1rem;
            margin-top: 1rem;
        }

        .option {
            background-color: #f8fafc;
            border: 2px solid #e2e8f0;
            padding: 1rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .option:hover {
            border-color: #818cf8;
            background-color: #f1f5f9;
        }

        .option.selected {
            border-color: #818cf8;
            background-color: #e0e7ff;
        }

        .option.correct {
            background-color: #86efac;
            border-color: #22c55e;
        }

        .option.wrong {
            background-color: #fca5a5;
            border-color: #ef4444;
        }

        .score-display {
            text-align: center;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .player-score {
            margin: 0.5rem 0;
            padding: 0.5rem;
            background-color: #f1f5f9;
            border-radius: 6px;
        }

        .current-player {
            color: #6366f1;
            font-weight: bold;
            margin-bottom: 1rem;
            text-align: center;
        }

        .difficulty {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            color: white;
        }

        .difficulty.easy {
            background-color: rgb(103, 218, 145);
        }

        .difficulty.medium {
            background-color: rgb(31, 163, 80);
        }

        .difficulty.hard {
            background-color: rgb(17, 68, 36);
        }

        .submit-btn {
            margin-top: 1rem;
            width: 100%;
        }

        .selected-count {
            color: #6366f1;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Écran de configuration -->
        <div id="configScreen" class="screen active">
            <h1>Configuration des Joueurs</h1>
            <form id="playerForm">
                <div class="player-inputs" id="playerInputs">
                    <input type="text" class="input-field" placeholder="Nom du joueur 1" required>
                </div>
                <div class="button-container">
                    <button type="button" class="add-player-btn" id="addPlayerBtn">
                        Ajouter un joueur
                    </button>
                    <button type="submit" class="btn">Commencer</button>
                </div>
            </form>
        </div>

        <!-- Écran de jeu -->
        <div id="gameScreen" class="screen">
            <div class="score-display" id="scoreDisplay"></div>
            <div class="current-player" id="currentPlayer"></div>
            <div class="question-container">
                <div class="points" id="questionPoints"></div>
                <div id="questionText"></div>
                <div id="selectedCount" class="selected-count"></div>
                <div class="options" id="options"></div>
                <button class="btn submit-btn" id="submitAnswer">Valider les réponses</button>
            </div>
        </div>

        <!-- Écran de fin -->
        <div id="endScreen" class="screen">
            <h1>Fin de la partie!</h1>
            <div id="finalScores"></div>
            <div class="button-container">
                <button class="btn" onclick="resetGame()">Nouvelle Partie</button>
            </div>
        </div>
    </div>

    <script>
        // Questions du quiz avec réponses multiples
        const questions = [
            {
                question: "Cite une boisson de petit-dejeuner",
                options: [
                    { text: "Thé", correct: true, difficulty: "easy", points: 6 },
                    { text: "Jus de Pomme", correct: true, difficulty: "easy", points: 14 },
                    { text: "Eau", correct: true, difficulty: "medium", points: 15 },
                    { text: "Lait", correct: true, difficulty: "medium", points: 18 },
                    { text: "Café", correct: true, difficulty: "hard", points: 19 },
                    { text: "Jus d'orange", correct: true, difficulty: "hard", points: 28 }
                ],
                minRequired: 2,
                maxSelectable: 4
            },
            {
                question: "Cite un fruit jaune",
                options: [
                    { text: "Pomme", correct: true, difficulty: "easy", points: 11 },
                    { text: "Mangue", correct: true, difficulty: "easy", points: 11 },
                    { text: "Ananas", correct: true, difficulty: "easy", points: 12 },
                    { text: "Citron", correct: true, difficulty: "medium", points: 24 },
                    { text: "Banane", correct: true, difficulty: "hard", points: 34 }
                ],
                minRequired: 1,
                maxSelectable: 2
            },
            {
                question: "Quelles planètes du système solaire ont des anneaux?",
                options: [
                    { text: "Saturne", correct: true, difficulty: "easy", points: 50 },
                    { text: "Jupiter", correct: true, difficulty: "medium", points: 100 },
                    { text: "Uranus", correct: true, difficulty: "hard", points: 150 },
                    { text: "Neptune", correct: true, difficulty: "hard", points: 150 },
                    { text: "Mars", correct: false, difficulty: "medium", points: 100 }
                ],
                minRequired: 1,
                maxSelectable: 4
            }
            
        ];

        let players = [];
        let scores = {};
        let currentQuestionIndex = 0;
        let currentPlayerIndex = 0;
        let selectedAnswers = new Set();

        // Gestion de la configuration des joueurs
        const playerInputs = document.getElementById('playerInputs');
        const addPlayerBtn = document.getElementById('addPlayerBtn');
        const playerForm = document.getElementById('playerForm');
        const submitAnswerBtn = document.getElementById('submitAnswer');
        let playerCount = 1;

        addPlayerBtn.addEventListener('click', () => {
            playerCount++;
            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'input-field';
            input.placeholder = `Nom du joueur ${playerCount}`;
            input.required = true;
            playerInputs.appendChild(input);
        });

        playerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            players = Array.from(playerInputs.getElementsByTagName('input'))
                .map(input => input.value);
            
            players.forEach(player => scores[player] = 0);
            
            showGameScreen();
            displayQuestion();
        });

        function showScreen(screenId) {
            document.querySelectorAll('.screen').forEach(screen => screen.classList.remove('active'));
            document.getElementById(screenId).classList.add('active');
        }

        function showGameScreen() {
            showScreen('gameScreen');
            updateScoreDisplay();
        }

        function updateScoreDisplay() {
            const scoreHtml = Object.entries(scores)
                .map(([player, score]) => `<div class="player-score">${player}: ${score} points</div>`)
                .join('');
            document.getElementById('scoreDisplay').innerHTML = scoreHtml;
            document.getElementById('currentPlayer').textContent = `Au tour de ${players[currentPlayerIndex]}`;
        }

        function displayQuestion() {
            if (currentQuestionIndex >= questions.length) {
                showEndScreen();
                return;
            }

            selectedAnswers.clear();
            const question = questions[currentQuestionIndex];
            document.getElementById('questionText').textContent = question.question;
            document.getElementById('questionPoints').textContent = 
                `Sélectionnez entre ${question.minRequired} et ${question.maxSelectable} réponses`;
            
            const optionsHtml = question.options
                .map((option, index) => `
                    <div class="option" data-index="${index}">
                        <span>${option.text}</span>
                        <span class="difficulty ${option.difficulty}">${option.points} pts</span>
                    </div>
                `).join('');
            document.getElementById('options').innerHTML = optionsHtml;

            // Ajouter les événements de clic sur les options
            document.querySelectorAll('.option').forEach(option => {
                option.addEventListener('click', () => toggleAnswer(parseInt(option.dataset.index)));
            });

            updateSelectedCount();
            submitAnswerBtn.disabled = true;
        }

        function toggleAnswer(index) {
            const question = questions[currentQuestionIndex];
            
            if (selectedAnswers.has(index)) {
                selectedAnswers.delete(index);
                document.querySelector(`.option[data-index="${index}"]`).classList.remove('selected');
            } else if (selectedAnswers.size < question.maxSelectable) {
                selectedAnswers.add(index);
                document.querySelector(`.option[data-index="${index}"]`).classList.add('selected');
            }

            submitAnswerBtn.disabled = selectedAnswers.size < question.minRequired;
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const question = questions[currentQuestionIndex];
            document.getElementById('selectedCount').textContent = 
                `${selectedAnswers.size} réponse(s) sélectionnée(s) sur ${question.maxSelectable} maximum`;
        }

        submitAnswerBtn.addEventListener('click', checkAnswers);

        function checkAnswers() {
            const question = questions[currentQuestionIndex];
            const options = document.getElementsByClassName('option');
            let pointsEarned = 0;

            // Désactiver les clics
            Array.from(options).forEach(option => {
                option.style.pointerEvents = 'none';
            });

            // Vérifier chaque réponse
            selectedAnswers.forEach(index => {
                const option = question.options[index];
                if (option.correct) {
                    pointsEarned += option.points;
                    options[index].classList.add('correct');
                } else {
                    pointsEarned = Math.max(0, pointsEarned - option.points);
                    options[index].classList.add('wrong');
                }
            });

            // Montrer les réponses correctes non sélectionnées
            question.options.forEach((option, index) => {
                if (option.correct && !selectedAnswers.has(index)) {
                    options[index].classList.add('correct');
                }
            });

            // Ajouter les points au score du joueur
            scores[players[currentPlayerIndex]] += pointsEarned;

            // Passer au joueur/question suivant après un délai
            setTimeout(() => {
                currentPlayerIndex = (currentPlayerIndex + 1) % players.length;
                if (currentPlayerIndex === 0) {
                    currentQuestionIndex++;
                }
                displayQuestion();
                updateScoreDisplay();
            }, 2000);
        }

        function showEndScreen() {
            showScreen('endScreen');
            const sortedScores = Object.entries(scores)
                .sort(([,a], [,b]) => b - a)
                .map(([player, score], index) => `
                    <div class="player-score">
                        ${index + 1}. ${player}: ${score} points
                    </div>
                `).join('');
            document.getElementById('finalScores').innerHTML = sortedScores;
        }

        function resetGame() {
            currentQuestionIndex = 0;
            currentPlayerIndex = 0;
            scores = {};
            selectedAnswers.clear();
            showScreen('configScreen');
            playerInputs.innerHTML = '<input type="text" class="input-field" placeholder="Nom du joueur 1" required>';
            playerCount = 1;
        }
    </script>
</body>

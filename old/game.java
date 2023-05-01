package old;
<!-- game.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browser-based Multiplayer RPG</title>
    <style>
        #game-container {
            width: 800px;
            height: 600px;
            border: 1px solid black;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div id="game-container">
        <form id="character-form">
            <input type="text" id="name" placeholder="Enter character name">
            <button type="submit">Create Character</button>
        </form>
        <div id="game-content" style="display:none;">
            <p id="greeting"></p>
            <button id="talk-to-npc">Talk to NPC</button>
        </div>
    </div>

    <script>
        // character.js
        class Character {
            constructor(name, health, attack, defense) {
                this.name = name;
                this.health = health;
                this.attack = attack;
                this.defense = defense;
            }
        }

        // npc.js
        class NPC {
            constructor(name, dialogue) {
                this.name = name;
                this.dialogue = dialogue;
            }

            talk() {
                console.log(`${this.name}: ${this.dialogue}`);
            }
        }

        const npc = new NPC("Old Man", "Welcome to our village!");

        document.getElementById('character-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const name = document.getElementById('name').value;

            const response = await fetch('game.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `name=${encodeURIComponent(name)}`
            });

            const result = await response.text();
            if (result === "Character created successfully") {
                const character = new Character(name, 100, 10, 5);
                document.getElementById('character-form').style.display = 'none';
                document.getElementById('game-content').style.display = 'block';
                document.getElementById('greeting').innerHTML = `Hello, ${character.name}!`;
            } else {
                console.error(result);
            }
        });

        document.getElementById('talk-to-npc').addEventListener('click', () => {
            npc.talk();
        });
    </script>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Replace 'your_database_name' with the actual database name
    $conn = new mysqli('localhost', 'username', 'password', 'your_database_name');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $name = $_POST['name'];
    $health = 100;
    $attack = 10;
    $defense = 5;

    $sql = "INSERT INTO characters (name, health, attack, defense) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siii", $name, $health, $attack, $defense);
    if ($stmt->execute()) {
        echo "Character created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
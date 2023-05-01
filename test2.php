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
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-around;
        }

        #game-content {
            width: 100%;
            display: flex;
            justify-content: space-around;
        }

        #chat-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #chat-area {
            width: 100%;
            height: 200px;
            overflow-y: scroll;
        }

        #player-list {
            width: 100%;
            height: 200px;
            overflow-y: scroll;
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
            <div id="npc-container">
                <p id="npc-name"></p>
                <p id="npc-dialogue"></p>
                <button id="talk-to-npc">Talk to NPC</button>
                <button id="fight-npc">Fight NPC</button>
            </div>
            <div id="player-list">
                <h3>Players Online</h3>
                <ul id="online-players"></ul>
            </div>
        </div>
        <div id="chat-container" style="display:none;">
            <h3>Chat</h3>
            <div id="chat-area"></div>
            <form id="chat-form">
                <input type="text" id="chat-message" placeholder="Type your message">
                <button type="submit">Send</button>
            </form>
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

            takeDamage(damage) {
                this.health -= damage;
                if (this.health < 0) {
                    this.health = 0;
                }
            }
        }

        // npc.js
        // npc.js
class NPC {
    constructor(name, dialogue, health, attack, defense) {
        this.name = name;
        this.dialogue = dialogue;
        this.health = health;
        this.attack = attack;
        this.defense = defense;
    }

    talk() {
        console.log(`${this.name}: ${this.dialogue}`);
    }

    takeDamage(damage) {
        this.health -= damage;
        if (this.health < 0) {
            this.health = 0;
        }
    }
}
const npc = new NPC("Old Man", "Welcome to our village!", 50, 8, 4);

async function fetchOnlinePlayers() {
    const response = await fetch('getOnlinePlayers.php');
    const players = await response.json();
    const onlinePlayersList = document.getElementById('online-players');
    onlinePlayersList.innerHTML = '';

    players.forEach(player => {
        const li = document.createElement('li');
        li.textContent = player.name;
        onlinePlayersList.appendChild(li);
    });
}

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
        document.getElementById('chat-container').style.display = 'block';
        document.getElementById('greeting').innerHTML = `Hello, ${character.name}!`;
        document.getElementById('npc-name').innerHTML = `NPC: ${npc.name}`;
        document.getElementById('npc-dialogue').innerHTML = `${npc.name}: ${npc.dialogue}`;
        fetchOnlinePlayers();
    } else {
        console.error(result);
    }
});
document.getElementById('talk-to-npc').addEventListener('click', () => {
    npc.talk();
});
document.getElementById('fight-npc').addEventListener('click', () => {
    const damageToNPC = Math.max(character.attack - npc.defense, 0);
    npc.takeDamage(damageToNPC);
    if (npc.health <= 0) {
        console.log(`You defeated ${npc.name}!`);
        npc.health = 50;
        document.getElementById('npc-dialogue').innerHTML = `${npc.name}: You defeated me. Good job!`;
    } else {
        console.log(`${npc.name} took ${damageToNPC} damage, remaining health: ${npc.health}`);
        const damageToCharacter = Math.max(npc.attack - character.defense, 0);
        character.takeDamage(damageToCharacter);
        if (character.health <= 0) {
            console.log(`You were defeated by ${npc.name}...`);
            character.health = 100;
        } else {
            console.log(`You took ${damageToCharacter} damage, remaining health: ${character.health}`);
        }
    }
});

document.getElementById('chat-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const message = document.getElementById('chat-message').value;
    document.getElementById('chat-message').value = '';

    const response = await fetch('sendMessage.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `user=${encodeURIComponent(character.name)}&message=${encodeURIComponent(message)}`
    });

    const result = await response.text();
    if (result !== "Message sent successfully") {
        console.error(result);
    }
});

async function fetchChatMessages() {
    const response = await fetch('getChatMessages.php');
    const messages = await response.json();
    const chatArea = document.getElementById('chat-area');
    chatArea.innerHTML = '';

    messages.forEach(message => {
        const p = document.createElement('p');
        p.textContent = `${message.user}: ${message.message}`;
        chatArea.appendChild(p);
    });
    chatArea.scrollTop = chatArea.scrollHeight;
}

setInterval(fetchChatMessages, 1000);
setInterval(fetchOnlinePlayers, 10000);

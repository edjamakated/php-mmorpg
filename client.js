// client.js
class GameClient {
    constructor(url) {
        this.socket = new WebSocket(url);
        this.setupEventListeners();
    }

    setupEventListeners() {
        this.socket.addEventListener('open', (event) => {
            console.log('WebSocket connected:', event);
        });

        this.socket.addEventListener('message', (event) => {
            const data = JSON.parse(event.data);
            this.handleServerMessage(data);
        });

        this.socket.addEventListener('close', (event) => {
            console.log('WebSocket disconnected:', event);
        });

        this.socket.addEventListener('error', (event) => {
            console.error('WebSocket error:', event);
        });
    }

    handleServerMessage(data) {
        // Handle server messages
    }

    sendMessage(data) {
        this.socket.send(JSON.stringify(data));
    }
}

const gameClient = new GameClient('ws://localhost:8080');

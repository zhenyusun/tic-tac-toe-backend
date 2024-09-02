# Tic-Tac-Toe Game

A simple Tic-Tac-Toe game built with React for the frontend and Laravel for the backend. Players can take turns placing Xs and Os on the grid, and the game keeps track of scores.

## Installation

### Prerequisites
- Node.js
- npm
- PHP
- Composer

### Backend Setup
1. Navigate to the backend directory:
   cd tic-tac-toe-backend

2. Install dependencies:
   composer install

3. Serve the backend using Apache or another server.

### Frontend Setup
1. Navigate to the frontend directory:
    cd tic-tac-toe-frontend
    
2. Install dependencies
    npm install

3. Build the front-end
    npm run build

4. Serve the frontend using Apache or another server.

### Usage
- Access the game at http://your-domain/tic-tac-toe/
- Click on cells to place Xs and Os.
- Use the "Restart Game" button to start a new game.
- Use the "Reset All" button to reset scores.

### API Endpoints
## GET /api/game
Returns the current game state.

## POST /api/game/{piece}
Places a piece on the board.

## POST /api/game/restart
Restarts the game.

## DELETE /api/game
Resets the board and scores.

### Technologies Used
React: Frontend framework
Laravel: Backend framework
Axios: HTTP client for the frontend

# Tic-Tac-Toe Game

A simple Tic-Tac-Toe game built with React for the frontend and Laravel for the backend. Players can take turns placing Xs and Os on the grid, and the game keeps track of scores. The game also includes a basic AI for single-player mode where the computer plays as "O".

## Installation

### Prerequisites
- Node.js
- npm
- PHP
- Composer

### Backend Setup
1. Navigate to the backend directory:
    ```bash
    cd tic-tac-toe-backend
    ```
2. Install dependencies:
    ```bash
    composer install
    ```
3. Serve the backend using Apache or another server.

### Frontend Setup
1. Navigate to the frontend directory:
    ```bash
    cd tic-tac-toe-frontend
    ```
2. Install dependencies:
    ```bash
    npm install
    ```
3. Open the .env file in the root of the frontend directory and add the API URL
    ```bash
    REACT_APP_API_URL=http://your-domain-or-ip/tic-tac-toe/api/game
    ```
4. Build the frontend:
    ```bash
    npm run build
    ```
5. Serve the frontend using Apache or another server.

## Usage
1. Access the game at `http://your-domain/tic-tac-toe/`.
2. Click on cells to place Xs and Os.
3. The computer will automatically make its move after the player.
4. Use the "Restart Game" button to start a new game.
5. Use the "Reset All" button to reset scores.

## API Endpoints
- **GET /api/game**  
  Returns the current game state.

- **POST /api/game/{piece}**  
  Places a piece on the board.

- **POST /api/game/restart**  
  Restarts the game.

- **DELETE /api/game**  
  Resets the board and scores.

## Testing
1. Navigate to the backend directory:
```bash
cd tic-tac-toe-backend
```
2. Run the tests:
```bash
vendor/bin/phpunit
```

## Technologies Used
- **React**: Frontend framework
- **Laravel**: Backend framework
- **Axios**: HTTP client for the frontend

## Future Enhancements
- Implement unit tests for backend logic.
- Make the domain/IP address dynamic in `App.js` for easier deployment across environments.

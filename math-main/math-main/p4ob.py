import matplotlib.pyplot as plt
import numpy as np
import random 
import pickle

fig, ax = plt.subplots()
matrice_Grille = np.zeros((6, 7), dtype=int)


class Game:
    players = []
    current_player = 0

    @staticmethod
    def restart():
        Game.players = []

    @staticmethod
    def add_player(player):
        Game.players.append(player)

    @staticmethod
    def get_player():
        return Game.players[Game.current_player]

    @staticmethod
    def do_a_turn():
        player = Game.get_player()
        plt.title("Joueur " + str(player) + ", choisir la colonne à jouer : ")
        player.play()
        plt.draw()
        Game.current_player = (Game.current_player + 1) % (len(Game.players))

    @staticmethod
    def is_not_finish():
        return not Game.check_for_winner() and not Game.is_grid_full()

    @staticmethod
    def is_grid_full():
        return np.all(matrice_Grille != 0)

    @staticmethod
    def check_for_winner():
        # Check for a winner horizontally, vertically, and diagonally
        if Grille.check_consecutive_four(matrice_Grille):
            return True
        # Check for a winner in the transposed matrix (columns become rows and vice versa)
        if Grille.check_consecutive_four(np.transpose(matrice_Grille)):
            return True
        # Check for a winner in the diagonals
        if Grille.check_consecutive_four_in_diagonals(matrice_Grille):
            return True

        return False


class Grille:
    @staticmethod
    def init():
        for col in range(8):
            if col != 0:
                ax.text(col - 0.5, -0.5, str(col), horizontalalignment='center')
            for lig in range(7):
                ax.plot([col, col], [0, 6], color='blue')
                ax.plot([0, 7], [lig, lig], color='blue')
        ax.set_axis_off()
        ax.set(aspect="equal", title='PUISSANCE 4')
        plt.draw()

    @staticmethod
    def add_piece(column, color):
        if column <= 7:
            row = 0
            for elem in matrice_Grille:
                row += 1 if elem[column - 1] != 0 else 0
            if row < 6:
                matrice_Grille[row, column - 1] = Game.current_player + 1
                ax.scatter(column - 0.5, row + 0.5, s=1000, color=color)

    @staticmethod
    def check_consecutive_four(matrix):
        # Check for four consecutive pieces in rows or columns
        for i in range(matrix.shape[0]):
            for j in range(matrix.shape[1] - 3):
                if matrix[i, j] == matrix[i, j + 1] == matrix[i, j + 2] == matrix[i, j + 3] != 0:
                    return True

        return False

    @staticmethod
    def check_consecutive_four_in_diagonals(matrix):
        # Check for four consecutive pieces in diagonals
        for i in range(matrix.shape[0] - 3):
            for j in range(matrix.shape[1] - 3):
                # Check diagonals from top-left to bottom-right
                if matrix[i, j] == matrix[i + 1, j + 1] == matrix[i + 2, j + 2] == matrix[i + 3, j + 3] != 0:
                    return True

        for i in range(matrix.shape[0] - 3):
            for j in range(3, matrix.shape[1]):
                # Check diagonals from top-right to bottom-left
                if matrix[i, j] == matrix[i + 1, j - 1] == matrix[i + 2, j - 2] == matrix[i + 3, j - 3] != 0:
                    return True
        return False


class Player:
    def __init__(self, color):
        self.color = color

    def __str__(self):
        return "Player"

class Random(Player):
    def __init__(self, color):
        super().__init__(color)

    def __str__(self):
        return f"IA ({self.color})"

    def play(self):
        valid_columns = [col for col in range(7) if matrice_Grille[5, col] == 0]
        if valid_columns:
            selected_column = np.random.choice(valid_columns)
            row = np.argmax(matrice_Grille[:, selected_column] == 0)
            Grille.add_piece(selected_column + 1, self.color)

class IA(Player):
    def __init__(self, color):
        super().__init__(color)

    def __str__(self):
        return f"IA ({self.color})"

    def play(self):
        # Check for winning moves
        for col in range(7):
            if self.check_winning_move(col, 2):
                return

        # Check for blocking opponent's winning moves
        for col in range(7):
            if self.check_winning_move(col, 1):
                return

        # If no winning or blocking moves, make a strategic move
        strategic_column = self.choose_strategic_move()
        Grille.add_piece(strategic_column + 1, 2)

    def check_winning_move(self, column, player_color):
        temp_grid = matrice_Grille.copy()
        for row in range(5, -1, -1):
            if temp_grid[row, column] == 0:
                temp_grid[row, column] = 1
                if Game.check_for_winner():
                    temp_grid[row, column] = 0  # Reset the move
                    return True
                else:
                    temp_grid[row, column] = 0  # Reset the move
                    break  # Move to the next column
        return False

    def choose_strategic_move(self):
        # Prioritize center column
        if matrice_Grille[0, 3] == 0:
            return 3

        # Prioritize winning moves
        for col in range(7):
            temp_grid = matrice_Grille.copy()
            for row in range(5, -1, -1):
                if temp_grid[row, col] == 0:
                    temp_grid[row, col] = 2

class QLearningAI(Player):
    def __init__(self, color, epsilon=0.1, learning_rate=0.1, discount_factor=0.9):
        super().__init__(color)
        self.epsilon = epsilon
        self.learning_rate = learning_rate
        self.discount_factor = discount_factor

        # Chargement de la table Q depuis un fichier s'il existe
        try:
            with open('q_table.pkl', 'rb') as f:
                self.q_table = pickle.load(f)
        except FileNotFoundError:
            self.q_table = {}


    def play(self):
        # Explore or exploit
        if random.uniform(0, 1) < self.epsilon:  # Explore with epsilon probability
            selected_column = self.explore_move()
        else:
            selected_column = self.exploit_move()

        # Play the selected move
        Grille.add_piece(selected_column + 1, self.color)

        # Check if the game is finished and compute the reward
        reward = self.compute_reward()

        # Update the Q-value for the selected move
        self.update_q_value(selected_column, reward)

    def compute_reward(self):
        # You need to define a reward function based on the current state of the game
        # and possibly the outcome of the game. For example, you can give a positive
        # reward if the move leads to a win, a negative reward if it leads to a loss,
        # and zero otherwise.
        # Implement your reward function here based on your game logic.
        # For example:
        if Game.check_for_winner():
            return 1.0  # Positive reward for winning
        elif Game.is_grid_full():
            return 0.0  # Zero reward for a draw
        else:
            return 0.0  # No reward for other moves



    def explore_move(self):
        valid_columns = [col for col in range(7) if matrice_Grille[5, col] == 0]
        return random.choice(valid_columns)

    def exploit_move(self):
        valid_columns = [col for col in range(7) if matrice_Grille[5, col] == 0]
    
        if not valid_columns:
            # If there are no valid columns, choose randomly
            return self.explore_move()

        q_values = {col: self.get_q_value(col) for col in valid_columns}
        return max(q_values, key=q_values.get)

    def get_q_value(self, column):
        state = tuple(matrice_Grille.flatten())  # Convert the game state to a hashable tuple
        if state not in self.q_table:
            return 0.0  # Default value for unseen states
        return self.q_table[state].get(column, 0.0)

    def update_q_value(self, column, reward):
        state = tuple(matrice_Grille.flatten())
        if state not in self.q_table:
            self.q_table[state] = {}
        self.q_table[state][column] = self.get_q_value(column) + self.learning_rate * (
            reward + self.discount_factor * max(self.get_q_value(next_column) for next_column in range(7)) - self.get_q_value(column)
        )
        # Sauvegarde de la table Q dans un fichier
        with open('q_table.pkl', 'wb') as f:
            pickle.dump(self.q_table, f)

class Human(Player):
    def __init__(self, color):
        super().__init__(color)
        self.color = color

    def __str__(self):
        return str(self.__class__)

    def play(self):
        pt = plt.ginput(1, timeout=-60)[0]
        jeton = int(pt[0]) + 1
        Grille.add_piece(jeton, self.color)

class QLearningAI2(Player):
    def __init__(self, color, epsilon=0.1, learning_rate=0.1, discount_factor=0.9):
        super().__init__(color)
        self.epsilon = epsilon
        self.learning_rate = learning_rate
        self.discount_factor = discount_factor

        # Chargement de la table Q depuis un fichier s'il existe
        try:
            with open('q_table2.pkl', 'rb') as f:
                self.q_table = pickle.load(f)
        except FileNotFoundError:
            self.q_table = {}


    def play(self):
        # Explore or exploit
        if random.uniform(0, 1) < self.epsilon:  # Explore with epsilon probability
            selected_column = self.explore_move()
        else:
            selected_column = self.exploit_move()

        # Play the selected move
        Grille.add_piece(selected_column + 1, self.color)

        # Check if the game is finished and compute the reward
        reward = self.compute_reward()

        # Update the Q-value for the selected move
        self.update_q_value(selected_column, reward)

    def compute_reward(self):
        # You need to define a reward function based on the current state of the game
        # and possibly the outcome of the game. For example, you can give a positive
        # reward if the move leads to a win, a negative reward if it leads to a loss,
        # and zero otherwise.
        # Implement your reward function here based on your game logic.
        # For example:
        if Game.check_for_winner():
            return 1.0  # Positive reward for winning
        elif Game.is_grid_full():
            return 0.0  # Zero reward for a draw
        else:
            return 0.0  # No reward for other moves



    def explore_move(self):
        valid_columns = [col for col in range(7) if matrice_Grille[5, col] == 0]
        return random.choice(valid_columns)

    def exploit_move(self):
        valid_columns = [col for col in range(7) if matrice_Grille[5, col] == 0]
    
        if not valid_columns:
            # If there are no valid columns, choose randomly
            return self.explore_move()

        q_values = {col: self.get_q_value(col) for col in valid_columns}
        return max(q_values, key=q_values.get)

    def get_q_value(self, column):
        state = tuple(matrice_Grille.flatten())  # Convert the game state to a hashable tuple
        if state not in self.q_table:
            return 0.0  # Default value for unseen states
        return self.q_table[state].get(column, 0.0)

    def update_q_value(self, column, reward):
        state = tuple(matrice_Grille.flatten())
        if state not in self.q_table:
            self.q_table[state] = {}
        self.q_table[state][column] = self.get_q_value(column) + self.learning_rate * (
            reward + self.discount_factor * max(self.get_q_value(next_column) for next_column in range(7)) - self.get_q_value(column)
        )
        # Sauvegarde de la table Q dans un fichier
        with open('q_table2.pkl', 'wb') as f:
            pickle.dump(self.q_table, f)

Game.add_player(QLearningAI('red'))
Game.add_player(Random('yellow'))  # Add an AI player
Grille.init()
while Game.is_not_finish():
    Game.do_a_turn()

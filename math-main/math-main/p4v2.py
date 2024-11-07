import matplotlib.pyplot as plt
import numpy as np
import random 
import pickle

fig, ax = plt.subplots()
matrice_Grille = np.zeros((6, 7), dtype=int)

class NeuralNetwork:
    def __init__(self, input_size, hidden_size, output_size):
        self.input_size = input_size
        self.hidden_size = hidden_size
        self.output_size = output_size
        self.weights_input_hidden = np.random.randn(input_size, hidden_size)
        self.weights_hidden_output = np.random.randn(hidden_size, output_size)

    def forward(self, x):
        # Forward pass through the network
        self.hidden_activation = np.dot(x, self.weights_input_hidden)
        self.hidden_output = self.sigmoid(self.hidden_activation)
        self.output_activation = np.dot(self.hidden_output, self.weights_hidden_output)
        self.output = self.sigmoid(self.output_activation)
        return self.output

    def backward(self, x, y, output, learning_rate):
        # Backpropagation to update weights
        error = y - output
        output_delta = error * self.sigmoid_derivative(output)
        hidden_error = np.dot(output_delta, self.weights_hidden_output.T)
        hidden_delta = hidden_error * self.sigmoid_derivative(self.hidden_output)

        self.weights_hidden_output += learning_rate * np.dot(self.hidden_output.T, output_delta)
        self.weights_input_hidden += learning_rate * np.dot(x.T, hidden_delta)

    def sigmoid(self, x):
        return 1 / (1 + np.exp(-x))

    def sigmoid_derivative(self, x):
        return x * (1 - x)

    def save(self, filename):
        with open(filename, 'wb') as f:
            pickle.dump(self, f)

    @staticmethod
    def load(filename):
        with open(filename, 'rb') as f:
            return pickle.load(f)

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

class NeuralNetworkAI(Player):
    def __init__(self, color, filename=None):
        super().__init__(color)
        self.color = color
        self.neural_net = NeuralNetwork(input_size=42, hidden_size=20, output_size=7)
        if filename:
            self.load(filename)

    def __str__(self):
        return f"NeuralNetworkAI ({self.color})"

    def play(self):
        valid_columns = [col for col in range(7) if matrice_Grille[5, col] == 0]
        if valid_columns:
            flattened_grid = matrice_Grille.flatten()
            output = self.neural_net.forward(flattened_grid)
            selected_column = np.argmax(output)
            if selected_column in valid_columns:
                Grille.add_piece(selected_column + 1, self.color)

    def save(self, filename):
        self.neural_net.save(filename)

    def load(self, filename):
        self.neural_net = NeuralNetwork.load(filename)

yes = NeuralNetworkAI('red', 'neural_network.pkl')
Game.add_player(yes)  # Add a neural network player
Game.add_player(Human('yellow', 'neural_network.pkl'))
Grille.init()
while Game.is_not_finish():
    Game.do_a_turn()
    plt.draw()
plt.show()    

# Sauvegarde du réseau après la partie
yes.save('neural_network.pkl')

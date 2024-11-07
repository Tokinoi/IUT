import matplotlib.pyplot as plt
import numpy as np
import pickel

fig, ax = plt.subplots()

def initGrille():
    for col in range(8):
        if col!=0:
            ax.text(col-0.5,-0.5,str(col),horizontalalignment='center')
        for lig in range(7):
            ax.plot( [col,col],[0,6],color='black')
            ax.plot( [0,7],[lig,lig],color='black')
    ax.set_axis_off()
    ax.set(aspect="equal", title='PUISSANCE 4')
    plt.draw()
    
initGrille()
color = 'yellow'
while True:
    
    matrice_Grille=np.zeros((6,7),dtype=int)
    print(matrice_Grille)
    plt.title("Joueur "+color+", choisir la colonne à jouer : ")
    pt=plt.ginput(1,timeout=-60)[0]
    jeton1=int(pt[0])+1
    print(pt,jeton1)
    matrice_Grille[5,jeton1-1]=1
    print(matrice_Grille)
    ax.scatter(jeton1-0.5,0.5,s=1000,color=color)
    plt.draw()
    color = 'red' if (color == 'yellow') else 'yellow' 